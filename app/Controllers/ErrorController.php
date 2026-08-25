<?php
/*
 * Like Girl — 前端「错误/封禁」控制器
 * 渲染 `app/View/pages/error.php`（独立完整页面，不包含站点 header/footer）。
 */

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/Core/Database.php';
require_once dirname(__DIR__) . '/Models/IpError.php';

class ErrorController
{
    public function index()
    {
        global $connect;

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $db = new Database();
        $record = $db->fetchOne(
            "SELECT * FROM IPerror WHERE State='" . $db->escape($ip) . "' LIMIT 1"
        );

        $banTime = $record ? $record['Time'] : null;
        $errorTip = $record ? $record['text'] : null;

        view('pages/error', [
            'banTime'  => $banTime,
            'errorTip' => $errorTip,
        ]);
    }
}
