<?php
App::uses('AppController', 'Controller');
/**
 * Coupons Controller
 *
 * @property Coupon $Coupon
 */
class CouponsController extends AppController {

	var $uses = array('Coupon', 'Player', 'CouponIssue', 'CouponSponsor');
	
	public function using() {
		$this->validateQuery('serial');
		$serial      = $this->request->query['serial'];
		
//		$coupon	= $this->Coupon->find('first', array('conditions'=>array('serial' => $serial, 'is_active'=>true)));
		$coupon	= $this->Coupon->find('first', array('conditions'=>array('serial' => $serial, 'is_active'=>true, 'is_used'=>false)));

		if (empty($coupon)) {
			$this->error(4000);
		}

		$couponSponsor	= $this->CouponSponsor->find('first', array('conditions'=>array('CouponSponsor.id'=>$coupon['CouponIssue']['coupon_sponsor_id'])));
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));

		$coupon['Coupon']['is_used']	= true;
		$coupon['Coupon']['used_player_id']	= $player['Player']['id'];
		$coupon['Coupon']['used']	= date('Y-m-d H:i:s');
		
		if ($this->Coupon->save($coupon)) {
			$player['Player']['own_jade_count'] += $coupon['CouponIssue']['jade_count'];
			$this->Player->updateAll(array('Player.own_jade_count' => $player['Player']['own_jade_count']), array('Player.id' => $player['Player']['id']));
			$this->CouponIssue->updateAll(array('CouponIssue.used_count' => 'CouponIssue.used_count + 1'), array('CouponIssue.id' => $coupon['Coupon']['coupon_issue_id']));
			$result	= array();
			
			$result['own_jade_count']	= $player['Player']['own_jade_count'];
			$result['coupon_jade_count']	= $coupon['CouponIssue']['jade_count'];
			$result['sponsor_name']	= $couponSponsor['CouponSponsor']['name'];
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}
	
	public function using2() {
		$this->validateQuery('serial');
		$serial      = $this->request->query['serial'];

//		$coupon	= $this->Coupon->find('first', array('conditions'=>array('Coupon.serial' => $serial, 'Coupon.is_active'=>true)));
		$coupon	= $this->Coupon->find('first', array('conditions'=>array('serial' => $serial, 'is_active'=>true, 'is_used'=>false)));

		if (empty($coupon)) {
			$this->error(4000);
		}
	
		$couponSponsor	= $this->CouponSponsor->find('first', array('conditions'=>array('CouponSponsor.id'=>$coupon['CouponIssue']['coupon_sponsor_id'])));
		$player = $this->Player->find('first', array('conditions'=>array('Player.appid' => $this->accessToken['AccessToken']['appid'])));
	
		$coupon['Coupon']['is_used']	= true;
		$coupon['Coupon']['used_player_id']	= $player['Player']['id'];
		$coupon['Coupon']['used']	= date('Y-m-d H:i:s');
	
		if ($this->Coupon->save($coupon)) {
			$player['Player']['own_jade_count'] += $coupon['CouponIssue']['jade_count'];
			$this->Player->updateAll(array('Player.own_jade_count' => $player['Player']['own_jade_count']), array('Player.id' => $player['Player']['id']));
			$this->CouponIssue->updateAll(array('CouponIssue.used_count' => 'CouponIssue.used_count + 1'), array('CouponIssue.id' => $coupon['Coupon']['coupon_issue_id']));
			$result	= array();
				
			$result['Player']['own_jade_count']	= intval($player['Player']['own_jade_count']);
			$result['CouponIssue']['jade_count']	= intval($coupon['CouponIssue']['jade_count']);
			$result['CouponSponsor']['name']	= $couponSponsor['CouponSponsor']['name'];
			$this->resultRender($result);
		} else {
			$this->error(9000);
		}
	}
}
