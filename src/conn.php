<?php

$dbhost = '129.114.25.202';
$dbuname = 'admin';
$dbpass = 'admin';
$dbname = 'big_five_db';

try {
        $dbo = new PDO('mysql:host=' . $dbhost . ';port=3306;dbname=' . $dbname, $dbuname, $dbpass);
} catch (Exception $e) {
        echo "Exception: ";
        echo $e;
}

?>