<?php
    include("conn.php");
    header('Content-type:text/json'); 

    $do = $_REQUEST["do"];
    $do();
    $content = [
        "talk"=>"",
        "use"=>"",
        "class"=>"",
        "course"=>"",
        "topic"=>"",
    ];
    
    // 用户注册功能
    function CreateUse(){
        global $conn;
        global $content;
        // 获得客户端发送的用户code
        $code = $_REQUEST["code"];

        $appid = "wxb4e383cbca6113d9";
        $secret = "78d0b3ea6ef3c4234675278e1ae8a7bb";
        $reUrl = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
        $get = file_get_contents($reUrl);
        $canGet = json_decode($get);
        $useOpenId =  $canGet->openid;
        $mdOpenId = md5($useOpenId);

        // 判断是否第一次登陆
        $sql = "SELECT * FROM `s_use` WHERE `opid` = '$mdOpenId'";
        $que = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($que);
        if($num>0){
            $content["talk"] = "Have";
            $detail = mysqli_fetch_all($que,1);
            $content["use"] = $detail;      
            if($detail[0]["cid"]=="0"||$detail[0]["name"]=="0"){
                $content["talk"] = "NotHave";
            }else{
                $cid = $detail[0]["cid"];
                LoadCourse($cid);
            }   
            $content["uid"] = $detail[0]["uid"];    
        }else{
            $content["talk"]="NotHave";
            $sql = "INSERT INTO `s_use` VALUES (NULL,'0','0','$mdOpenId','0','1',now())";
            $que = mysqli_query($conn,$sql);
            $uid = mysqli_insert_id($conn);
            $content["uid"] = $uid;    
        }
        echo json_encode($content);
    }

    // 修改用户信息
    function UpdateNameClass(){
        global $conn;
        global $content;
        // 得到用户id、用户输入的班级密匙、用户输入的真实姓名
        $uid = $_REQUEST["uid"];
        $classkey = $_REQUEST["classkey"];
        $studentkey = $_REQUEST["studentkey"];

        // 判断班级密匙是否正确
        $sql = "SELECT * FROM `class` WHERE `classkey` = '$classkey'";
        $que = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($que);
        if($num>0){
            $content["talk"] = "Right";
            $detail = mysqli_fetch_all($que,1);
            $cid = $detail[0]["cid"];
            $sql = "UPDATE `s_use` SET `cid` = '$cid',`name` = '$studentkey' WHERE `uid` = '$uid'";
            $que = mysqli_query($conn,$sql);
            $sql = "SELECT * FROM `s_use` WHERE `uid` = '$uid'";
            $que = mysqli_query($conn,$sql);
            $detail = mysqli_fetch_all($que,1);
            $content["use"] = $detail;      
            LoadCourse($cid);
        }else{        
            $content["talk"] = "NotRight";
        }
        echo json_encode($content);
    }

    // 加载班级课程以及班级信息
    function LoadCourse($cid){
        global $conn;
        global $content;

        $sql = "SELECT couid,title,date FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
        $que = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($que);
        $detail = mysqli_fetch_all($que,1);
        $content["course"] = $detail;

        $sql = "SELECT * FROM `class` WHERE `cid` = '$cid'";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        $content["class"] = $detail;
    }

    // 课程详细展示
    function ClassRoom(){
        global $conn;
        global $content;

        $couid = $_REQUEST["couid"];

        $sql = "SELECT * FROM `course` WHERE `couid` = '$couid'";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        $content["course"] = $detail;

        echo json_encode($content);
    }

    // 课程列表展示
    function FindCourse(){
        global $conn;
        global $content;

        $cid = $_REQUEST["cid"];
        
        $sql = "SELECT couid,title,date FROM `course` WHERE `cid` = '$cid' ORDER BY `date` DESC";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        $content["course"] = $detail;

        echo json_encode($content);
    }

    // 创建话题
    function CreateTalk(){
        global $conn;
        global $content;

        $data = json_decode($_REQUEST["data"]);
        $course = json_decode($_REQUEST["course"]);
        $uid = $_REQUEST["uid"];
        $dateYear = $_REQUEST["dateYear"];

        $title = $data->title;
        $detail = $data->detail;
        $couid = $course->couid;
        $uid;
        $dateYear;
        $sql = "INSERT INTO `talk` VALUES (NULL,'$uid','$couid','$title','$detail','$dateYear')";
        if (mysqli_query($conn,$sql)) {
            $content["talk"] = "Ok";
        }else {
            $content["talk"] = "NotOk";
        }
        echo json_encode($content);
    }

    // 话题列表展示
    function FindTalk(){
        global $conn;
        global $content;

        $cid = $_REQUEST["cid"];
        $order = $_REQUEST["order"];
        $day = $_REQUEST["day"];

        
        if($day != ""){
            $day = "AND `date` = '$day'";
        }
        $sql = "SELECT `couid`,`date`,`title` FROM `course` WHERE `cid` = '$cid' ORDER BY `date` $order";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        foreach ($detail as $key => $value) {
            $couid = $value["couid"];
            $title = $value["title"];
            $sql = "SELECT * FROM `talk` WHERE `couid` = '$couid' $day ORDER BY `date` $order";
            $que = mysqli_query($conn,$sql);
            $detail2 = mysqli_fetch_all($que,1);
            $content["topic"][$key] = [$title,$detail2];
        }
        
        echo json_encode($content);
    }

    // 展示某一话题详细和跟帖
    function FindTalkDe(){
        global $conn;
        global $content;

        $taid = $_REQUEST["taid"];
        $sql = "SELECT `couid`,`date`,`title` FROM `course` WHERE `cid` = '$cid' ORDER BY `date` $order";
        $que = mysqli_query($conn,$sql);
        $detail = mysqli_fetch_all($que,1);
        $content["topic"] = $detail;

        echo json_encode($content);
    }

    // 上传跟帖
    function FollowTalk(){

    }
?>