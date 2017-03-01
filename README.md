# **Use this only in a development environment**

# PHP-Built-in-web-server-Router

This library comes with **caching support** for [**PHP built-in web-server**](http://php.net/manual/ro/features.commandline.webserver.php)

Router.php is loaded before index.php, so you can:
* Have **CORS** enabled 
* Set up your environment variables and constants
* And **CACHE EVERYTHING**

It also comes with a custom console function to output string|array into terminal ( check out index.php for usage ).

Don't forget to delete it after usage, this function is meant only for Development !

```console_output( $string , 'another', array('show'=>1) , ...args )```

# Requirements
PHP 5.4.0+ and /src/mimes.json file.

# Usage

Bellow you'll find a few examples. Additional params may be found [here](http://php.net/manual/ro/features.commandline.webserver.php)

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
###
# ALLOW CORS
###
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, CUSTOMREQUEST, REQUEST');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Access-Control-Allow-Origin, X-Token');

    
class PHP_Webserver_Router{...}
    
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


    
$php_web_server->listen();
```

# Notes
I've tested this with simple php files and the following php frameworks:
* Yii2 ( i know Yii2 has [it's own webserver](http://www.yiiframework.com/wiki/819/php-built-in-server-integration/), but it doesn't have caching support unfortunately )
* CodeIgniter 3
* Wordpress ( You may encounter [The White Screen of Death](https://codex.wordpress.org/Common_WordPress_Errors) upon installing, if so, you will need to manually create and modify wp-config.php with the database params )

On the first request sent to router.php, it will attempt to download and create mimes.json, if that fails, you can find mimes.json in ./src/ and copy it next to router.php

Personally I bind the webserver to ```php -S 0.0.0.0 ...``` so I can test from other remote devices.

# Thanks
The method create_mime_file() is a modified version of [Josh Sean's generateUpToDateMimeArray function](http://php.net/manual/ro/function.mime-content-type.php#107798)