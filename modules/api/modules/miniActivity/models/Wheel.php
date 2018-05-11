<?php

namespace app\modules\api\modules\miniActivity\models;

use app\modules\api\extensions\UtilsHelper;
use app\modules\api\models\Constant;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%mini_activity_wheel}}".
 *
 * @property int $id
 * @property string $title 活动名称
 * @property string $win_message 中奖消息
 * @property string $get_award_message 兑奖消息
 * @property int $begin_datetime 开始时间
 * @property int $end_datetime 结束时间
 * @property string $description 活动说明
 * @property string $photo 活动预览图片
 * @property string $repeat_play_message 重复抽奖提示信息
 * @property string $background_image 背景图片
 * @property string $background_image_repeat_type 背景类型
 * @property string $finished_title 活动结束公告主题
 * @property string $finished_description 活动结束说明
 * @property string $finished_photo 活动结束图片
 * @property int $estimated_people_count 预计活动人数
 * @property int $actual_people_count 实际活动人数
 * @property int $play_times_per_person 每人抽奖总次数
 * @property int $play_limit_type 抽奖限制规则
 * @property int $play_times_per_person_by_limit_type 抽奖限制规则次数
 * @property int $win_times_per_person 每人中奖次数
 * @property int $win_interval_seconds 每人每次中奖时间间隔
 * @property int $show_awards_quantity 显示奖品数量
 * @property int $blocks_count 区块数量
 * @property int $ordering 排序
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Wheel extends \yii\db\ActiveRecord
{

    const PLAY_LIMIT_TYPE_NO_LIMIT = 0;
    const PLAY_LIMIT_TYPE_IP = 1;
    const PLAY_LIMIT_TYPE_DAY = 2;
    const PLAY_LIMIT_TYPE_WEEK = 3;
    const PLAY_LIMIT_TYPE_MONTH = 4;
    const PLAY_LIMIT_TYPE_QUARTER = 5;
    const PLAY_LIMIT_TYPE_YEAR = 6;
    const PLAY_LIMIT_TYPE_WHOLE = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mini_activity_wheel}}';
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'winMessage' => 'win_message',
            'getAwardMessage' => 'get_award_message',
            'beginDatetime' => 'begin_datetime',
            'endDatetime' => 'end_datetime',
            'description',
            'photo' => function () {
                return $this->photo ? UtilsHelper::fixStaticAssetUrl($this->photo) : null;
            },
            'repeatPlayMessage' => 'repeat_play_message',
            'backgroundImage' => function () {
                return $this->background_image ? UtilsHelper::fixStaticAssetUrl($this->background_image) : null;
            },
            'backgroundImageRepeatType' => 'background_image_repeat_type',
            'finishedTitle' => 'finished_title',
            'finishedDescription' => 'finished_description',
            'finishedPhoto' => function () {
                return $this->finished_photo ? UtilsHelper::fixStaticAssetUrl($this->finished_photo) : null;
            },
            'blocksCount' => 'blocks_count',
            'awards' => function () {
                return self::getAwards($this->id, $this->blocks_count);
            },
            'estimatedPeopleCount' => 'estimated_people_count',
            'actualPeopleCount' => 'actual_people_count',
            'playTimesPerPerson' => 'play_times_per_person',
            'playLimitType' => function () {
                return $this->play_limit_type;
            },
            'playTimesPerPersonByLimitType' => 'play_times_per_person_by_limit_type',
            'winTimesPerPerson' => 'win_times_per_person',
            'winIntervalSeconds' => 'win_interval_seconds',
            'showAwardsQuantity' => function () {
                return boolval($this->show_awards_quantity);
            },
            'ordering',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public function extraFields()
    {
        return ['awards'];
    }

    /**
     * 获取奖品列表（包括不能中奖的项目）
     *
     * @param $wheelId
     * @param $blocksCount
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getAwards($wheelId, $blocksCount)
    {
        $awards = [];
        if ($blocksCount) {
            for ($i = 1; $i <= $blocksCount; $i++) {
                $awards["award_$i"] = [
                    'id' => 0,
                    'title' => null,
                    'description' => null,
                    'photo' => null,
                    'totalQuantity' => null,
                    'remainingQuantity' => null,
                    '_id' => $i,
                ];
            }
            $rawAwards = \Yii::$app->getDb()->createCommand('SELECT [[id]], [[ordering]], [[title]], [[description]], [[photo]], [[total_quantity]] AS [[totalQuantity]], [[remaining_quantity]] AS [[remainingQuantity]] FROM {{%mini_activity_wheel_award}} WHERE [[wheel_id]] = :wheelId AND [[enabled]] = :enabled ORDER BY [[ordering]] ASC', [
                ':wheelId' => $wheelId,
                ':enabled' => Constant::BOOLEAN_TRUE,
            ])->queryAll();

            $request = \Yii::$app->getRequest();
            $url = $request->getHostInfo() . $request->getBaseUrl();
            foreach ($rawAwards as $award) {
                $key = $award['ordering'];
                if (!isset($awards["award_$key"])) {
                    continue;
                }
                unset($award['ordering']);
                $award['_id'] = (int) $key;
                $award['photo'] = $award['photo'] ? $url . $award['photo'] : null;
                $awards["award_$key"] = $award;
            }
        }

        return $awards;
    }

    /**
     * 抽奖限制规则
     *
     * @return array
     */
    public static function playLimitTypeOptions()
    {
        return [
            self::PLAY_LIMIT_TYPE_NO_LIMIT => '无限制',
            self::PLAY_LIMIT_TYPE_IP => 'IP 限制',
            self::PLAY_LIMIT_TYPE_DAY => '每日限制',
            self::PLAY_LIMIT_TYPE_WEEK => '每周限制',
            self::PLAY_LIMIT_TYPE_MONTH => '每月限制',
            self::PLAY_LIMIT_TYPE_QUARTER => '每季限制',
            self::PLAY_LIMIT_TYPE_YEAR => '每年限制',
            self::PLAY_LIMIT_TYPE_WHOLE => '整个活动',
        ];
    }

}
