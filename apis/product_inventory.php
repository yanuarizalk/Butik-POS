<?php
if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
if (isset($_GET['act'])) {
	switch (strtolower($_GET['act'])) {
	case 'add':
		if (!isset(
			$_POST['act'],
			$_POST['id_toko'], $_POST['tanggal'],
			$_POST['jam'], $_POST['menit'],
			$_POST['keterangan'], $_POST['qty']
		)) errorInvalid();
		$keterangan = trim($_POST['keterangan']);
		$act = $_POST['act'];
		$id_toko = $_POST['id_toko'];
		$tanggal = strtotime($_POST['tanggal']);
		$jam = $_POST['jam'];
		$menit = $_POST['menit'];
		$qty = $_POST['qty'];

		if (($tanggal == false) ||
				(is_nan($jam)) || (is_nan($menit)) ||
				(is_array($qty) == false) || (is_nan($id_toko)) ||
				(!($act == 'IN' || $act == 'OUT'))
			 ) errorInvalid();

		if (!(($id_toko >= 1) && ($id_toko <= 5))) errorInvalid();

		$dt = $tanggal + (($menit * 60) * $jam);

		if (!( (validLen($keterangan, 0, INPUT_KETERANGAN_MAX ))
		)) errorLength();

		$db['query'] = $db['con'] -> prepare('INSERT INTO produk_inventory VALUES(null, :id_variant, :id_toko, :act, :qty, :dt, :keterangan) ');
		$q = 'UPDATE produk_variant '.
				'SET produk_variant.stock = JSON_SET(produk_variant.stock, \'$."'.
				$id_toko .'"\', '.
				'(SELECT SUM(IF(act = \'IN\', qty, -qty)) '.
				'AS total_stock FROM produk_inventory '.
				'WHERE id_variant = :id_variant AND id_toko = :id_toko GROUP BY id_variant)) '.
				'WHERE produk_variant.id = :id_variant';
		$db['query2'] = $db['con'] -> prepare($q);
		foreach ($qty as $key => $val) {
			if ($val <= 0) errorInvalid();
			if (!$db['query'] -> execute([
				':id_variant' => $key,
				':id_toko' => $id_toko,
				':act' => $act,
				':qty' => $val,
				':dt' => $dt,
				':keterangan' => $keterangan
			])) errorQuery($db['query'] -> errorInfo());

			if (!($db['query2'] -> execute([
				':id_variant' => $key,
				':id_toko' => $id_toko
			]))) errorQuery($db['query2'] -> errorInfo());
		}

		send([
			'status' => 'success'
		]);

		break;
	case 'transfer':
		if (!isset(
			$_POST['id_toko_to'],
			$_POST['id_toko_from'], $_POST['tanggal'],
			$_POST['jam'], $_POST['menit'],
			$_POST['keterangan'], $_POST['qty']
		)) errorInvalid();
		$keterangan = trim($_POST['keterangan']);
		$id_toko_from = $_POST['id_toko_from'];
		$id_toko_to = $_POST['id_toko_to'];
		$tanggal = strtotime($_POST['tanggal']);
		$jam = $_POST['jam'];
		$menit = $_POST['menit'];
		$qty = $_POST['qty'];

		if (($tanggal == false) ||
				(is_nan($jam)) || (is_nan($menit)) ||
				(is_array($qty) == false) || (is_nan($id_toko_from)) ||
				(is_nan($id_toko_to))
			 ) errorInvalid();

		if (!(
			($id_toko_from >= 1) && ($id_toko_from <= 5)) &&
			($id_toko_to >= 1) && ($id_toko_to <= 5) &&
			($id_toko_to != $id_toko_from)
			 ) errorInvalid();

		$dt = $tanggal + (($menit * 60) * $jam);

		if (!( (validLen($keterangan, 0, INPUT_KETERANGAN_MAX ))
		)) errorLength();

		$db['query'] = $db['con'] -> prepare('INSERT INTO produk_inventory VALUES(null, :id_variant, :id_toko_from, \'OUT\', :qty, :dt, :keterangan) ');
		$db['query2'] = $db['con'] -> prepare('INSERT INTO produk_inventory VALUES(null, :id_variant, :id_toko_to, \'IN\', :qty, :dt, :keterangan) ');
		$q = 'UPDATE produk_variant '.
				'SET produk_variant.stock = JSON_REPLACE(produk_variant.stock, '.
				'\'$."'.$id_toko_from .'"\', (SELECT SUM(IF(id_toko = '.$id_toko_from.
				', IF(act = \'IN\', qty, -qty), 0)) AS total_stock FROM produk_inventory '.
				'WHERE id_variant = :id_variant GROUP BY id_variant), \'$."'.
				$id_toko_to.'"\', (SELECT SUM(IF(id_toko = '.$id_toko_to.
				', IF(act = \'IN\', qty, -qty), 0)) AS total_stock FROM produk_inventory '.
				'WHERE id_variant = :id_variant GROUP BY id_variant)) WHERE produk_variant.id = :id_variant';
		$db['query3'] = $db['con'] -> prepare($q);
		foreach ($qty as $key => $val) {
			if ($val <= 0) errorInvalid();
			// Out
			if (!$db['query'] -> execute([
				':id_variant' => $key,
				':id_toko_from' => $id_toko_from,
				':qty' => $val, ':dt' => $dt,
				':keterangan' => $keterangan
			])) errorQuery($db['query'] -> errorInfo());
			// In
			if (!$db['query2'] -> execute([
				':id_variant' => $key,
				':id_toko_to' => $id_toko_to,
				':qty' => $val, ':dt' => $dt,
				':keterangan' => $keterangan
			])) errorQuery($db['query'] -> errorInfo());
			// Update
			if (!($db['query3'] -> execute([
				':id_variant' => $key
			]))) errorQuery($db['query'] -> errorInfo());
			//$db['query'] = $db['con'] -> prepare('SELECT act, qty FROM produk_inventory WHERE id_variant = :id_variant');
		}
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
		$search = $_POST['search']['value'];
		$order_by = $_POST['order'][0]['column'];
		switch ($order_by) {
			case 1:
					$order_by = 'nama_produk'; break;
			case 2:
					$order_by = 'nama_variant'; break;
			case 3:
					$order_by = 'barcode'; break;
			case 4:
					$order_by = 'stock_butik'; break;
			case 5:
					$order_by = 'lokasi_butik'; break;
			case 6:
					$order_by = 'stock_online'; break;
			case 7:
					$order_by = 'lokasi_online'; break;
			case 8:
					$order_by = 'stock_bazar_a'; break;
			case 9:
					$order_by = 'stock_bazar_a'; break;
			case 10:
					$order_by = 'stock_bazar_a'; break;
			case 0:
			default:
					$order_by = 'id_produk'; break;
		}
		$order_as = $_POST['order'][0]['dir'];
		switch ($order_as) {
			case 'desc': $order_as = 'desc'; break;
			case 'asc':
			default: $order_as = 'asc'; break;
		}
		$q = 'SELECT COUNT(id_variant) FROM (SELECT id_variant FROM produk_inventory INNER JOIN '.
			'produk_variant ON id_variant = produk_variant.id INNER JOIN produk ON produk_variant.id_produk'.
			' = produk.id GROUP BY id_variant) AS tbl_inventory;';
		$db['query'] = $db['con'] -> prepare($q);
		$db['query'] -> execute();
		$db['res'] = $db['query'] -> fetchAll();
		$rTotal = $db['res'][0][0];
			//TODO: gonna use view instead of manual query
		$q = 'SELECT produk.id AS id_produk, produk.nama_produk, id_variant, '.
				'produk_variant.nama AS nama_variant, produk_variant.barcode, '.
				/*'JSON_UNQUOTE(JSON_EXTRACT(produk_variant.lokasi, \'$."'.ID_ONLINE.'"\')) AS lokasi_online, '.
				'JSON_UNQUOTE(JSON_EXTRACT(produk_variant.lokasi, \'$."'.ID_BUTIK.'"\')) AS lokasi_butik, '.*/
				'produk_variant.lokasi ->> \'$."'.ID_ONLINE.'"\' AS lokasi_online, '.
				'produk_variant.lokasi ->> \'$."'.ID_BUTIK.'"\' AS lokasi_butik, '.
				'SUM(IF(id_toko = '.ID_ONLINE.', IF(act = \'IN\', qty, -qty), 0)) AS stock_online, '.
				'SUM(IF(id_toko = '.ID_BUTIK.', IF(act = \'IN\', qty, -qty), 0)) AS stock_butik, '.
				'SUM(IF(id_toko = '.ID_BAZAR_A.', IF(act = \'IN\', qty, -qty), 0)) AS stock_bazar_a, '.
				'SUM(IF(id_toko = '.ID_BAZAR_B.', IF(act = \'IN\', qty, -qty), 0)) AS stock_bazar_b, '.
				'SUM(IF(id_toko = '.ID_BAZAR_C.', IF(act = \'IN\', qty, -qty), 0)) AS stock_bazar_c, '.
				'SUM(IF(act = \'IN\', qty, -qty)) AS total '.
				'FROM produk_inventory INNER JOIN produk_variant ON id_variant = produk_variant.id '.
				'INNER JOIN produk ON produk_variant.id_produk = produk.id '.
				'WHERE produk.id LIKE :search OR produk.nama_produk LIKE :search OR produk_variant.nama LIKE :search OR '.
				'produk_variant.barcode LIKE :search OR LCASE(produk_variant.lokasi ->> \'$."'.ID_ONLINE.'"\') '.
				'LIKE :search OR LCASE(produk_variant.lokasi ->> \'$."'.ID_BUTIK.'"\') LIKE :search '.
				'GROUP BY id_variant ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length;
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute(array(
				':search' => '%'. strtolower($search) .'%'
		))) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		$data = array();
		foreach ($db['res'] as $row) {
			array_push($data, array(
				//'DT_RowId' => 'row_'.$row['id'],
				'DT_RowAttr' => array(
						'data-id_produk' => $row['id_produk'],
						'data-id_variant' => $row['id_variant']
				),
				'id_produk' => out($row['id_produk']),
				'nama_produk' => out($row['nama_produk']),
				'nama_variant' => out($row['nama_variant']),
				'barcode' => $row['barcode'],
				'stock_butik' => $row['stock_butik'],
				'lokasi_butik' => $row['lokasi_butik'],
				'stock_online' => $row['stock_online'],
				'lokasi_online' => $row['lokasi_online'],
				'stock_bazar_a' => $row['stock_bazar_a'],
				'stock_bazar_b' => $row['stock_bazar_b'],
				'stock_bazar_c' => $row['stock_bazar_c'],
				'total' => $row['total']
			));
		}
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
	default:
			errorInvalid();
	}
} else {
	errorInvalid();
}

?>
