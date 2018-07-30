<?php
    include("conn.php");
    header('Content-type:text/json'); 

    $do = $_REQUEST["do"];
    $do();
    $content = [
        "talk"=>"",
        "use"=>"",
        "class"=>"",
    ];

    function Show(){
        $cid = $

        $sql = "SELECT * FROM `s_course` WHERE `s_cid` = '$cid'";
    }
?>