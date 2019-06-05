<?php

namespace app\modules\api\modules\finance\controllers;

use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\modules\finance\models\Finance;
use app\modules\api\modules\finance\models\FinanceSearch;
use DateTime;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * `finance/default` 财务接口
 *
 * @package app\modules\api\modules\finance\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = Finance::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'view', 'statistics'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        $search = new FinanceSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

    /**
     * 统计
     *
     * @param int $type
     * @param null $beginDate
     * @param null $endDate
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionStatistics($type = Finance::TYPE_INCOME, $beginDate = null, $endDate = null)
    {
        $items = [];
        $types = Finance::typeOptions();
        if (!in_array($type, $types)) {
            $type = Finance::TYPE_INCOME;
        }
        $begin = $end = null;
        try {
            $beginDate = new DateTime($beginDate);
            $beginDate->setTime(0, 0, 0);
            $begin = $beginDate->getTimestamp();
            $endDate = new DateTime($endDate);
            $endDate->setTime(23, 59, 59);
            $end = $endDate->getTimestamp();
        } catch (\Exception $e) {
        }
        if (!$begin || !$end || $begin > $end) {
            // Default is today
            $datetime = new DateTime();
            $begin = $datetime->setTime(0, 0, 0)->getTimestamp();
            $end = $datetime->setTime(23, 59, 59)->getTimestamp();
        }
        $rows = \Yii::$app->getDb()->createCommand('SELECT * FROM {{%finance}} WHERE [[type]] = :type AND [[created_at]] BETWEEN :begin AND :end ORDER BY [[id]] ASC', [
            ':type' => $type,
            ':begin' => $begin,
            ':end' => $end,
        ])->queryAll();
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('serialize_precision', 10);
        }
        foreach ($rows as $row) {
            $day = date('Y-m-d', $row['created_at']);
            $money = abs($row['money']) / 100;
            if (!isset($items[$day])) {
                $items[$day] = [
                    'day' => $day,
                    'money' => $money,
                ];
            } else {
                $items[$day]['money'] += $money;
            }
        }

        return array_values($items);
    }

}
