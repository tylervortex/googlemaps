<?php
class SiteHydroSeiom extends AppModel {

	var $name = 'SiteHydroSeiom';
	var $useTable = 'sites_hydros_seiom';
	
	var $validates = array(
		'email' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'E-mail não pode ser deixado em branco.',
				'last' => true,
			),
			'_isUniqueEmail' => array(
				'rule' => '_isUniqueEmail',
				'message' => 'Email já existe.',
				'last' => true,
			),
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Email inválido.',
				'last' => true,
			),
		),
	);

	var $belongsTo = array(
		'SiteHydro' => array(
			'className' => 'SiteHydro',
			'foreignKey' => 'site_hydro_id'
		),
	);

	function _isUniqueEmail() {
		$email = $this->find('first', array('conditions' => array('email' => $this->data['SiteHydroSeiom']['email']), 'recursive' => -1));
		if (!$email) {
			return true;
		}
		if (isset($this->data['SiteHydroSeiom']['id']) && $user['SiteHydroSeiom']['id'] == $this->data['SiteHydroSeiom']['id']) {
			return true;
		} else {
			return false;
		}
	}

}
?>
