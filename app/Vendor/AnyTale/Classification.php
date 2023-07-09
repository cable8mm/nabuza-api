<?php
class Classification {
	var $goldLevelSteps	= array(25000, 400000, 999999999);
	var $goldLevels	= array(
			0 => 100
			,500
			,1000
			,2000
			,3000
			,4000
			,6000
			,8000
			,10000
			,15000
			,20000
			,25000
			,35000
			,45000
			,55000
			,80000
			,105000
			,130000
			,160000
			,190000
			,220000
			,300000
			,350000
			,400000
			,500000
			,600000
			,700000
			,900000
			,1100000
			,1300000
			,2000000
			,2500000
			,3000000
			,4000000
			,5000000
			,999999999
	);
	
	public function getGoldLevel($gold) {
		foreach ($this->goldLevels as $k=>$goldLevel) {
			if ($goldLevel >= $gold)
				return $k;
		}
	}
}