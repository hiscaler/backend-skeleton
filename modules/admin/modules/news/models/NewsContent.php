<?php

namespace app\modules\admin\modules\news\models;

use Yii;

/**
 * This is the model class for table "{{%news_content}}".
 *
 * @property int $news_id 资讯 id
 * @property string $content 正文
 */
class NewsContent extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news_content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['news_id'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'news_id' => '资讯 id',
            'content' => '正文',
        ];
    }
}
