<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\extensions\Formatter;
use app\modules\api\models\Member;
use app\modules\api\models\MemberSearch;
use DateTime;
use Exception;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * api/member/ 接口
 * Class MemberController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberController extends ActiveController
{

    /**
     * 统计类型
     */
    const STATISTICS_TYPE = 'date';
    const STATISTICS_TYPE_MEMBER_TYPE = 'type';

    public $modelClass = Member::class;

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'statistics'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @throws \Throwable
     */
    public function prepareDataProvider()
    {
        $search = new MemberSearch();

        return $search->search(Yii::$app->getRequest()->getQueryParams());
    }

    /**
     * 会员注册统计
     *
     * @param null $beginDate
     * @param null $endDate
     * @param string $type
     * @return array
     * @throws Exception
     */
    public function actionStatistics($beginDate = null, $endDate = null, $type = self::STATISTICS_TYPE)
    {
        $items = [];
        $condition = [];
        if ($beginDate && $endDate) {
            try {
                $beginTimestamp = (new Datetime($beginDate))->setTime(0, 0, 0)->getTimestamp();
                $endTimestamp = (new Datetime($endDate))->setTime(23, 59, 59)->getTimestamp();
                $condition = [
                    'BETWEEN', 'created_at', $beginTimestamp, $endTimestamp,
                ];
            } catch (Exception $e) {
            }
        }

        $q = (new Query())
            ->select(['type', 'created_at'])
            ->from('{{%member}}')
            ->where($condition)
            ->orderBy(['id' => SORT_ASC]);
        switch (strtolower($type)) {
            case self::STATISTICS_TYPE_MEMBER_TYPE:
                /* @var $formatter Formatter */
                $formatter = Yii::$app->getFormatter();
                foreach ($q->each() as $row) {
                    $key = $row['type'];
                    if (!isset($items[$key])) {
                        $items[$key] = [
                            'name' => $formatter->asMemberType($row['type']),
                            'value' => 1,
                        ];
                    } else {
                        $items[$key]['value'] += 1;
                    }
                }
                break;

            default:
                // 根据注册时间统计
                foreach ($q->each() as $row) {
                    $key = date('Y-m-d', $row['created_at']);
                    if (!isset($items[$key])) {
                        $items[$key] = [
                            'name' => $key,
                            'value' => 1,
                        ];
                    } else {
                        $items[$key]['value'] += 1;
                    }
                }

                // 填充空白日期
                reset($items);
                $t = current($items);
                $firstDay = $t['name'];
                $t = end($items);
                $lastDay = $t['name'];
                if ($beginDate && $endDate) {
                    $firstDay != $beginDate && $firstDay = $beginDate;
                    $lastDay != $endDate && $lastDay = $endDate;
                }
                $datetime = new DateTime($firstDay);
                $days = $datetime->diff(new DateTime($lastDay))->days;
                for ($i = 0; $i <= $days; $i++) {
                    $key = $datetime->format('Y-m-d');
                    if (!isset($items[$key])) {
                        $items[$key] = [
                            'name' => $key,
                            'value' => 0,
                        ];
                    }
                    $datetime->modify('+1 day');
                }
                ArrayHelper::multisort($items, 'name', SORT_ASC);
                break;
        }

        return array_values($items);
    }

}