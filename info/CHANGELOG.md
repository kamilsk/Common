CHANGELOG for 1.x
=================

* 1.2 (2016-02-xx)

  * Up code quality and code coverage ([#19](../../issues/19))
  * [git diff](/../../compare/1.1...master)

* 1.1 (2016-01-07)

  * Support multichannel for `Monolog` like [Monolog Cascade](https://github.com/theorchard/monolog-cascade)
  ([#20](/../../issues/20))
  * Full support of configuration in json ([#22](/../../issues/22))
  * `SimpleConfig` now implements `ArrayAccess` and `Iterator`, and support composite key like `path:to:config`
  ([#24](/../../issues/24))
  * `pimple/pimple` now is not suggest
  * [git diff](/../../compare/1.0.2...1.1)

* 1.0 (2015-12-20)

  * First stable release
  * Complete support `PHP 7.0` and `HHVM` (tested on 3.6.6)
  * Move not specific classes from [CilexServiceProviders](https://github.com/kamilsk/CilexServiceProviders)
  ([#17](/../../issues/17))
  * [git diff](/../../compare/0.4.2...1.0)
