<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>锚图</title>
    <link rel="stylesheet" href="styles.css" type="text/css" />
</head>

<?php

include("db.php");


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
<script type="text/javascript">
    var file_upload;

    function showURL(url) {
        let httpurl = url;
        let mdurl = "![" + "](" + httpurl + ")";
        console.log(httpurl);
        console.log(mdurl);
        let pdiv = document.createElement("div");
        pdiv.style = "width: 100%; height: auto; border:0px;text-align:center;vertical-align: middle;margin: 0 auto;"

        let ptext = document.createElement("p");
        ptext.style = "font-size:20px;font-color:black;font-weight:550;";
        ptext.appendChild(document.createTextNode("上传成功!"));

        let ptext1 = document.createElement("p");
        ptext1.style = "font-size:20px;font-color:black;font-weight:550;";
        ptext1.appendChild(document.createTextNode("复制markdown链接:"));

        let pmdurl = document.createElement("input");
        pmdurl.style = "height:auto;width:80%;font-size:20px;background-color: rgba(255,255,255,0.4);border-radius: 5px;"
        pmdurl.value = mdurl;

        let ptext2 = document.createElement("p");
        ptext2.style = "font-size:20px;font-color:black;font-weight:550;";
        ptext2.appendChild(document.createTextNode("复制https链接:"));

        let phttpurl = document.createElement("input");
        phttpurl.style = "height:auto;width:80%;font-size:20px;background-color: rgba(255,255,255,0.4);border-radius: 5px;"
        phttpurl.value = httpurl;

        let pbr = document.createElement("p");
        pbr.style = "font-size:20px;font-color:black;font-weight:550;";
        pbr.innerHTML = "<br>"
        //pbr.appendChild(document.createTextNode("<br>"));

        pdiv.appendChild(ptext);
        pdiv.appendChild(ptext1);
        pdiv.appendChild(pmdurl);
        pdiv.appendChild(ptext2);
        pdiv.appendChild(phttpurl);
        pdiv.appendChild(pbr);
        document.getElementById("result").replaceChildren(pdiv);
        pmdurl.click();
        pmdurl.select();
    }

    function showResult(jsonStr) {
        // console.log(curHost); 
        let json = JSON.parse(jsonStr);
        if (json.status == "ok") {
            let url = json.result;
            showURL(url);
        } else if (json.status == "format") {
            alert("上传失败!\n 文件格式不支持!");
        } else if (json.status == "limit") {
            alert(json.result);
        } else if (json.status == "same") {
            // let url = json.result;
            let url = json.result;
            alert("似乎这张图片已经上传过了,请注意检查是否是您想要的图片.");
            showURL(url);
        } else {
            alert("上传失败\n" + json.result);
        }
    }

    function uploadFile(file) {
        if (file == null) {
            alert("没有选择文件!")
        } else {
            let toast = document.getElementById("toast");
            toast.style.display = "flex";
            let form = new FormData();
            form.append("file", file, file.name);
            // form.append("filename", file.name);
            console.log(file.name);
            fetch('upload.php', {
                method: 'POST',
                body: form
            }).then(function(data) {
                data.text().then(s => {
                    str = s;
                    console.log("response:" + str);
                    if (str.indexOf("<title>413 Request Entity Too Large</title>") != -1) {
                        alert("文件过大! 单图片最大16MB.");
                    } else {
                        showResult(str);
                    }
                });
            }).catch(function(err) {
                console.log(err);
            }).finally(function() {
                console.log("上传完成");
                toast.style.display = "none";
            });
        }
    }

    function showImg(img) {
        img.style = "vertical-align:middle; max-height:500px; max-width: 85%";
        if (document.getElementById("preview-empty") != null) {
            document.getElementById("preview-empty").remove();
        }
        let previewDiv = document.getElementById("preview");
        // previewDiv.parentElement.parentElement.style.height = img.style.height;
        img.style.maxHeight = "700px";
        img.style.maxWidth = "400px";
        previewDiv.replaceChild(img, document.getElementById("preview").firstChild);
    }

    function getImageFromDrop(e) {
        let files = e.dataTransfer.files;
        let file = files[0];
        if (file.type.indexOf("image") != -1) {
            let url = window.URL || window.webkitURL;
            let img = new Image(); //手动创建一个Image对象
            img.src = url.createObjectURL(file); //创建Image的对象的url
            showImg(img);
            file_upload = file;
        } else {
            alert("文件类型错误,仅支持图片文件. (" + file.name + ")");
            return;
        }
    }

    function getImageFromPaste(e) {
        if (e.clipboardData.items) {
            let files = e.clipboardData.items;
            if (files[0].kind == 'file') {
                let file = files[0].getAsFile();
                if (files[0].type.indexOf('image/') !== -1) {
                    let url = window.URL || window.webkitURL;
                    let img = new Image();
                    img.src = url.createObjectURL(file);
                    showImg(img);
                    file_upload = file;
                } else {
                    alert("文件类型错误,仅支持图片文件. (" + file.name + ")");
                }
            }
        }
    }


    //测试 全页面拖拽事件
    document.ondrop = (event) => {
        console.log('全页面拖拽事件.');
        event.preventDefault(); //阻止浏览器默认行为
        event.stopPropagation(); //阻止事件冒泡
        getImageFromDrop(event);
        return false
    }
    //粘贴
    document.onpaste = function(event) {
        console.log('粘贴事件.')
        getImageFromPaste(event);
        return false;
    };
    //按钮点击
    document.getElementById("upload").onclick = function() {
        uploadFile(file_upload);
    }
    //回车
    /*
    var isctrl = false;
    document.onkeydown = function (event) {
        //记着阻止其他按键
        console.log(event.key);
        if (event.key == "Enter") {
            document.getElementById("upload").click();
            event.preventDefault();
        } else if (event.key == "Control") {
            console.log("Ctrl");
            isctrl = true;
            window.setTimeout(function () {
                isctrl = false;
            }, 500);
        } else if (event.key == 'v') {
            console.log("v");
            if (!isctrl) {
                event.preventDefault();
            }
        }else if (event.key == 'c') {
            console.log("c");
            if (!isctrl) {
                event.preventDefault();
            }
        }
        else if (event.key.length >= 2 && event.key[0] == 'F') {
            console.log("function key");
        }
        else {
            event.preventDefault();
        }
    }*/
    // var done = false;
    const doodle = document.querySelector('css-doodle');
    document.onclick = function() {
        doodle.update();
    }
</script>
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