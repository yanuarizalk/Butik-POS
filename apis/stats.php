<?php
	if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
		case 'all_sales':
			if (!(isset($_POST['range']))) errorInvalid();
			$range['unit'] = $_POST['range'];
			switch ($range['unit']) {
				case 'month':
					$range['month'] = isset($_POST['month']) ? intval($_POST['month']) : errorInvalid();
					$range['year'] = isset($_POST['year']) ? intval($_POST['year']) : errorInvalid();
					$range['from'] = strtotime('1.'.$range['month'].'.'.$range['year']);
					$range['to'] = strtotime('1.'.$range['month'].'.'.$range['year'].' +1 month');
					break;
				case 'year':
					$range['year'] = isset($_POST['year']) ? intval($_POST['year']) : errorInvalid();
					$range['from'] = strtotime('1.1.'.$range['year']);
					$range['to'] = strtotime('1.1.'.$range['year'].' +1 year');
					break;
				default:
					errorInvalid();
			}
			//die(var_dump($range));
			$q = "SELECT dt, sum(total_sale) AS total_sale FROM sales WHERE dt between :from AND :to GROUP BY dt ORDER BY dt ASC";
			$db['query'] = $db['con'] -> prepare($q);
			if (!($db['query']) -> execute([
				':from' => $range['from'],
				':to' => $range['to']
			])) errorQuery($db['con'] -> errorInfo());
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			$xy = []; $pastDay = '';
			foreach ($db['res'] as $key => $val) {
				if (date('d M Y', $val['dt']) == $pastDay) {
					$xy[count($xy) - 1][1] += floatval($val['total_sale']);
				}
				else {
					$pastDay = date('d M Y', $val['dt']);
					$xy[] = [strtotime($pastDay), floatval($val['total_sale'])];
				}
			}
			send([
				'status' => 'success',
				'stats' => [
					'label' => 'Penjualan '.date('M Y', $range['from']).' - '.date('M Y', $range['to']),
					'data' => $xy
				]
			]);
			break;
		default:
				errorInvalid();
		}
	} else {
		errorInvalid();
	}

?>
