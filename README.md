[![Stable Version](https://img.shields.io/packagist/v/putyourlightson/craft-blitz-cloudfront?label=stable)]((https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront))
[![Total Downloads](https://img.shields.io/packagist/dt/putyourlightson/craft-blitz-cloudfront)](https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront)

<p align="center"><img width="130" src="https://putyourlightson.com/assets/logos/blitz.svg"></p>

# Blitz CloudFront Purger for Craft CMS

The CloudFront Purger allows the [Blitz](https://putyourlightson.com/plugins/blitz) plugin for [Craft CMS](https://craftcms.com/) to intelligently purge cached pages.

**Note that Amazon CloudFront charges for invalidation requests. Since invalidation requests can quickly add up when purging individual URLs, you should be aware of the potential costs. PutYourLightsOn takes no responsibility whatsoever for expenses incurred.**

> The first 1,000 invalidation paths that you submit per month are free; you pay for each invalidation path over 1,000 in a month. An invalidation path can be for a single file (such as `/images/logo.jpg`) or for multiple files (such as `/images/*`). A path that includes the `*` wildcard counts as one path even if it causes CloudFront to invalidate thousands of files.

Source: [docs.aws.amazon.com](https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/Invalidation.html#PayingForInvalidation)

## Usage

Install the purger using composer.

```shell
composer require putyourlightson/craft-blitz-cloudfront
```

Then add the class to the `cachePurgerTypes` config setting in `config/blitz.php`.

```php
// The purger type classes to add to the plugin’s default purger types.
'cachePurgerTypes' => [
    'putyourlightson\blitzcloudfront\CloudFrontPurger',
],
```

You can then select the purger and settings either in the control panel or in `config/blitz.php`.

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

## Documentation

Read the documentation at [putyourlightson.com/plugins/blitz](https://putyourlightson.com/plugins/blitz#reverse-proxy-purgers).

Created by [PutYourLightsOn](https://putyourlightson.com/).
