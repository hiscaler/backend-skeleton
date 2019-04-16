<?php

namespace app\modules\admin\modules\notice\extensions;

use app\modules\admin\modules\notice\models\Notice;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\notice\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \yii\i18n\Formatter
{

    public $nullDisplay = '';

    /**
     * 查看权限列表
     *
     * @param $value
     * @return string|null
     */
    public function asNoticeViewPermission($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Notice::viewPermissionOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

}
