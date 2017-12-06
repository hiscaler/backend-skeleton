<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * 记录操作按钮部件
 */
class MenuButtons extends Widget
{

    public $outerTag = 'ul';
    public $outerHtmlOptions = array('class' => 'tasks');
    public $innerTag = 'li';
    public $items = array();

    public function run()
    {
        echo Html::beginTag('div', array('id' => 'menu-buttons'));
        echo Html::beginTag($this->outerTag, $this->outerHtmlOptions);
        $max = count($this->items) - 1;
        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && $item['visible'] === false) {
                continue;
            }
            $linkHtmlOptions = (isset($item['htmlOptions'])) ? $item['htmlOptions'] : array();
            $innerHtmlOptions = array();
            if ($max == 0) {
                $innerHtmlOptions['class'] = 'first-last';
            } else {
                if ($i == 0) {
                    $innerHtmlOptions['class'] = 'first';
                } else if ($i == $max) {
                    $innerHtmlOptions['class'] = 'last';
                }
            }
            if ($item['url'] == '#') {
                $item['url'] = 'javascript: void(0);';
                if (isset($innerHtmlOptions['class'])) {
                    $innerHtmlOptions['class'] .= ' search-button';
                } else {
                    $innerHtmlOptions['class'] = 'search-button';
                }
            }
            echo Html::beginTag($this->innerTag, $innerHtmlOptions);
            echo Html::a($item['label'], $item['url'], $linkHtmlOptions);
            echo Html::endTag($this->innerTag);
        }
        echo Html::endTag($this->outerTag);
        echo Html::endTag('div');
    }

}
