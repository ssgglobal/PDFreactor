<?php

/**
 * StepStone PDFreactor PHP Wrapper version 2
 * 
 * This library is based on RealObjects PDFreactor PHP Wrapper v4.
 * https://www.pdfreactor.com
 * 
 * Released under the following license:
 * 
 * The MIT License (MIT)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace StepStone\PDFreactor;

use Exception;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use stdClass;
use StepStone\PDFreactor\Exceptions\HttpException;

class PDFreactor
{
    const CLIENT    = 'PHP';
    const VERSION   = 2;

    /** @var Api */
    protected $api;

    /**
     * Store the result of the last API call made.
     *
     * @var null|stdClass
     */
    protected $result;

    /**
     * Creates a new instance of Api to use.
     *
     * @param string $url
     * @param integer $port
     * @param string|null $apiKey
     * @param MockHandler|null $mock
     */
    public function __construct(string $url, int $port = 9423, ?string $apiKey = null, ?MockHandler $mock = null)
    {
        $options    = [
            'base_uri'  => "{$url}:{$port}/service/rest/"
        ];

        if ($mock) {
            $options['handler'] = HandlerStack::create($mock);
        }

        $this->api  = Api::create($options, $apiKey);
    }

    /**
     * Make an async request to PDFReactor to create a new Document.
     * 
     * @see https://www.pdfreactor.com/product/doc/webservice/rest.html#post-convert-async
     * 
     * @throws HttpException If Location header is missing from result.
     * 
     * @throws HttpException If the Document Id sent by the server can't be parsed from the Location header.
     *      The PDFreactor service will send a UUID as a document Id. 
     *
     * @param Convertable $config
     * @return string
     */
    public function convertAsync(Convertable $convertable): string
    {
        try {
            $this->result = $this->api->send('POST', 'convert/async.json', $convertable->__toArray());

            if (! isset($this->result->headers['Location'][0])) {
                throw new HttpException("Unable to retrieve Document ID from Response.", 500);
            }

            preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $this->result->headers['Location'][0], $matches);

            if (! count($matches) || ! is_string($matches[0])) {
                throw new HttpException("Unable to retrieve Document ID from Response.", 500);
            }

            return $matches[0];

        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), 500, $e->getCode());
        }
    }

    /**
     * Returns result from the last API call made.
     *
     * @return stdClass|null
     */
    public function getLastResult(): ?stdClass
    {
        return $this->result;
    }

    /**
     * Get the progress of an aysnc conversion process.
     * 
     * @see https://www.pdfreactor.com/product/doc/webservice/rest.html#get-progress-id
     * 
     * @throws HttpException when the server returns a 404 error.
     * 
     *
     * @param string $documentId
     * @return void
     */
    public function getProgress(string $documentId): stdClass
    {
        // The native PDFreactor error doesn't attach the $documentId in the
        // error message, so we'll catch and rethrow with it.
        try {
            $this->result = $this->api->send('GET', "progress/{$documentId}.json");

            return $this->result->json;

        } catch (HttpException $e) {

            throw new HttpException(
                ($e->getStatus() == 404 ? "No document was found with ID {$documentId}." : $e->getMessage()),
                $e->getStatus(),
                $e->getCode()
            );
        }
    }
}