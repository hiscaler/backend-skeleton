<?php

use yii\helpers\Url;

$request = Yii::$app->getRequest();
$alias = $request->get('alias');
$category = $request->get('category');
?>
<div id="header">
    <div class="banner">

    </div>
    <div class="main-menus">
        <ul class="inner">
            <li class="first"<?= $controllerId == 'site' ? ' active' : '' ?>><a href="<?= Url::toRoute(['/site/index']) ?>">商城首页</a></li>
            <li<?= $controllerId == 'page' && $alias == 'about' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/page/index', 'alias' => 'about']) ?>">公司信息</a></li>
            <li<?= $category == 3 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 3]) ?>">重要通知</a></li>
            <li<?= $category == 4 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 4]) ?>">新品上市</a></li>
            <li<?= $category == 5 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 5]) ?>">促销通知</a></li>
            <li<?= $category == 6 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 6]) ?>">调价通知</a></li>
            <li<?= $category == 7 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 7]) ?>">培训计划</a></li>
            <li<?= $category == 8 && $controllerId == 'news' ? ' class="active"' : '' ?>><a href="<?= Url::toRoute(['/news/index', 'category' => 8]) ?>">展会信息</a></li>
            <li class="last<?= $controllerId == 'feedbacks' ? ' active' : '' ?>"><a href="<?= Url::toRoute(['/feedbacks/index']) ?>">客户留言</a></li>
        </ul>
    </div>
</div>