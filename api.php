<?php
	$nodirect = true;
	include 'init.php';

	header('Content-Type: application/json');

	function send($array) {
			die(json_encode($array));
	}

	function errorInvalid() {
			send(array(
					'status' => 'error',
					'desc' => 'Invalid Request Data'
			));
	}
	function errorLength() {
			send(array(
					'status' => 'error',
					'desc' => 'Data length doesn\'t meet requirement'
			));
	}
	function errorQuery($msg) {
			send(array(
					'status' => 'error',
					'desc' => 'Query Error',
					'msg' => $msg
			));
	}
	function errorIDNotFound() {
			send(array(
					'status' => 'error',
					'desc' => 'ID Not Found'
			));
	}

	function logError($msg, $error) {
			global $cfg;
			$logger = fopen($cfg['path']['error'].date('d-m-Y').'.txt', 'a');
			fwrite($logger, '['.date('H:i:s').'] '.$msg.PHP_EOL.$error.PHP_EOL);
			fclose($logger);
	}

	function logInfo($msg) {
			global $cfg;
			$logger = fopen($cfg['path']['log'].date('d-m-Y').'.txt', 'a');
			fwrite($logger, '['.date('H:i:s').'] '.$msg.PHP_EOL);
			fclose($logger);
	}

	function validLen($str, $min, $max) {
			if ( (strlen($str) >= $min) && (strlen($str) <= $max) ) {
					return true;
			} else return false;
	}

	// May w.i.p, so complicate
	/*function updateStock($id_variant, $toko, $qty) {
		if (is_array($id_variant)) {
			if ($toko == null) {

			} elseif (is_array($toko)) {
				$q = 'UPDATE produk_variant SET produk_variant.stock = JSON_SET(produk_variant, \'$."'.
					$toko[].'"\' )';
			}
		} elseif (is_int($id_variant)) {
			if ($toko == null) {

			} else {
				$q = 'UPDATE produk_variant SET produk_variant.stock = JSON_SET(produk_variant, \'$."'.
					$toko.'"\' )';
			}
			$db['query_ex'] = $db['con'] -> prepare($q);
			if (!($db['query3'] -> execute([
				':id_variant' => $key
			]))) errorQuery($db['query_ex'] -> errorInfo());
		}
	}*/

	if (isset($_GET['data'])) {
		switch (strtolower($_GET['data'])) {
			case 'admins':
				include 'apis/admin.php'; break;
			case 'stats':
				include 'apis/stats.php'; break;
			case 'sales':
				include 'apis/sales.php'; break;
			case 'buku_kas':
				include 'apis/cashbook.php'; break;
			case 'spg':
				include 'apis/spg.php'; break;
			case 'product_group':
				include 'apis/product_group.php'; break;
			case 'product_inventory':
				include 'apis/product_inventory.php'; break;
			case 'product':
				include 'apis/products.php'; break;
			case 'variant':
				include 'apis/variant.php'; break;
			default:
			errorInvalid();
		}
	}

?>
