<?php

namespace app\controllers;

use app\models\Yad;
use Yii;

class Controller extends \yii\web\Controller
{

    public $tenantId;

    /**
     * 获取保存在 COOKIE 中的站点信息记录
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getI18nValue($key, $default = null)
    {
        $tenant = Yii::$app->getRequest()->getCookies()->get(Yii::$app->id . '_site');
        if ($tenant != null) {
            $value = $tenant->value;

            return isset($value[$key]) ? $value[$key] : $default;
        } else {
            return $default;
        }
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->tenantId = $this->getI18nValue('id', 1);

            $formatter = Yii::$app->getFormatter();
            $language = $this->getI18nValue('language', Yii::$app->getRequest()->getPreferredLanguage(array_keys(Yad::getLanguages())));

            if ($language) {
                Yii::$app->language = $language;
            }
            $timezone = $this->getI18nValue('timezone', Yii::$app->timeZone);
            if ($timezone) {
                Yii::$app->timeZone = $timezone;
            }

            $formatter->defaultTimeZone = Yii::$app->timeZone;
            $dateFormat = $this->getI18nValue('dateFormat', 'php:Y-m-d');
            if ($dateFormat) {
                $formatter->dateFormat = $dateFormat;
            }
            $timeFormat = $this->getI18nValue('timeFormat', 'php:H:i:s');
            if ($timeFormat) {
                $formatter->timeFormat = $timeFormat;
            }
            $datetimeFormat = $this->getI18nValue('datetimeFormat', 'php:Y-m-d H:i:s');
            if ($datetimeFormat) {
                $formatter->datetimeFormat = $datetimeFormat;
            }

            return true;
        }

        return false;
    }

}
