<?php
function connect(){
	$host='localhost';
	$dbname='phone_shop';
	$username='root';
	$password='';
	// chuẩn bị nguồn dữ liệu
	$dns="mysql:host=$host;dbname=$dbname;charset=utf8";
	$conn= new PDO($dns,$username,$password);
	//$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::ATTR_ERRMODE);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	return $conn;
	}