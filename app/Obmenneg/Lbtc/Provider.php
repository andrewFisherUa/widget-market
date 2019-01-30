<?php

namespace App\Obmenneg\Lbtc;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
	private static $instance=null;
	public static function getInstance(){
		if(self::$instance==null){
			self::$instance=new self;
		}
		return self::$instance;
	}
	
	public function selectProvider($provider){
		if ($provider=='ALIPAY')
		return 'alipay';
		if ($provider=='PINGIT')
		return 'pingit';
		if ($provider=='TIGOPESA_TANZANIA')
		return 'tigo-pesa-tanzania';
		if ($provider=='GIFT_CARD_CODE_AMAZON')
		return 'amazon-gift-card-code';
		if ($provider=='GIFT_CARD_CODE_WALMART')
		return 'walmart-gift-card-code';
		if ($provider=='OTHER_REMITTANCE')
		return 'other-remittance';
		if ($provider=='SQUARE_CASH')
		return 'square-cash';
		if ($provider=='OTHER')
		return 'other-online-payment';
		if ($provider=='BPAY')
		return 'bpay-bill-payment';
		if ($provider=='YANDEXMONEY')
		return 'yandex-money';
		if ($provider=='MONEYGRAM')
		return 'moneygram';
		if ($provider=='PAYM')
		return 'paym';
		if ($provider=='HAL_CASH')
		return 'hal-cash';
		if ($provider=='ALTCOIN_ETH')
		return 'ethereum-altcoin';
		if ($provider=='WECHAT')
		return 'wechat';
		if ($provider=='OTHER_ONLINE_WALLET_GLOBAL')
		return 'other-online-wallet-global';
		if ($provider=='OTHER_PRE_PAID_DEBIT')
		return 'other-pre-paid-debit-card';
		if ($provider=='OTHER_ONLINE_WALLET')
		return 'other-online-wallet';
		if ($provider=='SEPA')
		return 'sepa-eu-bank-transfer';
		if ($provider=='EASYPAISA')
		return 'easypaisa';
		if ($provider=='PostePay')
		return 'postepay';
		if ($provider=='CASH_AT_ATM')
		return 'cash-at-atm';
		if ($provider=='NETELLER')
		return 'neteller';
		if ($provider=='PAYEER')
		return 'payeer';
		if ($provider=='MPESA_KENYA')
		return 'm-pesa-kenya-safaricom';
		if ($provider=='XOOM')
		return 'xoom';
		if ($provider=='GIFT_CARD_CODE_APPLE_STORE')
		return 'apple-store-gift-card-code';
		if ($provider=='GOOGLEWALLET')
		return 'google-wallet';
		if ($provider=='GIFT_CARD_CODE')
		return 'gift-card-code';
		if ($provider=='NATIONAL_BANK')
		return 'national-bank-transfer';
		if ($provider=='CASH_BY_MAIL')
		return 'cash-by-mail';
		if ($provider=='PAYPALMYCASH')
		return 'paypal-my-cash';
		if ($provider=='CASHIERS_CHECK')
		return 'cashiers-check';
		if ($provider=='VENMO')
		return 'venmo';
		if ($provider=='VIPPS')
		return 'vipps';
		if ($provider=='INTERAC')
		return 'interac-e-transfer';
		if ($provider=='WU')
		return 'western-union';
		if ($provider=='BANK_TRANSFER_IMPS')
		return 'imps-bank-transfer-india';
		if ($provider=='RIA')
		return 'ria-money-transfer';
		if ($provider=='OKPAY')
		return 'okpay';
		if ($provider=='WEBMONEY')
		return 'webmoney';
		if ($provider=='PAYPAL')
		return 'paypal';
		if ($provider=='TRANSFERWISE')
		return 'transferwise';
		if ($provider=='SPECIFIC_BANK')
		return 'transfers-with-specific-bank';
		if ($provider=='PERFECT_MONEY')
		return 'perfect-money';
		if ($provider=='PAYONEER')
		return 'payoneer';
		if ($provider=='INTERNATIONAL_WIRE_SWIFT')
		return 'international-wire-swift';
		if ($provider=='GIFT_CARD_CODE_STEAM')
		return 'steam-gift-card-code';
		if ($provider=='CHASE_QUICKPAY')
		return 'chase-quickpay';
		if ($provider=='PYC')
		return 'pyc';
		if ($provider=='GIFT_CARD_CODE_STARBUCKS')
		return 'starbucks-gift-card-code';
		if ($provider=='MPESA_TANZANIA')
		return 'm-pesa-tanzania-vodacom';
		if ($provider=='SWISH')
		return 'swish';
		if ($provider=='SERVE2SERVE')
		return 'serve2serve';
		if ($provider=='QIWI')
		return 'qiwi';
		if ($provider=='GIFT_CARD_CODE_GLOBAL')
		return 'gift-card-code-global';
		if ($provider=='PAYZA')
		return 'payza';
		if ($provider=='VANILLA')
		return 'vanilla';
		if ($provider=='PAYSAFECARD')
		return 'paysafecard';
		if ($provider=='GIFT_CARD_CODE_EBAY')
		return 'ebay-gift-card-code';
		if ($provider=='WALMART2WALMART')
		return 'walmart-2-walmart';
		if ($provider=='PAXUM')
		return 'paxum';
		if ($provider=='PAYTM')
		return 'paytm';
		if ($provider=='CREDITCARD')
		return 'credit-card';
		if ($provider=='CASH_DEPOSIT')
		return 'cash-deposit';
		if ($provider=='ADVCASH')
		return 'advcash';
		if ($provider=='MONEYBOOKERS')
		return 'moneybookers-skrill';
		return 'error';
	}
}
