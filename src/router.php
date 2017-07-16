<?php

$cwd = dirname(__FILE__);

require($cwd . "/router.class.php");

$_SERVER["CI_ENV"] = "development";
error_reporting(E_ALL);

$php_web_server = new PHP_Webserver_Router();

###
# Uncomment to Disable http output in console:
###
//$php_web_server->log_enable = FALSE;

###
# Change this if your "index.php" has another name.
###
//$php_web_server->indexPath = "my_new_index_file.php";

return $php_web_server->listen();

