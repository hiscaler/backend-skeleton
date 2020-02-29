<?php

namespace app\modules\admin\modules\rbac\controllers;

use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class PermissionsController
 * 权限接口
 *
 * @package app\modules\admin\modules\rbac\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PermissionsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 返回定义的所有权限
     *
     * @rbacIgnore true
     * @rbacDescription 所有定义的权限返回
     * @return Response
     */
    public function actionIndex()
    {
        return array_values($this->auth->getPermissions());
    }

    /**
     * 添加权限
     *
     * @rbacIgnore true
     * @rbacDescription 添加权限
     * @return Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            $rawBody = $request->getRawBody();
            $rawBody = json_decode($rawBody, true);
            if ($rawBody !== null) {
                // is post json value
                $name = isset($rawBody['name']) ? $rawBody['name'] : null;
                $description = isset($rawBody['description']) ? $rawBody['description'] : null;
            } else {
                $name = trim($request->post('name'));
                $description = trim($request->post('description'));
            }
            if (empty($name)) {
                throw new BadRequestHttpException('称不能为空。');
            } else {
                $permission = $this->auth->getPermission($name);
                if ($permission) {
                    $permission->description = $description;
                    $this->auth->update($name, $permission);
                } else {
                    $permission = $this->auth->createPermission($name);
                    $permission->description = $description;
                    $this->auth->add($permission);
                }

                return $permission;
            }
        }
    }

    /**
     * 删除权限
     *
     * @rbacIgnore true
     * @rbacDescription 删除权限
     * @param string $name
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionDelete($name)
    {
        try {
            $name = trim($name);
            $permission = $this->auth->getPermission($name);
            $this->auth->remove($permission);

            return $permission;
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

}