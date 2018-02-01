<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\Lookup;
use app\models\LookupSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 基本设置管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class LookupsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'form', 'create', 'update', 'delete', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Lookup models.
     *
     * @return mixed
     */
    public function actionForm()
    {
        $db = Yii::$app->getDb();
        if (Yii::$app->getRequest()->isPost) {
            $postData = Yii::$app->getRequest()->post();
            $inputValues = isset($postData['inputValues']) ? $postData['inputValues'] : [];
            $updateCommand = $db->createCommand();
            $now = time();
            $userId = Yii::$app->getUser()->getId();
            foreach ($postData as $key => $value) {
                if (substr($key, 0, 1) != '_') {
                    // label 值格式为 a.b-c
                    $key = str_replace('_', '.', $key);
                    $columns = [
                        'updated_by' => $userId,
                        'updated_at' => $now,
                    ];
                    if (isset($inputValues[$key])) {
                        $columns['input_value'] = $value;
                    } else {
                        $columns['value'] = serialize($value);
                    }
                    $updateCommand->update('{{%lookup}}', $columns, ['key' => $key, 'type' => Lookup::TYPE_PUBLIC])->execute();
                }
            }
            Lookup::refreshCache();
            Yii::$app->getSession()->setFlash('notice', '更新成功。');
        }

        $items = [];
        $rawItems = $db->createCommand('SELECT * FROM {{%lookup}} WHERE [[type]] = :type', [':type' => Lookup::TYPE_PUBLIC])->queryAll();
        foreach ($rawItems as $item) {
            $key = $item['group'];
            if (!isset($items[$key])) {
                $items[$key] = [];
            }
            $items[$key][] = $item;
        }

        return $this->render('form', [
            'items' => $items,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new LookupSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Lookup model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lookup();
        $model->loadDefaultValues();
        $model->return_type = Lookup::RETURN_TYPE_STRING;
        $model->enabled = Constant::BOOLEAN_TRUE;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Lookup model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Lookup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%lookup}} WHERE [[id]] = :id')->bindValues([
            ':id' => (int) $id,
        ])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $db->createCommand()->update('{{%lookup}}', ['enabled' => $value, 'updated_at' => time()], '[[id]] = :id', [':id' => (int) $id])->execute();
            $responseData = [
                'success' => true,
                'data' => [
                    'value' => $value,
                ],
            ];
        } else {
            $responseData = [
                'success' => false,
                'error' => [
                    'message' => '数据有误',
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseData,
        ]);
    }

    /**
     * Finds the Lookup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Lookup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Lookup::find()->where([
            'id' => (int) $id,
        ])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
