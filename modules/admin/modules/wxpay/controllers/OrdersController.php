<?php

namespace app\modules\admin\modules\wxpay\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\wxpay\models\Order;
use app\modules\admin\modules\wxpay\models\OrderSearch;
use Exception;
use Overtrue\Wechat\Payment\QueryOrder;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

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
                        'actions' => ['index', 'view', 'update', 'delete', 'query'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
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
