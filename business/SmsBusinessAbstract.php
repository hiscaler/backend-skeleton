<?php

namespace app\business;

use Exception;

/**
 * 短信消息处理
 *
 * @package app\business
 * @author hiscaler <hiscaler@gmail.com>
 */
abstract class SmsBusinessAbstract
{

    /**
     * @var string 短信发送内容
     */
    private $content;

    /**
     * @var array 发送内容占位符值
     */
    private $data = [];

    /**
     * @var bool 是否缓存
     */
    private $useCache = false;

    /**
     * @var mixed 缓存值
     */
    private $cacheValue;

    /**
     * @var int 缓存时间
     */
    private $cacheDuration = 600;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return SmsBusinessAbstract
     */
    public function setContent($content)
    {
        $this->content = trim($content);

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return SmsBusinessAbstract
     */
    public function setData($key, $value)
    {
        $this->data[(string) $key] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function useCache()
    {
        return boolval($this->useCache);
    }

    /**
     * @param bool $use
     * @return SmsBusinessAbstract
     */
    public function setUseCache($use)
    {
        $this->useCache = $use;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacheValue()
    {
        return $this->cacheValue;
    }

    /**
     * @param mixed $value
     * @return SmsBusinessAbstract
     */
    public function setCacheValue($value)
    {
        $this->cacheValue = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheDuration()
    {
        return (int) $this->cacheDuration;
    }

    /**
     * @param int $seconds
     * @return SmsBusinessAbstract
     */
    public function setCacheDuration($seconds)
    {
        $this->cacheDuration = $seconds;

        return $this;
    }

    /**
     * 构建短信发送对象
     *
     * @return SmsBusinessAbstract
     */
    abstract public function build();

    /**
     * @return $this
     * @throws Exception
     */
    public function getPayload()
    {
        if ($this->useCache()) {
            if ($this->getCacheDuration() <= 0) {
                throw new Exception("业务逻辑处理缓存时间无效。");
            }
            if ($this->getCacheValue() === null || $this->getCacheValue() === "") {
                throw new Exception("业务逻辑处理缓存值无效。");
            }
        }

        return $this;
    }

}