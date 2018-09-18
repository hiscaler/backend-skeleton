<?php

namespace app\modules\api\modules\vote\models;

use app\models\Category;
use Yii;

/**
 * This is the model class for table "{{%vote}}".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property string $title 名称
 * @property string $description 描述
 * @property int $begin_datetime 开始时间
 * @property int $end_datetime 结束时间
 * @property int $votes_count 总票数
 * @property int $allow_anonymous 允许匿名投票
 * @property int $allow_view_results 允许查看结果
 * @property int $allow_multiple_choice 允许多选
 * @property int $interval_seconds 间隔时间
 * @property int $ordering 排序
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Vote extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote}}';
    }

    public function fields()
    {
        return [
            'id',
            'categoryId' => 'category_id',
            'categoryName' => function () {
                return $this->category_id ? $this->category->name : null;
            },
            'title',
            'description',
            'beginDatetime' => 'begin_datetime',
            'endDatetime' => 'end_datetime',
            'totalVotesCount' => 'total_votes_count',
            'allowAnonymous' => function () {
                return boolval($this->allow_anonymous);
            },
            'allowViewResults' => function () {
                return boolval($this->allow_view_results);
            },
            'allowMultipleChoice' => function () {
                return boolval($this->allow_multiple_choice);
            },
            'intervalSeconds' => 'interval_seconds',
            'ordering',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public function extraFields()
    {
        return ['options', 'canVoting'];
    }

    /**
     * 判断是否能投票
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function getCanVoting()
    {
        $canVoting = false;
        $now = time();
        if ($this->begin_datetime > $now && $this->end_datetime < $now) {
            $ip = Yii::$app->getRequest()->getUserIP();
            if ($this->interval_seconds) {
                $lastPostDatetime = Yii::$app->getDb()->createCommand('SELECT [[post_datetime]] FROM {{%vote_log}} WHERE [[vote_id]] = :voteId AND [[ip_address]] = :ip ORDER BY [[post_datetime]] DESC', [':voteId' => $this->id, ':ip' => $ip])->queryScalar();
                if (!$lastPostDatetime || ($lastPostDatetime + $this->interval_seconds) < $now) {
                    $canVoting = true;
                }
            } else {
                $canVoting = true;
            }
        }

        return $canVoting;
    }

    /**
     * 所属分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * 投票选项
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(VoteOption::class, ['vote_id' => 'id']);
    }

}
