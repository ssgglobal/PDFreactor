<?php

namespace StepStone\PDFreactor\Models;

/**
 * Result Model
 * 
 * @link https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Result
 */
class Result extends AbstractModel
{
    public $callbackUrl;
    public $connections;
    public $contentType;
    public $conversionName;
    public $document;
    public $documentArray;
    public $documentId;
    public $documentUrl;
    public $endDate;
    public $error;
    public $exceedingContents;
    public $javaScriptExports;
    public $keepDocument;
    public $log;
    public $numberOfPages           = 0;
    public $numberOfPagesLiteral    = 0;
    public $startDate;
}