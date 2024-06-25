# Release Notes for Blitz CloudFront Purger

## 4.1.0 - 2024-06-25

### Changed

- Purging a site now results in a single invalidation path sent to the CloudFront API, potentially saving on invalidation request charges.

## 4.0.0 - 2024-03-19

> {warning} Blitz CloudFront Purger is now a Craft CMS plugin rather than a PHP package, and as such it must be installed via Craft to be usable. You should manually remove `CloudFrontPurger` from the `cachePurgerTypes` config setting in your `config/blitz.php` file, if it exists, since the plugin now registers the purger automatically.

### Added

- Added a `condenseUrls` setting that can help reduce the number of invalidation paths sent to the CloudFront API, potentially saving on invalidation request charges.
