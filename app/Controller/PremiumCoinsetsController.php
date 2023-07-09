<?php
App::uses('AppController', 'Controller');
/**
 * SpentGifts Controller
 *
 * @property SpentGift $SpentGift
 */
class PremiumCoinsetsController extends AppController {

// 	public function get() {
// 		Controller::loadModel('GameInfo');
// 		$coinsetTerm        = $this->GameInfo->find('first', array('conditions' => array('GameInfo.id'=>2)));
// 		$coinsetTotalCount	= $this->Coinset->find('count', array('conditions'=>array('Coinset.is_active'=>true)));
		
// 		$currentTimestamp	= time();

// 		$selectedCoinsetId	= (ceil($currentTimestamp / 60 / 5)) % $coinsetTotalCount;
// 		$coinset	= $this->Coinset->find('first', array('conditions'=>array('Coinset.is_active'=>true), 'limit'=>1, 'offset'=>$selectedCoinsetId, 'order'=>'Coinset.id ASC'));

// 		Controller::loadModel('CoinsetOrder');
// 		$coinsetOrders	= $this->CoinsetOrder->find('list', array('order'=>'CoinsetOrder.order ASC','fields'=>array('CoinsetOrder.order', 'CoinsetOrder.coin_color'), 'conditions'=>array('CoinsetOrder.coinset_id'=>$coinset['Coinset']['id'])));

// 		$coinsetOrdersSerial	= '';
// 		foreach ($coinsetOrders as $coinsetOrder) {
// 			$coinsetOrdersSerial.= $coinsetOrder;
// 		}
		
// 		$this->resultRender($coinsetOrdersSerial);
// 	}
	
	public function get_all() {
		$this->validateQuery('last_sync_datetime');
		$currentDatetime      = $this->request->query['last_sync_datetime'];
		if (!preg_match('/^[0-9]{14}$/', $currentDatetime)) {	// 20131212832372
			$this->error(100);
		}
		
		$currentDatetimeFormat	= preg_replace('/(....)(..)(..)(..)(..)(..)/', "\\1-\\2-\\3 \\4:\\5:\\6", $currentDatetime);

		$premiumCoinsetCount	= $this->PremiumCoinset->find('count', array('conditions'=>array('PremiumCoinset.modified > ' => $currentDatetimeFormat)));
		
		if ($premiumCoinsetCount > 0) {
			$this->PremiumCoinset->bindModel(array('hasMany'=>array('PremiumCoinsetOrder'=>array('fields'=>array('PremiumCoinsetOrder.order', 'PremiumCoinsetOrder.coin_color')))));
			$coinsets	= $this->PremiumCoinset->find('all', array('fields'=>array('PremiumCoinset.id'), 'conditions'=>array('PremiumCoinset.is_active'=>true), 'order'=>'PremiumCoinset.id ASC'));

			foreach ($coinsets as $kc=>$coinset) {
				$coinsetOrders	= '';
				foreach ($coinset['PremiumCoinsetOrder'] as $ko=>$coinsetOrder) {
					$coinsetOrders	.= $coinsetOrder['coin_color'];
				}
				
				$coinsets[$kc]['PremiumCoinset']['premium_coin_orders']	= $coinsetOrders;
				unset($coinsets[$kc]['PremiumCoinsetOrder']);
			}
		
			$result['PremiumCoinsets']	= $coinsets;
		}
		
		$result['current_datetime']	= date("YmdHis");
		
		$this->resultRender($result);
	}
}
