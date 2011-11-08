<?php
class Group extends AppModel {

	var $name = 'Group';
	var $displayField = 'name';

	var $actsAs = array(
		'Acl' => 'requester',
		'SoftDeletable' => array('field' => 'deleted', 'field_date' => null),
	);

	var $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'group_id',
			'dependent' => false,
		),
	);

	function parentNode() {
		return null;
	}

	function afterSave($created) {
		$node = $this->node();
		$aro = $node[0];
		if (isset($this->data['Group']['name']) && !empty($this->data['Group']['name'])) {
			$aro['Aro']['alias'] = $this->data['Group']['name'];
		}
		$this->Aro->save($aro);
	}

}
?>
