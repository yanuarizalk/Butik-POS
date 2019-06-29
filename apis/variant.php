<?php
	if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
//nggo tambah data
		case 'add':
			if (!isset(
				$_POST['nama'],
				$_POST['lokasi_butik'], $_POST['lokasi_online'],
				$_POST['stock_butik'], $_POST['stock_online'],
				$_POST['stock_bazar_a'], $_POST['id_produk'],
				$_POST['stock_bazar_b'], $_POST['stock_bazar_c']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$lokasi_online = trim($_POST['lokasi_online']);
			$lokasi_butik = trim($_POST['lokasi_butik']);
			$stock_online = $_POST['stock_online'];
			$stock_butik = $_POST['stock_butik'];
			$stock_bazar_a = $_POST['stock_bazar_a'];
			$stock_bazar_b = $_POST['stock_bazar_b'];
			$stock_bazar_c = $_POST['stock_bazar_c'];
			$id_produk = $_POST['id_produk'];

			if (!( (validLen($nama, INPUT_VARIANT_NAMA_MIN , INPUT_VARIANT_NAMA_MAX))
				&& (validLen($lokasi_online, 0, INPUT_VARIANT_LOKASI_MAX))
				&& (validLen($lokasi_butik, 0, INPUT_VARIANT_LOKASI_MAX))
				&& (validLen($stock_bazar_a, INPUT_STOCK_MIN, INPUT_STOCK_MAX ))
				&& (validLen($stock_bazar_b, INPUT_STOCK_MIN , INPUT_STOCK_MAX))
				&& (validLen($stock_bazar_c, INPUT_STOCK_MIN, INPUT_STOCK_MAX))
				&& (validLen($stock_online, INPUT_STOCK_MIN, INPUT_STOCK_MAX))
				&& (validLen($stock_butik, INPUT_STOCK_MIN, INPUT_STOCK_MAX))
			)) errorLength();

			if ((is_nan($stock_bazar_a)) ||
				(is_nan($stock_bazar_b)) ||
				(is_nan($stock_bazar_c)) ||
				(is_nan($stock_online)) ||
				(is_nan($stock_butik)) ||
				(intval($id_produk) == 0)
			 ) errorInvalid();
			$stock_awal = [
				ID_ONLINE => $stock_online,
				ID_BUTIK => $stock_butik,
				ID_BAZAR_A => $stock_bazar_a,
				ID_BAZAR_B => $stock_bazar_b,
				ID_BAZAR_C => $stock_bazar_c
			];
			$lokasi = [
				ID_ONLINE => $lokasi_online,
				ID_BUTIK => $lokasi_butik
			];
			$db['query'] = $db['con'] -> prepare('INSERT INTO produk_variant VALUES(null, :id_produk, 0, :nama, :stock_awal, :lokasi) ');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':id_produk' => $id_produk,
					':stock_awal' => json_encode($stock_awal, JSON_NUMERIC_CHECK),
					':lokasi' => json_encode($lokasi)
			])) errorQuery($db['con'] -> errorInfo());

			$lastId = $db['con'] -> lastInsertId();
			$barcode = str_pad($lastId, BARCODE_LENGTH, "0", STR_PAD_LEFT);

			$db['query'] = $db['con'] -> prepare('UPDATE produk_variant SET barcode = :barcode WHERE id = :id');
			if (!$db['query'] -> execute([
					':barcode' => $barcode,
					':id' => $lastId
			])) errorQuery($db['con'] -> errorInfo());
			$dt = time();
			$db['query'] = $db['con'] -> prepare('INSERT INTO produk_inventory VALUES(null, '.$lastId.', :id_toko, \'IN\', :qty, '.$dt.', \'Stock Awal\')');
			foreach ($stock_awal as $index => $val) {
				if ($val > 0) {
					if (!$db['query'] -> execute([
						':id_toko' => $index, ':qty' => $val
					])) errorQuery($db['con'] -> errorInfo());
				}
			}
			send([
					'status' => 'success'
			]);

			break;
//nggo update data
		case 'edit':
			if (!isset(
				$_POST['nama'], $_POST['id'],
				$_POST['lokasi_butik'], $_POST['lokasi_online']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$lokasi_online = trim($_POST['lokasi_online']);
			$lokasi_butik = trim($_POST['lokasi_butik']);
			$id = $_POST['id'];

			if (!( (validLen($nama, INPUT_VARIANT_NAMA_MIN , INPUT_VARIANT_NAMA_MAX))
					&& (validLen($lokasi_online, 0, INPUT_VARIANT_LOKASI_MAX))
					&& (validLen($lokasi_butik, 0, INPUT_VARIANT_LOKASI_MAX))
			)) errorLength();

			if (intval($id) == 0) errorInvalid();
			$lokasi = [
				ID_ONLINE => $lokasi_online,
				ID_BUTIK => $lokasi_butik
			];
			$db['query'] = $db['con'] -> prepare('UPDATE produk_variant SET nama = :nama, lokasi = :lokasi WHERE id = :id ');
			if (!$db['query'] -> execute([
				':nama' => $nama,
				':lokasi' => json_encode($lokasi),
				':id' => $id
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
			$db['query'] = $db['con'] -> prepare('DELETE FROM produk_variant WHERE id = :id');
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

			$db['query'] = $db['con'] -> prepare('SELECT * FROM produk_variant WHERE id_produk = :id ');
			$db['query'] -> execute(array(
				':id' => filter_input(INPUT_POST, 'id_produk', FILTER_SANITIZE_NUMBER_INT)
			));
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			$data = [];
			foreach ($db['res'] as $row) {
				$parseLokasi = json_decode($row['lokasi'], true);
				array_push($data, [
					'DT_RowAttr' => array(
							'data-id' => $row['id']
					),
					'nama' => out($row['nama']),
					'barcode' => out($row['barcode']),
					'lokasi_butik' => $parseLokasi[ID_BUTIK],
					'lokasi_online' => $parseLokasi[ID_ONLINE],
					'id' => $row['id']
				]);
			}
			$rTotal = $db['query'] -> rowCount();
			send(array(
					'draw' => $draw,
					'recordsTotal' => $rTotal,
					'recordsFiltered' => $rTotal,
					'data' => $data,
					'error' => ''
			));


			break;
		case 'fetch':
			if (!isset($_POST['id']))
				errorInvalid();
			if (intval($_POST['id']) == 0)
				errorInvalid();
			$db['query'] = $db['con'] -> prepare('SELECT nama, lokasi, barcode FROM produk_variant WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
			])) errorQuery($db['con'] -> errorInfo());
			if ($db['query'] -> rowCount() < 1) {
					errorIDNotFound();
			}
			$db['res'] = $db['query'] -> fetchAll();
			$parseLokasi = json_decode($db['res'][0]['lokasi'], true);
			send([
					'status' => 'success',
					'nama' => $db['res'][0]['nama'],
					'lokasi_butik' => $parseLokasi[ID_BUTIK],
					'lokasi_online' => $parseLokasi[ID_ONLINE],
					'barcode' => $db['res'][0]['barcode']
			]);
			//edit
			break;
	case 'name-fetch':
		if (!isset($_POST['search']))
			errorInvalid();
		$search = $_POST['search'];
		if (!validLen($search, 0, INPUT_PRODUK_NAMA_PRODUK_MAX ))
			errorLength();

		$q = 'SELECT produk.nama_produk, produk_variant.nama as nama_variant, produk_variant.barcode, produk.id_group, '.
				'produk.id as id_produk, produk_variant.id as id_variant, produk.harga_ecer, produk.harga_grosir FROM produk_variant INNER JOIN '.
				'produk ON produk_variant.id_produk = produk.id WHERE '.
				'produk.nama_produk LIKE :search OR produk_variant.nama LIKE :search';
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute([
				':search' => '%'.$search.'%'
		])) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		$result = [];
		foreach ($db['res'] as $key => $val) {
			$val['nama_produk'] = htmlspecialchars($val['nama_produk']);
			$val['nama_variant'] = htmlspecialchars($val['nama_variant']);
			array_push($result, $val);
		}
		send([
				'status' => 'success',
				'result' => $result
		]);
		break;
	case 'barcode-fetch':
		if (!isset($_POST['search']))
			errorInvalid();
		$search = $_POST['search'];
		if (!validLen($search, BARCODE_LENGTH, BARCODE_LENGTH ))
			errorLength();
		$barcode = $_POST['search'];

		$q = 'SELECT produk.nama_produk, produk_variant.nama as nama_variant, produk_variant.barcode, produk.id_group, '.
				'produk.id as id_produk, produk_variant.id, produk.harga_ecer, produk.harga_grosir FROM produk_variant INNER JOIN '.
				'produk ON produk_variant.id_produk = produk.id WHERE '.
				'produk_variant.barcode = :barcode';
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute([
				':barcode' => $barcode
		])) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		if ($db['query'] -> rowCount() < 1) {
			send([
				'status' => 'notfound'
			]);
		}
		$result = $db['res'][0];
		$result['nama_produk'] = htmlspecialchars($result['nama_produk']);
		$result['nama_variant'] = htmlspecialchars($result['nama_variant']);
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
