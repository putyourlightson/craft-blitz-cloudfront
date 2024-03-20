# Release Notes for Blitz CloudFront Purger

## 4.0.0 - Unreleased

> {warning} Blitz CloudFront Purger is now a Craft CMS plugin rather than a PHP package, and as such it must be installed via Craft to be usable.

### Added

- Added a `condenseUrls` setting that can help reduce the number of invalidation paths sent to the CloudFront API, potentially saving on invalidation request charges.
