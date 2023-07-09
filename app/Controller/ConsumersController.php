<?php
App::uses('AppController', 'Controller');
/**
 * Consumers Controller
 *
 * @property Consumer $Consumer
 */
class ConsumersController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Consumer->recursive = 0;
		$this->set('consumers', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Consumer->id = $id;
		if (!$this->Consumer->exists()) {
			throw new NotFoundException(__('Invalid consumer'));
		}
		$this->set('consumer', $this->Consumer->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Consumer->create();
			if ($this->Consumer->save($this->request->data)) {
				$this->flash(__('Consumer saved.'), array('action' => 'index'));
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
		$this->Consumer->id = $id;
		if (!$this->Consumer->exists()) {
			throw new NotFoundException(__('Invalid consumer'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Consumer->save($this->request->data)) {
				$this->flash(__('The consumer has been saved.'), array('action' => 'index'));
			} else {
			}
		} else {
			$this->request->data = $this->Consumer->read(null, $id);
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
		$this->Consumer->id = $id;
		if (!$this->Consumer->exists()) {
			throw new NotFoundException(__('Invalid consumer'));
		}
		if ($this->Consumer->delete()) {
			$this->flash(__('Consumer deleted'), array('action' => 'index'));
		}
		$this->flash(__('Consumer was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
