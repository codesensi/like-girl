<?php
/*
 * Like Girl — 后台「关于」控制器
 *
 * 归并原文件：
 *   aboutSet.php → index（关于页配置表单）
 *   aboutPost.php→ update（保存配置）
 */

require_once __DIR__ . '/AbstractAdminController.php';

class AboutController extends AbstractAdminController
{
    /* 关于页配置 */
    public function index()
    {
        global $connect;

        $res = mysqli_query($connect, "SELECT * FROM about");
        $about = $res ? mysqli_fetch_array($res) : [];

        $this->render('aboutSet', ['about' => $about]);
    }

    /* 保存（AJAX） */
    public function update()
    {
        global $connect;

        $fields = [
            'title', 'aboutimg', 'info1', 'info2', 'info3', 'btn1', 'btn2',
            'infox1', 'infox2', 'infox3', 'infox4', 'infox5', 'infox6', 'btnx2',
            'infof1', 'infof2', 'infof3', 'infof4', 'btnf3',
            'infod1', 'infod2', 'infod3', 'infod4', 'infod5',
        ];
        $sets = [];
        foreach ($fields as $f) {
            $sets[] = "`$f` = '" . trim($_POST[$f] ?? '') . "'";
        }
        $sql = "update about set " . implode(',', $sets) . " where id = '1'";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }
}
