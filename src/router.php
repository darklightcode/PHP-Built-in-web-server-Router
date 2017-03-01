<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, CUSTOMREQUEST, REQUEST');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Access-Control-Allow-Origin, X-Token');

class PHP_Webserver_Router
{

    var $log_enable = TRUE;

    var $request_uri = "";
    var $physical_file = "";
    var $extension = "";
    var $eTag = "";
    var $eTagHeader = "";
    var $last_modified = "";
    var $if_modified_since = "";
    var $file_length = "";
    var $indexPath = "index.php";
    var $mvc_enabled = TRUE;

    var $http_status = 200;

    function __construct()
    {

        $this->request_uri = urldecode(\filter_input(\INPUT_SERVER, 'REQUEST_URI', \FILTER_SANITIZE_ENCODED));
        $this->physical_file = $_SERVER['SCRIPT_FILENAME'];
        $this->extension = strrev(strstr(strrev($this->physical_file), '.', TRUE));
        $this->last_modified = filemtime($this->physical_file);
        $this->eTag = md5_file($this->physical_file);
        $this->file_length = filesize($this->physical_file);

        $this->if_modified_since = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
        $this->eTagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

    }

    function log_output()
    {

        if ($this->log_enable) {

            $host_port = $_SERVER["REMOTE_ADDR"] . ":" . $_SERVER["REMOTE_PORT"];
            $split = explode("?", urldecode($this->request_uri));

            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $split[0])) {
                $this->http_status = 404;
                clearstatcache();
            }

            $this->console(sprintf("%s [%s]: %s", $host_port, $this->http_status, urldecode($this->request_uri)));


        }

    }

    function process_request()
    {

        $split = explode("?", urldecode($this->request_uri));

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $split[0])) {

            header('HTTP/1.1 404 Not Found');
            $this->http_status = 404;

        }else {


            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->last_modified) . ' GMT');
            header('Etag: ' . $this->eTag);
            header('Cache-Control: public');

            if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $this->last_modified || $this->eTagHeader == $this->eTag) {

                header('HTTP/1.1 304 Not Modified');
                $this->http_status = 304;

            } else {

                $mime_type_db = $this->retrieve_mime_types();
                $mime_type = isset($mime_type_db[$this->extension]) ? $mime_type_db[$this->extension] : mime_content_type($this->physical_file);

                header('Content-Type: ' . $mime_type);
                header('Content-Length: ' . $this->file_length);
                @readfile($this->physical_file);

            }

        }

        $this->log_output();

    }

    function bootstrap()
    {

        if (!function_exists('console_output')) {

            function console_output()
            {

                call_user_func_array(array(new PHP_Webserver_Router(), 'console'), func_get_args());

            }

        }

        chdir($_SERVER['DOCUMENT_ROOT']);

        $split = explode("?", urldecode($this->request_uri));

        if( $this->mvc_enabled == FALSE ){

            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $split[0]) && !is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $split[0]) ) {

                header('HTTP/1.1 404 Not Found');
                $this->log_output();
                die();

            }else{

                $new_dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $split[0];

                if( is_dir( $new_dir )){

                    $search_files = scandir($new_dir);

                    $index_array = array("index.php", "index.html", "index.htm");

                    $found_index = false;

                    foreach ( $index_array as $key=>$index ){

                        if( in_array($index, $search_files) && $found_index == false ){

                            $found_index = true;
                            $this->indexPath = $split[0] . "/" . $index;

                        }

                        if( $key == count($index_array)-1 && !$found_index ){

                            $html = "";
                            foreach( $search_files as $files){
                                $html .= '<a href="'.rtrim($this->request_uri,'/').'/'.$files.'" >'.$files.'</a><br />';
                            }

                            echo $html;
                            die();

                        }

                    }

                }

            }

        }



        $load_index = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->indexPath;

        if (!file_exists($load_index)) {

            $not_found_message = "Your index file doesn't exist at " . $load_index;

            $this->console($not_found_message);
            exit($not_found_message);

        } else {

            include($_SERVER['DOCUMENT_ROOT'] . "/$this->indexPath");

        }

    }

    function listen()
    {

        if (preg_match('/\.(.*?)$/', $this->request_uri)) {


            $filename = $this->request_uri;

            if (($found = strstr($this->request_uri, "?", TRUE)) != FALSE) {

                $filename = $found;

            }

            if (strrev(strstr(strrev($filename), '.', TRUE)) == 'php') {

                $this->indexPath = $filename;

                $this->bootstrap();

            } else {

                $this->process_request();

            }


        } else {

            $this->bootstrap();

        }

        exit;

    }

    function console()
    {

        $args = func_get_args();

        if (count($args) > 0) {

            foreach ($args as $arg) {

                ob_start();
                print_r($arg);
                $output = ob_get_clean();
                error_log($output, 4);

            }

        }

    }

    function retrieve_mime_types()
    {

        $mimes_file = dirname(__FILE__) . '/mimes.json';

        if (!file_exists($mimes_file)) {

            $this->create_mime_file();

        }

        return json_decode(file_get_contents($mimes_file), true);

    }

    private function create_mime_file()
    {

        $s = array();
        foreach (@explode("\n", @file_get_contents("http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types")) as $x) {

            if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1) {
                for ($i = 1; $i < $c; $i++) {
                    $s[] = '&nbsp;&nbsp;&nbsp;\'' . $out[1][$i] . '\' => \'' . $out[1][0] . '\'';
                }
            }
        }

        $tmp_arr = array();

        foreach ($s as $k => $v) {

            $split = explode('=>', $v);
            $new_key = trim(preg_replace('/\s+/', '', str_replace(array("   '", "'", " ", "	", "   ", '&nbsp;'), "", $split[0])));
            $new_val = trim(str_replace(array("   '", "'"), "", $split[1]));

            $tmp_arr[$new_key] = $new_val;

        }
        ksort($tmp_arr);

        fwrite(fopen(dirname(__FILE__) . '/mimes.json', 'w+'), json_encode($tmp_arr));

    }


}


$_SERVER["ENVIRONMENT"] = "development";
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

$php_web_server->listen();
