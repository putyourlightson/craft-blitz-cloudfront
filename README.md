[![Stable Version](https://img.shields.io/packagist/v/putyourlightson/craft-blitz-cloudfront?label=stable)]((https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront))
[![Total Downloads](https://img.shields.io/packagist/dt/putyourlightson/craft-blitz-cloudfront)](https://packagist.org/packages/putyourlightson/craft-blitz-cloudfront)

<p align="center"><img width="130" src="https://putyourlightson.com/assets/logos/blitz.svg"></p>

# Blitz CloudFront Purger for Craft CMS

The CloudFront Purger allows the [Blitz](https://putyourlightson.com/plugins/blitz) plugin for [Craft CMS](https://craftcms.com/) to intelligently purge cached pages.

**Note that Amazon CloudFront charges for invalidation requests. Since invalidation requests can quickly add up when purging individual URLs, you should be aware of the potential costs. PutYourLightsOn takes no responsibility whatsoever for expenses incurred.**

> No additional charge for the first 1,000 paths requested for invalidation each month. Thereafter, $0.005 per path requested for invalidation. 

> A path listed in your invalidation request represents the URL (or multiple URLs if the path contains a wildcard character) of the object(s) you want to invalidate from CloudFront cache.

Source: [aws.amazon.com/cloudfront/pricing](https://aws.amazon.com/cloudfront/pricing/)

## Usage

Install the purger using composer.

```
composer require putyourlightson/craft-blitz-cloudfront
```

Then add the class to the `cachePurgerTypes` config setting in `config/blitz.php`.

```php
// The purger type classes to add to the plugin’s default purger types.
'cachePurgerTypes' => [
    'putyourlightson\blitz\drivers\purgers\CloudflarePurger',
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
