<?php
/*
 * Like Girl — 后台「站点设置」控制器
 *
 * 归并原文件：
 *   Set.php          → set（渲染设置页）
 *   adminPost.php    → updateBasics（基本信息 + 开关）
 *   loveadminPost.php→ updateCouple（情侣配置）
 *   CardadminPost.php→ updateCard（卡片 & 版权）
 *   CopyadminPost.php→ updateIcp（备案/版权 → 跳转首页）
 */

require_once __DIR__ . '/AbstractAdminController.php';

class SettingController extends AbstractAdminController
{
    /* 设置页（含基本/情侣/卡片三组表单） */
    public function set()
    {
        $this->render('set');
    }

    /* 基本信息 + 开关（AJAX） */
    public function updateBasics()
    {
        global $connect;

        $title   = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES);
        $logo    = htmlspecialchars(trim($_POST['logo'] ?? ''), ENT_QUOTES);
        $writing = htmlspecialchars(trim($_POST['writing'] ?? ''), ENT_QUOTES);
        $WebPjax = trim($_POST['WebPjax'] ?? '');
        $WebBlur = trim($_POST['WebBlur'] ?? '');

        $result = mysqli_query(
            $connect,
            "update text set title = '$title', logo = '$logo' , writing = '$writing' where id = '1'"
        );
        echo $result ? "1" : "0";

        $diy = mysqli_query(
            $connect,
            "update diySet set Pjaxkg = '$WebPjax' , Blurkg = '$WebBlur' where id = '1'"
        );
        echo $diy ? "3" : "4";
    }

    /* 情侣配置（AJAX） */
    public function updateCouple()
    {
        global $connect;

        $boy       = htmlspecialchars(trim($_POST['boy'] ?? ''));
        $girl      = htmlspecialchars(trim($_POST['girl'] ?? ''));
        $boyimg    = htmlspecialchars(trim($_POST['boyimg'] ?? ''), ENT_QUOTES);
        $girlimg   = htmlspecialchars(trim($_POST['girlimg'] ?? ''), ENT_QUOTES);
        $startTime = trim($_POST['startTime'] ?? '');

        if (checkQQ($boyimg) && checkQQ($girlimg)) {
            $sql = "update text set startTime = '$startTime', girlimg = '$girlimg' , boyimg = '$boyimg', girl = '$girl' , boy = '$boy' ";
            echo mysqli_query($connect, $sql) ? "1" : "0";
        } else {
            echo "3";
        }
    }

    /* 卡片 & 版权（AJAX） */
    public function updateCard()
    {
        global $connect;

        $card1 = htmlspecialchars(trim($_POST['card1'] ?? ''), ENT_QUOTES);
        $card2 = htmlspecialchars(trim($_POST['card2'] ?? ''), ENT_QUOTES);
        $card3 = htmlspecialchars(trim($_POST['card3'] ?? ''), ENT_QUOTES);
        $deci1 = htmlspecialchars(trim($_POST['deci1'] ?? ''), ENT_QUOTES);
        $deci2 = htmlspecialchars(trim($_POST['deci2'] ?? ''), ENT_QUOTES);
        $deci3 = htmlspecialchars(trim($_POST['deci3'] ?? ''), ENT_QUOTES);
        $icp   = htmlspecialchars(trim($_POST['icp'] ?? ''), ENT_QUOTES);
        $Copyright = htmlspecialchars(trim($_POST['Copyright'] ?? ''), ENT_QUOTES);
        $bgimg = htmlspecialchars(trim($_POST['bgimg'] ?? ''), ENT_QUOTES);

        $sql = "update text set icp = '$icp', Copyright = '$Copyright', card1 = '$card1',card2 = '$card2',card3 = '$card3',deci1 = '$deci1',deci2 = '$deci2',deci3 = '$deci3',bgimg = '$bgimg' ";
        echo mysqli_query($connect, $sql) ? "1" : "0";
    }

    /* 备案/版权（CopyadminPost，成功后返回首页） */
    public function updateIcp()
    {
        global $connect;

        $adminName = trim($_POST['adminName'] ?? '');
        $icp       = trim($_POST['icp'] ?? '');
        $Copyright = trim($_POST['Copyright'] ?? '');

        $sql = "update text set icp = '$icp', Copyright = '$Copyright' ";
        if (mysqli_query($connect, $sql)) {
            echo "<script>alert('更新成功');location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('更新失败');history.back();</script>";
        }
    }
}
