<?php
    
    use Blockavel\LaraBlockIo\LaraBlockIo;
    
    class DemoTest extends PHPUnit_Framework_TestCase 
    { 
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
        
        public function testGetNetwork()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getNetwork();
            
            $this->assertTrue($res == 'BTCTEST' || $res = "LTCTEST" || $res = "DGCTEST");
        }
        
        public function testGetAvailableBalance()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getAvailableBalance();
            
            $this->assertTrue(is_numeric($res) && $res >= 0);
        }
        
        public function testGetPendingReceivedBalance()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getPendingReceivedBalance();
            
            $this->assertTrue(is_numeric($res) && $res >= 0);
        }

        
    }

?>
