# Testing

## Usage

1. Install the [Craft Pest](https://craft-pest.com) plugin.
    ```shell
    composer require-dev markhuot/craft-pest --dev
    php craft plugin/install pest
    ```
2. Copy `phpunit.xml` to the root of your project.
3. Execute the following command from the root of your project.
    ```shell
    php craft pest/test --test-directory=vendor/putyourlightson/craft-blitz-cloudfront/tests/pest
    ```

### Makefile

A Makefile can be used to simplify the running of tests.

```makefile
# Default values
vendor?=putyourlightson
plugin?=blitz-cloudfront
filter?=test
test:
    php craft pest/test --test-directory=vendor/$(vendor)/craft-$(plugin)/tests/pest --filter=$(filter)
```

```shell
# Run tests using the default values
make test

# Run tests using all optional values
make test vendor=putyourlightson plugin=blitz-cloudfront filter=queue
```
