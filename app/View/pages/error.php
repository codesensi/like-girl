<!--
 * @Version：Like Girl 5.2.1-Stable
 * @Author: Ki.
 * @Date: 2025-09-03 00:00:00
 * @LastEditTime: 2025-09-03
 * @Description: 愿得一心人 白头不相离
 * @Document：https://blog.kikiw.cn/index.php/archives/52/
 * @Copyright (c) 2023 - 2025 by Ki All Rights Reserved. 
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Message：开发不易 版权信息请保留 
-->

<!DOCTYPE html>
<html lang="zh-CN">
<?php
// 由 app/Controllers/ErrorController@index 渲染
// $banTime（封禁时间）、$errorTip（封禁原因）由控制器注入，避免与全局站点 $text 冲突
?>
<head>
    <meta charset="utf-8"/>
    <title>抱歉 您的IP已被封禁</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>

    <!-- App css -->
    <link href="/admin/assets/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="/admin/assets/css/app.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/css/loading.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400&display=swap" rel="stylesheet">
</head>

<div id="Loadanimation" style="z-index:999999;">
    <div id="Loadanimation-center">
        <div id="Loadanimation-center-absolute">
            <div class="xccx_object" id="xccx_four"></div>
            <div class="xccx_object" id="xccx_three"></div>
            <div class="xccx_object" id="xccx_two"></div>
            <div class="xccx_object" id="xccx_one"></div>
        </div>
    </div>
</div>
<script src="assets/jquery/jquery.min.js"></script>
<script>
    $(function () {
        $("#Loadanimation").fadeOut(1000);
    });
</script>

<style>
    .card {
        border-radius: 15px;
    }

    .card-header.pt-4.pb-4.text-center.bg-primary {
        border-radius: 15px 15px 0 0;
    }

    .btn-primary {
        padding: 10px 25px;
        border-radius: 20px;
    }

    body {
        font-family: 'Noto Serif SC', serif;
        font-weight: 700;
    }

    span.badge.badge-danger-lighten {
        font-size: 1.1rem;
    }
    span.badge.badge-success-lighten {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
</style>

<body class="authentication-bg">

<div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card">

                    <!-- Logo -->
                    <div class="card-header pt-4 pb-4 text-center bg-primary">
                        <a href="##">
                                <span
                                        style="color: #fff;font-size: 1.3rem;font-family: '宋体';font-weight: 700;">你的行为已被记录</span>
                        </a>
                    </div>

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <h4 class="text-dark-50 text-center mt-0 font-weight-bold">Like_Girl 5.2.1</h4>
                            <p class="text-muted">善语结善缘 恶语伤人心</p>
                            <p class="text-muted mb-4">
                                <span class="badge badge-success-lighten">封禁时间：
                                <?php if ($banTime) { ?><?php echo $banTime; ?><?php } else { ?>无 <?php } ?>
                                </span>
                                <span class="badge badge-danger-lighten">封禁原因：
                                <?php if ($errorTip) { ?><?php echo $errorTip; ?><?php } else { ?>您的IP未被封禁 <?php } ?>
                                </span>

                            </p>
                        </div>
                        <div class="text-center w-75 m-auto" style="margin-bottom: 40px!important;">
                            <?php if ($errorTip){?> <img src="https://img.gejiba.com/images/ff63a429a6fbd20d6748242b182d2159.jpg" style="width: 100%;border-radius: 20px;" alt=""> <?php } ?>

                        </div>

                        <div class="form-group mb-0 text-center">
                            <a href="https://wpa.qq.com/msgrd?v=3&uin=3439780232&site=qq&menu=yes">
                                <button
                                        class="btn btn-primary" type="submit"> 申诉解封
                                </button>
                            </a>
                        </div>

                    </div> <!-- end card-body -->
                </div>
                <!-- end card -->

            </div>
            <!-- end row -->

        </div> <!-- end col -->
    </div>
    <!-- end row -->
</div>
<!-- end container -->
</div>
<!-- end page -->

<footer class="footer footer-alt">
    Copyright © 2022 Ki. && <a href="https://blog.kikiw.cn/index.php/archives/24/" target="_blank">Like_Girl</a> All
    Rights Reserved.
</footer>

<!-- App js -->
<script src="/admin/assets/js/app.min.js"></script>
</body>

</html>
