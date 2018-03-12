<?php

namespace app\modules\admin\modules\accessStatistic\models;

use Yii;

/**
 * This is the model class for table "{{%access_statistic_site_log}}".
 *
 * @property int $id
 * @property int $site_id 所属站点
 * @property string $ip IP 地址
 * @property string $referrer 来源
 * @property int $access_datetime 访问时间
 */
class AccessStatisticSiteLog extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_statistic_site_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'ip', 'referrer', 'access_datetime'], 'required'],
            [['site_id', 'access_datetime'], 'integer'],
            [['ip'], 'string', 'max' => 15],
            [['referrer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_id' => '所属站点',
            'ip' => 'IP 地址',
            'referrer' => '来源',
            'access_datetime' => '访问时间',
        ];
    }

    public function getSite()
    {
        return $this->hasOne(AccessStatisticSite::class, ['id' => 'site_id']);
    }

}
