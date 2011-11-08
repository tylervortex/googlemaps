<?php
class SiteConverter extends AppModel {

	var $name = 'SiteConverter';
	var $displayField = 'model';
	var $useTable = 'sites_converters';

	var $validate = array(
		'ip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Endereço não pode ser deixado em branco.'
			),
			'ip' => array(
				'rule' => 'ip',
				'message' => 'IP inválido.',
				'last' => true
			),
		),
		'username' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Usuário não pode ser deixado em branco.'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Senha não pode ser deixada em branco.'
			)
		),
		'converter_id' => array(
			'converterId' => array(
				'rule' => 'converterId',
				'message' => 'Conversor inválido.',
				'last' => true
			),
		),
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id'
		)
	);

	var $hasOne = array(
		'Converter' => array(
			'className' => 'Converter',
			'foreignKey' => 'id'
		),
	);


	function converterId($data) {
		if (isset($data['converter_id'])) {
			if (!$data['converter_id']) {
				return false;
			}
			return $this->Converter->find('count', array('conditions' => array('id' => $data['converter_id'])));
		}
		return false;
	}

}
?>
