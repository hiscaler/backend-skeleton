<?php

namespace app\modules\admin\modules\rbac\controllers;

use stdClass;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class RolesController
 * 角色接口
 *
 * @package app\modules\admin\modules\rbac\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class RolesController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'save', 'delete', 'permissions-by-role', 'add-child', 'add-children', 'remove-child', 'remove-children'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add-child' => ['post'],
                    'create' => ['post'],
                    'delete' => ['post'],
                    'remove-child' => ['post'],
                    'remove-children' => ['post'],
                ],
            ],
        ];
    }

    /**
     *返回所有定义的角色
     *
     * @rbacIgnore true
     * @rbacDescription 所有角色获取权限
     * @return Response
     */
    public function actionIndex()
    {
        return array_values($this->auth->getRoles());
    }

    /**
     * 添加更新角色
     *
     * @rbacIgnore true
     * @rbacDescription 角色添加、更新权限
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSave()
    {
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            $success = true;
            $errorMessage = null;
            $insert = true;
            $name = trim($request->post('name'));
            if (empty($name)) {
                $success = false;
                $errorMessage = '角色名称不能为空。';
            } else {
                $role = $this->auth->getRole($name);
                if ($role) {
                    $role->description = trim($request->post('description'));
                    $this->auth->update($name, $role);
                    $insert = false;
                } else {
                    $role = $this->auth->createRole($name);
                    $role->description = trim($request->post('description'));
                    $this->auth->add($role);
                }
            }
            if ($success) {
                return [
                    'role' => (array) $role,
                    'insert' => $insert ? true : false,
                ];
            } else {
                throw new BadRequestHttpException($errorMessage);
            }
        }
    }

    /**
     * 删除角色
     *
     * @rbacIgnore true
     * @rbacDescription 角色删除权限
     * @param string $name
     * @return Response
     */
    public function actionDelete($name)
    {
        try {
            $role = $this->auth->getRole(trim($name));
            $this->auth->remove($role);
            $responseBody = [
                'success' => true,
                'data' => $role,
            ];
        } catch (\Exception $e) {
            $responseBody = [
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * 获取角色关联的权限
     *
     * @rbacIgnore true
     * @rbacDescription 角色关联的权限数据列表获取权限
     * @param string $roleName
     * @return Response
     */
    public function actionPermissionsByRole($roleName)
    {
        $permissions = array_values($this->auth->getPermissionsByRole($roleName));

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $permissions,
        ]);
    }

    /**
     * 添加角色和权限关联关系
     *
     * @rbacIgnore true
     * @rbacDescription 添加角色和权限关联关系权限
     * @param string $roleName
     * @param string $permissionName
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAddChild($roleName, $permissionName)
    {
        try {
            $role = $this->auth->getRole($roleName);
            $permission = $this->auth->getPermission($permissionName);
            $this->auth->addChild($role, $permission);

            return $role;
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 添加所有权限至指定的角色
     *
     * @rbacIgnore true
     * @rbacDescription 添加所有权限至指定的角色权限
     * @param $roleName
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAddChildren($roleName)
    {
        try {
            $role = $this->auth->getRole($roleName);
            if ($role) {
                $existPermissions = $this->auth->getPermissionsByRole($roleName);
                foreach ($this->auth->getPermissions() as $key => $permission) {
                    if (!isset($existPermissions[$key])) {
                        $this->auth->addChild($role, $permission);
                    }
                }

                return [];
            } else {
                throw new BadRequestHttpException("$roleName 不存在。");
            }
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 移除角色和权限关联关系
     *
     * @rbacIgnore true
     * @rbacDescription 移除角色和权限关联关系权限
     * @param string $roleName
     * @param string $permissionName
     * @return stdClass
     * @throws BadRequestHttpException
     */
    public function actionRemoveChild($roleName, $permissionName)
    {
        try {
            $this->auth->removeChild($this->auth->getRole($roleName), $this->auth->getPermission($permissionName));

            return new stdClass();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 删除角色关联的所有权限
     *
     * @rbacIgnore true
     * @rbacDescription 删除角色关联的所有权限
     * @param string $name
     * @return stdClass
     * @throws BadRequestHttpException
     */
    public function actionRemoveChildren($name)
    {
        try {
            $role = $this->auth->getRole(trim($name));
            $result = $this->auth->removeChildren($role);
            if (!$result) {
                throw new BadRequestHttpException('Unknown Error.');
            } else {
                return new stdClass();
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

}