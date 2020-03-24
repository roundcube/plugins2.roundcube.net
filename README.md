# Roundcube Plugins Repository (Packagist Proxy)

Webservice serving the Roundcube Plugins Repository

This project is a replacement for the old packagist-based repository for Roundcube Plugins.

It consists of a **server part** which emulates the packagist API to act as a Composer repository.
It serves static files from the frozen packagist repository in order to keep old insrallations
working while still maintained Roudcube plugins are now published at packagist.org and served from there.

The second part is a **singe-page app** providing a minimalist UI to search the packagist.org repository
for Roundcube plugins.

