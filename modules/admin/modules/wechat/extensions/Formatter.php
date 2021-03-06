<?php

namespace app\modules\admin\modules\wechat\extensions;

use app\modules\admin\modules\wechat\models\Order;

class Formatter extends \app\modules\admin\extensions\Formatter
{

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
