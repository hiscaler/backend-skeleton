<?php

namespace app\modules\admin\extensions;

use app\models\FileUploadConfig;
use app\models\Lookup;
use app\models\Member;
use app\models\Meta;
use app\models\Option;
use app\models\User;
use Yii;
use yii\helpers\Html;

/**
 * Class Formatter
 *
 * @package app\modules\admin\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \yii\i18n\Formatter
{

    public $nullDisplay = '';

    // Common
    public function asBoolean($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $imageUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin/images/';
        $booleanFormat = [
            $imageUrl . 'yes.png',
            $imageUrl . 'no.png'
        ];

        return Html::img($value ? $booleanFormat[0] : $booleanFormat[1]);
    }

    /**
     * 图片展示
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function asImage($value, $options = [])
    {
        return empty($value) ? $this->nullDisplay : Html::img(Yii::$app->getRequest()->getBaseUrl() . $value, $options);
    }

    /**
     * 性别
     *
     * @param $value
     * @return null|string
     */
    public function asSex($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Option::sexes();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * Get data status text view
     *
     * @param integer $value
     * @return mixed
     */
    public function asDataStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Option::status();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * Get model name text view
     *
     * @param integer $value
     * @return mixed
     */
    public function asModelName($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        if (($index = stripos($value, "app\models")) !== false) {
            $value = Yii::t('model', substr($value, 11));
        } else {
            list($moduleName, $modelName) = explode('\models\\', substr($value, 26));
            $value = Yii::t($moduleName, $modelName);
        }

        return $value;
    }


    // Meta

    /**
     * Meta 表名称
     *
     * @param $value
     * @return string
     * @throws \yii\base\NotSupportedException
     */
    public function asMetaTableName($value)
    {
        $tableName = $value;
        $models = Option::models(true);
        $value = isset($models[$value]) ? $models[$value] : null;

        return $this->asModelName($value) . " [ $tableName ]";
    }

    /**
     * 数据输入方式
     *
     * @param $value
     * @return string
     */
    public function asMetaInputType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Meta::inputTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * 返回数据类型
     *
     * @param $value
     * @return string
     */
    public function asMetaReturnValueType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Meta::returnValueTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // User
    public function asUserType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = User::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    public function asUserRole($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = User::roleOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // File upload config
    public function asFileUploadConfigType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = FileUploadConfig::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Lookup

    /**
     * 返回类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asLookupReturnType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Lookup::returnTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Member

    /**
     * 会员类型
     *
     * @param $value
     * @return string
     */
    public function asMemberType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Member::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * 会员状态
     *
     * @param integer $value
     * @return mixed
     */
    public function asMemberStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Member::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    public function asWeekDay($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Option::weekDays();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

}
