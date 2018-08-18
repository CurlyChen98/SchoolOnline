<?php
include("conn.php");
header('Content-type:text/json');

$do = $_REQUEST["do"];
$do();
$content = [];
    
// 查找用户
function SelectUse()
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
        $detail = mysqli_fetch_assoc($que);
        $content["use"] = $detail;

        $cid = $detail["cid"];
        $sql = "SELECT couid,title,date FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_all($que, 1);
        $content["course"] = $detail;

        $sql = "SELECT * FROM `class` WHERE `cid` = '$cid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $content["class"] = $detail;
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
    // 判断班级密匙是否正确并获取cid
    $sql = "SELECT * FROM `class` WHERE `classkey` = '$classkey'";
    $que = mysqli_query($conn, $sql);
    $classNum = mysqli_num_rows($que);
    $detail = mysqli_fetch_assoc($que);
    $cid = $detail['cid'];
    $className = $detail['name'];
    // 判断是不是管理者组
    if ($className == "管理者组") {
        $level = 10;
        // 判断姓名是否在指定名单
        $nameNum = 1;
    } else {
        $level = 1;
        // 判断姓名是否在指定名单
        $sql = "SELECT `id` FROM `class_student_list` WHERE `student_name` = '$studentkey'";
        $que = mysqli_query($conn, $sql);
        $nameNum = mysqli_num_rows($que);
    }
    // 判断
    if ($classNum == 0) {
        $content["talk"] = "NotOk";
        $content["error"] = "班级密匙错误";
    } else if ($nameNum == 0) {
        $content["talk"] = "NotOk";
        $content["error"] = "查无此学生";
    } else {
        $sql = "SELECT * FROM `s_use` WHERE `opid` = '$mdOpenId'";
        $que = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($que);
        if ($num == 0) {
            $sql = "INSERT INTO `s_use`(`uid`, `gid`, `cid`, `opid`, `name`, `level`, `lastdate`,`password`) 
                    VALUES (NULL,'0','$cid','$mdOpenId','$studentkey','$level',now(),'0')";
            if (mysqli_query($conn, $sql)) {
                $content["talk"] = "Ok";
            } else {
                $content["talk"] = "NotOk";
                $content["error"] = "未知错误";
            }
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "该OpenId已存在";
        }
    }
    // 输出
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
function FindTopic()
{
    global $conn;
    global $content;

    $cid = $_REQUEST["cid"];
    $order = $_REQUEST["order"];
    $day = $_REQUEST["day"];

    if ($day != "") {
        $day = "AND DATE(k.date) = '$day'";
    }

    $sql = "SELECT eku.couTitle AS couTitle,eku.topData AS topData,eku.topTitle AS topTitle,eku.taid AS taid,eku.uName AS uName,COUNT(t.taid) AS countMsg
            FROM
                (
                    SELECT ek.couTitle AS couTitle,ek.topData AS topData,ek.topTitle AS topTitle,ek.taid AS taid,u.name AS uName
                    FROM
                        (
                            SELECT e.title AS couTitle,k.date AS topData,k.title AS topTitle,k.taid AS taid,k.uid AS uid
                            FROM
                                course e,talk k
                            WHERE e.couid = k.couid AND e.cid = '$cid' $day LIMIT 0,30
                        ) ek
                    LEFT JOIN s_use u ON u.uid = ek.uid
                    ) eku
            LEFT JOIN talkdet t ON t.taid = eku.taid
            GROUP BY t.taid
            ORDER BY `eku`.`topData` $order";
    // $content["sql"] = $sql;
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $content["talk"] = "Ok";
        $detail = mysqli_fetch_all($que, 1);
        $content["topic"] = $detail;
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "没有数据错误";
    }


    echo json_encode($content);
}

// 展示某一话题详细和跟帖
function FindTopicDe()
{
    global $conn;
    global $content;

    $taid = $_REQUEST["taid"];

    $sql = "SELECT u.name AS uName,k.date AS topData,k.title AS topTitle,k.detail AS topDetail
            FROM s_use u,talk k
            WHERE u.uid = k.uid AND k.taid = '$taid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
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
        $content["error"] = "未知的错误";
    }

    echo json_encode($content);
}

// 检查小组Key
function CheckGroup()
{
    global $conn;
    global $content;

    $groupkey = $_REQUEST["groupkey"];
    $uid = $_REQUEST["uid"];

    $sql = "SELECT * FROM `s_group` WHERE `groupkey` = '$groupkey'";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    if ($num > 0) {
        $detail = mysqli_fetch_assoc($que);
        $gid = $detail['gid'];
        $sql = "UPDATE `s_use` SET `gid` = '$gid' WHERE `uid` = '$uid'";
        if (mysqli_query($conn, $sql)) {
            $content["talk"] = "Ok";
            $content["gid"] = $gid;
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "未知的错误";
        }
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "小组密匙错误";
    }

    echo json_encode($content);
}

// 检查老师Key
function CheckTeacher()
{
    global $conn;
    global $content;

    $key = $_REQUEST["key"];
    $uid = $_REQUEST["uid"];
    $cid = $_REQUEST["cid"];

    $sql = "SELECT * FROM `class` WHERE `teacherkey` = '$key' AND cid = '$cid'";
    $que = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($que);
    $detail = mysqli_fetch_assoc($que);
    $className = $detail["name"];
    if ($className == "管理者组") {
        $level = 10;
    } else {
        $level = 5;
    }
    if ($num > 0) {
        $sql = "UPDATE `s_use` SET `level` = '$level' WHERE `uid` = '$uid'";
        if (mysqli_query($conn, $sql)) {
            $content["talk"] = "Ok";
            $content["level"] = $level;
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "未知的错误";
        }
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "密匙错误";
    }

    echo json_encode($content);
}

// 寻找我发布的帖子
function FindMyTopic()
{
    global $conn;
    global $content;

    $uid = $_REQUEST["uid"];

    $sql = "SELECT k.taid,k.title,k.date,k.msg_count,p.title AS courseTitle
            FROM
                (
                SELECT  u.taid AS taid,u.title AS title,u.date AS date,u.couid AS couid,COUNT(f.taid) AS msg_count
                FROM
                    (
                    SELECT taid,title,date,couid
                    FROM talk
                    WHERE uid = $uid
                    ) u
                LEFT JOIN talkdet f ON f.taid = u.taid
                GROUP BY f.taid
                ) k
            LEFT JOIN course p ON p.couid = k.couid;";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["topic"] = $detail;

    $sql = "SELECT `uid`, `gid`, `cid`, `name`, `level`, `lastdate` FROM `s_use` WHERE `uid` = '$uid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
    $content["use"] = $detail;

    echo json_encode($content);
}

// 寻找我跟帖的
function FindMyFollow()
{
    global $conn;
    global $content;

    $uid = $_REQUEST["uid"];

    $sql = "SELECT kte.taid AS taid,kte.det_detail AS det_detail,kte.det_detdate AS det_detdate,kte.top_title AS top_title,kte.top_date AS top_date,kte.course_title AS course_title,u.name AS top_name
            FROM
                (
                    SELECT kt.taid AS taid,kt.det_detail AS det_detail,kt.det_detdate AS det_detdate,kt.top_title AS top_title,kt.top_date AS top_date,kt.top_uid AS top_uid,e.title AS course_title
                    FROM
                        (
                            SELECT t.taid AS taid,t.detail AS det_detail,t.date AS det_detdate,k.title AS top_title,k.date AS top_date,k.couid AS top_couid,k.uid AS top_uid
                            FROM
                                (
                                    SELECT taid,detail,DATE
                                    FROM
                                        talkdet
                                    WHERE
                                        uid = $uid
                                ) t
                            LEFT JOIN talk k ON t.taid = k.taid
                        ) kt
                    LEFT JOIN course e ON e.couid = kt.top_couid
                ) kte
            LEFT JOIN s_use u ON u.uid = kte.top_uid;
            ";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_all($que, 1);
    $content["topic"] = $detail;

    echo json_encode($content);
}

// 寻找班级成员
function FindMyClass()
{
    global $conn;
    global $content;

    $cid = $_REQUEST["cid"];

    $sql = "SELECT `uid`, `name`, `level`, `lastdate` 
            FROM `s_use` WHERE `cid` = $cid";
    $que = mysqli_query($conn, $sql);
    $content["studentList"] = mysqli_fetch_all($que, 1);

    echo json_encode($content);
}

// 删除学生从班级中
function DeleteStudent()
{
    global $conn;
    global $content;

    $uid = $_REQUEST["uid"];

    try {
        // 删除作业和作业表相关数据
        $sql = "SELECT `task_url` FROM `task` WHERE `uid`= $uid";
        $que = mysqli_query($conn, $sql);
        $work = mysqli_fetch_all($que, 1);
        foreach ($work as $key => $value) {
            $url = urldecode("../HomeworkSubmit/" . $value["task_url"]);
            unlink($url);
        }
        $sql = "DELETE FROM `task` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
    
        // 删除有关讨论内容
        $sql = "DELETE FROM `talkdet` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM `talk` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM `s_use` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        $content["talk"] = "Ok";
    } catch (Exception $error) {
        $content["talk"] = "NotOk";
        $content["error"] = "未知的错误";
    }
    echo json_encode($content);
}
?>



