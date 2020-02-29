<?php

namespace app\modules\admin\modules\rbac\controllers;

use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\rbac\helpers\RbacHelper;
use Yii;
use yii\base\Exception;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\Response;

/**
 * Class DefaultController
 *
 * @package app\modules\admin\modules\rbac\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    use RbacHelper;

    /** @var \yii\rbac\DbManager $auth */
    protected $auth;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'scan'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws Exception
     */
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
     * @rbacDescription 会员授权功能
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
        $className = str_replace('/', '\\', $className);
        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (preg_match('/^action[A-Z]+[a-zA-Z]*/', $method->getName())) {
                $ignore = false;
                $description = null;
                foreach (explode(PHP_EOL, $method->getDocComment()) as $row) {
                    $row = trim($row);
                    if ($row) {
                        if (strpos($row, '@rbacIgnore') !== false) {
                            $ignore = true;
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
                !$ignore && $permissions['normal'][substr($method->getName(), 6)] = $description;
            }
        }

        return $permissions;
    }

    /**
     * 扫描所有控制器获取动作和其说明
     *
     * @rbacIgnore true
     * @rbacDescription 扫描所有控制器获取动作和其说明权限
     * @return Response
     * @throws \ReflectionException
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
            $mainModuleId = $module->getUniqueId();
            if (in_array($mainModuleId, $options['disabledScanModules'])) {
                continue;
            }
            $paths["$mainModuleId-"] = $module->getControllerPath();

            // 激活的子模块
            foreach ($module->getModules() as $k => $subModule) {
                if (!$subModule instanceof Module) {
                    if (class_exists("\\app\\modules\\$mainModuleId\\modules\\$k\Module")) {
                        $subModule = Yii::$app->getModule("$mainModuleId/$k");
                    } else {
                        continue;
                    }
                }

                $subModuleId = str_replace('/', '-', $subModule->getUniqueId());
                if (!in_array($subModuleId, $options['disabledScanModules'])) {
                    $paths["$subModuleId-"] = $subModule->getControllerPath();
                }
            }
        }
        foreach ($paths as $mainModuleId => $path) {
            if (!isset($files[$mainModuleId])) {
                $files[$mainModuleId] = [];
            }
            $files[$mainModuleId] = FileHelper::findFiles($path);
        }
        $ignorePermissions = [];
        $existPermissions = $this->auth->getPermissions();
        foreach ($files as $mainModuleId => $items) {
            foreach ($items as $file) {
                $rawPermissions = $this->_parsePermissionsFromControllerClass($file);
                foreach ($rawPermissions['ignore'] as $permissionName) {
                    $ignorePermissions[] = ($mainModuleId ?: '') . Inflector::camel2id(str_replace('Controller', '', basename($file, '.php')) . '.' . Inflector::camel2id($permissionName));
                }

                foreach ($rawPermissions['normal'] as $key => $description) {
                    $name = ($mainModuleId ?: '') . Inflector::camel2id(str_replace('Controller', '', basename($file, '.php')) . '.' . Inflector::camel2id($key));
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
