<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/application.css',
        'css/form.css',
        'js/layer/skin/layer.css',
    ];
    public $js = [
        'js/underscore-min.js',
        'js/layer/layer.js',
        'js/application.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
