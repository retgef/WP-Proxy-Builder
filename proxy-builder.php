<?php
/*
Plugin Name: Proxy Builder
Description: Add a proxy for a specific request URI
Version: 0.1
Author: Brian Fegter
Author URI: http://coderrr.com
*/
class ProxyBuilder{
    protected $proxies = array();
    protected $host;
    
    function __construct($proxies){
        $this->proxies = $proxies;
        $this->host = $_SERVER['SERVER_NAME'];
        if(!is_admin())
            add_action('init', array(&$this, 'add_proxy'), 0);
    }
    
    public function add_proxy(){
        foreach($this->proxies as $uri => $url){ 
            if(preg_match("~^/$uri~", $_SERVER['REQUEST_URI'])){
                $uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $replace_url = "http://$this->host/$uri";
                $content_url = $url;
                if(count($uri_parts) > 1){
                    unset($uri_parts[0]);
                    if($uri_parts[1] == 'www' && !preg_match('/www/', $replace_url)){
                        unset($uri_parts[1]);
                        $content_url .= '/'. implode('/', $uri_parts); 
                        $content_url = str_replace('http://', 'http://www.', $content_url);
                    }
                    else
                        $content_url .= '/'. implode('/', $uri_parts); 
                }
                
                $query = $_SERVER['QUERY_STRING'] ? '?'.esc_attr($_SERVER['QUERY_STRING']) : '';
                $content_url .= $query;
                
                $response = wp_remote_get($content_url);
                $content = wp_remote_retrieve_body($response);
                
                $www_url = str_replace('http://', 'http://www.', $url);
                $content = str_replace($www_url, $replace_url.'/www', $content);
                $content = str_replace($url, $replace_url, $content);
                $content = str_replace('src="/', 'src="'.$replace_url.'/', $content);
                $content = str_replace('href="/', 'href="'.$replace_url.'/', $content);
                $content = str_replace('action="/', 'action="'.$replace_url.'/', $content);
                
                $this->set_file_headers($content_url);
                echo $content;
                exit;
            }
        }
    }
    
    protected function set_file_headers($content_url){
        $ext =  substr(strrchr($content_url,'.'),1);
        switch ($ext){
            case 'jpg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'gif':
                header('Content-Type: image/gif');
                break;
            case 'zip':
                header('Content-Type: application/zip');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            case 'txt':
                header('Content-Type: text/plain');
                break;
            case 'mp3':
                header('Content-Type: audio/mpeg');
                break;
            case 'swf':
                header('Content-Type: application/x-shockwave-flash');
                break;
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: text/javascript');
                break;
            default:
        }
    
    }
}

$proxies = array(
    'google' => 'http://www.google.com'
);
new ProxyBuilder($proxies);