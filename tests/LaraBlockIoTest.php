<?php

class LaraBlockIoTest extends Orchestra\Testbench\TestCase
{

    public $dTrustLabel;
    public $s1;
    public $s2;
    public $s3;

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

    public function setProperties()
    {
        $this->dTrustLabel = $this->randomString();
        $this->s1 = $this->randomString();
        $this->s2 = $this->randomString();
        $this->s3 = $this->randomString();
    }

    public function getDTrustLabel()
    {
        return $this->dTrustLabel;
    }

    public function getS1()
    {
        return $this->s1;
    }

    public function getS2()
    {
        return $this->s2;
    }

    public function getS3()
    {
        return $this->s3;
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
        sleep(1);

    }

    public function testGetNetwork()
    {

        $res = LaraBlockIo::getNetwork();

        $this->assertTrue($res == 'BTCTEST' || $res = "LTCTEST" || $res = "DGCTEST");

        sleep(1);
    }

    public function testGetAvailableBalance()
    {

        $res = LaraBlockIo::getAvailableBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);

        sleep(1);
    }

    public function testGetPendingReceivedBalance()
    {

        $res = LaraBlockIo::getPendingReceivedBalance();

        $this->assertTrue(is_numeric($res) && $res >= 0);

        sleep(1);
    }

    public function testCreateAddress()
    {

        $label = $this->randomString();

        $res = LaraBlockIo::createAddress($label);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createAddress($label);

        sleep(1);

        $this->assertTrue(gettype($res) == 'object');
        $this->assertArrayHasKey('status', (array) $res);
        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);

        LaraBlockIo::archiveAddressesByLabels($label);

        sleep(1);
    }

    public function testGetAddressInfo()
    {

        $res = LaraBlockIo::getAddressesInfo();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);

        sleep(1);
    }

    public function testGetAddressesInfoWithoutBalances()
    {

        $res = LaraBlockIo::getAddressesInfoWithoutBalances();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);

        sleep(1);
    }

    public function testGetAddresses()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);
        $this->assertArrayHasKey('available_balance', (array) $res[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res[0]);

        sleep(1);
    }

    public function testGetAddressesWithoutBalance()
    {

        $res = LaraBlockIo::getAddresses();

        $this->assertArrayHasKey('user_id', (array) $res[0]);
        $this->assertArrayHasKey('address', (array) $res[0]);
        $this->assertArrayHasKey('label', (array) $res[0]);

        sleep(1);
    }

    public function testGetAddressesBalanceByAddress()
    {

        $addresses = LaraBlockIo::getAddresses()[0]->address;

        sleep(1);

        $res = LaraBlockIo::getAddressesBalanceByAddress($addresses);

        sleep(1);

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

        sleep(1);

    }

    public function testGetAddressesBalanceByLabels()
    {

        $labels = LaraBlockIo::getAddresses()[0]->label;

        sleep(1);

        $res = LaraBlockIo::getAddressesBalanceByLabels($labels);

        sleep(1);

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

        sleep(1);
    }

    public function testGetAddressByLabel()
    {

        $label = LaraBlockIo::getAddresses()[0]->label;

        sleep(1);

        $res = LaraBlockIo::getAddressByLabel($label);

        sleep(1);

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

        sleep(1);
    }

    public function testGetUsers()
    {

        $res = LaraBlockIo::getUsers();

        sleep(1);

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

        sleep(1);

        $res = LaraBlockIo::getUsersBalance($users);

        sleep(1);

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

        sleep(1);
    }

    public function testGetUserAddress()
    {

        $user = LaraBlockIo::getUsers()->data->addresses[0]->user_id;

        sleep(1);

        $res = LaraBlockIo::getUserAddress($user);

        sleep(1);

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

        sleep(1);
    }

    public function cmp($a, $b)
    {
        return $a->available_balance < $b->available_balance;
    }

    public function testGetNetworkFeeEstimate()
    {

        $addresses = LaraBlockIo::getAddresses();

        sleep(1);

        usort($addresses, array($this, "cmp"));

        $amount = $addresses[0]->available_balance * 0.5;

        if($amount > .001)
        {
            $address = $addresses[count($addresses) - 1];

            $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address->address);

            sleep(1);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('estimated_network_fee', (array) $res->data);
        }

        $this->expectException(Exception::class);

        $address = $this->randomString();

        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);

        sleep(1);

        $this->expectException(Exception::class);

        $address = $addresses[count($addresses)]->address;

        $amount = 0;

        $res = LaraBlockIo::getNetworkFeeEstimate($amount, $address);

        sleep(1);

    }

    public function testWithdraw()
    {
        $addresses = LaraBlockIo::getAddresses();

        sleep(1);

        usort($addresses, array($this, "cmp"));

        if($addresses[0]->available_balance * 0.5 > 0.002)
        {
            $amount = .001;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdraw($amount, $toAddresses);

            sleep(1);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }

        $this->expectException(Exception::class);

        $toAddresses = $this->randomString();

        $res = LaraBlockIo::withdraw($amount, $toAddresses);

        sleep(1);

        $this->expectException(Exception::class);

        $toAddresses = $addresses[count($addresses)]->address;

        $amount = 0;

        $res = LaraBlockIo::withdraw($amount, $toAddresses);

        sleep(1);
    }

    public function testWithdrawFromAddressesToAddresses()
    {
        $addresses = LaraBlockIo::getAddresses();

        sleep(1);

        usort($addresses, array($this, "cmp"));

        if($addresses[0]->available_balance * 0.5 > 0.002)
        {
            $amounts = .001;
            $fromAddresses = $addresses[0]->address;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

            sleep(1);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }

        sleep(1);

        $this->expectException(Exception::class);

        $toAddresses = $this->randomString();

        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

        sleep(1);

        $this->expectException(Exception::class);

        $toAddresses = $addresses[count($addresses) - 1]->address;

        $fromAddresses = $this->randomString();

        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

        sleep(1);

        $this->expectException(Exception::class);

        $amounts = 0;
        $fromAddresses = $addresses[0]->address;
        $toAddresses = $addresses[count($addresses) - 1]->address;

        $res = LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

        sleep(1);

    }

    public function testWithdrawFromLabelsToLabels()
    {
        $addresses = LaraBlockIo::getAddresses();

        usort($addresses, array($this, "cmp"));

        sleep(1);

        if($addresses[0]->available_balance * 0.5 > 0.002)
        {
            $amounts = .001;
            $fromLabels = $addresses[0]->label;
            $toLabels = $addresses[count($addresses) - 1]->label;
            $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);

            sleep(1);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }

        sleep(1);

        $this->expectException(Exception::class);

        $toLabels = $this->randomString();

        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);

        $this->expectException(Exception::class);

        $toLabels = $addresses[count($addresses) - 1]->label;

        $fromLabels = $this->randomString();

        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);

        sleep(1);

        $this->expectException(Exception::class);

        $amounts = 0;
        $fromLabels = $addresses[0]->label;
            $toLabels = $addresses[count($addresses) - 1]->label;

        $res = LaraBlockIo::withdrawFromLabelsToLabels($amounts, $fromLabels, $toLabels);

        sleep(1);

    }

    public function testWithdrawFromLabelsToAddresses()
    {
        $addresses = LaraBlockIo::getAddresses();

        sleep(1);

        usort($addresses, array($this, "cmp"));

        if($addresses[0]->available_balance * 0.5 > 0.002)
        {
            $amounts = .001;
            $fromLabels = $addresses[0]->label;
            $toAddresses = $addresses[count($addresses) - 1]->address;
            $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);

            sleep(1);

            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }

        sleep(1);

        $this->expectException(Exception::class);

        $toLabels = $this->randomString();

        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);

        sleep(1);

        $this->expectException(Exception::class);

        $toLabels = $addresses[count($addresses) - 1]->label;

        $fromLabels = $this->randomString();

        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);

        sleep(1);

        $this->expectException(Exception::class);

        $amounts = 0;
        $fromLabels = $addresses[0]->label;
        $toAddresses = $addresses[count($addresses) - 1]->address;

        $res = LaraBlockIo::withdrawFromLabelsToAddresses($amounts, $fromLabels, $toAddresses);

        sleep(1);
    }

    public function testArchiveAndUnarchiveAddressesByAddress()
    {
        $address = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->address;

        sleep(1);

        $res = LaraBlockIo::archiveAddressesByAddress($address);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == true);

        sleep(1);

        $res = LaraBlockIo::unarchiveAddressesByAddress($address);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == false);

        sleep(1);
    }

    public function testArchiveAndUnarchiveAddressesByLabels()
    {
        $label = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->label;

        sleep(1);

        $res = LaraBlockIo::archiveAddressesByLabels($label);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == true);

        sleep(1);

        $res = LaraBlockIo::unarchiveAddressesByLabels($label);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));
        $this->assertArrayHasKey('archived', (array) ($res->data->addresses[0]));
        $this->assertTrue($res->data->addresses[0]->archived == false);

        sleep(1);
    }

    public function testGetArchivedAddresses()
    {

        $label = LaraBlockIo::getAddresses()[count(LaraBlockIo::getAddresses()) - 1]->label;

        sleep(1);

        $res = LaraBlockIo::archiveAddressesByLabels($label);

        sleep(1);

        $res = LaraBlockIo::getArchivedAddresses();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) ($res->data->addresses[0]));

        sleep(1);

        $res = LaraBlockIo::unarchiveAddressesByLabels($label);

        sleep(1);
    }

    public function testGetTransactionsByAddresses()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));
        $address = $addresses[0]->address;

        $res = LaraBlockIo::getTransactionsByAddresses($type, $address);

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);

        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $address);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());

        sleep(1);
    }

    public function testGetTransactionsByLabels()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;
        usort($addresses, array($this, "cmp"));
        $label = $addresses[0]->label;

        $res = LaraBlockIo::getTransactionsByLabels($type, $label);

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);

        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }

        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $label);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());

        sleep(1);
    }

    public function getTransactionsByUserIds()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getAddresses();;

        sleep(1);

        usort($addresses, array($this, "cmp"));
        $userId = $addresses[0]->user_id;

        $res = LaraBlockIo::getTransactionsByUserIds($type, $userId);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($this->randomString(), $userId);

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getTransactionsByAddresses($type, $this->randomString());

        sleep(1);
    }

    public function testGetReceivedTransactions()
    {
        $res = LaraBlockIo::getReceivedTransactions();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('recipient', (array) $res->data->txs[0]->amounts_received[0]);
        $this->assertArrayHasKey('amount', (array) $res->data->txs[0]->amounts_received[0]);
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);
    }

    public function testGetSentTransactions()
    {
        $res = LaraBlockIo::getSentTransactions();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('recipient', (array) $res->data->txs[0]->amounts_sent[0]);
        $this->assertArrayHasKey('amount', (array) $res->data->txs[0]->amounts_sent[0]);
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);
    }

    public function testIsGreenTransaction()
    {
        $txid = LaraBlockIo::getReceivedTransactions()->data->txs[0]->txid;

        sleep(1);

        $res = LaraBlockIo::isGreenTransaction($txid);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('green_txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->green_txs[0]);
        $this->assertArrayHasKey('network', (array) $res->data->green_txs[0]);

        sleep(1);

    }

    public function testGetNotConfirmedTxs()
    {
        $addresses = LaraBlockIo::getAddresses();;

        sleep(1);

        usort($addresses, array($this, "cmp"));

        $toAddress = $addresses[0]->address;
        $confidenceThreshold = '0.99';

        $res = LaraBlockIo::getNotConfirmedTxs($toAddress, $confidenceThreshold);

        if(count($res) > 0)
        {
            $this->assertArrayHasKey('txid', (array) $res[0]);
            $this->assertArrayHasKey('from_green_address', (array) $res[0]);
            $this->assertArrayHasKey('time', (array) $res[0]);
            $this->assertArrayHasKey('confirmations', (array) $res[0]);
            $this->assertArrayHasKey('amounts_received', (array) $res[0]);
            $this->assertArrayHasKey('senders', (array) $res[0]);
            $this->assertArrayHasKey('confidence', (array) $res[0]);
            $this->assertArrayHasKey('propagated_by_nodes', (array) $res[0]);
        }


        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getNotConfirmedTxs($toAddress, $this->randomString());

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getNotConfirmedTxs($this->randomString(), $confidenceThreshold);

        sleep(1);
    }

    protected function createMultiSigAddress()
    {
        $reqSigs = 2;

        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), $reqSigs, $this->getS1(), $this->getS2(), $this->getS3()
        );

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('additional_required_signatures', (array) $res->data);
        $this->assertArrayHasKey('additional_signers', (array) $res->data);
        $this->assertArrayHasKey('redeem_script', (array) $res->data);

        $addresses = LaraBlockIo::getAddresses();
        usort($addresses, array($this, "cmp"));

        sleep(1);

        if($addresses[0]->available_balance > .005)
        {
            $amounts = 0.004;
            $fromAddresses = $addresses[0]->address;
            $toAddresses = LaraBlockIo::getDTrustInfoByLabel($this->getDTrustLabel())->data->address;

            sleep(1);

            LaraBlockIo::withdrawFromAddressesToAddresses($amounts, $fromAddresses, $toAddresses);

            sleep(1);

            $this->multiSigWithdraw();
        }

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), $reqSigs, $this->getS1()
        );

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createMultiSigAddress(
            $this->getDTrustLabel(), 0, $this->getS1(), $this->getS2(), $this->getS3()
        );

        sleep(1);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::createMultiSigAddress(
            '', 0, $this->getS1(), $this->getS2(), $this->getS3()
        );

        sleep(1);
    }

    public function testGetDTrustAddresses()
    {

        $res = LaraBlockIo::getDTrustAddresses();

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('addresses', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('user_id', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('label', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('available_balance', (array) $res->data->addresses[0]);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data->addresses[0]);
    }


    protected function getDTrustInfoByLabel()
    {
        $label = $this->getDTrustLabel();

        $res = LaraBlockIo::getDTrustInfoByLabel($this->getDTrustLabel());

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('address', (array) $res->data);
        $this->assertArrayHasKey('user_id', (array) $res->data);
        $this->assertArrayHasKey('label', (array) $res->data);
        $this->assertArrayHasKey('available_balance', (array) $res->data);
        $this->assertArrayHasKey('pending_received_balance', (array) $res->data);

        $this->expectException(Exception::class);

        $res = LaraBlockIo::getDTrustInfoByLabel($this->stringRandom());

        sleep(1);
    }


    protected function multiSigWithdraw()
    {

        $addresses = LaraBlockIo::getAddresses();

        sleep(1);

        usort($addresses, array($this, "cmp"));

        $label = $this->getDTrustLabel();

        $toAddress = $addresses[0]->address;

        $amount = .001;

        if(LaraBlockIo::getDTrustInfoByLabel($label)->data->available_balance >= 0.002)
        {

            sleep(1);

            $res = LaraBlockIo::multiSigWithdraw($label, $toAddress, $amount);

            sleep(1);

            $reference_id = $res['reference_id'];

            $this->assertArrayHasKey('data', (array) $res['response']);
            $this->assertArrayHasKey('reference_id', (array) $res['response']->data);
            $this->assertArrayHasKey('more_signatures_needed', (array) $res['response']->data);
            $this->assertArrayHasKey('inputs', (array) $res['response']->data);
            $this->assertArrayHasKey('input_no', (array) $res['response']->data->inputs[0]);
            $this->assertArrayHasKey('signatures_needed', (array) $res['response']->data->inputs[0]);
            $this->assertArrayHasKey('data_to_sign', (array) $res['response']->data->inputs[0]);
            $this->assertArrayHasKey('signers', (array) $res['response']->data->inputs[0]);
            $this->assertArrayHasKey('encrypted_passphrase', (array) $res['response']->data);

            $this->signMultiSigWithdraw($reference_id);
        }

        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::multiSigWithdraw($this->randomString(), $toAddress, $amount);
        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::multiSigWithdraw($label, $this->randomString(), $amount);
        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::multiSigWithdraw($label, $toAddress, $this->randomString());
        sleep(1);

    }

    protected function signMultiSigWithdraw($referenceId)
    {
        $passPhrase = $this->getS1();

        $res = LaraBlockIo::signMultiSigWithdraw($referenceId, $passPhrase);

        sleep(1);

        if(is_numeric($res) && $res > 0)
        {
            $this->assertTrue(is_numeric($res) && $res > 0);
        }

        $passPhrase = $this->getS2();

        $res = LaraBlockIo::signMultiSigWithdraw($referenceId, $passPhrase);

        sleep(1);

        if(!is_numeric($res))
        {
            $this->assertArrayHasKey('data', (array) $res);
            $this->assertArrayHasKey('network', (array) $res->data);
            $this->assertArrayHasKey('txid', (array) $res->data);
            $this->assertArrayHasKey('amount_withdrawn', (array) $res->data);
            $this->assertArrayHasKey('amount_sent', (array) $res->data);
            $this->assertArrayHasKey('network_fee', (array) $res->data);
        }

        $this->expectException(Exception::class);

        LaraBlockIo::signMultiSigWithdraw($this->randomString(), $passPhrase);

        sleep(1);

        $this->expectException(Exception::class);

        LaraBlockIo::signMultiSigWithdraw($referenceId, $this->randomString());

        sleep(1);
    }

    public function testDTrust()
    {
        $this->setProperties();
        $this->createMultiSigAddress();
        $this->getDTrustInfoByLabel();
    }

    public function testGetSentDTrustTransactions()
    {
        $res = LaraBlockIo::getSentDTrustTransactions();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);
    }

    public function testGetReceivedDTrustTransactions()
    {
        $res = LaraBlockIo::getReceivedDTrustTransactions();

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        sleep(1);
    }

    public function testGetDTrustTransactionsByAddress()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getDTrustAddresses()->data->addresses;

        sleep(1);

        usort($addresses, array($this, "cmp"));
        $address = $addresses[0]->address;

        $res = LaraBlockIo::getDTrustTransactionsByAddresses($type, $address);

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByAddresses($this->randomString(), $address);

        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByAddresses($typpe, $this->randomString());

        sleep(1);
    }

    public function testGetDTrustTransactionsByLabels()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getDTrustAddresses()->data->addresses;

        sleep(1);

        usort($addresses, array($this, "cmp"));
        $label = $addresses[0]->label;

        $res = LaraBlockIo::getDTrustTransactionsByLabels($type, $label);

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByLabels($this->randomString(), $label);

        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByLabels($typpe, $this->randomString());

        sleep(1);
    }

    /*public function testGetDTrustTransactionsByLabels()
    {
        $type = array_rand(array('sent' => 0,'received' => 1));
        $addresses = LaraBlockIo::getDTrustAddresses()->data->addresses;

        sleep(1);

        usort($addresses, array($this, "cmp"));
        $userId = $addresses[0]->user_id;

        $res = LaraBlockIo::getDTrustTransactionsByUserIds($type, $userId);

        sleep(1);

        $this->assertArrayHasKey('data', (array) $res);
        $this->assertArrayHasKey('network', (array) $res->data);
        $this->assertArrayHasKey('txs', (array) $res->data);
        $this->assertArrayHasKey('txid', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('from_green_address', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('time', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confirmations', (array) $res->data->txs[0]);
        if(strcasecmp($type, 'sent') == 0)
        {
            $this->assertArrayHasKey('total_amount_sent', (array) $res->data->txs[0]);
            $this->assertArrayHasKey('amounts_sent', (array) $res->data->txs[0]);
        }
        else
        {
            $this->assertArrayHasKey('amounts_received', (array) $res->data->txs[0]);
        }
        $this->assertArrayHasKey('senders', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('confidence', (array) $res->data->txs[0]);
        $this->assertArrayHasKey('propagated_by_nodes', (array) $res->data->txs[0]);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByUserIds($this->randomString(), $userId);

        sleep(1);

        $this->expectException(Exception::class);
        LaraBlockIo::getDTrustTransactionsByUserIds($typpe, $this->randomString());

        sleep(1);
    }*/
}

?>
