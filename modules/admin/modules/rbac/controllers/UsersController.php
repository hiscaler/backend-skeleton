<?php

namespace app\modules\admin\modules\rbac\controllers;

use stdClass;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\rbac\Item;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class UsersController
 * 用户接口
 *
 * @package app\modules\admin\modules\rbac\controllers
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
                        'actions' => ['index', 'roles', 'permissions', 'auths', 'assign', 'revoke'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 获取所有用户
     *
     * @rbacIgnore true
     * @rbacDescription 获取所有用户权限
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $userTable = $this->getModuleOptions()['userTable'];
        $rawColumns = $userTable['columns'];
        $columns = [
            $rawColumns['id'],
            $rawColumns['username'],
        ];
        $extras = [];
        foreach ($rawColumns['extra'] as $name => $text) {
            $columns[] = $name;
            $extras[$name] = $text ?: $name;
        }
        $extras['roles'] = Yii::t('rbac', 'Role');
        $authAssignments = [];
        $rawAuthAssignments = \Yii::$app->getDb()->createCommand("SELECT [[item_name]], [[user_id]] FROM {$this->auth->assignmentTable}")->queryAll();
        foreach ($rawAuthAssignments as $authAssignment) {
            if (!isset($authAssignments[$authAssignment['user_id']])) {
                $authAssignments[$authAssignment['user_id']] = [];
            }
            $authAssignments[$authAssignment['user_id']][] = $authAssignment['item_name'];
        }
        $items = (new Query())
            ->select($columns)
            ->from($userTable['name'])
            ->where(is_array($userTable['where']) ? $userTable['where'] : [])
            ->all($this->auth->db);
        foreach ($items as $key => &$item) {
            $item['roles'] = [];
            $t = $item;
            if ($rawColumns['id'] != 'id') {
                $item['id'] = $item[$rawColumns['id']];
                unset($item[$rawColumns['id']]);
            }
            if ($rawColumns['username'] != 'username') {
                $t['username'] = $item[$rawColumns['username']];
                unset($item[$rawColumns['username']]);
            }
            if (isset($authAssignments[$item['id']])) {
                $item['roles'] = $authAssignments[$item['id']];
            }
        }

        return [
            'items' => $items,
            'extras' => $extras,
        ];
    }

    /**
     * 用户分配的角色
     *
     * @rbacIgnore true
     * @rbacDescription 获取用户分配的角色权限
     * @param integer|mixed $id 用户 id
     * @return Response
     */
    public function actionRoles($id = null)
    {
        if (!$id) {
            $id = Yii::$app->getUser()->getId();
        }

        return array_values($this->auth->getRolesByUser($id));
    }

    /**
     * 用户分配的权限
     *
     * @rbacIgnore true
     * @rbacDescription 获取用户分配的权限
     * @param integer|mixed $id
     * @return Response
     */
    public function actionPermissions($id = null)
    {
        if (!$id) {
            $id = Yii::$app->getUser()->getId();
        }

        return $this->auth->getPermissionsByUser($id);
    }

    /**
     * 用户授权列表
     *
     * @rbacIgnore true
     * @rbacDescription 获取用户授权列表权限
     * @param null $id
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionAuths($id = null)
    {
        $items = [];
        if (!$id) {
            $user = Yii::$app->getUser();
            $id = $user->getIsGuest() ? 0 : $user->getId();
        }
        if ($id) {
            $ownRoles = $this->auth->getRolesByUser($id);
            $ownPermissions = $this->auth->getPermissionsByUser($id);
            $itemCommand = $this->auth->db->createCommand('SELECT * FROM ' . $this->auth->itemTable . ' WHERE [[type]] = :type');
            $roles = $itemCommand->bindValue(':type', Item::TYPE_ROLE)->queryAll();
            $permissions = $itemCommand->bindValue(':type', Item::TYPE_PERMISSION)->queryAll();
            foreach ($roles as $key => $role) {
                $roles[$key]['own'] = isset($ownRoles[$role['name']]);
            }
            foreach ($permissions as $key => $permission) {
                $permissions[$key]['own'] = isset($ownPermissions[$permission['name']]);
            }
            $items = [
                'userId' => $id,
                'roles' => $roles,
                'permissions' => $permissions,
            ];
        }

        return $items;
    }

    /**
     * 分配用户角色
     *
     * @rbacIgnore true
     * @rbacDescription 分配用户角色权限
     * @return Response
     * @throws \Exception
     */
    public function actionAssign()
    {
        $success = true;
        $errorMessage = null;
        $rawBody = Yii::$app->getRequest()->getRawBody();
        $rawBody = json_decode($rawBody, true);
        if ($rawBody !== null) {
            $roleName = isset($rawBody['roleName']) ? $rawBody['roleName'] : null;
            $userId = isset($rawBody['userId']) ? $rawBody['userId'] : null;
            $this->auth->assign($this->auth->getRole($roleName), $userId);
        } else {
            $success = false;
            $errorMessage = 'Parameters error.';
        }

        if ($success) {
            return new stdClass();
        } else {
            throw new BadRequestHttpException($errorMessage);
        }
    }

    /**
     * 撤销用户角色
     *
     * @rbacIgnore true
     * @rbacDescription 撤销用户角色权限
     * @return stdClass
     * @throws BadRequestHttpException
     */
    public function actionRevoke()
    {
        $success = true;
        $errorMessage = null;
        $rawBody = Yii::$app->getRequest()->getRawBody();
        $rawBody = json_decode($rawBody, true);
        if ($rawBody !== null) {
            $roleName = isset($rawBody['roleName']) ? $rawBody['roleName'] : null;
            $userId = isset($rawBody['userId']) ? $rawBody['userId'] : null;
            $this->auth->revoke($this->auth->getRole($roleName), $userId);
        } else {
            $success = false;
            $errorMessage = 'Parameters error.';
        }
        if ($success) {
            return new stdClass();
        } else {
            throw new BadRequestHttpException($errorMessage);
        }
    }

}