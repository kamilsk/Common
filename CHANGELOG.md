[CHANGELOG](http://keepachangelog.com)
======================================

# Version 3

## [3.1.0] - 2016-06-07
### Added
- development dependencies
  - `composer/composer:~1.0` for testing Composer scripts
  - `symfony/asset:~3.0` for using the AdminLte package to register asset alias in Twig
  - `symfony/twig-bridge:~3.0` for using the Twig AssetExtension to register AdminLte asset alias
- classes
  - final `Asset\AdminLtePackage`
  - abstract `Common\Command\Command` (ported from `kamilsk/cilex-service-providers`)
  - final `Command\Doctrine\CheckMigrationCommand` (ported from `kamilsk/cilex-service-providers`)
  - final `Command\Doctrine\GenerateIndexNameCommand` (ported from `kamilsk/cilex-service-providers`)
  - final `Composer\Script\AdminLte\Config`
  - final `Composer\Script\Processor`
  - abstract `Composer\Script\Publisher`
  - final `Config\Loader\Parser\IniParser`
  - final `Exception\XmlException`
  - final `Twig\Extension\AssetExtension`
  - final `Util\Ini`
  - final `Util\Xml`
- interfaces
  - `Composer\Script\ConfigInterface`
- methods
  - `Util\Call::go` to prevent trowing exception
  - `Util\Json::softEncode` and `Util\Json::softDecode` without throwing exception but returning it as second value
- functions
  - `camelize`
- zero cost assertions
- support `@id` as reference to another component in `monolog` configuration

### Changed
- development dependencies
  - `jolicode/jolinotif` up to `^1.0.5`
- classes
  - finalized
    - `Composer\Script\AdminLte\Publisher`
    - `Config\FileConfig`
    - `Config\Loader\FileLoader`
    - `Config\Loader\Parser\JsonParser`
    - `Config\Loader\Parser\YamlParser`
    - `Doctrine\Util\ConfigResolver`
    - `Doctrine\Util\Limiter`
    - `Doctrine\Util\Parser`
    - `Monolog\Handler\BulletproofStreamHandler`
    - `Monolog\Handler\DesktopNotificationHandler`
    - `Monolog\Processor\SignProcessor`
    - `Monolog\Processor\TimeExecutionProcessor`
    - `Monolog\Service\ComponentBuilder`
    - `Monolog\Service\ComponentFactory`
    - `Monolog\Service\Locator`
    - `Monolog\Util\Dumper`
    - `Util\ArrayHelper`
    - `Util\Call`
    - `Util\Json`
    - `Util\System`
  - `Config\SimpleConfig` now invokable and takes offset
  - `Util\Call` now invokable and takes the same arguments that the `begin` callable
  - `DesktopNotificationHandler` now takes string and int as `level` argument
- methods
  - `Util\Call::rescue` now takes `checkSubclasses` to check exception's parents
- moving to [package.meta](https://github.com/octolab/pmc) to describe composer package
- code optimizations (using greediness in regular expressions, removing while loops,
using arbitrary expression dereferencing instead intermediate vars, etc.)
- [git diff](/../../compare/3.0.5...3.x)

### Removed
- classes
  - `Composer\Script\AdminLte\Processor`, use `Composer\Script\Processor` instead

## [3.0.x] - 2016-04-23
### Added
- classes
  - `Monolog\Service\Locator`
  - `Monolog\Service\ComponentBuilder`
  - `Monolog\Service\ComponentFactory`
  - `Test\ClassAvailability`
- methods
  - `Util\Json::new()`

### Changed
- PHP support is up to 7.0 version
- classes
  - `Util\CallableSugar` was renamed to `Util\Call` and not it not final
- method signature
  - all methods, where it is possible, changed scalar and return type declaration
  - `Util\ArrayHelper::findByPath(string $path, array $scope)` arguments were swapped
  - `Util\Call::end()` _throws_ part (`Throwable` support now)
  - `Util\Json::__construct()` all arguments are required now, use `Util\Json::new()` instead
- [git diff](/../../compare/2.3.1...3.0.5)

### Removed
- classes
  - `Monolog\LoggerLocator`, use `Monolog\Service\Locator` instead

# Version 2

## [2.3.x] - LTS - 2016-04-20
### Changed
- PHP support is up to 5.6 version
- [git diff](/../../compare/2.2.1...2.3.1)

## [2.2] - 2016-04-12
### Added
- classes
  - `Composer\Script\AdminLte\Processor`
  - `Composer\Script\AdminLte\Publisher`
  - `Monolog\Processor\SignProcessor`
  - `Util\CallableSugar`

### Changed
- [git diff](/../../compare/2.1.6...2.2.1)

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
- [git diff](/../../compare/2.0.2...2.1.6)

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

# Version 1

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
