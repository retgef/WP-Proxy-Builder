WP Proxy Builder
================

Target a specific URI segment AKA 'page slug' as a HTTP proxy.

Installation
------------

* Clone to your wp-content/plugins folder

Usage
-----

* Single Proxy

`$proxy = array('uri-segment' => 'http://domain.com');`

`new ProxyBuilder($proxy);`

* Multiple Proxies

`$proxies = array('uri-segment' => 'http://domain.com', 'uri-segment-2' => 'http://domain1.com');`

`new ProxyBuilder($proxies);`

Disclaimer
----------

I am not responsible for illegal or ill-intended usage of this plugin. Please act responsibly as this is a proof-of-concept for adding a proxy via WordPress.