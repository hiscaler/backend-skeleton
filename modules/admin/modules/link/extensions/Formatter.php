<?php

namespace app\modules\admin\modules\link\extensions;

use app\modules\admin\modules\link\models\Link;

class Formatter extends \yii\i18n\Formatter
{

    public $nullDisplay = '';

    /**
     * 任务状态
     *
     * @param integer $value
     * @return mixed
     */
    public function asType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Link::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

}
