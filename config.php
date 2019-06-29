<?php
	if (!isset($nodirect)) die('nope');

	date_default_timezone_set('Asia/Jakarta');

	define('DB_HOST', 'localhost');
	define('DB_PORT', '3306');
	define('DB_NAME', 'pos_new');
	define('DB_USER', 'gravis');
	define('DB_PASS', 'freeyourmind');

//Development
	define('BASE_URL', 'http://yanz.dev.com:8080/POS/');
	define('DB_OPTIONS', [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING ]);
	error_reporting(E_ALL);

//Production
	# define('BASE_URL', 'https://yanz.dev.com/');
	# define('DB_OPTIONS', []);
	# error_reporting(E_ALL ^ E_WARNING)
// ra recommend
	# error_reporting(0)

	define('PATH_JS', 'assets/js/');
	define('PATH_CSS', 'assets/css/');
	define('PATH_IMG', 'assets/img/');
	define('PATH_IMG_PRODUCT', 'assets/img-products/');
	define('PATH_LOG', 'assets/logs/');
	define('PATH_ERROR', 'assets/logs/errors/');

	define('KEY_AUTH', '_damntrain}');

	define('ID_ONLINE', 1);
	define('ID_BUTIK', 2);
	define('ID_BAZAR_A', 3);
	define('ID_BAZAR_B', 4);
	define('ID_BAZAR_C', 5);

// Character Length Rule
	define('INPUT_EMAIL_MIN', 5);
	define('INPUT_EMAIL_MAX', 200);
	define('INPUT_USER_NAMA_MIN', 3);
	define('INPUT_USER_NAMA_MAX', 200);
	define('INPUT_USER_PASS_MIN', 5);
	define('INPUT_USER_PASS_MAX', 200);
	define('INPUT_USER_KETERANGAN_MAX', 200);
	define('INPUT_CASHBOOK_NAMA_MIN', 3);
	define('INPUT_CASHBOOK_NAMA_MAX', 200);
	define('INPUT_CASHBOOK_SALDO_AWAL_MIN', 1);
	define('INPUT_CASHBOOK_SALDO_AWAL_MAX', 20);
	define('INPUT_CASHBOOK_KETERANGAN_MAX', 200);
	define('INPUT_SPG_NAMA_MIN', 3);
	define('INPUT_SPG_NAMA_MAX', 200);
	define('INPUT_SPG_NOHP_MAX', 14);
	define('INPUT_SPG_KETERANGAN_MAX', 200);
	define('INPUT_PRODUK_NAMA_PRODUK_MIN', 2);
	define('INPUT_PRODUK_NAMA_PRODUK_MAX', 200);
	define('INPUT_PRODUK_NAMA_STRUK_MIN', 2);
	define('INPUT_PRODUK_NAMA_STRUK_MAX', 200);
	define('INPUT_PRODUK_KETERANGAN_MAX', 200);
	/*define('INPUT_PRODUK_HARGA_POKOK_MIN', 1);
	define('INPUT_PRODUK_HARGA_POKOK_MAX', 20);*/
	define('INPUT_PRODUK_HARGA_MIN', 1);
	define('INPUT_PRODUK_HARGA_MAX', 20);
	define('INPUT_PRODUK_IMG_SIZE_MAX', 5242880); //5mb, itungane byte
	define('INPUT_VARIANT_NAMA_MIN', 3);
	define('INPUT_VARIANT_NAMA_MAX', 200);
	define('INPUT_VARIANT_LOKASI_MAX', 200);
	define('INPUT_STOCK_MIN', 1);
	define('INPUT_STOCK_MAX', 8);
// TODO: clear fix kabeh INPUT & validasi
	define('INPUT_DATE_MIN', 5);
	define('INPUT_DATE_MAX', 30); //i think, it was useless
	define('INPUT_TIME_MIN', 1);	//felt used, might delete later idk
	define('INPUT_TIME_MAX', 2);
	define('INPUT_KETERANGAN_MAX', 200);

	define('INPUT_GROUP_NAMA_MAX', 200);
	define('INPUT_NAMA_KONSUMEN_MAX', 200);

	define('BARCODE_LENGTH', 6);
	// percent
	define('CHARGE_CC', 2);

// TODO: Nambah Internalization, i18n

?>
