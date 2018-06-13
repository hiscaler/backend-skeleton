<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

/**
 * 数据库操作
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DbController extends Controller
{

    /**
     * 生成数据字典
     *
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     */
    public function actionGenerateDict()
    {
        $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'db-dict';
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $tableHeader = [
            ['序号', '字段名称', '类型', '长度', '允许 NULL', '默认值', '备注'],
            [':---:', '---', '---', ':---:', ':---:', ':---:', '---']
        ];
        $db = \Yii::$app->getDb();
        $schema = $db->getSchema();
        $tables = $schema->getTableNames();

        foreach ($tables as $table) {
            echo "Generate $table table dict..." . PHP_EOL;
            $tableSchema = $schema->getTableSchema($table, true);
            $tableRows = [];
            $i = 1;
            foreach ($tableSchema->columns as $column) {
                $tableRows[] = [
                    $i,
                    $column->name,
                    $column->type,
                    $column->size,
                    $column->allowNull ? 'Y' : 'N',
                    $column->defaultValue,
                    $column->comment,
                ];
                $i += 1;
            }
            $doc = '';
            $tableRows = array_merge($tableHeader, $tableRows);
            foreach ($tableRows as $row) {
                $doc .= '| ' . implode(' | ', $row) . ' | ' . PHP_EOL;
            }

            file_put_contents($path . DIRECTORY_SEPARATOR . str_replace($db->tablePrefix, '', $table) . ".md", $doc);
        }
        echo "Done.";
    }

}
