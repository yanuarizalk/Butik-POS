<?php
    if (!isset($nodirect)) die('nope');

    $title = "Transfer Inventory";

?>

<div class="page">
	<h2>
			<img src="<?php echo BASE_URL.PATH_IMG; ?>T-Shirt.svg" alt="">

			<?php echo $title; ?>

	</h2>

	<form action="" id="editor" style="margin-top: 20px;">
	<div class="page-40">
		<table class="commonTable">
			<tr>
				<td style="width: 25%;">Dari *</td>
				<td style="width: 75%;">
					<select name="id_toko_from" style="width: 50%; max-width: 170px;" required>
						<option value="" disabled selected> -- Pilih Toko -- </option>
						<option value="<?php echo ID_ONLINE; ?>">Online</option>
						<option value="<?php echo ID_BUTIK; ?>">Butik</option>
						<option value="<?php echo ID_BAZAR_A; ?>">Bazar A</option>
						<option value="<?php echo ID_BAZAR_B; ?>">Bazar B</option>
						<option value="<?php echo ID_BAZAR_C; ?>">Bazar C</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Menuju *</td>
				<td>
					<select name="id_toko_to" style="width: 50%; max-width: 170px;" required>
						<option value="" disabled selected> -- Pilih Toko -- </option>
						<option value="<?php echo ID_ONLINE; ?>">Online</option>
						<option value="<?php echo ID_BUTIK; ?>">Butik</option>
						<option value="<?php echo ID_BAZAR_A; ?>">Bazar A</option>
						<option value="<?php echo ID_BAZAR_B; ?>">Bazar B</option>
						<option value="<?php echo ID_BAZAR_C; ?>">Bazar C</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Tanggal *</td>
				<td>
					<input type="text" name="tanggal" required>
					<select name="jam" style="width: 50px;" required>
						<option value="" disabled> -- Jam -- </option>
					</select>
						<script>
							for (iFor = 0; iFor <= 23; iFor++) {
								$('select[name="jam"]').append('<option value="'+ iFor +'">'+ String(iFor).padStart(2, 0) +'</option>');
							}
						</script>
					<select name="menit" style="width: 50px;" required>
						<option value="" disabled> -- Menit -- </option>
					</select>
						<script>
							for (iFor = 0; iFor <= 59; iFor++) {
								$('select[name="menit"]').append('<option value="'+ iFor +'">'+ String(iFor).padStart(2, 0) +'</option>');
							}
						</script>
				</td>
			</tr>
			<tr>
				<td>Keterangan </td>
				<td><textarea name="keterangan" style="width: 100%; min-height: 60px;" maxlength="<?php echo INPUT_KETERANGAN_MAX; ?>" ></textarea></td>
			</tr>
		</table>
	</div><div class="page-5"></div><!--
 --><div class="page-55">
			<hr>
			<input type="text" name="scan" pattern="[0-9]{<?php echo '0,'.BARCODE_LENGTH; ?>}" maxlength="<?php echo BARCODE_LENGTH; ?>" placeholder="Scan Barcode">
			atau
			<input type="text" id="search" class="search" readonly placeholder="Pencarian Nama">
			<div class="box-search">
				<input type="text">
				<select size="8">

				</select>
			</div>
			<br> <br>
			<table class="preTable" id="preProduk">
				<tr><td class="header" colspan="4">
					Item Produk
				</td></tr>
				<tr>
					<th width="20%">ID Barcode</th>
					<th width="50%">Nama Produk/Varian</th>
					<th width="20%">Jumlah *</th>
					<th width="10%"></th>
				</tr>
				<tr><th colspan="4"> &nbsp; </th></tr>
			</table>

		</div>
		<div class="bottom" style="float: right;">
			<a href="<?php echo BASE_URL; ?>product_inventory"><button type="button" class="btn">
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
	#preProduk tr:nth-child(n+2) td {
		text-align: center;
	}
	#preProduk tr td input {
		max-width: 60px;
		text-align: right;
	}
</style>

<script>
let dp;

function minProduct(id) {
	$('#preProduk tr td input[name="qty['+ id +']"]').parent().parent().remove();
}

function addToList(data) {
	if ($('#preProduk tr td input[name="qty['+ data.id +']"]').length > 0 )
		return;
	let endTable = $('#preProduk tr:last-child');
	let printRow = '<tr>'+
			'<td>'+ data.barcode +'</td>'+
			'<td>'+ data.name +'</td>'+
			'<td><input type="number" min="1" name="qty['+
			data.id +']" id="qty" required> Pcs</td>'+
			'<td><img src="<?php echo BASE_URL.PATH_IMG; ?>minus.svg" onClick="minProduct('+
			data.id +')"></td>'
			+'</tr>';
	$(printRow).insertBefore(endTable);
	$('input[name="qty['+ data.id + ']"]').focus();
	$('input[name="scan"]').val('');
	curSearch.val('');
}

$(document).ready(function() {
	$('.menu > ol > li:nth-child(3)').addClass('menu-active');
	$('.menu > ol > li:nth-child(3) li:nth-child(2)').addClass('menu-active');
	dp = $('input[name="tanggal"]').datepicker({
		'language': 'en',
		'toggleSelected': false,
		'dateFormat': 'dd M yyyy',
		/*'maxDate': new Date(),*/
	});
	$('input[name="tanggal"]').datepicker().data('datepicker').selectDate(new Date());
	$('select[name="jam"]').val(new Date().getHours());
	$('select[name="menit"]').val(new Date().getMinutes());

	$('#editor').on('submit', function(ev) {
		ev.preventDefault();
		let submit = $(this).serializeArray();
		/*let dtParser = $('input[name="tanggal"]').datepicker().data('datepicker').selectedDates[0].getTime();
		dtParser = Math.floor(dtParser / 1000) + ((60 * $('select[name="menit"]').val()) * $('select[name="jam"]').val());
		submit.find(input => input.name == 'tanggal').value = dtParser;*/
		if (!$('#preProduk').has('#qty').length) {
			showAlert('', 'Mohon inputkan Produk yang ingin ditransfer');
			return;
		}
		if ($('select[name="id_toko_from"]').val() == $('select[name="id_toko_to"]').val()) {
			showAlert('', 'Transfer Inventory tidak dapat dilakukan<br>pada Toko yang sama');
			return;
		}
		$.ajax({
			'url': '<?php echo BASE_URL; ?>api.php?data=product_inventory&act=transfer',
			'method': 'POST',
			'data': submit,
			'success': function(data) {
				if (data.status == 'success') {
					showAlert('', 'Inventory berhasil ditransfer');
					$('#modal-public').off('modal:close');
					$('#modal-public').on('modal:close', function() {
						location.href = '<?php echo BASE_URL; ?>product_inventory/';
					});
				} else if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
			}
		});
	});

	$(document).on('keydown', 'input[name="scan"]', function(ev) {
		if (ev.originalEvent.key == 'Enter') {
			ev.preventDefault();
			if (
				($(this).val() == '') ||
				($(this).val().length != <?php echo BARCODE_LENGTH; ?>)
				) {
				showAlert('', 'Pencarian tidak memenuhi format barcode');
				$('#modal-public').off('modal:close');
				$('#modal-public').on('modal:close', function() {
					$('input[name="scan"]').focus();
				});
				return;
			}
			$.ajax({
				'url': '<?php echo BASE_URL; ?>api.php?data=variant&act=barcode-fetch',
				'method': 'POST',
				'data': {
					'search': $(this).val()
				},
				'success': function(data) {
					if (data.status == 'success') {
						data.result.name = data.result.nama_produk + ' ('+ data.result.nama_variant +')';
						addToList(data.result);
					} else if (data.status == 'notfound') {
						showAlert('', 'Pencarian tidak ditemukan');
						$('#modal-public').off('modal:close');
						$('#modal-public').on('modal:close', function() {
							$('input[name="scan"]').focus();
						});
					} else if (data.status == 'error') {
						showAlert('', data.desc);
						console.log(data.msg);
					}
				}
			});
		}
	});

	$(document).on('keyup', '.box-search input', function(ev, isFake) {
		/*if ($(this).val() == '') {
			$('.box-search select').html('');
			return;
		}*/
		if (isFake == undefined) {
			if ((ev.originalEvent.key == 'ArrowDown') || (ev.originalEvent.key == 'ArrowUp'))
				return;
		}

		$.ajax({
			'url': '<?php echo BASE_URL; ?>api.php?data=variant&act=name-fetch',
			'method': 'POST',
			'data': {
				'search': $(this).val()
			},
			'success': function(data) {
				if (data.status == 'success') {
					$('.box-search > select').html('');
					let pilih = '<option data-index="0" value="0"> -- Pilih Produk -- </option>';
					$('.box-search select').append($(pilih));
					if (data.result.length < 7)
						$('.box-search > select').attr('size', data.result.length + 1);
					else
						$('.box-search > select').attr('size', 8);
					data.result.forEach(function(val, index) {
						let opsi = '<option value="'+ val.id_variant +
							'" data-index="'+ (index + 1) +'" data-id_produk="'+
							val.id_produk +'" data-barcode="'+ val.barcode +'">'+
							val.nama_produk +' ('+ val.nama_variant +')</option>';
						$('.box-search select').append($(opsi));
					});
				} else if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
			}
		});
	});

});
</script>
