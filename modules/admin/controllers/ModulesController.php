<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
    private $_installedModules = [];

    public function init()
    {
        parent::init();
        $defaultIcon = Yii::$app->getRequest()->getBaseUrl() . '/images/default-module-icon.png';
        $localModules = [];
        $baseDirectory = Yii::getAlias('@app/modules');
        $handle = opendir($baseDirectory);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: {$baseDirectory}");
        }
        while (($dir = readdir($handle)) !== false) {
            if ($dir === '.' || $dir === '..' || $dir === 'admin' || $dir == 'api' || !file_exists($baseDirectory . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'Module.php')) {
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
                ];
                if (file_exists($fullDirectory . DIRECTORY_SEPARATOR . 'readme.txt')) {
                    $readme = file($fullDirectory . DIRECTORY_SEPARATOR . 'readme.txt');
                    if ($readme !== false) {
                        foreach ($readme as $row) {
                            if (stripos($row, ':') !== false) {
                                $row = array_map('trim', explode(':', $row));
                                $key = array_shift($row);
                                $value = implode('', $row);
                                if ($key && $value && $key != 'alias' && array_key_exists($key, $m)) {
                                    $m[$key] = $value;
                                }
                            }
                        }
                    }
                }
                if (file_exists($fullDirectory . DIRECTORY_SEPARATOR . 'icon.png')) {
                    $t = Yii::$app->getModule($dir);
                    $t && $m['icon'] = $t->getBasePath() . '/icon.png';
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'install', 'uninstall'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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
     * Displays a single Module model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Module model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Module();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Module model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Module model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
                    'enabled' => Constant::BOOLEAN_TRUE,
                    'created_at' => $now,
                    'created_by' => $userId,
                    'updated_at' => $now,
                    'updated_by' => $userId,
                ])->execute();
                $success = true;
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
            $db->createCommand()->delete('{{%module}}', ['id' => $moduleId])->execute();
            $success = true;
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
     * Finds the Module model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Module the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Module::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
