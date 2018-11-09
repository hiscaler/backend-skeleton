<?php

namespace app\modules\api\modules\signin\controllers;

use app\modules\api\extensions\AuthController;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * /api/slide/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends AuthController
{

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionSign()
    {
        $db = \Yii::$app->getDb();
        $memberId = \Yii::$app->getUser()->getId();
        $ymd = date('Ymd');
        $exists = $db->createCommand('SELECT COUNT(*) FROM {{%signin}} WHERE [[member_id]] = :memberId AND [[ymd]] = :ymd', [':memberId' => $memberId, ':ymd' => $ymd])->queryScalar();
        if ($exists) {
            throw new BadRequestHttpException('已签到。');
        }
        $creditConfigs = $db->createCommand('SELECT [[credits]], [[message]] FROM {{%signin_credit_config}}')->queryAll();
        if ($creditConfigs) {
            $creditConfig = $creditConfigs[array_rand($creditConfigs)];
        } else {
            $creditConfig = [
                'credits' => random_int(1, 10),
                'message' => null,
            ];
        }

        $transaction = $db->beginTransaction();
        try {
            $db->createCommand()->insert('{{%signin}}', [
                'member_id' => $memberId,
                'ymd' => $ymd,
                'signin_datetime' => time(),
                'credits' => $creditConfig['credits'],
                'ip_address' => Yii::$app->getRequest()->getRemoteIP(),
            ])->execute();
            if ($creditConfig['credits']) {
                $db->createCommand('UPDATE {{%member}} SET [[total_credits]] = [[total_credits]] + :n1, [[available_credits]] = [[available_credits]] + :n2 WHERE [[id]] = :memberId', [':n1' => $creditConfig['credits'], ':n2' => $creditConfig['credits'], ':memberId' => $memberId])->execute();
            }
            $transaction->commit();

            return $creditConfig;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}
