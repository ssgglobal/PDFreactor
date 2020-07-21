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
use StepStone\PDFreactor\Exceptions\HttpException;

class PDFreactor
{
    const CLIENT    = 'PHP';

    const VERSION   = 2;

    /** @var Api */
    protected $api;

    /**
     * Creates a new instance of Api to use.
     *
     * @param string $url
     * @param integer $port
     * @param string|null $apiKey
     */
    public function __construct(string $url, int $port = 9423, ?string $apiKey = null)
    {
        $this->api  = new Api($url, $port, $apiKey);
    }

    /**
     * Make an async request to PDFReactor to create a new Document.
     * 
     * @see https://www.pdfreactor.com/product/doc/webservice/rest.html#post-convert-async
     * 
     * @throws Exception if $body isn't a string or instance of Config.
     *
     * @param Config|string $body
     * @return string
     */
    public function convertAsync($body): string
    {
        if (is_string($body)) {
            $body   = new Config($body, [
                'ClientName'    => self::CLIENT,
                'ClientVersion' => self::VERSION,
            ]);
        } elseif ($body instanceof Config) {
            $body->addConfig('ClientName', self::CLIENT)
                ->addConfig('ClientVersion', self::VERSION);
        } else {
            throw new Exception('$body must be a string or instance of \StepStone\PDFReactor\Config');
        }

        $result = $this->api->send('POST', 'convert/async.json', $body->__toArray());

        if (! isset($result->id)) {
            throw new Exception('Unable to retrieve Document ID from request.');
        }

        return $result->id;
    }
}