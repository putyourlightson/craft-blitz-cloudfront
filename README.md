[![Stable Version](https://img.shields.io/packagist/v/putyourlightson/craft-blitz-cloudfront?label=stable)]((https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront))
[![Total Downloads](https://img.shields.io/packagist/dt/putyourlightson/craft-blitz-cloudfront)](https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront)

<p align="center"><img width="130" src="https://raw.githubusercontent.com/putyourlightson/craft-blitz-cloudfront/develop/src/icon.svg"></p>

# Blitz CloudFront Purger Plugin for Craft CMS

The CloudFront Purger plugin allows the [Blitz](https://putyourlightson.com/plugins/blitz) plugin for [Craft CMS](https://craftcms.com/) to intelligently purge pages cached on [AWS CloudFront](https://aws.amazon.com/cloudfront/).

**Note that Amazon CloudFront charges for invalidation requests. Since
invalidation requests can quickly add up when purging individual URLs, you
should be aware of the potential costs. PutYourLightsOn takes no responsibility
whatsoever for expenses incurred.**

> The first 1,000 invalidation paths that you submit per month are free; you pay for each invalidation path over 1,000 in a month. An invalidation path can be for a single file (such as `/images/logo.jpg`) or for multiple files (such as `/images/*`). A path that includes the `*` wildcard counts as one path even if it causes CloudFront to invalidate thousands of files.

Source: [docs.aws.amazon.com](https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Invalidation.html#PayingForInvalidation)

## License

This plugin requires a free commercial license available through the [Craft Plugin Store](https://plugins.craftcms.com/blitz-cloudfront).

## Requirements

This plugin requires [Craft CMS](https://craftcms.com/) 3.0.0 or later, or 4.0.0 or later, or 5.0.0 or later.

## Installation

To install the plugin, search for “Blitz CloudFront Purger” in the Craft Plugin Store, or install manually using composer.

```shell
composer require putyourlightson/craft-blitz-cloudfront
```

## Usage

Once installed, the CloudFront Purger can be selected in the Blitz plugin settings or in `config/blitz.php`.

```php
// The purger type to use.
'cachePurgerType' => 'putyourlightson\blitzcloudfront\CloudFrontPurger',

// The purger settings.
'cachePurgerSettings' => [
   'region' => 'us-east-1',
   'apiKey' => 'p_prod_abcdefgh1234567890',
   'apiSecret' => 's_prod_abcdefgh1234567890',
   'distributionId' => '123456789',
   'warmCacheDelay' => '5',
],
```

---

Created by [PutYourLightsOn](https://putyourlightson.com/).
