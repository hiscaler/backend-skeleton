<?php

namespace app\models;

/**
 * 常量定义
 *
 * @package app\models
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseConstant
{

    /**
     * 布尔值定义
     */
    /** 假 */
    const BOOLEAN_FALSE = 0;
    /** 真 */
    const BOOLEAN_TRUE = 1;

    /**
     * 状态值定义
     */
    /** 待审核 */
    const STATUS_PENDING = 0;
    /** 激活 */
    const STATUS_ACTIVE = 1;

    /**
     * 未知性别
     */
    const SEX_UNKNOWN = 0;
    /**
     * 男
     */
    const SEX_MALE = 1;
    /**
     * 女
     */
    const SEX_FEMALE = 2;

    /**
     * 数据分隔符
     */
    const DELIMITER = ',';

}
