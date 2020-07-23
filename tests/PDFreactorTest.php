<?php

namespace StepStone\PDFreactor\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use stdClass;
use StepStone\PDFreactor\PDFreactor;
use StepStone\PDFreactor\Convertable;
use StepStone\PDFreactor\Exceptions\HttpException;

class PDFreactorTest extends TestCase
{
    protected $uuid = '485fe366-b01c-4dbb-91a2-29449d02ee80';

    public function test_can_get_document_id_from_async()
    {
        $mock   = new MockHandler([
            new Response(204, ['Location' => "/progress/{$this->uuid}"]),
        ]);

        $pdfreactor = new PDFreactor('http://pdfreactor.stepstoneapis.com', 9423, null, $mock);
        $config     = new Convertable('<title>A tribute to the best PDF document ever created.</title>');
        $result     = $pdfreactor->convertAsync($config);

        $this->assertIsString($result);
        $this->assertSame('485fe366-b01c-4dbb-91a2-29449d02ee80', $result);
    }

    public function test_async_progress_error_includes_id()
    {
        try {

            $mock   = new MockHandler([
                new Response(404, ['Content-Type' => 'application/json'], "{\"error\": \"No document was found with ID\"}"),    
            ]);
            
            $pdfreactor = new PDFreactor('http://pdfreactor.stepstoneapis.com', 9423, null, $mock);
            
            $pdfreactor->getProgress($this->uuid);

            $this->fail('No Exceptions were thrown.');

        } catch (HttpException $e) {

            $this->assertSame(404, $e->getStatus());
            $this->assertStringContainsString($this->uuid, $e->getMessage());
        }        
    }

    public function test_async_convert_progress()
    {
        $pdfreactor   = $this->pdfreactorMock(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $this->progressResponse(false)),
            new Response(201, ['Content-Type' => 'application/json'], $this->progressResponse(true)),
        ]));

        $result = $pdfreactor->getProgress($this->uuid);
        $this->assertFalse($result->finished);

        $result = $pdfreactor->getProgress($this->uuid);
        $this->assertTrue($result->finished);
    }

    public function test_get_server_version()
    {
        $pdfreactor = $this->pdfreactorMock(new MockHandler([
            new Response(200, ['Content-Type' => 'text/plain'], '9.1.9797.9'),
        ]));

        $this->assertIsString(
            $pdfreactor->getVersion()
        );
    }

    /**
     * Creates a new PDFreactor instances for testing.
     *
     * @param MockHandler $mock
     * @return void
     */
    protected function pdfreactorMock(MockHandler $mock): PDFreactor
    {
        return (new PDFreactor('http://pdfreactor.stepstoneapis.com', 9423, null, $mock));
    }

    protected function progressResponse(bool $finished = false): string
    {
        return json_encode([
            'callbackUrl'       => '',
            'contentType'       => 'application/pdf',
            'conversionName'    => '',
            'documentId'        => $this->uuid,
            'finished'          => $finished,
            'log'               => new stdClass,
            'progress'          => ($finished ? 100 : 50),
            'startDate'         => date('Y-m-d H:i:s'),
        ]);
    }
}