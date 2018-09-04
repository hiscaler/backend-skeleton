<?php

namespace app\commands;

use app\models\Option;
use Exception;
use Yii;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * 数据库操作
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DbController extends Controller
{

    const PAGE_SIZE = 500;

    public $helpMessages = <<<EOT
Usage: ./yii db/generate-dict
EOT;

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
        $this->stdout("Begin ..." . PHP_EOL);
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
        $tables = $coreTables ? Option::coreTables(true) : $schema->getTableNames('', true);
        foreach ($tables as $table) {
            if (str_replace($tablePrefix, '', $table) == 'migration') {
                continue;
            }
            $this->stdout("Generate $table table dict..." . PHP_EOL);
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
        $this->stdout("Done.");
    }

    /**
     * 数据同步
     *
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     * @throws \yii\console\Exception
     */
    public function actionSync()
    {
        $this->stdout("Begin Sync data..." . PHP_EOL);
        try {
            $fromDb = \Yii::$app->getDb();
        } catch (\Exception $e) {
            throw new \yii\console\Exception($e->getMessage());
        }
        /** @var $toDb yii\db\Connection */
        $toDb = \Yii::$app->toDB;
        $toCmd = $toDb->createCommand();
        $schema = $fromDb->getSchema();
        $tables = $schema->getTableNames();
        $toDb->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();
        foreach ($tables as $table) {
            $toCmd->truncateTable($table)->execute();
            $query = (new Query())
                ->from($table);
            $count = $query->count();
            $totalPages = (int) (($count + self::PAGE_SIZE - 1) / self::PAGE_SIZE);
            Console::startProgress(0, $totalPages, "$table ");
            for ($page = 1; $page <= $totalPages; $page++) {
                Console::updateProgress($page, $totalPages);
                $items = $query->limit(self::PAGE_SIZE)->offset(($page - 1) * self::PAGE_SIZE)->all();
                $toCmd->batchInsert($table, array_keys($items[0]), $items)->execute();
            }
            Console::endProgress();
        }
        $toDb->createCommand("SET FOREIGN_KEY_CHECKS = 1")->execute();
        $this->stdout("Done.");
    }

}
