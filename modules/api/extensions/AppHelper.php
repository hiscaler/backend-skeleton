<?php

namespace app\modules\api\extensions;

use app\helpers\App;
use app\helpers\Config;
use Yii;
use yii\helpers\Inflector;
use yii\web\UnauthorizedHttpException;

/**
 * Class AppHelper
 *
 * @package app\modules\api\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class AppHelper
{

    /**
     * 只保留 $fields 提供的字段内容
     *
     * @param array $selectColumns 所有查询的字段名称
     * @param string $fields 请求查询的字段名称
     * @param array $relatedFields 请求查询的字段需要的附加字段（比如查询 shortTitle 需要 title 属性，根据 model 的 fields 设定）
     * @return array
     */
    public static function filterQuerySelectColumns($selectColumns, $fields, $relatedFields = [])
    {
        if (!empty($fields)) {
            $fields = explode(',', Inflector::camel2id($fields, '_'));
            foreach ($selectColumns as $key => $columnName) {
                $columnName = trim($columnName);
                if (in_array($columnName, $fields)) {
                    continue;
                }

                if ($relatedFields && in_array($columnName, $relatedFields)) {
                    foreach ($fields as $field) {
                        if (isset($relatedFields[$field])) {
                            break 2;
                        }
                    }
                }

                foreach ([' ', '.'] as $value) {
                    if (($pos = strrpos($columnName, $value)) !== false) {
                        $columnName = substr($columnName, $pos + 1);
                    }
                }

                if (!in_array($columnName, $fields)) {
                    unset($selectColumns[$key]);
                }
            }
        }

        return $selectColumns;
    }

    /**
     * 调整数组的键名称
     * created_at => createdAt
     *
     * @param array $rawData
     * @return array
     */
    public static function adjustFieldNames($rawData)
    {
        $items = [];
        if (is_array($rawData)) {
            foreach ($rawData as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[lcfirst(Inflector::id2camel($key, '_'))] = $value;
                }
                $items[] = $data;
            }
        }

        return $items;
    }

    /**
     * 清理掉字符串中的无效字符，默认清理掉空格（全角和半角空格）
     *
     * @param string $string
     * @param boolean $toLower
     * @param array $replacePairs
     * @return string
     */
    public static function cleanString($string, $toLower = true, $replacePairs = [' ' => '', '　' => ''])
    {
        if ($replacePairs) {
            $string = strtr($string, $replacePairs);
        }

        return $toLower ? strtolower($string) : $string;
    }

    /**
     * 清理传入的整型值（非数字和零将全部清理掉）
     * 0,1,2,3,3,abc 返回 1,2,3
     *
     * @param string $string
     * @return array
     */
    public static function cleanIntegerNumbers($string)
    {
        return array_filter(array_unique(array_map('intval', explode(',', $string))));
    }

    /**
     * 处理图片、视频等静态资源的 URL 地址
     *
     * @param string $url
     * @return string
     */
    public static function fixStaticAssetUrl($url)
    {
        if (!empty($url) && strncmp($url, 'http', 4) !== 0 && strncmp($url, '//', 2) !== 0) {
            return Yii::$app->getRequest()->getHostInfo() . '/' . trim($url, '/');
        } else {
            return $url;
        }
    }

    /**
     * 处理正文内容的静态资源地址
     *
     * @param string $content
     * @return string
     */
    public static function fixContentAssetUrl($content)
    {
        if (!empty($content)) {
            $pattern = "/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $content, $matches);
            if ($matches) {
                $hostInfo = Yii::$app->getRequest()->hostInfo . '/';
                $replacePairs = [];
                foreach ($matches[1] as $img) {
                    if (!empty($img) && strncmp($img, 'http', 4) !== 0 && strncmp($img, '//', 2) !== 0) {
                        $replacePairs[$img] = $hostInfo . trim($img, '/');
                    }
                }

                return strtr($content, $replacePairs);
            } else {
                return $content;
            }
        } else {
            return $content;
        }
    }

    /**
     * 格式化日期，获得日期的开始和结束时间戳
     *
     * @param string $date
     * @return array|mixed
     */
    public static function parseDate($date)
    {
        switch (strlen($date)) {
            case 4: // year
                $result = [
                    mktime(0, 0, 0, 1, 1, $date),
                    mktime(0, 0, 0, 12, 31, $date)
                ];
                break;

            case 6: // year + month
                $t = str_split($date, 4);
                $beginTime = mktime(0, 0, 0, $t[1], 1, $t[0]);
                $result = [$beginTime, mktime(23, 59, 59, $t[1], date("t", $beginTime), $t[0])];
                break;

            case 8: // year + month + day
                $year = substr($date, 0, 4);
                $month = substr($date, 4, 2);
                $day = substr($date, 6, 2);
                $result = [
                    mktime(0, 0, 0, $month, $day, $year),
                    mktime(23, 59, 59, $month, $day, $year)
                ];
                break;

            default:
                $result = null;
        }

        return $result;
    }

    /**
     * @param $moduleUniqueId
     * @param $action
     * @return bool
     * @throws UnauthorizedHttpException
     * @throws \Throwable
     */
    public static function checkRbacAuth($moduleUniqueId, $action)
    {
        $user = Yii::$app->getUser();
        if (App::rbacWorking()) {
            $rbacConfig = Config::get('rbac', []);
            $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
            if (!is_array($ignoreUsers)) {
                $ignoreUsers = [];
            }
            if ($ignoreUsers) {
                if (!$user->getIsGuest() && in_array($user->getIdentity()->getUsername(), $ignoreUsers)) {
                    return true;
                }
            }

            $key = str_replace('/', '-', $moduleUniqueId);
            $key && $key .= '-';
            $key = $key . Inflector::camel2id(Yii::$app->controller->id) . '.' . Inflector::camel2id($action->id);
            if (in_array($key, $rbacConfig['ignorePermissionNames']) || $user->can($key)) {
                return true;
            } else {
                throw new UnauthorizedHttpException('对不起，您没有操作该动作的权限。');
            }
        }

        return true;
    }

}