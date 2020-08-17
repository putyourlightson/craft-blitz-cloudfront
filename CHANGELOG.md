# Release Notes for Blitz CloudFront Purger

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
- Changed minimum required version of Blitz to 3.0.0.

## 1.0.0 - 2019-04-15
- Initial release.
