> # Common codebase
>
> Based on [don’t repeat yourself](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself) principle.

[![Patreon](https://img.shields.io/badge/patreon-donate-orange.svg)](https://www.patreon.com/octolab)
[![License](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](LICENSE)

## [Documentation](https://github.com/kamilsk/Common/wiki)

## Installation

### Git (development)

[Fork it before](https://github.com/kamilsk/Common/fork).

```bash
$ git clone git@github.com:<your github account>/Common.git
$ cd Common && composer install
$ git remote add upstream git@github.com:kamilsk/Common.git
```

#### Mirror

```bash
$ git remote add mirror git@bitbucket.org:kamilsk/common.git
```

### Composer (production)

```bash
$ composer require kamilsk/common:~3.1
```

## Pulse of repository

| Version / PHP | 5.5 | 5.6 | 7.0 | HHVM | Support                                           |
|:-------------:|:---:|:---:|:---:|:----:|:--------------------------------------------------|
| 2.3.x LTS     | -   | +   | +   | +    | Security support and bug fixing until 31 Dec 2018 |
| 3.x           | -   | -   | +   | -    | Security support and bug fixing until 3 Dec 2017  |
| 4.x LTS       | -   | -   | +   | -    | Active support and new features until 31 Dec 2018 |

_Other versions are not supported._

### [Changelog](CHANGELOG.md)

### General information

[![Build status](https://travis-ci.org/kamilsk/Common.svg?branch=3.x)](https://travis-ci.org/kamilsk/Common)
[![Tests status](http://php-eye.com/badge/kamilsk/common/tested.svg?branch=3.x-dev)](http://php-eye.com/package/kamilsk/common)
[![Latest stable version](https://poser.pugx.org/kamilsk/common/v/stable.png)](https://packagist.org/packages/kamilsk/common)

### Code quality

[![Code coverage](https://scrutinizer-ci.com/g/kamilsk/Common/badges/coverage.png?b=3.x)](https://scrutinizer-ci.com/g/kamilsk/Common/?branch=3.x)
[![Scrutinizer code quality](https://scrutinizer-ci.com/g/kamilsk/Common/badges/quality-score.png?b=3.x)](https://scrutinizer-ci.com/g/kamilsk/Common/?branch=3.x)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/37088460-5995-43cd-9dcb-920ca502984d/big.png)](https://insight.sensiolabs.com/projects/37088460-5995-43cd-9dcb-920ca502984d)

### Stats

[![Total downloads](https://poser.pugx.org/kamilsk/common/downloads.png)](https://packagist.org/packages/kamilsk/common)
[![Monthly downloads](https://poser.pugx.org/kamilsk/common/d/monthly.png)](https://packagist.org/packages/kamilsk/common)
[![Daily downloads](https://poser.pugx.org/kamilsk/common/d/daily.png)](https://packagist.org/packages/kamilsk/common)
[![Total references](https://www.versioneye.com/php/kamilsk:common/reference_badge.svg)](https://www.versioneye.com/php/kamilsk:common/references)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

### [package.meta](https://github.com/octolab/pmc)

We using `package.meta` to describe the package instead of `composer.json`.
Thus, changes in `composer.json` file directly is not allowed.

## Security

If you discover any security related issues, please email feedback@octolab.org instead of using the issue tracker.

## Feedback

[![@kamilsk](https://img.shields.io/badge/author-%40kamilsk-blue.svg)](https://twitter.com/ikamilsk)
[![@octolab](https://img.shields.io/badge/sponsor-%40octolab-blue.svg)](https://twitter.com/octolab_inc)

## Notes

- made with ❤️ by [OctoLab](https://www.octolab.org/)

[![Analytics](https://ga-beacon.appspot.com/UA-109817251-23/unsupported/Common/readme)](https://github.com/igrigorik/ga-beacon)
