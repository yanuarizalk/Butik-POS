<?php
	if (!isset($nodirect)) die('nope');

	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
//nggo tambah data
		case 'add':
			if (!$_FILESsset(
				$_POST['nama'], $_POST['keterangan'],
				$_POSTT['nohp']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$keterangan = trim($_POST['keterangan']);
			$nohp = trim($_POST['nohp']);

			//if (preg_match('/[^ A-Za-z]/', $nama, $matches)
			if (!( (validLen($nama, INPUT_SPG_NAMA_MIN, INPUT_SPG_NAMA_MAX))
					&& (validLen($keterangan, 0, INPUT_SPG_KETERANGAN_MAX))
					&& (validLen($nohp, 0, INPUT_SPG_NOHP_MAX))
			)) errorLength();

			$db['query'] = $db['con'] -> prepare('INSERT INTO spg VALUES(null, :nama, :nohp, :keterangan)');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':nohp' => $nohp,
					':keterangan' => $keterangan,
			])) errorQuery($db['con'] -> errorInfo());
			send([
					'status' => 'success'
			]);

			break;
//nggo update data
	case 'edit':
			if (!isset(
					$_POST['nama'], $_POST['keterangan'],
					$_POST['id'], $_POST['nohp']
			)) errorInvalid();
			if (is_nan($_POST['id'])) errorInvalid();
			$id = $_POST['id'];
			$nama = trim($_POST['nama']);
			$keterangan = trim($_POST['keterangan']);
			$nohp = trim($_POST['nohp']);

			//if (preg_match('/[^ A-Za-z]/', $nama, $matches)
			if (!( (validLen($nama, INPUT_SPG_NAMA_MIN, INPUT_SPG_NAMA_MAX))
					&& (validLen($keterangan, 0, INPUT_SPG_KETERANGAN_MAX))
					&& (validLen($nohp, 0, INPUT_SPG_NOHP_MAX))
			)) errorLength();

			$db['query'] = $db['con'] -> prepare('UPDATE spg SET nama = :nama, keterangan = :keterangan, no_hp = :nohp WHERE id = :id');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':id' => $id,
					':nohp' => $nohp,
					':keterangan' => $keterangan,
			])) errorQuery($db['con'] -> errorInfo());
			send([
					'status' => 'success'
			]);

			break;
//hapus
	case 'del':
			if (!isset(
					$_POST['id']
			)) errorInvalid();
			if (is_nan($_POST['id'])) errorInvalid();
			$id = $_POST['id'];
			$db['query'] = $db['con'] -> prepare('DELETE FROM spg WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => $id,
			])) errorQuery($db['con'] -> errorInfo());
			send([
					'status' => 'success'
			]);
			break;
//request sko datatable
	case 'dt':
			$draw = $_POST['draw'];
			$start = $_POST['start'];
			if (is_nan($start)) $start = 0;
			$length = $_POST['length'];
			if (is_nan($length)) $length = 10;
			//$search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
			$search = $_POST['search']['value'];
			$order_by = $_POST['order'][0]['column'];
			switch ($order_by) {
					case 1:
							$order_by = 'no_hp'; break;
					case 3:
							$order_by = 'id'; break;
					case 0:
					default:
							$order_by = 'nama'; break;
			}
			$order_as = $_POST['order'][0]['dir'];
			switch ($order_as) {
					case 'desc': $order_as = 'desc'; break;
					case 'asc':
					default: $order_as = 'asc'; break;
			}
			$db['query'] = $db['con'] -> prepare('SELECT COUNT(*) AS total FROM spg;');
			$db['query'] -> execute();
			$db['res'] = $db['query'] -> fetchAll();
			$rTotal = $db['res'][0][0];

			$db['query'] = $db['con'] -> prepare('SELECT id, nama, no_hp, keterangan FROM spg WHERE nama LIKE :search OR no_hp LIKE :search OR keterangan LIKE :search OR id LIKE :search ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length);
			$db['query'] -> execute(array(
					':search' => '%'. $search .'%'
			));
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			$data = array();
			foreach ($db['res'] as $row) {
					array_push($data, array(
							//'DT_RowId' => 'row_'.$row['id'],
							'DT_RowAttr' => array(
									'data-id' => $row['id']
							),
							'nama' => htmlspecialchars($row['nama']),
							'nohp' => htmlspecialchars($row['no_hp']),
							'keterangan' => nl2br(htmlspecialchars(($row['keterangan']))),
							'id' => $row['id']
					));
			}
			//$data = $db['res'];
			if ($search == '') $rFilter = $rTotal;
			else $rFilter = $db['query'] -> rowCount();
			send(array(
					'draw' => $draw,
					'recordsTotal' => $rTotal,
					'recordsFiltered' => $rFilter,
					'data' => $data,
					'error' => ''
			));


			break;
	case 'fetch':
			if (!isset($_POST['id'])) {
					errorInvalid();
			}
			if (is_nan($_POST['id'])) {
					errorInvalid();
			}
			$db['query'] = $db['con'] -> prepare('SELECT id, nama, keterangan, no_hp FROM spg WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
			])) errorQuery($db['con'] -> errorInfo());
			if ($db['query'] -> rowCount() < 1) {
					errorIDNotFound();
			}
			$db['res'] = $db['query'] -> fetchAll();
			send([
					'status' => 'success',
					'nama' => $db['res'][0]['nama'],
					'keterangan' => $db['res'][0]['keterangan'],
					'nohp' => $db['res'][0]['no_hp'],
			]);
			//edit
			break;
	case 'name-fetch':
		if (!isset($_POST['search']))
			errorInvalid();
		$search = $_POST['search'];
		if (!validLen($search, 0, INPUT_SPG_NAMA_MAX ))
			errorLength();

		$q = 'SELECT * FROM spg WHERE '.
				'nama LIKE :search';
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute([
				':search' => '%'.$search.'%'
		])) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		$result = [];
		foreach ($db['res'] as $key => $val) {
			$val['nama'] = htmlspecialchars($val['nama']);
			array_push($result, $val);
		}
		send([
				'status' => 'success',
				'result' => $result
		]);
		break;
		default:
				errorInvalid();
		}
	} else {
		errorInvalid();
	}

?>
