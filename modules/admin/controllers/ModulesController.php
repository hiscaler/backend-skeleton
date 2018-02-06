<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\console\controllers\MigrateController;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Response;

/**
 * 模块管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class ModulesController extends Controller
{

    /**
     * 本地放置的模块，不一定有安装到系统中
     *
     * @var array
     */
    private $_localModules = [];

    public function init()
    {
        parent::init();
        $defaultIcon = Yii::$app->getRequest()->getBaseUrl() . '/admin/images/module-icons/default-module-icon.png';
        $localModules = [];
        $baseDirectory = Yii::getAlias('@app/modules/admin/modules');
        $handle = opendir($baseDirectory);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: {$baseDirectory}");
        }
        while (($dir = readdir($handle)) !== false) {
            if ($dir === '.' || $dir === '..' || $dir === 'admin' || $dir === 'api' || !file_exists($baseDirectory . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'Module.php')) {
                continue;
            }
            // @todo 需要检测类的有效性
            $fullDirectory = $baseDirectory . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($fullDirectory)) {
                $m = [
                    'alias' => $dir,
                    'name' => null,
                    'author' => null,
                    'version' => null,
                    'url' => null,
                    'icon' => $defaultIcon,
                    'description' => null,
                    'menus' => [],
                    'depends' => []
                ];
                if (file_exists($fullDirectory . DIRECTORY_SEPARATOR . 'conf.json')) {
                    $rawConfig = file_get_contents($fullDirectory . DIRECTORY_SEPARATOR . 'conf.json');
                    if ($rawConfig !== false && ($configs = json_decode($rawConfig, true)) !== false) {
                        $requireItems = ['name', 'author', 'version'];
                        foreach ($requireItems as $item) {
                            if (!isset($configs[$item]) || empty($configs[$item])) {
                                continue 2;
                            }
                        }
                        foreach ($configs as $key => $value) {
                            if (array_key_exists($key, $m)) {
                                switch ($key) {
                                    case 'menus':
                                        if (is_array($value)) {
                                            $links = [];
                                            foreach ($value as $link) {
                                                if (
                                                    !isset($link['label'], $link['url']) ||
                                                    empty($link['url']) ||
                                                    !isset($link['url'][0]) ||
                                                    !is_string($link['url'][0])
                                                ) {
                                                    continue;
                                                }
                                                $t = ["/admin/{$link['url'][0]}"];
                                                if (isset($link['url'][1]) && is_array($link['url'][1])) {
                                                    foreach ($link['url'][1] as $k => $v) {
                                                        $t[$k] = (string) $v;
                                                    }
                                                }
                                                $links[] = [
                                                    'label' => $link['label'],
                                                    'url' => $t,
                                                    'active' => isset($link['active']) ? $link['active'] : null,
                                                ];
                                            }
                                            $value = $links;
                                        } else {
                                            continue;
                                        }
                                        break;
                                    case 'depends':
                                        if (!is_array($value)) {
                                            continue;
                                        }
                                        break;
                                }
                                $m[$key] = $value;
                            }
                        }
                    }
                }
                if (file_exists($fullDirectory . DIRECTORY_SEPARATOR . 'icon.png')) {
                    $dest = Yii::getAlias('@webroot') . "/admin/images/module-icons/{$dir}-icon.png";
                    !file_exists($dest) && copy($fullDirectory . DIRECTORY_SEPARATOR . 'icon.png', $dest);
                    $m['icon'] = "/admin/images/module-icons/{$dir}-icon.png";
                }
                $localModules[$dir] = $m;
            }
        }
        closedir($handle);
        $this->_localModules = $localModules;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'install', 'uninstall', 'upgrade'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'install' => ['POST'],
                    'uninstall' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 迁移数据库脚本
     *
     * @param $moduleId
     * @param string $action
     * @return string
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    private function _migrate($moduleId, $action = 'up')
    {
        if (!defined('STDIN')) {
            define('STDIN', fopen("php://stdin", "r"));
        }
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }
        $migrationFilesPath = Yii::getAlias('@app/modules/admin/modules/' . $moduleId . '/migrations/');
        Yii::setAlias('@migrations', $migrationFilesPath);
        if (!file_exists($migrationFilesPath)) {
            return null;
        }

        ob_start();
        $migration = new MigrateController('migrate', Yii::$app);
        if ($action == 'up') {
            $migration->runAction('up', ['migrationPath' => '@migrations', 'interactive' => false]);
        } else {
            // Down
            $query = (new Query())
                ->select(['version', 'apply_time'])
                ->from($migration->migrationTable)
                ->orderBy(['apply_time' => SORT_DESC, 'version' => SORT_DESC]);

            if (empty($migration->migrationNamespaces)) {
                $rows = $query->all();
                $history = ArrayHelper::map($rows, 'version', 'apply_time');
                unset($history[$migration::BASE_MIGRATION]);
            } else {
                $rows = $query->all($migration->db);

                $history = [];
                foreach ($rows as $key => $row) {
                    if ($row['version'] === $migration::BASE_MIGRATION) {
                        continue;
                    }
                    if (preg_match('/m?(\d{6}_?\d{6})(\D.*)?$/is', $row['version'], $matches)) {
                        $time = str_replace('_', '', $matches[1]);
                        $row['canonicalVersion'] = $time;
                    } else {
                        $row['canonicalVersion'] = $row['version'];
                    }
                    $row['apply_time'] = (int) $row['apply_time'];
                    $history[] = $row;
                }

                usort($history, function ($a, $b) {
                    if ($a['apply_time'] === $b['apply_time']) {
                        if (($compareResult = strcasecmp($b['canonicalVersion'], $a['canonicalVersion'])) !== 0) {
                            return $compareResult;
                        }

                        return strcasecmp($b['version'], $a['version']);
                    }

                    return ($a['apply_time'] > $b['apply_time']) ? -1 : +1;
                });

                $history = ArrayHelper::map($history, 'version', 'apply_time');
            }

            $files = FileHelper::findFiles($migrationFilesPath);
            $migration->interactive = false;
            $migration->db = Yii::$app->getDb();
            $cmd = $migration->db->createCommand();
            foreach ($files as $i => $file) {
                $version = basename($file, '.php');
                if (!isset($history[$version])) {
                    continue;
                }

                // delete and add
                $cmd->delete($migration->migrationTable, ['version' => $version])->execute();
                $cmd->insert($migration->migrationTable, ['version' => $version, 'apply_time' => time()])->execute();
                sleep(1);

                $migration->runAction('down', ['migrationPath' => '@migrations', 'interactive' => false]);
            }
        }

        ob_clean();
        $handle = fopen('php://stdout', 'r');
        $message = '';
        while (($buffer = fgets($handle, 4096)) !== false) {
            $message .= $buffer . "<br>";
        }
        fclose($handle);

        return $message;
    }

    /**
     * Lists all Module models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $notInstalledModules = $this->_localModules;
        $installedModules = Yii::$app->getDb()->createCommand('SELECT * FROM {{%module}} ORDER BY [[updated_at]] DESC')->queryAll();
        foreach ($installedModules as $key => $module) {
            $installedModules[$key]['error'] = isset($this->_localModules[$module['alias']]) ? Module::ERROR_NONE : Module::ERROR_NOT_FOUND_DIRECTORY;
            if (isset($notInstalledModules[$module['alias']])) {
                unset($notInstalledModules[$module['alias']]);
            }
        }

        return $this->render('index', [
            'installedModules' => $installedModules,
            'notInstalledModules' => $notInstalledModules,
        ]);
    }

    /**
     * 模块安装
     *
     * @param $alias
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionInstall($alias)
    {
        $success = false;
        $errorMessage = null;
        $db = Yii::$app->getDb();
        $exists = $db->createCommand('SELECT COUNT(*) FROM {{%module}} WHERE [[alias]] = :alias', [':alias' => trim($alias)])->queryScalar();
        if ($exists) {
            $errorMessage = '该模块已经安装。';
        } else {
            $module = isset($this->_localModules[$alias]) ? $this->_localModules[$alias] : null;
            if ($module === null) {
                $errorMessage = '安装模块不存在。';
            } else {
                try {
                    $now = time();
                    $userId = Yii::$app->getUser()->getId();
                    $db->createCommand()->insert('{{%module}}', [
                        'alias' => $alias,
                        'name' => $module['name'],
                        'author' => $module['author'],
                        'version' => $module['version'],
                        'icon' => $module['icon'],
                        'url' => $module['url'],
                        'description' => $module['description'],
                        'menus' => $module['menus'] ? json_encode($module['menus'], JSON_UNESCAPED_UNICODE + JSON_NUMERIC_CHECK) : null,
                        'enabled' => Constant::BOOLEAN_TRUE,
                        'created_at' => $now,
                        'created_by' => $userId,
                        'updated_at' => $now,
                        'updated_by' => $userId,
                    ])->execute();
                    $this->_migrate($alias, 'up');
                    $success = true;
                } catch (\Exception $ex) {
                    $errorMessage = $ex->getMessage();
                }
            }
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
     * 模块卸载
     *
     * @param $alias
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionUninstall($alias)
    {
        $success = false;
        $errorMessage = null;
        $db = Yii::$app->getDb();
        $moduleId = $db->createCommand('SELECT [[id]] FROM {{%module}} WHERE [[alias]] = :alias', [':alias' => trim($alias)])->queryScalar();
        if ($moduleId) {
            try {
                if (isset(Yii::$app->params['uninstall.module.after.droptable']) && Yii::$app->params['uninstall.module.after.droptable'] === true) {
                    $this->_migrate($alias, 'down');
                }
                $db->createCommand()->delete('{{%module}}', ['id' => $moduleId])->execute();
                $success = true;
            } catch (\Exception $ex) {
                $errorMessage = $ex->getMessage();
            }
        } else {
            $errorMessage = '该模块不存在。';
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
     * 更新模块
     *
     * @todo 数据及文件的处理
     *
     * @param $alias
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionUpgrade($alias)
    {
        $success = false;
        $errorMessage = null;
        $db = Yii::$app->getDb();
        $moduleId = $db->createCommand('SELECT [[id]] FROM {{%module}} WHERE [[alias]] = :alias', [':alias' => trim($alias)])->queryScalar();
        if ($moduleId) {
            $module = isset($this->_localModules[$alias]) ? $this->_localModules[$alias] : null;
            if ($module === null) {
                $errorMessage = '安装模块不存在。';
            } else {
                try {
                    $db->createCommand()->update('{{%module}}', [
                        'name' => $module['name'],
                        'author' => $module['author'],
                        'version' => $module['version'],
                        'icon' => $module['icon'],
                        'url' => $module['url'],
                        'description' => $module['description'],
                        'menus' => $module['menus'] ? json_encode($module['menus'], JSON_UNESCAPED_UNICODE + JSON_NUMERIC_CHECK) : null,
                        'updated_at' => time(),
                        'updated_by' => Yii::$app->getUser()->getId(),
                    ], ['id' => $moduleId])->execute();

                    // 更新翻译文本
                    $messagePath = Yii::getAlias('@app/modules/admin/modules/' . $alias . '/messages');
                    if (file_exists($messagePath)) {
                        FileHelper::copyDirectory($messagePath, Yii::getAlias('@app/messages'));
                    }

                    $success = true;
                } catch (\Exception $ex) {
                    $errorMessage = $ex->getMessage();
                }
            }
        } else {
            $errorMessage = '该模块不存在。';
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
