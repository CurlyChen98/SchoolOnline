<?php
    include("conn.php");
    header('Content-type:text/json'); 

    $cid = $_REQUEST["cid"];
    $couid = $_REQUEST["couid"];
    $do = $_REQUEST["do"];
    $content = [
        "talk"=>"",
        "use"=>"",
        "class"=>"",
        "course"=>"",
    ];
    $do();

    function Dw(){
        global$content;
        global$conn;
        global$couid;
        global$cid;

        $sql = "SELECT * FROM `course` WHERE `couid` = '$couid' AND `cid` = '$cid'";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        $content["course"] = $detail;

        echo json_encode($content);
    }

?>