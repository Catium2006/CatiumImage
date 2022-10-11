<?php

include("db.php");

function checkPasswdMD5($md5)
{
    $v = exec_sql("SELECT password_md5 FROM mgmt WHERE id = 0")[0];
    $passwd_md5 = $v['password_md5'];
    if ($md5 == $passwd_md5) {
        setcookie("passwd", $md5, time() + 600); //10分钟内不需要登录
        return true;
    }
    return false;
}

function checkPasswd($passwd)
{
    return checkPasswdMD5(md5($passwd));
}

function isAdmin()
{
    return checkPasswdMD5($_COOKIE["passwd"]);
}

function setPassword($passwd)
{

    $adminmail = exec_sql("SELECT adminmail FROM mgmt WHERE id = 0")[0]['adminmail'];

    $title = "Password Changed";

    $message = "
    <p>Hi there!</p>
    <p>The administrator password of your CatiumImage(" . getDomain() . ") has changed, check it please!</p>
    <p>New password: <pre>$passwd</pre></p>";

    $headers = "Content-Type: text/html; charset=UTF-8";

    if (mail($adminmail, $title, $message, $headers, '')) {
        $passwdmd5 = md5($passwd);
        exec_sql("UPDATE mgmt SET password_md5 = '$passwdmd5' WHERE id = 0");
        return "ok";
    } else {
        return "failed: can't exec mail().";
    }
}

function setNotice($arg)
{
    exec_sql("UPDATE mgmt SET notice = '$arg' WHERE id = 0");
    return "ok";
}

//删除图片
function deleteFile($filename_short)
{
    $target = './upload/' . $filename_short . '';
    if (file_exists($target)) {
        if (unlink($target)) {
            $sql = "DELETE FROM records WHERE file_name_short=" . "'" . $filename_short . "'";
            exec_sql($sql);
            $sql = "UPDATE mgmt SET file_count = file_count - 1 WHERE id = 0";
            exec_sql($sql);
            return "ok";
        } else {
            return "unlink failed: " . $target;
        }
    } else {
        return "no such file: " . $target;
    }
}

function getImgListFromDB($arg)
{
    $array = exec_sql("SELECT * FROM records $arg");
    return $array;
}

function setBackground($arg)
{
    exec_sql("UPDATE mgmt SET background_img = './upload/$arg' WHERE id = 0");
    return "ok";
}

function setGrid($arg)
{
    exec_sql("UPDATE mgmt SET grid_img = './upload/$arg' WHERE id = 0");
    return "ok";
}

function remove_surfix($str, $surfix)
{
    if (str_ends_with($str, $surfix)) {
        return substr($str, 0, strlen($str) - strlen($surfix));
    }
    return $str;
}


//js调用这些函数,还要保证cookie
if ($_GET['function'] != null && isAdmin()) {
    $target = $_GET['function'];
    if ($target == 'deleteFile') {
        echo deleteFile($_GET['arg']);
    }
    if ($target == 'setBackground') {
        echo setBackground($_GET['arg']);
    }
    if ($target == 'setGrid') {
        echo setGrid($_GET['arg']);
    }
    if ($target == 'setNotice') {
        echo setNotice($_GET['arg']);
    }
    if ($target == 'setPassword') {
        echo setPassword($_GET['arg']);
    }
    exit();
}
?>

<!-- UI -->
<!DOCTYPE>
<html>

<head>
    <title>后台</title>
</head>
<style>
    .toast {
        display: none;
        position: fixed;
        z-index: 999;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 18rem;
        height: 18rem;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.8);
        border-radius: 1rem;
        color: white;
        font-size: 2.5rem;
    }

    .load {
        display: inline-block;
        margin-bottom: 1.5rem;
        height: 4rem;
        width: 4rem;
        border: 0.4rem solid transparent;
        border-top-color: white;
        border-left-color: white;
        border-bottom-color: white;
        animation: circle 1s infinite linear;
        -webkit-animation: circle 1s infinite linear;
        /* Safari 和 Chrome */
        border-radius: 50%
    }

    @-webkit-keyframes circle {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(-360deg)
        }
    }

    .body {
        width: 95%;

        /* height: 95%; */
        margin: 0 auto;
        border: 0;
        vertical-align: middle;
        text-align: center;
        background-image: url(<?php echo getBackgroundImg(); ?>);
        background-size: cover;
    }

    .dashboard_frame {
        text-align: center;
        margin: 0 auto;
        width: 80%;
        height: 100%;
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 20px;
    }

    .table_frame {
        text-align: center;
        margin: 0 auto;
        width: 50%;
        height: 85%;
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        float: right;

        /* overflow: hidden; */
        overflow-x: scroll;
        overflow-y: scroll;
    }

    .table {
        margin: 0 auto;
        border: 1px black solid;
        /* float: right; */
        border-radius: 10px;
    }

    .preview_frame {
        text-align: center;
        margin: 0 auto;
        width: 44%;
        height: 85%;
        backdrop-filter: blur(8px);
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        float: left;
    }

    .tr {
        border: 1px black solid;
    }

    .td {
        border: 1px black solid;
    }

    .th {
        border: 1px black solid;
    }
</style>

<body class="body">

    <div>
        <?php
        if (isAdmin()) {
            // setcookie("passwd", pwd_md5, time() + 600); //10分钟内不需要登录
        ?>
            <div style="position:fixed;top:0px;right:10px;z-index:100;background-color:rgba(0.8,0.8,0.8,0.5);font-size:20px;">
                <p>设置公告</p>
                <!-- input-notice -->
                <input id="input-notice" style="font-size:20px;line-height:20px;height:30px;word-break:break-all;" type="text" value="<?php echo getNotice(); ?>">
                <button style="font-size:20px;" onclick="setNotice()">提交</button>
                <br>
                <p>修改密码</p>
                <!-- input-password -->
                <input id="input-password" style="font-size:20px;line-height:20px;height:30px;word-break:break-all;" type="password">
                <button style="font-size:20px;" onclick="setPassword()">提交</button>
            </div>
            <!-- 隐藏的加载画面 -->
            <div id="dashboard" class="dashboard_frame">
                <p style="font-size: 30px;">控制台</p>
                <p id="info" style=" font-size: 20px;">共有<?php echo getCount(); ?>张图片</p>
                <div class="toast" id="toast">
                    <span class="load"></span>
                    <span>加载中...</span>
                </div>
                <div style="width:2%;float:left;">
                    <p></p>
                </div>
                <div class="preview_frame">
                    <p>这里是预览</p>
                    <img id="preview" style="width:80%;">
                    <button id="delete" onclick="deleteImg()" style="width:50%;font-size:20px;background-color:red;color:azure;">删除</button>
                    <button id="setBackground" onclick="setBackground()" style="width:50%;font-size:20px;background-color:deepskyblue;color:azure;">设置为背景</button>
                    <button id="setGrid" onclick="setGrid()" style="width:50%;font-size:20px;background-color:deepskyblue;color:azure;">设置为Grid</button>
                </div>
                <div style="width:2%;float:right;">
                    <p></p>
                </div>
                <div class="table_frame">
                    <table class="table">
                        <tr class="tr">
                            <th class="th">
                                原始文件名
                            </th>
                            <th class="th">
                                短文件名
                            </th>
                            <!-- <th class="th">
                                文件md5值
                            </th> -->
                            <th class="th">
                                文件大小
                            </th>
                            <th class="th">
                                上传时间
                            </th>
                        </tr>
                        <script type="text/javascript">
                            let toast = document.getElementById("toast");
                            toast.style.display = "flex";
                        </script>
                        <?php
                        $page = 1;
                        if ($_GET['page'] != null) {
                            $page = $_GET['page'];
                        }
                        $offset = 20 * ($page - 1); //现在要显示开始的地方
                        $sort = '';
                        $request = '';
                        $arg = '';
                        if ($_GET['sort'] != null) {
                            $sort = $_GET['sort'];
                            $arg = $arg . 'order by ' . $_GET["sort"] . ' ';
                        } else {
                            $sort = "upload_time desc";
                            $arg = $arg . 'order by ' . $sort . ' ';
                        }
                        if ($_GET['request'] != null) {
                            $request = $_GET['request'];
                            $arg = $arg .  $_GET['request'] . ' ';
                        }
                        $array = getImgListFromDB($arg);
                        $idx = 0;
                        foreach ($array as $v) {
                            if ($idx >= $offset && $idx <= $offset + 20) {
                        ?>
                                <tr class="tr">
                                    <td id=<?php echo '"tableid/' . $v['file_name_short'] . '"' ?> style="background-color:burlywood;border-radius: 8px;border: 1px solid;">
                                        <?php
                                        if (strlen($v['file_name_original']) > 16) {
                                            echo substr($v['file_name_original'], 0, 16) . '...';
                                        } else {
                                            echo $v['file_name_original'];
                                        }
                                        ?>
                                        <script type="text/javascript">
                                            let ele<?php echo $idx ?> = document.getElementById(<?php echo '"tableid/' . $v['file_name_short'] . '"' ?>);
                                            ele<?php echo $idx ?>.onmouseenter = function() {
                                                let img = document.getElementById("preview");
                                                img.src = <?php echo '"upload/' . $v['file_name_short'] . '"' ?>;
                                                ele<?php echo $idx ?>.style.backgroundColor = "rgb(202,164,115)";
                                            }
                                            ele<?php echo $idx ?>.onmouseleave = function() {
                                                ele<?php echo $idx ?>.style.backgroundColor = "rgb(222,184,135)";
                                            }
                                            ele<?php echo $idx ?>.oncontextmenu = function(eve) {
                                                eve.preventDefault();
                                                let url = <?php echo '"' . $v['file_name_short'] . '"' ?>;
                                                let arrURL = document.URL.split("//");
                                                let end = arrURL[1].indexOf("/");
                                                let host = "<?php echo getDomain() ?>";
                                                url = host + "upload/" + url;
                                                let mdurl = "![" + "](" + url + ")";
                                                let p = document.createElement('input');
                                                // p.innerText = mdurl;
                                                // p.style.display = "none";
                                                p.value = mdurl;
                                                p.select();
                                                // p.style.display = "none";
                                                p.style.display = "flex";
                                                p.style.position = "fixed";
                                                p.style.left = "50%";
                                                p.style.top = "50%";
                                                document.body.appendChild(p);
                                                p.select();
                                                document.execCommand('copy')
                                                setTimeout(function() {
                                                    p.style.display = "none";
                                                }, 100);
                                            }
                                        </script>
                                    </td>
                                    <td class="td">
                                        <?php echo $v['file_name_short'] ?>
                                    </td>
                                    <!-- <td class="td">
                                        <?php //echo substr($v['file_md5'], 0, 16) . '...' 
                                        ?>
                                    </td> -->
                                    <td class="td">
                                        <?php echo sprintf("%.1f", $v['file_size'] / 1024) . 'KB' ?>
                                    </td>
                                    <td class="td">
                                        <?php echo $v['upload_time'] ?>
                                    </td>
                                </tr>
                        <?php
                            }
                            $idx++;
                        }
                        ?>
                        <script type="text/javascript">
                            // let toast = document.getElementById("toast");
                            toast.style.display = "none";
                        </script>
                    </table>
                    <div>
                        <p>共有<?php echo sprintf("%d", ceil(count($array) / 20)) ?>页, 这是第<?php echo $page ?>页</p>
                        <button onclick="page_pre()">上一页</button>
                        <button onclick="page_nxt()">下一页</button>
                        <br>
                        <button onclick="sort('upload_time')">按时间排序</button>
                        <button onclick="sort('file_name_original')">按原始文件名排序</button>
                        <button onclick="sort('file_size')">按文件大小排序</button>
                        <br>
                        <button onclick="reverse()">反转顺序</button>
                        <br>
                        <script type="text/javascript">
                            function page_nxt() {
                                window.location.replace("<?php echo $_SERVER['PHP_SELF'] . '?page=' . min(ceil(count($array) / 20), ($page + 1)) . '&request=' . $request  . '&sort=' . $sort; ?>");
                            }

                            function page_pre() {
                                window.location.replace("<?php echo $_SERVER['PHP_SELF'] . '?page=' . max(1, ($page - 1)) . '&request=' . $request  . '&sort=' . $sort; ?>");
                            }

                            function sort(arg) {
                                window.location.replace("<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($page) . '&request=' . $request  . '&sort='; ?>" + arg);
                            }

                            function reverse() {
                                let url =
                                    "<?php if (str_contains($sort, " desc")) {
                                            echo $_SERVER['PHP_SELF'] . '?page=' . ($page) . '&request=' . $request  . '&sort=' . remove_surfix($sort, ' desc');
                                        } else {
                                            echo $_SERVER['PHP_SELF'] . '?page=' . ($page) . '&request=' . $request  . '&sort=' . $sort . ' desc';
                                        } ?>";
                                window.location.replace(url);
                            }
                        </script>
                    </div>
                </div>
                <div style="width:2%;float:right;">
                    <p></p>
                </div>

            </div>
        <?php
        } else if ($_POST['pwd'] == null) {
        ?>
            <!-- 没有登录的时候 -->
            <div style="text-align: center; margin: 0 auto;width:80%;height:100%;backdrop-filter: blur(8px); background-color: rgba(255,255,255,0.3);border-radius: 20px;">
                <br>
                <br>
                <br>
                <div style="text-align: center; margin: 0 auto;width:40%;height:auto;backdrop-filter: blur(8px); background-color: rgba(255,255,255,0.4);border-radius: 20px;">
                    <p style="font-size: 30px;">输入管理密码</p>
                    <form action="" method="post" style="width: 60%; height: auto; border:0px;text-align:center;vertical-align: middle;margin: 0 auto;">
                        <br>
                        <input type="password" name="pwd" style="font-size:20px;font-weight:550;width:50%;font-size:20px;background-color: rgba(255,255,255,0.4);border-radius: 5px;" />
                        <br>
                        <br>
                        <br>
                        <input type="submit" style="width: 60%;height:40px;vertical-align:middle;font-size:20px;background-color: rgba(255,165,0,0.8);border-radius: 8px;" />
                        <br>
                    </form>
                    <br>
                </div>
            </div>
            <?php
        } else {
            if (!checkPasswd($_POST['pwd'])) {
            ?>
                <!-- 登录失败 -->
                <div style="text-align: center; margin: 0 auto;width:80%;height:100%;backdrop-filter: blur(8px); background-color: rgba(255,255,255,0.3);border-radius: 20px;">
                    <br>
                    <br>
                    <div style="text-align: center; margin: 0 auto;width:40%;height:auto;backdrop-filter: blur(8px); background-color: rgba(255,255,255,0.4);border-radius: 20px;">
                        <br>
                        <p style="font-size: 30px;">密码错误</p>
                        <br>
                    </div>
                </div>
        <?php
            } else {
                // 登录成功
                // setcookie("passwd", pwd_md5, time() + 600); //10分钟内不需要登录
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        ?>
    </div>
</body>

<?php
if (isAdmin()) {
?>
    <script type="text/javascript">
        function setBackground() {
            console.log("设置背景按钮点击");
            let url = document.getElementById("preview").src;
            if (url != "") {
                let toast = document.getElementById("toast");
                toast.style.display = "flex";
                url = url.split("//")[1];
                url = url.split("/")[2];
                console.log("尝试设置背景为" + url)
                fetch('?function=setBackground&arg=' + url, {
                    method: "GET",
                }).then(function(data) {
                    data.text().then(s => {
                        console.log("function response:" + s);
                        if (s == "ok") {
                            alert("设置成功");
                            //相当于刷新
                            window.location.reload;
                        } else {
                            console.log("设置失败:" + s);
                            alert("设置失败");
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                }).finally(function() {
                    toast.style.display = "none";
                    window.location.reload();
                });
            } else {
                let bt0 = document.getElementById("setBackground");
                bt0.textContent = "没有正在操作的文件";
                setTimeout(function() {
                    let bt = document.getElementById("setBackground");
                    bt.textContent = "设置为背景";
                }, 1500);
            }
        }

        function setGrid() {
            let url = document.getElementById("preview").src;
            if (url != "") {
                let toast = document.getElementById("toast");
                toast.style.display = "flex";
                url = url.split("//")[1];
                url = url.split("/")[2];
                fetch('?function=setGrid&arg=' + url, {
                    method: "GET",
                }).then(function(data) {
                    data.text().then(s => {
                        console.log("function response:" + s);
                        if (s == "ok") {
                            alert("设置成功");
                            //相当于刷新
                            window.location.reload;
                        } else {
                            console.log("设置失败:" + s);
                            alert("设置失败");
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                }).finally(function() {
                    toast.style.display = "none";
                    window.location.reload();
                });
            } else {
                let bt0 = document.getElementById("setGrid");
                bt0.textContent = "没有正在操作的文件";
                setTimeout(function() {
                    let bt = document.getElementById("setGrid");
                    bt.textContent = "设置为Grid";
                }, 1500);
            }
        }

        function setNotice() {
            let arg = "" + document.getElementById("input-notice").value;
            fetch('?function=setNotice&arg=' + arg, {
                method: "GET",
            }).then(function(data) {
                data.text().then(s => {
                    console.log("function response:" + s);
                    if (s == "ok") {
                        alert("设置成功");
                        window.location.reload;
                    } else {
                        console.log("设置失败:" + s);
                        alert("设置失败");
                    }
                });
            }).catch(function(err) {
                console.log(err);
            }).finally(function() {
                toast.style.display = "none";
                // window.location.reload();
            });
        }

        function setPassword() {
            let arg = "" + document.getElementById("input-password").value;
            console.log('password:' + arg);
            fetch('?function=setPassword&arg=' + arg, {
                method: "GET",
            }).then(function(data) {
                // console.log(data);
                data.text().then(s => {
                    console.log("function response:" + s);
                    if (s == "ok") {
                        alert("设置成功");
                        window.location.reload;
                    } else {
                        console.log("设置失败:" + s);
                        alert("设置失败");
                    }
                });
            }).catch(function(err) {
                console.log(err);
            }).finally(function() {
                toast.style.display = "none";
                // window.location.reload();
            });
        }

        function deleteImg() {
            console.log("删除按钮点击");
            let url = document.getElementById("preview").src;
            if (url != "") {
                let toast = document.getElementById("toast");
                toast.style.display = "flex";
                url = url.split("//")[1];
                url = url.split("/")[2];
                console.log("尝试删除" + url)
                fetch('?function=deleteFile&arg=' + url, {
                    method: "GET",
                }).then(function(data) {
                    data.text().then(s => {
                        console.log("function response:" + s);
                        if (s == 'ok') {
                            alert("删除成功");
                            //相当于刷新
                            // window.location.href = "/backdoor.php?";
                        } else {
                            console.log("删除失败:" + s);
                            alert("操作失败");
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                }).finally(function() {
                    toast.style.display = "none";
                    window.location.reload();
                });
            } else {
                let bt0 = document.getElementById("delete");
                bt0.textContent = "没有正在操作的文件";
                setTimeout(function() {
                    let bt = document.getElementById("delete");
                    bt.textContent = "删除";
                }, 1500);
            }
        }
    </script>

<?php
}
?>

</html>