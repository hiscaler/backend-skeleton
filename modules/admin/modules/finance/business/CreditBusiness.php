<?php

namespace app\modules\admin\modules\finance\business;

use app\models\MemberCreditLog;
use app\modules\admin\modules\finance\models\Finance;
use Yii;

/**
 * 积分处理
 *
 * @package app\modules\admin\modules\finance\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class CreditBusiness implements BusinessInterface
{

    /**
     * @param Finance $finance
     * @param array $changedAttributes
     * @throws \Exception
     */
    public function process(bool $insert, array $changedAttributes, Finance $finance)
    {
        if ($insert) {
            if (Yii::$app->params['module']['finance']['business']['exchangeRate']) {
                $rate = Yii::$app->params['module']['finance']['business']['exchangeRate'];
                if (stripos($rate, ':') === false) {
                    $rate = '1:1';
                }
            } else {
                $rate = '1:1';
            }
            list($m, $c) = explode(':', $rate);
            $credits = $finance->money * round($c / $m, 2);
            $v = \app\models\MemberCreditLog::add($finance->member_id, MemberCreditLog::OPERATION_FINANCE_RECHARGE_CONVERSION, $credits, $finance->id);
            if ($v !== false) {
                \Yii::$app->getDb()->createCommand()->update('{{%finance}}', ['related_key' => $v], ['id' => $finance->id])->execute();

                // 添加资金出账记录
                $financeLog = new Finance();
                $financeLog->call_business_process = false;
                $financeLog->type = Finance::TYPE_DISBURSE;
                $financeLog->money = abs($finance->money);
                $financeLog->source = Finance::SOURCE_OTHER;
                $financeLog->member_id = $finance->member_id;
                $financeLog->status = Finance::STATUS_VALID;
                $financeLog->save();
            }
        }
    }

}