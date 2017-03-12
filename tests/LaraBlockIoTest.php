<?php

class LaraBlockIoTest extends Orchestra\Testbench\TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Blockavel\LaraBlockIo\LaraBlockIoServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'LaraBlockIo' => 'Blockavel\LaraBlockIo\LaraBlockIoFacade'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('larablockio.apiKey', getenv('BLOCKIO_API_KEY'));
        $app['config']->set('larablockio.pin', getenv('BLOCKIO_PIN'));
        $app['config']->set('larablockio.version', getenv('BLOCKIO_VERSION'));
    }

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

        $res = LaraBlockIo::getBalanceInfo();
        $this->assertTrue(gettype($res) == 'object');
        $this->assertArrayHasKey('status', (array) $res);
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);

    }

    public function testGetNetwork()
    {

        $res = LaraBlockIo::getNetwork();

        $this->assertTrue($res == 'BTCTEST' || $res = "LTCTEST" || $res = "DGCTEST");
    }

    public function testGetAvailableBalance()
    {

        $res = LaraBlockIo::getAvailableBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);
    }

    public function testGetPendingReceivedBalance()
    {

        $res = LaraBlockIo::getPendingReceivedBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);
    }

    public function testCreateAddress()
    {

        $label = $this->randomString();

        $res = LaraBlockIo::createAddress($label);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createAddress($label);

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

        $res = LaraBlockIo::getAddressesInfo();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
    }

    public function testGetAddressesInfoWithoutBalances()
    {

        $res = LaraBlockIo::getAddressesInfoWithoutBalances();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
    }

    public function testGetAddresses()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);
        $this->assertArrayHasKey('available_balance', (array) $res[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res[0]);
    }

    public function testGetAddressesWithoutBalance()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);
    }

    public function testGetAddressesBalanceByAddress()
    {

        $addresses = LaraBlockIo::getAddresses()[0]->address;

        $res = LaraBlockIo::getAddressesBalanceByAddress($addresses);

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

        $res = LaraBlockIo::getAddressesBalanceByAddress($addresses);

    }

    public function testGetAddressesBalanceByLabels()
    {

        $labels = LaraBlockIo::getAddresses()[0]->label;

        $res = LaraBlockIo::getAddressesBalanceByLabels($labels);

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

        $res = LaraBlockIo::getAddressesBalanceByLabels($labels);
    }

    public function testGetAddressByLabel()
    {

        $label = LaraBlockIo::getAddresses()[0]->label;

        $res = LaraBlockIo::getAddressByLabel($label);

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

        $res = LaraBlockIo::getAddressByLabel($label);
    }

    public function testGetUsers()
    {

        $res = LaraBlockIo::getUsers();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('user_id', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('address', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('label', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
    }

    public function testGetUsersBalance()
    {

        $users = LaraBlockIo::getUsers()->data->addresses[0]->user_id;

        $res = LaraBlockIo::getUsersBalance($users);

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

        $res = LaraBlockIo::getUsersBalance($users);
    }

    public function testGetUserAddress()
    {

        $user = LaraBlockIo::getUsers()->data->addresses[0]->user_id;

        $res = LaraBlockIo::getUserAddress($user);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('confirmed_balance', (array) $res->data);
        $this->assertArrayHasKey('unconfirmed_balance', (array) $res->data);

        $user = $this->randomString();

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getUserAddress($user);
    }
    
    public function testGetNetworkFeeEstimate()
    {
        
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));
        
        $amount = $addresses[0]->available_balance * .5;
        
        if($amount > .002)
        {
            $address = $addresses[count($addresses) - 1];
        
            $res = $laraBlockIo->getNetworkFeeEstimate($amount, $address->address);
            
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('estimated_network_fee', (array) $res->data);
        }
        
        $this->expectException(Exception::class);
        
        $address = $this->randomString();
        
        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);
        
        $this->expectException(Exception::class);
        
        $address = $addresses[count($addresses)];
        
        $amount = 0;

        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);
        
    }
}

?>
