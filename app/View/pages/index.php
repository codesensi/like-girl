<?php
// 由 app/Controllers/HomeController@index 渲染
// 全局变量（$text/$diy/$version/$Animation 等）由 app/bootstrap.php 注入
?>

<head>
    <meta charset="utf-8" />
    <title><?php echo $text['title'] ?></title>
    <link rel="stylesheet" href="assets/css/index.css?LikeGirl=<?php echo $version ?>">
</head>

<body>
    <div id="pjax-container">
        <!-- 时间区域 -->
        <div class="time">
            <span id="span_dt_dt"></span>
            <b id="tian"></b>
            <b id="shi"></b>
            <b id="fen"></b>
            <b id="miao"></b>
        </div>
        <script>show_date_time();</script>
        <!-- 卡片区域 -->
        <div class="card-wrap">
            <div class="row central">
                <div
                    class="card col-lg-4 col-sm-12 col-sm-x-12 flex-h <?php if ($text['Animation'] == "1") {
                        echo 'animated fadeInUp';
                    } ?>">
                    <img src="assets/img/card/likegirl_card_1.svg">
                    <div class="text">
                        <span><a target="_self" href="little.php"><?php echo $text['card1'] ?></a></span>
                        <p><?php echo $text['deci1'] ?></p>
                    </div>
                </div>
                <div
                    class="card col-lg-4 col-sm-12 col-sm-x-12 flex-h <?php if ($text['Animation'] == "1") {
                        echo 'animated fadeInUp';
                    } ?>">
                    <img src="assets/img/card/likegirl_card_2.svg" alt="">
                    <div class="text">
                        <span><a target="_self" href="leaving.php"><?php echo $text['card2'] ?></a></span>
                        <p><?php echo $text['deci2'] ?></p>
                    </div>
                </div>
                <div
                    class="card col-lg-4 col-sm-12 col-sm-x-12 flex-h <?php if ($text['Animation'] == "1") {
                        echo 'animated fadeInUp';
                    } ?>">
                    <img src="assets/img/card/likegirl_card_3.svg" alt="">
                    <div class="text">
                        <span><a target="_self" href="about.php"><?php echo $text['card3'] ?></a></span>
                        <p><?php echo $text['deci3'] ?></p>
                    </div>
                </div>
                <div
                    class="card-b col-lg-6 col-12 col-sm-12 flex-h <?php if ($text['Animation'] == "1") {
                        echo 'animated fadeInUp';
                    } ?>">
                    <img src="assets/img/card/likegirl_card_4_1.svg" alt="">
                    <div class="text">
                        <span><a target="_self" href="loveImg.php">Love Photo</a></span>
                        <p>恋爱相册 记录最美瞬间</p>
                    </div>
                </div>
                <div
                    class="card-b col-lg-6 col-12 col-sm-12 flex-h <?php if ($text['Animation'] == "1") {
                        echo 'animated fadeInUp';
                    } ?>">
                    <img src="assets/img/card/likegirl_card_5.svg" alt="">
                    <div class="text">
                        <span><a target="_self" href="list.php">Love List</a></span>
                        <p>恋爱列表 你我之间的约定</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    view('partials/footer');
    ?>

</body>

</html>
