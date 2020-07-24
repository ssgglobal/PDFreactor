<?php

namespace StepStone\PDFreactor\Models;

/**
 * Version Model
 * 
 * @link https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Version
 */
class Version extends AbstractModel
{
    public $build;
    public $label;
    public $major;
    public $micro;
    public $minor;
    public $revision;
}