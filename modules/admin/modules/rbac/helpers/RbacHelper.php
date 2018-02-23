<?php

namespace app\modules\admin\modules\rbac\helpers;

trait RbacHelper
{

    /**
     * 默认设置
     *
     * @var array
     */
    private $defaultModuleOptions = [
        'userTable' => [
            'name' => '{{%user}}', // 查询的用户表
            'columns' => [
                'id' => 'id', // 主键
                'username' => 'username', // 用户名
                /**
                 * 扩展字段（数据库字段名称 => 显示名称）
                 *
                 * [
                 *     'nickname' => '昵称',
                 *     'email' => '邮箱',
                 * ]
                 */
                'extra' => [],
            ],
            'where' => [], // 查询条件
        ],
        'disabledScanModules' => ['gii', 'debug', 'api'], // 禁止扫描的模块
        'selfish' => true, // 是否只显示当前应用的相关数据
    ];

    /**
     * 获取模块设置
     *
     * @return array
     * @throws \yii\base\InvalidCallException
     */
    public function getModuleOptions()
    {
        if ($this instanceof \yii\web\Controller) {
            $options = property_exists($this->module, 'options') ? $this->module->options : [];

            return \yii\helpers\ArrayHelper::merge($this->defaultModuleOptions, $options);
        } else {
            throw new \yii\base\InvalidCallException(get_class($this) . ' must is a \yii\web\Controller instance.');
        }
    }
}