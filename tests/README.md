# Testing

## Static Analysis

To run static analysis on the plugin, install [PHPStan for Craft CMS](https://github.com/craftcms/phpstan) and run the following command from the root of your project.

```shell
./vendor/bin/phpstan analyse -c vendor/putyourlightson/craft-blitz-cloudfront/phpstan.neon  --memory-limit 1G
```

## Easy Coding Standard

To run the Easy Coding Standard on the plugin, install [ECS for Craft CMS](https://github.com/craftcms/ecs) and run the following command from the root of your project.

```shell
./vendor/bin/ecs check -c vendor/putyourlightson/craft-blitz-cloudfront/ecs.php
```

## Pest Tests

To run Pest tests, install [Craft Pest](https://craft-pest.com/) and run the following command from the root of your project.

```shell
php craft pest/test --test-directory=vendor/putyourlightson/craft-blitz-cloudfront/tests/pest
```

Or to run a specific test.

```shell
php craft pest/test --test-directory=vendor/putyourlightson/craft-blitz-cloudfront/tests/pest --filter=CacheRequestTest
```
