<?php

#require_once 'Date.php';

class SitesController extends AppController {

	var $name = 'Sites';
	var $pageTitle = 'Pontos de medição';
	var $helpers = array('Geocode.Geomap', 'CakePtbr.Formatacao', 'Number');
	var $uses = array('Site', 'DataScde', 'DataHydro', 'MeterRole', 'Manufacturer', 'CakePtbr.EstadoBrasileiro');
	var $components = array('RequestHandler', 'CakePtbr.Formatacao', 'CachedData');

	function beforeFilter() {
		Configure::write('user_id', $this->Session->read('Auth.User.id'));
		Configure::write('group_id', $this->Session->read('Auth.User.group_id'));

		$permsites['add'] = false;
		$permsites['edit'] = false;
		$permsites['delete'] = false;
		$permsites['view'] = false;
		$permsites['index'] = false;
		$permsites['meters'] = false;
		$permsites['map'] = false;
		$permsites['graph'] = false;
		$permsites['table'] = false;
		$permsites['scde'] = false;
		$permsites['diff'] = false;
		$permsites['hydro'] = false;
		$permsites['gallery'] = false;
		$permsites['fetch'] = false;
		$permsites['fetchlog'] = false;
		$permsites['access'] = false;
		$permsites['details'] = false;

		if ($this->Session->check('Auth.User') && !$this->Session->check('Auth.User.permsites')) {
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/add')) {
				$permsites['add'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/edit')) {
				$permsites['edit'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/delete')) {
				$permsites['delete'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/view')) {
				$permsites['view'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/index')) {
				$permsites['index'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/meters')) {
				$permsites['meters'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/map')) {
				$permsites['map'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/graph')) {
				$permsites['graph'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/table')) {
				$permsites['table'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/scde')) {
				$permsites['scde'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Tools/diff')) {
				$permsites['diff'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/hydro')) {
				$permsites['hydro'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/gallery')) {
				$permsites['gallery'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/ajax_fetch')) {
				$permsites['fetch'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/fetchlog')) {
				$permsites['fetchlog'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Users/access')) {
				$permsites['access'] = true;
			}
			if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'Sites/details')) {
				$permsites['details'] = true;
			}
			
			$this->Session->write('Auth.User.permsites', $permsites);
		}

		$permsites = $this->Session->read('Auth.User.permsites');
		$this->set('permsites', $permsites);

		parent::beforeFilter();
	}

	function index() {
		//
	}

	function meters() {
		$this->redirect(array('action' => 'map'));
	}

	function map() {
		$this->Site->unbindModel(array('hasAndBelongsToMany' => array('User')), false);
		$this->Site->SiteMeter->unbindModel(array('belongsTo' => array('Manufacturer')), false);
		
		App::import('Model', 'UserMap');
		$UserMap = new UserMap;
		
		if (!empty($this->data['UserMap'])) {
			$result = $UserMap->find('first', array(
				'fields' => array('id', 'option', 'color'),
				'conditions' => array(
					'user_id' => $this->Session->read('Auth.User.id'),
					'option' => $this->data['UserMap']['option'],
					'color' => $this->data['UserMap']['color'],
				),
			));
			
			if (!$result) {
				$results = $UserMap->find('all', array(
					'conditions' => array(
						'user_id' => $this->Session->read('Auth.User.id')
					)
				));
				if (!$results) {
					$this->data['UserMap']['user_id'] = $this->Session->read('Auth.User.id');
					$this->data['UserMap']['option'] = $this->data['UserMap']['option'];
					$this->data['UserMap']['color'] = $this->data['UserMap']['color'];
					$UserMap->save($this->data);
				} else {
					foreach ($results as $key => $result) {
						if ($result['UserMap']['option'] != $this->data['UserMap']['option'] || $result['UserMap']['color'] != $this->data['UserMap']['color']) {
							$this->data['UserMap']['user_id'] = $this->Session->read('Auth.User.id');
							$this->data['UserMap']['option'] = $this->data['UserMap']['option'];
							$this->data['UserMap']['color'] = $this->data['UserMap']['color'];
							if ($UserMap->delete($result['UserMap']['id'])) {
								$UserMap->save($this->data);
							}
						}
					}
				}
			}
		}
		
		$results = $UserMap->find( 'all', array(
		    'recursive' => 0, 'order' => 'UserMap.user_id',
		    'fields' => array('UserMap.id', 'UserMap.option', 'UserMap.color'),
		    'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))
		));
		$options = Set::combine($results, '{n}.UserMap.id', '{n}.UserMap.option');
		$colors = Set::combine($results, '{n}.UserMap.id', '{n}.UserMap.color');
		$this->set('options', $options);
		$this->set('colors', $colors);
		
		$findOption = Array();
		$siteCondit = array('SET time_zone' => '-3:00', 'Site.deleted' => null, 'order' => 'Site.short_name');
		
		if (!empty($results)) {
			foreach ($results as $l => $item) {
				switch($item['UserMap']['option']) {
					case 'fct':
						$findOption[] = array('Site.generation_type_name' => 0, 'Site.segment_name' => 'consumption');
						break;
					case 'pch':
						$findOption[] = array('Site.generation_type_name' => array('pch', 'cgh', 'pct'));
						break;
					case 'ute':
						$findOption[] = array('Site.generation_type_name' => array('ute'));
						break;
					case 'sol':
						$findOption[] = array('Site.generation_type_name' => array('sol'));
						break;
					case 'se':
						$findOption[] = array('Site.generation_type_name' => array('se'));
						break;
					case 'riv':
						$findOption[] = array('Site.energy' => 0, 'Site.hydro' => 1);
						break;
					default:
						'';
				}
				
				$db =& ConnectionManager::getDataSource('default');
				switch($item['UserMap']['color']) {
					case 'blue':
						$findOption[] = array('SiteMeter.fetch' => 1, 'DATE_SUB(NOW(), INTERVAL 1 HOUR) >' => 'SiteMeter.log_status_time');
						//$findOption[][time() - 'UNIX_TIMESTAMP(SiteMeter.log_status_time)'] = $db->expression("UNIX_TIMESTAMP() - UNIX_TIMESTAMP(SiteMeter.log_status_time) < 3 * 60 * 60");
						break;
					case 'yellow':
						//$findOption[] = array('DATE_SUB(NOW(), INTERVAL 3 HOUR) <' => 'SiteMeter.log_status_time', 'DATE_SUB(NOW(), INTERVAL 6 HOUR) >' => 'SiteMeter.log_status_time');
						$findOption[][time() - 'UNIX_TIMESTAMP(SiteMeter.log_status_time)'] = $db->expression("UNIX_TIMESTAMP() - UNIX_TIMESTAMP(SiteMeter.log_status_time) < 3 * 60 * 60");
						break;
					case 'red':
						$findOption[] = array('DATE_SUB(NOW(), INTERVAL 6 HOUR) >' => 'SiteMeter.log_status_time');
						//$findOption[] = array('DATE_SUB(NOW(), INTERVAL 6 HOUR)');
						//$findOption[] = array("SiteMeter.log_status_time >" => date('Y-m-d', strtotime("6 HOUR")));
						//$findOption[][time() - 'UNIX_TIMESTAMP(SiteMeter.log_status_time)'] = $db->expression("UNIX_TIMESTAMP() - UNIX_TIMESTAMP(SiteMeter.log_status_time) > 6 * 60 * 60");
						break;
					case 'gray':
						$findOption[] = array('SiteMeter.fetch' => 0);
						break;
					default:
						'';
				}
			}
			$siteCondit['joins'] = array(
				array(
					'table' => 'sites_meters',
					'alias' => 'SiteMeter',
					'type' => 'inner',
					'conditions' => array(
						'SiteMeter.site_id = Site.id'
					)
				)
			);
			$siteCondit['conditions'] = array($findOption);
		}
		$results = $this->Site->find('all', $siteCondit);
		pr($siteCondit);
		
		$this->set('sitesLocations', $results);
		$this->set('meterRoles', $this->MeterRole->find('list', array('order' => 'weight')));
	}
}
?>
