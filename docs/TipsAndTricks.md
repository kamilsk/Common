# Tips and tricks

## Composer integration

### AdminLTE assets publishing

```json
{
  "require": {
    "almasaeed2010/adminlte": "~2.0",
    "kamilsk/common": "~2.2"
  },
  "scripts": {
    "configure": [
      "OctoLab\\Common\\Composer\\Script\\AdminLte\\Publisher::publish"
    ],
    "post-install-cmd": [
      "@configure"
    ],
    "post-update-cmd": [
      "@configure"
    ]
  },
  "extra": {
    "admin-lte": {
      "target": "web/assets/",
      "bootstrap": true,
      "plugins": true,
      "demo": true,
      "symlink": true,
      "relative": true
    }
  }
}
```

## Callable sugar

### Misuse

```php
Call::begin([$http, 'get'])
    // if an HttpTimeoutException will be thrown, then will attempt to repeat it three times every three seconds
    ->rescue(HttpTimeoutException::class)
    ->retry(3, 3000)
    // if an HttpServiceUnavailableException will be thrown, then will attempt to write $request into log file
    ->rescue(HttpServiceUnavailableException::class, [$logger, 'logHttpRequest'])
    // run $http::get($request)
    ->end($request = new Request('http://example.com'))
;
```

In example above you have opaque implementation that complicate refactoring in the future.

### Proper use

```php
Call::begin(function (Request $request) use ($http) {
    return $http->get($request);
})
    ->rescue(HttpTimeoutException::class)
    ->retry(3, 3000)
    ->rescue(HttpServiceUnavailableException::class, function (Request $request) use ($logger) {
        return $logger->logHttpRequest($request);
    })
    ->end($request = new Request('http://example.com'))
;
```
