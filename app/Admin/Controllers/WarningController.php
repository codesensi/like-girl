<?php
/*
 * Like Girl — 后台「警告/非法操作」控制器
 *
 * 归并原文件：
 *   feifa.php   → feifa（非法访问列表，后台布局）
 *   warning.php → warning 为公开非法操作提示/记录页，不经过本控制器鉴权，
 *                 由 public/admin/warning.php 独立承载（与原文件行为一致）。
 */

require_once __DIR__ . '/AbstractAdminController.php';

class WarningController extends AbstractAdminController
{
    /* 非法访问列表 */
    public function feifa()
    {
        global $connect;

        $res = mysqli_query($connect, "select * from warning order by id desc");
        $warnings = [];
        if ($res) {
            while ($r = mysqli_fetch_array($res)) {
                $warnings[] = $r;
            }
        }

        $this->render('feifa', ['warnings' => $warnings]);
    }
}
