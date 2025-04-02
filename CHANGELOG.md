# Release Notes for Blitz CloudFront Purger

## 5.2.0 - 2025-04-01

### Added

- Added a `beforePurgeSiteUris` event that can be used to modify which site URIs to purge.

## 5.1.4 - 2025-03-19

### Fixed

- Fixed a bug in which the base site URL was not being purged ([#15](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/15)).

## 5.1.3 - 2025-03-12

### Fixed

- Fixed a typo in the field instructions on the plugin settings page ([#14](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/14)).
- Fixed a bug in which base site URLs containing sub-paths were not being purged ([#15](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/15)).

## 5.1.2 - 2024-10-18

### Fixed

- Fixed the required indicators in the plugin settings page ([#13](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/13)).

## 5.1.1 - 2024-10-18

### Fixed

- Fixed the ability to leave the API key and secret empty ([#13](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/13)).

## 5.1.0 - 2024-06-25

### Changed

- Purging a site now results in a single invalidation path sent to the CloudFront API, potentially saving on invalidation request charges.

## 5.0.0 - 2024-04-08

### Added

- Added compatibility with Craft 5.
