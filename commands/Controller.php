<?php

namespace app\commands;

use yii\console\Exception;

/**
 * 命令行脚本基类
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class Controller extends \yii\console\Controller
{

    public $debug = true;

    public function init()
    {
        parent::init();
        if (YII_ENV != 'prod') {
            $this->debug = false;
        }
    }

    /**
     * @var string 帮助信息
     */
    public $helpMessages = null;

    /**
     * @throws Exception
     */
    public function actionHelp()
    {
        if ($this->helpMessages) {
            $this->stdout($this->helpMessages);
        } else {
            throw new Exception('Not implement it in `' . get_called_class() . '` class.');
        }
    }

}
