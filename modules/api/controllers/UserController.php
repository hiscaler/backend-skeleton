<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\forms\ChangeMyPasswordForm;
use app\modules\api\forms\UserRegisterForm;
use app\modules\api\models\Member;
use app\modules\api\models\User;
use InvalidArgumentException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * api/user/ 接口
 * Class UserController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserController extends ActiveController
{

    /**
     * @var string token 参数名称
     */
    private $_token_param = 'accessToken';

    public $modelClass = User::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if ($this->action->id == 'login') {
            unset($behaviors['authenticator']);
        }

        return $behaviors;
    }

    /**
     * 会员列表
     *
     * @deprecated
     * @param null $fields
     * @param null $username
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function _actionIndex($fields = null, $username = null, $page = 1, $pageSize = 20)
    {
        // Basic condition
        $condition = [];
        if ($username) {
            $condition = ['AND', $condition, ['t.username' => $username]];
        }
        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.username', 't.nickname', 't.avatar', 't.access_token', 't.email', 't.role', 't.register_ip', 't.login_count', 't.last_login_ip', 'last_login_time', 't.last_login_session', 't.status', 't.remark', 't.created_at', 't.updated_at', 'u.nickname AS editor'], $fields, []);
        $query = (new \yii\db\ActiveQuery(Member::class))
            ->alias('t')
            ->select($selectColumns);

        $query->offset($page)->limit($pageSize);

        $query->where($condition);

        // Order By
        $orderByColumns = [];
        if (!empty($orderBy)) {
            $orderByColumnLimit = ['id', 'username', 'createdAt', 'updatedAt']; // Supported order by column names
            foreach (explode(',', trim($orderBy)) as $string) {
                if (!empty($string)) {
                    $string = explode('.', $string);
                    if (in_array($string[0], $orderByColumnLimit)) {
                        $orderByColumns['t.' . Inflector::camel2id($string[0], '_')] = isset($string[1]) && $string[1] == 'asc' ? SORT_ASC : SORT_DESC;
                    }
                }
            }
        }

        $query->orderBy($orderByColumns ?: ['t.id' => SORT_DESC]);
        if ($this->debug) {
            Yii::debug($query->createCommand()->getRawSql(), 'API DEBUG');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 用户注册
     *
     * @return UserRegisterForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new UserRegisterForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate() && $model->setPassword($model->password) && $model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * 登录认证
     *
     * @return \yii\web\IdentityInterface|\yii\web\User
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function actionLogin()
    {
        $request = \Yii::$app->getRequest();

        $username = $request->post('username');
        $password = $request->post('password');
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException('无效的 username 或 password 参数。');
        }
        $user = User::findByUsername($username);

        if ($user === null) {
            throw new BadRequestHttpException('帐号错误');
        }

        if (!$user->validatePassword($password)) {
            throw new BadRequestHttpException('密码错误');
        }

        Yii::$app->getUser()->login($user, 0);
        User::afterLogin(null);

        return \Yii::$app->getUser()->getIdentity();
    }

    /**
     * 修改密码
     *
     * @return ChangeMyPasswordForm
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword()
    {
        $request = \Yii::$app->getRequest();
        $token = $request->get($this->_token_param);
        if ($token) {
            $member = User::findIdentityByAccessToken($token);
            if ($member) {
                $password = $request->post('password');
                $payload = [
                    'oldPassword' => $request->post('old_password'),
                    'password' => $password,
                    'confirmPassword' => $request->post('confirm_password'),
                ];
                $model = new ChangeMyPasswordForm();
                $model->load($payload, '');
                $member->setPassword($password);
                if ($model->validate() && $member->save(false) === false && !$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
                } else {
                    return $model;
                }
            } else {
                throw new BadRequestHttpException("用户验证失败。");
            }
        } else {
            throw new InvalidArgumentException("无效的 $this->_token_param 参数。");
        }
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = User::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $model;
    }

}