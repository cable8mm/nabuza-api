<?php
class AndroidMessageShell extends AppShell {
	var $registration_id	= "APA91bE9MxoF8vVg5pxLbz-SWZLl-t7CohcJE2swdyJ2jvJfYbUZTpYYWQjiScwV_FSVjI2bKwh7SucqYb2-loyyeeOeMuEBzzesIQh4pvTcr2Mw2GYetwi8S-HzKup15n1vV9xXXXXX";
	var $realServerKey = "AIzaSyAvXj5G_jQKBASvhIp1TQ41K2XQB_qag9Y";
	var $devServerKey = "AIzaSyCsU2tOfhpJrEU8aQjhWh1pmrTuqJD7RT0";
	public function send() {
		$this->out('Hello world.'.$this->args[0]);
		
		if (empty($this->args[0]))
			die();

		$regId	= $this->args[0];
		
		$data = array(
				'registration_ids' => array($regId),
				'data' => array('msg' => 'Welcome GCM')
		);
		
		$headers = array(
				"Content-Type:application/json",
				"Authorization:key=".$this->devServerKey
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		
		print_r($result);
		curl_close($ch);
	}
}