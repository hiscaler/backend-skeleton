<?php

namespace app\modules\admin\controllers;

use app\models\Meta;
use app\models\User;
use app\models\UserSearch;
use app\modules\admin\forms\ChangePasswordForm;
use app\modules\admin\forms\DynamicForm;
use app\modules\admin\forms\RegisterForm;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\base\InvalidCallException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 系统用户管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class UsersController extends GlobalController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'change-password', 'auth', 'toggle'],
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
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RegisterForm();
        $model->status = User::STATUS_ACTIVE;
        $model->loadDefaultValues();

        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $model->setPassword($model->password);
            if ($model->save()) {
                $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $model->save(false);
            $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dynamicModel' => $dynamicModel,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ((int) $id == Yii::$app->getUser()->getId()) {
            throw new BadRequestHttpException("Can't remove itself.");
        }

        $model = $this->findModel($id);
        $userId = $model->id;
        $db = Yii::$app->getDb();
        $db->transaction(function ($db) use ($userId) {
            $bindValues = [
                ':userId' => $userId
            ];
            $db->createCommand('DELETE FROM {{%user_auth_category}} WHERE [[user_id]] = :userId AND [[category_id]] IN (SELECT [[id]] FROM {{%category}})')->bindValues($bindValues)->execute();
        });

        return $this->redirect(['index']);
    }

    /**修改密码
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionChangePassword($id)
    {
        $user = $this->findModel($id);
        $model = new ChangePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "用户 {$user->username} 密码修改成功，请通知用户下次登录使用新的密码。");

                return $this->redirect(['index']);
            }
        }

        return $this->render('change-password', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    /**
     * 设置用户可管理分类数据
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionAuth($id)
    {
        $userId = (int) $id;
        $db = Yii::$app->getDb();
        $userExists = $db->createCommand('SELECT COUNT(*) FROM {{%user}} WHERE [[id]] = :id', [':id' => $userId])->queryScalar();
        if (!$userExists) {
            throw new NotFoundHttpException('用户不存在。');
        }
        $existingCategoryIds = $db->createCommand('SELECT [[category_id]] FROM {{%user_auth_category}} WHERE [[user_id]] = :userId', [':userId' => $userId])->queryColumn();
        $request = Yii::$app->getRequest();
        if ($request->isAjax) {
            if ($request->isPost) {
                $choiceCategoryIds = $request->post('choiceCategoryIds');
                if (!empty($choiceCategoryIds)) {
                    $choiceCategoryIds = explode(',', $choiceCategoryIds);
                    $insertCategoryIds = array_diff($choiceCategoryIds, $existingCategoryIds);
                    $deleteCategoryIds = array_diff($existingCategoryIds, $choiceCategoryIds);
                } else {
                    $insertCategoryIds = [];
                    $deleteCategoryIds = $existingCategoryIds; // 如果没有选择任何分类，表示删除所有已经存在分类
                }

                if ($insertCategoryIds || $deleteCategoryIds) {
                    $transaction = $db->beginTransaction();
                    try {
                        if ($insertCategoryIds) {
                            $insertRows = [];
                            foreach ($insertCategoryIds as $nodeId) {
                                $insertRows[] = [$userId, $nodeId];
                            }
                            if ($insertRows) {
                                $db->createCommand()->batchInsert('{{%user_auth_category}}', ['user_id', 'category_id'], $insertRows)->execute();
                            }
                        }
                        if ($deleteCategoryIds) {
                            $db->createCommand()->delete('{{%user_auth_category}}', [
                                'user_id' => $userId,
                                'category_id' => $deleteCategoryIds
                            ])->execute();
                        }
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();

                        return new Response([
                            'format' => Response::FORMAT_JSON,
                            'data' => [
                                'success' => false,
                                'error' => [
                                    'message' => $e->getMessage()
                                ]
                            ],
                        ]);
                    }
                }

                return new Response([
                    'format' => Response::FORMAT_JSON,
                    'data' => [
                        'success' => true
                    ],
                ]);
            }

            $nodes = $db->createCommand('SELECT [[id]], [[parent_id]] AS [[pId]], [[name]] FROM {{%category}} ORDER BY [[ordering]] ASC')->queryAll();
            if ($existingCategoryIds) {
                foreach ($nodes as $key => $node) {
                    if (in_array($node['id'], $existingCategoryIds)) {
                        $nodes[$key]['checked'] = true;
                    }
                }
            }
            $nodes = ArrayHelper::toTree($nodes, 'id', 'pId');

            return $this->renderAjax('auth', [
                'categories' => $nodes,
            ]);
        } else {
            throw new InvalidCallException('无效的访问方式。');
        }
    }

    /**
     * 激活禁止操作
     *
     * @return Response
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[status]] FROM {{%user}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== false) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%user}}', ['status' => $value, 'updated_at' => $now, 'updated_by' => Yii::$app->getUser()->getId()], '[[id]] = :id', [':id' => (int) $id])->execute();
            $responseData = [
                'success' => true,
                'data' => [
                    'value' => $value,
                    'updatedAt' => Yii::$app->getFormatter()->asDate($now),
                    'updatedBy' => Yii::$app->getUser()->getIdentity()->username,
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = User::find()->where(['id' => (int) $id])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
