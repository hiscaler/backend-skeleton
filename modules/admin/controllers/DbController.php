<?php

namespace app\modules\admin\controllers;

use app\models\Option;
use DateTime;
use Exception;
use Yii;
use yii\base\ErrorException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 数据库管理
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DbController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'backup', 'restore', 'delete', 'clean'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'backup' => ['post'],
                    'restore' => ['post'],
                    'delete' => ['post'],
                    'clean' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 备份历史记录
     *
     * @rbacDescription 备份历史记录查看权限
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $histories = FileHelper::findDirectories(Yii::getAlias('@app/backup'));
        rsort($histories);
        foreach ($histories as $key => $history) {
            $name = basename($history, '.bak');
            $histories[$key] = [
                'name' => $name,
                'date' => (new DateTime($name))->format('Y-m-d H:i:s'),
            ];
        }

        return $this->render('index', [
            'histories' => $histories,
        ]);
    }

    /**
     * 数据库备份
     *
     * @rbacDescription 数据库备份权限
     *
     * @return string
     * @throws \yii\base\ErrorException
     */
    public function actionBackup()
    {
        $backupDir = date('YmdHis');
        $backupPath = Yii::getAlias('@app/backup/' . $backupDir);
        try {
            ignore_user_abort(true);
            ini_set('memory_limit', -1);
            ini_set('max_execution_time', 0);
            if (!file_exists($backupDir)) {
                FileHelper::createDirectory($backupPath);
            }
            $pageSize = 500;
            $db = Yii::$app->getDb();
            $tablePrefix = $db->tablePrefix;
            $tables = Option::tables();
            $processTablesCount = $processRowsCount = 0;
            foreach ($tables as $table) {
                $totalCount = $db->createCommand("SELECT COUNT(*) FROM $table")->queryScalar();
                $processTablesCount += 1;
                if (!$totalCount) {
                    continue;
                }
                $totalPages = (int) (($totalCount + $pageSize - 1) / $pageSize);
                for ($page = 1; $page <= $totalPages; $page++) {
                    $data = (new Query())
                        ->from($table)
                        ->offset(($page - 1) * $pageSize)
                        ->limit($pageSize)
                        ->all();
                    $processRowsCount += count($data);
                    $data = gzcompress(serialize([
                        'table' => str_replace($tablePrefix, '', $table),
                        'data' => $data,
                    ]), 9);
                    file_put_contents($backupPath . DIRECTORY_SEPARATOR . "$table-$page.bak", $data);
                }
            }

            return new Response([
                'format' => Response::FORMAT_JSON,
                'data' => [
                    'success' => true,
                    'data' => [
                        'processTablesCount' => $processTablesCount,
                        'processRowsCount' => $processRowsCount,
                    ]
                ]
            ]);
        } catch (Exception $e) {
            FileHelper::removeDirectory($backupPath);

            return new Response([
                'format' => Response::FORMAT_JSON,
                'data' => [
                    'success' => false,
                    'error' => [
                        'message' => $e->getMessage(),
                    ]
                ]
            ]);
        }
    }

    /**
     * 恢复数据库数据
     *
     * @rbacDescription 数据库恢复权限
     *
     * @param $name
     * @throws NotFoundHttpException
     * @throws \yii\base\NotSupportedException
     */
    public function actionRestore($name)
    {
        ignore_user_abort(true);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $path = Yii::getAlias("@app/backup/$name");
        if (file_exists($path)) {
            $db = Yii::$app->getDb();
            $cmd = $db->createCommand();
            $tablePrefix = $db->tablePrefix;
            $tables = Option::tables();
            $files = FileHelper::findFiles($path);
            $currentTable = null;
            $transaction = $db->beginTransaction();
            try {
                foreach ($files as $file) {
                    $data = file_get_contents($file);
                    if ($data !== false) {
                        $rawData = gzuncompress($data);
                        if ($rawData !== false) {
                            $rawData = unserialize($rawData);
                            $table = $tablePrefix . $rawData['table'];
                            if (!in_array($table, $tables)) {
                                continue;
                            }

                            if ($currentTable != $table) {
                                $tableSchema = $db->getTableSchema($table);
                                if ($tableSchema->foreignKeys) {
                                    $cmd->delete($table)->execute();
                                } else {
                                    $cmd->truncateTable($table)->execute();
                                }
                            }
                            $rows = $rawData['data'];
                            if (!$rows) {
                                continue;
                            }
                            $cmd->batchInsert($table, array_keys($rows[0]), $rows)->execute();
                        } else {
                            throw new \Exception(basename($file) . ' 文件读取失败。');
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->getSession()->setFlash('notice', "$name 备份恢复成功。");
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('notice', "$name 备份恢复失败，失败原因：" . $e->getMessage());
            }
            $this->redirect(['index']);
        } else {
            throw new NotFoundHttpException("$name 备份不存在。");
        }
    }

    /**
     * 删除数据库备份
     *
     * @rbacDescription 数据库备份删除权限
     *
     * @param $name
     * @throws NotFoundHttpException
     * @throws \yii\base\ErrorException
     */
    public function actionDelete($name)
    {
        ignore_user_abort(true);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $path = Yii::getAlias("@app/backup/$name");
        if (file_exists($path)) {
            FileHelper::removeDirectory($path);
        } else {
            throw new NotFoundHttpException("$name 备份不存在。");
        }
        $this->redirect(['index']);
    }

    /**
     * 清理掉所有备份
     *
     * @rbacDescription 数据库备份清理权限
     *
     * @throws NotFoundHttpException
     * @throws \yii\base\ErrorException
     */
    public function actionClean()
    {
        ignore_user_abort(true);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        $path = Yii::getAlias("@app/backup");
        if (file_exists($path)) {
            try {
                foreach (FileHelper::findDirectories($path) as $dir) {
                    FileHelper::removeDirectory($dir);
                }
                Yii::$app->getResponse()->setStatusCode(200);
            } catch (\Exception $e) {
                throw new ErrorException($e->getMessage());
            }
        } else {
            throw new NotFoundHttpException("$path 备份目录不存在。");
        }
    }

}