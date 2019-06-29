<?php
	if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
	if (isset($_GET['act'])) {
		switch (strtolower($_GET['act'])) {
//nggo tambah data
		case 'add':
			if (!isset(
				$_POST['nama'], $_POST['added_product']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$id_product = $_POST['added_product'];

			if (!(
				(is_array($id_product))
			)) errorInvalid();

			if (!( (validLen($nama, 0, INPUT_GROUP_NAMA_MAX))
			)) errorLength();

			$db['query'] = $db['con'] -> prepare('INSERT INTO produk_group VALUES(null, :nama)');
			if (!$db['query'] -> execute([
					':nama' => $nama
			])) errorQuery($db['con'] -> errorInfo());

			$lastId = $db['con'] -> lastInsertId();

			$db['query'] = $db['con'] -> prepare('UPDATE produk SET id_group = :id_group WHERE id = :id_produk');
			foreach($id_product as $index => $val) {
				if (is_nan($id_product[$index])) errorInvalid();
				if (!$db['query'] -> execute([
					':id_group' => $lastId,
					':id_produk' => $id_product[$index]
				])) errorQuery($db['con'] -> errorInfo());
			}
			send([
				'status' => 'success'
			]);

			break;
//nggo update data
		case 'edit':
			if (!isset(
				$_POST['nama'], $_POST['added_product'],
				$_POST['id']
			)) errorInvalid();
			$nama = trim($_POST['nama']);
			$id_product = $_POST['added_product'];
			$id_group = $_POST['id'];
			$deleted_product = isset($_POST['deleted_product']) ? $_POST['deleted_product'] : [];
			if (!(
				(is_array($id_product)) &&
				(is_array($deleted_product)) &&
				(!is_nan($id_group))
			)) errorInvalid();

			if (!( (validLen($nama, 0, INPUT_GROUP_NAMA_MAX))
			)) errorLength();

			$db['query'] = $db['con'] -> prepare('UPDATE produk_group SET nama = :nama WHERE id = :id');
			if (!$db['query'] -> execute([
					':nama' => $nama,
					':id' => $id_group
			])) errorQuery($db['con'] -> errorInfo());

			$db['query'] = $db['con'] -> prepare('UPDATE produk SET id_group = :id_group WHERE id = :id_produk');
			foreach($id_product as $index => $val) {
				if (is_nan($id_product[$index])) errorInvalid();
				if (!$db['query'] -> execute([
					':id_group' => $id_group,
					':id_produk' => $id_product[$index]
				])) errorQuery($db['con'] -> errorInfo());
			}
			foreach($deleted_product as $index => $val) {
				if (is_nan($deleted_product[$index])) errorInvalid();
				if (!$db['query'] -> execute([
					':id_group' => 0,
					':id_produk' => $deleted_product[$index]
				])) errorQuery($db['con'] -> errorInfo());
			}
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
			$db['query'] = $db['con'] -> prepare('DELETE FROM produk_group WHERE id = :id');
			if (!$db['query'] -> execute([
					':id' => $id,
			])) errorQuery($db['con'] -> errorInfo());
			$db['query'] = $db['con'] -> prepare('UPDATE produk SET id_group = 0 WHERE id_group = :id_group');
			if (!$db['query'] -> execute([
					':id_group' => $id,
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
							$order_by = 'id_group'; break;
					case 2:
							$order_by = 'nama_produk'; break;
					case 0:
					default:
							$order_by = 'nama_group'; break;
			}
			$order_as = $_POST['order'][0]['dir'];
			switch ($order_as) {
					case 'desc': $order_as = 'desc'; break;
					case 'asc':
					default: $order_as = 'asc'; break;
			}
			$db['query'] = $db['con'] -> prepare('SELECT COUNT(*) AS total FROM produk_group;');
			$db['query'] -> execute();
			$db['res'] = $db['query'] -> fetchAll();
			$rTotal = $db['res'][0][0];

			$db['query'] = $db['con'] -> prepare('SELECT produk.id AS id_produk, produk.nama_produk, produk_group.id AS id_group, produk_group.nama AS nama_group FROM produk RIGHT JOIN produk_group ON produk.id_group = produk_group.id WHERE nama_produk LIKE :search OR produk_group.nama LIKE :search  OR id_group LIKE :search ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length);
			$db['query'] -> execute(array(
					':search' => '%'. $search .'%'
			));
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			$data = []; $list = [];

			foreach ($db['res'] as $index => $row) {
				$list[$row['id_group']][] = $row['nama_produk'];
				/*array_push($list[fck, stuck],
					$row['nama_produk']
				);*/
			}
			$curID = 0;
			foreach ($db['res'] as $index => $row) {
				if ($curID == $row['id_group']) continue;
				$curID = $row['id_group'];
				array_push($data, [
					//'DT_RowId' => 'row_'.$row['id'],
					'DT_RowAttr' => array(
						'data-id' => $row['id_group']
					),
					'nama' => htmlspecialchars($row['nama_group']),
					'list' => $list[$row['id_group']],
					'id' => $row['id_group']
				]);
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
			if (!isset($_POST['id'])) errorInvalid();
			if (is_nan($_POST['id'])) {
					errorInvalid();
			}
			$db['query'] = $db['con'] -> prepare('SELECT produk.id AS id_produk, produk.nama_produk, produk_group.id AS id_group, produk_group.nama AS nama_group FROM produk RIGHT JOIN produk_group ON produk.id_group = produk_group.id WHERE produk.id_group = :id_group');
			$db['query'] -> execute(array(
					':id_group' => filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
			));
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
			$list = [];
			if ($db['query'] -> rowCount() < 1) {
					errorIDNotFound();
			}
			foreach ($db['res'] as $index => $row) {
				$list[] = [
					'name' => $row['nama_produk'],
					'id' => $row['id_produk']
				];
			}
			send([
					'status' => 'success',
					'name' => $db['res'][0]['nama_group'],
					'list' => $list
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
