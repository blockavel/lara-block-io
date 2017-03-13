<?php

namespace Blockavel\LaraBlockIo;

class LaraBlockIo
{

    /**
     * The BlockIo instance
     *
     * @var BlockIo
     */
    protected $blockIo;

    /**
     * Instantiating the BlockIo Class passing the API key, the pin,
     * and the API version. Define your environment variables in the
     * Laravel in the respective .env file
     */
    public function __construct()
    {
        $this->blockIo = new \BlockIo(
                config('larablockio.apiKey'),
                config('larablockio.pin'),
                config('larablockio.version')
            );
    }

    /**
     * BlockIo getter method, returns a BlockIo object.
     *
     * @return BlockIo
     */
    public function getBlockIo()
    {
        return $this->blockIo;
    }

    /**
     * Get the balance information associated with a Bitcoin Dogecoin,
     * or Litecoin account.
     *
     * @return object Contains balance information
     */
    public function getBalanceInfo()
    {
        return $this->blockIo->get_balance();
    }

    /**
     * Get the Network associated with your API KEY
     *
     * @return string Contains network information
     */
    public function getNetwork()
    {
        return $this->getBalanceInfo()->data->network;
    }

    /**
     * Get the total balance of your entire network
     *
     * @return string Contains the balance value
     */
    public function getAvailableBalance()
    {
        return $this->getBalanceInfo()->data->available_balance;
    }

    /**
     * Get the the balance that's pending confirmation in your network
     *
     * @return string Contains the pending balance
     */
    public function getPendingReceivedBalance()
    {
        return $this->getBalanceInfo()->data->pending_received_balance;
    }

    /**
     * Create new address. Receives a string and uses the value
     * as label to create a new wallet
     *
     * @param string $label Containing the label of the wallet
     * @return object Contains the status of wallet creation
     */

    public function createAddress($label)
    {
        return $this->blockIo->get_new_address(['label' => $label]);
    }

    /**
     * Get all the (unarchived) addresses, their labels, user ids, and
     * balances on an account. Do not use this if you plan on
     * having more than 2,500 addresses on your account.
     * Use get_address_balance (below) instead.
     *
     * @return object An object of objects containing all the addresses
     */
    public function getAddressesInfo()
    {
        return $this->blockIo->get_my_addresses();
    }

    /**
     * Get all the (unarchived) addresses, their labels and user ids on
     * an account.
     *
     * @return object An object of objects containing all the addresses
     */
    public function getAddressesInfoWithoutBalances()
    {
        return $this->blockIo->get_my_addresses_without_balances();
    }

    /**
     * Get just the (unarchived) addresses associated with an account,
     * their labels, user ids, available and pending balances
     *
     * @return array Contains objects of addresses
     */
    public function getAddresses()
    {
        return $this->getAddressesInfo()->data->addresses;
    }

    /**
     * Get just the (unarchived) addresses associated with an account,
     * their labels and user ids
     *
     * @return array Contains objects of addresses
     */
    public function getAddressesWithoutBalances()
    {
        return $this->getAddressesInfoWithoutBalances()->data->addresses;
    }

    /**
     * Get address(es) balance by specified address(es). This
     * method can be used to query balances for external (non-account)
     * addresses. If an external address' balance is returned, its
     * user_id and label fields will be null.
     *
     * @param string $addresses Containing comma separated addresses
     * @return object Contains information associate with each address
     */
    public function getAddressesBalanceByAddress($addresses)
    {
        return $this->blockIo->get_address_balance(['addresses' => $addresses]);
    }

    /**
     * Get address(es) balance by specified label(s). This
     * method can be used to query balances for external (non-account)
     * addresses. If an external address' balance is returned, its
     * user_id and label fields will be null.
     *
     * @param string $labels Containing comma separated labels
     * @return object Contains information associate with each address
     */
    public function getAddressesBalanceByLabels($labels)
    {
        return $this->blockIo->get_address_balance(['label' => $labels]);
    }

    /**
     * Get address by label.
     *
     * @param string $label Containing the wallet's label
     * @return object Contains information associated with the wallet
     */
    public function getAddressByLabel($label)
    {
        return $this->blockIo->get_address_by_label(['label' => $label]);
    }

    /**
     * Get all users in your network
     *
     * @return object Contains all the users and associated information
     */
    public function getUsers()
    {
        return $this->blockIo->get_users();
    }

    /**
     * Get user(s)' balance
     *
     * @param string $userIds Containing comma separated user ids
     * @return object Contains information about the respective users
     */
    public function getUsersBalance($userIds)
    {
        return $this->blockIo->get_user_balance(['user_id' => $userIds]);
    }

    /**
     * Get a user's address
     *
     * @param string $userId Containing a single user id
     * @return object Contains user's address and balance information
     */
    public function getUserAddress($userId)
    {
        return $this->blockIo->get_user_address(['user_id' => $userId]);
    }

    /**
     * Verifying the presicion of the provided amounts.
     * You need to have the php7.0-bcmath package installed.
     *
     * To install it in ubuntu run:
     *      sudo apt-get updatesudo
     *      apt-get install php7.0-bcmath
     *
     * @param array $array
     * @return string|Exception
     */
    protected function setAmountsPrecision($array)
    {
        $amounts = explode(',', str_replace(' ', '', $array['amounts']));
        unset($array['amounts']);
        $temp = array();

        try
        {
            foreach($amounts as $amount)
            {
                $temp[] = bcadd($amount, '0', 8);
            }

            return array_merge(
                        ['amounts' => implode(',', array_values($temp))],
                        $array
                    );
        }
        catch(Exception $e)
        {
            $e->getMessage();
        }
    }

    /**
     * Get network fee estimate for transacting (withdrawing, sending).
     * Note: Amount should be below available balance
     *
     * @param $amounts string Containing comma separated amount values
     * @param $addresses string Containing comma separated address values
     * @return object Contains estimated network fee for the amount
     */
    public function getNetworkFeeEstimate($amounts, $addresses)
    {
        return $this->blockIo->get_network_fee_estimate(
                    $this->setAmountsPrecision([
                        'amounts' => $amounts,
                        'to_addresses' => $addresses
                    ])
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

    public function withdraw($amounts, $toAddresses, $nonce = null)
    {
        $array = [
            'amounts' => $amounts,
            'to_addresses' => $toAddresses,
            'nonce' => $nonce
        ];

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

    public function withdrawFromAddressesToAddresses(
        $amounts, $fromAddresses, $toAddresses, $nonce = null
    )
    {
        $array = [
            'amounts' => $amounts,
            'from_addresses' => $fromAddresses,
            'to_addresses' => $toAddresses,
            'nonce' => $nonce
        ];

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

    public function withdrawFromLabelsToLabels(
        $amounts, $fromLabels, $toLabels, $nonce = null)
    {
        $array = [
            'amounts' => $amounts,
            'from_labels' => $fromLabels,
            'to_labels' => $toLabels,
            'nonce' => $nonce
        ];

        return $this->blockIo->withdraw_from_labels(
                    $this->setAmountsPrecision($array)
               );

    }

    public function withdrawFromLabelsToAddresses(
        $amounts, $fromLabels, $toAddresses, $nonce = null)
    {
        $array = [
            'amounts' => $amounts,
            'from_labels' => $fromLabels,
            'to_addresses' => $toAddresses,
            'nonce' => $nonce
        ];

        return $this->blockIo->withdraw_from_labels(
                    $this->setAmountsPrecision($array)
               );

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

    public function archiveAddressesByAddress($addresses)
    {
        $array = [
            'addresses' => $addresses
        ];

        return $this->blockIo->archive_addresses($array);
    }

    public function archiveAddressesByLabels($labels)
    {
        $array = [
            'labels' => $labels
        ];

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

    public function unarchiveAddressesByAddress($addresses)
    {
        $array = [
            'addresses' => $addresses
        ];

        return $this->blockIo->unarchive_addresses($array);
    }

    public function unarchiveAddressesByLabels($labels)
    {
        $array = [
            'labels' => $labels
        ];

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

    public function getTransactionsByAddresses(
        $type, $addresses, $beforeTx = null
    )
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'addresses' => $addresses,
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'addresses' => $addresses,
                'before_tx' => $beforeTx,
            ];
        }

        return $this->blockIo->get_transactions($array);
    }

    public function getTransactionsByLabels(
        $type, $labels, $beforeTx = null
    )
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'labels' => $labels
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'before_tx' => $beforeTx,
                'labels' => $labels
            ];
        }

        return $this->blockIo->get_transactions($array);
    }

    public function getTransactionsByUserIds(
        $type, $userIds, $beforeTx = null
    )
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'user_ids' => $userIds
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'before_tx' => $beforeTx,
                'user_ids' => $userIds
            ];
        }

        return $this->blockIo->get_transactions($array);
    }

    public function getReceivedTransactions($beforeTx = null)
    {
        if(is_null($beforeTx)) $array = ['type' => 'received'];
        else $array = ['type' => 'received', 'before_tx' => $beforeTx];

        return $this->blockIo->get_transactions($array);
    }

    public function getSentTransactions($beforeTx = null)
    {
        if(is_null($beforeTx)) $array = ['type' => 'sent'];
        else $array = ['type' => 'sent', 'before_tx' => $beforeTx];

        return $this->blockIo->get_transactions($array);
    }


    /**
     * Returns the prices from the largest exchanges for Bitcoin, Dogecoin,
     * or Litecoin, specified by the API Key. Specifying the base
     * currency is optional.
     */

    public function getCurrentPrice($baseCurrency = null)
    {
        if(!is_null($baseCurrency)) $array = ['price_base' => $baseCurrency];

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
     * Not working properly.
     *
     */

    public function isGreenAddress($addresses)
    {
        $array = ['addresses' => $addresses];

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

    public function isGreenTransaction($txIds)
    {
        $array = ['transaction_ids' => $txIds];

        return $this->blockIo->is_green_transaction($array);
    }

    /**
     * Look for incoming transactions, and know when they have been processed.
     * Returns true if the transactions are done.
     *
     * We can do the number of pending transactions related to an adress, and
     * calculate the expcted amount ourselves. Just asking for the
     * confidence treshold and the recipient adress as inputs.
     */

    public function getNotConfirmedTxs($toAddress, $confidenceThreshold)
    {
        $txs = $this->blockIo->get_transactions(
                        array('addresses' => $toAddress, 'type' => 'received')
                   )->data->txs;

        $txs = array_where($txs, function($value) use ($confidenceThreshold){
                        if($value->confidence < $confidenceThreshold
                            && $value->from_green_address == true)
                        {
                            return $value;
                        }
                        elseif($value->confidence < $confidenceThreshold
                            || ($value->from_green_address == false
                            && $value->confirmations < 3))
                        {
                            return $value;
                        }
                });

        return $txs;

    }
    /**
     * Get all dTrust addresses
     */

    public function getDTrustAddresses()
    {
        return $this->blockIo->get_my_dtrust_addresses();
    }

    protected function createPassphrases($passphrases_array)
    {
        $passphrases = [];

        foreach(array_values($passphrases_array) as $passphrase)
        {
            $passphrases[] = strToHex($passphrase);
        }

        return $passphrases;
    }

    protected function createKeys($passphrases)
    {
        $keys = [];

        foreach(array_values($passphrases) as $passphrase)
        {
            $keys[] = $this->blockIo
                           ->initKey()
                           ->fromPassphrase($passphrase)
                           ->getPublicKey();
        }

        return $keys;

    }

    public function createMultiSigAddress(
                        $label,
                        $reqSigs,
                        $s1 = null,
                        $s2 = null,
                        $s3 = null,
                        $s4 = null
                    )
    {
        $passphrases_array = [];

        if(!is_null($s4)) array_push($passphrases_array, $s4);
        if(!is_null($s3)) array_push($passphrases_array, $s3);
        if(!is_null($s2)) array_push($passphrases_array, $s2);
        if(!is_null($s1)) array_push($passphrases_array, $s1);

        $passphrases = $this->createPassphrases($passphrases_array);

        $keys = $this->createKeys($passphrases);

        $pubKeyStr = implode(',', $keys);

        return $this->blockIo->get_new_dtrust_address(
                                    array(
                                        'label' => $label,
                                        'public_keys' => $pubKeyStr,
                                        'required_signatures' => $reqSigs
                                    )
                                );
    }

    public function getDTrustInfoByLabel($label)
    {
        $array = ['label' => $label];

        return $this->blockIo->get_dtrust_address_by_label($array);
    }

    public function multiSigWithdraw($label, $toAddress, $amount)
    {
        $array = [
            'from_labels' => $label,
            'to_addresses' => $toAddress,
            'amounts' => $amount
        ];

        $response = $this->blockIo->withdraw_from_dtrust_address($array);

        $reference_id = $response->data->reference_id;

        return compact('response', 'reference_id');
    }

    /**
     * Returns the pending withdrawal from a MultiSig address. Receives the
     * following parameter:
     *
     * array('reference_id' => 'REFERENCEID')
     */

    protected function getKey($passphrase)
    {
        return $this->blockIo->initKey()->fromPassphrase(strToHex($passphrase));
    }

    protected function signDTrust($response)
    {
        $json_string = json_encode($response->data->details);

    	return $this->blockIo->sign_transaction(
    	                            array('signature_data' => $json_string)
    	                       );
    }

    protected function getSigCount($reference_id)
    {
        $array = array('reference_id' => $reference_id);

        $response = $this->getMultiSigWithdraw($array)->data->details;

        if($response->more_signatures_needed)
        {
            $count = 0;

            foreach($response->inputs as $input)
            {
                $count += $input->signatures_needed;
            }

            return $count;
        }
        else return 0;
    }

    protected function closeMultiSigTxs($reference_id)
    {
        return $this->blockIo->finalize_transaction(
                                    array('reference_id' => $reference_id)
                               );
    }

    public function getMultiSigWithdraw($array)
    {
        return $this->blockIo->get_remaining_signers($array);
    }

    public function signMultiSigWithdraw($reference_id, $passphrase)
    {
        $array = array('reference_id' => $reference_id);

        $response = $this->getMultiSigWithdraw($array);

        $key = $this->getKey($passphrase);

        $signature = &$key;

        foreach($response->data->details->inputs as &$input)
        {
            $dataToSign = $input->data_to_sign;

            foreach($input->signers as &$signer)
            {
                if($signer->signer_public_key == $signature->getPublicKey())
                {
                    $signer->signed_data = $signature->signHash($dataToSign);
                    break;
                }
            }
        }

        $this->signDTrust($response);

        $reqSigs = $this->getSigCount($reference_id);

        if($reqSigs == 0)
        {
            return $this->closeMultiSigTxs($reference_id);
        }

        return $reqSigs;
    }

    public function getSentDTrustTransactions($beforeTx = null)
    {
        if(is_null($beforeTx)) $array = ['type' => 'sent'];
        else $array = ['type' => 'sent', 'before_tx' => $beforeTx];

        return $this->blockIo->get_dtrust_transactions($array);
    }

    public function getReceivedDTrustTransactions($beforeTx = null)
    {
        if(is_null($beforeTx)) $array = ['type' => 'received'];
        else $array = ['type' => 'received', 'before_tx' => $beforeTx];

        return $this->blockIo->get_dtrust_transactions($array);
    }

    public function getDtrustTransactionsByAddress(
        $type, $addresses, $beforeTx = null
    )
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'addresses' => $addresses,
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'addresses' => $addresses,
                'before_tx' => $beforeTx,
            ];
        }

        return $this->blockIo->get_dtrust_transactions($array);
    }

    public function getDtrustTransactionsByLabel(
        $type, $labels, $beforeTx = null)
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'labels' => $labels
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'before_tx' => $beforeTx,
                'labels' => $labels
            ];
        }

        return $this->blockIo->get_dtrust_transactions($array);
    }

    public function getDTrustTransactionsByUserIds(
        $type, $userIds, $beforeTx = null
    )
    {
        if(is_null($beforeTx))
        {
            $array = [
                'type' => $type,
                'user_ids' => $userIds
            ];
        }
        else
        {
            $array = [
                'type' => $type,
                'before_tx' => $beforeTx,
                'user_ids' => $userIds
            ];
        }

        return $this->blockIo->get_dtrust_transactions($array);
    }

    public function getDTrustAddressBalance($addresses)
    {
        $array = ['addresses' => $addresses];

        return $this->blockIo->get_dtrust_address_balance($array);
    }

    public function archiveDTrustAddress($addresses)
    {
        $array = ['addresses' => $addresses];

        return $this->blockIo->archive_dtrust_address($array);
    }

    public function unarchiveDTrustAddress($addresses)
    {
        $array = ['addresses' => $addresses];

        return $this->blockIo->unarchive_dtrust_address($array);
    }

    public function getArchivedDTrustAddresses()
    {
        return $this->blockIo->get_my_archived_dtrust_addresses();
    }

    public function getNetworkDTrustFeeEstimate(
        $amounts, $fromAddress, $toAddress)
    {
        return $this->blockIo->get_dtrust_network_fee_estimate([
                        'amounts' => $amounts,
                        'from_addresses' => $fromAddress,
                        'to_addresses' => $toAddress
                    ]);
    }

    public function sweepFromAddress($from_address, $to_address, $private_key)
    {
        return $this->blockIo->sweep_from_address(
                                    array('from_address' => $from_address,
                                    'to_address' => $to_address,
                                    'private_key' => $private_key)
                               );
    }

}

/**
   BlockIos List of Available Methods
   BlockIo.PASSTHROUGH_METHODS = [
      'get_address_received',
      'create_user', '',
      'get_user_received',
      'sign_and_finalize_withdrawal',
    ];

    // withdrawal methods that need local signing
    BlockIo.WITHDRAWAL_METHODS = [
      'withdraw_from_users',
    ];

*/
