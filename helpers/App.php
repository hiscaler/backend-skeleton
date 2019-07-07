<?php

namespace app\helpers;

/**
 * Class App
 *
 * @package app\helpers
 * @author hiscaler <hiscaler@gmail.com>
 */
class App
{

    /**
     * 判断当前是否在 CLI 模式下
     *
     * @return bool
     */
    public static function isCli()
    {
        if (defined('STDIN')) {
            return true;
        }

        if (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        }

        return false;
    }

}