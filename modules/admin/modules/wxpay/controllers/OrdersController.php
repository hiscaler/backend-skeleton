<?php

namespace app\modules\admin\modules\wxpay\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wxpay\models\Order;
use app\modules\admin\modules\wxpay\models\OrderSearch;
use Exception;
use Overtrue\Wechat\Payment\Business;
use Overtrue\Wechat\Payment\QueryOrder;
use Overtrue\Wechat\Payment\Refund;
use Yii;
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
                        'actions' => ['index', 'view', 'update', 'delete', 'query', 'refund'],
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
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionQuery($id)
    {
        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : [];
        if (!isset($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key'])) {
            throw new InvalidConfigException('无效的微信公众号配置。');
        }

        $db = \Yii::$app->getDb();
        $outTradeNo = $db->createCommand('SELECT [[out_trade_no]] FROM {{%wx_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($outTradeNo) {
            try {
                $queryOrder = new QueryOrder($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key']);
                $response = $queryOrder->getTransaction($outTradeNo, true);
                if ($response !== false) {
                    $response = $response->toArray();
                    if ($response['trade_state'] == 'SUCCESS') {
                        $db->createCommand()->update('{{%wx_order}}', ['transaction_id' => $response['transaction_id'], 'status' => Order::STATUS_NOTIFIED], ['id' => (int) $id])->execute();
                    }

                    $this->layout = '@app/modules/admin/views/layouts/ajax';

                    return $this->render('query', [
                        'outTradeNo' => $outTradeNo,
                        'data' => $response,
                    ]);
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            throw new NotFoundHttpException('订单不存在。');
        }
    }

    public function actionRefund($id, $refundFee)
    {
        $options = isset(Yii::$app->params['wechat']) ? Yii::$app->params['wechat'] : [];
        if (!isset($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key'])) {
            throw new InvalidConfigException('无效的微信公众号配置。');
        }

        $db = \Yii::$app->getDb();
        $order = $db->createCommand('SELECT [[id]], [[total_fee]], [[sign]], [[sign_type]], [[out_trade_no]] FROM {{%wx_order}} WHERE [[id]] = :id', [':id' => (int) $id])->queryOne();
        if ($order) {
            try {
                $success = false;
                $errorMessage = null;
                $business = new Business($options['appid'], $options['secret'], $options['mch_id'], $options['mch_key']);
                // @see https://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate
                $business->setClientCert(Yii::getAlias('@webroot/certs/apiclient_cert.pem'));
                $business->setClientKey(Yii::getAlias('@webroot/certs/apiclient_key.pem'));

                $refund = new Refund($business);
                $outRefundNo = md5(uniqid(microtime()));
                $refundFee = $refundFee * 100;
                $refund->out_refund_no = $outRefundNo;
                $refund->total_fee = $order['total_fee'];
                $refund->refund_fee = $refundFee;
                $refund->out_trade_no = $order['out_trade_no'];
                $refund->refund_account = 'REFUND_SOURCE_RECHARGE_FUNDS';
                $response = $refund->getResponse();
                if ($response['return_code'] == 'SUCCESS') {
                    if ($response['result_code'] == 'SUCCESS') {
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
                        $db->createCommand()->insert('{{%wx_order_refund}}', $columns)->execute();
                        $success = true;
                    } else {
                        $errorMessage = $response['err_code'] . ': ' . $response['err_code_desc'];
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
