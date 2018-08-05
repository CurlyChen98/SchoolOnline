<?php
include("conn.php");
header('Content-type:text/json');

$do = $_REQUEST["do"];
$do();
$content = [];
    
// 查找用户
function CreateUse()
{
    global $conn;
    global $content;
    // 获得客户端发送的用户code
    $code = $_REQUEST["code"];
    // 获取真实openid
    $mdOpenId = FindOpenId($code);
    // 判断是否第一次登陆
    $sql = "SELECT * FROM `s_use` WHERE `opid` = '$mdOpenId'";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Ok";
        $detail = mysqli_fetch_all($que, 1);
        $content["use"] = $detail;
        LoadCourse($detail[0]["cid"]);
    } else {
        $content["talk"] = "NotOk";
    }
    // 输出
    echo json_encode($content);
}

// 注册用户
function InsertUse()
{
    global $conn;
    global $content;
    // 得到用户id、用户输入的班级密匙、用户输入的真实姓名
    $code = $_REQUEST["code"];
    $classkey = $_REQUEST["classkey"];
    $studentkey = $_REQUEST["studentkey"];
    // 获取真实openid
    $mdOpenId = FindOpenId($code);
    // 判断班级密匙是否正确
    $sql = "SELECT * FROM `class` WHERE `classkey` = '$classkey'";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Ok";
        $detail = mysqli_fetch_assoc($que);
        $cid = $detail["cid"];
        $sql = "INSERT INTO `s_use`(`uid`, `gid`, `cid`, `opid`, `name`, `level`, `lastdate`) 
                VALUES (NULL,'0','$cid','$mdOpenId','$studentkey','1',now())";
        if(mysqli_query($conn, $sql)){
            $content["talk"] = "Ok";
        }else{
            $content["talk"] = "NotOk";
        }
    } else {
        $content["talk"] = "NotOk";
    }
    echo json_encode($content);
}

// 获取真实openid
function FindOpenId($code)
{
    $appid = "wxb4e383cbca6113d9";
    $secret = "78d0b3ea6ef3c4234675278e1ae8a7bb";
    $reUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
    $get = file_get_contents($reUrl);
    $canGet = json_decode($get);
    $useOpenId = $canGet->openid;
    $mdOpenId = md5($useOpenId);
    return $mdOpenId;
}

// 加载班级课程以及班级信息
function LoadCourse($cid)
{
    global $conn;
    global $content;

    $sql = "SELECT couid,title,date FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    $sql = "SELECT * FROM `class` WHERE `cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["class"] = $detail;
}

    // 课程详细展示
function ClassRoom()
{
    global $conn;
    global $content;

    $couid = $_REQUEST["couid"];

    $sql = "SELECT * FROM `course` WHERE `couid` = '$couid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    echo json_encode($content);
}

    // 课程列表展示
function FindCourse()
{
    global $conn;
    global $content;

    $cid = $_REQUEST["cid"];

    $sql = "SELECT couid,title,date FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    echo json_encode($content);
}

    // 创建话题
function CreateTalk()
{
    global $conn;
    global $content;

    $data = json_decode($_REQUEST["data"]);
    $course = json_decode($_REQUEST["course"]);
    $uid = $_REQUEST["uid"];

    $title = $data->title;
    $detail = $data->detail;
    $couid = $course->couid;
    $uid;
    $sql = "INSERT INTO `talk` VALUES (NULL,'$uid','$couid','$title','$detail',now())";
    if (mysqli_query($conn, $sql)) {
        $content["talk"] = "Ok";
    } else {
        $content["talk"] = "NotOk";
    }
    echo json_encode($content);
}

    // 话题列表展示
function FindTalk()
{
    global $conn;
    global $content;

    $cid = $_REQUEST["cid"];
    $order = $_REQUEST["order"];
    $day = $_REQUEST["day"];


    if ($day != "") {
        $day = "AND `date` = '$day'";
    }
    $sql = "SELECT `couid`,`date`,`title` FROM `course` WHERE `cid` = '$cid' ORDER BY `date` $order";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    foreach ($detail as $key => $value) {
        $couid = $value["couid"];
        $title = $value["title"];
        $sql = "SELECT * FROM `talk` WHERE `couid` = '$couid' $day ORDER BY `date` $order";
        $que = mysqli_query($conn, $sql);
        $detail2 = mysqli_fetch_all($que, 1);
        $content["topic"][$key] = [$title, $detail2];
    }

    echo json_encode($content);
}

    // 展示某一话题详细和跟帖
function FindTalkDe()
{
    global $conn;
    global $content;

    $taid = $_REQUEST["taid"];

    $sql = "SELECT talk.title,talk.detail,talk.date, s_use.name 
                FROM talk, s_use WHERE talk.taid = '$taid' AND talk.uid = s_use.uid 
                ORDER BY talk.date DESC";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["topic"] = $detail;

    $sql = "SELECT talkdet.detail,talkdet.date,s_use.name 
                FROM talkdet, s_use WHERE talkdet.taid = '$taid' AND talkdet.uid = s_use.uid 
                ORDER BY talkdet.date ASC";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["topicdet"] = $detail;

    echo json_encode($content);
}

    // 上传跟帖
function FollowTalk()
{
    global $conn;
    global $content;

    $taid = $_REQUEST["taid"];
    $uid = $_REQUEST["uid"];
    $detail = $_REQUEST["detail"];

    $sql = "INSERT INTO `talkdet` VALUES ('$taid','$uid','$detail',now())";
    if (mysqli_query($conn, $sql)) {
        $content["talk"] = "Ok";
    } else {
        $content["talk"] = "NotOk";
    }

    echo json_encode($content);
}
?>