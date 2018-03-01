<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '数据库管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<a href="<?= \yii\helpers\Url::toRoute('backup') ?>">备份数据库</a>
<?php if ($histories): ?>
    <div class="grid-view">
        <table class="table">
            <thead>
            <tr>
                <th>序号</th>
                <th>名称</th>
                <th>备份时间</th>
                <th class="buttons-2 last">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($histories as $i => $history): ?>
                <tr>
                    <td class="serial-number"><?= $i + 1 ?></td>
                    <td><?= $history['name'] ?></td>
                    <td class="datetime"><?= $history['date'] ?></td>
                    <td>
                        <a href="<?= \yii\helpers\Url::toRoute(['restore', 'name' => $history['name']]) ?>">恢复备份</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <?= \app\modules\admin\components\MessageBox::widget([
        'message' => '您还没有备份过数据库。<a class="button" href="' . \yii\helpers\Url::toRoute('backup') . '">开始备份数据库</a>',
    ]) ?>
<?php endif; ?>
