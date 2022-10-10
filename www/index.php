<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>锚图</title>
    <link rel="stylesheet" href="styles.css" type="text/css" />
</head>

<?php

include("db.php");

function getBackgroundImg()
{
    return exec_sql("SELECT background_img FROM mgmt WHERE id = 0")[0]['background_img'];
}
function getGridImg()
{
    return exec_sql("SELECT grid_img FROM mgmt WHERE id = 0")[0]['grid_img'];
}
function getNotice()
{
    return exec_sql("SELECT notice FROM mgmt WHERE id = 0")[0]['notice'];
}
function getCount()
{
    return exec_sql("SELECT file_count FROM mgmt WHERE id = 0")[0]['file_count'];
}
?>

<body class="body" style="background-image:url(<?php echo getBackgroundImg(); ?>)">
    <!-- 上传过程显示 -->
    <div class="toast" id="toast">
        <span class="load"></span>
        <span>上传中...</span>
    </div>
    <section class="main-div">
        <css-doodle>
            :doodle {
            @grid: 10x10;
            background: #0a0c27;
            /*关键性质1*/
            overflow:visable;
            }
            :container {
            /*margin-top: -10px;*/
            /*margin-left: -10px;*/
            /*关键性质2*/!
            /* 容器高度为零就看不见了 */
            height: 0px;
            }
            --hue: calc(180 + 1.5 * @row * @col);
            /*background: hsl(var(--hue), 50%, 70%);*/
            background: url(<?php echo getGridImg(); ?>);
            background-size: contain;

            margin: -.5px;
            transition: @r(0.7s) ease;
            clip-path: polygon(@pick(
            '0 0, 100% 0, 100% 100%',
            '0 0, 100% 0, 0 100%',
            '0 0, 100% 100%, 0 100%',
            '100% 0, 100% 100%, 0 100%'
            ));
            width: 100px;
            height: 100px;
            /*关键性质3*/
            /*背景需要低的纵深*/
            z-index: -10;
            /*border: 20px solid rgba(255,255,255,0.5);*/
            :after {
            font-size: 50px;
            color: red;
            /*content: \@hex(@rand(0x1F600, 0x1F640));*/
            }
        </css-doodle>
        <p class="title">Catiumの图床</p>
        <p class="remark">
            单图片最大16MB, 上传文件md5验重. 复制图片到本页粘贴或拖拽文件到预览框即可上传.
        </p>
        <!-- 新的公告写法 php抽取数据库 -->
        <p class="notification">
            <?php echo getNotice(); ?>
        </p>
        <p id="count" class="remark">已上传<?php echo getCount(); ?>张图片"</p>
        <!-- <img id="pastebin" contenteditable="true" dropzone="true" draggable="true" src="res/add.png" style="width:60%"> -->
        <button id="upload" class="button">
            上传
        </button>
        <br>
        <br>
        <div id="result" style="width:70%; height:auto;text-align:center;margin: 0 auto;border: 0px solid;border-radius: 10px; border-color: white; background-color: rgba(255, 255, 255, 0.5);">
        </div>
        <div class="preview" contenteditable="true">
            <p style="text-align: center;color:black; font-size: 20px;font-weight: 600;">这里是预览
            </p>
            <div id="preview" style="vertical-align: middle;margin: 0 auto; ">
                <p id="preview-empty" style="color: rgba(0,0,0,0.5);">空空如也</p>
            </div>
            <br>
        </div>
        <p>善用图床,可用空间仅有1TB</p>
    </section>
    <!-- music -->
    <!-- <audio autoplay="autoplay" loop id="music">
        <source src="./Tinker.JunKtion.Grey.Charnel.mp3" audio="">
    </audio> -->
</body>

<!-- CSS doodle 绘图模块 -->
<script src="https://cdn.jsdelivr.net/npm/css-doodle@0.29.0/css-doodle.min.js"></script>
<!-- 主页的脚本 -->
<script type="text/javascript" src="index.html.js"></script>
<!-- 猫 -->
<script type="text/javascript">
    document.write('<iframe src="https://XiEn1847.github.io/tools/adult-cat.html" width="320" height="430" style="position:fixed;bottom:0px;right:10px;z-index:100" frameBorder="0"></iframe>');
</script>

<!-- live2d的脚本 -->
<!-- <script src="https://cdn.jsdelivr.net/npm/live2d-widget@3.0.4/lib/L2Dwidget.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/live2d-widget@3.0.4/lib/L2Dwidget.min.js"></script>
<script type="text/javascript">
    L2Dwidget.init({
        "model": {
            // jsonPath: "https://unpkg.com/live2d-widget-model-chitose@1.0.5/assets/chitose.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-haruto@1.0.5/assets/haruto.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-hibiki@1.0.5/assets/hibiki.model.json",
            jsonPath: "https://unpkg.com/live2d-widget-model-hijiki@1.0.5/assets/hijiki.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-izumi@1.0.5/assets/izumi.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-koharu@1.0.5/assets/koharu.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-miku@1.0.5/assets/miku.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-ni-j@1.0.5/assets/ni-j.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-tororo@1.0.5/assets/tororo.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-tsumiki@1.0.5/assets/tsumiki.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-unitychan@1.0.5/assets/unitychan.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-z16@1.0.5/assets/z16.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-nico@1.0.5/assets/nico.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-nipsilon@1.0.5/assets/nipsilon.model.json",
            // jsonPath: "https://unpkg.com/live2d-widget-model-nito@1.0.5/assets/nito.model.json",
            // "jsonPath": "https://unpkg.com/live2d-widget-model-wanko@1.0.5/assets/wanko.model.json",
            // "jsonPath": "https://unpkg.com/live2d-widget-model-shizuku@1.0.5/assets/shizuku.model.json",
            "scale": 0.5
        },
        "display": {
            "position": "left",
            "width": 300,
            "height": 360,
            "hOffset": 0,
            "vOffset": -5
        },
        "mobile": {
            "show": true,
            "scale": 0.5
        },
        "react": {
            "opacityDefault": 0.8,
            "opacityOnHover": 0.8
        }
    });
</script>
 -->

</html>