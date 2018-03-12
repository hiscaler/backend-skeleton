<?php

use yii\db\Migration;

/**
 * 站点统计数据
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180312_030534_create_access_statistic_site_log_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access_statistic_site_log}}', [
            'id' => $this->primaryKey(),
            'site_id' => $this->integer()->notNull()->comment('所属站点'),
            'ip' => $this->string(15)->notNull()->comment('IP 地址'),
            'referrer' => $this->string()->notNull()->comment('来源'),
            'access_datetime' => $this->integer()->notNull()->comment('访问时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_statistic_site_log}}');
    }
}
