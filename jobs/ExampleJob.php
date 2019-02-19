<?php

namespace app\jobs;

use Yii;
use yii\queue\JobInterface;

/**
 * Class ExampleJob
 *
 * @package app\jobs
 * @author hiscaler <hiscaler@gmail.com>
 */
class ExampleJob extends Job implements JobInterface
{

    public $url;
    public $filename;

    public function execute($queue)
    {
        file_put_contents(Yii::getAlias('@runtime/' . $this->filename), file_get_contents($this->url));
    }

}