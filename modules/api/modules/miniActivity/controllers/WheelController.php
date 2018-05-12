<?php

namespace app\modules\api\modules\miniActivity\controllers;

use app\extensions\SMSHelper;
use app\modules\api\models\Constant;
use app\modules\api\modules\miniActivity\models\Wheel;
use yadjet\helpers\DatetimeHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * /api/miniActivity/wheel/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class WheelController extends Controller
{

    public $modelClass = 'app\modules\api\modules\miniActivity\models\Wheel';

    /**
     * 列表
     *
     * @api /api/miniActivity/default/index
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function actionIndex($page = 1, $pageSize = 20)
    {
        $query = (new ActiveQuery(Wheel::class))->where(['enabled' => Constant::BOOLEAN_TRUE]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 详情
     *
     * @api /api/miniActivity/wheel/default/view?id=1
     *
     * @param $id
     * @return Wheel|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    public function actionPlay($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            $now = time();
            if ($model['begin_datetime'] > $now) {
                throw new BadRequestHttpException('活动尚未开始。');
            } elseif ($model['end_datetime'] < $now) {
                throw new BadRequestHttpException('活动已结束。');
            } else {
                $db = Yii::$app->getDb();
                $now = time();
                $memberId = \Yii::$app->getUser()->getId();
                $ipAddress = \Yii::$app->getRequest()->getUserIP();
                $memberPlays = $db->createCommand('SELECT [[is_win]], [[post_datetime]] FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[member_id]] = :memberId ORDER BY [[post_datetime]] DESC', [':wheelId' => $model['id'], ':memberId' => $memberId])->queryAll();
                if (count($memberPlays) >= $model['play_times_per_person']) {
                    throw new BadRequestHttpException('超过抽奖次数。');
                }
                $winTimes = 0;
                $lastPostDatetime = null;
                foreach ($memberPlays as $play) {
                    if ($play['is_win']) {
                        $winTimes++;
                        $lastPostDatetime == null && $lastPostDatetime = $play['post_datetime'];
                    }
                }
                if ($winTimes >= $model['win_times_per_person']) {
                    throw new BadRequestHttpException("您已经中奖 $winTimes 次。");
                }
                if ($lastPostDatetime && $now - $lastPostDatetime >= $model['win_interval_seconds']) {
                    return new \stdClass();
                }

                $passed = false;
                $errorMessage = null;
                switch ($model['play_limit_type']) {
                    case Wheel::PLAY_LIMIT_TYPE_NO_LIMIT:
                        $passed = true;
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_IP:
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[ip_address]] = :ip', [':wheelId' => $model['id'], ':ip' => $ipAddress])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "同一个 IP 只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_DAY:
                        $dateRange = DatetimeHelper::todayRange();
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[post_datetime]] BETWEEN :begin AND :end', [':wheelId' => $model['id'], ':begin' => $dateRange[0], ':end' => $dateRange[1]])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "一天只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_WEEK:
                        $dateRange = DatetimeHelper::weekRange();
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[post_datetime]] BETWEEN :begin AND :end', [':wheelId' => $model['id'], ':begin' => $dateRange[0], ':end' => $dateRange[1]])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "本周只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_MONTH:
                        $dateRange = DatetimeHelper::monthRange();
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[post_datetime]] BETWEEN :begin AND :end', [':wheelId' => $model['id'], ':begin' => $dateRange[0], ':end' => $dateRange[1]])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "本月只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_QUARTER:
                        $dateRange = DatetimeHelper::quarterRange();
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[post_datetime]] BETWEEN :begin AND :end', [':wheelId' => $model['id'], ':begin' => $dateRange[0], ':end' => $dateRange[1]])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "本季度只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_YEAR:
                        $dateRange = DatetimeHelper::yearRange();
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId AND [[post_datetime]] BETWEEN :begin AND :end', [':wheelId' => $model['id'], ':begin' => $dateRange[0], ':end' => $dateRange[1]])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "本年度只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;

                    case Wheel::PLAY_LIMIT_TYPE_WHOLE:
                        $times = $db->createCommand('SELECT COUNT(*) FROM {{%mini_activity_wheel_log}} WHERE [[wheel_id]] = :wheelId', [':wheelId' => $model['id']])->queryScalar();
                        if ($times <= $model['play_times_per_person_by_limit_type']) {
                            $passed = true;
                        } else {
                            $errorMessage = "本活动只能玩 {$model['play_times_per_person_by_limit_type']} 次。";
                        }
                        break;
                }
                if (!$passed) {
                    throw new BadRequestHttpException($errorMessage);
                }

                // 开始抽奖
                $columns = [
                    'wheel_id' => $model['id'],
                    'ip_address' => $ipAddress,
                    'post_datetime' => $now,
                    'member_id' => $memberId,
                    'is_get' => Constant::BOOLEAN_FALSE,
                    'get_datetime' => null,
                ];
                $awards = Wheel::getAwards($model['id'], $model['blocks_count']);
                $key = mt_rand(1, 100);
                if ($awards[$key]['id']) {
                    $columns['award_id'] = $awards[$key]['id'];
                    $columns['is_win'] = Constant::BOOLEAN_TRUE;
                    $columns['get_password'] = \yadjet\helpers\StringHelper::generateRandomString(16);

                    // 发送短息
                    $mobilePhone = $db->createCommand('SELECT [[mobile_phone]] FROM {{%member}} WHERE [[id]] = :id', [':id' => $memberId])->queryScalar();
                    if ($mobilePhone) {
                        SMSHelper::send($mobilePhone, $model['win_message']);
                    }
                } else {
                    $columns['is_win'] = Constant::BOOLEAN_FALSE;
                    $columns['award_id'] = 0;
                }

                $db->createCommand()->insert('{{%mini_activity_wheel_log}}', $columns)->execute();

                return [
                    '_id' => $awards[$key]['_id']
                ];
            }
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function findModel($id)
    {
        $model = Wheel::find()->where(['id' => $id, 'enabled' => Constant::BOOLEAN_TRUE])->one();
        if (!$model) {
            throw new NotFoundHttpException('数据不存在。');
        }

        return $model;
    }
}
