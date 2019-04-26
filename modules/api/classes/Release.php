<?php

namespace app\modules\api\classes;

/**
 * 发布日志
 *
 * @package app\modules\api\classes
 * @author hiscaler <hiscaler@gmail.com>
 */
class Release
{

    /**
     * @var string 发布标题
     */
    protected $title;

    /**
     * @var string 发布时间
     */
    protected $datetime;

    /**
     * @var array 发布说明
     */
    protected $items = [];

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = trim($datetime);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $item = trim($item);
        if ($item && $item[0] == '*') {
            $item = trim(substr($item, 1));
        }
        $item && $this->items[] = $item;
    }

}