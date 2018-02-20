<?php
/**
 * Created by PhpStorm.
 * User: hiscaler
 * Date: 2018-02-20
 * Time: 11:14
 */

namespace app\modules\admin\modules\rbac\controllers;

use Exception;
use Yii;
use yii\web\Response;

class PermissionsController extends Controller
{

    /**
     * 返回定义的所有权限
     *
     * @return Response
     */
    public function actionIndex()
    {
        $items = $this->auth->getPermissions();
        if ($this->getModuleOptions()['selfish']) {
            $appId = Yii::$app->id;
            $len = strlen($appId);
            foreach ($items as $key => $item) {
                if (strncmp($appId, $key, $len) !== 0) {
                    unset($items[$key]);
                }
            }
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => array_values($items),
        ]);
    }

    /**
     * 添加权限
     *
     * @return Response
     */
    public function actionCreate()
    {
        $request = Yii::$app->getRequest();
        if ($request->isPost) {
            $success = true;
            $errorMessage = null;
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
                $success = false;
                $errorMessage = '名称不能为空。';
            } else {
                $permission = $this->auth->createPermission(Yii::$app->id . '@' . $name);
                $permission->description = $description;
                $this->auth->add($permission);
            }
            $responseBody = [
                'success' => $success,
            ];
            if (!$success) {
                $responseBody['error']['message'] = $errorMessage;
            } else {
                $permission = (array) $permission;
                $responseBody['data'] = $permission;
            }

            return new Response([
                'format' => Response::FORMAT_JSON,
                'data' => $responseBody
            ]);
        }
    }

    /**
     * 删除权限
     *
     * @param string $name
     * @return Response
     */
    public function actionDelete($name)
    {
        try {
            $name = trim($name);
            $permission = $this->auth->getPermission($name);
            $this->auth->remove($permission);
            $responseBody = [
                'success' => true,
            ];
        } catch (Exception $ex) {
            $responseBody = [
                'success' => false,
                'error' => [
                    'message' => $ex->getMessage(),
                ]
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

}