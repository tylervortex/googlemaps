<?php
class AppError extends ErrorHandler {

	function maintenance() {
		$this->controller->set('title_for_layout', 'Manutenção');
		$this->controller->layout = 'maintenance';
		$this->_outputMessage('maintenance');
	}

}	
?>
