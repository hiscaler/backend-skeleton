<?php

namespace app\modules\admin\modules\wxpay\controllers;

use app\models\Lookup;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wxpay\models\Order;
use app\modules\admin\modules\wxpay\models\OrderSearch;
use DateTime;
use EasyWeChat\Foundation\Application;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update', 'delete', 'query', 'refund', 'refund-query'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'refund' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     *
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
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 微信商户平台订单查询
     *
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

        $db = \Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[out_trade_no]], [[trade_state]] FROM {{%wx_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
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
                        $db->createCommand()->update('{{%wx_order}}', $columns, ['id' => (int) $id])->execute();
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

        $db = \Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[id]], [[total_fee]], [[sign]], [[sign_type]], [[out_trade_no]] FROM {{%wx_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
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
                                'created_by' => \Yii::$app->getUser()->getId(),
                            ];
                            $db->createCommand()->insert('{{%wx_refund_order}}', $columns)->execute();
                            $db->createCommand()->update('{{%wx_order}}', ['trade_state' => Order::TRADE_STATE_REFUND], ['id' => $order['id']])->execute();
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
