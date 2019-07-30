<?php

namespace app\modules\admin\components;

use Yii;
use yii\grid\DataColumn;

/**
 * 改进后的 GridView，支持根据设定显示需要的列
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class GridView extends \yii\grid\GridView
{

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->tableOptions += ['data-models' => base64_encode(serialize($this->dataProvider->getModels()))];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        $invisibleColumns = $this->invisibleColumns();
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::class,
                    'grid' => $this
                ], $column));
            }

            $attribute = false;
            if ($column->hasProperty('attribute')) {
                $attribute = $column->attribute;
            }
            if (!$column->visible || ($attribute && in_array($attribute, $invisibleColumns))) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    /**
     * 返回所有不可见的项目
     *
     * @return array
     * @throws \yii\db\Exception
     */
    private function invisibleColumns()
    {
        $columns = [];
        $configs = $this->getColumnConfigs();
        foreach ($configs as $config) {
            if (!$config['visible']) {
                $columns[] = $config['attribute'];
            }
        }

        return $columns;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    private function getColumnConfigs()
    {
        return Yii::$app->getDb()->createCommand('SELECT [[name]], [[attribute]], [[css_class]], [[visible]] FROM {{%grid_column_config}} WHERE [[name]] = :name AND [[user_id]] = :userId', [
            ':name' => $this->id,
            ':userId' => Yii::$app->getUser()->getId(),
        ])->queryAll();
    }

}
