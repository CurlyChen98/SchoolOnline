<?php
    $a = $_REQUEST["a"];

    $b = urlencode($a);
    echo $b;
    $c = urldecode($b);
    echo $c;
?>