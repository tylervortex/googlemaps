<?php
class AppModel extends Model {

	/**
	 * http://snook.ca/archives/cakephp/multiple_validation_sets_cakephp/
	 */
	function validates($options = array()) {
		// copy the data over from a custom var, otherwise
		App::import('Core', 'Router');
		$actionSet = 'validate' . Inflector::camelize(Router::getParam('action'));
		if (isset($this->validationSet)) {
			$temp = $this->validate;
			$param = 'validate' . $validationSet;
			$this->validate = $this->{$param};
		} elseif (isset($this->{$actionSet})) {
			$temp = $this->validate;
			$param = $actionSet;
			$this->validate = $this->{$param};
		}

		$errors = $this->invalidFields($options);

		// copy it back
		if (isset($temp)) {
			$this->validate = $temp;
			unset($this->validationSet);
		}

		if (is_array($errors)) {
			return count($errors) === 0;
		}
		return $errors;
	}

	function convertToNumber($data) {
		if (!$data) {
			return true;
		}
		foreach ($data as $k => $v) {
			$v = str_replace('.', '', $v);
			$v = str_replace(',', '.', $v);
			if (!is_numeric($v)) {
				return  false;
			}
		}
		return true;
	}

	/*
	function delete($id = null, $cascade = true) {
		if (!empty($id)) {
			$this->id = $id;
		}
		$id = $this->id;

		if ($this->exists() && $this->beforeDelete($cascade)) {
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			if (!$this->Behaviors->trigger($this, 'beforeDelete', array($cascade), array('break' => true, 'breakOn' => false))) {
				return true;
			}
		}
		return false; 
	}
	*/

}
?>
