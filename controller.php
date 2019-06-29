<?php
	if (!isset($nodirect)) die('nope');

	//
	$path = "";
	if (isset($_GET['page'])) $path = explode('/', $_GET['page']);

	function view_menu() {
		if ($_SESSION['POS']['loggedin'] == true) {
			?>
			<li>
					<a href="<?php echo BASE_URL; ?>home">Home</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>sales">Penjualan</a>
			</li><!--
	--><li class="dropdown">
					<a href="#0">
							Produk Inventory</a>
					<ol class="dropdown-menu">
							<li><a href="<?php echo BASE_URL; ?>products">Produk</a></li>
							<li><a href="<?php echo BASE_URL; ?>product_inventory">Inventori</a></li>
							<li><a href="<?php echo BASE_URL; ?>product_group">Group</a></li>
					</ol>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>kas">Kas</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>member">Member</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>hapiut">Hapiut</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>inventory">Inventaris</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>report">Laporan</a>
			</li><!--
	--><li>
					<a href="<?php echo BASE_URL; ?>settings">Pengaturan</a>
			</li>

			<a href="<?php echo BASE_URL; ?>settings/users/edit/<?php echo $_SESSION['POS']['id']; ?>">
				<img src="<?php echo BASE_URL.PATH_IMG; ?>profiles/<?php echo $_SESSION['POS']['pic']; ?>.svg" width="24px" alt="">
			</a><!--
			--><a href="<?php echo BASE_URL; ?>logout">
				<img src="<?php echo BASE_URL.PATH_IMG; ?>power-sign.svg" width="24px" alt="">
			</a>
			<?php
		} else {
			?>
			<li>
					<a href="<?php echo BASE_URL; ?>login">Login</a>
			</li>
			<?php
		}
	}

	function view_error() {

	}

	function view_content() {
		global $cfg, $path;
		global $nodirect;
		global $db;
		/*if (page() == 'login') {
				include 'page/login.php';
		} elseif (page() == 'members') {
				include 'page/members.php';
		} elseif (page() == 'detail') {
				include 'page/detail.php';
		}*/
		$curPage;
		if ($_SESSION['POS']['loggedin'] == true) {
			if (isset($_GET['page'])) {
				$page = $_GET['page'];
				if (substr($page, strlen($page) - 1, 1) == '/')
					$page = substr($_GET['page'], 0, strlen($_GET['page']) - 1);
				switch (strtolower($page)) {
					case 'sales':
					case 'products':
					case 'product_inventory':
					case 'product_group':
					case 'kas':
					case 'member':
					case 'hapiut':
					case 'inventory':
					case 'report':
							$curPage = strtolower($page);
							break;
					case 'products/new':
					case 'products/edit':
							$curPage = 'product-edit';
							break;
					case 'products/variant':
							$curPage = 'variant';
							break;
					case 'product_inventory/edit':
							$curPage = 'product_inventory-edit';
							break;
					case 'product_inventory/bulk':
							$curPage = 'product_inventory-bulk';
							break;
					case 'product_inventory/transfer':
							$curPage = 'product_inventory-transfer';
							break;
					case '':
					case 'home':
							$curPage = 'home'; break;
					case 'sales/new':
							$curPage = 'sales-new'; break;
					case 'settings':
					case 'settings/users':
							$curPage = 'users'; break;
					case 'settings/users/add':
					case 'settings/users/edit':
							$curPage = 'user-edit'; break;
					case 'settings/cashbook':
							$curPage = 'bukukas'; break;
					case 'settings/spg':
							$curPage = 'spg'; break;
					case 'settings/category':
							$curPage = 'category'; break;
					case 'settings/hh':
					case 'settings/happyhours':
							$curPage = 'hh'; break;
					case 'settings/umum':
							$curPage = 'umum'; break;
					case 'logout':
						unset($_SESSION['POS']);
						?>
						<script>location.href = '<?php echo BASE_URL; ?>login/'</script>
						<?php
						break;
					default:
							$curPage = '404';
				}
			} else {
				$curPage = 'home';
			}
		} else {
			if (isset($_GET['page'])) {
				switch (strtolower($_GET['page'])) {
						case '':
						case 'signin':
						case 'login':
								$curPage = 'login'; break;
						/*case 'register':
						case 'signup':
								$curPage = 'register'; break;*/
						default:
								$curPage = '404';
				}
			} else {
				$curPage = 'login';
			}
		}

		include 'pages/'.$curPage.'.php';
	}
?>
