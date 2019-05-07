<?php

namespace app\modules\api\models;

use Yii;

/**
 * AR with 动态处理
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
trait ActiveRecordWithingTrait
{

    /**
     * @param $ar
     * @return yii\db\ActiveRecord
     */
    public function addWiths($ar)
    {
        /* @var $ar yii\db\ActiveRecord */
        $expandParam = isset(Yii::$app->controller->serializer['expandParam']) ? Yii::$app->controller->serializer['expandParam'] : 'expand';
        $expandValues = Yii::$app->getRequest()->get($expandParam);
        if ($expandValues) {
            $extraFields = $this->extraFields();
            foreach (explode(',', $expandValues) as $value) {
                $value = trim($value);
                $relationName = null;
                if (in_array($value, $extraFields)) {
                    $relationName = $value;
                } elseif (isset($extraFields[$value])) {
                    $relationName = $extraFields[$value];
                }
                if ($relationName) {
                    /* @var $ar yii\db\ActiveQueryTrait */
                    $ar->with($relationName);
                }
            }
        }

        return $ar;
    }

}
