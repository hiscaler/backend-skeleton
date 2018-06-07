<?php

namespace app\modules\admin\modules\rbac\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\rbac\Item;
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
     * @rbacDescription 获取所有用户权限
     * @return Response
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
        $items = (new \yii\db\Query())
            ->select($columns)
            ->from($userTable['name'])
            ->where(is_array($userTable['where']) ? $userTable['where'] : [])
            ->all($this->auth->db);
        if ($rawColumns['id'] != 'id' || $rawColumns['username'] != 'username') {
            foreach ($items as $key => $item) {
                $t = $item;
                if ($rawColumns['id'] != 'id') {
                    $t['id'] = $item[$rawColumns['id']];
                    unset($t[$rawColumns['id']]);
                }
                if ($rawColumns['username'] != 'username') {
                    $t['username'] = $item[$rawColumns['username']];
                    unset($t[$rawColumns['username']]);
                }
                $items[$key] = $t;
            }
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => [
                'items' => $items,
                'extras' => $extras,
            ],
        ]);
    }

    /**
     * 用户分配的角色
     *
     * @rbacDescription 获取用户分配的角色权限
     * @param integer|mixed $id 用户 id
     * @return Response
     */
    public function actionRoles($id = null)
    {
        if (!$id) {
            $id = Yii::$app->getUser()->getId();
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => array_values($this->auth->getRolesByUser($id)),
        ]);
    }

    /**
     * 用户分配的权限
     *
     * @rbacDescription 获取用户分配的权限
     * @param integer|mixed $id
     * @return Response
     */
    public function actionPermissions($id = null)
    {
        if (!$id) {
            $id = Yii::$app->getUser()->getId();
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $this->auth->getPermissionsByUser($id),
        ]);
    }

    /**
     * 用户授权列表
     *
     * @rbacDescription 获取用户授权列表权限
     * @param null $id
     * @return Response
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

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $items,
        ]);
    }

    /**
     * 分配用户角色
     *
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
        $responseBody = ['success' => $success];
        if (!$success) {
            $responseBody['error']['message'] = $errorMessage;
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * 撤销用户角色
     *
     * @rbacDescription 撤销用户角色权限
     * @return Response
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
        $responseBody = ['success' => $success];
        if (!$success) {
            $responseBody['error']['message'] = $errorMessage;
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

}