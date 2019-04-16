<?php

namespace app\modules\admin\modules\finance\business;

use app\modules\admin\modules\finance\models\Finance;

/**
 * 业务处理接口类
 *
 * @package app\modules\admin\modules\finance\business
 * @author hiscaler <hiscaler@gmail.com>
 */
interface BusinessInterface
{

    /**
     * 业务逻辑处理代码
     * 添加或者更新财务数据后，您可以在此处处理相关业务逻辑
     *
     * @param bool $insert 是否为新增记录
     * @param array $changedAttributes 更新数据发生变化的属性
     * @param Finance $finance 更新的实例
     */
    public function process(bool $insert, array $changedAttributes, Finance $finance);

}