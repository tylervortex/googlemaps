<?php
class SiteImage extends AppModel {

	var $name = 'SiteImage';
	var $useTable = 'sites_images';
	var $actAs = array(
		'FileUpload.FileUpload' => array(
			'fileModel' => 'SiteImage',
			'massSave' => true
		)
	);

	var $validate = array(
	);

	var $belongsTo = array(
		'Site' => array(
			'className' => 'Site',
			'foreignKey' => 'site_id',
		)
	);

}
?>