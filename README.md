WP Proxy Builder
================

Target a specific URI segment AKA 'page slug' as a simple HTTP proxy. This works well on sites that do not have dynamically generated JavaScript and HTML inserted after page load.

Installation
------------

* Clone to your plugins folder and activate Proxy Builder

Usage
-----

* Adding A Single Proxy

`$proxy = array('uri-segment' => 'http://domain.com');`

`new ProxyBuilder($proxy);`

* Adding Multiple Proxies

`$proxies = array('uri-segment' => 'http://domain.com', 'uri-segment-2' => 'http://domain1.com');`

`new ProxyBuilder($proxies);`

* View your proxy at http://yourdomain.com/uri-segment

Disclaimer
----------

I am not responsible for illegal or ill-intended usage of this plugin. Please act responsibly as this is a proof-of-concept for adding a proxy via WordPress.