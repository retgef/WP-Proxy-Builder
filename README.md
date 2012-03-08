WP Proxy Builder
================

Target a specific URI segment AKA 'page slug' as a HTTP proxy.

Installation
------------

* Clone to your wp-content/plugins folder
* Add the following to your functions.php file:
`
$proxy = array('uri-segment' => 'http://domain.com');
new ProxyBuilder($proxy);
`

Disclaimer
----------

I am not responsible for illegal use of this plugin. Please act responsibly as this is a proof-of-concept for adding a proxy via WordPress.