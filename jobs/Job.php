<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;

/**
 * Class Job
 *
 * @package app\jobs
 * @author hiscaler <hiscaler@gmail.com>
 */
class Job extends BaseObject
{

    public function debug($message)
    {
        Yii::debug($message, get_called_class());
    }

    public function error($message)
    {
        Yii::error($message, get_called_class());
    }

    public function warning($message)
    {
        Yii::warning($message, get_called_class());
    }

    public function info($message)
    {
        Yii::info($message, get_called_class());
    }

}