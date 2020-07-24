<?php

namespace StepStone\PDFreactor\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use stdClass;
use StepStone\PDFreactor\PDFreactor;
use StepStone\PDFreactor\Convertable;
use StepStone\PDFreactor\Exceptions\HttpException;
use StepStone\PDFreactor\Result;

class PDFreactorTest extends TestCase
{
    protected $uuid = '485fe366-b01c-4dbb-91a2-29449d02ee80';

    public function test_can_get_document_id_from_async()
    {
        $pdfreactor = $this->pdfreactorMock(new MockHandler([
            new Response(204, ['Location' => "/progress/{$this->uuid}"]),
        ]));

        $result     = $pdfreactor->convertAsync(
            new Convertable('<title>A tribute to the best PDF document ever created.</title>')
        );

        $this->assertIsString($result);
        $this->assertSame('485fe366-b01c-4dbb-91a2-29449d02ee80', $result);
    }

    public function test_async_progress_error_includes_id()
    {
        try {

            $pdfreactor   = $this->pdfreactorMock(new MockHandler([
                new Response(404, ['Content-Type' => 'application/json'], "{\"error\": \"No document was found with ID\"}"),    
            ]));
            
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

    public function test_get_document_as_binary()
    {
        $pdfreactor = $this->pdfreactorMock(new MockHandler([
            new Response(404, ['Content-Type' => 'text/plain'], "No document was found with this ID"),
        ]));

        try {
            $pdfreactor->getDocumentAsBinary($this->uuid);
        } catch (HttpException $e) {
            // we're gonna get a 404, the $document id should be included.
            $this->assertStringContainsString($this->uuid, $e->getMessage());
        }
    }

    public function test_get_bool_when_deleting_document()
    {
        $pdfreactor   = $this->pdfreactorMock(new MockHandler([
            new Response(204),
        ]));

        $this->assertTrue(
            $pdfreactor->deleteDocument($this->uuid)
        );
    }

    public function test_get_server_version()
    {
        $pdfreactor = $this->pdfreactorMock(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'build' => 9797,
                'major' => 9,
                'micro' => 9,
                'minor' => 1,
            ])),
        ]));

        $version    = $pdfreactor->getVersion();

        $this->assertIsObject($version);
        $this->assertObjectHasAttribute('build', $version);
        $this->assertObjectHasAttribute('major', $version);
        $this->assertObjectHasAttribute('micro', $version);
        $this->assertObjectHasAttribute('minor', $version);
    }

    public function test_get_server_status()
    {
        $pdfreactor = $this->pdfreactorMock(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], 'OK'),
            new Response(401, ['Content-Type' => 'application/json'], '{"error": "The client failed an authorization check"}'),
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "PDFreactor Web Service is unavailable."}'),
            new Response(410, ['Content-Type' => 'application/json'], '{"error": "Gone"}')
        ]));

        $this->assertEquals(200, $pdfreactor->getStatus()->status);
        $this->assertEquals(401, $pdfreactor->getStatus()->status);
        $this->assertEquals(503, $pdfreactor->getStatus()->status);

        // if it's not 401 or 503 then it should be a 500.
       $this->assertEquals(500, $pdfreactor->getStatus()->status);
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

    /**
     * Sample response from a document progress call.
     *
     * @param boolean $finished
     * @return string
     */
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