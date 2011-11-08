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

	function _tableMeter1h($page, $begin_ts, $end_ts, $find, $interval, $export, $file, &$site) {
		if ($page) {
			$first = strtotime(date('Y-m-d', $begin_ts) . ' +' . $page . ' days');
		} else {
			$first = $begin_ts;
		}
		$last = strtotime(date('Y-m-d', $first) . ' +1 day');

		$first_iso = date('Y-m-d', $first);
		$last_iso = date('Y-m-d', $last);
		$this->data['Site']['current_iso'] = date('Y-m-d', $first);
		$this->data['Site']['current'] = date('d/m/Y', $first);
		$this->data['Site']['next_iso'] = date('Y-m-d', $last);

		$x = array();

		if ($interval == INTERVAL_5MIN) {
			$n = 5;
		} else if ($interval == INTERVAL_15MIN) {
			$n = 15;
		} else if ($interval == INTERVAL_30MIN) {
			$n = 30;
		} else {
			$n = 60;
		}
		$groupby = array("FLOOR((UNIX_TIMESTAMP(time)-5*60)/($n*60))");
		$offset = ($n - 5) * 60;

		if ($find['name'] == 'DataMeter') {
			$fields = array(
				'time_ts',
				"DATE_FORMAT(DATE_ADD(time, INTERVAL $offset SECOND), '%d/%m/%Y %H:%i') AS `date_format`",
				"DATE_FORMAT(DATE_ADD(time, INTERVAL $offset SECOND), '%Y-%m-%d') AS `date_iso`",
				"DATE_FORMAT(DATE_ADD(time, INTERVAL $offset SECOND), '%H:%i:%s') AS `hour_iso`",
				'kwh_ger_sum',
				'kwh_con_sum',
				'kvarh_ger_sum',
				'kvarh_con_sum',
				'va_avg', 'vb_avg', 'vc_avg', 'ia_avg', 'ib_avg', 'ic_avg', 'sh', 'fp_avg'
			);
		} else if ($find['name'] == 'DataScde') {
			$fields = array(
				'time_ts',
				"DATE_FORMAT(time, '%d/%m/%Y %H:%i') AS `date_format`",
				'kwh_ger_sum',
				'kwh_con_sum',
				'kvarh_ger_sum',
				'kvarh_con_sum',
				'energy_type',
				'missing_intervals',
				'status',
				'motive'
			);
		} else if ($find['name'] == 'DataHydro') {
			$this->set('columnDateHydro', 'Data/Horário');
			$fields = array(
				"DATE_FORMAT(time, '%d/%m/%Y %H:%i') AS `date_format`",
				'rainfall_sum',
				'stage_avg',
				'flow_avg',
			);
		}

		$find['model']->query('SET time_zone="-3:00"');
		$results = $find['model']->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$find['field'] => $find['value'],
				'time >=' => $first_iso . ' 00:00:01',
				'time <=' => $last_iso . ' 00:00:00',
				$find['conditions']
			),
			'fields' => $fields,
			'group' => $groupby,
		));

		foreach ($results as $k => $result) {
			if (isset($result[$find['name']]) && !empty($result[$find['name']])) {
				/* SCDE não tem intervalo de menos de 1h */
				if ($interval == INTERVAL_15MIN) {
					$x[] = array(
						$result[0]['date_format'],
						(double) $result['DataMeter']['kwh_ger_sum'],
						(double) $result['DataMeter']['kwh_con_sum'],
						(double) 4 * $result['DataMeter']['kwh_ger_sum'],
						(double) 4 * $result['DataMeter']['kwh_con_sum'],
						(double) $result['DataMeter']['kvarh_ger_sum'],
						(double) $result['DataMeter']['kvarh_con_sum'],
						(double) $result['DataMeter']['va_avg'],
						(double) $result['DataMeter']['vb_avg'],
						(double) $result['DataMeter']['vc_avg'],
						(double) $result['DataMeter']['ia_avg'],
						(double) $result['DataMeter']['ib_avg'],
						(double) $result['DataMeter']['ic_avg'],
						(double) $result['DataMeter']['fp_avg'],
						$result['DataMeter']['sh'],
					);
					if ($export == 'csv') {
						fwrite($file,
							'"' . $result[0]['date_format'] . '";' .
							number_format((double) $result['DataMeter']['kwh_ger_sum'], 3, ',', '') . ';' .
							number_format((double) $result['DataMeter']['kwh_con_sum'], 3, ',', '') . ';' .
							number_format((double) 4 * $result['DataMeter']['kwh_ger_sum'], 3, ',', '') . ';' .
							number_format((double) 4 * $result['DataMeter']['kwh_con_sum'], 3, ',', '') . ';' .
							number_format((double) $result['DataMeter']['kvarh_ger_sum'], 3, ',', '') . ';' .
							number_format((double) $result['DataMeter']['kvarh_con_sum'], 3, ',', '') . ';' .
							number_format((double) $result['DataMeter']['va_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['vb_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['vc_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['ia_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['ib_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['ic_avg'], 4, ',', '') . ';' .
							number_format((double) $result['DataMeter']['fp_avg'], 3, ',', '') . ';' .
							$result['DataMeter']['sh'] . "\n"
						);
					}
				} else {
					if ($find['name'] == 'DataMeter') {
						if ($interval == INTERVAL_1H) {
							$x[] = array(
								$result[0]['date_format'],
								(double) $result['DataMeter']['kwh_ger_sum'],
								(double) $result['DataMeter']['kwh_con_sum'],
								(double) $result['DataMeter']['kvarh_ger_sum'],
								(double) $result['DataMeter']['kvarh_con_sum'],
								(double) $result['DataMeter']['va_avg'],
								(double) $result['DataMeter']['vb_avg'],
								(double) $result['DataMeter']['vc_avg'],
								(double) $result['DataMeter']['ia_avg'],
								(double) $result['DataMeter']['ib_avg'],
								(double) $result['DataMeter']['ic_avg'],
								(double) $result['DataMeter']['fp_avg'],
								$result['DataMeter']['sh'],
							);
						} else {
							$x[] = array(
								$result[0]['date_format'],
								(double) $result['DataMeter']['kwh_ger_sum'],
								(double) $result['DataMeter']['kwh_con_sum'],
								(double) $result['DataMeter']['kvarh_ger_sum'],
								(double) $result['DataMeter']['kvarh_con_sum'],
								(double) $result['DataMeter']['va_avg'],
								(double) $result['DataMeter']['vb_avg'],
								(double) $result['DataMeter']['vc_avg'],
								(double) $result['DataMeter']['ia_avg'],
								(double) $result['DataMeter']['ib_avg'],
								(double) $result['DataMeter']['ic_avg'],
								(double) $result['DataMeter']['fp_avg'],
								$result['DataMeter']['sh'],
							);
						}
						if ($export == 'csv') {
							fwrite($file,
								'"' . $result[0]['date_format'] . '";' .
								number_format((double) $result['DataMeter']['kwh_ger_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataMeter']['kwh_con_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataMeter']['kvarh_ger_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataMeter']['kvarh_con_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataMeter']['va_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['vb_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['vc_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['ia_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['ib_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['ic_avg'], 4, ',', '') . ';' .
								number_format((double) $result['DataMeter']['fp_avg'], 4, ',', '') . ';' .
								$result['DataMeter']['sh'] . "\n"
							);
						} else if ($export == 'xml') {
							$this->_xmlData($file, $result);
						}
					} else if ($find['name'] == 'DataScde') {
						$x[] = array(
							$result[0]['date_format'],
							(double) $result['DataScde']['kwh_ger_sum'],
							(double) $result['DataScde']['kwh_con_sum'],
							(double) $result['DataScde']['kvarh_ger_sum'],
							(double) $result['DataScde']['kvarh_con_sum'],
							$result['DataScde']['energy_type'],
							$result['DataScde']['missing_intervals'],
							$result['DataScde']['status'],
							$result['DataScde']['motive'],							
						);
						if ($export) {
							fwrite($file,
								'"' . $result[0]['date_format'] . '";' .
								number_format((double) $result['DataScde']['kwh_ger_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataScde']['kwh_con_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataScde']['kvarh_ger_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataScde']['kvarh_con_sum'], 3, ',', '') . ';' .
								$result['DataScde']['energy_type'] . ';' . 
								$result['DataScde']['missing_intervals'] . ';' .
								$result['DataScde']['status'] . ';' .
								$result['DataScde']['motive'] . ';' . "\n"
							);
						}
					} else if ($find['name'] == 'DataHydro') {
						$x[] = array(
							$result[0]['date_format'],
							$result['DataHydro']['rainfall_sum'],
							$result['DataHydro']['stage_avg'],
							$result['DataHydro']['flow_avg'],
						);
						if ($export) {
							fwrite($file,
								'"' . $result[0]['date_format'] . '";' .
								number_format((double) $result['DataHydro']['rainfall_sum'], 3, ',', '') . ';' .
								number_format((double) $result['DataHydro']['stage_avg'], 3, ',', '') . ';' .
								number_format((double) $result['DataHydro']['flow_avg'], 3, ',', '') . ';' .
								"\n"
							);
						}
					}
				}
				unset($results[$k][$find['name']]);
			}
		}

		unset($results);

		return $x;
	}

	function _tableMeter1d($page, $begin_ts, $end_ts, $find, $export, $file) {
		if ($page) {
			$first = strtotime(date('Y-m-d', $begin_ts) . ' +' . $page . ' months');
			$first = mktime(0, 0, 0, date('n', $first), 1, date('Y', $first));
		} else {
			$first = $begin_ts;
		}
		$last = mktime(0, 0, 0, date('n', $first), date('t', $first), date('Y', $first));
		$first_iso = date('Y-m-d', $first);
		$last_iso = date('Y-m-d', $last);
		$this->data['Site']['current_iso'] = date('Y-m-d', $first);
		$this->data['Site']['current'] = strftime('%B/%Y', $first);
		$this->data['Site']['next_iso'] = date('Y-m-d', $last);

		$x = array();

		if ($find['name'] == 'DataMeter') {
			$fields = array(
				'time_ts',
				"DATE_FORMAT(time, '%d/%m/%Y') AS `date_format`",
				'kwh_ger_sum',
				'kwh_con_sum',
				'kvarh_ger_sum',
				'kvarh_con_sum',
				'va_avg', 'vb_avg', 'vc_avg', 'ia_avg', 'ib_avg', 'ic_avg', 'fp_avg'
			);

		} else if ($find['name'] == 'DataScde') {
			$fields = array(
				'time_ts',
				"DATE_FORMAT(time, '%d/%m/%Y') AS `date_format`",
				'kwh_ger_sum',
				'kwh_con_sum',
				'kvarh_ger_sum',
				'kvarh_con_sum',
				'missing_intervals_sum',
			);
		} else if ($find['name'] == 'DataHydro') {
			$this->set('columnDateHydro', 'Data');
			$fields = array(
				"DATE_FORMAT(time, '%d/%m/%Y') AS `date_format`",
				'rainfall_sum',
				'stage_avg',
				'stage_min',
				'stage_max',
				'flow_avg',
				'flow_min',
				'flow_max',
			);
		}

		$cur = $first;
		$end = $last;
		$cur_first_iso = date('Y-m-d', $cur);
		while ($cur <= $end && $cur <= $end_ts) {
			$cur_last_iso = date('Y-m-d', strtotime(date('Y-m-d', $cur) . ' +1 day'));

			$find['model']->query('SET time_zone="-3:00"');
			$results = $find['model']->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					$find['field'] => $find['value'],
					'time >=' => $cur_first_iso . ' 00:00:01',
					'time <=' => $cur_last_iso . ' 00:00:00',
					$find['conditions']
				),
				'fields' => $fields,
				'group' => $find['field'],
			));

			if ($results) {
				$results[0][$find['name']]['date'] = $cur_first_iso;

				foreach ($results as $k => $result) {
					if (isset($result[$find['name']]) && !empty($result[$find['name']])) {
						if ($find['name'] == 'DataMeter') {
							$x[] = array(
								$result[0]['date_format'],
								(double) $result['DataMeter']['kwh_ger_sum'],
								(double) $result['DataMeter']['kwh_con_sum'],
								(double) $result['DataMeter']['kvarh_ger_sum'],
								(double) $result['DataMeter']['kvarh_con_sum'],
								(double) $result['DataMeter']['va_avg'],
								(double) $result['DataMeter']['vb_avg'],
								(double) $result['DataMeter']['vc_avg'],
								(double) $result['DataMeter']['ia_avg'],
								(double) $result['DataMeter']['ib_avg'],
								(double) $result['DataMeter']['ic_avg'],
								(double) $result['DataMeter']['fp_avg'],
							);
							if ($export) {
								fwrite($file,
									'"' . $result[0]['date_format'] . '";' .
									number_format((double) $result['DataMeter']['kwh_ger_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataMeter']['kwh_con_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataMeter']['kvarh_ger_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataMeter']['kvarh_con_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataMeter']['va_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['vb_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['vc_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['ia_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['ib_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['ic_avg'], 4, ',', '') . ';' .
									number_format((double) $result['DataMeter']['fp_avg'], 3, ',', '') . "\n"
								);
							}
							unset($results[$k]['DataMeter']);
						} else if ($find['name'] == 'DataScde') {
							$x[] = array(
								$result[0]['date_format'],
								(double) $result['DataScde']['kwh_ger_sum'],
								(double) $result['DataScde']['kwh_con_sum'],
								(double) $result['DataScde']['kvarh_ger_sum'],
								(double) $result['DataScde']['kvarh_con_sum'],
								$result['DataScde']['missing_intervals_sum'],
							);
							if ($export) {
								fwrite($file,
									'"' . $result[0]['date_format'] . '";' .
									number_format((double) $result['DataScde']['kwh_ger_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataScde']['kwh_con_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataScde']['kvarh_ger_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataScde']['kvarh_con_sum'], 3, ',', '') . ';' .
									$result['DataScde']['missing_intervals_sum'] . ';' . "\n"
								);
							}
							unset($results[$k]['DataScde']);
						} else if ($find['name'] == 'DataHydro') {
							$x[] = array(
								$result[0]['date_format'],
								$result['DataHydro']['rainfall_sum'],
								$result['DataHydro']['stage_min'],
								$result['DataHydro']['stage_avg'],
								$result['DataHydro']['stage_max'],
								$result['DataHydro']['flow_min'],
								$result['DataHydro']['flow_avg'],
								$result['DataHydro']['flow_max'],
							);
							if ($export) {
								fwrite($file,
									'"' . $result[0]['date_format'] . '";' .
									number_format((double) $result['DataHydro']['rainfall_sum'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['stage_min'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['stage_avg'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['stage_max'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['flow_min'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['flow_avg'], 3, ',', '') . ';' .
									number_format((double) $result['DataHydro']['flow_max'], 3, ',', '') . ';' .
									"\n"
								);
							}
						}
					}
				}
			}

			unset($results);

			$cur_first_iso = $cur_last_iso;
			$cur = strtotime($cur_last_iso);
		}

		return $x;
	}

	function _tableMeter($site, $begin = null, $end = null, $interval = null, $meterRole = null, $page = null, $group = null, $export = false, $scde = false) {

		$tip['fp']['title'] = 'Fator de Potência';
		$tip['fp']['text'] = 'Valor positivo significa fator de potência capacitivo e valor negativo significa fator de potência indutivo.';
		$tip['fp']['csv'] = 'FP: Fator de Potencia. Valor positivo significa fator de potencia capacitivo e valor negativo significa fator de potencia indutivo.';

		if ($export || empty($this->data)) {
			$this->data['Site']['id'] = $site['Site']['id'];

			if ($begin == NULL) {
				$t = mktime(0, 0, 0, date('n'), 1, date('Y'));
				$this->data['Site']['begin'] = date('d/m/Y', $t);
				$this->data['Site']['begin_iso'] = date('Y-m-d', $t);
			} else {
				$this->data['Site']['begin'] = $this->Formatacao->data($begin);
				$this->data['Site']['begin_iso'] = $begin;
			}

			if ($end == NULL) {
				$this->data['Site']['end'] = date('d/m/Y');
				$this->data['Site']['end_iso'] = date('Y-m-d');
			} else {
				$this->data['Site']['end'] = $this->Formatacao->data($end);
				$this->data['Site']['end_iso'] = $end;
			}

			if ($interval == null) {
				$this->data['Site']['interval'] = '1h';
			} else {
				$this->data['Site']['interval'] = $interval;
			}

			if (!$scde) {
				if ($meterRole == null) {
					$this->data['Site']['meter_role'] = 'main';
				} else {
					$this->data['Site']['meter_role'] = $meterRole;
				}
			}

			if ($page == null) {
				if (isset($this->data['Site']['page'])) {
					$page = $this->data['Site']['page'];
				} else {
					$page = 0;
					$this->data['Site']['page'] = $page;
				}
			} else {
				$this->data['Site']['page'] = $page;
			}
		}

		$this->log('sites table export:' . $export . ' ' . print_r($this->data, true), 'debug');
		
		$begin_ts = strtotime($this->data['Site']['begin_iso']);
		$end_ts = strtotime($this->data['Site']['end_iso']);

		if ($begin_ts > $end_ts) {
			$tmp = $this->data['Site']['begin_iso'];
			$this->data['Site']['begin_iso'] = $this->data['Site']['end_iso'];
			$this->data['Site']['end_iso'] = $tmp;
			
			$tmp = $this->data['Site']['begin'];
			$this->data['Site']['begin'] = $this->data['Site']['end'];
			$this->data['Site']['end'] = $tmp;

			$tmp = $begin_ts;
			$begin_ts = $end_ts;
			$end_ts = $begin_ts;
		}

		$this->data['Site']['begin_ts'] = $begin_ts;
		$this->data['Site']['end_ts'] = $end_ts;

		if (!$export) {
			if (!$scde) {
				$this->set('intervals',
					array(
						'5min' => '5 min',
						'15min' => '15 min',
						'30min' => '30 min',
						'1h' => '1 h',
						'1d' => '1 dia',
						'1m' => '1 mês',
					)
				);
			} else {
				$this->set('intervals',
					array(
						'1h' => '1 h',
						'1d' => '1 dia',
						'1m' => '1 mês',
					)
				);
			}
		}

		define('INTERVAL_5MIN', 1);
		define('INTERVAL_15MIN', 2);
		define('INTERVAL_30MIN', 3);
		define('INTERVAL_1H', 4);
		define('INTERVAL_1D', 5);
		define('INTERVAL_1M', 6);

		switch ($this->data['Site']['interval']) {
			/* navegação dia a dia */
			case '5min': $interval = INTERVAL_5MIN; break;
			case '15min': $interval = INTERVAL_15MIN; break;
			case '30min': $interval = INTERVAL_30MIN; break;
			case '1h': $interval = INTERVAL_1H; break;

			/* navegação mês a mês */
			case '1d': $interval = INTERVAL_1D; break;
			
			/* navegação ano a ano */
			case '1m': $interval = INTERVAL_1M; break;
			
			default: $interval = INTERVAL_1H; break;
		}

		if ($interval <= INTERVAL_1H) { /* navegação dia a dia */
			$pagesOptions = array();
			$current_ts = $begin_ts;
			while (date('Ymd', $current_ts) <= date('Ymd', $end_ts)) {
				$pagesOptions[] = strftime('%d/%m/%Y', $current_ts);
				$current_ts = strtotime(date('Y-m-d', $current_ts) . ' +1 day');
			}
		} else if ($interval == INTERVAL_1D) { /* navegação mês a mês */
			$pagesOptions = array();
			$current_ts = $begin_ts;
			while (date('Ym', $current_ts) <= date('Ym', $end_ts)) {
				$pagesOptions[] = strftime('%B/%Y', $current_ts);
				$current_ts = strtotime(date('Y-m-d', $current_ts) . ' +1 month');
			}
		} else if ($interval == INTERVAL_1M) { /* navegação ano a ano */
			$pagesOptions = array();
			$current_ts = $begin_ts;
			while (date('Y', $current_ts) <= date('Y', $end_ts)) {
				$pagesOptions[] = strftime('%Y', $current_ts);
				$current_ts = strtotime(date('Y-m-d', $current_ts) . ' +1 year');
			}
		} else { /* navegação década a década */
			$pagesOptions = array();
			$current_ts = $begin_ts;
			while (date('Y', $current_ts) - date('Y', $end_ts) <= 10) {
				$pagesOptions[] = strftime('%B/%Y', $current_ts);
				$current_ts = strtotime(date('Y-m-d', $current_ts) . ' +10 years');
			}
		}

		if (!$export) {
			$this->set('pagesOptions', $pagesOptions);
		}

		$this->data['Site']['page'] = $page;
		$this->data['Site']['short_name_file'] = $this->_short_name($site['Site']['short_name']);

		if (!$scde) {
			$meterRole = $this->data['Site']['meter_role'];
			$meterRoles = $this->MeterRole->find('list', array('order' => 'weight'));
		}
		
		if (!$export) {
			if (!$scde) {
				$this->set('meterRoles', $meterRoles);
			}
			$results = $this->Site->find('all', array('recursive' => 0, 'order' => 'Site.short_name', 'fields' => array('Site.id', 'Site.short_name')));
			$list = Set::combine($results, '{n}.Site.id', '{n}.Site.short_name');
			$this->set('sites', $list);
		}

		if (!$scde) {
			$siteMeterIndex = $this->_siteMeterIndex($site['SiteMeter'], $meterRole);
			$this->data['Site']['site_meter_id'] = $site['SiteMeter'][$siteMeterIndex]['id'];
		
			$findIdField = 'site_meter_id';
			$findIdValue = $site['SiteMeter'][$siteMeterIndex]['id'];
			$findModel = $this->Site->SiteMeter->DataMeter;
			$findModelName = 'DataMeter';
			$findConditions = '1=1';
			$find = array(
				'field' => 'site_meter_id',
				'value' => $site['SiteMeter'][$siteMeterIndex]['id'],
				'model' => $this->Site->SiteMeter->DataMeter,
				'name' => 'DataMeter',
				'conditions' => $findConditions
			);
			$resume['meter'] = $site['SiteMeter'][$siteMeterIndex];
		} else {
			$findIdField = 'site_id';
			$findIdValue = $site['Site']['id'];
			$findModel = $this->DataScde;
			$findModelName = 'DataScde';

			// A tabela de dados do SCDE pode ter mais de um registro para cada conjunto (site_id,time),
			// por isso, existe uma coluna "version" que numera as versões do registro em ordem
			// decrescente do horário da importação (coluna "data_scde")
			$findConditions = 'version=0';

			$find = array(
				'field' => 'site_id',
				'value' => $site['Site']['id'],
				'model' => $this->DataScde,
				'name' => 'DataScde',
				'conditions' => $findConditions
			);
		}

		if (!$export) {
			$findModel->query('SET time_zone="-3:00"');
			$minMax = $findModel->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					$findIdField => $findIdValue,
					$findConditions
				),
				'fields' => array(
					'MIN(time) AS min',
					'MAX(time) AS max',
				),
			));

			if (isset($minMax[0][0])) {
				$t = strtotime($minMax[0][0]['min']);
				$this->set('minDate', array('year' => date('Y', $t), 'month' => date('n', $t), 'day' => date('j', $t)));
				$t = strtotime($minMax[0][0]['max']);
				$this->set('maxDate', array('year' => date('Y', $t), 'month' => date('n', $t), 'day' => date('j', $t)));
			}
		}

		if ($export) {
			if (!$scde) {
				if ($export == 'csv') {
					$this->data['filename'] = strtolower($this->_filename($site['Site']['short_name']) . '_' . strtolower($meterRoles[$meterRole]) . '_' . date('dmy', $begin_ts) . '_' . date('dmy', $end_ts) . '.csv');
				} else if ($export == 'xml') {
					$this->_xmlFileName(&$site, $site['SiteMeter'][$siteMeterIndex], $begin_ts, $end_ts, $meterRoles[$meterRole]);
				}
			} else {
				$this->data['filename'] = strtolower($this->_filename($site['Site']['short_name']) . '_scde_' . date('dmy', $begin_ts) . '_' . date('dmy', $end_ts) . '.xml');
			}
			header('Content-disposition:attachment;filename=' . $this->data['filename']);
			header('Content-type:application/vnd.ms-excel');
			$file = fopen('php://output', 'w+');
		} else {
			$file = null;
		}

		$end_year = date('Y', $this->data['Site']['end_ts']);
		$end_month = date('n', $this->data['Site']['end_ts']);
		$end_day = date('j', $this->data['Site']['end_ts']);

		$end_next_ts = mktime(0, 0, 0, $end_month, $end_day + 1, $end_year);

		$findModel->query('SET time_zone="-3:00"');
		$data = $findModel->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$findIdField => $findIdValue,
				'time >=' => date('Y-m-d', $this->data['Site']['begin_ts']) . ' 00:00:01',
				'time <=' => date('Y-m-d', $end_next_ts) . ' 00:00:00',
				$findConditions
			),
			'fields' => array(
				'kwh_ger_sum',
				'kwh_con_sum',
				'kvarh_ger_sum',
				'kvarh_con_sum',
			),
		));
		
		if ($data && isset($data[0][$findModelName]['kwh_ger_sum'])) {
			$resume['data'] = $data[0][$findModelName];

			$findModel->query('SET time_zone="-3:00"');
			$minMax = $findModel->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					$findIdField => $findIdValue,
					'time >=' => date('Y-m-d', $this->data['Site']['begin_ts']) . ' 00:00:01',
					'time <=' => date('Y-m-d', $end_next_ts) . ' 00:00:00',
					$findConditions
				),
				'fields' => array(
					'MIN(time) AS min',
					'MAX(time) AS max',
				),
			));

			if (isset($minMax[0][0])) {
				$resume['first'] = $this->Formatacao->dataHora($minMax[0][0]['min'], false, array('userOffset' => -3));
				$resume['last'] = $this->Formatacao->dataHora($minMax[0][0]['max'], false, array('userOffset' => -3));
			}

			if (!$scde) {
				$group = array("FLOOR((UNIX_TIMESTAMP(time)-5*60)/(15*60))");
				$field_ger = 'kwh_ger_sum';
				$field_con = 'kwh_con_sum';
				$field_dateformat = "DATE_FORMAT(DATE_ADD(time, INTERVAL 600 SECOND), '%d/%m/%Y %H:%i') AS `date_format`";
				// 4 vezes o valor da energia medida agrupada a cada quinze minutos
				$group_multiple = 4;
			} else {
				$group = false;
				$field_ger = 'kwh_ger';
				$field_con = 'kwh_con';
				$field_dateformat = "DATE_FORMAT(time, '%d/%m/%Y %H:%i') AS `date_format`";
				$group_multiple = 1;
			}

			// Demanda Geração
			$findModel->query('SET time_zone="-3:00"');
			$results = $findModel->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					$findIdField => $findIdValue,
					'time >=' => date('Y-m-d', $this->data['Site']['begin_ts']) . ' 00:00:01',
					'time <=' => date('Y-m-d', $end_next_ts) . ' 00:00:00',
					$findConditions
				),
				'fields' => array(
					$field_dateformat,
					$field_ger
				),
				'group' => $group,
				'order' => $field_ger . ' DESC, time',
				'limit' => 1,
			));
			$resume['data']['kw_ger_max']['value'] = $group_multiple * 1000 * $results[0][$findModelName][$field_ger];
			$resume['data']['kw_ger_max']['time'] = $results[0][0]['date_format'];

			// Demanda Consumo
			$findModel->query('SET time_zone="-3:00"');
			$results = $findModel->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					$findIdField => $findIdValue,
					'time >=' => date('Y-m-d', $this->data['Site']['begin_ts']) . ' 00:00:01',
					'time <=' => date('Y-m-d', $end_next_ts) . ' 00:00:00',
					$findConditions
				),
				'fields' => array(
					$field_dateformat,
					$field_con
				),
				'group' => $group,
				'order' => $field_con . ' DESC, time',
				'limit' => 1,
			));
			$resume['data']['kw_con_max']['value'] = $group_multiple * 1000 * $results[0][$findModelName][$field_con];
			$resume['data']['kw_con_max']['time'] = $results[0][0]['date_format'];

			$this->set('resume', $resume);

			if (isset($site['SiteHydro'][0]['id'])) {
				$hydro = $this->Site->SiteHydro->DataHydro->find('all', array(
					'recursive' => -1,
					'conditions' => array(
						'site_hydro_id' => $site['SiteHydro'][0]['id'],
						'time >=' => date('Y-m-d', $this->data['Site']['begin_ts']) . ' 00:00:01',
						'time <=' => date('Y-m-d', $end_next_ts) . ' 00:00:00',
					),
					'fields' => array(
						'rainfall_sum',
						'stage_avg',
						'stage_min',
						'stage_max',
						'flow_avg',
						'flow_min',
						'flow_max',
					),
					'group' => 'site_hydro_id',
				));

				if (isset($hydro[0])) {
					$this->set('hydro', $hydro[0]);
				}
			}
		}
		
		$padrao3 = array(
			'before'=> '',
			'after' => ' Wh',
			'zero' => '0,000',
			'places' => 3,
			'thousands' => '',
			'decimals' => ',',
			'negative' => '()',
			'escape' => true,
			'prefix' => true,
		);
		$padrao3_kvarh = array(
			'before'=> '',
			'after' => ' VArh',
			'zero' => '0,000',
			'places' => 3,
			'thousands' => '',
			'decimals' => ',',
			'negative' => '()',
			'escape' => true,
			'prefix' => true,
		);
		$padrao3_kw = array(
			'before'=> '',
			'after' => ' W',
			'zero' => '0,000',
			'places' => 3,
			'thousands' => '',
			'decimals' => ',',
			'negative' => '()',
			'escape' => true,
			'prefix' => true,
		);

		if ($export == 'csv') {
			if (!$scde) {
				$medidor = $meterRoles[$meterRole];
			} else {
				$medidor = 'SCDE';
			}
			fwrite($file,
				'"Ponto:";"' . $site['Site']['short_name'] . '";"Medidor:";"' . $medidor . '";"Agente:";"' . $site['SiteCcee']['agent'] . '";' . "\n" .
				'"Fabricante/Modelo:";"' . $site['SiteMeter'][$siteMeterIndex]['manufacturer_name'] . '/' . $site['SiteMeter'][$siteMeterIndex]['model_name'] . '";"No. Serie:";"' . $site['SiteMeter'][$siteMeterIndex]['serial_number'] . '";"Cod. Instalacao";"'  . $site['SiteMeter'][$siteMeterIndex]['installation_code'] . '"' . "\n" .
				'"Inicio:";"' . $resume['first'] . '";"Termino:";"' . $resume['last'] . '";' . "\n\n" .
				'"";"Energia Ativa";"Energia Reativa";"Demanda Maxima - Valor";"Demanda Maxima - Data"' . ";\n" .
				'"Geracao";"' .
				$this->Formatacao->format((double) $resume['data']['kwh_ger_sum'] * 1000, $padrao3) . '";"' .
				$this->Formatacao->format((double) $resume['data']['kvarh_ger_sum'] * 1000, $padrao3_kvarh) . '";"' .
				$this->Formatacao->format((double) $resume['data']['kw_ger_max']['value'], $padrao3_kw) . '";"' .
				$resume['data']['kw_ger_max']['time'] . '"' . ";\n" .
				'"Consumo";"' .
				$this->Formatacao->format((double) $resume['data']['kwh_con_sum'] * 1000, $padrao3) . '";"' .
				$this->Formatacao->format((double) $resume['data']['kvarh_con_sum'] * 1000, $padrao3_kvarh) . '";"' .
				$this->Formatacao->format((double) $resume['data']['kw_con_max']['value'], $padrao3_kw) . '";"' .
				$resume['data']['kw_con_max']['time'] . '"' . ";\n\n"
			);
		} else if ($export == 'xml') {
			if (!$scde) {
				$medidor = $meterRoles[$meterRole];
			} else {
				$medidor = 'SCDE';
			}
			$this->_xmlHead($file);
			$meter = $site['SiteMeter'][$siteMeterIndex];
			$this->_xmlMedidor($file, $meter);
		}

		if ($interval == INTERVAL_5MIN) {
			$med = '';
			$medcsv = '';
		} else {
			$med = ' méd';
			$medcsv = ' med';
		}

		if ($interval <= INTERVAL_1H) { /* navegação dia a dia */
			if ($export == 'csv') {
				// TODO	 Exportar dados SCDE
				if ($interval == INTERVAL_15MIN) {
					$columns = array(
						'Data/Horario',
						'Ativa Geracao (kWh)', 
						'Ativa Consumo (kWh)', 
						'Demanda Geracao (kW)', 
						'Demanda Consumo (kW)', 
						'Reativa Capacitiva (kVArh)', 
						'Reativa Indutiva (kVArh)',
						"Va$medcsv (kV)", "Vb$medcsv (kV)", "Vc$medcsv (kV)",
						"Ia$medcsv (A)", "Ib$medcsv (A)", "Ic$medcsv (A)",
						'FP *', 'Ponta', '', 
						'* ' . $tip['fp']['csv']
					);
				} else {
					if (!$scde) {
						$columns = array(
							'Data/Horario',
							'Ativa Geracao (kWh)', 
							'Ativa Consumo (kWh)', 
							'Reativa Capacitiva (kVArh)', 
							'Reativa Indutiva (kVArh)',
							"Va$medcsv (kV)", "Vb$medcsv (kV)", "Vc$medcsv (kV)",
							"Ia$medcsv (A)", "Ib$medcsv (A)", "Ic$medcsv (A)",
							'FP *', 'Ponta', '', 
							'* ' . $tip['fp']['csv']
						);
					} else {
						$columns = array(
							'Data/Horario',
							'Ativa Geracao (kWh)', 
							'Ativa Consumo (kWh)', 
							'Reativa Capacitiva (kVArh)', 
							'Reativa Indutiva (kVArh)',
							'Tipo de Energia', 
							'Faltas',
							'Situacao',
							'Motivo'
						);
					}
				}
				foreach ($columns as $column) {
					fwrite($file, '"' . $column . '";');
				}
				fwrite($file, "\n");
			} else {
				if ($interval == INTERVAL_15MIN) {
					$columns = array(
						array('name' => 'Data/Horário', 'title' => ''),
						array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Demanda Geração<br />(kW)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Demanda Consumo<br />(kW)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => "Va$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vb$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vc$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ia$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ib$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ic$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => 'FP', 'title' => 'Fator de potência', 'format' => 'number3', 'tip' => $tip['fp']),
						array('name' => 'Ponta', 'title' => 'Horário de ponta/fora de ponta'),
					);
					$this->set('fatorPotencia', 1);
					$this->set('column_ponta', 14);
				} else {
					if (!$scde) {
						$columns = array(
							array('name' => 'Data/Horário', 'title' => ''),
							array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
							array('name' => "Va$med<br />(kV)", 'title' => '', 'format' => 'number4'),
							array('name' => "Vb$med<br />(kV)", 'title' => '', 'format' => 'number4'),
							array('name' => "Vc$med<br />(kV)", 'title' => '', 'format' => 'number4'),
							array('name' => "Ia$med<br />(A)", 'title' => '', 'format' => 'number4'),
							array('name' => "Ib$med<br />(A)", 'title' => '', 'format' => 'number4'),
							array('name' => "Ic$med<br />(A)", 'title' => '', 'format' => 'number4'),
							array('name' => 'FP', 'title' => 'Fator de potência', 'format' => 'number3', 'tip' => $tip['fp']),
							array('name' => 'Ponta', 'title' => 'Horário de ponta/fora de ponta'),
						);
						$this->set('fatorPotencia', 1);
						$this->set('column_ponta', 12);
					} else {
						$columns = array(
							array('name' => 'Data/Horário', 'title' => ''),
							array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
							array('name' => 'Tipo de Energia', 'title' => 'Tipo de energia'),
							array('name' => 'Faltas', 'title' => 'Intervalos faltantes'),
							array('name' => 'Situação', 'title' => 'Situação da medida'),
							array('name' => 'Motivo', 'title' => 'Motivo da situação')
						);
					}
				}
				$this->set('columns', $columns);
			}
			$count = count($pagesOptions);
			do {
				$x = $this->_tableMeter1h($page, $begin_ts, $end_ts, $find, $interval, $export, $file, &$site);

				/* Tabela com dados hidrológicos. */
				if (!$export && $site['Site']['hydro']) {
					for ($i = 0; $i < count($site['SiteHydro']); $i++) {
						$findHydro = array(
							'recursive' => -1,
							'field' => 'site_hydro_id',
							'value' => $site['SiteHydro'][$i]['id'],
							'model' => $this->Site->SiteHydro->DataHydro,
							'name' => 'DataHydro',
							'group' => 'site_hydro_id',
							'conditions' => '1=1',
						);
						$results = $this->_tableMeter1h($page, $begin_ts, $end_ts, $findHydro, INTERVAL_1H, $export, $file, &$site);
						$hydroData[$i] = $this->_hydroResultKeys($results);
					}
					
					$keys = array();
					
					foreach ($hydroData as $h) {
						foreach ($h as $k => $v) {
							if (!in_array($k, $keys)) {
								$keys[] = $k;
							}
						}
					}
					
					asort($keys);					
					$r = array();
					
					foreach ($keys as $k) {
						foreach ($hydroData as $i => $h) {
							if (isset($h[$k])) {
								$r[$k][$i] = $h[$k];
							} else {
								$r[$k][$i] = array();
							}
						}						
					}
					
					$this->set('dataHydro', $r);
					$this->set('dataHydroColumns', 3);
				}
			
				$page++;
			} while ($export && $page < $count);

		} else if ($interval == INTERVAL_1D) { /* navegação mês a mês */
			if ($export) {
				if (!$scde) {
					$columns = array(
						'Data',
						'Geracao Ativa (kWh)', 
						'Consumo Ativa (kWh)', 
						'Geracao Reativa (kVArh)', 
						'Consumo Reativa (kVArh)',
						"Va$medcsv (kV)", "Vb$medcsv (kV)", "Vc$medcsv (kV)",
						"Ia$medcsv (A)", "Ib$medcsv (A)", "Ic$medcsv (A)",
						'FP *', '',
						'* ' . $tip['fp']['csv']
					);
					$hasFatorPotencia = true;
				} else {
					$columns = array(
						'Data/Horario',
						'Ativa Geracao (kWh)', 
						'Ativa Consumo (kWh)', 
						'Reativa Capacitiva (kVArh)', 
						'Reativa Indutiva (kVArh)',
						'Faltas',
					);
				}
				$count = count($columns);
				for ($i = 0; $i < $count; $i++) {
					if ($i < $count - 1) {
						fwrite($file, '"' . $columns[$i] . '";');
					} else {
						fwrite($file, '"' . $columns[$i] . '"');
					}
				}
				fwrite($file, "\n");
			} else {
				if (!$scde) {
					$columns = array(
						array('name' => 'Data', 'title' => '', 'format' => 'text'),
						array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => "Va$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vb$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vc$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ia$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ib$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ic$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => 'FP', 'title' => 'Fator de potência', 'format' => 'number3', 'tip' => $tip['fp']),
					);
					$this->set('fatorPotencia', 1);
				} else {
					$columns = array(
						array('name' => 'Data/Horário', 'title' => ''),
						array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Faltas', 'title' => 'Intervalos faltantes'),
					);
				}

				$this->set('columns', $columns);
			}
			do {
				$x = $this->_tableMeter1d($page, $begin_ts, $end_ts, $find, $export, $file);

				/* Tabela com dados hidrológicos. */
				if (!$export && $site['Site']['hydro']) {
					for ($i = 0; $i < count($site['SiteHydro']); $i++) {
						$findHydro = array(
							'recursive' => -1,
							'field' => 'site_hydro_id',
							'value' => $site['SiteHydro'][$i]['id'],
							'model' => $this->Site->SiteHydro->DataHydro,
							'name' => 'DataHydro',
							'group' => 'site_hydro_id',
							'conditions' => '1=1',
						);
						$results = $this->_tableMeter1d($page, $begin_ts, $end_ts, $findHydro, $export, $file);
						$hydroData[$i] = $this->_hydroResultKeys($results);
					}
					
					$keys = array();
					
					foreach ($hydroData as $h) {
						foreach ($h as $k => $v) {
							if (!in_array($k, $keys)) {
								$keys[] = $k;
							}
						}
					}
					
					asort($keys);
					$r = array();
					
					foreach ($keys as $k) {
						foreach ($hydroData as $i => $h) {
							if (isset($h[$k])) {
								$r[$k][$i] = $h[$k];
							} else {
								$r[$k][$i] = array();
							}
						}						
					}
					
					$this->set('dataHydro', $r);
					$this->set('dataHydroColumns', 7);
				}
				
				$page++;
			} while ($export && $x);
		} else if ($interval == INTERVAL_1M) { /* navegação ano a ano */
			if ($export) {
				if (!$scde) {
					$columns = array(
						'Data',
						'Geracao Ativa (kWh)', 
						'Consumo Ativa (kWh)', 
						'Contabilizacao (MWh)', 
						'Geracao Reativa (kVArh)', 
						'Consumo Reativa (kVArh)',
						"Va$medcsv (kV)", "Vb$medcsv (kV)", "Vc$medcsv (kV)",
						"Ia$medcsv (A)", "Ib$medcsv (A)", "Ic$medcsv (A)",
						'FP *', '', 
						'* ' . $tip['fp']['csv']
					);
					$hasFatorPotencia = true;
				} else {
					$columns = array(
						'Data/Horario',
						'Ativa Geracao (kWh)', 
						'Ativa Consumo (kWh)', 
						'Reativa Capacitiva (kVArh)', 
						'Reativa Indutiva (kVArh)',
						'Faltas',
					);
				}
				$count = count($columns);
				for ($i = 0; $i < $count; $i++) {
					if ($i < $count - 1) {
						fwrite($file, '"' . $columns[$i] . '";');
					} else {
						fwrite($file, '"' . $columns[$i] . '"');
					}
				}
				fwrite($file, "\n");
			} else {
				if (!$scde) {
					$columns = array(
						array('name' => 'Mês', 'title' => '', 'format' => 'text'),
						array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Estimativa de Contabilização<br />(MWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => "Va$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vb$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Vc$med<br />(kV)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ia$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ib$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => "Ic$med<br />(A)", 'title' => '', 'format' => 'number4'),
						array('name' => 'FP', 'title' => 'Fator de potência', 'format' => 'number3', 'tip' => $tip['fp']),
					);
					$this->set('fatorPotencia', 1);
				} else {
					$columns = array(
						array('name' => 'Data/Horário', 'title' => ''),
						array('name' => 'Ativa Geração<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Ativa Consumo<br />(kWh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Capacitiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Reativa Indutiva<br />(kVArh)', 'title' => '', 'format' => 'number3'),
						array('name' => 'Faltas', 'title' => 'Intervalos faltantes'),
					);
				}
				$this->set('columns', $columns);
			}
			do {
				$x = $this->_tableMeter1m(&$site, $page, $begin_ts, $end_ts, $find, $export, $file);

				/* Tabela com dados hidrológicos. */
				if (!$export && $site['Site']['hydro']) {
					for ($i = 0; $i < count($site['SiteHydro']); $i++) {
						$findHydro = array(
							'recursive' => -1,
							'field' => 'site_hydro_id',
							'value' => $site['SiteHydro'][$i]['id'],
							'model' => $this->Site->SiteHydro->DataHydro,
							'name' => 'DataHydro',
							'group' => 'site_hydro_id',
							'conditions' => '1=1',
						);
						$results = $this->_tableMeter1m(&$site, $page, $begin_ts, $end_ts, $findHydro, $export, $file);
						$hydroData[$i] = $this->_hydroResultKeys($results);
					}
					
					$keys = array();
					
					foreach ($hydroData as $h) {
						foreach ($h as $k => $v) {
							if (!in_array($k, $keys)) {
								$keys[] = $k;
							}
						}
					}
					
					asort($keys);
					$r = array();
					
					foreach ($keys as $k) {
						foreach ($hydroData as $i => $h) {
							if (isset($h[$k])) {
								$r[$k][$i] = $h[$k];
							} else {
								$r[$k][$i] = array();
							}
						}						
					}
					
					$this->set('dataHydro', $r);
					$this->set('dataHydroColumns', 7);
				}

				$page++;
			} while ($export && $x);
		}

		if ($export) {
			if ($export == 'xml') {
				$this->_xmlFoot($file);
			}
			fclose($file);
			return;
		}
		
		$this->set('data', $x);
		if (!$scde) {
			//$this->set('plots', $this->_plots(&$site, $this->data, $meterRoles));
		}

		if (!isset($site['SiteHydro'][0]['id'])) {
			$x = $this->Site->User->UserHydroAd->find('first', array('conditions' => array('user_id' => $this->Session->read('Auth.User.id'))));
			if (isset($x['UserHydroAd']['show']) && $x['UserHydroAd']['show'] == 0) {
				$this->set('showHydroAd', false);
			} else {
				$this->set('showHydroAd', true);
			}
		}
	}

	function table($id = null, $meterRole = null, $begin = null, $end = null, $interval = null, $page = null, $group = null) {
		$this->log('sites table' . ' id:' . $id . ' meterRole:' . $meterRole . ' begin:' . $begin . ' end:' . $end . ' interval:' . $interval . ' page:' . $page . ' group:' . $group, 'debug');
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Local inválido.', 'default', array('class' => 'message-error'), 'module');
			$this->redirect(array('action' => 'meters'));
		}

		if ($this->Site->User->access_check($this->Session->read('Auth.User'), $id) == false) {
			$this->Session->setFlash('Local inválido.', 'default', array('class' => 'message-error'), 'module');
			$this->redirect(array('action' => 'meters'));
		}

		$this->Site->unbindModel(array('hasAndBelongsToMany' => array('User')), false);
		$site = $this->Site->read(null, $id);
		$this->set('site', $site);

		if ($site['Site']['communication_type_name'] == 'enersul') {
			$this->_tableEnersul($id, $begin, $end, $interval, $meterRole, $site);
		} else if ($site['Site']['communication_type_name'] == 'copel') {
			$this->_tableCopel($id, $begin, $end, $interval, $meterRole, $site);
		} else if (array_search($site['Site']['communication_type_name'], array('router', 'telespazio', 'firewall')) !== null) {
			$this->_tableMeter($site, $begin, $end, $interval, $meterRole, $page, $group);
		}
	}
	
	function map() {
		$this->Site->unbindModel(array('hasAndBelongsToMany' => array('User')), false);
		$this->Site->SiteMeter->unbindModel(array('belongsTo' => array('Manufacturer')), false);
		
		App::import('Model', 'UserMap');
		$UserMap = new UserMap;
		
		if (!empty($this->data['UserMap'])) {
			print_r($this->data);
			exit;
			foreach ($this->data['UserMap'] as $c => $userMap) {
				if (isset($this->data['UserMap'])) {
					$this->data['UserMap']['user_id'] = $this->Session->read('Auth.User.id');
					$this->data['UserMap']['option'] = $this->data['UserMap'][$c]['option'];
					$UserMap->save($this->data);
				} else {
					$id = $UserMap->find('first', array(
						'fields' => array('id'),
						'conditions' => array(
						    'user_id' => $this->Session->read('Auth.User.id'),
						    'option' => $this->data['UserMap'][$c]['option']
						)
					));
					$UserMap->delete($id);
				}
			}
		}
		
		$list = $UserMap->find( 'list', array(
		    'recursive' => 0, 'order' => 'UserMap.user_id',
		    'fields' => array('UserMap.id', 'UserMap.option'),
		    'conditions' => array('user_id' => $this->Session->read('Auth.User.id'))
		));
		$this->set('maps', $list);
		
		$siteFind = Array();
		$siteMeterFind = Array();
		$siteCondit = array('order' => 'Site.short_name');
		$siteMeterCondit = array('order' => 'SiteMeter.fetch');
		
		if (isset($list) && is_array($list) && count($list)>0) {
			if (in_array('pch', $list)) $list[] = 'cgh';
			if (in_array('pct', $list)) $list[] = 'ute';
			foreach ($list as $l => $item) {
				if ($item == 'fct') $item = '0';
				$siteFind['Site.generation_type_name'][$l] = $item;
			}
			$siteCondit['conditions'] = array('Site.deleted' => null, 'NOT' => $siteFind);
		}
		$sites = $this->Site->find('all', $siteCondit);
		if (!$sites) $sites = $this->Site->find('all');
		pr($siteCondit);
		
		if (isset($list) && is_array($list) && count($list)>0) {	
			// Busca para medidores
			$collects = array();
			if (in_array('gray', $list)) $siteMeterFind['SiteMeter.fetch'] = 0; // sem coleta
			if (in_array('blue', $list)) $collects[] = "< 6"; // coletando
			if (in_array('yellow', $list)) $collects[] = "> 3"; // há mais de 3h
			if (in_array('red', $list)) $collects[] = "> 6"; // há mais de 6h
			
			$site_id = Array();
			foreach ($sites as $s => $site) {
				$site_id['SiteMeter.site_id'][$s] = $site['Site']['id'];
			}
			
			$db =& ConnectionManager::getDataSource('default');
			foreach ($collects as $cl => $collect) {
				$siteMeterFind[time() - 'UNIX_TIMESTAMP(SiteMeter.log_status_time)'][$cl] = $db->expression("UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`SiteMeter`.`log_status_time`) $collect * 60 * 60");
				//$siteFind = array("NOW() - UNIX_TIMESTAMP(`SiteMeter.log_status_time`) $collect 60 * 60");
			}
			$siteMeterCondit['conditions'] = array($site_id, 'NOT' => $siteMeterFind);
		}
		$meters = $this->Site->SiteMeter->find('all', $siteMeterCondit);
		if (!$meters) $meters = $this->Site->SiteMeter->find('all');
		pr($siteMeterCondit);
		
		$this->set('sitesLocations', $sites);
		$this->set('meterRoles', $this->MeterRole->find('list', array('order' => 'weight')));
	}
}
?>
