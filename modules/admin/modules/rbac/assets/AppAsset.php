<?php

namespace app\modules\admin\modules\rbac\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $sourcePath = '@app/modules/admin/modules/rbac/statics';

    public $css = [
        'css/application.css',
    ];

    public $js = [
        'js/application.js',
    ];

    public $depends = [
        'app\modules\admin\assets\AppAsset',
    ];

}
