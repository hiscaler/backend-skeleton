<?php

namespace app\modules\api\modules\exam\controllers;

use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\exam\models\Question;
use app\modules\api\modules\exam\models\QuestionBank;
use app\modules\api\modules\exam\models\QuestionSearch;
use Yii;
use yii\filters\VerbFilter;

/**
 * `exam/question` 接口
 *
 * @package app\modules\api\modules\exam\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class QuestionController extends Controller
{

    public $modelClass = QuestionBank::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['create']['class'] = CreateAction::class;

        return $actions;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'actions' => ['index', 'create', 'view', 'delete', 'update', 'random'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
        ]);

        return $behaviors;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $search = new QuestionSearch();

        return $search->search(Yii::$app->getRequest()->getQueryParams());
    }

    /**
     * 随机获取试题
     *
     * @param string|int $filter
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionRandom($filter = 10)
    {
        $items = [];
        $condition = []; // bankId => size
        $defaultCount = 10;
        if (ctype_digit((string) $filter)) {
            $n = (int) $filter;
            $n <= 0 && $n = $defaultCount;
            $condition = [0 => $n];
        } else {
            // 1.2,2.3 表示从题库 1 中调取 2 个试题，从题库 2 中调取 3 个试题
            foreach (explode(',', $filter) as $str) {
                if (stripos($str, ".") !== false) {
                    list($bankId, $n) = explode('.', $str);
                    $bankId = (int) $bankId;
                    $n = (int) $n;
                    if ($bankId && $n) {
                        $condition[$bankId] = $n;
                    }
                }
            }
            if (!$condition) {
                $condition = [0 => $defaultCount];
            }
        }

        $db = Yii::$app->getDb();
        foreach ($condition as $bankId => $size) {
            $where = "[[status]] = " . Question::STATUS_OPEN;
            if ($bankId) {
                $where .= ' AND [[question_bank_id]] = ' . (int) $bankId;
            }
            $t = $db->createCommand("SELECT [[t1.id]], [[type]], [[content]], [[options]], [[resolve]], [[answer]] FROM {{%exam_question}} AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX([[id]]) FROM {{%exam_question}} WHERE $where)) AS id) AS t2 WHERE t1.id >= t2.id AND $where ORDER BY t1.id ASC LIMIT :limit", [':limit' => $size])->queryAll();
            $items = array_merge($items, $t);
        }

        shuffle($items);

        foreach ($items as $key => $data) {
            switch ($data['type']) {
                case Question::TYPE_SINGLE_CHOICE:
                    $data['type'] = 'choice';
                    $data['typeName'] = '单选题';
                    $data['answer'] = trim($data['answer']);
                    break;

                case Question::TYPE_MULTIPLE_CHOICE:
                    $data['type'] = 'multipleChoice';
                    $data['typeName'] = '多选题';
                    $answers = [];
                    foreach (explode(PHP_EOL, $data['answer']) as $line) {
                        $line = trim($line);
                        if ($line) {
                            $answers[] = $line;
                        }
                    }
                    $data['answer'] = $answers;
                    break;

                default:
                    $data['type'] = 'trueOrFalse';
                    $data['typeName'] = '判断题';
                    $data['answer'] = trim($data['answer']);
            }

            $options = explode(PHP_EOL, $data['options']);
            foreach ($options as $k => $option) {
                $option = trim($option);
                switch ($k) {
                    case 0:
                        $letter = 'A';
                        break;

                    case 1:
                        $letter = 'B';
                        break;

                    case 2:
                        $letter = 'C';
                        break;

                    case 3:
                        $letter = 'D';
                        break;

                    case 4:
                        $letter = 'E';
                        break;

                    case 5:
                        $letter = 'F';
                        break;

                    default:
                        $letter = '';
                        break;
                }

                $options[$k] = [
                    'letter' => $letter,
                    'text' => $option
                ];
            }
            $data['options'] = $options;
            $items[$key] = $data;
        }

        return $items;
    }

}
