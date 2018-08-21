<?php
include("conn.php");

// 删除目录下所有文件
function DeleteDirAll($dir)
{
    try {
        $p = scandir($dir);
        foreach ($p as $val) {
            if ($val != "." && $val != "..") {
                chmod($dir . $val, 0755);
                unlink($dir . $val);
            }
        }
        rmdir($dirFile);
        return $talk = "Ok";
    } catch (Exception $error) {
        return $talk = "NotOk";
    }
}

// 删除课程
function DeleteCou($couid)
{
    global $conn;// 链接mysql

    try {

        $sql = "SELECT `task_src` FROM `course` WHERE `couid` = '$couid'";
        $que = mysqli_query($conn, $sql);
        $detail = mysqli_fetch_assoc($que);

        $dirFile = "../HomeworkDownload/" . $detail["task_src"];
        $dirFile = urldecode($dirFile);
        $dir = substr($dirFile, 0, strrpos($dirFile, '/')) . "/";
        echo $dir;
        $talk = DeleteDirAll($dir);

        $dirFile = "../HomeworkSubmit/" . $detail["task_src"];
        $dirFile = urldecode($dirFile);
        $dir = substr($dirFile, 0, strrpos($dirFile, '/')) . "/";
        $talk = DeleteDirAll($dir);

        $sql = "DELETE FROM `course` WHERE `couid`= '$couid'";
        mysqli_query($conn, $sql);
        return $talk = "Ok";
    } catch (Exception $error) {
        return $talk = "NotOk";
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
        $sql = "DELETE FROM `talkdet` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        $sql = "DELETE FROM `talk` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);

        // 删除学生
        $sql = "DELETE FROM `s_use` WHERE `uid` = '$uid'";
        mysqli_query($conn, $sql);
        return $talk = "Ok";
    } catch (Exception $error) {
        return $talk = "NotOk";
    }
}

?>