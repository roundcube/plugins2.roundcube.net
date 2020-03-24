<?php

/**
 * CLI script to pull a recent list of roundcube-plugins from packagist.org
 * and remove those from the static cache files served as composer repository.
 */

require_once './vendor/autoload.php';

use GuzzleHttp\Client;

$DATA_DIR = realpath(__dir__ . '/data');
$SOURCE_URL = 'https://packagist.org/search.json?type=roundcube-plugin';

$client = new Client();
$next = $SOURCE_URL;
$results = [];

while ($next) {
  $response = $client->request('GET', $next);
  $data = json_decode($response->getBody(), true);

  if (isset($data['results'])) {
    foreach ($data['results'] as $r)
      $results[] = $r['name'];
  }

  $next = $data['next'] ?? null;
}

$destfile = $DATA_DIR . '/deprecated.json';
file_put_contents($destfile, json_encode($results, JSON_PRETTY_PRINT) . PHP_EOL);
printf('Dumped %d packages into %s' . PHP_EOL, count($results), $destfile);

// TODO: remove deprecated packages from /p/provider-* files

foreach (glob($DATA_DIR . '/a/provider-*.json') as $filepath) {
  $filename = basename($filepath);
  $data = json_decode(file_get_contents($filepath), true);
  echo $filename . PHP_EOL;

  foreach ($data['providers'] as $name => $val) {
    if (in_array($name, $results)) {
      unset($data['providers'][$name]);
    }
  }

  file_put_contents($DATA_DIR . '/p/' . $filename, json_encode($data));
  printf('Updated cache data file /p/%s' . PHP_EOL, $filename);
}
