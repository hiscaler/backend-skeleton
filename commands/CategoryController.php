<?php

namespace app\commands;

use app\models\Category;
use yadjet\helpers\StringHelper;
use Yii;
use yii\console\Exception;

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
     *
     * @param $parentId
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionImport($parentId)
    {
        $this->stdout("Begin..." . PHP_EOL);
        $db = Yii::$app->getDb();
        $rows = file(__DIR__ . '/data/category.txt');
        foreach ($rows as $i => $row) {
            $pid = $parentId;
            $row = StringHelper::removeSpace($row);
            $this->stdout("当前正在处理第 " . ($i + 1) . " 条数据 [ $row ]" . PHP_EOL);
            $names = explode("\t", $row);
            foreach ($names as $name) {
                $name = StringHelper::removeSpace($name);
                if (!$name) {
                    continue;
                }
                $msg = " > $name";
                $errorMessages = [];
                $id = $db->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[parent_id]] = :parentId AND [[name]] = :name', [
                    ':parentId' => $pid,
                    ':name' => $name
                ])->queryScalar();
                if ($id) {
                    $msg .= " (Nothing)";
                    $pid = $id;
                } else {
                    // Insert
                    $category = new Category();
                    $category->loadDefaultValues();
                    $category->name = $name;
                    $category->parent_id = $pid;
                    $category->validate() || $category->alias .= mt_rand(0, 10);
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
