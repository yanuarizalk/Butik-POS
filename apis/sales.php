<?php
if (!isset($nodirect)) die('nope');
//die(var_dump($_REQUEST));
if (isset($_GET['act'])) {
	switch (strtolower($_GET['act'])) {
	case 'new':
		if (!isset(
			$_POST['nama_konsumen'], $_POST['qty'],
			$_POST['jam'], $_POST['menit'],
			$_POST['diskon'], $_POST['tanggal'],
			$_POST['keterangan'], $_POST['voucher'],
			$_POST['diskon_voucher'], $_POST['pay'],
			$_POST['member'], $_POST['spg'],
			$_POST['payment'], $_POST['transaksi']
		)) errorInvalid();
		$keterangan = trim($_POST['keterangan']);
		$nama_konsumen = trim($_POST['nama_konsumen']);
		$payment = trim($_POST['payment']);
		$voucher = trim($_POST['voucher']);
		$diskon_voucher = floatval($_POST['diskon_voucher']) ?: 0;
		$pay = floatval($_POST['pay']);
		$spg = intval($_POST['spg']);
		$member = intval($_POST['member']);
		//do this?
		$tanggal = strtotime($_POST['tanggal']) ?: errorInvalid();
		$jam = intval($_POST['jam']);
		$menit = intval($_POST['menit']);
		$qty = is_array($_POST['qty']) ? $_POST['qty'] : errorInvalid();
		$diskon = is_array($_POST['diskon']) ? $_POST['diskon'] : errorInvalid();
		$transaksi = intval($_POST['transaksi']) ?: errorInvalid();

		//or like this? benchmark plss
		/*if (($tanggal == false) ||
				(is_nan($jam)) || (is_nan($menit)) ||
				(is_array($qty) == false) || (is_nan($transaksi))
			 ) errorInvalid();*/

		if (!(($transaksi >= 1) && ($transaksi <= 5))) errorInvalid();


		if (!( (validLen($keterangan, 0, INPUT_KETERANGAN_MAX )) &&
					(validLen($nama_konsumen, 0, INPUT_NAMA_KONSUMEN_MAX )) &&
					(validLen($payment, 0, 5))
		)) errorLength();

		$q = 'SELECT harga_pp, harga_ecer, harga_grosir, id_group FROM produk '.
			'INNER JOIN produk_variant ON produk.id = produk_variant.id_produk '.
			'WHERE produk_variant.id = :id_variant';
		$db['query'] = $db['con'] -> prepare($q);

		$items = [];
		$total = 0; $min = 0; $plus = 0; $totalAll = 0;
		$discGrosir = 0; $discItem = 0;
		$ecer = []; $grosir = []; $sameGroup = [];
		$pokok = []; $prodGroup = [];

		foreach ($qty as $key => $val) {
			if (intval($val) <= 0) errorInvalid();
			if ( !((intval($diskon[$key]) >= 0) && (intval($diskon[$key]) <= 100) )) errorInvalid();
			if (!$db['query'] -> execute([
				':id_variant' => intval($key)
			])) errorQuery($db['query'] -> errorInfo());
			if ($db['query'] -> rowCount() <= 0) send([
				'status' => 'error',
				'desc' => 'Produk varian dengan id '.$key.' tidak ditemukan'
			]); //continue;
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);

			$prodGroup[$key] = $db['res'][0]['id_group'];
			$sameGroup[$prodGroup[$key]] = isset($sameGroup[$prodGroup[$key]]) ? $sameGroup[$prodGroup[$key]] + intval($val) : intval($val);
			$ecer[$key] = $db['res'][0]['harga_ecer'];
			$pokok[$key] = $db['res'][0]['harga_pp'];
			$grosir[$key] = isset($grosir[$key]) ?: json_decode($db['res'][0]['harga_grosir'], true);
			//id, qty, diskon! field: items on sales table
			array_push($items, [$key, $val, $diskon[$key]]);
		}
		//second loop sibling, gonna do calculating
		//just translate it from existing javascript on the left of ur workspace
		foreach ($qty as $key => $val) {
			if (intval($val) <= 0) errorInvalid();
			if ( !((intval($diskon[$key]) >= 0) && (intval($diskon[$key]) <= 100) )) errorInvalid();
			$curTotal = 0; $curSatuan = $ecer[$key];
			if ($grosir[$key] != []) {
			//loop it, if there is grocery price.
			//remember $key -> index of qty var.
				foreach ($grosir[$key] as $keyGrosir => $valGrosir) {
					if ($prodGroup[$key] != 0) {
						if (
							($sameGroup[$prodGroup[$key]] >= intval($valGrosir[0])) &&
							($sameGroup[$prodGroup[$key]] <= intval($valGrosir[1]))
						) {
							$discGrosir += $curSatuan - floatval($valGrosir[2]);
							$curSatuan = floatval($valGrosir[2]);
							break;
						}
						// ignore it, if the product doesn't have any group alias 0
					} else {
						if (
							($val >= intval($valGrosir[0])) &&
							($val <= intval($valGrosir[1]))
						) {
							$discGrosir += $curSatuan - floatval($valGrosir[2]);
							$curSatuan = floatval($valGrosir[2]);
							break;
						}
					}
				}
			}
			$curTotal = $curSatuan * intval($qty[$key]);
			$curDiskon = intval($diskon[$key]) / 100 * $curTotal;
			$discItem += $curDiskon;
			$total += $curTotal - $curDiskon;
		}

		$dt = $tanggal + (($menit * 60) + ($jam * 3600));
		$charge_cc = isset($_POST['charge_cc']) ? CHARGE_CC / 100 * $total : 0;
		$totalAll = $total + $charge_cc - $diskon_voucher;

		if ($pay < $totalAll) send([
			'status' => 'error',
			'desc' => 'Pembayaran kurang'
		]);

		$q = 'INSERT INTO sales VALUES(null, '.
			':transaksi, :id_spg, 0, :items, :dt, :nama_konsumen,'.
			' :keterangan, :diskon_voucher, :charge_cc, :pay, :payment, :total_sale, :total_tranc) ';
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute([
			':transaksi' => $transaksi,
			':id_spg' => $spg, ':nama_konsumen' => $nama_konsumen,
			':items' => json_encode($items, JSON_NUMERIC_CHECK), ':dt' => $dt,
			':keterangan' => $keterangan, ':diskon_voucher' => $diskon_voucher,
			':charge_cc' => CHARGE_CC,
			':pay' => $pay, ':payment' => $payment,
			':total_sale' => $total, ':total_tranc' => $totalAll
		])) errorQuery($db['query'] -> errorInfo());
		$lastId = $db['con'] -> lastInsertId();

		//new insert to kas_tranc
		$q = 'INSERT INTO kas_trans VALUES(null, '.
			':id_kas, :id_sales, :act, :uang, :keterangan'.
			')';
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute([
			':id_kas' => $transaksi, ':id_sales' => $lastId,
			':keterangan' => 'Penjualan Item',
			':act' => 'IN', ':uang' => floatval($total + $discGrosir + $discItem)
		])) errorQuery($db['query'] -> errorInfo());
		if ($discItem > 0)
			if (!$db['query'] -> execute([
				':id_kas' => $transaksi, ':id_sales' => $lastId,
				':keterangan' => 'Diskon Item',
				':act' => 'OUT', ':uang' => $discItem
			])) errorQuery($db['query'] -> errorInfo());
		if ($discGrosir > 0)
			if (!$db['query'] -> execute([
				':id_kas' => $transaksi, ':id_sales' => $lastId,
				':keterangan' => 'Potongan Grosir',
				':act' => 'OUT', ':uang' => $discGrosir
			])) errorQuery($db['query'] -> errorInfo());
		if ($charge_cc > 0)
			if (!$db['query'] -> execute([
				':id_kas' => $transaksi, ':id_sales' => $lastId,
				':keterangan' => 'Biaya tambahan',
				':act' => 'IN', ':uang' => $charge_cc
			])) errorQuery($db['query'] -> errorInfo());
		if ($diskon_voucher > 0)
			if (!$db['query'] -> execute([
				':id_kas' => $transaksi, ':id_sales' => $lastId,
				':keterangan' => 'Diskon Voucher',
				':act' => 'OUT', ':uang' => $diskon_voucher
			])) errorQuery($db['query'] -> errorInfo());
		//Preparing to update stock
		$db['query'] = $db['con'] -> prepare('INSERT INTO produk_inventory VALUES(null, :id_variant, :id_toko, :act, :qty, :dt, :keterangan) ');
		$q = 'UPDATE produk_variant '.
				'SET produk_variant.stock = JSON_SET(produk_variant.stock, \'$."'.
				$transaksi .'"\', '.
				'(SELECT SUM(IF(act = \'IN\', qty, -qty)) '.
				'AS total_stock FROM produk_inventory '.
				'WHERE id_variant = :id_variant AND id_toko = :id_toko GROUP BY id_variant)) '.
				'WHERE produk_variant.id = :id_variant';
		$db['query2'] = $db['con'] -> prepare($q);
		foreach ($qty as $key => $val) {
			if ($val <= 0) errorInvalid();
			if (!$db['query'] -> execute([
				':id_variant' => $key,
				':id_toko' => $transaksi,
				':act' => 'OUT',
				':qty' => $val,
				':dt' => $dt,
				':keterangan' => 'Penjualan'
			])) errorQuery($db['query'] -> errorInfo());

			if (!($db['query2'] -> execute([
				':id_variant' => $key,
				':id_toko' => $transaksi
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
					$order_by = 'id'; break;
			case 2:
					$order_by = 'id_toko'; break;
			case 3:
					$order_by = 'nama_konsumen'; break;
			case 5:
					$order_by = 'total_sale'; break;
			case 6:
					$order_by = 'total_transaksi'; break;
			case 7:
					$order_by = 'nama_bukukas'; break;
			case 0:
			default:
					$order_by = 'dt'; break;
		}
		$order_as = $_POST['order'][0]['dir'];
		switch ($order_as) {
			case 'desc': $order_as = 'desc'; break;
			case 'asc':
			default: $order_as = 'asc'; break;
		}
		$q = 'SELECT COUNT(*) AS total FROM sales;';
		$db['query'] = $db['con'] -> prepare($q);
		$db['query'] -> execute();
		$db['res'] = $db['query'] -> fetchAll();
		$rTotal = $db['res'][0][0];
		$variant = [];
		$q = 'SELECT sales.id, id_toko, items, dt, nama_konsumen, '.
				'total_sale, total_transaksi, kas.nama AS nama_bukukas '.
				'FROM sales LEFT JOIN kas ON id_toko = kas.id '.
				'WHERE sales.id LIKE :search OR kas.nama LIKE :search OR nama_konsumen LIKE :search '.
				'ORDER BY '.$order_by.' '.$order_as. ' LIMIT '.$start.', '.$length;
		$db['query'] = $db['con'] -> prepare($q);
		if (!$db['query'] -> execute(array(
				':search' => '%'. strtolower($search) .'%'
		))) errorQuery($db['query'] -> errorInfo());
		$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		$data = array();
		foreach ($db['res'] as $row) {
			switch ($row['id_toko']) {
				case ID_ONLINE: $row['id_toko'] = 'Online'; break;
				case ID_BUTIK: $row['id_toko'] = 'Butik'; break;
				case ID_BAZAR_A: $row['id_toko'] = 'Bazar A'; break;
				case ID_BAZAR_B: $row['id_toko'] = 'Bazar B'; break;
				case ID_BAZAR_C: $row['id_toko'] = 'Bazar C'; break;
			}
			$items = [];
			$q = 'SELECT produk.nama_produk, produk.nama_struk, '.
				'produk_variant.nama AS nama_variant FROM produk_variant INNER JOIN produk '.
				'ON id_produk = produk.id WHERE produk_variant.id = :id_variant';
			foreach (json_decode($row['items'], true) as $list) {
				$db['query'] = $db['con'] -> prepare($q);
				if (!$db['query'] -> execute(array(
						':id_variant' => $list[0]
				))) errorQuery($db['query'] -> errorInfo());
				if ($db['query'] -> rowCount() > 0) {
					$fetchItem = $db['query'] -> fetchAll(PDO::FETCH_ASSOC)[0];
					$items[] = [$fetchItem['nama_produk'].
						'('.$fetchItem['nama_variant'].')', $list[1], $list[2]];
				} else
					$items[] = ['<i>Produk telah dihapus</i>', $list[1], $list[2]];
			}
			array_push($data, array(
				//'DT_RowId' => 'row_'.$row['id'],
				'DT_RowAttr' => array(
						'data-id' => $row['id']
				),
				'id' => $row['id'],
				'toko' => $row['id_toko'],
				'tanggal' => date('d M Y H:i', $row['dt']),
				'client' => out($row['nama_konsumen']),
				'bukukas' => out($row['nama_bukukas']),
				'list_item' => json_encode($items),
				'penjualan' => $row['total_sale'],
				'total' => $row['total_transaksi']
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
	case 'detail-fetch':
		//errorInvalid();
		if (!isset($_POST['id']))
			errorInvalid();
		//not allowed for zero id too
		$id = intval($_POST['id']) ?: errorInvalid();
		$q = 'SELECT id_toko AS toko, spg.nama AS nama_spg, items, sales.dt, nama_konsumen, '.
			'sales.keterangan, payment, total_sale, total_transaksi FROM sales '.
			'LEFT JOIN spg ON id_spg = spg.id WHERE sales.id = :id_sales';
		$db['query'] = $db['con'] -> prepare($q);
		if (!($db['query']) -> execute([
			':id_sales' => $id
		])) errorQuery($db['query'] -> errorInfo());
		if ($db['query'] -> rowCount() <= 0) send([
			'status' => 'error',
			'desc' => 'Transaksi Penjualan tidak ditemukan'
		]);
		$resSales = $db['query'] -> fetchAll(PDO::FETCH_ASSOC)[0];
		$q = 'SELECT kas_trans.act, uang, kas_trans.keterangan, kas.nama FROM kas_trans INNER JOIN kas ON '.
			'id_kas = kas.id WHERE id_sales = :id_sales';
		$db['query'] = $db['con'] -> prepare($q);
		if (!($db['query']) -> execute([
			':id_sales' => $id
		])) errorQuery($db['query'] -> errorInfo());
		$transaksi = [];
		$resTrans = $db['query'] -> fetchAll(PDO::FETCH_ASSOC);
		foreach ($resTrans as $key => $val) {
			array_push($transaksi, [
				$val['nama'], $val['keterangan'],
				$val['uang'], $val['act']
			]);
		}
		$items = json_decode($resSales['items'], true);
		$q = 'SELECT produk.id, produk.nama_struk, '.
				'produk_variant.nama AS nama_variant FROM produk_variant INNER JOIN produk '.
				'ON id_produk = produk.id WHERE produk_variant.id = :id_variant';
		$db['query'] = $db['con'] -> prepare($q);
		foreach ($items as $key => $val) {
			if (!($db['query']) -> execute([
				':id_variant' => $val[0]
			])) errorQuery($db['query'] -> errorInfo());
			$db['res'] = $db['query'] -> fetchAll(PDO::FETCH_ASSOC)[0];
			$items[$key][3] = out($db['res']['nama_struk']).'('.out($db['res']['nama_variant']).')';
			$items[$key][0] = $db['res']['id'];
		}
		switch ($resSales['toko']) {
			case ID_ONLINE: $resSales['toko'] = 'Online'; break;
			case ID_BUTIK: $resSales['toko'] = 'Butik'; break;
			case ID_BAZAR_A: $resSales['toko'] = 'Bazar A'; break;
			case ID_BAZAR_B: $resSales['toko'] = 'Bazar B'; break;
			case ID_BAZAR_C: $resSales['toko'] = 'Bazar C'; break;
		}
		$result = $resSales;
		$result['nama_spg'] = out($result['nama_spg']);
		$result['nama_konsumen'] = out($result['nama_konsumen']);
		$result['keterangan'] = nlTo(out($result['keterangan']), '<br>');
		$result['dt'] = date('d M Y - H:i', $result['dt']);
		$result['transaksi'] = $transaksi;
		$result['items'] = $items;
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
