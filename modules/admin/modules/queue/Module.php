<?php

namespace app\modules\admin\modules\queue;

use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;

/**
 * `queue` module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\queue\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'queue' => [
                'class' => Queue::class,
                'db' => 'db', // DB connection component or its config
                'tableName' => '{{%queue}}', // Table name
                'channel' => 'default', // Queue channel key
                'mutex' => MysqlMutex::class, // Mutex that used to sync queries
            ],
        ]);
    }
}
