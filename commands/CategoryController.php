<?php

namespace app\commands;

use app\models\Category;
use yadjet\helpers\StringHelper;
use Yii;
use yii\console\Exception;
use yii\helpers\FileHelper;

/**
 * 分类数据处理
 *
 * @package app\commands
 * @author hiscaler <hiscaler@gmail.com>
 */
class CategoryController extends Controller
{

    /**
     * 导入分类数据
     * yii category/import 1 a:aa,b:bb
     *
     * @param $parentId
     * @param array $replacePairs
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionImport($parentId, array $replacePairs = [])
    {
        $filePath = FileHelper::normalizePath(__DIR__ . '/data/category.txt');
        if (!file_exists($filePath)) {
            throw new Exception(sprintf("`%s` 文件不存在。", $filePath));
        }
        $this->stdout("Begin..." . PHP_EOL);
        $pairs = [];
        foreach ($replacePairs as $item) {
            if (stripos($item, ":") !== false) {
                list($k, $v) = explode(":", $item);
                $k = StringHelper::removeSpace($k);
                $k && $pairs[$k] = StringHelper::removeSpace($v);
            }
        }
        $db = Yii::$app->getDb();
        $rows = file($filePath);
        $pid = null;
        $name = null;
        $categoryIdCmd = $db->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[parent_id]] = :parentId AND [[name]] = :name');
        foreach ($rows as $i => $row) {
            $pid = $parentId;
            $row = StringHelper::removeSpace($row);
            $this->stdout("当前正在处理第 " . ($i + 1) . " 条数据 [ $row ]" . PHP_EOL);
            foreach (explode("\t", $row) as $name) {
                $name = StringHelper::removeSpace($name);
                $name && $pairs && $name = strtr($name, $pairs);
                if (!$name) {
                    continue;
                }
                $msg = " > $name";
                $errorMessages = [];
                $id = $categoryIdCmd->bindValues([':parentId' => $pid, ':name' => $name])->queryScalar();
                if ($id) {
                    $msg .= " (Nothing)";
                    $pid = $id;
                } else {
                    // Insert
                    $category = new Category();
                    $category->loadDefaultValues();
                    $category->name = $name;
                    $category->parent_id = $pid;
                    if (!$category->validate()) {
                        foreach ($category->getErrors() as $attr => $error) {
                            if ($attr == 'alias') {
                                $n = strlen($category->alias);
                                $category->alias .= mt_rand($n, $n + 100);
                                break;
                            }
                        }
                    }
                    if ($category->save()) {
                        $msg .= "(Successful)";
                        $pid = $category->id;
                    } else {
                        foreach ($category->getErrors() as $error) {
                            $errorMessages[] = $error[0];
                        }
                        $msg .= "(Fail)";
                    }
                }
                $this->stdout($msg . PHP_EOL);
                if ($errorMessages) {
                    $this->stderr(implode(PHP_EOL, $errorMessages) . PHP_EOL);
                    throw new Exception("保存数据失败。");
                }
            }
        }
        $this->stdout("Done." . PHP_EOL);
    }

}
