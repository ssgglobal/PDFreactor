<?php

namespace StepStone\PDFreactor\Models;

/**
 * Progress Model
 * 
 * @link https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Progress
 */
class Progress extends AbstractModel
{
    public $callbackUrl;
    public $contentType;
    public $conversionName;
    public $document;
    public $documentId;
    public $documentUrl;
    public $finished;
    public $log;
    public $progress;
    public $startDate;
}