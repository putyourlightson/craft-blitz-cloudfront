<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzcloudfront;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Aws\Result;
use Craft;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use putyourlightson\blitz\Blitz;
use putyourlightson\blitz\drivers\purgers\BaseCachePurger;
use putyourlightson\blitz\events\RefreshCacheEvent;
use yii\base\Event;

/**
 * @property mixed $settingsHtml
 */
class CloudFrontPurger extends BaseCachePurger
{
    // Constants
    // =========================================================================

    /**
     * The CloudFront service endpoint only allows connecting through a single region.
     * https://docs.aws.amazon.com/general/latest/gr/cf_region.html
     *
     * @var string
     */
    const REGION = 'us-east-1';

    // Properties
    // =========================================================================

    /**
     * @deprecated Since only a single region is allowed.
     * TODO: Remove in version 3.0.0
     *
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $apiSecret;

    /**
     * @var string
     */
    public $distributionId = '';

    /**
     * @var string
     */
    private $_version = 'latest';

    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('blitz', 'CloudFront Purger');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['blitz-cloudfront'] = __DIR__.'/templates/';
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apiKey', 'apiSecret'], 'required'],
            [['warmCacheDelay'], 'integer', 'min' => 0, 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function purgeUris(array $siteUris)
    {
        $event = new RefreshCacheEvent(['siteUris' => $siteUris]);
        $this->trigger(self::EVENT_BEFORE_PURGE_CACHE, $event);

        if (!$event->isValid) {
            return;
        }

        $reservedCharacters = [";", "/", "?", ":", "@", "=", "&"];
        $encodedReservedCharacters = array_map(function($c) {
            return urlencode($c);
        }, $reservedCharacters);

        // Get paths from site URIs (https://github.com/putyourlightson/craft-blitz-cloudfront/issues/1)
        $paths = array_map(function($siteUri) use ($encodedReservedCharacters, $reservedCharacters) {
            return '/' . str_replace($encodedReservedCharacters, $reservedCharacters, urlencode($siteUri->uri));
        }, $event->siteUris);

        $this->_sendRequest($paths);

        if ($this->hasEventHandlers(self::EVENT_AFTER_PURGE_CACHE)) {
            $this->trigger(self::EVENT_AFTER_PURGE_CACHE, $event);
        }
    }

    /**
     * @inheritdoc
     */
    public function purgeAll()
    {
        $event = new RefreshCacheEvent();
        $this->trigger(self::EVENT_BEFORE_PURGE_ALL_CACHE, $event);

        if (!$event->isValid) {
            return;
        }

        $this->_sendRequest(['/*']);

        if ($this->hasEventHandlers(self::EVENT_AFTER_PURGE_ALL_CACHE)) {
            $this->trigger(self::EVENT_AFTER_PURGE_ALL_CACHE, $event);
        }
    }

    /**
     * @inheritdoc
     */
    public function test(): bool
    {
        $response = $this->_sendRequest([]);

        if (!$response) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('blitz-cloudfront/settings', [
            'purger' => $this,
        ]);
    }

    // Private Methods
    // =========================================================================

    /**
     * Sends a request to the API.
     *
     * @param array $paths
     *
     * @return Result|bool
     */
    private function _sendRequest(array $paths)
    {
        $result = '';

        $config = [
            'version' => $this->_version,
            'region' => self::REGION,
        ];

        $key = Craft::parseEnv($this->apiKey);
        $secret = Craft::parseEnv($this->apiSecret);

        if ($key && $secret) {
            $config['credentials'] = [
                'key' => Craft::parseEnv($this->apiKey),
                'secret' => Craft::parseEnv($this->apiSecret),
            ];
        }

        $client = new CloudFrontClient($config);

        try {
            $result = $client->createInvalidation([
                'DistributionId' => Craft::parseEnv($this->distributionId),
                'InvalidationBatch' => [
                    'CallerReference' => time(),
                    'Paths' => [
                        'Items' => $paths,
                        'Quantity' => count($paths),
                    ],
                ]
            ]);
        }
        catch (AwsException $exception) {
            $errorCode = $exception->getAwsErrorCode() ?: 'Not provided.';
            $errorMessage = $exception->getAwsErrorMessage() ?: 'Not provided.';
            $error = 'AWS Client Error - Code: '.$errorCode.' - Message: '.$errorMessage;
            Blitz::$plugin->log($error, [], 'error');

            return false;
        }

        return $result;
    }
}
