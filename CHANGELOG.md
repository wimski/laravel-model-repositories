# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This changelog is initialized in release 1.0.0

## [Unreleased]

## [v6.0.0] - 2025-02-26

### Added
* Laravel 12 support

### Changed
* Update minimum required PHP version to 8.3
* Update ModelRepositoryInterface signatures
* Make AbstractModelRepository readonly and use promoted property
* Make typing of ModelRepositoryServiceProvider::$repositories more explicit

### Removed
* Laravel 9 and 10 support
* Carbon

## [v5.0.0] - 2025-01-17

### Changed
* ModelRepositoryInterface::builder() now only returns a Builder

## [v4.0.0] - 2024-03-15

### Added
* Laravel 11 support

### Removed
* PHP 7.4 and Laravel 8 support

## [v3.0.0] - 2023-03-30

### Changed
* Update builder method

## [v2.8.0] - 2023-03-29

### Added
* Cursor method to repository

## [v2.7.0] - 2023-02-16

### Added
* Laravel 10 support

## [v2.6.0] - 2023-02-07

### Added
* Builder method to repository

## [v2.5.0] - 2022-08-18

### Added
* WhereIn method to repository
* WhereNotIn method to repository

## [v2.4.0] - 2022-06-03

### Added
* First methods to repository

## [v2.3.0] - 2022-03-21

### Added
* Creation methods to repository

## [v2.2.0] - 2022-02-23

### Added
* Laravel 9 support

## [v2.1.1] - 2022-01-18

### Fixed
* Array type for $column in where methods

## [v2.1.0] - 2022-01-18

### Added
* Extra type for $column in where methods

## [v2.0.0] - 2022-01-17

### Changed
* Refactor repository methods

## [v1.1.0] - 2022-01-11

### Added
* CHANGELOG.md
* Columns argument to ModelRepositoryInterface::all to be compatible with the underlying Eloquent model
* FQN options for contract and repository to ModelRepositoryMakeCommand

[Unreleased]: https://github.com/wimski/laravel-model-repositories/compare/v6.0.0...master
[v6.0.0]: https://github.com/wimski/laravel-model-repositories/compare/v5.0.0...v6.0.0
[v5.0.0]: https://github.com/wimski/laravel-model-repositories/compare/v4.0.0...v5.0.0
[v4.0.0]: https://github.com/wimski/laravel-model-repositories/compare/v3.0.0...v4.0.0
[v3.0.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.8.0...v3.0.0
[v2.8.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.7.0...v2.8.0
[v2.7.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.6.0...v2.7.0
[v2.6.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.5.0...v2.6.0
[v2.5.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.4.0...v2.5.0
[v2.4.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.3.0...v2.4.0
[v2.3.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.2.0...v2.3.0
[v2.2.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.1.1...v2.2.0
[v2.1.1]: https://github.com/wimski/laravel-model-repositories/compare/v2.1.0...v2.1.1
[v2.1.0]: https://github.com/wimski/laravel-model-repositories/compare/v2.0.0...v2.1.0
[v2.0.0]: https://github.com/wimski/laravel-model-repositories/compare/v1.1.0...v2.0.0
[v1.1.0]: https://github.com/wimski/laravel-model-repositories/compare/v1.0.0...v1.1.0
