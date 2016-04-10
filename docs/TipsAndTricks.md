# Tips and tricks

## Callable sugar

```php
CallableSugar::begin([$http, 'get'])
    // if have thrown HttpTimeoutException, then will retry three times with interval of three second
    ->rescue(HttpTimeoutException::class)
    ->retry(3, 3000)
    // if have thrown HttpServiceUnavailableException, then will write to log file
    ->rescue(HttpServiceUnavailableException::class, [$logger, 'logHttpRequest'])
    // run $http::get($request)
    ->end($request = new Request('http://example.com'))
;
```
