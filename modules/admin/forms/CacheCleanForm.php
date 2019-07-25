<?php

namespace app\modules\admin\forms;

use yii\base\Model;

/**
 * 缓存清理表单
 *
 * @package app\modules\admin\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class CacheCleanForm extends Model
{

    const TYPE_SYSTEM = 'system';
    const TYPE_ALL = 'all';

    public $type = self::TYPE_SYSTEM;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type'], 'required'],
            ['type', 'in', 'range' => array_keys(self::typeOptions())],
        ]);
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_SYSTEM => '系统缓存',
            self::TYPE_ALL => '全部缓存',
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '清理对象',
        ];
    }

}
