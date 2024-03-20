<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzcloudfront;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Aws\Exception\CredentialsException;
use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\App;
use craft\web\View;
use putyourlightson\blitz\Blitz;
use putyourlightson\blitz\drivers\purgers\BaseCachePurger;
use putyourlightson\blitz\events\RefreshCacheEvent;
use putyourlightson\blitz\helpers\SiteUriHelper;
use yii\base\Event;
use yii\log\Logger;

/**
 * @property-read null|string $settingsHtml
 */
class CloudFrontPurger extends BaseCachePurger
{
    /**
     * The CloudFront service endpoint only allows connecting through a single region.
     * https://docs.aws.amazon.com/general/latest/gr/cf_region.html
     *
     * @var string
     */
    public const REGION = 'us-east-1';

    /**
     * @var string
     */
    public const VERSION = 'latest';

    /**
     * @var string
     */
    public string $apiKey = '';

    /**
     * @var string
     */
    public string $apiSecret = '';

    /**
     * @var string
     */
    public string $distributionId = '';

    /**
     * @var bool
     */
    public bool $condenseUrls = false;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('blitz', 'CloudFront Purger');
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['blitz-cloudfront'] = __DIR__ . '/templates/';
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'apiKey',
                'apiSecret',
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'apiKey' => Craft::t('blitz', 'API Key'),
            'apiSecret' => Craft::t('blitz', 'API Secret'),
            'distributionId' => Craft::t('blitz', 'Distribution ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['apiKey', 'apiSecret', 'distributionId'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function purgeAll(callable $setProgressHandler = null, bool $queue = true): void
    {
        $event = new RefreshCacheEvent();
        $this->trigger(self::EVENT_BEFORE_PURGE_ALL_CACHE, $event);

        if (!$event->isValid) {
            return;
        }

        $this->sendRequest(['/*']);

        if ($this->hasEventHandlers(self::EVENT_AFTER_PURGE_ALL_CACHE)) {
            $this->trigger(self::EVENT_AFTER_PURGE_ALL_CACHE, $event);
        }
    }

    /**
     * @inheritdoc
     */
    public function purgeUrisWithProgress(array $siteUris, callable $setProgressHandler = null): void
    {
        if (empty($siteUris)) {
            return;
        }

        $urls = SiteUriHelper::getUrlsFromSiteUris($siteUris);

        if ($this->condenseUrls) {
            $urls = $this->getCondensedUrls($urls);
        }

        $count = 0;
        $total = count($urls);
        $label = 'Purging {total} pages.';

        if (is_callable($setProgressHandler)) {
            $progressLabel = Craft::t('blitz', $label, ['count' => $count, 'total' => $total]);
            call_user_func($setProgressHandler, $count, $total, $progressLabel);
        }

        $paths = array_map(fn($url) => $this->getPathFromUrl($url), $urls);

        $this->sendRequest($paths);

        $count = $total;

        if (is_callable($setProgressHandler)) {
            $progressLabel = Craft::t('blitz', $label, ['count' => $count, 'total' => $total]);
            call_user_func($setProgressHandler, $count, $total, $progressLabel);
        }
    }

    /**
     * @inheritdoc
     */
    public function test(): bool
    {
        $response = $this->sendRequest(['/test']);

        if (!$response) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('blitz-cloudfront/settings', [
            'purger' => $this,
        ]);
    }

    /**
     * Returns condensed URLs by eagerly adding a wildcard character.
     * This overly simplified method returns a single URL with a wildcard character after the longest common prefix.
     *
     * @param string[] $urls
     * @return string[]
     */
    public function getCondensedUrls(array $urls): array
    {
        if (count($urls) < 2) {
            return $urls;
        }

        // Get the longest common prefix between the two most dissimilar strings.
        sort($urls);

        return [$this->getLongestCommonPrefix(reset($urls), end($urls)) . '*'];
    }

    /**
     * Returns the longest common prefix between two strings.
     */
    private function getLongestCommonPrefix($str1, $str2): string
    {
        $length = min(strlen($str1), strlen($str2));
        for ($i = 0; $i < $length; $i++) {
            if ($str1[$i] !== $str2[$i]) {
                return substr($str1, 0, $i);
            }
        }

        return substr($str1, 0, $length);
    }

    /**
     * Returns a path from a URL.
     */
    private function getPathFromUrl(string $url): string
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        $path = parse_url($url, PHP_URL_PATH);
        $path .= $queryString ? '?' . $queryString : '';

        // Revert encoded reserved characters back to their original values.
        // https://github.com/putyourlightson/craft-blitz-cloudfront/pull/6
        // https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Invalidation.html#invalidation-specifying-objects
        $reservedCharacters = [';', '/', '?', ':', '@', '=', '&', '*'];
        $encodedReservedCharacters = ['%3B', '%2F', '%3F', '%3A', '%40', '%3D', '%26', '%2A'];
        $path = str_replace($encodedReservedCharacters, $reservedCharacters, urlencode($path));

        // Append a trailing slash if `addTrailingSlashesToUrls` is `true`.
        if (Craft::$app->config->general->addTrailingSlashesToUrls) {
            $path = rtrim($path, '/') . '/';
        }

        return $path;
    }

    /**
     * Sends a request to the API.
     */
    private function sendRequest(array $paths): bool
    {
        $config = [
            'version' => self::VERSION,
            'region' => self::REGION,
        ];

        $key = App::parseEnv($this->apiKey);
        $secret = App::parseEnv($this->apiSecret);

        if ($key && $secret) {
            $config['credentials'] = [
                'key' => App::parseEnv($this->apiKey),
                'secret' => App::parseEnv($this->apiSecret),
            ];
        }

        $client = new CloudFrontClient($config);

        try {
            $client->createInvalidation([
                'DistributionId' => App::parseEnv($this->distributionId),
                'InvalidationBatch' => [
                    'CallerReference' => time(),
                    'Paths' => [
                        'Items' => $paths,
                        'Quantity' => count($paths),
                    ],
                ],
            ]);
        } catch (AwsException $exception) {
            $errorCode = $exception->getAwsErrorCode() ?: 'Not provided.';
            $errorMessage = $exception->getAwsErrorMessage() ?: 'Not provided.';
            $error = 'AWS Client Error - Code: ' . $errorCode . ' - Message: ' . $errorMessage;
            Blitz::$plugin->log($error, [], Logger::LEVEL_ERROR);

            return false;
        } catch (CredentialsException $exception) {
            $errorCode = $exception->getCode();
            $errorMessage = $exception->getMessage();
            $error = 'AWS Client Error - Code: ' . $errorCode . ' - Message: ' . $errorMessage;
            Blitz::$plugin->log($error, [], Logger::LEVEL_ERROR);

            return false;
        }

        return true;
    }
}
