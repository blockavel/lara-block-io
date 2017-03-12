<?php

use Blockavel\LaraBlockIo\LaraBlockIo;

class LaraBlockIoTest extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('larablockio.apiKey', getenv('BLOCKIO_API_KEY'));
        $app['config']->set('larablockio.pin', getenv('BLOCKIO_PIN'));
        $app['config']->set('larablockio.version', getenv('BLOCKIO_VERSION'));
    }

    public function testGetBalanceInfo()
    {
        $laraBlockIo = new LaraBlockIo;

        $res = $laraBlockIo->getBalanceInfo();

        $this->assertTrue(gettype($res) == 'object');
        $this->assertArrayHasKey('status', (array) $res);
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);
    }

}
