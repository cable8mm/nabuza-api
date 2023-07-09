<?php
App::uses('AppHelper', 'View/Helper');

class XmlHelper extends AppHelper {
	public function xml_encode($array, $indent=false, $i=0) {
		// Logic to create specially formatted link goes here...
		if(!$i) {
			$data = '<?xml version="1.0" encoding="UTF-8" ?>'.($indent?"\r\n":'').'<root>'.($indent?"\r\n":'');
		} else {
			$data = '';
		}
		
		foreach($array as $k => $v) {
			if(is_numeric($k)) {
				$k = 'item';
			}
		
			$data .= ($indent?str_repeat("\t", $i):'').'<'.$k.'>';
		
			if(is_array($v)) {
				$data .= ($indent?"\r\n":'').$this->xml_encode($v, $indent, ($i + 1)).($indent?str_repeat("\t", $i):'');
			} else {
				if($v === true)
					$data	.= 'true';
				else if($v === false)
					$data	.= 'false';
				else {
					if($k == 'kakaotalk_message' || $k == 'sms_message' || $k == 'contents') {
						$data .= '<![CDATA['.nl2br(htmlspecialchars($v, 16)).']]>';	// xml encoding 필요함. 16 = ENT_XML1
					} else
						$data .= htmlspecialchars($v, 16);	// xml encoding 필요함. 16 = ENT_XML1
				}
			}
		
			$data .= '</'.$k.'>'.($indent?"\r\n":'');
		}
		
		if(!$i) {
			$data .= '</root>';
		}
		
		return $data;
	}
}