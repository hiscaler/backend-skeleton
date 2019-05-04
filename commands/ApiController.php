<?php

namespace app\commands;

use app\models\Option;
use Exception;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\rest\ActiveController;

/**
 * 接口文档生成
 *
 * @package app\commands
 * @author hiscaler <hiscaler@gmail.com>
 */
class ApiController extends Controller
{

    private function parseComment($comment)
    {
        $lines = [];
        foreach (explode(PHP_EOL, $comment) as $line) {
            $line = trim($line);
            if (in_array($line, ['/*', '/**', '*', '*/'])) {
                continue;
            }
            $line = trim(substr($line, 1)); // Remove ‘*’
            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * 获取表单提交参数
     *
     * @param $tableName
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    private function getTableParams($tableName)
    {
        $tableName = strtr($tableName, ['{{%' => '', '}}' => '']);
        $params = [];
        $db = \Yii::$app->getDb();
        $schema = $db->getSchema();
        $tablePrefix = $db->tablePrefix;
        $tables = Option::tables(false);
        try {
            foreach ($tables as $table) {
                if ($table != $tableName) {
                    continue;
                }
                $tableSchema = $schema->getTableSchema($tablePrefix . $table, true);
                foreach ($tableSchema->columns as $column) {
                    $columnName = $column->name;
                    if (in_array($columnName, ['id', 'created_at', 'created_by', 'updated_at', 'updated_by'])) {
                        continue;
                    }
                    $sectionParam = new SectionParam();
                    $sectionParam->setName($columnName);
                    $sectionParam->setType($column->type);
                    $sectionParam->setLength($column->size);
                    $sectionParam->setRequired($column->allowNull);
                    $sectionParam->setDefaultValue($column->defaultValue);
                    $sectionParam->setRemark($column->comment);
                    $params[] = $sectionParam;
                }
            }
        } catch (Exception $e) {
        }

        return $params;
    }

    /**
     * @throws \ReflectionException
     * @throws \yii\base\Exception
     */
    public function actionGenerate()
    {
        $this->stdout("Begin ..." . PHP_EOL);
        $path = Yii::getAlias('@app/docs/tmp');
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $files = FileHelper::findFiles(Yii::getAlias('@app/modules/api/controllers'));
        foreach ($files as $file) {
            $doc = new Doc();

            $basename = basename($file, '.php');
            if ($basename == 'Controller') {
                continue;
            }

            $ctrlName = Inflector::camel2id(str_replace('Controller', '', $basename));
            if ($ctrlName != 'category') {
                continue;
            }
            $class = new \ReflectionClass("app\\modules\\api\\controllers\\$basename");
            $docComment = $class->getDocComment();
            $lines = [];
            foreach (explode(PHP_EOL, $docComment) as $line) {
                $line = trim($line);
                if (in_array($line, ['/*', '/**', '*', '*/'])) {
                    continue;
                }
                $line = trim(substr($line, 1)); // Remove ‘*’
                $lines[] = $line;
            }

            foreach ($lines as $i => $line) {
                if ($i == 0) {
                    $doc->setTitle($line);
                } else {
                    $doc->setDescription($line);
                }
            }

            $newInstance = $class->newInstanceWithoutConstructor();
            $modelClass = new \ReflectionClass($newInstance->modelClass);
            $tableName = ($modelClass->newInstanceWithoutConstructor())->tableName();
            $isActiveControllerClass = $class->isSubclassOf(ActiveController::class);
            if ($isActiveControllerClass) {
                $actions = $newInstance->actions();
                foreach ($actions as $key => $_) {
                    if (!in_array($key, ['index', 'create', 'update', 'view', 'delete'])) {
                        continue;
                    }
                    $section = new Section();
                    $section->setUrl("/api/$ctrlName/$key");
                    if ($key != 'index') {
                        $section->setParams($this->getTableParams($tableName));
                    }
                    switch ($key) {
                        case 'index':
                            $section->setTitle('列表');
                            $section->setMethod("GET");
                            break;

                        case 'create':
                            $section->setTitle('添加');
                            $section->setMethod("POST");
                            break;

                        case 'update':
                            $section->setTitle('更新');
                            $section->setMethod("PUT|PATCH");
                            break;

                        case 'view':
                            $section->setTitle('详情');
                            $section->setMethod("GET");
                            break;

                        case 'delete':
                            $section->setTitle('删除');
                            $section->setMethod("DELETE");
                            break;
                    }
                    $doc->setSections($section);
                }
            }
            foreach ($class->getMethods() as $method) {
                $methodName = $method->getName();
                if (StringHelper::startsWith($methodName, 'action') && $methodName != 'actions') {
                    $section = new Section();
                    $section->setUrl("/api/$ctrlName/" . substr($methodName, 6));
                    $lines = $this->parseComment($method->getDocComment());
                    $params = [];
                    foreach ($lines as $i => $line) {
                        $lineItems = explode(' ', $line);
                        switch ($lineItems[0]) {
                            case '@dbParam':
                                if (isset($lineItems[1]) && $lineItems[1] == 'true') {
                                    $params[] = $this->getTableParams($tableName);
                                }
                                continue;
                                break;

                            case '@param':
                                /**
                                 * Example: @param bool $flat [长度,必填,默认值] 描述
                                 *          0      1    2      3    4   5      6
                                 */
                                $variableType = $lineItems[1] ?? null;
                                $variableName = $lineItems[2] ?? null;
                                $variableLength = $lineItems[3] ?? null;
                                $variableRequired = isset($lineItems[4]) && $lineItems[4] == 'y' ? 'Y' : '';
                                $variableDefaultValue = $lineItems[5] ?? null;
                                $variableDescription = $lineItems[6] ?? null;
                                $sectionParam = new SectionParam();
                                $sectionParam->setName($variableName ? substr($variableName, 1) : null);
                                $sectionParam->setType($variableType);
                                $sectionParam->setLength($variableLength);
                                $sectionParam->setRequired($variableRequired);
                                $sectionParam->setDefaultValue($variableDefaultValue);
                                $sectionParam->setRemark($variableDescription);
                                $params[] = $sectionParam;
                                $section->setDescription($line);
                                break;

                            default:
                                if ($i == 0) {
                                    $section->setTitle($line);
                                } else {
                                    $section->setDescription($line);
                                }
                                break;
                        }
                    }
                    $section->setParams($params);
                    if (!$section->getTitle()) {
                        $section->setTitle($methodName);
                    }
                    $doc->setSections($section);
                }
            }
            file_put_contents($path . DIRECTORY_SEPARATOR . $ctrlName . '.md', $doc->getContent());
        }

        $this->stdout("Done.");
    }

}

class SectionParam
{

    protected $name;
    protected $type;
    protected $length;
    protected $required;
    protected $defaultValue;
    protected $remark;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name ?: '';
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = trim($name);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type ?: '';
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        if (stripos($type, '|') !== false) {
            $type = str_replace('|', ',', $type);
        }
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param mixed $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return mixed
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param mixed $required
     */
    public function setRequired($required)
    {
        $this->required = boolval($required);
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue ?: '';
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return mixed
     */
    public function getRemark()
    {
        return $this->remark ?: '';
    }

    /**
     * @param mixed $remark
     */
    public function setRemark($remark)
    {
        $this->remark = trim($remark);
    }

}

class Section
{

    protected $title;
    protected $description = [];
    protected $method;
    protected $url;
    protected $params;
    protected $response;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description[] = $description;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        $params = [];
        $tableHeader = [
            ['字段名称', '类型', '长度', '必填', '默认值', '备注'],
            ['---', '---', ':---:', ':---:', ':---:', '---']
        ];
        if ($this->params) {
            $rows = [];
            foreach ($this->params as $param) {
                /* @var $param SectionParam */
                $rows[] = [
                    $param->getName(), $param->getType(), $param->getLength(), $param->getRequired() ? 'Y' : '', $param->getDefaultValue(), $param->getRemark(),
                ];
            }

            $columnWidths = [];
            foreach ($rows as $row) {
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

            foreach ($rows as $i => &$row) {
                foreach ($row as $key => $item) {
                    if (mb_strlen($item) < $columnWidths[$key]) {
                        $row[$key] = str_pad($item, $columnWidths[$key], ' ', $key == 0 ? STR_PAD_LEFT : STR_PAD_RIGHT);
                    }
                }
            }
            unset($row);
            $params = array_merge($tableHeader, $rows);
        }

        return $params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

}

class Doc
{

    protected $title;

    protected $description = [];

    protected $sections = [];

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description[] = $description;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param Section $section
     */
    public function setSections(Section $section)
    {
        $this->sections[] = $section;
    }

    public function getContent()
    {
        $lines = [];
        if ($this->getTitle()) {
            $lines[] = $this->getTitle();
            $lines[] = str_repeat('=', (mb_strlen($this->getTitle()) - 1) * 2);
        }
        if ($this->description) {
            foreach ($this->description as $desc) {
                $lines[] = "> $desc  ";
            }
        }

        foreach ($this->getSections() as $section) {
            $lines[] = '';
            /* @var $section Section */
            $lines[] = "## " . $section->getTitle();
            $t = $section->getMethod();
            if ($t) {
                $t .= ' ';
            }
            $t .= $section->getUrl();
            $t && $lines[] = $t;
            if ($section->getDescription()) {
                foreach ($section->getDescription() as $desc) {
                    $lines[] = "> $desc  ";
                }
            }
            if ($section->getParams()) {
                $lines[] = '';
                $lines[] = "### 参数说明";
                foreach ($section->getParams() as $param) {
                    $lines[] = '| ' . implode(' | ', $param) . ' |';
                }
            }
        }

        return implode(PHP_EOL, $lines);
    }

}
