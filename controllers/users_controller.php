<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $pageTitle = 'Usuários';
	var $components = array('Email', 'RequestHandler', 'SwiftMailer');
	var $helpers = array('CakePtbr.Formatacao');

	function beforeFilter() {
		Configure::write('user_id', $this->Session->read('Auth.User.id'));
		Configure::write('group_id', $this->Session->read('Auth.User.group_id'));

		$this->Auth->mapActions(
			array(
				'update' => array('passwd'),
			)
		);
		parent::beforeFilter();
		$this->Auth->allowedActions = array('verify', 'reset', 'login', 'logout', 'forgot', 'ajax_reset');

		$permusers['add'] = false;
		$permusers['edit'] = false;
		$permusers['delete'] = false;
		$permusers['view'] = false;
		$permusers['index'] = false;
		$permusers['passwd'] = false;
		$permusers['access'] = false;
		$permusers['groups']['view'] = false;

		if ($this->Session->check('Auth.User') && !$this->Session->check('Auth.User.permusers')) {
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/add')) {
				$permusers['add'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/edit')) {
				$permusers['edit'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/delete')) {
				$permusers['delete'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/view')) {
				$permusers['view'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/index')) {
				$permusers['index'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/passwd')) {
			$this->log('verify' . print_r($this->data, true), 'debug');
				$permusers['passwd'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/access')) {
				$permusers['access'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Groups/view')) {
				$permusers['groups']['view'] = true;
			}
		
			$this->Session->write('Auth.User.permusers', $permusers);
		}

		$permusers = $this->Session->read('Auth.User.permusers');
		$this->set('permusers', $permusers);
	}
	
	function index() {
		$this->redirect(array('action' => 'login'));
	}

	function login() {
		if ($this->Session->read('Auth.User.id')) {
			$this->log('login ' . print_r($this->data, true), 'debug');
			# Se o grupo for de clientes, redirecionar para a página com quadros.
			if ($this->Session->read('Auth.User.group_id') == 2) {
				$this->redirect(array('controller' => 'sites', 'action' => 'index'));
			} else {
				$this->redirect(array('controller' => 'sites', 'action' => 'meters'));
			}
		} else {
			$this->log('users login ' . print_r($this->data, true), 'debug');
                        Configure::load('maintenance');
			if (!Configure::read('App.maintenance')
				&& Router::getParam('controller') != 'users' && Router::getParam('action') != 'verify') {
				$this->redirect('/');
			}
		}
	}

	function logout() {
		$this->Auth->logout();
		$this->redirect('http://www.vetorlog.com');
	}
}
?>
