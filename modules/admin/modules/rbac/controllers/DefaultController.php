<?php

namespace app\modules\admin\modules\rbac\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\rbac\helpers\RbacHelper;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\Response;

/**
 * `rbac` 子模块
 */
class DefaultController extends BaseController
{

    use RbacHelper;

    /** @var \yii\rbac\DbManager $auth */
    protected $auth;

    public function init()
    {
        parent::init();
        $this->auth = \Yii::$app->getAuthManager();
        if ($this->auth === null) {
            throw new Exception('Please setting authManager component in config file.');
        }
    }

    /**
     * 主页面
     *
     * @rbacDescription 查看权限认证主页面权限
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 解析控制器中的动作代码
     *
     * @param $file
     * @return array
     * @throws \ReflectionException
     */
    private function _parsePermissionsFromControllerClass($file)
    {
        $permissions = [
            'ignore' => [],
            'normal' => [],
        ];
        $className = str_replace([Yii::getAlias('@app'), '.php'], ['app'], FileHelper::normalizePath($file));
        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (preg_match('/^action[A-Z]+[a-zA-Z]*/', $method->getName())) {
                $description = null;
                foreach (explode(PHP_EOL, $method->getDocComment()) as $row) {
                    $row = trim($row);
                    if ($row) {
                        if (strpos($row, '@rbacIgnore') !== false) {
                            $permissions['ignore'][] = substr($method->getName(), 6);
                            break;
                        } elseif (strpos($row, '@rbacDescription') !== false) {
                            preg_match('/.*@rbacDescription(.*)/', $row, $matches);
                            if ($matches) {
                                $description = trim($matches[1]);
                            }
                        }
                    }
                }
                $permissions['normal'][substr($method->getName(), 6)] = $description;
            }
        }

        return $permissions;
    }

    /**
     * 扫描所有控制器获取动作和其说明
     *
     * @rbacDescription 扫描所有控制器获取动作和其说明权限
     * @return Response
     */
    public function actionScan()
    {
        $options = $this->getModuleOptions();
        $permissions = $files = [];
        $paths = [
            Yii::$app->getControllerPath()
        ];
        foreach (Yii::$app->getModules() as $key => $config) {
            $module = Yii::$app->getModule($key);
            $moduleId = $module->getUniqueId();
            if (in_array($moduleId, $options['disabledScanModules'])) {
                continue;
            }
            $paths["$moduleId-"] = $module->getControllerPath();

            // 激活的子模块
            foreach ($module->getModules() as $subModule) {
                $subModuleId = str_replace('/', '-', $subModule->getUniqueId());
                if (!in_array($subModuleId, $options['disabledScanModules'])) {
                    $paths["$subModuleId-"] = $subModule->getControllerPath();
                }
            }
        }
        foreach ($paths as $moduleId => $path) {
            if (!isset($files[$moduleId])) {
                $files[$moduleId] = [];
            }
            $files[$moduleId] = FileHelper::findFiles($path);
        }
        $ignorePermissions = [];
        $existPermissions = $this->auth->getPermissions();
        foreach ($files as $moduleId => $items) {
            foreach ($items as $file) {
                $rawPermissions = $this->_parsePermissionsFromControllerClass($file);
                foreach ($rawPermissions['ignore'] as $permissionName) {
                    $ignorePermissions[] = ($moduleId ?: '') . Inflector::camel2id(str_replace('Controller', '', basename($file, '.php')) . '.' . Inflector::camel2id($permissionName));
                }

                foreach ($rawPermissions['normal'] as $key => $description) {
                    $name = ($moduleId ?: '') . Inflector::camel2id(str_replace('Controller', '', basename($file, '.php')) . '.' . Inflector::camel2id($key));
                    $permissions[] = [
                        'name' => $name,
                        'description' => isset($existPermissions[$name]) ? $existPermissions[$name]->description : $description,
                        'active' => isset($existPermissions[$name]) ? false : true,
                    ];
                }
            }
        }

        Yii::$app->getCache()->set('admin.rbac.default.roles', $ignorePermissions, 0);
        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $permissions,
        ]);
    }
}
