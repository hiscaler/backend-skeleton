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
