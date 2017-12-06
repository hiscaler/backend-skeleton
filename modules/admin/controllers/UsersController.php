<?php

namespace app\modules\admin\controllers;

use app\models\User;
use app\models\UserSearch;
use app\models\Yad;
use app\modules\admin\forms\ChangePasswordForm;
use app\modules\admin\forms\RegisterForm;
use PDO;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\base\Security;
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
                        'actions' => ['index', 'create', 'update', 'delete', 'change-password', 'auth'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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
        $model->type = User::TYPE_USER;
        $model->status = User::STATUS_ACTIVE;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->password_hash = (new Security())->generatePasswordHash($model->password);
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

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

    /**
     * 修改密码
     *
     * @return mixed
     */
    public function actionChangePassword($id)
    {
        $user = $this->findModel($id);
        $model = new ChangePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
//                Yii::$app->getDb()->createCommand('UPDATE {{%user}} SET [[last_change_password_time]] = :now WHERE [[id]] = :id', [':now' => time(), ':id' => $user->id])->execute();
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
