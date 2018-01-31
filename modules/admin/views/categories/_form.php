<?php

use app\models\Category;
use app\models\Option;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>
    <div class="form-outside">
        <div class="form">
            <?php $form = ActiveForm::begin(); ?>
            <div class="entry" id="module-name-choice" style="<?= $model->parent_id ? 'display: none;' : '' ?>">
                <?= $form->field($model, 'module_name')->dropDownList(\app\models\Module::getModules(), ['prompt' => '']) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="entry">
                <?= $form->field($model, 'parent_id')->dropDownList(Category::tree(Category::RETURN_TYPE_ALL), ['prompt' => '']) ?>

                <?= $form->field($model, 'icon_path')->fileInput() ?>
            </div>
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
            <div class="entry">
                <?= $form->field($model, 'ordering')->dropDownList(Option::orderingOptions()) ?>

                <?= $form->field($model, 'enabled')->checkbox([], false) ?>
            </div>
            <div class="form-group buttons">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php \app\modules\admin\components\JsBlock::begin() ?>
    <script type="text/javascript">
        $(function () {
            $('#category-parent_id').change(function () {
                console.info($(this).val());
                if ($(this).val() > 0) {
                    $('#module-name-choice').hide();
                } else {
                    $('#module-name-choice').show();
                }
            });
        });
    </script>
<?php \app\modules\admin\components\JsBlock::end() ?>