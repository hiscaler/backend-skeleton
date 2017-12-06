<?php

namespace app\modules\admin\forms;

use yii\base\DynamicModel;

/**
 * 动态表单
 *
 * @author hiscaler<hiscaler@gmail.com>
 */
class DynamicForm extends DynamicModel
{

    public $dynamicAttributeLabels = [];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->dynamicAttributeLabels;
    }

}
