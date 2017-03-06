<?php

namespace Blockavel\Blockavel;

require_once __DIR__ . '/../vendor/autoload.php';

class Blockavel
{
    protected $blockIo;
    
    /**
     * Instantiating the BlockIo Class passing the API key, the pin, 
     * and the API version.
     */
    
    public function __construct()
    {
        $this->blockIo = new \BlockIo(
                config('blockavel-blockavel.apiKey'),
                config('blockavel-blockavel.pin'),
                config('blockavel-blockavel.version')
            );
    }
    
    /**
     * BlockIo getter method, returns a BlockIo object.
     */
    
    public function getBlockIo()
    {
        return $this->blockIo;
    }
    
    /**
     * Get the balance information associated with a Bitcoin Dogecoin,
     * or Litecoin account.
     */
    
    public function getBalanceInfo()
    {
        return $this->blockIo->get_balance();
    }
    
    public function getNetwork()
    {
        return $this->getBalanceInfo()->data->network;
    }
    
    public function getAvailableBalance()
    {
        return $this->getBalanceInfo()->data->available_balance;
    }
    
    public function getPendingReceivedBalance()
    {
        return $this->getBalanceInfo()->data->pending_received_balance;
    }
    
    /**
     * Create new address. Receives an associative array with 'label'
     * as key and the string values for the label(s). 
     * 
     * Ex: $array = array('label' => 'USER1')
     */
    
    public function createAddress($array)
    {
        return $this->blockIo->get_new_address($array);
    }
    
    /**
     * Get information assoiciated with a given address
     */
     
    public function getAddressString($address)
    {
        return $address->data->address;
    }
    
    public function getAddressLabel($address)
    {
        return $address->data->label;
    }
    
    public function getUserId($address)
    {
        return $address->data->user_id;
    }
    
    /**
     * Get all the (unarchived) addresses, their labels, user ids, and
     * balances on an account. Do not use this if you plan on 
     * having more than 2,500 addresses on your account. 
     * Use get_address_balance (below) instead.
     */
    
    public function getAddressesInfo()
    {
        return $this->blockIo->get_my_addresses();
    }
    
    public function getAddressesInfoWithoutBalances()
    {
        return $this->blockIo->get_my_addresses_without_balances();
    }
    
    /**
     * Get just the (unarchived) addresses associated with an account.
     */ 
    
    public function getAddresses()
    {
        return $this->getAddressesInfo()->data->addresses;
    }
    
    public function getAddressesWithoutBalances()
    {
        return $this->getAddressesInfoWithoutBalances()->data->addresses;
    }
    
    /**
     * Get address(es) balance by specified address(es) or label(s). This 
     * method can be used to query balances for external (non-account) 
     * addresses. If an external address' balance is returned, its 
     * user_id and label fields will be null.
     * 
     * Ex: $array = array('label' => 'USER1,USER2,...')
     * 
     * OR
     * 
     * Ex: $array = array('addresses' => 'ADDRESS1,ADDRESS2,...')
     */
    
    public function getAddressBalance($array)
    {
        return $this->blockIo->get_address_balance($array);
    }
    
    /**
     * Get address by label.
     */
     
    public function getAddressByLabel($array)
    {
        return $this->blockIo->get_address_by_label($array);
    }
    
    /**
     * Verifying the presicion of the provided amounts.
     * It is important to have the php7.0-bcmath package installed.
     * 
     * To install it in ubuntu run:
     *      sudo apt-get updatesudo  
     *      apt-get install php7.0-bcmath 
     */
    
    protected function setAmountsPrecision($array)
    {
        $amounts = explode(',', str_replace(' ', '', $array['amounts']));
        
        unset($array['amounts']);
        
        $temp = array();
        
        foreach($amounts as $amount)
        {
            $temp[] = bcadd($amount, '0', 8);
        }
        
        return array_merge(
                    ['amounts' => implode(',', array_values($temp))], 
                    $array
                );
    }
    
    /**
     * Get network fee estimate for transacting (withdrawing, sending).
     * Receives an associative array of addresses and amounts.
     * 
     * Ex $array = array(
     *                  'amounts' => 'AMOUNT1,AMOUNT2,...', 
     *                  'to_addresses' => 'ADDRESS1,ADDRESS2,...'
     *             )
     */
     
    public function getNetworkFeeEstimate($array)
    {
        return $this->blockIo->get_network_fee_estimate(
                    $this->setAmountsPrecision($array)
               );
    }
    
    /**
     * Withdraws amount of coins from any addresses in your account to up to 
     * 2500 destination addresses. If you have more than 2500 unarchived 
     * addresses on your account, you cannot use this method for 
     * withdrawal. Please use the more granular 
     * withdraw_from_addresses, and 
     * withdraw_from_labels 
     * methods instead.
     * 
     * Receives an associative array of amounts and addresses.
     * 
     * $array = array(
     *              'amounts' => 'AMOUNT1,AMOUNT2,...', 
     *              'to_addresses' => 'ADDRESS1,ADDRESS2,...',
     *              'nonce' => 'VALUE1,VALUE2,...' (optional)
     *          )
     */
    
    public function withdraw($array)
    {
        return $this->blockIo->withdraw(
                    $this->setAmountsPrecision($array)
               );
    }
    
    /**
     * Withdraws AMOUNT coins from upto 2500 addresses at a time, and deposits 
     * it to up to 2500 destination addresses.
     * 
     * Receives an associative array of amounts, from_addresses, & to_addresses
     * 
     * Ex: array(
     *          'amounts' => 'AMOUNT1,AMOUNT2,...', 
     *          'from_addresses' => 'ADDRESS1,ADDRESS2,...', 
     *          'to_addresses' => 'ADDRESS1,ADDRESS2,...',
     *          'nonce' => 'VALUE1,VALUE2,...' (optional)
     *     )
     */
     
    public function withdrawFromAdresses($array)
    {
        return $this->blockIo->withdraw_from_addresses(
                    $this->setAmountsPrecision($array)
               );     
    }
    
    /**
     * Withdraws AMOUNT coins from upto 2500 labels at a time, and deposits 
     * it to upto 2500 destination addresses, or labels.
     * 
     * Receives an associative array of amounts, from_labels, 
     * & to_addresses or to_labels
     * 
     * Ex: array(
     *          'amounts' => 'AMOUNT1,AMOUNT2,...', 
     *          'from_labels' => 'LABEL1,LABEL2,...',
     *          'to_addresses' => 'ADDRESS1,ADDRESS2,...',
     *          'nonce' => 'VALUE1,VALUE2,...' (optional)
     *     )
     * 
     * OR
     * 
     * Ex: array(
     *          'amounts' => 'AMOUNT1,AMOUNT2,...', 
     *          'from_labels' => 'LABEL1,LABEL2,...', 
     *          'to_labels' => 'LABEL1,LABEL2,...',
     *          'nonce' => 'VALUE1,VALUE2,...' (optional)
     *     )
     */
     
    public function withdrawFromLabels($array)
    {
        return $this->blockIo->withdraw_from_labels(
                    $this->setAmountsPrecision($array)
               );    
        
    }
    
    /**
     * Get transactions information.
     */
    
    public function getTransactionInfo($transaction)
    {
        return $transaction->data;
    }
    
    public function getTxid($transaction)
    {
        return $this->getTransactionInfo($transaction)->txid;
    }
    
    public function getAmountWithdrawn($transaction)
    {
        return $this->getTransactionInfo($transaction)->amount_withdrawn;
    }
    
    public function getAmountSent($transaction)
    {
        return $this->getTransactionInfo($transaction)->amount_sent;
    }
    
    public function getTransactionNetworkFee($transaction)
    {
        return $this->getTransactionInfo($transaction)->network_fee;
    }
    
    public function getBlockIoFee($transaction)
    {
        return $this->getTransactionInfo($transaction)->blockio_fee;
    }
    
    /**
     * Archiving of addresses help you control account bloat due to a large 
     * number of addresses. When an address is archived, it is: 
     *  -Not displayed in your wallet dashboard.
     *  -Not included in the get_my_addresses API call.
     *  -Not used to get available account balance.
     *  -Not used as a withdrawal address, unless specified.
     * Address archival can greatly enhance the operational security of your 
     * applications by allowing you to move coins to new addresses 
     * without clogging your API call responses.
     * 
     * Archives upto 100 addresses in a single API call. Addresses can be 
     * specified by their labels.
     * 
     * Receives an array of associative array of adresses or labels
     * 
     * Ex:
     * 
     * $array = array('addresses' => 'ADDRESS1,ADDRESS2,...')
     * 
     * OR
     * 
     * $array = array('labels' => 'LABEL1,LABEL2,...')
     */ 
    
    public function archiveAdresses($array)
    {
        return $this->blockIo->archive_addresses($array);
    }
    
    /**
     * Unarchives upto 100 addresses in a single API call. Addresses can be 
     * specified by their labels.
     * 
     * Receives an array of associative array of adresses or labels
     * 
     * Ex:
     * 
     * $array = array('addresses' => 'ADDRESS1,ADDRESS2,...')
     * 
     * OR
     * 
     * $array = array('labels' => 'LABEL1,LABEL2,...') 
     */
     
    public function unarchiveAddresses($array)
    {
        return $this->blockIo->unarchive_addresses($array);
    }
    
    /**
     * Returns all the archived addresses, their labels, and user ids on your 
     * account.
     */
     
    public function getArchivedAddresses()
    {
        return $this->blockIo->get_my_archived_addresses();
    }
    
    /**
     * Returns various data for the last 25 transactions spent or received. 
     * You can optionally specify a before_tx parameter to get earlier 
     * transactions.
     * 
     * You can use this method to query for addresses that are not on your 
     * account.
     * 
     * Each result provides a confidence rating that shows the network's belief 
     * in the transaction's viability. This is useful if you need to validate 
     * transactions quickly (for e.g., in retail store settings) without 
     * waiting for confirmations. We recommend waiting for confidence 
     * ratings to reach 0.90-0.99 for unconfirmed transactions if 
     * you need to validate it. For unconfirmed transactions, 
     * you are also provided with the number of nodes 
     * (propagated_by_nodes) on the Network that 
     * approve of the given unconfirmed 
     * transaction (out of 150 
     * sampled nodes).
     * 
     * If a double spend is detected for an unconfirmed transaction, its 
     * confidence rating falls to 0.0.
     * 
     * Receives the following arrays
     * 
     * array('type' => 'sent')
     * array('type' => 'received')
     * array('type' => 'sent', 'before_tx' => 'TXID')
     * array('type' => 'received', 'before_tx' => 'TXID')
     * array('type' => 'received', 'addresses' => 'ADDRESS1,ADDRESS2,...')
     * array('type' => 'received', 'user_ids' => 'USERID1,USERID2,...')
     * array('type' => 'received', 'labels' => 'LABEL1,LABEL2,...')
     * array('type' => 'sent', 'before_tx' => 'TXID', 
     *      'addresses' => 'ADDRESS1,ADDRESS2,...')
     * array('type' => 'received', 'before_tx' => 'TXID', 
     *      'addresses' => 'ADDRESS1,ADDRESS2,...')
     * 
     */
    
    public function getTransactions($array)
    {
        return $this->blockIo->get_transactions($array);
    }
    
    public function getAddressTranscations($array)
    {
        return $this->blockIo->get_address_balance($array);
    }
    
    /**
     * Returns the prices from the largest exchanges for Bitcoin, Dogecoin, 
     * or Litecoin, specified by the API Key. Specifying the base 
     * currency is optional.
     */
    
    public function getCurrentPrice($array = array())
    {
        return $this->blockIo->get_current_price($array);
    }
    
    /**
     * Returns an array of Block.io Green Addresses. Funds sent from Green 
     * Addresses are guaranteed by Block.io, and can be used immediately 
     * on receipt with zero network confirmations.
     * 
     * Receives the following array
     * 
     * array('addresses' => 'ADDRESS1,ADDRESS2,...')
     * 
     */
    
    public function isGreenAdress($array)
    {
        return $this->blockIo->is_green_address($array);
    }
    
    /**
     * Returns an array of transactions that were sent by Block.io Green 
     * Addresses. Funds sent from Green Addresses are guaranteed by 
     * Block.io, and can be used immediately on receipt with 
     * zero network confirmations. This API call does 
     * not need an API Key.
     * 
     * Receives the following array
     * 
     * array('transaction_ids' => 'TXID1,TXID2,...')
     */
     
    public function isGreenTransaction($array)
    {
        return $this->blockIo->is_green_transaction($array);
    }
    
    /**
     * Look for an incoming transactions, and know when they are done based on  
     * the confidence treshold. Returns true if the transactions are done.
     */
     
    public function confirmed($toAddress, $expectedAmount, $confidenceThreshold)
    {   
        $cnt = 0;
        
        while(true)
        {
            $txs = $this->getTransactions(
                        array('addresses' => $toAddress, 'type' => 'received')
                   )->data->txs;
            
            $paymentReceived = '0.0';

            foreach($txs as $tx)
            {
                foreach($tx->amounts_received as $amountReceived)
                {
                    if ($amountReceived->recipient == $toAddress) 
                    {
            		   if ($tx->confidence >= $confidenceThreshold) 
            		   {
            		      $paymentReceived = bcadd(
                                		          $amountReceived->amount, 
                                		          $paymentReceived, 8
                                		     );
            		   }
                    }
                }
            }
            
            if (bccomp($paymentReceived, $expectedAmount,8) >= 0) 
            {
                return true;
            } else {
                if($cnt > 10)
                {
                    return 'Amount Pending: ' . 
                            bccomp($paymentReceived, $expectedAmount,8) . 
                            PHP_EOL . 'Amount Received: ' . $paymentReceived .
                            'Amount Expected: ' . $expectedAmount;
                }
                
                sleep(1);
                $cnt++;
            }
        }
    }
}
