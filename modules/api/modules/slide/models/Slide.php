<?php

namespace app\modules\api\modules\slide\models;

use app\models\BaseActiveRecord;
use app\models\FileUploadConfig;
use app\models\Lookup;
use app\modules\admin\components\ApplicationHelper;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;
use yii\web\YiiAsset;

/**
 * This is the model class for table "{{%slide}}".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $url
 * @property string $url_open_target
 * @property string $picture_path
 * @property integer $ordering
 * @property integer $enabled
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Slide extends BaseActiveRecord
{

    /**
     * 链接打开方式
     */
    const URL_OPEN_TARGET_BLANK = '_blank';
    const URL_OPEN_TARGET_SLFE = '_self';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%slide}}';
    }

    public function fields()
    {
        return [
            'id',
            'categoryId' => 'category_id',
            'title',
            'url',
            'urlOpenTarget' => 'url_open_target',
            'picturePath' => function () {
                return $this->picture_path ? Yii::$app->getRequest()->getHostInfo() . $this->picture_path : null;
            },
            'ordering',
        ];
    }

}
