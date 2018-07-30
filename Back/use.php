<?php
    include("conn.php");
    header('Content-type:text/json'); 

    $do = $_REQUEST["do"];
    $do();
    $content = [
        "talk"=>"",
        "use"=>"",
        "class"=>"",
    ];
    
    // 首次打开时先根据openid在数据库注册一个用户
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
        $sql = "SELECT `s_uid`,`s_uame`,`s_ulevel`,`cid` FROM `s_use` WHERE `s_uopid` = '$mdOpenId'";
        $que = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($que);
        if($num>0){
            // 非第一次
            $que = mysqli_query($conn,$sql);
            $detail = mysqli_fetch_all($que,1);
            $content["use"] = $detail[0];
            $content["talk"] = "Have";
            if($detail[0]["cid"] != ""){
                $one = $detail[0]["cid"];
                $sql = "SELECT * FROM `s_class` WHERE `s_cid` = '$one'";
                $que = mysqli_query($conn,$sql);
                $detail = mysqli_fetch_all($que,1);
                $content["class"] = $detail[0];
            }
        }else{
            // 第一次
            $sql = "INSERT INTO `s_use` VALUES (NULL, '', '$mdOpenId', '1', '', '', now())";
            $que = mysqli_query($conn,$sql);
            $id = mysqli_insert_id($conn);
            $content["use"] = $id;
            $content["talk"] = "NotHave";
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
        $sql = "SELECT * FROM `s_class` WHERE `classkey` = '$classkey'";
        $que = mysqli_query($conn,$sql);
        $num = mysqli_num_rows($que);
        if($num>0){
            // 若班级密匙正确
            $content["talk"] = "Right";
            $detail = mysqli_fetch_all($que,1);
            $content["class"] = $detail[0];
            $cid = $content["class"]["s_cid"];
            // 先修改学生信息
            $sql = "UPDATE `s_use` SET `cid` = '$cid' , `s_uame` = '$studentkey' WHERE `s_uid` = '$uid';";
            $que = mysqli_query($conn,$sql);
            // 查询并输出学生信息
            $sql = "SELECT `s_uid`,`s_uame`,`s_ulevel` FROM `s_use` WHERE `s_uid` = '$uid'";
            $content["zzzzzzzzz"] = $sql;
            
            $que = mysqli_query($conn,$sql);
            $detail = mysqli_fetch_all($que,1);
            $content["use"] = $detail[0];
        }else{        
            // 若班级密匙不正确
            $content["talk"] = "NotRight";
        }
        echo json_encode($content);
    }
?>