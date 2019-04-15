<?php

namespace app\modules\admin\modules\ticket\extensions;

use app\modules\admin\modules\ticket\models\Ticket;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\ticket\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \app\modules\admin\extensions\Formatter
{

    /**
     * 工单状态
     *
     * @param $value
     * @return string|null
     */
    public function asTicketStatus($value)
    {
        $options = Ticket::statusOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

}
