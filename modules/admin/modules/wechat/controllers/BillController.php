<?php

namespace app\modules\admin\modules\wechat\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wechat\forms\BillDownload;
use EasyWeChat\Foundation\Application;
use Yii;

/**
 * 对账单管理
 *
 * @package app\modules\admin\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class BillController extends BaseController
{

    /**
     * 对账单下载
     *
     * @rbacDescription 微信支付对账单下载权限
     * @return string
     * @throws \yii\base\ExitException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     */
    public function actionIndex()
    {
        if (!isset(Yii::$app->params['wechat']) || !is_array(Yii::$app->params['wechat'])) {
            throw new InvalidConfigException('无效的微信参数配置。');
        }

        $model = new BillDownload();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $application = new Application(Yii::$app->params['wechat']);
            $payment = $application->payment;
            $bill = $payment->downloadBill($model->date, $model->type)->getContents();
            Yii::$app->getResponse()->sendContentAsFile($bill, 'bill-' . $model->date . '.csv');
            Yii::$app->end();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

}