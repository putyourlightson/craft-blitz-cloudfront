# Blitz CloudFront Purger for Craft CMS

The CloudFront Purger allows the [Blitz](https://putyourlightson.com/craft-plugins/blitz) plugin for [Craft CMS](https://craftcms.com/) to intelligently purge cached pages.

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

```
// The purger type classes to add to the plugin’s default purger types.
'cachePurgerTypes' => [
    'putyourlightson\blitz\drivers\purgers\CloudflarePurger',
    'putyourlightson\blitzcloudfront\CloudFrontPurger',
],
```

## Documentation

Read the documentation at [putyourlightson.com/craft-plugins/blitz/docs](https://putyourlightson.com/craft-plugins/blitz/docs#/?id=custom-reverse-proxy-purgers).

Created by [PutYourLightsOn](https://putyourlightson.com/).
