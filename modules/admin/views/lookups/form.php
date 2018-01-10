<?php

use yii\helpers\Html;

$this->title = '常规设定';
$this->params['breadcrumbs'][] = ['label' => '基本设置', 'url' => ['lookups/index']];
$this->params['breadcrumbs'][] = '常规设定';


$this->params['menus'] = [
    ['label' => Yii::t('app', 'List'), 'url' => ['index']],
    ['label' => Yii::t('app', 'Create'), 'url' => ['create']],
];
?>

<div class="clearfix">
    <ul class="tabs-common">
        <?php
        $i = 0;
        foreach (app\models\Lookup::getGroupOptions() as $group => $name):
            if (!isset($items[$group])) {
                continue;
            }
            $i++;
            if ($i == 1) {
                $activeGroup = $group;
            }
            ?>
            <li <?= $i == 1 ? ' class="active"' : '' ?>><a href="javascript:;" data-toggle="tab-group-<?= $group ?>"><?= $name ?></a></li>
        <?php endforeach; ?>
    </ul>

    <?php if ($items): ?>
        <?php echo Html::beginForm(['index']); ?>
        <div class="panels">
            <div id="form-lookup" class="form">

                <?php
                foreach ($items as $group => $data):
                    ?>
                    <div class="tab-panel" id="tab-group-<?= $group ?>"<?= $activeGroup == $group ? '' : ' style="display: none"' ?>>
                        <?php if ($data) : ?>
                            <?php foreach ($data as $d) : ?>
                                <div class="form-group">
                                    <label><?= $d['label'] ?></label>
                                    <?php
                                    switch ($d['input_method']) {
                                        case \app\models\Lookup::INPUT_METHOD_TEXTAREA:
                                            if ($d['return_type'] == app\models\Lookup::RETURN_TYPE_STRING) {
                                                $input = Html::textarea($d['key'], unserialize($d['value']), ['class' => 'form-control']);
                                            } elseif ($d['return_type'] == app\models\Lookup::RETURN_TYPE_ARRAY) {
                                                echo Html::hiddenInput('inputValues[' . \yii\helpers\Inflector::camel2id($d['key']) . ']', 1);
                                                $input = Html::textarea($d['key'], $d['input_value'], ['class' => 'form-control']);
                                            } else {
                                                $input = null;
                                            }

                                            break;

                                        case \app\models\Lookup::INPUT_METHOD_CHECKBOX:
                                            $input = Html::checkbox($d['key'], unserialize($d['value']), ['uncheck' => 0]);
                                            break;

                                        case \app\models\Lookup::INPUT_METHOD_DROPDOWNLIST:
                                            $items = [];
                                            foreach (explode(PHP_EOL, $d['input_value']) as $key) {
                                                $v = explode(':', $key);
                                                if (count($v) == 2 && $v[0] != '' && $v[1] != '') {
                                                    $items[$v[0]] = $v[1];
                                                }
                                            }
                                            $input = Html::dropDownList($d['key'], unserialize($d['value']), $items, ['class' => 'form-control']);
                                            break;

                                        default:
                                            $input = Html::textInput($d['key'], unserialize($d['value']), ['class' => 'form-control']);
                                            break;
                                    }

                                    echo $input;
                                    if ($group == 'custom') {
                                        echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $d['id']], ['class' => 'btn btn-primary btn-lookup-update']);
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notice">暂无设置项目</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="form-group buttons">
                    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
                </div>

                <?= Html::endForm(); ?>
            </div>
        </div>
    <?php else : ?>
        <div class="notice">暂无设置项目</div>
    <?php endif; ?>
</div>