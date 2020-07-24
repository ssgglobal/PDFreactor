<?php

namespace StepStone\PDFreactor;

use Exception;

class DocResource
{
    /**
     * Collection of Document Resources.
     *
     * @var array
     */
    protected $collection   = [];

    /**
     * Get the Document Collection.
     *
     * @return array
     */
    public function __toArray(): array
    {
        return $this->collection;
    }

    /**
     * Adds another document resource to the collection.
     * 
     * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Resource
     * 
     * @throws Exception if no resource options are given.
     *
     * @param string|null $data
     * @param string|null $content
     * @param string|null $uri
     * @param boolean|null $beforeDocumentScripts
     * @return void
     */
    public function add(?string $data = null, ?string $content = null, ?string $uri = null, ?bool $beforeDocumentScripts = null)
    {
        $resource   = [];

        if (! is_null($data)) $resource['data']                                     = $data;
        if (! is_null($content)) $resource['content']                               = $content;
        if (! is_null($uri)) $resource['uri']                                       = $uri;
        if (! is_null($beforeDocumentScripts)) $resource['beforeDocumentScripts']   = $beforeDocumentScripts;

        // even though everything is optional, we gotta have something.
        if (! count($resource)) {
            throw new Exception("Can't add invalid Resource.");
        }

        $this->collection[] = $resource;
    }
}