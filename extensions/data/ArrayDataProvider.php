<?php

namespace app\extensions\data;

/**
 * ArrayDataProvider
 *
 * @package app\extensions\data
 * @author hiscaler <hiscaler@gmail.com>
 */
class ArrayDataProvider extends \yii\data\ArrayDataProvider
{

    /**
     * @var bool 是否使用数组切片返回数据. 如果为 true，则会根据起始位置和数量截取数组，为 false 则直接返回数据，不进行任何处理。
     */
    public $useSlice = true;

    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        if (($models = $this->allModels) === null) {
            return [];
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();

            if ($pagination->getPageSize() > 0) {
                if ($this->useSlice) {
                    $models = array_slice($models, $pagination->getOffset(), $pagination->getLimit(), true);
                }
            }
        }

        return $models;
    }

}