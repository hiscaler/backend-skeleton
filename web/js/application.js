/**
 * 设为首页
 */
function setHomepage(obj, vrl){
    try{
        obj.style.behavior='url(#default#homepage)';
        obj.setHomePage(vrl);
        NavClickStat(1);
    }
    catch(e){
        if(window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  
            } catch (e) { 
                alert("抱歉！您的浏览器不支持直接设为首页。请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为“true”，点击“加入收藏”后忽略安全提示，即可设置成功。");  
            }
            var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            prefs.setCharPref('browser.startup.homepage', vrl);
        }
    }
}

/**
 * 加入收藏
 */
function addFavorite(sURL, sTitle) {
    try {
        window.external.addFavorite(sURL, sTitle);
    }　catch(e) {
        try {
            window.sidebar.addPanel(sTitle, sURL, "");
        } catch(e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

/*
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.5.0
 *
 */
(function(a){a.fn.lazyload=function(g){var c={threshold:0,failurelimit:0,event:"scroll",effect:"show",container:window};if(g){a.extend(c,g)}var f=this;if("scroll"==c.event){a(c.container).bind("scroll",function(b){var d=0;f.each(function(){if(a.abovethetop(this,c)||a.leftofbegin(this,c)){}else if(!a.belowthefold(this,c)&&!a.rightoffold(this,c)){a(this).trigger("appear")}else{if(d++>c.failurelimit){return false}}});var h=a.grep(f,function(e){return!e.loaded});f=a(h)})}this.each(function(){var b=this;if(undefined==a(b).attr("original")){a(b).attr("original",a(b).attr("src"))}if("scroll"!=c.event||undefined==a(b).attr("src")||c.placeholder==a(b).attr("src")||(a.abovethetop(b,c)||a.leftofbegin(b,c)||a.belowthefold(b,c)||a.rightoffold(b,c))){if(c.placeholder){a(b).attr("src",c.placeholder)}else{a(b).removeAttr("src")}b.loaded=false}else{b.loaded=true}a(b).one("appear",function(){if(!this.loaded){a("<img />").bind("load",function(){a(b).hide().attr("src",a(b).attr("original"))[c.effect](c.effectspeed);b.loaded=true}).attr("src",a(b).attr("original"))}});if("scroll"!=c.event){a(b).bind(c.event,function(e){if(!b.loaded){a(b).trigger("appear")}})}});a(c.container).trigger(c.event);return this};a.belowthefold=function(e,b){if(b.container===undefined||b.container===window){var d=a(window).height()+a(window).scrollTop()}else{var d=a(b.container).offset().top+a(b.container).height()}return d<=a(e).offset().top-b.threshold};a.rightoffold=function(e,b){if(b.container===undefined||b.container===window){var d=a(window).width()+a(window).scrollLeft()}else{var d=a(b.container).offset().left+a(b.container).width()}return d<=a(e).offset().left-b.threshold};a.abovethetop=function(e,b){if(b.container===undefined||b.container===window){var d=a(window).scrollTop()}else{var d=a(b.container).offset().top}return d>=a(e).offset().top+b.threshold+a(e).height()};a.leftofbegin=function(e,b){if(b.container===undefined||b.container===window){var d=a(window).scrollLeft()}else{var d=a(b.container).offset().left}return d>=a(e).offset().left+b.threshold+a(e).width()};a.extend(a.expr[':'],{"below-the-fold":"$.belowthefold(a, {threshold : 0, container: window})","above-the-fold":"!$.belowthefold(a, {threshold : 0, container: window})","right-of-fold":"$.rightoffold(a, {threshold : 0, container: window})","left-of-fold":"!$.rightoffold(a, {threshold : 0, container: window})"})})(jQuery);

$(document).on('click', 'a.delete-photo', function () {
    $(this).parent().remove();
    
    return false;
});
/**
 * 图片上传处理
 *
 * 显示图片：
 * <a href="javascript:;" class="delete-photo" data-url="<?= \yii\helpers\Url::toRoute(['delete-photo', 'id' => $photo['id']]) ?>">X</a>
 * 上传图片：
 * <input data-input-name="Person[photoFiles][]" type="file" accept="image/*" multiple="" name="pic" data-url="<?= \yii\helpers\Url::toRoute(['/api/file/uploading', 'key' => 'upload_file']) ?>" id="file-photo" class="weui-uploader__input">
 */
function uploadPhoto(vFD, DataURL, t, inputName) {
    var oXHR = new XMLHttpRequest;
    oXHR.onreadystatechange = function () {
        if (4 == this.readyState)
            if (console.log(this.responseText), t.parent().removeClass("load"), 200 == this.status) {
                var response = $.parseJSON(this.responseText);
                if (response.success) {
                    t.parent().parent().find('.weui-uploader__files').append('<li class="weui-uploader__file" style="background-image:url(' + response.data.photoPath + ')"><a href="javascript:;" class="delete-photo" data-url="' + response.data.deleteUrl + '">X</a><input type="hidden" name="' + inputName + '" value="' + response.data.photo + '" /></li>');
                    if (response.data.uploadCompleted) {
                        $('.buttons').show();
                    }
                    t.parent().removeClass('uploading');
                } else {
                    alert(response.error.message);
                }
            } else {
                alert('抱歉，图片上传失败，请重新上传！');
            }
    };
    oXHR.open("POST", t.attr('data-url'));
    oXHR.send(vFD);
}

function getJpegBlob(o) {
    var e = o.width,
        t = o.height;
    e > 800 && (t *= 800 / e, e = 800),
    t > 1600 && (e *= 1600 / t, t = 1600);
    var a = $('<canvas width="' + e + '" height="' + t + '"></canvas>')[0],
        n = a.getContext("2d");
    n.drawImage(o, 0, 0, e, t);
    var l = convertCanvasToBlob(a);
    return l;
}

function convertCanvasToBlob(o) {
    var e, t, a, n, l, s, r, d;
    for (n = "image/jpeg", e = o.toDataURL(n), t = window.atob(e.split(",")[1]), l = new window.ArrayBuffer(t.length), s = new window.Uint8Array(l), a = 0; a < t.length; a++)
        s[a] = t.charCodeAt(a);
    return r = window.WebKitBlobBuilder || window.MozBlobBuilder,
        r ? (d = new r, d.append(l), d.getBlob(n)) : new window.Blob([l], {
            type: n
        })
}

$("#file-photo").on("change", function (o) {
    var $this = $(this),
        e = o.target,
        t = e.files[0],
        a = t.size,
        n = t.type,
        inputName = $this.attr('data-input-name'),
        uploadLimit = parseInt($this.attr('data-limit'));
    
    if (uploadLimit && ($this.parent().parent().find('li').length + 1) > uploadLimit) {
        alert("您最多可以上传 " + uploadLimit + " 张图片");
        return true;
    }
    if (console.log("源文件：" + a, n), "image/jpeg" != n && "image/png" != n)
        return layer.open({
            content: "请上传jpg、png格式的图片！",
            className: "layerInfo",
            time: 5
        }),
            this.value = "",
            !1;
    if (a / 1024 > 10e3)
        return layer.open({
            content: "图片尺寸不能超过10M，请重新上传！",
            className: "layerInfo",
            time: 5
        }),
            this.value = "",
            !1;
    $this.parent().addClass("uploading");
    var l = new FileReader;
    l.onload = function () {
        var o = l.result,
            e = new FormData;
        if ("image/jpeg" == n) {
            var a = new Image;
            a.onload = function () {
                var n = getJpegBlob(a);
                n.size > 0 ? e.append("upload_file", n) : e.append("upload_file", t),
                    uploadPhoto(e, o, $this, inputName),
                    console.log("转换后blob：" + n.size, n.type)
            };
            a.src = o;
        } else {
            e.append("upload_file", t);
            uploadPhoto(e, o, $this, inputName);
        }
    };
    l.readAsDataURL(t);
    $this.attr('value', '');
});
