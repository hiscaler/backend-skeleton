<?php

namespace app\modules\admin\controllers;

use app\models\Category;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidCallException;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 分类管理
 * Class CategoriesController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class CategoriesController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'toggle'],
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
     *
     * Lists all Category models.
     *
     * @rbacDescription 查看分类列表权限
     * @return mixed
     */
    public function actionIndex()
    {
        $rawData = (new Query())->from('{{%category}}')->all();
        if ($rawData) {
            $rawData = Category::sortItems(['children' => ArrayHelper::toTree($rawData, 'id')]);
            unset($rawData[0]);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $rawData,
            'key' => 'id',
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 新建分类权限
     * @return mixed
     */
    public function actionCreate($parentId = 0)
    {
        $model = new Category();
        $model->loadDefaultValues();
        if ($parentId) {
            $db = \Yii::$app->getDb();
            $parentId = $db->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $parentId])->queryScalar();
            if ($parentId) {
                $ordering = $db->createCommand('SELECT MAX([[ordering]]) FROM {{%category}} WHERE [[parent_id]] = :parentId', [':parentId' => (int) $parentId])->queryScalar();
                $model['parent_id'] = $parentId;
            } else {
                $ordering = null;
            }
            $model['ordering'] = $ordering != null ? $ordering + 1 : 10;
        }

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['create', 'parentId' => $parentId ?: $model->parent_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 更新分类权限
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
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 删除分类权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $children = Category::hasChildren($model['id']);
        if ($children) {
            throw new InvalidCallException('该分类有下级分类，禁止删除。');
        } else {
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * 激活禁止操作
     *
     * @rbacDescription 激活、禁止操作分类权限
     * @return Response
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $id = (int) Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $ids = [$id];
            if (!$value) {
                $ids = array_merge($ids, Category::getChildrenIds($id));
            }
            $db->createCommand()->update('{{%category}}', ['enabled' => $value, 'updated_at' => $now, 'updated_by' => Yii::$app->getUser()->getId()], ['id' => $ids])->execute();
            $responseData = [
                'success' => true,
                'data' => [
                    'ids' => $ids,
                    'value' => $value,
                    'updatedAt' => Yii::$app->getFormatter()->asDate($now),
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
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
