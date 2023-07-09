<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * ActivatedPhoneNumbers Controller
 *
 * @property ActivatedPhoneNumber $ActivatedPhoneNumber
 */
class ActivatedPhoneNumbersController extends AppController {

	public function submit() {
		$this->validateQuery('phone_number');
		
		$phoneNumber	= $this->request->query['phone_number'];
//		$sentEmail	= $this->request->query['email'];

		$activatedPhoneNumber['ActivatedPhoneNumber']['phone_number']	= $phoneNumber;
		$activatedPhoneNumber['ActivatedPhoneNumber']['sent']	= DboSource::expression('NOW()');
		$activatedPhoneNumber['ActivatedPhoneNumber']['auth_number']	= rand(10000,99999);
		
		$this->ActivatedPhoneNumber->create();
		if ($this->ActivatedPhoneNumber->save($activatedPhoneNumber)) {
			include_once "/var/www/html/dotname_sms/apitool/class/json.class.php";
			include_once "/var/www/html/dotname_sms/apitool/config.php";
			include_once "/var/www/html/dotname_sms/apitool/class/now_sms_send.php";
			include_once "/var/www/html/dotname_sms/apitool/curl/curl.php";
			include_once "/var/www/html/dotname_sms/apitool/class/result_code.php";

			$data = new now_sms_send;
			$caller = "07043358077";
			$toll = $phoneNumber;
			$subject = "";
			$msg = $activatedPhoneNumber['ActivatedPhoneNumber']['auth_number'];
			$html_type = 0;
			$type_set = '';
			if($type_set == ''){
				$type_set = "-1";
			}

			$rs = $data->set($caller, $toll, $msg, 1, $subject, $type = $type_set );
			if($rs[0]==true){
				$res = $data->send();
				if($res == "발송성공") {
					$result['auth_number']  = $activatedPhoneNumber['ActivatedPhoneNumber']['auth_number'];
					$this->resultRender($result);
				} else {
					$this->resultRender($res);
				}

			}else{
				$this->resultRender(false);
			}
		} else {
			$this->error(9000);
		}
//			$email	= new CakeEmail();
//			$email->config('gmail');
//			$email->from(array('cable8mm@anytale.com' => 'Tester'));
//			$email->to($sentEmail);
//			$email->subject('Activated Phone Number Test');
//			$email->send($activatedPhoneNumber['ActivatedPhoneNumber']['auth_number']);
//			
//			$result['auth_number']	= $activatedPhoneNumber['ActivatedPhoneNumber']['auth_number'];
//			$this->resultRender($result);
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->ActivatedPhoneNumber->recursive = 0;
		$this->set('activatedPhoneNumbers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->ActivatedPhoneNumber->id = $id;
		if (!$this->ActivatedPhoneNumber->exists()) {
			throw new NotFoundException(__('Invalid activated phone number'));
		}
		$this->set('activatedPhoneNumber', $this->ActivatedPhoneNumber->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ActivatedPhoneNumber->create();
			if ($this->ActivatedPhoneNumber->save($this->request->data)) {
				$this->flash(__('Activatedphonenumber saved.'), array('action' => 'index'));
			} else {
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->ActivatedPhoneNumber->id = $id;
		if (!$this->ActivatedPhoneNumber->exists()) {
			throw new NotFoundException(__('Invalid activated phone number'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ActivatedPhoneNumber->save($this->request->data)) {
				$this->flash(__('The activated phone number has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->ActivatedPhoneNumber->read(null, $id);
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ActivatedPhoneNumber->id = $id;
		if (!$this->ActivatedPhoneNumber->exists()) {
			throw new NotFoundException(__('Invalid activated phone number'));
		}
		if ($this->ActivatedPhoneNumber->delete()) {
			$this->flash(__('Activated phone number deleted'), array('action' => 'index'));
		}
		$this->flash(__('Activated phone number was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
