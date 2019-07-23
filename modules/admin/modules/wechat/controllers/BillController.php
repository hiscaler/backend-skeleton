<?php

namespace app\modules\admin\modules\wechat\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wechat\forms\BillDownload;
use EasyWeChat\Foundation\Application;
use yadjet\helpers\IsHelper;
use Yii;
use yii\base\InvalidConfigException;

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
     * @return string
     * @throws InvalidConfigException
     * @throws \yii\base\ExitException
     * @throws \yii\web\RangeNotSatisfiableHttpException
     * @todo 支持时间段选择合并处理
     *
     * @rbacDescription 微信支付对账单下载权限
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
            $contents = $payment->downloadBill($model->date, $model->type)->getContents();
            if ($contents && !IsHelper::xml($contents)) {
                Yii::$app->getResponse()->sendContentAsFile($contents, 'bill-' . $model->date . '.csv');
                Yii::$app->end();
            } else {
                $model->addError('date', '暂无对账数据。');
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

}