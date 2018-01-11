<?php

use yii\db\Migration;

/**
 * Handles the creation for table `file_upload_config`.
 */
class m160808_122553_create_file_upload_config_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%file_upload_config}}', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull()->defaultValue(0)->comment('类型'),
            'model_name' => $this->string(60)->notNull()->comment('模型名称'),
            'attribute' => $this->string(60)->notNull()->comment('表字段名'),
            'extensions' => $this->string(60)->notNull()->comment('允许的文件后缀'),
            'min_size' => $this->integer()->notNull()->defaultValue(1)->comment('最小尺寸'),
            'max_size' => $this->integer()->notNull()->defaultValue(200)->comment('最大尺寸'),
            'thumb_width' => $this->smallInteger()->comment('缩略图宽度'),
            'thumb_height' => $this->smallInteger()->comment('缩略图高度'),
            'created_by' => $this->integer()->notNull()->comment('添加人'),
            'created_at' => $this->integer()->notNull()->comment('添加时间'),
            'updated_by' => $this->integer()->notNull()->comment('更新人'),
            'updated_at' => $this->integer()->notNull()->comment('更新时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%file_upload_config}}');
    }

}
