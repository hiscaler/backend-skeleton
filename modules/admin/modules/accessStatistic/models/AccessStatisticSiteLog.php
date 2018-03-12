<?php

namespace app\modules\admin\modules\accessStatistic\models;

/**
 * This is the model class for table "{{%access_statistic_site_log}}".
 *
 * @property int $id
 * @property int $site_id 所属站点
 * @property string $ip IP 地址
 * @property string $referrer 来源
 * @property string $browser 浏览器
 * @property string $browser_lang 浏览器语言
 * @property string $os 操作系统
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
            [['referrer', 'browser'], 'string', 'max' => 255],
            [['browser_lang', 'os'], 'string', 'max' => 20],
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
            'browser' => '浏览器',
            'browser_lang' => '浏览器语言',
            'os' => '操作系统',
            'access_datetime' => '访问时间',
        ];
    }

    public function getSite()
    {
        return $this->hasOne(AccessStatisticSite::class, ['id' => 'site_id']);
    }

}
