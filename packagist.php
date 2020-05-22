<?php

/**
 * Emulate a packagist composer API by serving static cache files
 * from the old Roundcube plugins repository.
 */

require_once './vendor/autoload.php';

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\Response\Serializer as ResponseSerializer;
use Zend\Diactoros\Request\Serializer as RequestSerializer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use GuzzleHttp\Exception\RequestException;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

$DATA_DIR = realpath(__dir__ . '/data');

// create a log channel
$logger = new Logger('packagist');
$logger->pushHandler((new StreamHandler('./log/packagist.log', Logger::WARNING))
  ->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n\n", null, true))
);

// create a PSR7 request based on the current browser request.
$request = ServerRequestFactory::fromGlobals();

$uri = $request->getUri();
$path = urldecode($uri->getPath());

$cacheFile = $DATA_DIR . $path;

$logger->info('>> ' . RequestSerializer::toString($request));
$logger->info('>> CACHE FILE = ' . $cacheFile);

// create PSR7 reponse object
if ($path === '/downloads/' && $request->getMethod() === 'POST') {
  $response = new JsonResponse(["status" => "success"], 201);
} else if (is_readable($cacheFile)) {
  $contents = file_get_contents($cacheFile);
  $mtime = filemtime($cacheFile);

  $response = new TextResponse(
    $contents,
    200,
    [
      'Content-Type' => 'application/json; charset=utf-8',
      'Last-Modified' => date('r', $mtime),
      'ETag' => '"' . md5($contents) . '"',
    ]
  );
} else {
  $response = new JsonResponse([], 404, []);
}

$logger->info('<< ' . ResponseSerializer::toString($response));

// output response to the browser.
$emitter = new SapiEmitter();
$emitter->emit($response);
