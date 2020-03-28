# Roundcube Plugins Repository (Packagist Proxy)

Webservice serving the Roundcube Plugins Repository

This project is a replacement for the old packagist-based repository for Roundcube Plugins.

It consists of a **server part** which emulates the packagist API to act as a Composer repository.
It serves static files from the frozen packagist repository in order to keep old insrallations
working while still maintained Roudcube plugins are now published at packagist.org and served from there.

The second part is a **singe-page app** providing a minimalist UI to search the packagist.org repository
for Roundcube plugins.

## Server

## `packagist.php`

Endpoint serving packagist API requests from Composer.

## `proxy.php`

Proxies API requests to the public packagist repositor API.

## `update.php`

Fetches roudcube-plugin packages registered at packagist.org and removes them from the static files
served by `packagist.php`. This is to be executed periodically by a cron job.


## Client

A simple [Vue.js](https://vuejs.org) application.
See the `README.md` file inside the `client` directory for details.

