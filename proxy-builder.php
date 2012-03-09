<?php
/*
Plugin Name: Proxy Builder
Plugin URL: https://github.com/inspectorfegter/WP-Proxy-Builder
Description: Add a proxy for a specific request URI
Version: 0.1
Author: Brian Fegter
Author URI: http://coderrr.com
License: GPLv2
*/

/** Proxy Builder Class
 * This class allows you to commandere a URI segment and turn it into a full-fleged HTTP proxy
*/
class ProxyBuilder{
    protected $proxies;
    protected $host;
    
    /** Instantiate Object
     * @param array $proxies Key/Value pair of URI segment => Proxy URL. Multiple proxies may be passed.
     * @return void
    */
    function __construct($proxies = array()){
        
        # Validate input (somewhat...)
        if(empty($proxies) || !is_array($proxies))
            return;
        
        # Set proxies and host vars
        $this->proxies = $proxies;
        $this->host = $_SERVER['SERVER_NAME'];
        
        # Hijack WordPress
        $this->register_hooks();
    }
    
    /** Register WP Actions and Filters
     * @return void
    */
    protected function register_hooks(){
        if(!is_admin())
            add_action('init', array(&$this, 'add_proxy'), 0);
    }
    
    /** Check for the specified URI segments and add the proxy functionality
     * @return void
    */
    public function add_proxy(){
        
        # Iterate through all supplied proxies
        foreach($this->proxies as $uri => $url){
            
            # Check for URI match at the begining of the request URI line
            if(preg_match("~^/$uri~", $_SERVER['REQUEST_URI'])){
                
                # Set an alternate content URL for possible modification
                $content_url = $url;
                
                # Set the replacement URL base
                $replace_url = "http://$this->host/$uri";
                
                # Generate an array of URI segments
                $uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                
                # If request is not the proxy root
                if(count($uri_parts) > 1){
                    
                    # Trash our URI segment since we already know it
                    unset($uri_parts[0]);
                    
                    # Check for WWW requests if supplied proxy domain is not WWW
                    if($uri_parts[1] == 'www' && !preg_match('/www/', $replace_url)){
                        
                        # Traxh the WWW segment as we don't need it anymore
                        unset($uri_parts[1]);
                        
                        # Build the new content URL to retrieve
                        $content_url .= '/'. implode('/', $uri_parts);
                        
                        # Inject WWW into the content URL
                        $content_url = str_replace('http://', 'http://www.', $content_url);
                    }
                    else
                        # Build the new content URL to retrieve
                        $content_url .= '/'. implode('/', $uri_parts); 
                }
                
                # Check for a query string and append it to the content URL
                $query = $_SERVER['QUERY_STRING'] ? '?'.esc_attr($_SERVER['QUERY_STRING']) : '';
                $content_url .= $query;
                
                # Retrieve the remote content
                $response = wp_remote_get($content_url);
                $content = wp_remote_retrieve_body($response);
                
                # Rewrite URLs make our proxy legit when others view our source code
                $content = str_replace($url, $replace_url, $content);
                
                # Create a WWW replacement URL in case of mismatch of URLs in the content
                $www_url = str_replace('http://', 'http://www.', $url);
                $content = str_replace($www_url, $replace_url.'/www', $content);
                
                # Add our proxy URL to IMG, A, LINK, SCRIPT, and FORM tags
                $content = str_replace('src="//', 'src="http://', $content);
                $content = str_replace('src="/', 'src="'.$replace_url.'/', $content);
                $content = str_replace('href="/', 'href="'.$replace_url.'/', $content);
                $content = str_replace('action="/', 'action="'.$replace_url.'/', $content);
                
                # Send file headers if retrieving single files
                $this->set_file_headers($content_url);
                
                # Send the response body to the browser
                echo $content;
                
                # Die WordPress, Die!
                exit;
            }
        }
    }
    
    /** Set file headers for single files.
     * @param string $content_url The URL from which to extract the file extension
     * @return void
    */
    protected function set_file_headers($content_url){
        
        # Find the file extension
        $ext =  substr(strrchr($content_url,'.'),1);
        
        # Set the proper header
        switch ($ext){
            case 'jpg':
                $type = 'image/jpeg';
                break;
            case 'png':
                $type = 'image/png';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'ico':
                $type = 'image/x-icon';
                break;
            case 'zip':
                $type = 'application/zip';
                break;
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'txt':
                $type = 'text/plain';
                break;
            case 'mp3':
                $type = 'audio/mpeg';
                break;
            case 'swf':
                $type = 'application/x-shockwave-flash';
                break;
            case 'css':
                $type = 'text/css';
                break;
            case 'js':
                $type = 'text/javascript';
                break;
            default:
        }
        if(isset($type))
            header("Content-Type: $type");
    }
}