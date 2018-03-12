<?php

namespace app\modules\admin\modules\accessStatistic\models;

use app\models\BaseActiveRecord;

/**
 * This is the model class for table "{{%access_statistic_site}}".
 *
 * @property int $id
 * @property string $name 站点名称
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class AccessStatisticSite extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_statistic_site}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['enabled'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '站点名称',
            'enabled' => '激活',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

}
