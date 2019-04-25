<?php

namespace app\modules\api\classes;

/**
 * 发布日志集合
 *
 * @package app\modules\api\classes
 * @author hiscaler <hiscaler@gmail.com>
 */
class Releases
{

    /**
     * @var array 发布日志列表
     */
    protected $items = [];

    /**
     * @return array
     */
    public function getItems()
    {
        $items = [];
        foreach ($this->items as $item) {
            /* @var $item Release */
            $items[] = [
                'title' => $item->getTitle(),
                'date' => $item->getDatetime(),
                'items' => $item->getItems(),
            ];
        }

        return $items;
    }

    /**
     * @param Release $release
     */
    public function setItem(Release $release)
    {
        $this->items[] = $release;
    }

}