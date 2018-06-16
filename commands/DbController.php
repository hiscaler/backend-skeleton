<?php

namespace app\commands;

use app\models\Option;
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
     * 生成数据表字典
     *
     * @param null $path 保存路径
     * @param bool $coreTables 是否只生成核心表数据词典
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     */
    public function actionGenerateDict($path = null, $coreTables = true)
    {
        if ($path) {
            $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . trim($path, '\/');
        } else {
            $path = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'db-dict';
        }

        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $tableHeader = [
            ['序号', '字段名称', '类型', '长度', '允许 NULL', '默认值', '备注'],
            [':---:', '---', '---', ':---:', ':---:', ':---:', '---']
        ];
        $db = \Yii::$app->getDb();
        $schema = $db->getSchema();
        $tablePrefix = $db->tablePrefix;
        $tables = $coreTables ? Option::coreTables(true) : $schema->getTableNames();
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
            $doc = "**$table**" . PHP_EOL;
            $doc .= "---" . PHP_EOL;
            $columnWidths = [];
            foreach ($tableRows as $row) {
                foreach ($row as $key => $column) {
                    $width = mb_strlen($column);
                    if (isset($columnWidths[$key])) {
                        if ($width > $columnWidths[$key]) {
                            $columnWidths[$key] = $width;
                        }
                    } else {
                        $columnWidths[$key] = $width;
                    }
                }
            }

            $tableRows = array_merge($tableHeader, $tableRows);
            foreach ($tableRows as $i => $row) {
                if ($i > 1) {
                    foreach ($row as $key => $item) {
                        if (mb_strlen($item) < $columnWidths[$key]) {
                            $row[$key] = str_pad($item, $columnWidths[$key], ' ', $key == 0 ? STR_PAD_LEFT : STR_PAD_RIGHT);
                        }
                    }
                }

                $doc .= '| ' . implode(' | ', $row) . ' | ' . PHP_EOL;
            }

            file_put_contents($path . DIRECTORY_SEPARATOR . str_replace($tablePrefix, '', $table) . ".md", $doc);
        }
        echo "Done.";
    }

}
