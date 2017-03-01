<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>PHP-Built-in-web-server-Router</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
    <link href="src/css/style.css" rel="stylesheet">
</head>
<?php
$bing = json_decode(file_get_contents("http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=en-US"), true);
$image = '//bing.com/' . $bing["images"][0]["url"];
?>
<body style="background:url('<?php echo $image ?>')">
<div class="main">

    <pre class="pre-block">
    <?php
    print_r($_SERVER);
    console_output('This message will appear only in console ' . date('Y-m-d H:i:s'), array("this" => array("vector" => "too")));
    ?>
    </pre>

    <div class="images-block">
        <h4 style="margin:0;">Check the following images in Developer Tools > Networking to see http status.</h4>
        <hr/>
        <img class="img" src="<?php echo 'http://' . $_SERVER['HTTP_HOST'] ?>/src/images/1.jpg"/>
        <img class="img" src="<?php echo 'http://' . $_SERVER['HTTP_HOST'] ?>/src/images/2.jpg"/>
        <div class="last-image">Loaded from style.css</div>
    </div>


</div>
</body>
</html>