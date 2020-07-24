<?php

namespace StepStone\PDFreactor\Models;

use Exception;
use ReflectionClass;
use stdClass;

abstract class AbstractModel
{
    /**
     * Parse the servers response.
     * 
     * @throws Exception if $data isn't an object or failed to be converted to one.
     *
     * @param string|stdClass $data
     * @return stdClass
     */
    protected function parseData($data): stdClass
    {
        if (is_string($data)) {
            $data   = json_decode($data);
        }

        if (! $data instanceof stdClass) {
            throw new Exception('Unable to parse response.');
        }

        return $data;
    }

    /**
     * Hydrate class with $data.
     * 
     * @uses ReflectionClass() to get list of properties for extending class.
     *
     * @param string|stdClass $data
     */
    public function __construct($data)
    {
        $data   = $this->parseData($data);
        $class  = new ReflectionClass(static::class);
        
        foreach ($class->getProperties() as $prop) {
            $name   = $prop->getName();

            if (isset($data->{$name}) && ! is_null($data->{$name})) {
                $this->{$name}  = $data->{$name};
            }
        }
    }
}