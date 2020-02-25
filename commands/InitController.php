<?php

namespace app\commands;

use app\models\Constant;
use app\models\Lookup;
use app\models\Member;
use app\models\User;
use Yii;
use yii\base\Security;
use yii\console\ExitCode;
use yii\helpers\Inflector;

/**
 * 初始化数据
 *
 * @package app\commands
 * @author hiscaler <hiscaler@gmail.com>
 */
class InitController extends Controller
{

    protected $hintMessages = <<<EOT
Usage: ./yii init
EOT;

    private $_userId = 0;

    private $_memberId = 0;

    /**
     * 初始化默认管理用户
     *
     * @return int
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    private function _initUser()
    {
        $username = 'admin';
        $db = Yii::$app->getDb();
        $userId = $db->createCommand('SELECT [[id]] FROM {{%user}} WHERE username = :username', [':username' => $username])->queryScalar();
        if (!$userId) {
            $now = time();
            $security = new Security;
            $columns = [
                'username' => $username,
                'nickname' => 'admin',
                'auth_key' => $security->generateRandomString(),
                'password_hash' => $security->generatePasswordHash('admin'),
                'password_reset_token' => null,
                'email' => 'admin@example.com',
                'role' => null,
                'register_ip' => 0,
                'login_count' => 0,
                'last_login_ip' => null,
                'last_login_time' => null,
                'status' => User::STATUS_ACTIVE,
                'created_by' => 0,
                'created_at' => $now,
                'updated_by' => 0,
                'updated_at' => $now,
            ];
            $db->createCommand()->insert('{{%user}}', $columns)->execute();
            $this->_userId = $db->getLastInsertID();
        } else {
            $this->_userId = $userId;
            $this->stdout("'{$username}' is exists." . PHP_EOL);
        }

        return $this->_userId;
    }

    /**
     * 初始化会员用户
     *
     * @return void
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    private function _initMember()
    {
        $db = Yii::$app->getDb();
        $usernames = ['admin', 'tmp'];
        foreach ($usernames as $i => $username) {
            $userId = $db->createCommand('SELECT [[id]] FROM {{%member}} WHERE username = :username', [':username' => $username])->queryScalar();
            if (!$userId) {
                $security = new Security;
                $member = new Member();
                $member->username = $username;
                $member->setPassword('111111');
                $member->mobile_phone = "158" . str_repeat($i, 8);
                if ($member->save()) {
                    if ($username == 'admin') {
                        $this->_memberId = $db->getLastInsertID();
                    }
                    $this->stdout("Create `$username` member successful.");
                }
            } else {
                $this->stdout("Member `$username` is exists." . PHP_EOL);
            }
        }
    }

    /**
     * 初始化配置资料
     *
     * @return int
     * @throws \yii\db\Exception
     */
    public function _initLookupRecords()
    {
        $this->stdout("Begin..." . PHP_EOL);
        $db = Yii::$app->getDb();
        $items = [
            Lookup::GROUP_CUSTOM => [
                'custom.site.name' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => null,
                ],
                'custom.site.icp' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => null,
                ],
                'custom.site.statistics.code' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXTAREA,
                    'value' => null,
                ],
                'custom.address' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => null,
                ],
                'custom.tel' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => null,
                ],
            ],
            Lookup::GROUP_SEO => [
                'seo.meta.keywords' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => null,
                ],
                'seo.meta.description' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXTAREA,
                    'value' => null,
                ],
            ],
            Lookup::GROUP_SYSTEM => [
                // 是否激活安全选项
                'system.security.enable' => [
                    'returnType' => Lookup::RETURN_TYPE_BOOLEAN,
                    'inputMethod' => Lookup::INPUT_METHOD_CHECKBOX,
                    'value' => true,
                ],
                'system.security.change-password-interval-days' => [
                    'returnType' => Lookup::RETURN_TYPE_INTEGER,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 30,
                ],
                'system.offline' => [
                    'returnType' => Lookup::RETURN_TYPE_BOOLEAN,
                    'inputMethod' => Lookup::INPUT_METHOD_CHECKBOX,
                    'value' => false,
                ],
                'system.offline.message' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXTAREA,
                    'value' => null,
                ],
                'system.language' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_DROPDOWNLIST,
                    'inputValue' => implode(PHP_EOL, [
                        'en-US:英文',
                        'zh-CN:简体中文',
                        'zh-TW:繁体中文',
                    ]),
                    'value' => 'zh-CN',
                ],
                'system.timezone' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_DROPDOWNLIST,
                    'inputValue' => implode(PHP_EOL, [
                        'Etc/GMT:格林威治时间',
                        'Etc/UTC:世界标准时间',
                        'PRC:中国标准时间',
                    ]),
                    'value' => 'PRC',
                ],
                'system.date-format' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 'php:Y-m-d',
                ],
                'system.time-format' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 'php:H:i:s',
                ],
                'system.datetime-format' => [
                    'returnType' => Lookup::RETURN_TYPE_STRING,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 'php:Y-m-d H:i:s',
                ],
                // 会员注册默认状态
                'system.member.register.default.status' => [
                    'returnType' => Lookup::RETURN_TYPE_INTEGER,
                    'inputMethod' => Lookup::INPUT_METHOD_DROPDOWNLIST,
                    'inputValue' => implode(PHP_EOL, [
                        Member::STATUS_PENDING . ':' . Yii::t('member', 'Pending'),
                        Member::STATUS_LOCKED . ':' . Yii::t('member', 'Locked'),
                        Member::STATUS_ACTIVE . ':' . Yii::t('member', 'Active'),
                    ]),
                    'value' => Member::STATUS_ACTIVE,
                ],
                // 会员注册赠送积分
                'system.member.register.default.credits' => [
                    'returnType' => Lookup::RETURN_TYPE_INTEGER,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 0,
                ],
                // 会员推荐注册赠送积分
                'system.member.register.referral.credits' => [
                    'returnType' => Lookup::RETURN_TYPE_INTEGER,
                    'inputMethod' => Lookup::INPUT_METHOD_TEXT,
                    'value' => 0,
                ],
            ],
        ];
        $cmd = $db->createCommand();
        $existsCmd = $db->createCommand('SELECT COUNT(*) FROM {{%lookup}} WHERE [[type]] = :type AND [[key]] = :key');
        $now = time();
        foreach ($items as $group => $data) {
            foreach ($data as $key => $item) {
                $type = isset($item['type']) ? $item['type'] : Lookup::TYPE_PUBLIC;
                $key = trim($key);
                // Check exists, ignore it if exists.
                $exists = $existsCmd->bindValues([
                    ':type' => $type,
                    ':key' => $key,
                ])->queryScalar();
                if ($exists) {
                    $this->stdout("{$key} is exists, ignore it..." . PHP_EOL);
                    continue;
                }

                $this->stdout("Insert {$key} ..." . PHP_EOL);
                $index = strpos($key, '.');
                if ($index !== false && in_array(substr($key, 0, $index), ['custom', 'seo', 'system'])) {
                    $label = substr($key, $index + 1);
                    $label = Inflector::camel2words($label, '.');
                } else {
                    $label = Inflector::camel2words($key);
                }
                $columns = [
                    'type' => $type,
                    'group' => $group,
                    'key' => $key,
                    'label' => Yii::t('lookup', $label),
                    'description' => Yii::t('lookup', $label),
                    'value' => serialize(isset($item['value']) ? $item['value'] : ''),
                    'return_type' => isset($item['returnType']) ? $item['returnType'] : Lookup::RETURN_TYPE_STRING,
                    'input_method' => isset($item['inputMethod']) ? $item['inputMethod'] : Lookup::INPUT_METHOD_TEXT,
                    'input_value' => isset($item['inputValue']) ? $item['inputValue'] : '',
                    'enabled' => Constant::BOOLEAN_TRUE,
                    'created_by' => $this->_memberId,
                    'created_at' => $now,
                    'updated_by' => $this->_memberId,
                    'updated_at' => $now,
                ];
                $cmd->insert('{{%lookup}}', $columns)->execute();
            }
        }
        $this->stdout("Done..." . PHP_EOL);

        return ExitCode::OK;
    }

    /**
     * yii init
     *
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $this->_initUser();
        $this->_initMember();
        $this->_initLookupRecords();
    }

}
