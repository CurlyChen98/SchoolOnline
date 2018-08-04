<?php
include("conn.php");
header('Content-type:text/json');

if ($do = $_REQUEST["do"]) {
    $do();
}
if ($do = $_FILES["do"]) {
    $do();
}
$content = [];

function Dw()
{
    global $content;
    global $conn;

    $cid = $_REQUEST["cid"];
    $couid = $_REQUEST["couid"];

    $sql = "SELECT * FROM `course` WHERE `couid` = '$couid' AND `cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    echo json_encode($content);
}

    // 上传页面寻找用户信息的方法
function Find()
{
    global $content;
    global $conn;

    $cid = $_REQUEST["cid"];
    $uid = $_REQUEST["uid"];
    $sql = "SELECT s_use.name AS stuName, class.name AS claName 
                FROM `s_use`, class 
                WHERE `s_use`.`uid` = '$uid' AND class.`cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
    $content["use"] = $detail;

    $sql = "SELECT couid,cid,title FROM `course` WHERE `cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    echo json_encode($content);
}

    // 获得上传文件并处理
function GetFile()
{
    global $content;
    global $conn;

    $file = $_FILES["file"];

    echo json_encode($file);
}
?>