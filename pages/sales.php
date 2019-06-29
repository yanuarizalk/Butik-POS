<?php
	if (!isset($nodirect)) die('nope');

	$db['query'] = $db['con'] -> prepare('SELECT SUM(total_sale) AS total FROM sales');
	$total = 0;
	if ($db['query'] -> execute()) 
		$total = $db['query'] -> fetchAll()[0][0];
	
?>

<div class="page">
	<h2>
		<img src="<?php echo BASE_URL.PATH_IMG; ?>shop.svg" alt="">
		Penjualan
		<div class="right text-blue">
			<label class="text-info"id="akumulasi">Rp 0,00</label>
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

<div id="modal-detail" class="modal modal-big">
<div class="modal-head">
		<h4>Detail Transaksi Penjualan</h4>
</div>
<div class="modal-text">
	<div class="page-45">
		<table class="commonTable" id="desc">
			<tr>
					<td style="width: 50%;">Tanggal Transaksi</td>
					<td style="width: 50%;" id="dt">
							<!--<input type="text" name="dt" style="min-width: 75px; width: 100px; text-align: right;" readonly>-->
					</td>
			</tr>
			<tr>
					<td>Toko</td>
					<td id="toko">
						<!--<input type="text" name="toko" readonly>-->
					</td>
			</tr>
			<tr>
					<td>SPG</td>
					<td id="spg">
						<!--<input type="text" name="spg" readonly>-->
					</td>
			</tr>
			<tr>
					<td>Nama Konsumen</td>
					<td id="konsumen">
							<!--<input type="text" name="nama_konsumen" style="min-width: 150px; width: 150px;" readonly>-->
					</td>
			</tr>
			<tr>
				<td>Member</td>
				<td id="member">
					<!--<input type="text" name="member" readonly>-->
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td id="keterangan">
					<!--<textarea name="keterangan" cols="30" rows="2" readonly></textarea>-->
				</td>
			</tr>
			<tr>
				<td>Pembayaran</td>
				<td id="payment"></td>
			</tr>
			<tr>
				<td style="width: 30%;">Total Penjualan</td>
				<td style="width: 70%;" id="total_sale"></td>
			</tr>
		</table>
	</div><div class="page-5">
	</div><div class="page-50">
		<table class="preTable" id="listItem">
			<tr><td class="header" colspan="4">
				Item(s)
			</td></tr>
			<tr>
				<th width="55%">Nama Produk - Varian</th>
				<th width="20%">Jumlah</th>
				<th width="20%">Diskon</th>
				<th width="5%"></th>
			</tr>
		</table><br>
		<table class="preTable" id="transaksi">
			<tr><td class="header" colspan="4">
				Transaksi
			</td></tr>
			<tr>
				<th width="5%"></th>
				<th width="25%">Buku Kas</th>
				<th width="35%">Keterangan</th>
				<th width="35%">Nominal</th>
			</tr>
			<tr>
				<th colspan="3"> Total Transaksi </th>
				<th id="total_transaksi"></th>
			</tr>
		</table>
	</div>
</div>
<div class="modal-foot" style="text-align: left;">
	<div class="bottom">
		<a href="#0" rel="modal:close"><button type="button" class="btn">Kembali</button></a>
		<div class="right">
			<button class="btn" id="btn-detail-hapus" type="button" disabled>Hapus</button>
			<button class="btn" id="btn-detail-print" type="button">Print</button>
		</div>
	</div>
</div>
</div>

<style>
	#viewer tr td:nth-child(2), #viewer tr td:nth-child(8) {
		text-align: center;
	}
	#viewer tr td:nth-child(6), #viewer tr td:nth-child(7) {
		text-align: right;
	}
	#viewer tbody tr:hover {
		background-color: rgb(250, 250, 250);
		cursor: pointer;
	}
	#desc tr td{
		padding: 10px 0;
	}
	#listItem tr td:nth-child(n+2) {
		text-align: center;
	}
	#transaksi tr td:nth-child(4) {
		text-align: right;
	}
	#transaksi tr td img {
		cursor: default;
	}
	#transaksi tr:last-child th {
		text-align: right;
	}
</style>

<script>
$(document).ready(function() {
	$('.menu > ol > li:nth-child(2)').addClass('menu-active');
	
	$('#akumulasi').html('&#9654; Rp '+ formatDecToCurrency(<?php echo $total; ?>) +' /<sub>PCS</sub>');
	
	$(document).on('click', '#viewer tbody tr', function() {
		let id = $(this).data('id');
		$.ajax({
			'url': '<?php echo BASE_URL; ?>api.php?data=sales&act=detail-fetch',
			'method': 'POST',
			'data': {'id': parseInt(id)},
			'success': function(data) {
				if (data.status == 'success') {
					$('#dt').html(data.result.dt);
					$('#toko').html(data.result.toko);
					$('#spg').html(data.result.nama_spg);
					$('#konsumen').html(data.result.nama_konsumen);
					$('#member').html(data.result.nama_member);
					$('#keterangan').html(data.result.keterangan);
					$('#payment').html(data.result.payment);
					$('#total_sale').html('Rp '+ formatDecToCurrency(data.result.total_sale));
					$('#total_transaksi').html('Rp '+ formatDecToCurrency(data.result.total_transaksi));
					$('#listItem tr:nth-child(n+3)').remove();
					let foot = $('#transaksi tr:last-child').clone();
					$('#transaksi tr:nth-child(n+3)').remove();
					$('#transaksi').append(foot);
					for (let iFor in data.result.items) {
						let detail = '<a target="_blank" href="<?php echo BASE_URL?>products/variant/'+ data.result.items[iFor][0] +'">'+
								'<img src="<?php echo BASE_URL.PATH_IMG; ?>search.svg" width="12px" ></a>';
						let row = '<td>'+ data.result.items[iFor][3] +'</td>'+
								'<td>'+ data.result.items[iFor][1] +' Pcs</td>'+
								'<td>'+ data.result.items[iFor][2] +'%</td>'+
								'<td>'+ detail +'</td>';
						$('#listItem').append('<tr>'+ row +'</tr>');
					}
					for (let iFor in data.result.transaksi) {
						let state = data.result.transaksi[iFor][3] == 'IN' ? 
								'<img src="<?php echo BASE_URL.PATH_IMG; ?>plus.svg" width="12px" >' :
								'<img src="<?php echo BASE_URL.PATH_IMG; ?>minus.svg" width="12px" >';
						let row = '<td>'+ state +'</td>'+
								'<td>'+ data.result.transaksi[iFor][0] +'</td>'+
								'<td>'+ data.result.transaksi[iFor][1] +'</td>'+
								'<td>Rp '+ formatDecToCurrency(data.result.transaksi[iFor][2]) +'</td>';
						$('<tr>'+ row +'</tr>').insertBefore('#transaksi tr:last-child');
					}
					$('#modal-detail').modal({
						showClose: false,
						blockerClass: 'nope'
					});
				}
			}
		});
	});
	
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
