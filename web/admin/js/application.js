Date.prototype.fromUnixTimestamp = function (value) {
    return new Date(parseFloat(value) * 1000);
};
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month 
        "d+": this.getDate(), //day 
        "h+": this.getHours(), //hour 
        "m+": this.getMinutes(), //minute 
        "s+": this.getSeconds(), //second 
        "q+": Math.floor((this.getMonth() + 3) / 3), //quarter 
        "S": this.getMilliseconds() //millisecond 
    };
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
};
Number.prototype.toFixed = function (d) {
    var s = this + "";
    if (!d) {
        d = 0;
    }
    if (s.indexOf(".") == -1) {
        s += ".";
    }
    s += new Array(d + 1).join("0");
    if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0," + (d + 1) + "})?)\\d*$").test(s)) {
        var s = "0" + RegExp.$2, pm = RegExp.$1, a = RegExp.$3.length, b = true;
        if (a == d + 2) {
            a = s.match(/\d/g);
            if (parseInt(a[a.length - 1]) > 4) {
                for (var i = a.length - 2; i >= 0; i--) {
                    a[i] = parseInt(a[i]) + 1;
                    if (a[i] == 10) {
                        a[i] = 0;
                        b = i != 1;
                    } else {
                        break;
                    }
                }
            }
            s = a.join("").replace(new RegExp("(\\d+)(\\d{" + d + "})\\d$"), "$1.$2");
        }
        if (b) {
            s = s.substr(1);
        }
        return (pm + s).replace(/\.$/, "");
    }
    return this + "";
};
/**
 * Lock UI
 */
(function ($) {
    $.fn.lock = function () {
        this.unlock();
        $('body').append('<div id="widget-lock-ui" class="lock-ui" style="position:absolute;width:100%;height:100%;top:0;left:0;z-index:1000;background-color:#000;cursor:wait;opacity:.7;filter: alpha(opacity=70);"><div>');
    };
    $.fn.unlock = function () {
        $('#widget-lock-ui').remove();
    };
})(jQuery);

$(function () {
    $(document).on('pjax:error', function (xhr, textStatus, error, options) {
        console.log(xhr);
        console.log(textStatus);
        console.log(error);
        console.log(options);
        layer.alert(textStatus.responseText);
    });
    $('.ajax').on('click', function () {
        var $this = $(this);
        $.ajax({
            type: 'POST',
            url: $this.attr('href'),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $this.remove();
                } else {
                    layer.alert(response.error.message);
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
            }
        });
        
        return false;
    });
    
    $('#logout').click(function () {
        var $t = $(this);
        layer.confirm('您是否确认退出本系统？', function (index) {
            layer.close(index);
            window.location.href = $t.attr('href');
        });
        return false;
    });
});

function clone(myObj) {
    if (typeof (myObj) != 'object' || myObj == null)
        return myObj;
    var newObj = new Object();
    for (var i in myObj) {
        newObj[i] = clone(myObj[i]);
    }
    
    return newObj;
}

var yadjet = yadjet || {};
yadjet.icons = yadjet.icon || {};
yadjet.icons.boolean = [
    '/images/no.png',
    '/images/yes.png'
];
yadjet.utils = yadjet.utils || {
    getCsrfParam: function () {
        return $('meta[name=csrf-param]').attr('content');
    },
    getCsrfToken: function () {
        return $('meta[name=csrf-token]').attr('content');
    }
};
yadjet.actions = yadjet.actions || {
    toggle: function (selector, url) {
        var dataExt = arguments[2] ? arguments[2] : {},
            trData = arguments[3] ? arguments[3] : [],
            callback = arguments[4] ? arguments[4] : undefined;
        $(document).off(selector).on('click', selector, function (event) {
            event.stopImmediatePropagation();
            event.stopPropagation();
            var $this = $(this);
            var $tr = $this.parent().parent();
            var data = {
                id: $tr.attr('data-key'),
                _csrf: yadjet.utils.getCsrfToken()
            };
            for (var key in dataExt) {
                data[key] = dataExt[key];
            }
            console.info(trData);
            for (var key in trData) {
                // `data-key` To `dataKey`
                var t = trData[key].toLowerCase();
                t = t.replace(/\b\w+\b/g, function (word) {
                    return word.substring(0, 1).toUpperCase() + word.substring(1);
                });
                t = t.replace('-', '');
                t = t.substring(0, 1).toLowerCase() + t.substring(1);
                data[t] = $tr.attr('data-' + trData[key]);
            }
            console.info(data);
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                beforeSend: function (xhr) {
                    $this.hide().parent().addClass('running-c-c');
                }, success: function (response) {
                    if (response.success) {
                        var data = response.data;
                        $this.attr('src', yadjet.icons.boolean[data.value ? 1 : 0]);
                        if (data.updatedAt) {
                            $tr.find('td.rb-updated-at').html(data.updatedAt);
                        }
                        if (data.updatedBy) {
                            $tr.find('td.rb-updated-by').html(data.updatedBy);
                        }
                        if (data.onOffDatetime) {
                            $tr.find('td.rb-on-off-datetime').html(data.onOffDatetime);
                        }
                        if (callback) {
                            callback(response);
                        }
                    } else {
                        layer.alert(response.error.message);
                    }
                    $this.show().parent().removeClass('running-c-c');
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                    $this.show().parent().removeClass('running-c-c');
                }
            });
            
            return false;
        });
    },
    gridColumnConfig: function () {
        jQuery(document).on('click', '#menu-buttons li a.grid-column-config', function () {
            var $this = $(this),
                gridId = $this.attr('data-grid-id');
            $.ajax({
                type: 'POST',
                url: $this.attr('href'),
                data: {models: $('#' + gridId + ' > table').attr('data-models')},
                beforeSend: function (xhr) {
                    $.fn.lock();
                }, success: function (response) {
                    layer.open({
                        title: '表格栏位设定',
                        content: '<div id="yad-grid-columns-setting-render">' + response + '</div>',
                        skin: 'layer-fix',
                        yes: function () {
                            $.pjax.reload({container: '#' + gridId});
                        }
                    });
                    $.fn.unlock();
                }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.alert('[ ' + XMLHttpRequest.status + ' ] ' + XMLHttpRequest.responseText);
                    $.fn.unlock();
                }
            });
            
            return false;
        });
    }
};

yadjet.actions.gridColumnConfig();

$(function () {
    $('.tabs-common li a').on('click', function () {
        var $t = $(this),
            $widget = $t.parent().parent().parent();
        $t.parent().siblings().removeClass('active');
        $t.parent().addClass('active');
        $widget.find('.tab-panel').hide();
        $widget.find('#' + $t.attr('data-toggle')).show();
        return false;
    });
});
$(document).on('click', '.search-button a', function () {
    var $t = $(this);
    if ($t.attr('data-toggle') === 'show') {
        $t.attr('data-toggle', 'hide');
        $('.form-search').hide();
    } else {
        $t.attr('data-toggle', 'show');
        $('.form-search').show();
    }
    
    return false;
});
if ($('#mts-app').length) {
    var vm = new Vue({
        el: '#mts-app',
        data: {
            // 所有数据验证规则
            validators: {},
            meta: {
                // 某个数据项的验证规则
                validators: {}
            },
            // 对象的 meta 信息
            metaObjects: {}
        },
        methods: {
            // 判断对象是否为空
            isEmptyObject: function (e) {
                var t;
                for (t in e)
                    return !1;
                return !0;
            }
        },
        computed: {
            // 当前 Meta 对象数据验证规则以及规则相关属性定义
            metaValidators: function () {
                var validators = [], validator;
                for (var validatorName in this.validators) {
                    validator = clone(this.validators[validatorName]);
                    validator.name = validatorName;
                    validator.active = false;
                    for (var j in this.meta.validators) {
                        if (this.meta.validators[j].name === validatorName) {
                            validator.active = true;
                            validator.options = this.meta.validators[j].options;
                            break;
                        }
                    }
                    validators.push(validator);
                }
                
                return validators;
            }
        }
    });
}
/**
 * 复制内容
 * @type {Clipboard}
 */
var clipboard = new Clipboard('.btn-copy');
clipboard.on('success', function (e) {
    var $o = $(e.trigger).attr('data-clipboard-target');
    layer.tips('复制成功', $(e.trigger).attr('data-clipboard-target'), {tips: [2, '#000000']});
    e.clearSelection();
});
clipboard.on('error', function (e) {
    layer.tips('复制失败，请手动复制', $(e.trigger).attr('data-clipboard-target'), {tips: 4});
});

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