[CHANGELOG](http://keepachangelog.com)
======================================

## [3.x] - unreleased
### Added
- classes
  - `Monolog\Service\Locator`
  - `Monolog\Service\ComponentBuilder`
  - `Monolog\Service\ComponentFactory`
- methods
  - `Util\Json::new()`

### Changed
- PHP support is up to 7.0 version
- classes
  - `Util\CallableSugar` was renamed to `Util\Call` and not it not final
- method signature
  - all methods, where it is possible, changed scalar and return type declaration
  - `Util\Call::end()` _throws_ part (`Throwable` support now)
  - `Util\Json::__construct()` all arguments are required now, use `Util\Json::new()` instead
- [git diff](/../../compare/master...3.x)

### Removed
- classes
  - `Monolog\LoggerLocator`, use `Monolog\Service\Locator` instead

## [2.3.x] - LTS - 2016-04-20
### Changed
- PHP support is up to 5.6 version
- [git diff](/../../compare/2.2.2...2.3.0)

## [2.2] - 2016-04-12
### Added
- classes
  - `Composer\Script\AdminLte\Processor`
  - `Composer\Script\AdminLte\Publisher`
  - `Monolog\Processor\SignProcessor`
  - `Util\Call`

### Changed
- [git diff](/../../compare/2.1.5...2.2.2)

## [2.1] - 2016-03-17
### Added
- classes
  - `Monolog\Handler\DesktopNotificationHandler`
  - `Util\System`
- packages
  - `jolicode/jolinotif`

### Changed
- classes
  - `Config\SimpleConfig` now implements `Countable` and `JsonSerializable`
  - `Monolog\LoggerLocator` now implements `Countable` and `Iterator`
- methods
  - `Monolog\LoggerLocator::keys()` now internal and will be isolated in future version
- [git diff](/../../compare/2.0.2...2.1.5)

### Fixed
- bug [#34](/../../issues/34)

## [2.0] - 2016-03-08
### Added
- classes
  - `Config\Loader\Parser\JsonParser`
  - `Config\Loader\Parser\YamlParser`
  - `Monolog\LoggerLocator` with lazy loading support
- interfaces
  - `Config\Loader\Parser\ParserInterface`
- methods
  - `Doctrine\Util\Limiter::getTwoTablePagination()`

### Changed
- `$check` was removed from `Config\FileConfig::load()`
- `Config\FileConfig::load()` now wait `$placeholders` for the second argument
- `Config\SimpleConfig::__construct()` now wait `$placeholders` for the second argument
- now `Config\Loader\FileLoader::load()`
  - return content and `Config\Loader\FileLoader` does not store it
  - merge content of all included files and remove imports
- `Config\Util\ArrayHelper` moved to `Util\ArrayHelper`
- `Doctrine\Migration\FileBasedMigration` follows [SemVer](http://semver.org)
- `Doctrine\Util\ConfigResolver::resolve()` is static now
- `Doctrine\Util\Parser::extractSql()` is static now
- `Monolog` configuration now completely changed:
```yml
channels:
  app:
    arguments: { name: APP }
    handlers:
    - file
    - chrome
  db:
    name: app
    handlers:
    - file
    processors:
    - memory
    - time
handlers:
  file:
    type: stream
    arguments: ["%root%/info.log", info, true]
    formatter: normal
  chrome:
    type: chrome_php
    arguments: { level: info, bubble: true }
    formatter: chrome
processors:
  memory:
    type: memory_usage
  time:
    class: OctoLab\Common\Monolog\Processor\TimeExecutionProcessor
formatters:
  normal:
    type: normalizer
  chrome:
    type: chrome_php
```
- [git diff](/../../compare/1.2...2.0.2)

### Fixed
- `Config\Loader\FileLoader::load()` ([#30](../../issues/30), [#32](../../issues/32))

### Removed
- classes
  - `Config\JsonConfig`, use `Config\FileConfig` instead
  - `Config\YamlConfig`, use `Config\FileConfig` instead
  - `Config\Parser\DipperYamlParser`, not supported now
  - `Config\Parser\SymfonyYamlParser`, use `Config\Loader\Parser\YamlParser` instead
  - `Config\Loader\JsonFileLoader`, use `Config\Loader\FileLoader` with `Config\Loader\Parser\JsonParser` instead
  - `Config\Loader\YamlFileLoader`, use `Config\Loader\FileLoader` with `Config\Loader\Parser\YamlParser` instead
  - `Monolog\Util\ConfigResolver`, use `Monolog\LoggerLocator` instead
  - `Util\Math`, merged with `Doctrine\Util\Limiter` now
- interfaces
  - `Config\Parser\ParserInterface`, use `Config\Loader\Parser\ParserInterface` instead
- methods
  - `Config\SimpleConfig::toArray()`, use array access instead
  - `Config\SimpleConfig::transform()`, now it is internal, use `$placeholders` argument in
  `Config\FileConfig::load()` and `Config\SimpleConfig::__construct()`
  - `Config\Loader\FileLoader::getContent()`, use `Config\Loader\FileLoader::load()` and save output
  - `Util\Math::getTwoTablePagination()`, use static `Doctrine\Util\Limiter::getTwoTablePagination()` instead

## [1.2] - 2016-03-06
### Changed
- up code quality and code coverage ([#19](../../issues/19))
- support import section in config as strings ([#25](../../issues/25))
- [git diff](/../../compare/1.1...1.2)

### Deprecated
- classes
  - `Config\JsonConfig`
  - `Config\YamlConfig`
  - `Config\Loader\JsonFileLoader`
  - `Config\Loader\YamlFileLoader`
  - `Config\Parser\SymfonyYamlParser`
  - `Config\Parser\DipperYamlParser`
- interfaces
  - `Config\Parser\ParserInterface`
- methods
  - `Config\SimpleConfig::replace`
  - `Config\SimpleConfig::toArray`
- packages
  - `secondparty/dipper`

## [1.1] - 2016-01-07
### Added
- support multichannel for `Monolog` like [Monolog Cascade](https://github.com/theorchard/monolog-cascade)
([#20](/../../issues/20))

### Changed
- `SimpleConfig` now implements `ArrayAccess` and `Iterator`, and support composite key like `path:to:config`
([#24](/../../issues/24))
- `pimple/pimple` now is not suggest
- [git diff](/../../compare/1.0.2...1.1)

### Fixed
- full support of configuration in json ([#22](/../../issues/22))

## [1.0] - 2015-12-20
### Changed
- complete support `PHP 7.0` and `HHVM` (tested on 3.6.6)
- move not specific classes from [CilexServiceProviders](https://github.com/kamilsk/CilexServiceProviders)
  ([#17](/../../issues/17))
- [git diff](/../../compare/0.4.2...1.0.2)
