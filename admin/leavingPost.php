<?php

include_once 'Database.php';
include_once 'Function.php';

$name = trim($_POST['name'] ?? '');
$qq = trim($_POST['qq'] ?? '');
$text = trim($_POST['text'] ?? '');
$time = time();
$file = $_SERVER['PHP_SELF'];
$Filter_IP = $_SERVER['REMOTE_ADDR'];

// 已留言过
if (!empty($_COOKIE["KiCookie"])) {
    echo "8";
    exit;
}

// 非 POST 请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "5";
    exit;
}

// 参数验证
if (!is_numeric($qq) || empty($name) || empty($text)) {
    echo "5";
    exit;
}

if (!filter_var($Filter_IP, FILTER_VALIDATE_IP)) {
    echo "4";
    exit;
}

if (!checkQQ($qq)) {
    echo "3";
    exit;
}

// 入库
$User_City = get_ip_city_New($Filter_IP);
if (!$User_City) $User_City = '未知';
$Filter_Name = replaceSpecialChar($name);
$Filter_QQ = replaceSpecialChar($qq);
$Filter_Text = replaceSpecialChar($text);
$Filter_Time = replaceSpecialChar($time);

$stmt = $conn->prepare("insert into leaving (name,QQ,text,\"time\",ip,city) values (?,?,?,?,?,?)");
$stmt->bind_param("sissss", $Filter_Name, $Filter_QQ, $Filter_Text, $Filter_Time, $Filter_IP, $User_City);
$result = $stmt->execute();

if ($result) {
    setcookie("KiCookie", $Filter_IP, time() + 3600 * 24, "/");
    echo "1";
} else {
    echo "0:" . $conn->error;
}


