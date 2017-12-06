<?php

namespace app\modules\admin\extensions;

use app\models\Ad;
use app\models\Feedback;
use app\models\FileUploadConfig;
use app\models\FriendlyLink;
use app\models\GroupOption;
use app\models\IpAccessRule;
use app\models\Lookup;
use app\models\Meta;
use app\models\Option;
use app\models\User;
use app\models\Video;
use app\models\WorkflowTask;
use Yii;
use yii\helpers\Html;

class Formatter extends \yii\i18n\Formatter
{

    public $nullDisplay = '';

    // Common
    public function asBoolean($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $imageUrl = Yii::$app->getRequest()->getBaseUrl() . '/admin/images/';
        $booleanFormat = [
            $imageUrl . 'yes.png',
            $imageUrl . 'no.png'
        ];

        return Html::img($value ? $booleanFormat[0] : $booleanFormat[1]);
    }

    /**
     * 图片展示
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function asImage($value, $options = [])
    {
        return empty($value) ? $this->nullDisplay : Html::img(Yii::$app->getRequest()->getBaseUrl() . $value, $options);
    }

    public function asLong2ip($value)
    {
        if (empty($value)) {
            return $this->nullDisplay;
        } else {
            return long2ip($value);
        }
    }

    /**
     * 下载按钮
     *
     * @param string $value
     * @return string
     */
    public function asDownload($value)
    {
        if (empty($value)) {
            return $this->nullDisplay;
        } else {
            return Html::a(Yii::t('app', 'Download'), $value, ['target' => '_blank', 'class' => 'btn-download']);
        }
    }

    /**
     * 获取分组名称
     *
     * @param string $groupName
     * @param string $value
     * @return mixed
     */
    public function asGroupName($groupName, $value)
    {
        if ($value == 0) {
            return $this->nullDisplay;
        } else {
            return GroupOption::getText($groupName, $value);
        }
    }

    /**
     * Get data status text view
     *
     * @param integer $value
     * @return mixed
     */
    public function asDataStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Option::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * Get model name text view
     *
     * @param integer $value
     * @return mixed
     */
    public function asModelName($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $modules = isset(Yii::$app->params['modules']) ? Yii::$app->params['modules'] : [];
        foreach ($modules as $ms) {
            foreach ($ms as $key => $item) {
                if ($value == $key) {
                    $value = Yii::t('app', $item['label']);
                    break;
                }
            }
        }

        return $value;
    }

    // IP Access Rule
    public function asIpAccessRuleType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = IpAccessRule::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Meta
    public function asMetaFormFieldType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Meta::formFieldTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    public function asMetaDbFieldType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Meta::dbFieldTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // User
    public function asUserType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = User::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    public function asUserStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = User::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    public function asUserRole($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = User::roleOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // File upload config
    public function asFileUploadConfigType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = FileUploadConfig::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Lookup

    /**
     * 返回类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asLookupReturnType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Lookup::returnTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Friendly Link

    /**
     * 链接类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asFriendlyLinkType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = FriendlyLink::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * 链接打开方式
     *
     * @param integer $value
     * @return mixed
     */
    public function asFriendlyLinkUrlOpenTarget($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = FriendlyLink::urlOpenTargetOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Ad

    /**
     * 广告类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asAdType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Ad::typeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    /**
     * 广告文件
     *
     * @param integer $value
     * @return mixed
     */
    public function asAdFile($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $value = Yii::$app->getRequest()->getBaseUrl() . $value;
        if (in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            $value = Html::img($value);
        }

        return $value;
    }

    // Slide

    /**
     * 链接打开方式
     *
     * @param integer $value
     * @return mixed
     */
    public function asSlideUrlOpenTarget($value)
    {
        return $this->asFriendlyLinkUrlOpenTarget($value);
    }

    // Feedback

    /**
     * 记录状态
     *
     * @param integer $value
     * @return mixed
     */
    public function asFeedbackStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Feedback::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Video

    /**
     * 视频路径保存类型
     *
     * @param integer $value
     * @return mixed
     */
    public function asVideoPathType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = Video::pathTypeOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

    // Workflow

    /**
     * 任务状态
     *
     * @param integer $value
     * @return mixed
     */
    public function asWorkflowTaskStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $options = WorkflowTask::statusOptions();

        return isset($options[$value]) ? $options[$value] : $this->nullDisplay;
    }

}
