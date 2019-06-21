# TYPO3 frontend client

This package provides an API to get a response from any
TYPO3 frontend request in any context, without the need to
do an actual HTTP request.

Examples:

## Doing a TYPO3 frontend request
```php
$request = new \TYPO3\CMS\Core\Http\ServerRequest($uri)
$client = new \Helhum\Typo3FrontendRequest\Typo3Client();
try {
    $response = $client->send($request);
    $body = (string)$response->getBody();
} catch (\Helhum\Typo3FrontendRequest\RequestFailed $e) {
    throw new \RuntimeException('Could not fetch  page "' . $uri . '", reason: ' . $e->getMessage(), 1552081550, $e);
}
```


## Doing a TYPO3 frontend request with a user being authenticated
```php
$request = new \TYPO3\CMS\Core\Http\ServerRequest($uri)
$request = $request->withHeader(
    'x-typo3-frontend-user',
    (string)$context->getPropertyFromAspect('frontend.user', 'id')
);
$client = new \Helhum\Typo3FrontendRequest\Typo3Client();
$response = $client->send($request);
$body = (string)$response->getBody();
```

