<?php

namespace app\modules\admin\forms;

use yii\base\DynamicModel;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;

/**
 * 动态表单
 *
 * @author hiscaler<hiscaler@gmail.com>
 */
class DynamicForm extends DynamicModel
{

    private $_metaItems = [];
    private $_attributeLabels = [];

    public function __construct($metaItems)
    {
        $this->_metaItems = $metaItems;
        $attributes = $attributeLabels = [];
        foreach ($this->_metaItems as $name => $meta) {
            $attributes[$name] = isset($meta['value']) ? $meta['value'] : null;
            $attributeLabels[$name] = isset($meta['i18n']) && $meta['i18n'] ? Yii::t('news', Inflector::camel2words(Inflector::id2camel($name, '_'))) : $meta['label'];

            // Add rule
            foreach ($meta as $key => $value) {
                if ($key == 'rules') {
                    foreach ($value as $rule => $ruleOptions) {
                        $this->addRule($name, $rule, $ruleOptions);
                    }
                }
            }
        }

        $this->_attributeLabels = $attributeLabels;
        parent::__construct($attributes, []);
    }

    public function getMetaOptions()
    {
        return $this->_metaItems;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->_attributeLabels;
    }

}
