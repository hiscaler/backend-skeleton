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

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 解析控制器中的动作代码
     *
     * @param $file
     * @return array
     */
    private function _parseControllerFile($file)
    {
        $className = str_replace(Yii::getAlias('@app'), 'app', FileHelper::normalizePath($file));
        $className = str_replace('.php', '', $className);
        $descriptions = [];
        $reflection = new \ReflectionClass($className);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (preg_match('/^action[A-Z]+[a-zA-Z]*/', $method->getName())) {
                $t = $method->getDocComment();
                $rbacDescription = null;
                foreach (explode(PHP_EOL, $t) as $row) {
                    $row = trim($row);
                    if ($row) {
                        if (strpos($row, '@rbacIgnore') !== false) {
                            break;
                        } elseif (strpos($row, '@rbacDescription') !== false) {
                            preg_match('/.*@rbacDescription(.*)/', $row, $matches);
                            if ($matches) {
                                $rbacDescription = trim($matches[1]);
                            }
                        }
                    }
                }
                $descriptions[substr($method->getName(), 6)] = $rbacDescription;
            }
        }

        return $descriptions;
    }

    public function actionScan()
    {
        $options = $this->getModuleOptions();
        $actions = $files = [];
        $paths = [
            Yii::$app->getControllerPath()
        ];
        foreach (Yii::$app->getModules() as $key => $config) {
            $moduleId = Yii::$app->getModule($key)->getUniqueId();
            if (empty($moduleId) || in_array($moduleId, $options['disabledScanModules'])) {
                continue;
            }
            $paths["{$moduleId}-"] = Yii::$app->getModule($moduleId)->getControllerPath();
        }
        foreach ($paths as $moduleId => $path) {
            if (!isset($files[$moduleId])) {
                $files[$moduleId] = [];
            }
            $files[$moduleId] = FileHelper::findFiles($path);
        }
        $existsActions = $this->auth->getPermissions();
        foreach ($files as $moduleId => $items) {
            foreach ($items as $file) {
                $parseActions = $this->_parseControllerFile($file);
                foreach ($parseActions as $key => $description) {
                    $name = ($moduleId ?: '') . Inflector::camel2id(str_replace('Controller', '', basename($file, '.php')) . '.' . Inflector::camel2id($key));
                    $actions[] = [
                        'name' => $name,
                        'description' => isset($existsActions[$name]) ? $existsActions[$name]->description : $description,
                        'active' => isset($existsActions[$name]) ? false : true,
                    ];
                }
            }
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $actions,
        ]);
    }
}
