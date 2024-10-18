# Release Notes for Blitz CloudFront Purger

## 5.1.1 - 2024-10-18

### Fixed

- Fixed the ability to leave the API key and secret empty ([#13](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/13)).

## 5.1.0 - 2024-06-25

### Changed

- Purging a site now results in a single invalidation path sent to the CloudFront API, potentially saving on invalidation request charges.

## 5.0.0 - 2024-04-08

### Added

- Added compatibility with Craft 5.
