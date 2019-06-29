<?php
	if (!isset($nodirect)) die('nope');

	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
//nggo tambah data
		case 'add':
			if (!isset(
					$_POST['nama'], $_POST['keterangan'],
					$_POST['saldo_awal']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$keterangan = trim($_POST['keterangan']);
			$saldo_awal = $_POST['saldo_awal'];

			//if (preg_match('/[^ A-Za-z]/', $nama, $matches)
			if (!( (validLen($nama, INPUT_CASHBOOK_NAMA_MIN, INPUT_CASHBOOK_NAMA_MAX))
					&& (validLen($keterangan, 0, INPUT_CASHBOOK_KETERANGAN_MAX))
			)) errorLength();

			$access_kas = array();
			if (isset($_POST['access_kas'])) {
					foreach ($_POST['access_kas'] as $key => $array) {
							array_push($access_kas, $key);
					}
			}
			$db['query'] = $db['con'] -> prepare('INSERT INTO kas VALUES(null, :nama, :saldo_awal, :saldo_awal, 0, :keterangan, :access_kas)');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':saldo_awal' => $saldo_awal,
					':access_kas' => json_encode($access_kas),
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
					$_POST['id']
			)) errorInvalid();
			if (is_nan($_POST['id'])) errorInvalid();
			$id = $_POST['id'];
			$nama = trim($_POST['nama']);
			$keterangan = trim($_POST['keterangan']);

			//if (preg_match('/[^ A-Za-z]/', $nama, $matches)
			if (!( (validLen($nama, INPUT_CASHBOOK_NAMA_MIN, INPUT_CASHBOOK_NAMA_MAX))
					&& (validLen($keterangan, 0, INPUT_CASHBOOK_KETERANGAN_MAX))
			)) errorLength();

			$access_kas = array();
			if (isset($_POST['access_kas'])) {
					foreach ($_POST['access_kas'] as $key => $array) {
							array_push($access_kas, $key);
					}
			}
			$db['query'] = $db['con'] -> prepare('UPDATE kas SET nama = :nama, keterangan = :keterangan, users = :access_kas WHERE id = :id');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':id' => $id,
					':access_kas' => json_encode($access_kas),
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
			if ($id <= 5) send([
					'status' => 'error',
					'desc' => 'Buku kas utama tidak dapat dihapus'
			]);
			$db['query'] = $db['con'] -> prepare('DELETE FROM kas WHERE id = :id');
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
							$order_by = 'keterangan'; break;
					case 2:
							$order_by = 'saldo_awal'; break;
					case 3:
							$order_by = 'saldo_now'; break;
					case 4:
							$order_by = 'act'; break;
					case 5:
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
			$db['query'] = $db['con'] -> prepare('SELECT COUNT(*) AS total FROM kas;');
			$db['query'] -> execute();
			$db['res'] = $db['query'] -> fetchAll();
			$rTotal = $db['res'][0][0];

			$db['query'] = $db['con'] -> prepare('SELECT id, nama, saldo_awal, saldo_now, act, keterangan FROM kas WHERE nama LIKE :search OR saldo_awal LIKE :search OR saldo_now LIKE :search OR act LIKE :search OR keterangan LIKE :search OR id LIKE :search ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length);
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
							'saldo_awal' => $row['saldo_awal'],
							'saldo_now' => $row['saldo_now'],
							'keterangan' => nl2br(htmlspecialchars($row['keterangan'])),
							'act' => $row['act'],
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
			$db['query'] = $db['con'] -> prepare('SELECT id, nama FROM users');
			if (!$db['query'] -> execute()) {
					errorQuery($db['con'] -> errorInfo());
			}
			$users = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			if (!isset($_POST['id'])) {
					//no edit, common fetch
					send([
							'status' => 'success',
							'users' => $users
					]);
			}
			if (is_nan($_POST['id'])) {
					errorInvalid();
			}
			$db['query'] = $db['con'] -> prepare('SELECT id, nama, keterangan, users FROM kas WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
			])) errorQuery($db['con'] -> errorInfo());
			if ($db['query'] -> rowCount() < 1) {
					errorIDNotFound();
			}
			$db['res'] = $db['query'] -> fetchAll();
			send([
					'status' => 'success',
					'users' => $users,
					'nama' => $db['res'][0]['nama'],
					'keterangan' => $db['res'][0]['keterangan'],
					'access_kas' => $db['res'][0]['users'],
			]);
			//edit
			break;
		default:
				errorInvalid();
		}
	} else {
			errorInvalid();
	}

?>
