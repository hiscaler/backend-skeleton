<?php

namespace app\modules\admin\modules\link\extensions;

use app\modules\admin\modules\link\models\Link;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\link\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \app\modules\admin\extensions\Formatter
{

    /**
     * 链接类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asLinkType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Link::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

}
