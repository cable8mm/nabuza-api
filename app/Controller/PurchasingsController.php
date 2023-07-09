<?php
App::uses('AppController', 'Controller');
/**
 * Purchasings Controller
 *
 * @property Purchasing $Purchasing
 */
class PurchasingsController extends AppController {

	public function item() {
		$this->validateQuery('product_id');
		$productId	= $this->request->query['product_id'];
		
		$purchasing['Purchasing']['buyed']	= date('Y-m-d H:i:s');
		$purchasing['Purchasing']['product_id']	= $productId;
		
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$purchasing['Purchasing']['player_id'] = $player['Player']['id'];

		$jade_count = 0;
		switch($productId)
		{
			case 1: $jade_count = 10; break;
			case 2: $jade_count = 55; break;
			case 3: $jade_count = 120; break;
			case 4: $jade_count = 360; break;
			case 5: $jade_count = 675; break;
			
		}
		$player['Player']['own_jade_count'] += $jade_count;
		
		if ($this->Purchasing->save($purchasing) && $this->Player->save($player)) {
			$this->resultRender(true);
		} else {
			$this->error(9000);
		}
	}

	public function item2() {
		$this->validateQuery('product_id');
		$productId	= $this->request->query['product_id'];
	
		$purchasing['Purchasing']['buyed']	= date('Y-m-d H:i:s');
		$purchasing['Purchasing']['product_id']	= $productId;
	
		// player_id 구하자
		Controller::loadModel('Player');
		$player	= $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
		$purchasing['Purchasing']['player_id'] = $player['Player']['id'];
	
		$jadeCount = $productId;
// 		switch($productId)
// 		{
// 			case 1: $jade_count = 10; break;
// 			case 2: $jade_count = 55; break;
// 			case 3: $jade_count = 120; break;
// 			case 4: $jade_count = 360; break;
// 			case 5: $jade_count = 675; break;
				
// 		}
		$player['Player']['own_jade_count'] += $jadeCount;
	
		if ($this->Purchasing->save($purchasing) && $this->Player->save($player)) {
			$this->resultRender(true);
		} else {
			$this->error(9000);
		}
	}
}
