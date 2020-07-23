<?php

namespace StepStone\PDFreactor;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use stdClass;
use StepStone\PDFreactor\Exceptions\HttpException;

class Api
{
    /**
     * API Key to attach to requests, if needed.
     *
     * @var null|string
     */
    protected $apiKey;

    /**
     * Cookies that are sent along with a request.
     *
     * @var CookieJar
     */
    protected $cookies  = [];

    /**
     * Headers that are sent along with a request.
     *
     * @var array
     */
    protected $headers  = [
        'Content-Type'      => 'application/json',
        'User-Agent'        => 'PDFreactor PHP API v' . PDFreactor::VERSION,
        'X-RO-User-Agent'   => 'PDFreactor PHP API v' . PDFreactor::VERSION,
    ];

    /**
     * Instance of the GuzzleHttp to make requests.
     *
     * @var HttpClient
     */
    protected $http;
    
    /**
     * Class constructor
     *
     * @param HttpClient $client
     * @param string|null $apiKey
     * @param array $headers
     * @param array $cookies
     */
    public function __construct(HttpClient $client, ?string $apiKey = null, array $headers = [], array $cookies = []) 
    {
        $this->apiKey       = $apiKey;
        $this->cookies      = array_merge($this->cookies, $cookies);
        $this->headers      = array_merge($this->headers, $headers);
        $this->http         = $client ?? new HttpClient;
    }

    /**
     * Creates a new API instance.
     *
     * @param array $options
     * @param string|null $apiKey
     * @param array $headers
     * @param array $cookies
     * @return Api
     */
    public static function create(array $options, ?string $apiKey, array $headers = [], array $cookies = []): Api
    {
        return (new self(new HttpClient($options), $apiKey, $headers, $cookies));
    }

    /**
     * Make the API call to the PDFreactor REST server.
     * 
     * @uses HttpClient
     * 
     * @throws HttpException if there is an error in the api response.
     *
     * @param string $verb
     * @param string $uri
     * @param mixed $body
     * @param array $headers
     * @param array $query
     * @return stdClass
     */
    public function send(string $verb, string $uri, $body = null, array $headers = [], array $query = []): stdClass
    {
        try {
            $options    = [
                'allow_redirects'   => false,
                'headers'           => array_merge($this->headers, $headers),
                'http_errors'       => true,
                'query'             => $query,
            ];
    
            // the $uri shouldn't have a leading forward slash.
            $uri    = ltrim($uri, '/');
    
            if ($this->apiKey) {
                $options['query']['apiKey'] = $this->apiKey;
            }
    
            switch (strtoupper($verb)) {
    
                case 'GET':
                    $response   = $this->http->request('GET', $uri, $options);
                break;
    
                case 'POST':
                    $options['json']    = $body;
                    $response           = $this->http->request('POST', $uri, $options);
                break;
    
                default:
                    throw new HttpException('Invalid Request Method.', 500);
            }

            $result = new stdClass;
            
            $result->headers    = $response->getHeaders();
            $result->status     = $response->getStatusCode();
            $result->success    = ($result->status >= 200 && $result->status <= 204);

            if ($result->status == 200 || $result->status == 201) {
                $result->body   = $response->getBody();
                $result->json   = json_decode($result->body);
            } else {
                $result->body   = null;
                $result->json   = null;
            }

            return $result;

        } catch (ClientException $e) {
            // Convert an GuzzleHttp\Exception\ClientException into a HttpException
            throw new HttpException(json_decode($e->getResponse()->getBody())->error, $e->getResponse()->getStatusCode());

        } catch (HttpException $e) {
            // Just rethrow it.
            throw $e;

        } catch (Exception $e) {
            // convert an Exception into an HttpException
            throw new HttpException($e->getMessage(), 500, $e->getCode());  
        } 
    }
}