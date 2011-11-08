<?php
class AppController extends Controller {

	//var $components = array('CachedAcl', 'Auth', 'Session');
	var $components = array('CachedAcl', 'Auth', 'Session', 'DebugKit.Toolbar');

	var $paginate = array('limit' => 20);

	function beforeFilter() {
		$this->Auth->authorize = 'actions';
		$this->Auth->actionPath = 'controllers/';
		$this->Auth->allowedActions = array('display');
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->autoRedirect = false;
		$this->Auth->userScope = array('Group.deleted' => null, 'NOT' => array('User.verified' => null));
		$this->Auth->authenticate = ClassRegistry::init('User');

		/**
		 * These statements tell AuthComponent to allow public access to all
		 * actions. This is only temporary and will be removed once we get a
		 * few users and groups into our database. Don't add any users or groups
		 * just yet though.
		 */
		//$this->Auth->allow('*');

		$perm['sites'] = false;
		$perm['meters'] = false;
		$perm['tools'] = false;
		$perm['registers'] = false;
		$perm['reports'] = false;

		Configure::load('maintenance');

		if (!Configure::read('App.maintenance_locked_database') && $this->Session->check('Auth.User')) {
			App::import('Model', 'User');
			$User =& ClassRegistry::init('User');
			$User->updateAll(
				array('User.used' => 'NOW()'),
				array('User.id' => $this->Session->read('Auth.User.id')
			));
		}
		
		if ($this->Session->check('Auth.User') && !$this->Session->check('Auth.User.perm')) {
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/index')) {
				$perm['sites'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/meters')) {
				$perm['meters'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Tools')) {
				$perm['tools'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Converters')
				|| $this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'DistribuitionCompanies')
				|| $this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Groups')
				|| $this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Hollidays')
				|| $this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users')) {
				$perm['registers'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Reports')) {
				$perm['reports'] = true;
			}
	
			$this->Session->write('Auth.User.perm', $perm);
		}
		
		$perm = $this->Session->read('Auth.User.perm');
		$this->set('perm', $perm);

		if (Configure::read('App.maintenance')
			&& Router::getParam('controller') != 'users' && Router::getParam('action') != 'login'
			&& array_search($this->Auth->user('username'), Configure::read('App.maintenance_allowed_users')) === FALSE
			&& $this->Auth->user('id') != 1) {
			$this->Auth->logout();
			$this->cakeError('maintenance'); 
		}

		$this->set('title_for_layout', $this->pageTitle);

		if ($this->Session->check('Auth.User')) {
			$log = Router::getParam('controller') . ' ' . Router::getParam('action');
			$this->log($this->Session->read('Auth.User.username') . ' ' . $log, 'debug');
		}

		parent::beforeFilter();
	}

}
?>
