<?php

namespace app\modules\admin\controllers;

use app\models\Member;
use app\models\User;
use app\modules\admin\forms\ChangeMyPasswordForm;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * 帐号管理
 * Class AccountController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class AccountController extends Controller
{

    const PAGE_SIZE = 12;

    public $layout = 'account';

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
                        'actions' => ['index', 'change-password', 'login-logs'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 帐号资料
     *
     * @rbacIgnore true
     * @rbacDescription 当前登录用户资料查看权限
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $model = $this->findCurrentUserModel();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('notice', Yii::t('app', 'User profile save successfully.'));

            $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * 修改密码
     *
     * @rbacIgnore true
     * @rbacDescription 当前登录用户密码修改权限
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword()
    {
        $user = $this->findCurrentUserModel();
        $model = new ChangeMyPasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "您的密码修改成功，请下次登录使用新的密码。");

                return $this->refresh();
            }
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * 用户登录日志
     *
     * @rbacIgnore true
     * @rbacDescription 当前登录用户日志查看权限
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLoginLogs()
    {
        $items = [];
        $formatter = Yii::$app->getFormatter();
        $query = (new Query())
            ->from('{{%member_login_log}}')
            ->where(['member_id' => \Yii::$app->getUser()->getId()]);

        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => self::PAGE_SIZE,
        ]);
        $rawData = $query
            ->select(['ip', 'client_information', 'login_at'])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($rawData as $data) {
            $items[$formatter->asDate($data['login_at'])][] = $data;
        }

        return $this->render('login-logs', [
            'items' => $items,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @return User|Member|null
     * @throws NotFoundHttpException
     */
    public function findCurrentUserModel()
    {
        /* @var $class User|Member */
        $class = \Yii::$app->getUser()->identityClass;
        if (($model = $class::findOne(Yii::$app->getUser()->getId())) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
