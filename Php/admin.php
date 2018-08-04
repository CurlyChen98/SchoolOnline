<?php
include("conn.php");
header('Content-type:text/json');
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

$do = $_REQUEST["do"];
$do();
$content = [];
            
// 用户登录功能
function Login()
{
    global $conn;
    global $content;

    $uname = $_REQUEST["uname"];
    $upwd = $_REQUEST["upwd"];

    $sql = "SELECT * FROM `s_use` WHERE `name`='$uname' AND `password`='$upwd' AND `level`>=5";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Ok";
        $detail = mysqli_fetch_all($que, 1);
        $content["use"] = $detail;
    } else {
        $content["talk"] = "NotOk";
    }
    echo json_encode($content);
}

// 展示所有有关联的学生
function ShowStudent()
{
    global $conn;
    global $content;
    // 获取前端数据
    $uid = $_REQUEST["uid"];
    $cid = $_REQUEST["cid"];
    $level = $_REQUEST["level"];
    @$showGroup = $_REQUEST["showGroup"];
    @$updateGroup = $_REQUEST["updateGroup"];
    @$stuGroup = $_REQUEST["stuGroup"];
    @$stuUid = $_REQUEST["stuUid"];
    @$claName = $_REQUEST["claName"];
    @$stuLevel = $_REQUEST["stuLevel"];
    @$stuName = $_REQUEST["stuName"];
    @$pageCount = $_REQUEST["pageCount"];
    @$pno = $_REQUEST["pno"];
    // 处理页数
    $samllp = $pageCount * $pno - $pageCount;
    $bigp = $pageCount;
    $content["pno"] = $pno;
    // 输出班级
    if ($level <= 5) {
        $sql = "SELECT * FROM `class` WHERE `cid` = $cid";
    } else {
        $sql = "SELECT * FROM `class`";
    }
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["class"] = $detail;
    // 默认第一条班级名单用户展示
    $cid = $detail[0]["cid"];
    $content["classOne"] = $detail[0]["name"];
    // 判断有无附加搜索条件
    $addsql = "";
    if ($stuUid != "") {
        $addsql = $addsql . " AND s_use.`uid` = '$stuUid'";
    }
    if ($stuLevel != "") {
        $addsql = $addsql . " AND s_use.`level` = '$stuLevel'";
    }
    if ($claName != "") {
        // 班级名单再次修改班级
        $claName = strtolower($claName);
        $sql = "SELECT * FROM `class` WHERE `name` = '$claName'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $cid = $detail["cid"];
        $content["classOne"] = $detail["name"];
    }
    if ($stuName != "") {
        $addsql = $addsql . " AND s_use.`name` = '$stuName'";
    }
    // 是否小组展示
    $addsqlgroup = "";
    if ($showGroup == "Y") {
        $sql = "SELECT * FROM `s_group` WHERE `cid` = $cid ORDER BY `s_group`.`gid` ASC";
        $que = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($que);
        if ($num == 0) {
            $content["talk"] = "NotGroup";
            echo json_encode($content);
            return;
        }
        $detail = mysqli_fetch_all($que, 1);
        $content["group"] = $detail;
        $gid = $detail[0]["gid"];
        $addsqlgroup = " AND s_use.`gid` = '$gid'";
        $content["groupOne"] = $detail[0]["name"];
    }
    if ($stuGroup != "") {
        // 小组名单再次修改班级
        $stuGroup = strtolower($stuGroup);
        $sql = "SELECT * FROM `s_group` WHERE `name` = '$stuGroup'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $gid = $detail["gid"];
        $addsqlgroup = " AND s_use.`gid` = '$gid'";
        $content["groupOne"] = $detail["name"];
    }
    $addsql = $addsql . $addsqlgroup;
    if ($updateGroup == "Y") {
        echo json_encode($content);
        return;
    }
    // 展示学生
    $sql = "SELECT s_use.name AS 'stuName', s_use.uid AS 'stuUid', s_use.level AS 'stuLevel', s_group.name AS 'groName' 
            FROM s_use, s_group 
            WHERE s_use.cid = '$cid' AND s_use.gid = s_group.gid AND s_use.level < 5 $addsql
            ORDER BY s_use.uid ASC LIMIT $samllp,$bigp";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        // 若有数据则展示
        $content["talk"] = "Have";
        $content["student"] = $detail;
        // 展示总数量
        $sql = "SELECT s_use.name AS 'stuName', s_use.uid AS 'stuUid', s_use.level AS 'stuLevel', s_group.name AS 'groName' 
                FROM s_use, s_group 
                WHERE s_use.cid = '$cid' AND s_use.gid = s_group.gid AND s_use.level < 5 $addsql";
        $que = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($que) / $pageCount;
        $content["howNum"] = $num;
    } else {
        // 无数据则报错
        $content["talk"] = "NotHave";
    }
    // 输出
    echo json_encode($content);
}

// 修改学生
function UpdateStudent()
{
    global $conn;
    global $content;
    // 获取前端数据
    @$uid = $_REQUEST["uid"];
    @$gid = $_REQUEST["gid"];
    $how = $_REQUEST["how"];
    // 判断动作并加以修饰
    $sql = "";
    if ($how == "upgrade") {
        $sql = "UPDATE `s_use` SET `level`='3' WHERE `uid` ='$uid';";
    } else if ($how == "downgrade") {
        $sql = "UPDATE `s_use` SET `level`='1' WHERE `uid` ='$uid'";
    } else if ($how == "kickOut") {
        $sql = "UPDATE `s_use` SET `cid`='0' WHERE `uid` ='$uid'";
    } else if ($how == "kickOutGroup") {
        $sql = "UPDATE `s_use` SET `gid`='0' WHERE `uid` ='$uid'";
    } else if ($how == "deleteGroup") {
        $sql = "DELETE FROM `s_group` WHERE `gid` ='$gid'";
    }
    // 执行动作
    $que = mysqli_query($conn, $sql);
    $res = mysqli_affected_rows($conn);
    if ($res > 0) {
        // 判断第二次动作并加以修饰
        if ($how == "kickOutGroup") {
            $sql = "SELECT * FROM `s_use` WHERE `gid` = $gid";
            $que = mysqli_query($conn, $sql);
            $num = mysqli_num_rows($que);
            if ($num == 0) {
                $sql = "DELETE FROM `s_group` WHERE `gid` = $gid";
                $que = mysqli_query($conn, $sql);
                $res = mysqli_affected_rows($conn);
            }
        } else if ($how == "deleteGroup") {
            $sql = "UPDATE `s_use` SET `gid` = '0' WHERE `gid` = $gid;";
            $que = mysqli_query($conn, $sql);
            $res = mysqli_affected_rows($conn);
        }
        // 执行第二次动作
        if ($res > 0) {
            $content["talk"] = "Ok";
        } else {
            $content["talk"] = "NotOk";
        }
    } else {
        $content["talk"] = "NotOk";
    }
    echo json_encode($content);
}

// 创建班级
function CreateClass()
{
    global $conn;
    global $content;
    // 获取前端数据
    $claName = $_REQUEST["claName"];
    // 查找班级名是否一存在
    $claName = strtolower($claName);
    $sql = "SELECT * FROM `class` WHERE `name` = '$claName'";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Have";
        return;
    } else {
        $arrcode1 = "PLOKMIJNUHBYGVTFCRDXESZWAQ123456789";
        $arrcode2 = "PLOKMIJNUHBYGVTFCRDXESZWAQzxcvbnmlkjhgfdsapoiuytrewq";
        $classcode = "";
        $teachercode = "";
        for (;; ) {
            $classcode = "KG" . substr(str_shuffle($arrcode1), 10, 10);
            $sql = "SELECT * FROM `class` WHERE `classkey` = '$classcode'";
            $que = mysqli_query($conn, $sql);
            $num = mysqli_num_rows($que);
            if ($num == 0) {
                break;
            }
        }
        for (;; ) {
            $teachercode = "KG" . substr(str_shuffle($arrcode2), 10, 10);
            $sql = "SELECT * FROM `class` WHERE `classkey` = '$teachercode'";
            $que = mysqli_query($conn, $sql);
            $num = mysqli_num_rows($que);
            if ($num == 0) {
                break;
            }
        }
        $sql = "INSERT INTO `class` VALUES (NULL,'$claName','$teachercode','$classcode')";
        if (mysqli_query($conn, $sql)) {
            $content["talk"] = "Ok";
        } else {
            $content["talk"] = "NotOk";
        }
        echo json_encode($content);
    }
}

// 查看小组密匙
function ShowGroupKey()
{
    global $conn;
    global $content;
    // 获取前端数据
    $gid = $_REQUEST["gid"];

    $sql = "SELECT * FROM `s_group` WHERE `gid` = $gid";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
    $content["group"] = $detail;

    echo json_encode($content);
}
?>
