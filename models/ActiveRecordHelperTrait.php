<?php

namespace app\models;

use Yii;

trait ActiveRecordHelperTrait
{

    /**
     * `app\model\Post` To `app-model-Post`
     *
     * @param string $className
     * @return string
     */
    public static function className2Id($className = null)
    {
        if ($className === null) {
            $className = static::className();
        }

        return str_replace('\\', '-', $className);
    }

    /**
     * `app-model-Post` To `app\model\Post`
     *
     * @param string $id
     * @return string
     */
    public static function id2ClassName($id)
    {
        return str_replace('-', '\\', $id);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                if (Yii::$app->getUser()->isGuest) {
                    $this->updated_by = Yii::$app->getUser()->getId();
                }
            }

            return true;
        } else {
            return false;
        }
    }

}
