[CHANGELOG](http://keepachangelog.com)
======================================

## [Unreleased]
### Changed
- now `Config\Loader\FileLoader::load()`
  - return content and `Config\Loader\FileLoader` does not store it
  - merge content of all included files and remove imports
- `Util\Math::getTwoTablePagination()` is static now
- `Config\Util\ArrayHelper` moved to `Util\ArrayHelper`
- `Doctrine\Migration\FileBasedMigration` follows [SemVer](http://semver.org)
- [git diff](/../../compare/1.2...master)

### Added
- interfaces
  - `Config\Loader\Parser\ParserInterface`
- classes
  - `Config\Loader\Parser\JsonParser`
  - `Config\Loader\Parser\YamlParser`

### Removed
- interfaces
  - `Config\Parser\ParserInterface`, use `Config\Loader\Parser\ParserInterface` instead
- classes
  - `Config\Parser\DipperYamlParser`, not supported now
  - `Config\Parser\SymfonyYamlParser`, use `Config\Loader\Parser\YamlParser` instead
  - `Config\Loader\JsonFileLoader`, use `Config\Loader\FileLoader` with `Config\Loader\Parser\JsonParser` instead
  - `Config\Loader\YamlFileLoader`, use `Config\Loader\FileLoader` with `Config\Loader\Parser\YamlParser` instead
- methods
  - `Config\Loader\FileLoader::getContent()`, use `Config\Loader\FileLoader::load()` and save output

## [1.2] - 2016-03-06
### Changed
- Up code quality and code coverage ([#19](../../issues/19))
- Support import section in config as strings ([#25](../../issues/25))
- [git diff](/../../compare/1.1...1.2)

### Deprecated
- package `secondparty/dipper`
- class `Config\JsonConfig`
- class `Config\YamlConfig`
- class `Config\Loader\JsonFileLoader`
- class `Config\Loader\YamlFileLoader`
- interface `Config\Parser\ParserInterface`
- class `Config\Parser\SymfonyYamlParser`
- class `Config\Parser\DipperYamlParser`
- method `Config\SimpleConfig::replace`
- method `Config\SimpleConfig::toArray`

## [1.1] - 2016-01-07
### Added
- Support multichannel for `Monolog` like [Monolog Cascade](https://github.com/theorchard/monolog-cascade)
([#20](/../../issues/20))

### Fixed
- Full support of configuration in json ([#22](/../../issues/22))

### Changed
- `SimpleConfig` now implements `ArrayAccess` and `Iterator`, and support composite key like `path:to:config`
([#24](/../../issues/24))
- `pimple/pimple` now is not suggest
- [git diff](/../../compare/1.0.2...1.1)

## [1.0] - 2015-12-20
### Changed
- First stable release
- Complete support `PHP 7.0` and `HHVM` (tested on 3.6.6)
- Move not specific classes from [CilexServiceProviders](https://github.com/kamilsk/CilexServiceProviders)
  ([#17](/../../issues/17))
- [git diff](/../../compare/0.4.2...1.0)
