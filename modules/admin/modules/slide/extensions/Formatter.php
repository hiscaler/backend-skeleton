<?php

namespace app\modules\admin\modules\slide\extensions;

use app\modules\admin\modules\slide\models\Slide;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\slide\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \app\modules\admin\extensions\Formatter
{

    /**
     * 链接地址打开方式
     *
     * @param $value
     * @return null|string
     */
    public function asUrlOpenTarget($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Slide::urlOpenTargetOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

}