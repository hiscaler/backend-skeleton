<?php

namespace app\modules\admin\controllers;

use app\models\Module;
use app\models\ModuleSearch;
use Yii;
use yii\base\InvalidParamException;
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

    public function init()
    {
        parent::init();
        $directories = [];
        $baseDirectory = Yii::getAlias('@app/modules');
        $handle = opendir($baseDirectory);
        if ($handle === false) {
            throw new InvalidParamException("Unable to open directory: {$baseDirectory}");
        }
        while (($dir = readdir($handle)) !== false) {
            if ($dir === '.' || $dir === '..' || $dir === 'admin' || !file_exists($baseDirectory . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'Module.php')) {
                continue;
            }
            // @todo 需要检测类的有效性
            if (is_dir($baseDirectory . DIRECTORY_SEPARATOR . $dir)) {
                $directories[] = $dir;
            }
        }
        closedir($handle);
        $this->_localModules = $directories;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
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
        $searchModel = new ModuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
            // @todo Insert module informations to DB
            $success = true;
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
