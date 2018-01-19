<?php

namespace app\modules\admin\modules\wxpay\extensions;

use app\modules\admin\modules\wxpay\models\Order;

class Formatter extends \yii\i18n\Formatter
{

    public $nullDisplay = '';

    /**
     * 任务状态
     *
     * @param integer $value
     * @return mixed
     */
    public function asOrderStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Order::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

}
