<?php
include("conn.php");
header('Content-type:text/json');

if (@$do = $_REQUEST["do"]) {
    $do();
}
if (@$do = $_FILES["do"]) {
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
                WHERE `s_use`.`uid` = '$uid' AND s_use.`cid` = '$cid' AND s_use.`cid`=class.`cid` ";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Ok";
        $detail = mysqli_fetch_assoc($que);
        $content["use"] = $detail;

        $sql = "SELECT couid,cid,title FROM `course` WHERE `cid` = '$cid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_all($que, 1);
        $content["course"] = $detail;
    } else {
        $content["talk"] = "NOtOk";
    }

    echo json_encode($content);
}

    // 获得上传文件并处理
function GetFile()
{
    global $content;
    global $conn;

    $file = $_FILES["file"];
    $cid = $_REQUEST["cid"];
    $uid = $_REQUEST["uid"];
    $select = $_REQUEST["select"];

    $sql = "SELECT * FROM `class` WHERE `cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
    $className = $detail["name"];

    if ($file["error"] > 0) {
        $content["talk"] = "NotOk";
        $content["error"] = "错误".$file["error"];
    } else if (!is_uploaded_file($file['tmp_name'])) {
        $content["talk"] = "NotOk";
        $content["error"] = "不合理的上传";
    } else if ($file['size'] > 2 * 1024 * 1024) {
        $content["talk"] = "NotOk";
        $content["error"] = "文件过大，不能上传大于2M的文件";
    } else {
        $extend = pathinfo($file['name']);
        $extend = $extend['extension'];
        $dir = "../HomeworkSubmit/" . $className . "/" . $select . "/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $save = $dir . $uid . "_" . $file["name"];
        if (!file_exists($save)) {
            if (move_uploaded_file($file['tmp_name'], $save))
                $content["talk"] = "Ok";
            else {
                $content["error"] = "未知的错误";
                $content["talk"] = "NotOk";
            }
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "已有同名文件存在";
        }
    }

    clearstatcache();
    echo json_encode($content);
}
?>