<?php
	if (!isset($nodirect)) die('nope');

	$title = 'Produk Variasi';

	if (!filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) > 0) {
		?>
	<script>
		location.href = "<?php echo BASE_URL; ?>products/";
	</script>
		<?php
	}
	$id = $_GET['id'];

?>

<div class="page">
	<h4>
		<?php echo $title; ?>
	</h4>
	<form action="" id="editor">
	<div class="page-50">
		<table id="viewer">
			<thead>
				<th style="width: 10%">Barcode</th>
				<th style="width: 25%">Varian</th>
				<th style="width: 25%">Butik - Lokasi</th>
				<th style="width: 25%">Online - Lokasi</th>
				<th style="width: 15%"></th>
			</thead>
			<tbody>
			</tbody>
    </table>
	</div><div class="page-10"></div><!--
 --><div class="page-40">
		<button class="btn2 btn-big" type="button" id="btn-add" onClick='add();'>Tambah Varian</button>
		<div class="page-inside">
			<table class="commonTable">
				<tr><th id="editor-state">Tambah</th></tr>
				<tr>
					<td>Barcode</td>
					<td id="barcode">-</td>
				</tr>
				<tr>
					<td>Nama Varian *</td>
					<td><input type="text" name="nama" minlength="<?php echo INPUT_VARIANT_NAMA_MIN; ?>" maxlength="<?php echo INPUT_VARIANT_NAMA_MAX; ?>" tabindex="1" required></td>
				</tr>
			</table><br>
			<table class="commonTable">
				<tr>
					<th>Lokasi</th><td></td>
					<th colspan="2">Jumlah Stok Awal</th>
				</tr>
				<tr>
					<td style="width: 25%">Butik</td>
					<td style="width: 35%"><input type="text" name="lokasi_butik" maxlength="<?php echo INPUT_VARIANT_LOKASI_MAX; ?>" tabindex="2"></td>
					<td style="width: 15%">Butik</td>
					<td style="width: 25%"><input type="number" min="0" maxlength="<?php echo INPUT_STOCK_MAX; ?>" name="stock_butik" tabindex="4" required> Pcs</td>
				</tr>
				<tr>
					<td>Online</td>
					<td><input type="text" name="lokasi_online" maxlength="<?php echo INPUT_VARIANT_LOKASI_MAX; ?>" tabindex="3"></td>
					<td>Online</td>
					<td><input type="number" min="0" maxlength="<?php echo INPUT_STOCK_MAX; ?>" name="stock_online" tabindex="5" required> Pcs</td>
				</tr>
				<tr>
					<td></td><td></td>
					<td>Bazar A</td>
					<td><input type="number" min="0" maxlength="<?php echo INPUT_STOCK_MAX; ?>" name="stock_bazar_a" tabindex="6" required> Pcs</td>
				</tr>
				<tr>
					<td></td><td></td>
					<td>Bazar B</td>
					<td><input type="number" min="0" maxlength="<?php echo INPUT_STOCK_MAX; ?>" name="stock_bazar_b" tabindex="7" required> Pcs</td>
				</tr>
				<tr>
					<td><input type="hidden" name="id" value="0"></td><td><input type="hidden" name="id_produk" value="<?php echo $id; ?>"></td>
					<td>Bazar C</td>
					<td><input type="number" min="0" maxlength="<?php echo INPUT_STOCK_MAX; ?>" name="stock_bazar_c" tabindex="8" required> Pcs</td>
				</tr>
			</table>
		</div>

	</div>
	<div class="bottom" style="float: right;">
			<a href="<?php echo BASE_URL; ?>products/edit/<?php echo $id; ?>"><button type="button" class="btn">
					&laquo; Batal
			</button></a>
			<button class="btn" type="submit" id="btn-submit">
					Simpan
			</button>
	</div>
	<div style="clear: both;"></div>
	</form>
</div>

<style>
	#viewer tr td:nth-child(3), #viewer tr td:nth-child(4) {
		/*nope*/
	}

	.commonTable tr:first-child {
		border-top: 0;
	}
	.commonTable tr:last-child {
		border-bottom: 0;
	}
	.commonTable:nth-child(3) tr td:nth-child(2) input {
		min-width: 100px; width: 60%;
	}
	.commonTable:nth-child(3) tr td:nth-child(4) input {
		min-width: 60px; width: 50%;
	}
</style>

<script>

//state 0: tambah, 1: edit
let editorState = 0;


function edit(id) {
	editorState = 1;
	$('#editor-state').html('Edit');
	$('.commonTable:nth-child(3) tr th:nth-child(3)').css('visibility', 'hidden');
	$('.commonTable:nth-child(3) tr td:nth-child(3)').css('visibility', 'hidden');
	$('.commonTable:nth-child(3) tr td:nth-child(4)').css('visibility', 'hidden');
	$('.commonTable:nth-child(3) tr td:nth-child(4) input').attr('required', false);
	$.ajax({
		url: '<?php echo BASE_URL; ?>api.php?data=variant&act=fetch',
		method: 'POST',
		dataType: 'json',
		data: {id: id},
		success: function(data) {
			if (data.status == "error") {
				showAlert('', data.desc);
				console.log(data.msg);
			} else if (data.status == "success") {
				$('input[name="nama"]').val(data.nama);
				$('input[name="id"]').val(id);
				$('input[name="lokasi_butik"]').val(data.lokasi_butik);
				$('input[name="lokasi_online"]').val(data.lokasi_online);
				$('#barcode').html(data.barcode);
			}
		},
		error: function(xhr, status) {}
	});
}
function add() {
	editorState = 0;
	$('#editor-state').html('Tambah');
	$('.commonTable:nth-child(3) tr th:nth-child(3)').css('visibility', 'visible');
	$('.commonTable:nth-child(3) tr td:nth-child(3)').css('visibility', 'visible');
	$('.commonTable:nth-child(3) tr td:nth-child(4)').css('visibility', 'visible');
	$('.commonTable:nth-child(3) tr td:nth-child(4) input').attr('required', true);
	$('#editor').trigger('reset');
	$('#barcode').html('-');
	$('input[name="id_produk"]').val('<?php echo $id; ?>');
}

function del(id) {
	showConfirm('', 'Anda yakin ingin menghapus Variant ini?');
    $('#btn-yes').click(function() {
			$.ajax({
				url: '<?php echo BASE_URL; ?>api.php?data=variant&act=del',
				method: 'POST',
				dataType: 'json',
				data: {'id': id},
				success: function(data) {
					if (data.status == "error") {
						showAlert('', data.desc);
						console.log(data.msg);
					}
					else if (data.status == "success") {
						showAlert('', 'Variant telah dihapus!');
						$('#viewer').DataTable().ajax.reload();
						$('#modal-public').on('modal:after-close', function(ev, modal) {
								$.modal.close();
						});
					}
				},
				error: function(xhr, status) {}
			});
    });
    $('#btn-no').click(function() {
			$.modal.close();
    });
}

$(document).ready(function() {
	$('.menu > ol > li:nth-child(3)').addClass('menu-active');
	$('.menu > ol > li:nth-child(3) li:nth-child(1)').addClass('menu-active');

	let viewer = $('#viewer').dataTable({
		'language': {
				'info' : ' ',
				'infoEmpty': ' ',
				'emptyTable': 'Varian produk masih kosong'
		},
		'serverSide': true,
		'ajax': {
			'url': '<?php echo BASE_URL; ?>api.php?data=variant&act=dt',
			'type': 'POST',
			'data': {'id_produk': <?php echo $id; ?>}
		},
		'columns': [
			{'data': 'barcode'},
			{'data': 'nama'},
			{'data': 'lokasi_butik'},
			{'data': 'lokasi_online'},
			{
				'data': 'id', 'render': function(data, type, row, meta) {
					let elPrint = '<img src="<?php echo BASE_URL.PATH_IMG; ?>print.svg" onClick="print('+ data +')";>';
					let elEdit = '<img src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg" onClick="edit('+ data +');">';
					let elDel = '<img src="<?php echo BASE_URL.PATH_IMG; ?>cross.svg" onClick="del('+ data +');">';
					//console.log(data); console.log(type); console.log(row); console.log(meta);
					//$('#user tr:nth-child(' + (meta.row + 1) + ') td:nth-child(' + (meta.col + 1) + ')').attr('data-id');
					return elPrint +' '+ elEdit +' '+ elDel;
				}
			}
		],
		'ordering': false,
		'paging': false,
		'searching': false
	});

	$('#editor').submit(function(ev) {
		ev.preventDefault();
		let url, submit;
		submit = $('#editor').serializeArray();
		if (editorState == 0) {
			url = '<?php echo BASE_URL; ?>api.php?data=variant&act=add';
			function cbSuccess() {
				showAlert('', 'Varian baru berhasil ditambahkan!');
				add();
				$('#viewer').DataTable().ajax.reload();
			}
		} else if (editorState == 1) {
			url = '<?php echo BASE_URL; ?>api.php?data=variant&act=edit';
			function cbSuccess() {
				showAlert('', 'Varian berhasil diubah!');
				add();
				$('#viewer').DataTable().ajax.reload();
			}
		}
		$.ajax({
			url: url,
			method: 'POST',
			data: submit,
			dataType: 'json',
			success: function(data, status) {
				if (data.status == 'success') {
					cbSuccess();
				} else if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
			}, error: function(xhr, status) {

			}
		});
	});


});

</script>
