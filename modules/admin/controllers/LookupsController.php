<?php

namespace app\modules\admin\controllers;

use app\helpers\Uploader;
use app\models\Constant;
use app\models\Lookup;
use app\models\LookupSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 基本设置管理
 * Class LookupsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LookupsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'form', 'create', 'update', 'delete', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
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
     * @rbacDescription 系统常规设定管理权限
     * @return mixed
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function actionForm()
    {
        $db = Yii::$app->getDb();
        if (Yii::$app->getRequest()->getIsPost()) {
            $postData = Yii::$app->getRequest()->post();
            $inputValues = isset($postData['inputValues']) ? $postData['inputValues'] : [];
            $updateCommand = $db->createCommand();
            $now = time();
            $userId = Yii::$app->getUser()->getId();
            foreach ($postData as $key => $value) {
                $file = UploadedFile::getInstanceByName($key);
                if ($file) {
                    $uploader = new Uploader();
                    $uploader->setFilename(null, $file->getExtension());
                    $originalValue = $value;
                    $value = $uploader->getUrl();
                    $file->saveAs($uploader->getPath());
                    $originalValue && FileHelper::unlink(Yii::getAlias('@webroot') . $originalValue);
                }

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

    /**
     * @rbacDescription 系统常规设定数据查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LookupSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Lookup model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 系统常规设定数据添加权限
     * @param string $group
     * @param string $type
     * @return string|Response
     */
    public function actionCreate($group = 'custom', $type = 'public')
    {
        $model = new Lookup();
        $model->loadDefaultValues();
        $model->return_type = Lookup::RETURN_TYPE_STRING;
        $model->enabled = Constant::BOOLEAN_TRUE;
        if (strtolower($type) == 'private') {
            $model->type = Lookup::TYPE_PRIVATE;
            $model->group = Lookup::GROUP_SYSTEM;
        } else {
            $model->type = Lookup::TYPE_PUBLIC;
            switch (strtolower($group)) {
                case 'system':
                    $group = Lookup::GROUP_SYSTEM;
                    break;

                case 'seo':
                    $group = Lookup::GROUP_SEO;
                    break;

                default:
                    $group = Lookup::GROUP_CUSTOM;
                    break;
            }
            $model->group = $group;
        }

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
     * @rbacDescription 系统常规设定数据更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * @rbacDescription 系统常规设定数据删除权限
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->group != Lookup::GROUP_CUSTOM) {
            throw new ForbiddenHttpException('非自定义设置项禁止删除。');
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * @rbacDescription 系统常规设定数据激活、禁止操作权限
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%lookup}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
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
