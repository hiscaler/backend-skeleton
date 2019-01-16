<?php

namespace app\modules\api\modules\link\models;

use app\models\BaseActiveRecord;
use app\models\Category;
use Yii;

/**
 * This is the model class for table "{{%link}}".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property int $type 类型
 * @property string $title 标题
 * @property string $description 描叙
 * @property string $url URL
 * @property string $url_open_target 链接打开方式
 * @property string $logo Logo
 * @property int $ordering 排序
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Link extends BaseActiveRecord
{

    /**
     * 友情链接类型
     */
    const TYPE_TEXT = 0;
    const TYPE_PICTURE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%link}}';
    }

    public function fields()
    {
        return [
            'id',
            'categoryId' => 'category_id',
            'categoryName' => function ($model) {
                $name = null;
                if ($model->category_id && $model->category) {
                    $name = $model->category->name;
                }

                return $name;
            },
            'type',
            'title',
            'description',
            'url',
            'urlOpenTarget' => 'url_open_target',
            'logo' => function ($model) {
                return $model->logo ? Yii::$app->getRequest()->getHostInfo() . $model->logo : null;
            }
        ];
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

}
