<?php 
class CachedDataComponent extends AclComponent {

	var $accountingName = 'accounting';
    
	function accounting() {
		$path = CACHE . DS . 'data';
		Cache::config($this->accountingName, array(
			'engine' => 'File',
			'duration'=> '+5 minute', 
			'path' => $path,
			'prefix' => $this->accountingName . '_'
		));
		return $this->accountingName;
	}

	function deletePeriodAccounting($id, $begin, $end) {
		$monthBegin = date('Ym', strtotime($begin));
		$monthEnd = date('Ym', strtotime($end));
		$monthCur = $monthBegin;

		do {
			$cacheKey = $id . '_' . $monthCur;
			Cache::delete($cacheKey, $cacheName);
			$monthCur = date('Ym', strtotime($monthCur . '01 +1 month'));
		} while ($monthCur <= $monthEnd);
	}

	function deleteAccounting($id = '', $begin = null, $end = null) {
		if ($begin == null || $end == null) {
			$settings = Cache::settings();
			$basename = $settings['prefix'] . $id . '_';
			$basenamelen = strlen($basename);
			$prefixlen = strlen($settings['prefix']);

			if ($settings['engine'] == 'File') {
				/* Se o cache for em arquivos podemos fazer um loop no 
				 * diretório e remover somente os meses
				 * existentens. */
				if ($handle = opendir($settings['path'])) {
					while (($file = readdir($handle)) !== false) {
						if (strncmp($file, $basename, $basenamelen) == 0) {
							$cacheKey = substr($file, $prefixlen);
							Cache::delete($cacheKey, $this->accountingName);
						}
					}
				}
			} else {
				/* Fazer um loop em todos os anos e meses possíveis. */
				$begin = date('Ym', 0);
				$end = date('Ym');
				$this->deletePeriodAccounting($id, $begin, $end);
			}
		} else {
			$this->deletePeriodAccounting($id, $begin, $end);
		}
	}

}
?>
