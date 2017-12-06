<?php

namespace app\modules\admin\components;

use app\models\Meta;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class DynamicMetaModel
{

    public static function make($metas, $isNewRecord = true)
    {
        if (is_array($metas)) {
            $dynamicModel = new DynamicModel($isNewRecord ? array_keys($metas) : ArrayHelper::map($metas, 'key', 'value'));
            foreach ($metas as $name => $meta) {
                if ($isNewRecord) {
                    $dynamicModel->$name = $meta['input_type'] != 'checkboxList' ? $meta['default_value'] : explode(',', $meta['default_value']);
                }
                foreach (Meta::getMetaRules($meta['id']) as $key => $options) {
                    $dynamicModel->addRule($name, $key, $options);
                }
            }

            return $dynamicModel;
        } else {
            throw new InvalidConfigException('Invalid meta config data.');
        }
    }

}
