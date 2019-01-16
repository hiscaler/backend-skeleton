<?php

namespace app\modules\api\modules\vote\models;

use app\modules\api\extensions\UtilsHelper;

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

    public function fields()
    {
        return [
            'id',
            'ordering',
            'title',
            'description',
            'photo' => function ($model) {
                $photo = $model->photo;

                return $photo ? UtilsHelper::fixStaticAssetUrl($photo) : null;
            },
            'votes_count',
        ];
    }

}
