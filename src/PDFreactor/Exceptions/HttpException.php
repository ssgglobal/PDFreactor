<?php

namespace StepStone\PDFreactor\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * HTTP status code.
     *
     * @var int
     */
    protected $status;

    public function __construct(?string $message = null, int $status = 400, int $code = 0, Exception $previous = null)
    {
        $this->status   = $status;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP Status code for this exception.
     *
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}