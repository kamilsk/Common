# Tips and tricks

## Callable sugar

### Misuse

```php
CallableSugar::begin([$http, 'get'])
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
CallableSugar::begin(function (Request $request) use ($http) {
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
