<?php

namespace app\modules\api\extensions\yii\data;

use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * 列表活动数据提供（不带翻页）
 *
 * @package app\modules\api\extensions\yii\data
 * @author hiscaler <hiscaler@gmail.com>
 */
class ActiveListDataProvider extends ActiveDataProvider
{

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }
        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
            $this->pagination = false;
        }

        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        return $query->all($this->db);
    }

}
