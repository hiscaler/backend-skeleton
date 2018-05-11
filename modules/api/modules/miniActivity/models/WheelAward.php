<?php

namespace app\modules\api\modules\miniActivity\models;

use app\modules\api\extensions\UtilsHelper;

/**
 * This is the model class for table "{{%mini_activity_wheel_award}}".
 *
 * @property int $id
 * @property int $wheel_id 转盘 id
 * @property int $ordering 排序
 * @property string $title 名称
 * @property string $description 描述
 * @property string $photo 奖品图片
 * @property int $total_quantity 总奖品数量
 * @property int $remaining_quantity 剩余奖品数量
 * @property int $enabled 激活
 */
class WheelAward extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mini_activity_wheel_award}}';
    }

    public function fields()
    {
        return [
            'id',
            'ordering',
            'title',
            'description',
            'photo' => function () {
                $photo = $this->photo;

                return $photo ? UtilsHelper::fixStaticAssetUrl($photo) : null;
            },
            'totalQuantity' => 'total_quantity',
            'remainingQuantity' => 'remaining_quantity',
        ];
    }

}
