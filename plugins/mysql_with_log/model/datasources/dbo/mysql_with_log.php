<?php

require (LIBS . 'model' . DS . 'datasources' . DS . 'dbo' . DS . 'dbo_mysql.php');

class DboMysqlWithLog extends DboMysql {
	function _execute($sql) {
		$this->log($sql, 'sql');
		return parent::_execute($sql);
	}
}

?>
