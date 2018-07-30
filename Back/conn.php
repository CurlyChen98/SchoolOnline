<?php
    $mysql_server_name='localhost'; //改成自己的mysql数据库服务器
	$mysql_username='root'; //改成自己的mysql数据库用户名
	$mysql_password='8502262'; //改成自己的mysql数据库密码
	$mysql_database='schoolonline'; //改成自己的mysql数据库名
	
	$conn=mysqli_connect($mysql_server_name,$mysql_username,$mysql_password,$mysql_database)or die("链接错误") ; //连接数据库;
	mysqli_query($conn,"set names 'utf8'"); //数据库输出编码 应该与你的数据库编码保持一致.建议用UTF-8 国际标准编码.
?>