var file_upload;
function showURL(url) {
    // let arrURL = document.URL.split("//");
    // let end = arrURL[1].indexOf("/");
    // let curHost = "https://" + arrURL[1].substr(0, end) + "/";
    let curHost = "https://i.catium.top:81/";
    let httpurl = curHost + url;
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
        // let url = json.result;
        let url = json.result.split("\/")[1];
        showURL(url);
    } else if (json.status == "format") {
        alert("上传失败!\n 文件格式不支持!");
    } else if (json.status == "limit") {
        alert(json.result);
    }
    else if (json.status == "same") {
        // let url = json.result;
        let url = json.result.split("\/")[1];
        alert("似乎这张图片已经上传过了,请注意检查是否是您想要的图片.");
        showURL(url);
    }
    else {
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
        }).then(function (data) {
            data.text().then(s => {
                str = s;
                console.log("response:" + str);
                if (str.indexOf("<title>413 Request Entity Too Large</title>") != -1) {
                    alert("文件过大! 单图片最大16MB.");
                } else {
                    showResult(str);
                }
            });
        }).catch(function (err) {
            console.log(err);
        }).finally(function () {
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
        let img = new Image();              //手动创建一个Image对象
        img.src = url.createObjectURL(file);//创建Image的对象的url
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


//拖拽
// document.getElementById("pastebin").ondrop = (event) => {
//     console.log('粘贴板拖拽事件.');
//     event.preventDefault(); //阻止浏览器默认行为
//     event.stopPropagation(); //阻止事件冒泡
//     getImageFromDrop(event);
//     return false
// }
//测试 全页面拖拽事件

document.ondrop = (event) => {
    console.log('全页面拖拽事件.');
    event.preventDefault(); //阻止浏览器默认行为
    event.stopPropagation(); //阻止事件冒泡
    getImageFromDrop(event);
    return false
}
//粘贴
document.onpaste = function (event) {
    console.log('粘贴事件.')
    getImageFromPaste(event);
    return false;
};
//按钮点击
document.getElementById("upload").onclick = function () {
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
document.onclick = function () {
    // if (!done) {
        // document.getElementById("music").play();
        // setInterval(function () {
            doodle.update();
        // }, 930);
        // document.getElementById("music").play();
        // done = true;
    // }
}




// document.addEventListener('click', function (e) {
//     doodle.update();
// });

