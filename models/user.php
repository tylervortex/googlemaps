<?php
class User extends AppModel {

	var $name = 'User';
	var $displayField = 'username';

	var $actsAs = array(
		'Acl' => 'requester',
		'SoftDeletable' => array('field' => 'deleted', 'field_date' => null),
	);

	var $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
	);

	var $hasAndBelongsToMany = array(
		'Site' => array(
			'className' => 'Site',
		),
	);

	function confirmPassword($data) {
		if (isset($data['password']) && $data['password'] == $this->data['User']['confirm_password']) {
			return true;
		} else {
			return false;
		}
	}

	function _isUniqueEmail() {
		$this->enableSoftDeletable(false); 
		$user = $this->find('first', array('conditions' => array('email' => $this->data['User']['email']), 'recursive' => -1));
		if (!$user) {
			return true;
		}
		if (isset($this->data['User']['id']) && $user['User']['id'] == $this->data['User']['id']) {
			return true;
		} else {
			return false;
		}
	}

	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		$data = $this->data;
		if (empty($this->data)) {
			$data = $this->read();
		}
		if (!isset($data['User']['group_id'])) {
			return null;
		} else {
			return array('Group' => array('id' => $data['User']['group_id']));
		}
	}

	function afterSave($created) {
		$parent = $this->parentNode();
		if (!$parent) {
			return;
		}
		$parent = $this->node($parent);
		$node = $this->node();
		$aro = $node[0];
		$aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
		if (isset($this->data['User']['name']) && !empty($this->data['User']['name'])) {
			$aro['Aro']['alias'] = $this->data['User']['name'];
		}
		if ($aro['Aro']['parent_id']) {
			$this->Aro->save($aro);
		}
	}

	function hashPasswords($data) {
		if ($this->alias != 'User') {
			return $data;
		}
		if (is_array($data)) {
			App::import('Compoent', 'Auth');
			$auth = new AuthComponent();
			if (isset($data[$this->alias])) {
				if (isset($data[$this->alias][$auth->fields['password']])) {
					$data[$this->alias][$auth->fields['password']] = $auth->password($data[$this->alias][$auth->fields['password']]);
					if (isset($data[$this->alias]['confirm_password'])) {
						$data[$this->alias]['confirm_password'] = $auth->password($data[$this->alias]['confirm_password']);
					}
				}
			}
		}
		return $data;
	}

	function beforeFind($queryData) {
		if (isset($queryData['recursive']) && $queryData['recursive'] == -1) {
			return $queryData;
		}
		$user_id = Configure::read('user_id');
		$group_id = Configure::read('group_id');
		# Grupo principal (ID:1) sempre pode listar todos os pontos.
		if ($group_id == 1) {
			if ($queryData['fields'] != 'COUNT(*) AS `count`') {
				$this->bindModel(array('hasOne' => array('SitesUser')), false);
				$queryData['group'][] = 'User.id';
			}
			return $queryData;
		}
		$this->bindModel(array('hasOne' => array('SitesUser')), false);
		#$queryData['conditions']['SitesUser.user_id'] = Configure::read('user_id');
		if ($queryData['fields'] != 'COUNT(*) AS `count`') {
			$queryData['group'][] = 'SitesUser.user_id';
		}
		return $queryData;
	}

	function afterFind($results) {
		if (isset($results[0][0]['count']) && isset($results[1][0]['count'])) {
			$results[0][0]['count'] = count($results);
		}
		return $results;
	}

	function access_check($user = null, $site_id = null) {
		# Grupo principal (ID:1) tem acesso a todos os pontos.
		if ($user['group_id'] == 1) {
			return true;
		}
		$result = $this->SitesUser->find('count',
			array(
				'conditions' => array(
					'user_id' => $user['id'],
					'site_id' => $site_id,
				)
			)
		);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

}
?>
