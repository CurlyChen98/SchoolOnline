<?php
include("conn.php");

// 删除目录下所有文件
function DeleteDirAll($path)
{
    try {
        $p = scandir($path);
        foreach ($p as $val) {
            if ($val != "." && $val != "..") {
                if (is_dir($path . $val)) {
                    DeleteDirAll($path . $val . '/');
                    rmdir($path . $val . '/');
                } else {
                    unlink($path . $val);
                }
            }
        }
        rmdir($path);
        return "Ok";
    } catch (Exception $error) {
        return "NotOk";
    }


}

// 删除课程
function DeleteCourse($couid, $class)
{
    global $conn;// 链接mysql

    try {

        $sql = "SELECT `task_src`,title FROM `course` WHERE `couid` = '$couid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $couName = $detail["title"];
        $dirFile = urldecode($detail["task_src"]);

        $className = substr($dirFile, 0, strpos($dirFile, '/'));
        $couDir1 = "../HomeworkDownload/$className/$couName/";
        $couDir2 = "../HomeworkSubmit/$className/$couName/";

        if ($detail["task_src"] != "0") {
            $talk1 = DeleteDirAll($couDir1);
            if ($talk1 == "NotOk") {
                return "NotOk";
            }
            $talk2 = DeleteDirAll($couDir2);
            if ($talk2 == "NotOk") {
                return "NotOk";
            }
        }

        $sql = "DELETE FROM `course` WHERE `couid`= '$couid'";
        mysqli_query($conn, $sql);
        return "Ok";
    } catch (Exception $error) {
        return "NotOk";
    }
}

// 删除学生
function DeleteStu($uid)
{
    global $conn;// 链接mysql

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
        $sql = "SELECT `taid` FROM `talk` WHERE uid = '$uid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);
        $taid = $detail['taid'];
        $sql = "DELETE FROM `talkdet` WHERE `taid` = '$taid'";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM `talk` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);

        // 删除学生
        $sql = "DELETE FROM `s_use` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        return "Ok";
    } catch (Exception $error) {
        return "NotOk";
    }
}

?>