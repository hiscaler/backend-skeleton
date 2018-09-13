<?php

namespace app\modules\api\exceptions;

/**
 * Class UserException
 *
 * @package app\modules\api\exceptions
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserException extends \yii\base\UserException
{

    private $statuses = [
        701 => '数据验证错误。',
    ];

    public $statusCode;

    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code, $previous);
    }

    public function getName()
    {
        if (isset($this->statuses[$this->statusCode])) {
            return $this->statuses[$this->statusCode];
        }

        return 'Error';
    }

}