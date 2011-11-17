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

		function ajax_map() {
		if (1 || $this->RequestHandler->isAjax()) {
			$this->log(print_r($this->data, true), 'debug');
			Configure::write('debug', 0);
			$this->layout = 'ajax';
		
			$meterRoles = $this->MeterRole->find('list', array('order' => 'weight'));
			$sitesLocations = $this->Site->find('all', array(
				'order' => 'Site.short_name',
			));

			$count = count($sitesLocations);
			for ($i = 0; $i < $count; $i++) {
				$site = $sitesLocations[$i];
				$index = -1;
				if ($site['Site']['segment_name'] == 'generation') {
					if ($site['Site']['generation_type_name'] == 'ute') {
						$index = 2;
					} else if ($site['Site']['generation_type_name'] == 'se') {
						$index = 3;
					} else if ($site['Site']['generation_type_name'] == 'sol') {
						$index = 4;
					} else {
						$index = 0;
					}
				} else if ($site['Site']['segment_name'] == 'consumption') {
					$index = 1;
				} else {
					/* Não é nem do segmento de geração, nem do segmento de consumo. 
					 * Verificar se é um ponto com hidrologia apenas. */
					if ($site['Site']['hydro'] && !$site['Site']['energy']) {
						$index = 5;
					}
				}
				/* XXX A seqüência de types tem que estar na mesma
				 * ordem da view.
				 * A função recebe apenas um inteiro como parâmetro e
				 * este inteiro é o índice desse array. Então, para
				 * selecionar pontos de hidrelétricas, por exemplo, a
				 * view passa o argumento "0" pela chamada Ajax; para
				 * selecionar pontos de consumidores, passa "1"; e
				 * assim por diante. */
				if ($index != $this->data['type']) {
					continue;
				}
				$x[$i]['i'] = $index;
				$x[$i]['lat'] = $site['Site']['latitude'];
				$x[$i]['lng'] = $site['Site']['longitude'];
				$padrao1 = array(
					'before'=> '',
					'after' => '',
					'zero' => '0,0',
					'places' => 1,
					'thousands' => '.',
					'decimals' => ',',
					'negative' => '()',
					'escape' => true
				);
				$padrao3 = array(
					'before'=> '',
					'after' => '',
					'zero' => '0,000',
					'places' => 3,
					'thousands' => '.',
					'decimals' => ',',
					'negative' => '()',
					'escape' => true
				);
				$x[$i]['t'] = ($site['Site']['segment_name'] == 'generation' ? $site['GenerationType']['title'] : '') . ' ' . $site['Site']['short_name'];
				$x[$i]['s'] = 0; /* sem coleta */
				foreach ($site['SiteMeter'] as $n => $meter) {
					if ($meter['fetch'] == 1) {
						$diff = time() - $meter['log_status_time_ts'];
						$x[$i]['s'] = 1;
						if ($meter['log_status_id'] != 1) {
							if ($diff > 6 * 60 * 60) {
								$x[$i]['s'] = 3;
							} else if ($diff > 3 * 60 * 60) {
								$x[$i]['s'] = 2;
							}
						}
					}
					$x[$i]['m'][$n]['d'] = $this->Formatacao->dataHora($site['SiteMeter'][$n]['log_status_time'], false);
					$x[$i]['m'][$n]['r'] = $meterRoles[$site['SiteMeter'][$n]['meter_role_name']];
				}
				/* parâmentro: Tensão */
				$x[$i]['pt'] = $this->Formatacao->format($site['Site']['voltage_level'], $padrao1) . ' kV';
				/* parâmetro: Capacidade de geração */
				$x[$i]['pg'] = $this->Formatacao->format($site['Site']['generation_capacity'], $padrao3) . ' MW';
				/* parâmetro: Capacidade de consumo */
				$x[$i]['pc'] = $this->Formatacao->format($site['Site']['consumption_capacity'], $padrao3) . ' MW';
			}
			
			$this->set('json', json_encode($x));
		}
	}
	
	function map() {
		$this->Site->unbindModel(array('hasAndBelongsToMany' => array('User')), false);
		$results = $this->Site->find('all', array(
			'order' => 'Site.short_name',
		));
		$this->set('sitesLocations', $results);
		$this->set('meterRoles', $this->MeterRole->find('list', array('order' => 'weight')));
	}
}
?>
