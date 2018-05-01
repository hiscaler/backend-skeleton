<?php

namespace app\modules\admin\modules\vote\models;

use Yii;

/**
 * This is the model class for table "{{%vote_option}}".
 *
 * @property int $id
 * @property int $vote_id 投票 id
 * @property int $ordering 排序
 * @property string $title 名称
 * @property string $description 描述
 * @property string $photo 图片
 * @property int $votes_count 票数
 * @property int $enabled 激活
 */
class VoteOption extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vote_option}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vote_id', 'title'], 'required'],
            [['vote_id', 'ordering', 'votes_count'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 60],
            [['photo'], 'string', 'max' => 100],
            [['enabled'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vote_id' => '投票 id',
            'ordering' => '排序',
            'title' => '名称',
            'description' => '描述',
            'photo' => '图片',
            'votes_count' => '票数',
            'enabled' => '激活',
        ];
    }

    /**
     * 所属投票
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVote()
    {
        return $this->hasOne(Vote::class, ['id' => 'vote_id']);
    }

}
