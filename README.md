# Laravel Translatable

A Laravel package for translating Eloquent model attributes.

| Branch  | Status | Code Coverage |
| :------------- | :------------- | :------------- |
| Main | ![tests](https://github.com/internetguru/laravel-translatable/actions/workflows/test.yml/badge.svg?branch=main) | ![coverage](https://raw.githubusercontent.com/internetguru/laravel-translatable/refs/heads/badges/main-coverage.svg) |
| Staging | ![tests](https://github.com/internetguru/laravel-translatable/actions/workflows/test.yml/badge.svg?branch=staging) | ![coverage](https://raw.githubusercontent.com/internetguru/laravel-translatable/refs/heads/badges/staging-coverage.svg) |
| Dev | ![tests](https://github.com/internetguru/laravel-translatable/actions/workflows/test.yml/badge.svg?branch=dev) | ![coverage](https://raw.githubusercontent.com/internetguru/laravel-translatable/refs/heads/badges/dev-coverage.svg) |

## Installation

1. Install the package via Composer:

    ```sh
    # First time installation
    composer require internetguru/translatable
    # For updating the package
    composer update internetguru/translatable
    ```

## Run Tests Locally

In Visual Studio Code you can simpy use `Ctrl+Shift+B` to run the tests.

To run the tests manually, you can use the following commands:

```sh
# Build the Docker image
docker build -t laravel-translatable .
# Run the tests
docker run --rm laravel-translatable
# Both steps combined
docker build -t laravel-translatable . && docker run --rm laravel-translatable
```
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
