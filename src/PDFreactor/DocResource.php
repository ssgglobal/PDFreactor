<?php

namespace StepStone\PDFreactor;

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

        $this->collection[] = $resource;
    }
}