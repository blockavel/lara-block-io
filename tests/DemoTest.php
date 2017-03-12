<?php
    
    use Blockavel\LaraBlockIo\LaraBlockIo;
    
    class DemoTest extends PHPUnit_Framework_TestCase 
    { 
        public function randomString($length = 10) {
        	$str = "";
        	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
        	$max = count($characters) - 1;
        	for ($i = 0; $i < $length; $i++) {
        		$rand = mt_rand(0, $max);
        		$str .= $characters[$rand];
        	}
        	return $str;
        }
        
        public function testGetBalanceInfo()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getBalanceInfo();
            
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

        public function testCreateAddress()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $label = $this->randomString();
            
            $res = $laraBlockIo->createAddress($label);         

            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->createAddress($label);

            $this->assertTrue(gettype($res) == 'object');
            $this->assertArrayHasKey('status', (array) $res);
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data);
            $this->assertArrayHasKey('address', (array) $res->data);    
            $this->assertArrayHasKey('label', (array) $res->data);
   
        }
        
        public function testGetAddressInfo()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getAddressesInfo();
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('addresses', (array) $res->data);
            $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
        }
        
        public function testGetAddressesInfoWithoutBalances()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getAddressesInfoWithoutBalances();
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('addresses', (array) $res->data);
        }
        
        public function testGetAddresses()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getAddresses();
            
            $this->assertArrayHasKey('user_id', (array) $res[0]);
            $this->assertArrayHasKey('address', (array) $res[0]);
            $this->assertArrayHasKey('label', (array) $res[0]);
            $this->assertArrayHasKey('available_balance', (array) $res[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res[0]);
        }
        
        public function testGetAddressesWithoutBalance()
        {
            $laraBlockIo = new LaraBlockIo;
            
            $res = $laraBlockIo->getAddresses();
            
            $this->assertArrayHasKey('user_id', (array) $res[0]);
            $this->assertArrayHasKey('address', (array) $res[0]);
            $this->assertArrayHasKey('label', (array) $res[0]);
        }

        public function testGetAddressesBalanceByAddress()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $addresses = $laraBlockIo->getAddresses()[0]->address;
            
            $res = $laraBlockIo->getAddressesBalanceByAddress($addresses);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('balances', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);
            
            $addresses = $this->randomString();
            
            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->getAddressesBalanceByAddress($addresses);

        }
        
        public function testGetAddressesBalanceByLabels()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $labels = $laraBlockIo->getAddresses()[0]->label;

            $res = $laraBlockIo->getAddressesBalanceByLabels($labels);    

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('balances', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);
            
            $labels = $this->randomString();
            
            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->getAddressesBalanceByLabels($labels);
        }

        public function testGetAddressByLabel()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $label = $laraBlockIo->getAddresses()[0]->label;
            
            $res = $laraBlockIo->getAddressByLabel($label);  

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('address', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data);
            $this->assertArrayHasKey('address', (array) $res->data);
            $this->assertArrayHasKey('label', (array) $res->data);
            $this->assertArrayHasKey('available_balance', (array) $res->data);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data);
            
            $label = $this->randomString();
            
            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->getAddressByLabel($label);
        }
        
        public function testGetUsers()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $res = $laraBlockIo->getUsers();
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('user_id', (array) $res->data->addresses[0]);
            $this->assertArrayHasKey('address', (array) $res->data->addresses[0]);
            $this->assertArrayHasKey('label', (array) $res->data->addresses[0]);
            $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
        }
        
        public function testGetUsersBalance()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $users = $laraBlockIo->getUsers()->data->addresses[0]->user_id;
            
            $res = $laraBlockIo->getUsersBalance($users);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('available_balance', (array) $res->data);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data);
            $this->assertArrayHasKey('balances', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('label', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('address', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('available_balance', (array) $res->data->balances[0]);
            $this->assertArrayHasKey('pending_received_balance', (array) $res->data->balances[0]);
            
            $users = $this->randomString();
            
            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->getUsersBalance($users);
        }
        
        public function testGetUserAddress()
        {
            $laraBlockIo =  new LaraBlockIo;
            
            $user = $laraBlockIo->getUsers()->data->addresses[0]->user_id;
            
            $res = $laraBlockIo->getUserAddress($user);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('user_id', (array) $res->data);
            $this->assertArrayHasKey('address', (array) $res->data);
            $this->assertArrayHasKey('label', (array) $res->data);
            $this->assertArrayHasKey('confirmed_balance', (array) $res->data);
            $this->assertArrayHasKey('unconfirmed_balance', (array) $res->data);
            
            $user = $this->randomString();
            
            $this->expectException(Exception::class);
            
            $res = $laraBlockIo->getUserAddress($user);
        }
    }

?>
