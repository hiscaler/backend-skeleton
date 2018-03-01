<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = '数据库管理';
$this->params['breadcrumbs'][] = $this->title;
$c = 0;
$numbers = 0;
$message = [];
foreach ($processTables as $tableName => $number) {
    $c++;
    $numbers += $number;
    $message[] = "<span>$tableName:</span> $number 条记录。";
}
array_unshift($message, "<strong>共备份 $c 个表，合计 $numbers 条记录。</strong>");
?>

<?= \app\modules\admin\components\MessageBox::widget([
    'message' => $message,
]) ?>

