<?php
/*
 * Like Girl — IP 封禁检查（由原 ipjc.php 迁移）
 *
 * 查询 IPerror 表，若当前访问 IP 在封禁列表中则终止并提示。
 * 依赖全局 $connect（由 app/config/database.php 提供）。
 */

if (isset($connect) && $connect) {
    $ipchaxun = "select * from IPerror";
    $ipres = mysqli_query($connect, $ipchaxun);

    if ($ipres) {
        while ($IPinfo = mysqli_fetch_array($ipres)) {
            $iplist = $IPinfo['State'];
            $banned_ip = array($iplist);
            $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';

            if (in_array(getenv("REMOTE_ADDR"), $banned_ip)) {
                die("<script>alert('你的IP($ip)已被封禁，禁止访问本页面');location.href = 'error.php';</script>");
            }
        }
    }
}
