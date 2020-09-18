<?php


namespace App\Exception\Http;

use Exception;

/**
 * Class BadRequestException
 * @package App\Exception\Http
 */
class BadRequestException extends Exception implements IHTTPStatus
{
    const STATUS_CODE = 400;

    private $error_message;
    private $error_code;

    /**
     * BadRequestException constructor.
     * @param $error_message
     * @param $error_code
     * @param null $previous
     */
    public function __construct($error_message, $error_code, $previous = null)
    {
        parent::__construct($error_message, 0, $previous);

        $this->error_message = $error_message;
        $this->error_code = $error_code;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->error_code;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return self::STATUS_CODE;
    }
}
