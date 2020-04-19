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
$results = ['roundcube/plugin-installer'];

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

// remove deprecated packages from /p/provider-* files
$packages = json_decode(file_get_contents($DATA_DIR . '/a/packages.json'), true);
$providers = $packages['provider-includes'];

foreach ($providers as $key => $prop) {
  $filepath = $DATA_DIR . '/a/' . str_replace('%hash%', $prop['sha256'], basename($key));
  $data = json_decode(file_get_contents($filepath), true);
  // echo $key . PHP_EOL;

  foreach ($data['providers'] as $name => $val) {
    if (in_array($name, $results)) {
      unset($data['providers'][$name]);
    }
  }

  $jsonData = json_encode($data);
  $hash = hash('sha256', $jsonData);
  $destpath = '/p/' . str_replace('%hash%', $hash, basename($key));
  file_put_contents($DATA_DIR . $destpath, $jsonData);
  printf('Updated provider data file %s (%s)' . PHP_EOL, $key, $hash);
  $providers[$key]['sha256'] = $hash;
}

$packages['provider-includes'] = $providers;
file_put_contents($DATA_DIR . '/p/packages.json', json_encode($packages));
print('Updated cache data file /p/packages.json' . PHP_EOL);
