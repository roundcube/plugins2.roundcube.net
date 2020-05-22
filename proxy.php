<?php

/**
 * Proxies API requests to the public packagist repositor API
 * by injecting a `type=roundcube-plugin` query parameter.
 */

require_once './vendor/autoload.php';

use Proxy\Proxy;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Filter\RemoveEncodingFilter;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\Serializer as ResponseSerializer;
use Zend\Diactoros\Request\Serializer as RequestSerializer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use GuzzleHttp\Exception\RequestException;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

// create a log channel
$logger = new Logger('proxy');
$logger->pushHandler((new StreamHandler('./log/proxy.log', Logger::WARNING))
  ->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n\n", null, true))
);

// create a PSR7 request based on the current browser request.
$request = ServerRequestFactory::fromGlobals();

// create a guzzle client
$guzzle = new GuzzleHttp\Client();

// create the proxy instance
$proxy = new Proxy(new GuzzleAdapter($guzzle));

$proxy->filter(new RemoveEncodingFilter());
$proxy->filter(function ($request, $response, $next) use ($logger) {
  // manipulate the request object
  $uri = $request->getUri();
  $path = urldecode($uri->getPath());

  // inject a type=roundcube-plugin query param
  if ($path === '/search.json' || $path === '/packages/list.json') {
    parse_str($uri->getQuery(), $query);
    $query['type'] = 'roundcube-plugin';
    $uri = $uri->withQuery(http_build_query($query));
  }

  $request = $request->withUri($uri);

  $logger->info('>> ' . RequestSerializer::toString($request));

  // call the next item in the middleware
  try {
    $response = $next($request, $response);
  } catch (RequestException $e) {
    // pipe error response to client
    $response = $e->getResponse();
  }

  $logger->info('<< ' . ResponseSerializer::toString($response));

  return $response;
});

// forward the request and get the response.
$response = $proxy->forward($request)->to('https://packagist.org');

// output response to the browser.
$emitter = new SapiEmitter();
$emitter->emit($response);
