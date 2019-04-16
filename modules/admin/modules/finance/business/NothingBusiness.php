<?php

namespace app\modules\admin\modules\finance\business;

use app\modules\admin\modules\finance\models\Finance;

/**
 * 演示代码，不进行任何业务处理
 *
 * @package app\modules\admin\modules\finance\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class NothingBusiness implements BusinessInterface
{

    public function process(bool $insert, array $changedAttributes, Finance $finance)
    {
        // Nothing
    }

}