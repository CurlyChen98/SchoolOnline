<?php
header('Content-type:text/json');
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
include("conn.php");
include("module.php");

if (@$do = $_REQUEST["do"]) {
    $do();
}
$content = [];
            
// 用户登录功能
function Login()
{
    global $conn;
    global $content;

    $uname = $_REQUEST["uname"];
    $upwd = $_REQUEST["upwd"];

    $sql = "SELECT * FROM `s_use` WHERE `name`='$uname' AND `password`='$upwd' AND `level`=5";
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
    $uid = $_REQUEST["uid"];//正在使用者的id
    @$stuUid = $_REQUEST["stuUid"];//搜索id
    @$cid = $_REQUEST["claName"];//搜搜班级id
    @$stuLevel = $_REQUEST["stuLevel"];//搜索等级
    @$stuName = $_REQUEST["stuName"];//搜索名字
    $pageCount = $_REQUEST["pageCount"];//每页行数
    $pno = $_REQUEST["pno"];//第几页
    // 处理页数
    $samllp = $pageCount * $pno - $pageCount;
    $bigp = $pageCount;
    $content["pno"] = $pno;


    $sqlAdd = "WHERE s_use.cid = class.cid AND s_use.level < 5 ";
    if ($stuUid != "") {
        $sqlAdd = $sqlAdd . "AND s_use.uid = '$stuUid'";
    }
    if ($cid != "") {
        $sqlAdd = $sqlAdd . "AND s_use.cid = '$cid'";
    }
    if ($stuLevel != "") {
        $sqlAdd = $sqlAdd . "AND s_use.level = '$stuLevel'";
    }
    if ($stuName != "") {
        $sqlAdd = $sqlAdd . "AND s_use.name = '$stuName'";
    }
    // 获取用户cid
    if ($cid == "") {
        $sql = "SELECT `cid` FROM `s_use` WHERE `uid` = '$uid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $cid = $detail["cid"];
        $sqlAdd = $sqlAdd . "AND s_use.cid = '$cid'";
    }

    $sql = "SELECT s_use.`uid` as uid ,class.`name` as clsName, s_use.`name` as stuName, s_use.`level` as stuLevel 
            FROM `s_use`,class " . $sqlAdd . "LIMIT $samllp , $bigp ";
    $que = mysqli_query($conn, $sql);
    if (mysqli_num_rows($que) > 0) {
        $content['studetList'] = mysqli_fetch_all($que, 1);
        // 返回老师所有名下班级
        $sql = "SELECT class.cid as cid,class.name AS claName FROM class,s_use WHERE s_use.uid='$uid' AND s_use.cid = class.cid";
        $que = mysqli_query($conn, $sql);
        $content['ClassList'] = mysqli_fetch_all($que, 1);
        // 返回总页数
        $sql = "SELECT s_use.`uid` as uid ,class.`name` as clsName, s_use.`name` as stuName, s_use.`level` as stuLevel FROM `s_use`,class " . $sqlAdd;
        $que = mysqli_query($conn, $sql);
        $content['howNum'] = mysqli_num_rows($que) / $pageCount;
        $content['talk'] = "Ok";
    } else {
        $content['talk'] = "NotOk";
        $content['error'] = "没有关联学生";

    }

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
        // $sql = "UPDATE `s_use` SET `cid`='0' WHERE `uid` ='$uid'";
        $content["talk"] = DeleteStu($uid);
        echo json_encode($content);
        return;
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

// 创建课程
function CreateCourse()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $cid = $_REQUEST["cid"];// 班级id
    $title = $_REQUEST["title"];// 课程标题
    $date = $_REQUEST["date"];// 课程标题
    $cont = $_REQUEST["content"];// 标题内容
    $video_src = $_REQUEST["video_src"]; // 视频链接
    $video_kind = $_REQUEST["video_kind"];// 视频链接类型

    $sql = "INSERT INTO `course`(`cid`, `title`, `date`, `content`, `video_src`, `video_kind`, `task_src`) 
            VALUES ('$cid','$title','$date','$cont','$video_src','$video_kind','0')";
    if (mysqli_query($conn, $sql)) {

        $sql = "SELECT * FROM `class` WHERE `cid` = '$cid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $className = $detail["name"];

        $dir = "../HomeworkSubmit/" . $className . "/" . $title . "/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $dir = "../HomeworkDownload/" . $className . "/" . $title . "/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $content["talk"] = "Ok";
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "未知的错误";
    }

    echo json_encode($content);
};

// 为课程添加作业文件
function InWork()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $file = $_FILES["file"];// 上传文件
    $couid = $_REQUEST["couid"];// 课程id
    $cid = $_REQUEST["cid"];// 班级id

    if ($file["error"] > 0) {
        $content["talk"] = "NotOk";
        $content["error"] = "错误" . $file["error"];
    } else if (!is_uploaded_file($file['tmp_name'])) {
        $content["talk"] = "NotOk";
        $content["error"] = "不合理的上传";
    } else if ($file['size'] > 2 * 1024 * 1024) {
        $content["talk"] = "NotOk";
        $content["error"] = "文件过大，不能上传大于2M的文件";
    } else {
        $sql = "SELECT name FROM `class` WHERE `cid` = '$cid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $className = $detail["name"];

        $sql = "SELECT title FROM `course` WHERE `couid` = '$couid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $couTitle = $detail["title"];

        $dir = "../HomeworkDownload/" . $className . "/" . $couTitle . "/";
        $save = $dir . $file["name"];
        $saveDir = $className . "/" . $couTitle . "/" . $file["name"];
        $saveDir = urlencode($saveDir);
        if (move_uploaded_file($file['tmp_name'], $save)) {
            $sql = "UPDATE `course` 
                    SET `task_src` = '$saveDir'
                    WHERE `course`.`couid` = '$couid'";
            if (mysqli_query($conn, $sql)) {
                $content["talk"] = "Ok";
            } else {
                $content["talk"] = "NotOk";
                $content["error"] = "未知的错误2";
            }
        } else {
            $content["error"] = "未知的错误";
            $content["talk"] = "NotOk";
        }

    }

    clearstatcache();
    echo json_encode($content);
}

// 删除作业文件
function DeleteWork()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $couid = $_REQUEST["couid"];// 课程id

    $sql = "SELECT `couid`, `cid`, `title`, `date`, `task_src` 
            FROM `course` 
            WHERE `couid` = '$couid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);
    $dirFile = "./../HomeworkDownload/" . $detail["task_src"];
    $dirFile = urldecode($dirFile);

    if (unlink($dirFile)) {
        $sql = "UPDATE `course` SET `task_src`='0' WHERE `couid`='$couid'";
        mysqli_query($conn, $sql);
        $content["talk"] = "Ok";
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "未知的错误";
    }

    echo json_encode($content);
}

// 删除课程
function DeleteCou()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $couid = $_REQUEST["couid"];// 课程id

    if (DeleteCourse($couid, "cou") == "Ok") {
        $content["talk"] = "Ok";
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "未知的错误";
    }

    echo json_encode($content);
}

// 展示课程
function FindCou()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $cid = $_REQUEST["cid"];// 班级id
    @$couid = $_REQUEST["couid"];// 课程id（首次加载可以没有）

    $sql = "SELECT `couid`,`title`, `date`, video_src, video_kind, task_src
            FROM `course` 
            WHERE `cid` = '$cid' 
            ORDER BY `date` DESC";
    if ($que = mysqli_query($conn, $sql)) {
        $num = mysqli_num_rows($que);
        if ($num > 0) {
            $detail = mysqli_fetch_all($que, 1);
            $content["course"] = $detail;

            $content["oneCourse"] = $detail[0];
            if ($couid != "") {
                $sql = "SELECT `couid`,`title`, `date`, video_src, video_kind, task_src
                        FROM `course` 
                        WHERE `cid` = '$cid' AND couid = '$couid'";
                $que = mysqli_query($conn, $sql);
                $content["oneCourse"] = mysqli_fetch_assoc($que);
            }

            $sql = "SELECT name FROM `class` WHERE `cid` = '$cid'";
            $que = mysqli_query($conn, $sql);
            $content["class"] = mysqli_fetch_assoc($que);

            $content["talk"] = "Ok";
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "没有课程";
        }
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "未知的错误";
    }

    echo json_encode($content);
}

// 展示班级所有学生作业
function ShowWork()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $cid = $_REQUEST["cid"];// 班级id
    @$couid = $_REQUEST["couid"];// 课程id（首次加载可以没有）
    $pageCount = $_REQUEST["pageCount"];
    $pno = $_REQUEST["pno"];

    // 处理页数
    $samllp = $pageCount * $pno - $pageCount;
    $bigp = $pageCount;
    $content["pno"] = $pno;

    $sql = "SELECT name FROM `class` WHERE `cid` = '$cid'";
    $que = mysqli_query($conn, $sql);
    $content["class"] = mysqli_fetch_assoc($que);

    $sql = "SELECT `couid`, `title`, `date`
            FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
    $que = mysqli_query($conn, $sql);
    if (mysqli_num_rows($que) > 0) {
        $detail = mysqli_fetch_all($que, 1);
        $content["course"] = $detail;

        $oneCouId = $detail[0]["couid"];
        if ($couid != "") {
            $oneCouId = $couid;
        }

        $sql = "SELECT `title` FROM `course` WHERE `couid` = '$oneCouId'";
        $que = mysqli_query($conn, $sql);
        $content["claName"] = mysqli_fetch_assoc($que);

        if (mysqli_num_rows($que) > 0) {
            $sql = "SELECT k.`task_id`,k.`task_name`,k.`date`,k.task_url,e.name
            FROM
                (
                    SELECT `task_id`,`task_name`,`uid`,`date`,task_url
                    FROM `task`
                    WHERE `couid` = '$oneCouId'
                ) K
            LEFT JOIN s_use e ON e.uid = k.uid
            ORDER BY k.`date` DESC
            LIMIT $samllp,$bigp";
            $que = mysqli_query($conn, $sql);
            $detail = mysqli_fetch_all($que, 1);
            $content["work"] = $detail;
            $content["url"] = "/SchoolOnline/HomeworkSubmit/";
            $content["talk"] = "Ok";
        } else {
            $content["talk"] = "NotOk";
            $content["error"] = "无作业";
        }
    } else {
        $content["talk"] = "NotOk";
        $content["error"] = "无课程";
    }

    echo json_encode($content);
}

// 下载学生作业
function DwWork()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    @$task_id = json_decode($_REQUEST["task_id"], true);// 作业id
    @$couid = $_REQUEST["couid"];// 课程id
    $how = $_REQUEST["how"];// 选择的数量(one、all)

    $fileList = [];
    $inArr = '';
    if ($how == "one") {
        $inArr = "task_id = '$task_id'";
    } else if ($how == "all") {
        $inArr = "couid = '$couid'";
    }
    $sql = "SELECT `task_url`, `task_name` 
            FROM `task` WHERE $inArr";
    $que = mysqli_query($conn, $sql);
    $fileList = mysqli_fetch_all($que, 1);

    $task_url = rawurldecode($fileList[0]["task_url"]);
    $task_name = rawurldecode($fileList[0]["task_name"]);
    $baseName = strrpos($task_url, $task_name);
    $dirZip = substr($task_url, 0, $baseName - 1);

    $zipname = $dirZip . "/work.zip";
    $zip = new ZipArchive();
    $res = $zip->open("../HomeworkSubmit/" . $zipname, ZipArchive::OVERWRITE | ZipArchive::CREATE);   //打开压缩包
    foreach ($fileList as $file) {
        $task_url = rawurldecode($file["task_url"]);
        $task_name = rawurldecode($file["task_name"]);

        $zip->addFile("../HomeworkSubmit/" . $task_url, $task_name);   //向压缩包中添加文件
    }
    $zip->close();  //关闭压缩包

    $content["zipname"] = $zipname;
    echo json_encode($content);
}

// 修改教师密码
function UpdatePassword()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $uid = $_REQUEST["uid"];// 接受用户id
    $oldPassword = $_REQUEST["oldPassword"];// 接受旧密码
    $newPassword = $_REQUEST["newPassword"];// 接受旧密码

    $sql = "SELECT uid FROM `s_use` WHERE `password` = '$oldPassword' AND `uid` = '$uid'";
    $que = mysqli_query($conn, $sql);
    if (mysqli_num_rows($que) > 0) {
        $sql = "UPDATE `s_use` SET `password` = '$newPassword' WHERE `uid` = '$uid'";
        if (mysqli_query($conn, $sql)) {
            $content["talk"] = "Ok";
        } else {
            $content["talk"] = "NotOk2";
        }
    } else {
        $content["talk"] = "NotOk1";
    }
    echo json_encode($content);

}

?>
