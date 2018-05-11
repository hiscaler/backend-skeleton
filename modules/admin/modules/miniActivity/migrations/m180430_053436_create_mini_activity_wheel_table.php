<?php

use yii\db\Migration;

/**
 * 大转盘
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class m180430_053436_create_mini_activity_wheel_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mini_activity_wheel}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(100)->notNull()->comment('活动名称'),
            'win_message' => $this->string()->notNull()->comment('中奖消息'),
            'get_award_message' => $this->string()->notNull()->comment('兑奖消息'),
            'begin_datetime' => $this->integer()->notNull()->comment('开始时间'),
            'end_datetime' => $this->integer()->notNull()->comment('结束时间'),
            'description' => $this->text()->comment('活动说明'),
            'photo' => $this->string(100)->comment('活动预览图片'),
            'repeat_play_message' => $this->string()->notNull()->comment('重复抽奖提示信息'),
            'background_image' => $this->string(100)->comment('背景图片'),
            'background_image_repeat_type' => $this->string(20)->comment('背景类型'),
            'finished_title' => $this->string(100)->notNull()->comment('活动结束公告主题'),
            'finished_description' => $this->text()->comment('活动结束说明'),
            'finished_photo' => $this->string(100)->comment('活动结束图片'),
            'estimated_people_count' => $this->integer()->notNull()->defaultValue(0)->comment('预计活动人数'),
            'actual_people_count' => $this->integer()->notNull()->defaultValue(0)->comment('实际活动人数'),
            'play_times_per_person' => $this->smallInteger()->notNull()->defaultValue(1)->comment('每人抽奖总次数'),
            'play_limit_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('抽奖限制规则'),
            'play_times_per_person_by_limit_type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('抽奖限制规则次数'),
            'win_times_per_person' => $this->smallInteger()->notNull()->defaultValue(1)->comment('每人中奖次数'),
            'win_interval_seconds' => $this->integer()->notNull()->defaultValue(0)->comment('每人每次中奖时间间隔'),
            'blocks_count' => $this->smallInteger()->notNull()->defaultValue(12)->comment('区块数量'),
            'show_awards_quantity' => $this->boolean()->notNull()->defaultValue(0)->comment('显示奖品数量'),
            'ordering' => $this->integer()->notNull()->defaultValue(1)->comment('排序'),
            'enabled' => $this->boolean()->notNull()->defaultValue(1)->comment('激活'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mini_activity_wheel}}');
    }
}
