<?php
include("conn.php");
include("module.php");
header('Content-type:text/json');
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

if (@$do = $_REQUEST["do"]) {
    $do();
}
if (@$do = $_FILES["do"]) {
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
        // 需要改
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
        // 需要改
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

    $sql = "SELECT `couid`, `cid`, `title`, `date`, `task_src` 
            FROM `course` 
            WHERE `couid` = '$couid'";
    $que = mysqli_query($conn, $sql);
    $detail = mysqli_fetch_assoc($que);

    $dirFile = "../HomeworkDownload/" . $detail["task_src"];
    $dirFile = urldecode($dirFile);
    $dir = substr($dirFile, 0, strrpos($dirFile, '/')) . "/";
    $p = scandir($dir);
    foreach ($p as $val) {
        if ($val != "." && $val != "..") {
            unlink($dir . $val);
        }
    }
    rmdir($dirFile);

    $dirFile = "../HomeworkSubmit/" . $detail["task_src"];
    $dirFile = urldecode($dirFile);
    $dir = substr($dirFile, 0, strrpos($dirFile, '/')) . "/";
    $p = scandir($dir);
    foreach ($p as $val) {
        if ($val != "." && $val != "..") {
            unlink($dir . $val);
        }
    }
    rmdir($dirFile);

    if (mysqli_query($conn, $sql)) {
        $sql = "DELETE FROM `course` WHERE `couid`= '$couid'";
        mysqli_query($conn, $sql);
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
    $detail = mysqli_fetch_all($que, 1);
    $content["course"] = $detail;

    $oneCouId = $detail[0]["couid"];
    if ($couid != "") {
        $oneCouId = $couid;
    }

    $sql = "SELECT `title` FROM `course` WHERE `couid` = '$oneCouId'";
    $que = mysqli_query($conn, $sql);
    $content["claName"] = mysqli_fetch_assoc($que);

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


    echo json_encode($content);
}

// 下载学生作业
function DwWork()
{
    global $conn;// 链接mysql
    global $content;// 用于输出

    $task_id = json_decode($_REQUEST["task_id"], true);// 作业id
    $how = $_REQUEST["how"];// 选择的数量(one、array、all)

    $fileList = [];
    $inArr = '';
    if ($how == "one") {
        $inArr = "`task_id` = $task_id";
    } else if ($how == "array") {
        $arr = [];
        foreach ($task_id as $key => $value) {
            array_push($arr, $value);
        }
        $inArr = "`task_id` IN ($arr)";
    } else if ($how == "all") {
        $inArr = "1";
    }
    $sql = "SELECT `task_url`, `task_name` 
            FROM `task` WHERE $inArr";
    $que = mysqli_query($conn, $sql);
    $fileList = mysqli_fetch_all($que, 1);

    $zipname = "work.zip";
    $zip = new ZipArchive();
    $res = $zip->open($zipname, ZipArchive::OVERWRITE | ZipArchive::CREATE);   //打开压缩包
    foreach ($fileList as $file) {
        $file["task_url"] = rawurldecode($file["task_url"]);
        $file["task_name"] = rawurldecode($file["task_name"]);
        var_dump("../HomeworkSubmit/" . $file["task_url"]);
        var_dump($file["task_name"]);
        $a = file_exists("../HomeworkSubmit/" . $file["task_url"]);
        var_dump($a);

        $zip->addFile("../HomeworkSubmit/" . $file["task_url"], $file["task_name"]);   //向压缩包中添加文件
    }
    $zip->close();  //关闭压缩包

    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length: " . filesize($zipname));
    header("Content-Disposition: attachment; filename=\"" . basename($zipname) . "\"");
    ob_clean();
    flush();

    $content["url"] = "http://localhost/SchoolOnline/Php/work.zip";

    echo json_encode($content);
}

?>
