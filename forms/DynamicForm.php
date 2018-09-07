<?php

namespace app\forms;

use yii\base\DynamicModel;
use yii\helpers\Inflector;

/**
 * 动态表单
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DynamicForm extends DynamicModel
{

    private $_metaOptions = [];
    private $_attributeLabels = [];

    public function __construct(array $metaOptions)
    {
        $this->_metaOptions = $metaOptions;
        $attributes = $attributeLabels = [];
        foreach ($this->_metaOptions as $name => $meta) {
            $attributes[$name] = isset($meta['value']) ? $meta['value'] : null;
            $label = null;
            if (isset($meta['i18n']) && $meta['i18n']) {
                $label = Yii::t('metaConfig', Inflector::camel2words(Inflector::id2camel($name, '_')));
            } elseif (isset($meta['label'])) {
                $label = $meta['label'];
            }
            $label && $attributeLabels[$name] = $label;

            // Add rule
            foreach ($meta as $key => $value) {
                if ($key == 'rules') {
                    foreach ($value as $rule => $ruleOptions) {
                        $this->addRule($name, $rule, $ruleOptions);
                    }
                }
            }
        }

        $attributeLabels && $this->_attributeLabels = $attributeLabels;
        parent::__construct($attributes, []);
    }

    public function getMetaOptions()
    {
        return $this->_metaOptions;
    }

    /**
     * 数据自定义字段表单数据
     *
     * @param $form
     */
    public function render($form)
    {
        foreach ($this->getMetaOptions() as $metaItem) {
            $inputType = $metaItem['input_type'];
            switch ($inputType) {
                case 'dropDownList':
                    echo $form->field($this, $metaItem['key'])->$inputType($metaItem['input_candidate_value'], ['value' => $metaItem['value'], 'prompt' => ''])->label($metaItem['label']);
                    break;

                default:
                    echo $form->field($this, $metaItem['key'])->$inputType(['value' => $metaItem['value']])->label($metaItem['label']);
                    break;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->_attributeLabels;
    }

}
