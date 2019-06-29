<?php
	if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
//nggo tambah data
		case 'add':
			if (!isset(
				$_POST['nama'], $_POST['keterangan'],
				$_POST['nama_struk'], $_POST['harga_pokok'],
				$_POST['harga_ecer'], $_POST['harga_grosir_min'][0],
				$_POST['harga_grosir_max'][0], $_POST['harga_grosir'][0]
			)) errorInvalid();
			$nama_produk = trim($_POST['nama']);
			$nama_struk = trim($_POST['nama_struk']);
			$keterangan = trim($_POST['keterangan']);
			$harga_pokok = $_POST['harga_pokok'];
			$harga_ecer = $_POST['harga_ecer'];
			$harga_grosir = [];
			//$curPhoto = $_POST['photos-cur'];
			//if (preg_match('/[^ A-Za-z]/', $nama, $matches)
			if (!( (validLen($nama_produk, INPUT_PRODUK_NAMA_PRODUK_MIN, INPUT_PRODUK_NAMA_PRODUK_MAX))
					&& (validLen($nama_struk, INPUT_PRODUK_NAMA_STRUK_MIN, INPUT_PRODUK_NAMA_STRUK_MAX))
					&& (validLen($keterangan, 0, INPUT_PRODUK_KETERANGAN_MAX))
					&& (validLen($harga_pokok, INPUT_PRODUK_HARGA_MIN, INPUT_PRODUK_HARGA_MAX))
					&& (validLen($harga_ecer, INPUT_PRODUK_HARGA_MIN, INPUT_PRODUK_HARGA_MAX))
			)) errorLength();

			foreach($_POST['harga_grosir'] as $index => $val) {
				//no allowed 0!!
					if (!( (floatval($_POST['harga_grosir'][$index]))
						&& (intval($_POST['harga_grosir_min'][$index]))
						&& (intval($_POST['harga_grosir_max'][$index]))
					 )) break;
				array_push($harga_grosir, [
					$_POST['harga_grosir_min'][$index],
					$_POST['harga_grosir_max'][$index],
					$_POST['harga_grosir'][$index]
				]);
			}
			$photos = [];

			//foreach ();

			$db['query'] = $db['con'] -> prepare('INSERT INTO produk VALUES(null, 0, :nama_produk, :nama_struk, :harga_pp, :harga_ecer, :harga_grosir, :keterangan, :photos)');
			if (!$db['query'] -> execute([
					':nama_produk' => $nama_produk,
					':nama_struk' => $nama_struk,
					':harga_pp' => $harga_pokok,
					':harga_ecer' => $harga_ecer,
					':harga_grosir' => json_encode($harga_grosir, JSON_NUMERIC_CHECK),
					':keterangan' => $keterangan,
					':photos' => json_encode($photos),
			])) errorQuery($db['con'] -> errorInfo());

			$lastId = $db['con'] -> lastInsertId();

			if (isset($_FILES['photos'])) {
				foreach ($_FILES['photos']['tmp_name'] as $index => $val) {
					/*$photoExt = pathinfo($_FILES['photos']['name'][$index]);
					$photoExt = $photoExt['extension'];*/
					$photoName = $lastId.'-'.$index;
					array_push($photos, $index);
					@move_uploaded_file($_FILES['photos']['tmp_name'][$index], getcwd().'/'.PATH_IMG_PRODUCT.$photoName);
				}
				//send([$test, $piro]);
			}
			$db['query'] = $db['con'] -> prepare('UPDATE produk SET foto = :photos WHERE id = :id');
			if (!$db['query'] -> execute([
					':photos' => json_encode($photos),
					':id' => $lastId
			])) errorQuery($db['con'] -> errorInfo());

			send([
				'status' => 'success',
				'id' => $lastId
			]);

			break;
//nggo update data
		case 'edit':
			//return '[]';
			if (!isset(
					$_POST['nama'], $_POST['keterangan'],
					$_POST['nama_struk'], $_POST['harga_pokok'],
					$_POST['harga_ecer'], $_POST['harga_grosir_min'][0],
					$_POST['harga_grosir_max'][0], $_POST['harga_grosir'][0],
					$_POST['id'], $_POST['photos-exist']
			)) errorInvalid();
			$nama_produk = trim($_POST['nama']);
			$nama_struk = trim($_POST['nama_struk']);
			$keterangan = trim($_POST['keterangan']);
			$harga_pokok = $_POST['harga_pokok'];
			$harga_ecer = $_POST['harga_ecer'];
			$harga_grosir = [];
			$id = $_POST['id'];
			$photos_exist = $_POST['photos-exist'];
			if (!( (validLen($nama_produk, INPUT_PRODUK_NAMA_PRODUK_MIN, INPUT_PRODUK_NAMA_PRODUK_MAX))
					&& (validLen($nama_struk, INPUT_PRODUK_NAMA_STRUK_MIN, INPUT_PRODUK_NAMA_STRUK_MAX))
					&& (validLen($keterangan, 0, INPUT_PRODUK_KETERANGAN_MAX))
					&& (validLen($harga_pokok, INPUT_PRODUK_HARGA_MIN, INPUT_PRODUK_HARGA_MAX))
					&& (validLen($harga_ecer, INPUT_PRODUK_HARGA_MIN, INPUT_PRODUK_HARGA_MAX))
			)) errorLength();

			foreach($_POST['harga_grosir'] as $index => $val) {
				//no allowed 0!!
					if (!( (floatval($_POST['harga_grosir'][$index]))
						&& (intval($_POST['harga_grosir_min'][$index]))
						&& (intval($_POST['harga_grosir_max'][$index]))
					 )) break;
					if (!validLen($_POST['harga_grosir'][$index], INPUT_PRODUK_HARGA_MIN, INPUT_PRODUK_HARGA_MAX))
						errorLength();
					array_push($harga_grosir, [
					$_POST['harga_grosir_min'][$index],
					$_POST['harga_grosir_max'][$index],
					$_POST['harga_grosir'][$index]
				]);
			}

			$photos = [];
			$photos = json_decode($photos_exist, true);
			if (is_null($photos)) errorInvalid();
			if (intval($id) == 0) errorInvalid();
			$lastFoto = max($photos) + 1;

			if (isset($_FILES['photos'])) {
				foreach ($_FILES['photos']['tmp_name'] as $index => $val) {
					/*$photoExt = pathinfo($_FILES['photos']['name'][$index]);
					$photoExt = $photoExt['extension'];*/
					$photoName = $id.'-'.($lastFoto + $index);
					array_push($photos, $lastFoto + $index);
					@move_uploaded_file($_FILES['photos']['tmp_name'][$index], getcwd().'/'.PATH_IMG_PRODUCT.$photoName);
				}
			}
			$db['query'] = $db['con'] -> prepare('UPDATE produk SET nama_produk = :nama_produk, nama_struk = :nama_struk, harga_pp = :harga_pp, harga_ecer = :harga_ecer, harga_grosir = :harga_grosir, keterangan = :keterangan, foto = :photos WHERE id = :id');
			if (!$db['query'] -> execute([
					':nama_produk' => $nama_produk,
					':nama_struk' => $nama_struk,
					':harga_pp' => $harga_pokok,
					':harga_ecer' => $harga_ecer,
					':harga_grosir' => json_encode($harga_grosir, JSON_NUMERIC_CHECK),
					':keterangan' => $keterangan,
					':photos' => json_encode($photos),
					':id' => $id,
			])) errorQuery($db['con'] -> errorInfo());

			send([
					'status' => 'success',
					'id' => $id
			]);

			break;
//hapus
// TODO: ndelet variant sisan
		case 'del':
			if (!isset(
					$_POST['id']
			)) errorInvalid();
			if (is_nan($_POST['id'])) errorInvalid();
			$id = $_POST['id'];
			$db['query'] = $db['con'] -> prepare('DELETE FROM produk WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => $id,
			])) errorQuery($db['con'] -> errorInfo());
			$db['query'] = $db['con'] -> prepare('DELETE FROM produk_variant WHERE id_produk = :id');
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
					case 2:
							$order_by = 'nama_struk'; break;
					case 3:
							$order_by = 'harga_ecer'; break;
					case 4:
							$order_by = 'harga_grosir'; break;
					case 0:
					case 1:
					default:
							$order_by = 'nama_produk'; break;
			}
			$order_as = $_POST['order'][0]['dir'];
			switch ($order_as) {
					case 'desc': $order_as = 'desc'; break;
					case 'asc':
					default: $order_as = 'asc'; break;
			}
			$db['query'] = $db['con'] -> prepare('SELECT COUNT(*) AS total FROM produk;');
			$db['query'] -> execute();
			$db['res'] = $db['query'] -> fetchAll();
			$rTotal = $db['res'][0][0];

			$db['query'] = $db['con'] -> prepare('SELECT id, nama_produk, nama_struk, harga_ecer, harga_grosir FROM produk WHERE nama_produk LIKE :search OR nama_struk LIKE :search OR harga_ecer LIKE :search OR harga_grosir LIKE :search OR id LIKE :search ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length);
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
							'nama' => htmlspecialchars($row['nama_produk']),
							'nama_struk' => htmlspecialchars($row['nama_struk']),
							'harga_satuan' => $row['harga_ecer'],
							'harga_grosir' => $row['harga_grosir'],
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
		if (!validLen($search, 0, INPUT_PRODUK_NAMA_PRODUK_MAX ))
			errorLength();
		$params[':search'] = '%'.$search.'%';
		$filter = isset($_POST['options']['filter']) ? $_POST['options']['filter']: '';
		if ($filter == 'group') $filter = 'AND id_group = 0';
		if ($filter == 'group&id') {
			if (isset($_POST['options']['filterId'])) {
				if (filter_var($_POST['options']['filterId'], FILTER_VALIDATE_INT) == false)
					errorInvalid();
				else {
					$filter = 'AND id_group = 0 OR id_group = :id_group_exist';
					//array_push($params, [':id_product' => filter_var($_POST['options']['filterId'], FILTER_VALIDATE_INT)]);
					$params[':id_group_exist'] = filter_var($_POST['options']['filterId'], FILTER_VALIDATE_INT);
				}
			} else errorInvalid();
		}
		$q = 'SELECT nama_produk, id FROM produk WHERE '.
			'nama_produk LIKE :search '.$filter;
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute($params)) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		$result = [];
		foreach ($db['res'] as $key => $val) {
			$val['nama_produk'] = htmlspecialchars($val['nama_produk']);
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
