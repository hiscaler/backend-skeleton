<?php

namespace app\modules\admin\components;

use Exception;
use yii\widgets\Block;

class CssBlock extends Block
{

    /**
     * @var null
     */
    public $key = null;

    /**
     * @var array
     */
    public $options = [];

    /**
     * Ends recording a block.
     * This method stops output buffering and saves the rendering result as a named block in the view.
     */
    public function run()
    {
        $block = ob_get_clean();
        if ($this->renderInPlace) {
            throw new Exception("not implemented yet ! ");
        }
        $block = trim($block);

        $cssBlockPattern = '|^<style[^>]*>(?P<block_content>.+?)</style>$|is';
        if (preg_match($cssBlockPattern, $block, $matches)) {
            $block = $matches['block_content'];
        }

        $this->view->registerCss($block, $this->options, $this->key);
    }

}
