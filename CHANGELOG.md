# Release Notes for Blitz CloudFront Purger

## 3.0.0-beta.1 - Unreleased
### Added
- Added compatibility with Blitz 4.

## 2.0.9 - 2022-02-01
### Fixed
- Fixed invalidating paths that were missing trailing slashes when the `addTrailingSlashesToUrls` general config was `true`.

## 2.0.8 - 2022-01-02
### Fixed
- Fixed invalidation errors by URL encoding non-reserved special characters in paths ([#6](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/6)).

## 2.0.7 - 2021-12-13
### Fixed
- Fixed an error that could be thrown if AWS returned a null error message ([#5](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/5)).

## 2.0.6 - 2020-12-07
### Added
- Added the “Warm Cache Delay” setting to the purger settings page.
- Added logging of exceptions on failed requests to CloudFront.

## 2.0.5 - 2020-10-25
### Fixed
- Fixed an issue in which `Quantity` did not match the number of `Items` ([#4](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/4)).

## 2.0.4 - 2020-08-17
### Changed
- If either the API key or secret is empty then it is assumed that we are running on EC2 and we have an IAM role assigned ([#3](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/3)).
- Site URIs to purge can be modified using the `EVENT_BEFORE_PURGE_CACHE` event.

### Fixed
- The Distribution ID can now be set to an environment variable ([#2](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/2)).

## 2.0.3 - 2020-03-31
### Fixed
- Fixed a bug when purging individual URIs ([#1](https://github.com/putyourlightson/craft-blitz-cloudfront/issues/1)).

## 2.0.2 - 2020-01-29
### Changed
- Removed the regions setting as the CloudFront service endpoint only allows connecting through the `us-east-1` region.

## 2.0.1 - 2020-01-25
### Fixed
- Fixed a bug in getting URLS to purge.

## 2.0.0 - 2020-01-22
### Changed
- Changed minimum required version of Blitz to 3.0.0.

## 1.0.0 - 2019-04-15
- Initial release.
