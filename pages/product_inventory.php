<?php
    if (!isset($nodirect)) die('nope');
?>

<div class="page">
	<h2>
		<img src="<?php echo BASE_URL.PATH_IMG; ?>T-Shirt.svg" alt="">
		Daftar Inventory
	</h2>
	<div class="nav-inside">
		<div class="right">
			<a href="<?php echo BASE_URL; ?>product_inventory/x"><div class="btn2">X</div></a>
			<a href="<?php echo BASE_URL; ?>product_inventory/bulk"><div class="btn2 btn-big">Mass Upload Inventory</div></a>
			<a href="<?php echo BASE_URL; ?>product_inventory/transfer"><div class="btn2 btn-big">Transfer</div></a>
			<a href="<?php echo BASE_URL; ?>product_inventory/edit"><div class="btn2 btn-big">Tambah / Kurangi Inventory</div></a>
		</div>
	</div>
	<table id="viewer">
		<thead>
			<tr>
				<th style="width: 6%" rowspan="2">ID Produk</th>
				<th style="width: 20%" rowspan="2">Nama Produk</th>
				<th style="width: 7%" rowspan="2">Varian</th>
				<th style="width: 7%" rowspan="2">Barcode</th>
				<th style="width: 14%" colspan="2">Butik</th>
				<th style="width: 14%" colspan="2">Online</th>
				<th style="width: 20%" colspan="3">Bazar</th>
				<th style="width: 6%" rowspan="2">Total</th>
				<th style="width: 6%" rowspan="2">Aksi</th>
			</tr>
			<tr>
				<th>Stok</th>
				<th>Lokasi</th>
				<th>Stok</th>
				<th>Lokasi</th>
				<th>Bazar A</th>
				<th>Bazar B</th>
				<th>Bazar C</th>
			</tr>
		</thead>
			<tbody>
			</tbody>
	</table>

</div>

<style>
    #viewer tr td {
        text-align: left;
    }
    #viewer tr td:nth-child(n+5) {
        text-align: center;
    }
</style>

<script>
$(document).ready(function() {
	$('.menu > ol > li:nth-child(3)').addClass('menu-active');
	$('.menu > ol li:nth-child(3) li:nth-child(2)').addClass('menu-active');
	let viewer = $('#viewer').dataTable({
		//dom: '<"top"lf>tp<"result"i><"pagination"p>'
		'language': {
			'lengthMenu': 'Tampilkan _MENU_ Inventory Produk',
			'search': '',
			'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Inventory Produk',
			'infoEmpty': '',
			'infoFiltered': '',
			'emptyTable': 'Tidak ada Inventory Produk yang dapat ditampilkan',
			'zeroRecords': 'Pencarian tidak ditemukan',
			'paginate': {
					'next': '&raquo;',
					'previous': '&laquo;'
			}
		},
		'serverSide': true,
		'ajax': {
			'url': '<?php echo BASE_URL; ?>api.php?data=product_inventory&act=dt',
			'type': 'POST'
		},
		'columns': [
			{'data': 'id_produk'},
			{'data': 'nama_produk'},
			{'data': 'nama_variant'},
			{'data': 'barcode'},
			{'data': 'stock_butik'},
			{'data': 'lokasi_butik'},
			{'data': 'stock_online'},
			{'data': 'lokasi_online'},
			{'data': 'stock_bazar_a'},
			{'data': 'stock_bazar_b'},
			{'data': 'stock_bazar_c'},
			{'data': 'total', 'orderable': false},
			{
				'data': null, 'orderable': false,
				'searchable': 'false', 'render': function(data, type, row, meta) {
						//console.log(data);
						//console.log(type);
						//console.log(row.DT_RowAttr['data-id_produk']);
						//console.log(meta);
						let elEdit = '<a href="<?php echo BASE_URL; ?>product_inventory/act/"><img src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg"></a>';
						let elDetail = '<a href="<?php echo BASE_URL; ?>product_inventory/detail/"><img src="<?php echo BASE_URL.PATH_IMG; ?>search-1.svg"></a>';
						return elEdit + ' ' + elDetail;
				}
			}
		],
		'order': [[1, 'asc']],
	});
});
</script>
