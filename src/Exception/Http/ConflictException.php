<?php


namespace App\Exception\Http;

use Exception;

/**
 * Class ConflictException
 * @package App\Exception\Http
 */
class ConflictException extends Exception implements IHTTPStatus
{
    const STATUS_CODE = 409;

    private $error_message;
    private $error_code;

    /**
     * ConflictException constructor.
     * @param $error_message
     * @param $error_code
     * @param null $previous
     */
    public function __construct($error_message, $error_code = "conflict", $previous = null)
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
        return $this->error_message ?? "Запрос не может быть выполнен из-за конфликтного обращения к ресурсу.";
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->error_code ?? "conflict";
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return self::STATUS_CODE;
    }
}
