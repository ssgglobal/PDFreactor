<?php

namespace StepStone\PDFreactor\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use StepStone\PDFreactor\PDFreactor;
use StepStone\PDFreactor\Convertable;

class PDFreactorTest extends TestCase
{
    public function test_can_get_document_id_from_async()
    {
        $mock   = new MockHandler([
            new Response(204, ['Location' => '/progress/485fe366-b01c-4dbb-91a2-29449d02ee80']),
        ]);

        $pdfreactor = new PDFreactor('http://pdfreactor.stepstoneapis.com', 9423, null, $mock);
        $config     = new Convertable('<title>A tribute to the best PDF document ever created.</title>');
        $result     = $pdfreactor->convertAsync($config);

        $this->assertIsString($result);
        $this->assertSame('485fe366-b01c-4dbb-91a2-29449d02ee80', $result);
    }
}