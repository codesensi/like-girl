<?php
/*
 * Like Girl — 非法操作提示/记录页（公开，不经过后台 Auth，与原 warning.php 行为一致）
 */

require dirname(__DIR__, 2) . '/app/config/database.php';
require dirname(__DIR__, 2) . '/app/Core/helpers.php';

$file = isset($_GET['route']) ? $_GET['route'] : '';

if ($file) {
    $ip   = $_SERVER["REMOTE_ADDR"];
    $gsd  = get_ip_city_New($ip);
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
    $stmt = $conn->prepare("insert into warning (ip,gsd,time,file) values (?,?,?,?)");
    $stmt->bind_param("ssss", $ip, $gsd, $time, $file);
    $result = $stmt->execute();
    if (!$result) echo "错误信息：" . $stmt->error;
} else {
    die ("<script>alert('参数错误 请注意你的行为');</script>");
}

// 渲染独立警告页
extract($GLOBALS, EXTR_SKIP);
include dirname(__DIR__, 2) . '/app/View/admin/pages/warning.php';
