<?php

namespace app\modules\admin\modules\finance\extensions;

use app\modules\admin\modules\finance\models\Finance;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\finance\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \app\modules\admin\extensions\Formatter
{

    /**
     * 类型
     *
     * @param $value
     * @return string|null
     */
    public function asFinanceType($value)
    {
        $options = Finance::typeOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * 类型
     *
     * @param $value
     * @return string|null
     */
    public function asFinanceSource($value)
    {
        $options = Finance::sourceOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * 状态
     *
     * @param $value
     * @return string|null
     */
    public function asFinanceStatus($value)
    {
        $options = Finance::statusOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

}
