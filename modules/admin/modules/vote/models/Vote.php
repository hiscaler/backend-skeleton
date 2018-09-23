<?php

namespace app\modules\admin\modules\vote\models;

use app\models\Category;
use app\models\Constant;
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
 * @property int $total_votes_count 总票数
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

    const SCENARIO_DELETE = 'DELETE';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DELETE => self::OP_DELETE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'total_votes_count', 'interval_seconds', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'begin_datetime', 'end_datetime'], 'required'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 100],
            ['begin_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'begin_datetime'],
            ['end_datetime', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'end_datetime'],
            [['allow_anonymous', 'allow_view_results', 'allow_multiple_choice', 'enabled'], 'boolean'],
            [['allow_anonymous', 'allow_view_results', 'allow_multiple_choice'], 'default', 'value' => Constant::BOOLEAN_FALSE],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['category_id', 'total_votes_count', 'interval_seconds'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => '分类',
            'title' => '名称',
            'description' => '描述',
            'begin_datetime' => '开始时间',
            'end_datetime' => '结束时间',
            'total_votes_count' => '总票数',
            'allow_anonymous' => '允许匿名投票',
            'allow_view_results' => '允许查看结果',
            'allow_multiple_choice' => '允许多选',
            'interval_seconds' => '间隔时间',
            'ordering' => '排序',
            'enabled' => '激活',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
            'voting_result' => '投票结果',
        ];
    }

    public function getFriendlyVotingResult()
    {
        $formatter = Yii::$app->getFormatter();
        $output = '<ul class="vote-friendly-result">';
        $totalCount = 0;
        $options = $this->options;
        foreach ($options as $option) {
            $totalCount += $option['votes_count'];
        }
        foreach ($options as $item) {
            $percent = $totalCount ? $formatter->asPercent($item['votes_count'] / $totalCount, 2) : '0%';
            $output .= '<li class="clearfix"><div class="option">' . $item['title'] . '</div><div class="bars"><div class="bar"><div class="percent" style="width: ' . $percent . '"></div></div><div class="data">' . $percent . '</div></div><div class="counter">' . $item['votes_count'] . '</div></li>';
        }

        return $output . '</ul>';
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

    // Events
    public $voting_result;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->voting_result = $this->getFriendlyVotingResult();
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = \Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                $this->updated_by = \Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        \Yii::$app->getDb()->createCommand()->delete('{{%vote_option}}', ['vote_id' => $this->id])->execute();
    }

}
