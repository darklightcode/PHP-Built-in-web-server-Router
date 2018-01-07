# **Use this only in a development environment**

# PHP-Built-in-web-server-Router
![PHP-Built-in-web-server-Router](./src/php_5.png)

This library comes with **caching support** for [**PHP built-in web-server**](http://php.net/manual/en/features.commandline.webserver.php)

Router.php is loaded before index.php, so you can:
* Have **CORS** enabled 
* Set up your environment variables and constants
* And **CACHE EVERYTHING**

It also comes with a custom console function to output string|array into terminal ( check out index.php for usage ).

Don't forget to delete it after usage, this function is meant only for Development !

```console_output( $string , 'another', array('show'=>1) , ...args )```

### NODE.JS
I highly recommend [node-php-awesome-server](https://www.npmjs.com/package/node-php-awesome-server) if you need a php webserver in a node environment, it uses this router as default router, and much more various configuration options.
```
npm install node-php-awesome-server --save-dev
```
If you install this as a npm package you can only retrieve the absolute paths of the router, the library file and a cURL certificate.
```
npm install php-built-in-web-server-router --save-dev
```
```javascript
let router = require('php-built-in-web-server-router');

console.log('Router path: ', router.path);
console.log('Router library: ', router.lib);
console.log('cURL certificate: ', router.cert);

```

# Requirements
PHP 5.4.0+ and /src/mimes.json file.

# Usage

Bellow you'll find a few examples. Additional params may be found [here](http://php.net/manual/en/features.commandline.webserver.php)

1. If router.php is located in the root folder
```cli
php -S localhost:8000 router.php
```
2. If router.php is located in separate folder:
```cli
php -S localhost:8000 misc/router.php
```
3. If your project structure is something like this, use '-t' parameter to set the document root to your public folder

```
./www/
    - your_php_framework/
        - private_framework_stuff/
          ...
        - public/
    - misc/
        - router.php
        - mimes.json
    - onefile.php
    
    
 php -S localhost:8000 -t ./your_php_framework/public misc/router.php 
```

# Edit router.php

This are the default settings.


```
# class PHP_Webserver_Router{...}
#    or   

include('router.class.php');
    
###
# Set up early your environment variables/constants
###
    
$_SERVER["ENVIRONMENT"] = "development";
error_reporting(E_ALL);

 
$php_web_server = new PHP_Webserver_Router();

    
###
# Uncomment to Disable http output in console:
###
//$php_web_server->log_enable = FALSE;

###
# Change this if your "index.php" has another name. By default is index.php
###
//$php_web_server->indexPath = "my_new_index_file.php";

return $php_web_server->listen();
```

# Notes
I've tested this with the following php frameworks:
* Slim 3 ( **the newest addition to this list** )
* Yii2
* CodeIgniter 3
* Wordpress 4
* Drupal 7 and 8

On the first request sent to router.php, it will attempt to download and create mimes.json, if that fails, you can find mimes.json in ./src/ and copy it next to router.php

Personally I bind the webserver to ```php -S 0.0.0.0 ...``` so I can test from other remote devices.

# Thanks
The method create_mime_file() is a modified version of [Josh Sean's generateUpToDateMimeArray function](http://php.net/manual/ro/function.mime-content-type.php#107798)