<?php

namespace app\modules\admin\controllers;

use app\helpers\App;
use app\helpers\Config;
use app\models\Member;
use app\models\MemberSearch;
use app\models\Meta;
use app\modules\admin\forms\ChangePasswordForm;
use app\modules\admin\forms\CreateMemberForm;
use app\modules\admin\forms\DynamicForm;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 会员管理
 * Class MembersController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MembersController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'change-password', 'statistics'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 会员列表数据
     *
     * @rbacDescription 会员列表数据查看权限
     * @param string $view
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionIndex($view = 'index')
    {
        $view = strtolower($view);
        if (!in_array($view, ['index', 'tree'])) {
            $view = 'index';
        }
        if ($view == 'index') {
            $searchModel = new MemberSearch();
            $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            $members = [];
            $rawMembers = Yii::$app->getDb()->createCommand('SELECT [[id]], [[parent_id]], [[username]], [[real_name]] FROM {{%member}} ORDER BY [[parent_id]] ASC')->queryAll();
            foreach ($rawMembers as $member) {
                $members[] = [
                    'id' => $member['id'],
                    'parent_id' => $member['parent_id'],
                    'name' => $member['username'] . " [ {$member['real_name']} ]",
                ];
            }
            $members = ArrayHelper::toTree($members, 'id');

            return $this->render('tree', [
                'members' => $members,
            ]);
        }
    }

    /**
     * 会员详情
     *
     * @rbacDescription 会员详情数据查看权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'metaItems' => Meta::getItems($model),
        ]);
    }

    /**
     * 会员数据添加
     *
     * @rbacDescription 会员数据添加权限
     * @return mixed
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new CreateMemberForm();
        $model->status = Member::STATUS_ACTIVE;
        $model->loadDefaultValues();
        $expiryMinutes = Config::get('member.register.expiryMinutes');
        if ((int) $expiryMinutes) {
            $model->expired_datetime = Yii::$app->getFormatter()->asDatetime(time() + $expiryMinutes * 60);
        }

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
     * 会员数据更新
     *
     * @rbacDescription 会员数据更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\ErrorException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $model->save(false);
            $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'dynamicModel' => $dynamicModel,
            ]);
        }
    }

    /**
     * 会员数据删除
     *
     * @rbacDescription 会员数据删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 修改密码
     *
     * @rbacDescription 会员密码修改权限
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword($id)
    {
        $member = $this->findModel($id);
        $model = new ChangePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $member->setPassword($model->password);
            if ($member->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "用户 {$member->username} 密码修改成功，请通知会员下次登录使用新的密码。");

                return $this->redirect(['index']);
            }
        }

        return $this->render('change-password', [
            'member' => $member,
            'model' => $model,
        ]);
    }

    /**
     * 统计
     *
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionStatistics()
    {
        return $this->render('statistics', [
            'accessToken' => App::getFakeMemberAccessToken(),
        ]);
    }

    /**
     * Finds the Member model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Member the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
