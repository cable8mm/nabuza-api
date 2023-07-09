<?php
App::uses('AppController', 'Controller');
/**
 * SpentGifts Controller
 *
 * @property SpentGift $SpentGift
 */
class CoinsetsController extends AppController {

	public function get() {
		Controller::loadModel('GameInfo');
		$coinsetTerm        = $this->GameInfo->find('first', array('conditions' => array('GameInfo.id'=>2)));
		$coinsetTotalCount	= $this->Coinset->find('count', array('conditions'=>array('Coinset.is_active'=>true)));
		
		$currentTimestamp	= time();

		$selectedCoinsetId	= (ceil($currentTimestamp / 60 / 5)) % $coinsetTotalCount;
		$coinset	= $this->Coinset->find('first', array('conditions'=>array('Coinset.is_active'=>true), 'limit'=>1, 'offset'=>$selectedCoinsetId, 'order'=>'Coinset.id ASC'));

		Controller::loadModel('CoinsetOrder');
		$coinsetOrders	= $this->CoinsetOrder->find('list', array('order'=>'CoinsetOrder.order ASC','fields'=>array('CoinsetOrder.order', 'CoinsetOrder.coin_color'), 'conditions'=>array('CoinsetOrder.coinset_id'=>$coinset['Coinset']['id'])));

		$coinsetOrdersSerial	= '';
		foreach ($coinsetOrders as $coinsetOrder) {
			$coinsetOrdersSerial.= $coinsetOrder;
		}
		
		$this->resultRender($coinsetOrdersSerial);
	}
	
	public function get_all() {
		$this->validateQuery('last_sync_datetime');
		$currentDatetime      = $this->request->query['last_sync_datetime'];
		if (!preg_match('/^[0-9]{14}$/', $currentDatetime)) {	// 20131212832372
			$this->error(100);
		}
		
		$currentDatetimeFormat	= preg_replace('/(....)(..)(..)(..)(..)(..)/', "\\1-\\2-\\3 \\4:\\5:\\6", $currentDatetime);
		
		$coinsetCount	= $this->Coinset->find('count', array('conditions'=>array('Coinset.modified > ' => $currentDatetimeFormat)));
		
		if ($coinsetCount > 0) {
			$this->Coinset->bindModel(array('hasMany'=>array('CoinsetOrder'=>array('fields'=>array('CoinsetOrder.order', 'CoinsetOrder.coin_color')))));
			$coinsets	= $this->Coinset->find('all', array('fields'=>array('Coinset.id'), 'conditions'=>array('Coinset.is_active'=>true), 'order'=>'Coinset.id ASC'));
		
			foreach ($coinsets as $kc=>$coinset) {
				$coinsetOrders	= '';
				foreach ($coinset['CoinsetOrder'] as $ko=>$coinsetOrder) {
					$coinsetOrders	.= $coinsetOrder['coin_color'];
				}
		
				$coinsets[$kc]['Coinset']['coin_orders']	= $coinsetOrders;
				unset($coinsets[$kc]['CoinsetOrder']);
			}
		
			$result['Coinsets']	= $coinsets;
		}
		
		$result['current_datetime']	= date("YmdHis");
		
		$this->resultRender($result);
	}
}
