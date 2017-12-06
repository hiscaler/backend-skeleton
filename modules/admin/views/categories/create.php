<?php
/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$menus = [];
$typeOptions = \app\models\Category::typeOptions();
foreach ($typeOptions as $key => $value) {
    $menus[] = ['label' => $value . Yii::t('app', 'Categories'), 'url' => ['index', 'CategorySearch[type]' => $key]];
    $menus[] = ['label' => Yii::t('app', 'Create') . $value . Yii::t('model', 'Category'), 'url' => ['create', 'type' => $key]];
}
if (!$typeOptions) {
    $menus[] = ['label' => Yii::t('app', 'Create'), 'url' => ['create']];
}
$this->params['menus'] = $menus;
?>
<div class="category-create">

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
