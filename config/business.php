<?php

return [
    // finance 模块
    'finance' => [
        'business' => [
            /**
             * 财务付款后的业务处理类
             *
             * 如果您不需要做任何处理，您可以将默认的 \app\modules\admin\modules\finance\business\CreditBusiness::class 替换为 \app\modules\admin\modules\finance\business\NothingBusiness::class 即可
             *
             * 如果您需要进行其他的业务处理，您也可以创建自有类，实现 \app\modules\admin\modules\finance\business\BusinessInterface 的 process() 方法，在其中实现自有的业务逻辑处理。
             */
            'class' => \app\modules\admin\modules\finance\business\CreditBusiness::class,
            'exchangeRate' => '100:1', // 入账金额和积分兑换比例设置（几分钱:几个积分）
        ],
    ]
];
