<?php

namespace app\modules\admin\components;

use Yii;
use yii\base\Widget;

/**
 * 消息盒子
 *
 */
class MessageBox extends Widget
{

    public $title = null;
    public $showFooter = false;
    public $message;
    public $showCloseButton = false;

    public function run()
    {
        if ($this->title == null) {
            $this->title = Yii::t('app', 'Prompt Message');
        }
        $header = '<div class="hd">{closeButton}{title}</div>';
        $headerReplacePairs = [
            '{title}' => $this->title,
        ];
        $headerReplacePairs['{closeButton}'] = $this->showCloseButton ? '<em><a class="close-button" href="javascript: void(0);" onClick=\'$("#' . $this->id . '").fadeOut("slow");\'>X</a></em>' : '';
        $header = strtr($header, $headerReplacePairs);
        $body = '<div class="bd">{body}</div>';
        $body = str_replace('{body}', $this->getBodyContent(), $body);
        $footer = '<div class="ft"></div>';

        return '<div id="' . $this->id . '" class="message-box">' . $header . $body . ($this->showFooter ? $footer : '') . '</div>';
    }

    protected function getBodyContent()
    {
        if ($this->message == null) {
            $this->message = 'I am sorry. No message. Please input you message text try again......';
        }

        if (is_array($this->message)) {
            $output = '<ol>';
            foreach ($this->message as $msg) {
                $output .= '<li>' . $msg . '</li>';
            }
            $this->message = $output . '</ol>';
        }

        return $this->message;
    }

}
