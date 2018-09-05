<?php

namespace app\modules\admin\controllers;

use app\models\Meta;
use app\models\User;
use app\models\UserSearch;
use app\modules\admin\forms\ChangePasswordForm;
use app\modules\admin\forms\CreateUserForm;
use app\modules\admin\forms\DynamicForm;
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
 * Class UsersController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UsersController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'change-password', 'auth', 'toggle'],
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
     * Lists all User models.
     *
     * @rbacDescription 系统用户列表数据查看权限
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);
        $hasCategoryData = \Yii::$app->getDb()->createCommand('SELECT COUNT(*) FROM {{%category}}')->queryScalar();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'hasCategoryData' => $hasCategoryData
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 系统用户添加权限
     * @return mixed
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $model = new CreateUserForm();
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

    /**
     * 系统用户更新
     *
     * @rbacDescription 系统用户更新权限
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     */
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
     * @rbacDescription 系统用户删除权限
     * @param integer $id
     * @return mixed
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        if ((int) $id == Yii::$app->getUser()->getId()) {
            throw new BadRequestHttpException("Can't remove itself.");
        }

        $model = $this->findModel($id);
        $userId = $model->id;
        $db = Yii::$app->getDb();
        $db->transaction(function ($db) use ($userId, $model) {
            $model->delete();
            /* @var $db \yii\db\Connection */
            $db->createCommand()->delete('{{%user_auth_category}}', ['user_id' => $userId])->execute();
        });

        return $this->redirect(['index']);
    }

    /**
     * 修改密码
     *
     * @rbacDescription 系统用户密码修改权限
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
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
     * @rbacDescription 设置系统用户可管理分类数据权限
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
        if ($request->getIsAjax()) {
            if ($request->getIsPost()) {
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

            $categories = $db->createCommand('SELECT [[id]], [[parent_id]] AS [[pId]], [[name]] FROM {{%category}} ORDER BY [[ordering]] ASC')->queryAll();
            if ($existingCategoryIds) {
                foreach ($categories as $key => $node) {
                    if (in_array($node['id'], $existingCategoryIds)) {
                        $categories[$key]['checked'] = true;
                    }
                }
            }
            $categories = ArrayHelper::toTree($categories, 'id', 'pId');

            return $this->renderAjax('auth', [
                'categories' => $categories,
            ]);
        } else {
            throw new InvalidCallException('无效的访问方式。');
        }
    }

    /**
     * 激活、锁定操作
     *
     * @rbacDescription 设置系统用户记录、锁定状态操作权限
     * @return Response
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $success = false;
        $errorMessage = null;
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[status]] FROM {{%user}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== false) {
            $value = !$value;
            $continue = true;
            if ($value == User::STATUS_LOCKED) {
                $c = $db->createCommand("SELECT COUNT(*) FROM {{%user}} WHERE [[status]] = :status", [':status' => User::STATUS_ACTIVE])->queryScalar();
                if ($c == 1) {
                    $continue = false;
                    // @todo 应该保留有一个拥有最高权限的用户
                    $errorMessage = '您至少应该保留一个可登录用户。';
                }
            }
            if ($continue) {
                $now = time();
                $db->createCommand()->update('{{%user}}', ['status' => $value, 'updated_at' => $now, 'updated_by' => Yii::$app->getUser()->getId()], '[[id]] = :id', [':id' => (int) $id])->execute();
                $success = true;
                $body = [
                    'value' => $value,
                    'updatedAt' => Yii::$app->getFormatter()->asDate($now),
                    'updatedBy' => Yii::$app->getUser()->getIdentity()->getUsername(),
                ];
            }
        } else {
            $errorMessage = '用户不存在。';
        }

        $responseBody = ['success' => $success];
        if ($success) {
            if (isset($body)) {
                $responseBody['data'] = $body;
            }
        } else {
            $responseBody['error']['message'] = $errorMessage;
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
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
