<?php

namespace app\modules\admin\modules\wechat\controllers;

use app\models\Lookup;
use app\modules\admin\components\QueryConditionCache;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wechat\extensions\Formatter;
use app\modules\admin\modules\wechat\models\Order;
use app\modules\admin\modules\wechat\models\OrderSearch;
use DateTime;
use EasyWeChat\Foundation\Application;
use Exception;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 微信支付订单管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class OrdersController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'query', 'refund', 'refund-query', 'to-excel'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'refund' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 微信订单
     *
     * @rbacDescription 微信订单列表数据查看权限
     * @return mixed
     * @throws Exception
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     *
     * @rbacDescription 微信订单详情查看权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 微信订单更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 微信订单删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 微信商户平台订单查询
     *
     * @rbacDescription 微信商户平台订单查询权限
     * @param $id
     * @return string
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionQuery($id)
    {
        if (!isset(Yii::$app->params['wechat']) || !is_array(Yii::$app->params['wechat'])) {
            throw new InvalidConfigException('无效的微信参数配置。');
        }

        $db = Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[out_trade_no]], [[trade_state]] FROM {{%wechat_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
        if ($order) {
            try {
                $application = new Application(Yii::$app->params['wechat']);
                $payment = $application->payment;
                $paymentResponse = $payment->query($order['out_trade_no']);
                if ($paymentResponse !== false) {
                    $paymentResponse = $paymentResponse->toArray();
                    if ($paymentResponse['trade_state'] != $order['trade_state']) {
                        $columns = ['trade_state' => $paymentResponse['trade_state']];
                        isset($paymentResponse['trade_state_desc']) && $columns['trade_state_desc'] = $paymentResponse['trade_state_desc'];
                        isset($paymentResponse['transaction_id']) && $columns['transaction_id'] = $paymentResponse['transaction_id'];
                        isset($paymentResponse['time_end']) && $columns['time_end'] = (new DateTime($paymentResponse['time_end']))->getTimestamp();
                        $db->createCommand()->update('{{%wechat_order}}', $columns, ['id' => (int) $id])->execute();
                    }
                }

                $refundResponse = $payment->queryRefund($order['out_trade_no'])->toArray();

                $this->layout = '@app/modules/admin/views/layouts/ajax';

                return $this->render('query', [
                    'outTradeNo' => $order['out_trade_no'],
                    'payment' => $paymentResponse,
                    'refund' => $refundResponse,

                ]);
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            throw new NotFoundHttpException('订单不存在。');
        }
    }

    /**
     * 订单退款
     *
     * @rbacDescription 微信商户平台订单退款权限
     * @param $id
     * @param $refundFee
     * @return Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionRefund($id, $refundFee)
    {
        if (!isset(Yii::$app->params['wechat']) || !is_array(Yii::$app->params['wechat'])) {
            throw new InvalidConfigException('无效的微信参数配置。');
        }

        $db = Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[id]], [[total_fee]], [[sign]], [[sign_type]], [[out_trade_no]] FROM {{%wechat_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
        if ($order) {
            try {
                $success = false;
                $errorMessage = null;
                $outRefundNo = md5(uniqid(microtime()));
                $refundFee = $refundFee * 100;
                $webrootPath = Yii::getAlias('@webroot');
                $application = new Application(Yii::$app->params['wechat']);
                // @see https://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate
                $application['config']->set('payment.cert_path', $webrootPath . Lookup::getValue('custom.wxapp.cert.cert'));
                $application['config']->set('payment.key_path', $webrootPath . Lookup::getValue('custom.wxapp.cert.key'));
                $response = $application->payment->refund($order['out_trade_no'], $outRefundNo, $order['total_fee'], $refundFee, null, 'out_trade_no', 'REFUND_SOURCE_RECHARGE_FUNDS');
                if ($response['return_code'] == 'SUCCESS') {
                    if ($response['result_code'] == 'SUCCESS') {
                        $transaction = $db->getTransaction();
                        try {
                            $columns = [
                                'order_id' => $order['id'],
                                'appid' => $response['appid'],
                                'mch_id' => $response['mch_id'],
                                'nonce_str' => $response['nonce_str'],
                                'sign' => $order['sign'],
                                'sign_type' => $order['sign_type'],
                                'transaction_id' => $response['transaction_id'],
                                'out_trade_no' => $response['out_trade_no'],
                                'out_refund_no' => $outRefundNo,
                                'total_fee' => $response['total_fee'],
                                'refund_fee' => $refundFee,
                                'refund_account' => 'REFUND_SOURCE_RECHARGE_FUNDS',
                                'created_at' => time(),
                                'created_by' => Yii::$app->getUser()->getId(),
                            ];
                            $db->createCommand()->insert('{{%wechat_refund_order}}', $columns)->execute();
                            $db->createCommand()->update('{{%wechat_order}}', ['trade_state' => Order::TRADE_STATE_REFUND], ['id' => $order['id']])->execute();
                            $transaction->commit();
                            $success = true;
                        } catch (\Exception $ex) {
                            $transaction->rollBack();
                            $errorMessage = $ex->getMessage();
                        }
                    } else {
                        $errorMessage = $response['err_code'] . ': ' . $response['err_code_des'];
                    }
                } else {
                    $errorMessage = $response['return_msg'];
                }
            } catch (Exception $ex) {
                $errorMessage = $ex->getMessage();
            }

            $responseBody = ['success' => $success];
            if (!$success) {
                $responseBody['error']['message'] = $errorMessage;
            }

            return new Response([
                'format' => Response::FORMAT_JSON,
                'data' => $responseBody,
            ]);
        } else {
            throw new NotFoundHttpException('订单不存在。');
        }
    }

    /**
     * 微信商户平台订单退款查询
     *
     * @rbacDescription 微信商户平台订单退款查询权限
     * @param $id
     * @return string
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionRefundQuery($id)
    {
        if (!isset(Yii::$app->params['wechat']) || !is_array(Yii::$app->params['wechat'])) {
            throw new InvalidConfigException('无效的微信参数配置。');
        }

        $db = Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[out_refund_no]] FROM {{%wechat_refund_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
        if ($order) {
            try {
                $application = new Application(Yii::$app->params['wechat']);
                $response = $application->payment->queryRefundByRefundNo($order['out_refund_no']);
                if ($response !== false) {
                    $response = $response->toArray();
                    if ($response['return_code']) {
                        $columns = ['trade_state' => $response['trade_state']];
                        isset($response['trade_state_desc']) && $columns['trade_state_desc'] = $response['trade_state_desc'];
                        isset($response['transaction_id']) && $columns['transaction_id'] = $response['transaction_id'];
                        isset($response['time_end']) && $columns['time_end'] = (new DateTime($response['time_end']))->getTimestamp();
                        $db->createCommand()->update('{{%wechat_order}}', $columns, ['id' => (int) $id])->execute();
                    }

                    $this->layout = '@app/modules/admin/views/layouts/ajax';

                    return $this->render('query', [
                        'outTradeNo' => $order['out_trade_no'],
                        'data' => $response,
                    ]);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            throw new NotFoundHttpException('订单不存在。');
        }
    }

    /**
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @throws \yii\db\Exception
     * @throws \yii\base\ExitException
     * @throws InvalidConfigException
     */
    public function actionToExcel()
    {
        $q = QueryConditionCache::get(OrderSearch::QUERY_CONDITION_CACHE_KEY);
        if ($q instanceof Query) {
            $items = $q->all();
        } else {
            $items = Order::find()->all();
        }
        $phpExcel = new PHPExcel();
        $phpExcel->getProperties()->setCreator("Microsoft")
            ->setLastModifiedBy("Microsoft")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP 
classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("data");
        $phpExcel->setActiveSheetIndex(0);
        $activeSheet = $phpExcel->getActiveSheet();
        $phpExcel->getDefaultStyle()
            ->getFont()->setSize(14);
        $phpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $activeSheet->getDefaultRowDimension()->setRowHeight(25);

        $activeSheet->setCellValue('A1', '订单汇总')->mergeCells('A1:K1')->getStyle()->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $activeSheet
            ->setCellValue("A2", '序号')
            ->setCellValue("B2", '微信订单号')
            ->setCellValue("C2", '商户订单号')
            ->setCellValue("D2", '商品描述')
            ->setCellValue("E2", '商品详情')
            ->setCellValue("F2", '订单金额')
            ->setCellValue("G2", '订单生成时间')
            ->setCellValue("H2", '状态')
            ->setCellValue("I2", '交易状态')
            ->setCellValue("J2", '交易状态描述')
            ->setCellValue("K2", '付款人');

        $row = 3;
        $i = 0;
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();
        foreach ($items as $item) {
            $i++;
            $activeSheet->setCellValue("A{$row}", $i)
                ->setCellValue("B{$row}", $item['transaction_id'])
                ->setCellValue("C{$row}", $item['out_trade_no'])
                ->setCellValue("D{$row}", $item['body'])
                ->setCellValue("E{$row}", $item['detail'])
                ->setCellValue("F{$row}", $formatter->asYuan($item['total_fee']))
                ->setCellValue("G{$row}", $formatter->asDatetime($item['time_start']))
                ->setCellValue("H{$row}", $formatter->asOrderStatus($item['status']))
                ->setCellValue("I{$row}", $item['trade_state'])
                ->setCellValue("J{$row}", $item['trade_state_desc'])
                ->setCellValue("K{$row}", $item['member']['username']);
            $row++;
        }
        $phpExcel->getActiveSheet()->setTitle('订单');
        $phpExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . 'abc' . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $objWriter->save('php://output', 'w');
        Yii::$app->end();
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
