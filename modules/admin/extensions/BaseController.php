<?php

namespace app\modules\admin\extensions;

use app\models\Lookup;
use Yii;
use yii\web\Controller;

class BaseController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::$app->timeZone = Lookup::getValue('system.timezone', 'PRC');
            Yii::$app->language = Lookup::getValue('system.language', 'zh-CN');
            $formatter = Yii::$app->getFormatter();
            $formatter->datetimeFormat = Lookup::getValue('system.datetime-format', 'php:Y-m-d H:i:s');
            $formatter->dateFormat = Lookup::getValue('system.date-format', 'php:Y-m-d');
            $formatter->timeFormat = Lookup::getValue('system.time-format', 'php:H:i:s');

            return true;
        }

        return false;
    }
}