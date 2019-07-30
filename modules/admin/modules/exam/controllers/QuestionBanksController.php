<?php

namespace app\modules\admin\modules\exam\controllers;

use app\modules\admin\modules\exam\models\QuestionBank;
use app\modules\admin\modules\exam\models\QuestionBankSearch;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 题库管理
 *
 * @package app\modules\admin\modules\exam\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class QuestionBanksController extends Controller
{

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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'to-word'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all QuestionBank models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuestionBankSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuestionBank model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new QuestionBank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new QuestionBank();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing QuestionBank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing QuestionBank model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionToWord($id)
    {
        $model = $this->findModel($id);
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('宋体'); // 全局字体
        $phpWord->setDefaultFontSize(9);

        $header = array('size' => 16, 'bold' => true);
        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'alignment' => JcTable::CENTER);
        $phpWord->addParagraphStyle('pStyle', array('align' => 'center'));
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle);
        $section = $phpWord->addSection();
        $section->addText($model->name, $header, 'pStyle');
        $section->addTextBreak(1);

        $titleFont = array('name' => 'Tahoma', 'size' => 14, 'bold' => true);
        $optionsFont = array('name' => 'Tahoma', 'size' => 12);
        $answerFont = array('name' => 'Tahoma', 'size' => 12, 'color' => '#e57373');

        $addPrefix = function ($value, $key) {
            switch ($key) {
                case 0:
                    $value = 'A：' . $value;
                    break;

                case 1:
                    $value = 'B：' . $value;
                    break;

                case 2:
                    $value = 'C：' . $value;
                    break;
                case 3:
                    $value = 'A：' . $value;
                    break;
            }

            return $value;
        };

        $questions = Yii::$app->getDb()->createCommand('SELECT [[type]], [[content]], [[options]], [[answer]] FROM {{%question}} WHERE [[question_bank_id]] = :bankId', [':bankId' => $model->id])->queryAll();
        foreach ($questions as $i => $question) {
            $section->addText($i + 1 . '、' . $question['content'], $titleFont);
            foreach (explode(PHP_EOL, $question['options']) as $key => $value) {
                $section->addText($addPrefix($value, $key), $optionsFont);
            }

            $answers = [];
            foreach (explode(PHP_EOL, $question['answer']) as $key => $value) {
                $answers[] = $addPrefix($value, $key);
            }
            $section->addText('正确答案：' . implode('、', $answers), $answerFont);
            $section->addText();
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = $model['id'] . '.docx';
        $file = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $filename;
        $objWriter->save($file);

        Yii::$app->getResponse()->sendFile($file, "{$model['name']}.docx", ['mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    /**
     * Finds the QuestionBank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return QuestionBank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuestionBank::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
