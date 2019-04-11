<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzcloudfront;

use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Aws\Result;
use Craft;
use putyourlightson\blitz\drivers\purgers\BaseCachePurger;
use putyourlightson\blitz\helpers\SiteUriHelper;
use putyourlightson\blitz\models\SiteUriModel;

/**
 * @property mixed $settingsHtml
 */
class CloudFrontPurger extends BaseCachePurger
{
    // Constants
    // =========================================================================

    /**
     * @var string[]
     */
    const REGIONS = [
        'us-east-1',
        'us-west-2',
        'eu-west-1',
    ];

    // Properties
    // =========================================================================

    /**
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

    /**
     * @inheritdoc
     */
    public static function getTemplatesRoot(): array
    {
        $templatePage = __DIR__.'/templates/';

        return [
            'blitz-cloudfront' => $templatePage
        ];
    }

    // Public Methods
    // =========================================================================

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
            [['region', 'apiKey', 'apiSecret'], 'required'],
            [['region'], 'in', 'range' => self::REGIONS, 'message' => Craft::t('blitz',
                'The region provided is not a valid AWS region.'
            )],
        ];
    }

    /**
     * @inheritdoc
     */
    public function purge(SiteUriModel $siteUri)
    {
        $this->_sendRequest([$siteUri->getUrl()]);
    }

    /**
     * @inheritdoc
     */
    public function purgeUris(array $siteUris)
    {
        $this->_sendRequest(SiteUriHelper::getUrls($siteUris));
    }

    /**
     * @inheritdoc
     */
    public function purgeAll()
    {
        $this->_sendRequest(['/*']);
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

        return $response->getStatusCode() == 200;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('blitz-cloudfront/settings', [
            'purger' => $this,
            'regions' => self::REGIONS,
        ]);
    }

    // Private Methods
    // =========================================================================

    /**
     * Sends a request to the API.
     *
     * @param array $paths
     *
     * @return Result
     */
    private function _sendRequest(array $paths)
    {
        $result = '';

        $client = new CloudFrontClient([
            'version' => $this->_version,
            'region' => $this->region,
            'credentials' => [
                'key' => Craft::parseEnv($this->apiKey),
                'secret' => Craft::parseEnv($this->apiSecret),
            ],
        ]);

        try {
            $result = $client->createInvalidation([
                'DistributionId' => $this->distributionId,
                'InvalidationBatch' => [
                    'CallerReference' => time(),
                    'Paths' => [
                        'Items' => $paths,
                        'Quantity' => 1,
                    ],
                ]
            ]);
        }
        catch (AwsException $e) { }

        return $result;
    }
}
