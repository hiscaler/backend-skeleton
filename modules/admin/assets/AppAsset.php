<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web/admin';
    public $css = [
        'css/application.css',
        'css/common.css',
        'css/widget-grid-view.css',
        'css/form.css',
        'layer/skin/layer.css',
    ];
    public $js = [
        'js/doT.min.js',
        'layer/layer.js',
        'js/vue/vue.js',
        'js/vue/vue-resource.min.js',
        'js/application.js',
        'js/underscore-min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
