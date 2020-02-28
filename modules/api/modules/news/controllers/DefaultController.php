<?php

namespace app\modules\api\modules\news\controllers;

use app\models\Category;
use app\modules\api\extensions\AppHelper;
use app\modules\api\extensions\BaseController;
use app\modules\api\models\Constant;
use app\modules\api\modules\news\models\News;
use app\modules\api\modules\news\models\NewsContent;
use Exception;
use GuzzleHttp\Client;
use RuntimeException;
use yadjet\helpers\ImageHelper;
use yadjet\helpers\StringHelper;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * /api/news/default
 *
 * @package app\modules\api\modules\news\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public $modelClass = 'app\modules\api\modules\news\models\News';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['delete']);

        return $actions;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if (!$this->debug) {
            if ($this->action->id == 'view') {
                $condition = '[[id]] = :id';
                $bindValues[':id'] = (int) Yii::$app->getRequest()->get('id');
            } else {
                $condition = '';
                $bindValues = [];
            }
            $sql = 'SELECT [[updated_at]] FROM {{%news}}';
            if (!empty($condition)) {
                $sql .= " WHERE $condition";
            }
            $sql .= ' ORDER BY [[updated_at]] DESC LIMIT 1';
            $cmd = Yii::$app->getDb()->createCommand($sql)->bindValues($bindValues);
            if ($this->dbCacheTime !== null) {
                $cmd->cache($this->dbCacheTime);
            }
            $timestamp = $cmd->queryScalar();
            $behaviors = array_merge($behaviors, [
                [
                    'class' => 'yii\filters\HttpCache',
                    'lastModified' => function () use ($timestamp) {
                        return $timestamp;
                    },
                    'etagSeed' => function () use ($timestamp) {
                        return $timestamp;
                    }
                ],
            ]);
        }

        return $behaviors;
    }

    /**
     * 解析查询条件
     *
     * @param string $fields
     * @param string $title
     * @param string $category
     * @param string $children
     * @param string $label
     * @param string $picture
     * @param string $date
     * @param string $author
     * @param string $keywords
     * @param string $reject
     * @param string $combinationMethod
     * @param string $orderBy
     * @param integer $offset
     * @param integer $limit
     * @return Query
     * @throws \yii\db\Exception
     */
    private function parserQuery($fields = null, $title = null, $category = null, $children = 'n', $label = null, $picture = null, $date = null, $author = null, $keywords = null, $reject = null, $combinationMethod = 'and', $orderBy = null, $offset = null, $limit = null)
    {
        // Basic condition
        $condition = [
            't.enabled' => Constant::BOOLEAN_TRUE,
        ];
        $selectColumns = AppHelper::filterQuerySelectColumns(['t.id', 't.category_id', 'c.name AS category_name', 't.title', 't.short_title', 't.author', 't.source', 't.keywords', 't.description', 't.is_picture_news', 't.picture_path', 't.enabled_comment', 'comments_count', 't.published_at', 't.created_at', 't.updated_at', 'u.nickname AS editor'], $fields, ['short_title' => 't.title']);
        $query = (new \yii\db\ActiveQuery(News::class))
            ->alias('t')
            ->select($selectColumns);
        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', '[[t.category_id]] = [[c.id]]');
        }
        if (in_array('u.nickname AS editor', $selectColumns)) {
            $query->leftJoin('{{%user}} u', 't.updated_by = u.id');
        }

        $query->offset($offset)->limit($limit);

        // 根据标题搜索
        if ($title) {
            $condition = ['AND', $condition, ['LIKE', 't.title', $title]];
        }

        // Picture news
        $picture = AppHelper::cleanString($picture);
        if (!empty($picture) && in_array($picture, ['y', 'n'])) {
            $condition['t.is_picture_news'] = $picture == 'y' ? Constant::BOOLEAN_TRUE : Constant::BOOLEAN_FALSE;
        }

        // Category condition
        $category = AppHelper::cleanString($category);
        if (!empty($category)) {
            // 1,2,3表示获取 1,2,3 节点的数据
            if (($index = strpos($category, '!')) === false) {
                $includeCategoryIds = $category;
                $excludeCategoryIds = '';
            } else {
                // !1,2,3表示排除 1,2,3 节点的数据
                if ($index == 0) {
                    // 如果“!”是第一个，则后续的字符全部为拒绝返回的节点集合
                    $includeCategoryIds = '';
                    $excludeCategoryIds = substr($category, 1);
                } else {
                    $includeCategoryIds = substr($category, 0, $index);
                    $excludeCategoryIds = substr($category, $index + 1);
                }
            }
            $includeCategoryIds = $includeCategoryIds ? array_unique(explode(',', $includeCategoryIds)) : [];
            if ($includeCategoryIds) {
                if (in_array($children = strtolower($children), ['y', 'n']) && $children == 'y') {
                    // 包含子栏目
                    $childrenCategoryIds = [];
                    foreach ($includeCategoryIds as $categoryId) {
                        $childrenCategoryIds = array_merge($childrenCategoryIds, Category::getChildrenIds($categoryId));
                    }
                    $includeCategoryIds = array_unique(array_merge($includeCategoryIds, $childrenCategoryIds));
                }
                $condition = ['AND', $condition, ['t.category_id' => $includeCategoryIds]];
            }
            $excludeCategoryIds = $excludeCategoryIds ? array_unique(explode(',', $excludeCategoryIds)) : [];
            if ($excludeCategoryIds) {
                $condition = ['AND', $condition, ['NOT IN', 't.category_id', $excludeCategoryIds]];
            }
        }

        // Label condition
        if (!empty($label)) {
            $attributeIdList = (new Query())->select('id')->from('{{%label}}')->where([
                'alias' => explode(',', $label)
            ])->column();
            if ($attributeIdList) {
                $subQuery = (new Query())
                    ->select('entity_id')
                    ->from('{{%entity_label}}')
                    ->where('[[t.id]] = [[entity_id]] AND [[model_name]] = :modelName', [':modelName' => \app\modules\admin\modules\news\models\News::class])
                    ->andWhere(['IN', 'label_id', $attributeIdList])
                    ->groupBy('entity_id')
                    ->having('COUNT(*) = ' . count($attributeIdList));
                $combinationMethod = strtolower(trim($combinationMethod)) == 'or' ? 'OR' : 'AND';
                $condition = $condition ? [$combinationMethod, $condition, ['EXISTS', $subQuery]] : ['EXISTS', $subQuery];
            } else {
                // 推送位设置有误的情况下，不返回任何数据
                $condition = '0 = 1';
            }
        }

        if ($condition != '0 = 1') {
            if ($date && preg_match("/[-\+]?\d{1,8}$/", $date)) {
                $len = strlen($date);
                if ($len > 1 && $len < 10) {
                    switch ($len) {
                        case 2: // +3（返回最近三天的文章）
                            $days = substr($date, -1);
                            $datetime = new \Datetime();
                            $endDatetime = $datetime->getTimestamp();
                            $datetime->modify('-' . $days . ' day' . ($days > 1 ? 's' : ''));
                            $datetime->setTime(0, 0, 0);
                            $condition = ['AND', $condition, ['BETWEEN', 't.published_at', $datetime->getTimestamp(), $endDatetime]];
                            break;

                        case 4: // 2015（返回2015年的文章）
                        case 6: // 201501（返回2015年1月份的文章）
                        case 8: // 20150101（返回2015年1月1日的文章）
                            $dateRange = AppHelper::parseDate($date);
                            if ($dateRange) {
                                $condition = ['AND', $condition, ['BETWEEN', 't.published_at', $dateRange[0], $dateRange[1]]];
                            }
                            break;

                        case 5: // -2015（返回2015年之前的文章）, +2015（返回2015年之后的文章）
                        case 7: // -201501（返回2015年1月份之前的文章）, +201501（返回2015年1月份之后的文章）
                        case 9: // -20150101（返回2015年1月1日之前的文章）, +20150101（返回2015年1月1日之后的文章）
                            if (in_array($date[0], ['-', '+'])) {
                                $dateRange = AppHelper::parseDate(substr($date, -($len - 1)));
                                if ($dateRange) {
                                    $condition = ['AND', $condition, [$date[0] === '-' ? '<' : '>', 't.published_at', $dateRange[0]]];
                                }
                            }
                            break;

                        default:
                            break;
                    }
                }
            }

            // 根据具体的作者搜索
            if (!empty($author)) {
                $condition = ['AND', $condition, ['t.author' => $author]];
            }

            if (!empty($keywords)) {
                $condition = ['AND', $condition, ['LIKE', 't.keywords', $keywords]];
            }

            // 拒绝返回的数据
            $rejectList = [];
            foreach (explode(';', $reject) as $string) {
                $string = trim(strtolower($string));
                foreach (['category', 'id'] as $value) {
                    if (strpos($string, $value) !== false) {
                        $fieldName = 't.' . ($value == 'category' ? 'category_id' : $value);
                        $rejectList[$fieldName] = substr($string, strlen($value) + 1);
                    }
                }
            }
            foreach ($rejectList as $key => $value) {
                $values = AppHelper::cleanIntegerNumbers($value);
                $count = count($values);
                if ($count) {
                    if ($count == 1) {
                        $condition = ['AND', $condition, ['<>', $key, $values[0]]];
                    } else {
                        $condition = ['AND', $condition, ['NOT IN', $key, $values]];
                    }
                }
            }
        }

        $query->where($condition);

        // Order By
        $orderByColumns = [];
        if (!empty($orderBy)) {
            $orderByColumnLimit = ['id', 'categoryId', 'clicksCount', 'publishedAt', 'createdAt', 'updatedAt']; // Supported order by column names
            foreach (explode(',', trim($orderBy)) as $string) {
                if (!empty($string)) {
                    $string = explode('.', $string);
                    if (in_array($string[0], $orderByColumnLimit)) {
                        $orderByColumns['t.' . Inflector::camel2id($string[0], '_')] = isset($string[1]) && $string[1] == 'asc' ? SORT_ASC : SORT_DESC;
                    }
                }
            }
        }

        $query->orderBy($orderByColumns ?: ['t.published_at' => SORT_DESC]);
        if ($this->debug) {
            Yii::debug($query->createCommand()->getRawSql(), 'API DEBUG');
        }

        return $query;
    }

    /**
     * 资讯列表（带翻页）
     *
     * @param string $title
     * @param string $category
     * @param string $children
     * @param string $label
     * @param string $picture
     * @param string $date
     * @param string $author
     * @param string $keywords
     * @param string $reject
     * @param string $combinationMethod
     * @param string $orderBy
     * @param integer $page
     * @param integer $pageSize
     * @return ActiveDataProvider
     * @throws \yii\db\Exception
     */
    public function actionIndex($title = null, $category = null, $children = 'n', $label = null, $picture = null, $date = null, $author = null, $keywords = null, $reject = null, $combinationMethod = 'and', $orderBy = null, $page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => $this->parserQuery(Yii::$app->getRequest()->get('fields'), $title, $category, $children, $label, $picture, $date, $author, $keywords, $reject, $combinationMethod, $orderBy, null, null),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 资讯列表（不带翻页）
     *
     * @param string $fields
     * @param string $title
     * @param string $category
     * @param string $children
     * @param string $label
     * @param string $picture
     * @param string $date
     * @param string $author
     * @param string $keywords
     * @param string $reject
     * @param string $combinationMethod
     * @param string $orderBy
     * @param integer $offset
     * @param integer $limit
     * @return ActiveDataProvider
     * @throws \yii\db\Exception
     */
    public function actionList($fields = null, $title = null, $category = null, $children = 'n', $label = null, $picture = null, $date = null, $author = null, $keywords = null, $reject = null, $combinationMethod = 'and', $orderBy = null, $offset = 0, $limit = 10)
    {
        return new ActiveDataProvider([
            'query' => $this->parserQuery($fields, $title, $category, $children, $label, $picture, $date, $author, $keywords, $reject, $combinationMethod, $orderBy, $offset, $limit),
            'pagination' => false
        ]);
    }

    /**
     * 更新点击次数
     *
     * @param integer $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdateClicksCount($id)
    {
        $db = Yii::$app->getDb();
        $data = $db->createCommand('SELECT [[id]], [[clicks_count]] FROM {{%news}} WHERE [[id]] = :id', [
            ':id' => (int) $id,
        ])->queryOne();
        if ($data) {
            $db->createCommand('UPDATE {{%news}} SET [[clicks_count]] = [[clicks_count]] + 1 WHERE [[id]] = :id', [':id' => $data['id']])->execute();

            return [
                'id' => $data['id'],
                'clicksCount' => $data['clicks_count'] + 1
            ];
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * 资讯提交接口
     *
     * @return News
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $model = new News();
        $model->loadDefaultValues();
        $newsContent = new NewsContent();
        $post = Yii::$app->getRequest()->post();
        if ($post) {
            if ($model->load($post, '') && $newsContent->load($post, '') && $model->validate() && $newsContent->validate()) {
                if (isset($_FILES['picture_path']) && $_FILES['picture_path']) {
                    $file = \yii\web\UploadedFile::getInstanceByName('picture_path');
                    if ($file instanceof \yii\web\UploadedFile && $file->error != UPLOAD_ERR_NO_FILE) {
                        $model->is_picture_news = Constant::BOOLEAN_TRUE;
                        $fileUrl = '/uploads/' . date('Ymd') . '/' . StringHelper::generateRandomString() . '.' . $file->getExtension();
                        $path = Yii::getAlias('@app/web') . $fileUrl;
                        $model->picture_path = $fileUrl;
                        @mkdir(pathinfo($path, PATHINFO_DIRNAME), 0777, true);
                        $file->saveAs($path);
                    }
                }
                // 解析文本内容，提取图片并下载
                $sourceUrl = $model['source_url'];
                $url = '';
                if ($sourceUrl) {
                    $urlConf = parse_url($sourceUrl);
                    if ($urlConf !== false) {
                        if (isset($urlConf['schema'])) {
                            $url .= $urlConf['schema'];
                        } else {
                            $url = 'http';
                        }
                        $url .= '://';
                        if (isset($urlConf['host'])) {
                            $url .= $urlConf['host'];
                        }
                        if (isset($urlConf['port']) && $urlConf['port'] != 80 && $urlConf['port'] != 443) {
                            $url .= ":{$urlConf['port']}";
                        }
                    }
                }
                $directory = date('Ymd');
                $webRoot = Yii::getAlias('@webroot');
                $savePath = $webRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $directory;
                if (!file_exists($savePath)) {
                    FileHelper::createDirectory($savePath);
                }

                $content = $newsContent['content'];
                $images = ImageHelper::parseImages($content);
                if ($images) {
                    $replaceParis = [];
                    $client = new Client();
                    foreach ($images as $image) {
                        $oriImage = $image;
                        if (file_exists($webRoot . '/' . ltrim($image, '/'))) {
                            continue;
                        }
                        if (strncasecmp($image, '//', 2) !== 0 && strncasecmp($image, 'http', 4) !== 0 && strncasecmp($image, 'https', 5) !== 0) {
                            $image = $url . '/' . ltrim($image, ' / ');
                        }
                        if (strncasecmp($image, '//', 2) === 0 || strncasecmp($image, 'http', 4) === 0 || strncasecmp($image, 'https', 5) === 0) {
                            try {
                                $response = $client->get($image);
                                $headers = $response->getHeaders();

                                // http://www.example.com/images/image/124/12460449.jpg?t=1537200428#a 此类情况需要处理
                                if (strpos($image, '?') !== false) {
                                    $t = parse_url($image);
                                    if (isset($t['query']) || isset($t['fragment'])) {
                                        $image = "{$t['scheme']}://{$t['host']}{$t['path']}";
                                    }
                                }
                                $filename = StringHelper::generateRandomString() . '.' . ImageHelper::getExtension($image);
                                if ($response->getStatusCode() == 200) {
                                    $img = $response->getBody();
                                    file_put_contents($savePath . DIRECTORY_SEPARATOR . $filename, $img);
                                    $replaceParis[$oriImage] = "/uploads/$directory/$filename";
                                }
                            } catch (RuntimeException $e) {
                            }
                        }
                    }
                    if ($replaceParis) {
                        $newsContent->content = strtr($content, $replaceParis);
                    }
                }

                $db = Yii::$app->getDb();
                $transaction = $db->beginTransaction();
                try {
                    $model->save(true);
                    $model->saveContent($newsContent); // 保存资讯内容
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw new HttpException(500, $e->getMessage());
                }

                return $model;
            } else {
                Yii::$app->getResponse()->setStatusCode(400);

                return array_merge($model->errors, $newsContent->errors);
            }
        } else {
            throw new \yii\base\InvalidArgumentException('未检测到提交的内容。');
        }
    }

    /**
     * 资讯详情
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = News::find()
            ->alias('t')
            ->select(['t.id', 't.title', 't.short_title', 't.keywords', 't.description', 't.category_id', 'c.name AS category_name', 't.created_at', 't.updated_at', 't.clicks_count', 't.enabled_comment', 't.comments_count', 't.author', 't.source', 't.published_at', 'u.nickname AS editor', 't.is_picture_news', 't.picture_path'])
            ->leftJoin('{{%category}} c', '[[t.category_id]] = [[c.id]]')
            ->leftJoin('{{%user}} u', '[[t.updated_by]] = [[u.id]]')
            ->where('[[t.id]] = :id AND [[t.enabled]] = :enabled', [
                ':id' => (int) $id,
                ':enabled' => Constant::BOOLEAN_TRUE,
            ])
            ->with(['newsContent'])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        return $model;
    }

}
