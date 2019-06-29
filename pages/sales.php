<?php
    if (!isset($nodirect)) die('nope');
?>

<div class="page">
	<h2>
		<img src="<?php echo BASE_URL.PATH_IMG; ?>shop.svg" alt="">
		Penjualan
		<div class="right text-blue">
			Rp
		</div>
	</h2>
	<div class="nav-inside">
		<select>
			<option value="0">Semua Penjualan</option>
		</select>
		<input type="text" name="tanggal" placeholder="Tanggal">
		<button class="btn">Go</button>
		<div class="right">
			<a href="<?php echo BASE_URL; ?>sales/x"><div class="btn2">X</div></a>
			<a href="<?php echo BASE_URL; ?>sales/new"><div class="btn2 btn-big">Transaksi Baru</div></a>
		</div>
	</div>
	<table id="viewer">
		<thead>
			<th style="width: 12%">Tanggal</th>
			<th style="width: 5%">ID</th>
			<th style="width: 12%">Toko</th>
			<th style="width: 12%">Klien</th>
			<th style="width: 19%">Nama/Deskripsi</th>
			<th style="width: 15%">Nilai Penjualan</th>
			<th style="width: 15%">Total Transaksi</th>
			<th style="width: 10%">Buku Kas</th>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div id="modal-detail" class="modal" style="min-width: 400px;">
<div class="modal-head">
		<h4>Detail Transaksi Penjualan</h4>
</div>
<div class="modal-text">
	<table class="commonTable">
		<tr>
				<td style="width: 30%;">Nama Konsumen</td>
				<td style="width: 70%;">
						<input type="text" name="nama" style="min-width: 150px; width: 150px;" minlength="<?php echo INPUT_CASHBOOK_NAMA_MIN; ?>" maxlength="<?php echo INPUT_CASHBOOK_NAMA_MAX; ?>" required>
				</td>
		</tr>
		<tr>
				<td>Ja</td>
				<td>
						<input type="text" name="keterangan" id="" maxlength="<?php echo INPUT_CASHBOOK_KETERANGAN_MAX; ?>">
				</td>
		</tr>
		<tr id="saldo_awal">
				<td>Saldo Awal *</td>
				<td>
						Rp. &nbsp; <input type="text" data-type="currency" name="saldo_awal" min="0" step="1" style="min-width: 75px; width: 100px; text-align: right;" minlength="<?php echo INPUT_CASHBOOK_SALDO_AWAL_MIN; ?>" maxlength="<?php echo INPUT_CASHBOOK_SALDO_AWAL_MAX; ?>" required>
				</td>
		</tr>
		<tr>
				<td>Akses User</td>
				<td id="access" style="max-height: 75px; overflow-y: scroll; display: block;">
				</td>
		</tr><input type="hidden" name="id">
	</table>
</div>
<div class="modal-foot">
	<a href="#0" rel="modal:close"><button type="button" class="btn">Kembali</button></a>
	<button class="btn" type="button">Hapus</button>
	<button class="btn" type="button">Print</button>
</div>
</div>

<style>
	#viewer tr td:nth-child(2), #viewer tr td:nth-child(8) {
		text-align: center;
	}
	#viewer tr td:nth-child(6), #viewer tr td:nth-child(7) {
		text-align: right;
	}
	#viewer tr:hover {
		background-color: rgb(250, 250, 250);
		cursor: pointer;
	}
</style>

<script>
$(document).ready(function() {
	$('.menu > ol > li:nth-child(2)').addClass('menu-active');
	let viewer = $('#viewer').dataTable({
		//dom: '<"top"lf>tp<"result"i><"pagination"p>'
		'language': {
			'lengthMenu': 'Tampilkan _MENU_ Penjualan',
			'search': '',
			'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Penjualan',
			'infoEmpty': '',
			'infoFiltered': '',
			'emptyTable': 'Tidak ada Penjualan',
			'zeroRecords': 'Pencarian tidak ditemukan',
			'paginate': {
					'next': '&raquo;',
					'previous': '&laquo;'
			}
		},
		'serverSide': true,
		'ajax': {
				'url': '<?php echo BASE_URL; ?>api.php?data=sales&act=dt',
				'type': 'POST'
		},
		'columns': [
			{'data': 'tanggal'},
			{'data': 'id'},
			{'data': 'toko'},
			{'data': 'client'},
			{
				'data': 'list_item',
				'orderable': false,
				'render': function(data) {
					data = JSON.parse(data);
					let items = '';
					for(let index in data) {
						items += '<li>' + data[index][0] + ' ' + data[index][1] + ' Pc(s)</li>';
					}
					return '<ul>'+ items +'</ul>';
				}
			},
			{
				'data': 'penjualan', 'render': function(data) {
					return 'Rp ' + formatDecToCurrency(data);
				}
			},
			{
					'data': 'total', 'render': function(data) {
						return 'Rp ' + formatDecToCurrency(data);
					}
			},
			{
					'data': 'bukukas'
			}
		],
		'order': [[0, 'asc']]
	});
});
</script>
