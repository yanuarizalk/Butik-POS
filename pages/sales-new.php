<?php
    if (!isset($nodirect)) die('nope');

    $title = "Penjualan Baru";

?>

<div class="page">
	<h2>
			<img src="<?php echo BASE_URL.PATH_IMG; ?>shop.svg" alt="">

			<?php echo $title; ?>
		<div class="right">
			<label class="text-info" id="how_much">Rp 0,00</label>
		</div>
	</h2>

	<form action="" id="editor" style="margin-top: 20px;">
	<div class="page-45">
		<table class="commonTable">
			<tr>
				<td style="width: 25%;">Transaksi *</td>
				<td style="width: 75%;">
					<select name="transaksi" required>
						<option value="" disabled selected> -- Pilih Transaksi -- </option>
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
				<td>SPG</td>
				<td>
					<input type="text" name="spg" id="search-spg" class="search" style="min-width: 150px; width: 40%;" readonly>
				</td>
			</tr>
			<tr>
				<td>Member ID</td>
				<td>
					<input type="text" name="member" id="search-member" class="search" style="min-width: 100px; width: 30%;" readonly>
					<a href="<?php echo BASE_URL; ?>member/new" target="_blank"><sub> Member baru</sub></a>
				</td>
			</tr>
			<tr>
				<td>Nama Konsumen</td>
				<td><input type="text" name="nama_konsumen" style="min-width: 100px; width: 30%;"></td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name="keterangan" style="width: 100%; min-height: 30px;" maxlength="<?php echo INPUT_KETERANGAN_MAX; ?>" ></textarea></td>
			</tr>
		</table>
	</div><div class="page-10"></div><!--
 --><div class="page-45">

	</div>
	<br><br>
	<div class="page-100">
		<input type="text" name="scan" pattern="[0-9]{<?php echo '0,'.BARCODE_LENGTH; ?>}" maxlength="<?php echo BARCODE_LENGTH; ?>" placeholder="Scan Barcode">
		atau
		<input type="text" id="search-produk" class="search" readonly placeholder="Pencarian Nama">
		<div class="box-search">
			<input type="text">
			<select size="8">

			</select>
		</div>
		<br> <br>
		<table class="preTable" id="preProduk">
			<tr><td class="header" colspan="7">
				Item Penjualan
			</td></tr>
			<tr>
				<th width="15%">ID Barcode</th>
				<th width="30%">Nama Produk - Varian</th>
				<th width="10%">Harga</th>
				<th width="10%">Jumlah</th>
				<th width="10%">Diskon Item</th>
				<th width="20%">Total</th>
				<th width="5%"></th>
			</tr>
			<tr>
				<th colspan="4"> Total Penjualan </th>
				<th><input type="text" name="total_qty" style="width: 50px" style="text-align: right;" readonly> Pcs</th>
				<th style="font-weight: inherit;"> Rp <input type="text" name="total_produk" style="width: 100px" data-type="currency" readonly> </th>
				<th></th>
			</tr>
		</table>
		<br> <br>
		<table class="preTable" id="prePlus">
			<tr><td class="header" colspan="3">
				Biaya Tambahan (+)
			</td></tr>
			<tr>
				<td width="50%"> Charge Credit Card (<?php echo CHARGE_CC; ?>%) <input type="checkbox" name="charge_cc" value="cek"></td>
				<td width="45%"> Rp <input type="text" name="charge_cc_much" data-type="currency" readonly> </td>
				<td width="5%"></td>
			</tr>
		</table>
		<br> <br>
		<table class="preTable" id="preMin">
			<tr><td class="header" colspan="3">
				Diskon (-)
			</td></tr>
			<tr>
				<td width="50%"> <input type="text" name="voucher" readonly placeholder="Kode Voucher"></td>
				<td width="45%"> Rp <input type="text" name="diskon_voucher" data-type="currency"> </td>
				<td width="5%"></td>
			</tr>
		</table>
		<br><br>
		<table id="finalTable" class="commonTable">
			<tr>
				<td>Total Transaksi</td>
				<td>Rp <input type="text" name="total_transaksi" data-type="currency" readonly></td>
				<td></td>
			</tr>
			<tr>
				<td>Dibayarkan *</td>
				<td>Rp <input type="text" name="pay" data-type="currency" required></td>
				<td></td>
			</tr>
			<tr>
				<td>Kembalian</td>
				<td>Rp <input name="payback" type="text" data-type="currency" readonly></td>
				<td></td>
			</tr>
			<tr>
				<td>Pembayaran *</td>
				<td><select name="payment" id="" required>
					<option value="Cash">Cash</option>
					<option value="Debit">Debit</option>
				</select></td>
				<td></td>
			</tr>
		</table>
	</div>
	<div class="bottom" style="">
		<a href="<?php echo BASE_URL; ?>sales"><button type="button" class="btn">
			&laquo; Batal
		</button></a>
	<div class="right">
		<button type="submit" class="btn" id='btn-submit'>
				Simpan
		</button>
		<button class="btn" type="button" id="btn-submit_print">
				Simpan dan Print
		</button>
	</div></div>
	<div style="clear: both;"></div>
	</form>
</div>

<style>
	input[data-type="currency"] {
		text-align: right;
	}
	#preProduk tr:last-child th{
		text-align: right;
	}
	#preProduk tr:not(:first-child) td {
		text-align: center;
	}
	#preProduk tr td:nth-child(3n) {
		text-align: right;
	}
	#preProduk tr td:nth-child(2) {text-align: left;}
	#preProduk tr td:nth-child(4) input, #preProduk tr td:nth-child(5) input {
		width: 50%;
		min-width: 40px;
	}
	#finalTable {
		width: 100%; background-color: var(--bg-abu);
	}
	#finalTable tr td {
		padding: 5px;
	}
	#finalTable td:nth-child(1) {
		width: 70%; text-align: right;
		font-weight: bold;
	}
	#finalTable td:nth-child(2) {
		width: 25%; text-align: right;
	}
	#finalTable td:nth-child(2) input {
		font-size: 18px;
		text-align: right;
		width: 50%;
	}
	#prePlus tr td:nth-child(2), #preMin tr td:nth-child(2) {
		text-align: right;
	}
	#prePlus tr td:nth-child(2) input, #preMin tr td:nth-child(2) input {
		width: 20%;
		min-width: 125px;
	}
	/*#preTable tr td:nth-child()*/
</style>

<script>
let dp;

function updateTotal() {
	let totQty = 0, tot = 0, iGroup = [];

	for(let iFor = 0; iFor < $('.qty').length; iFor++) {
		let group = parseInt($($('.qty')[iFor]).parent().parent().data('group')) || 0;
		if (iGroup[group] == undefined) iGroup[group] = 0;
		if (group == 0) continue;
		iGroup[group] += parseInt($($('.qty')[iFor]).val()) || 0;
	}
	//console.log(iGroup);
	for(let iFor = 0; iFor < $('.qty').length; iFor++) {
		if ((parseInt($($('.qty')[iFor]).val()) == NaN) || ($($('.qty')[iFor]).val() == '')) continue;
		let curGrosir = $($('.qty')[iFor]).parent().parent().data('grosir');
		let curGroup = parseInt($($('.qty')[iFor]).parent().parent().data('group')) || 0;
		let curTot = 0, curEcer = parseFloat($($('.qty')[iFor]).parent().parent().data('ecer'));

		for (let iForGrosir = 0; iForGrosir < curGrosir.length; iForGrosir++) {
		// grosir price checker
			if (curGroup != 0) {
				if (
					(iGroup[curGroup] >= parseInt(curGrosir[iForGrosir][0]) ) &&
					(iGroup[curGroup] <= parseInt(curGrosir[iForGrosir][1]) )
					) {
					curEcer = parseFloat(curGrosir[iForGrosir][2]);
					break;
				}
			} else {
				if (
					( parseInt($($('.qty')[iFor]).val()) >= parseInt(curGrosir[iForGrosir][0]) ) &&
					( parseInt($($('.qty')[iFor]).val()) <= parseInt(curGrosir[iForGrosir][1]) )
					) {
					curEcer = parseFloat(curGrosir[iForGrosir][2]);
					break;
				}
			}
		}
		if ( (parseInt($($('.diskon')[iFor]).val()) > 0) && (parseInt($($('.diskon')[iFor]).val()) <= 100) ) {
			curTot = parseInt($($('.diskon')[iFor]).val()) / 100;
			curTot *= parseInt($($('.qty')[iFor]).val()) * parseFloat(curEcer);
			curTot = parseInt($($('.qty')[iFor]).val()) * parseFloat(curEcer) - curTot;
		} else {
			curTot = parseInt($($('.qty')[iFor]).val()) * parseFloat(curEcer);
		}
		$($('.qty')[iFor]).parent().siblings('td:nth-child(3)').html('@ Rp' + formatDecToCurrency(curEcer));
		$($('.total_per')[iFor]).html('Rp '+ formatDecToCurrency(curTot));
		totQty += parseInt($($('.qty')[iFor]).val());
		tot += curTot;
	}
	let cekCC = $('input[name="charge_cc"]').is(':checked') ? tot * <?php echo CHARGE_CC; ?> / 100 : 0;
	let diskon_vc = $('input[name="diskon_voucher"]').val() || 0;
	let plusMin = cekCC - parseFloat(formatCurToDec(diskon_vc));
	$('input[name="total_qty"]').val(totQty);
	$('input[name="total_produk"]').val(formatDecToCurrency(tot));
	$('input[name="charge_cc_much"]').val(formatDecToCurrency(cekCC));
	$('#how_much').html('Rp '+ formatDecToCurrency(tot + plusMin));
	$('input[name="total_transaksi"]').val(formatDecToCurrency(tot + plusMin));
	updatePayBack();
}
function updatePayBack() {
	if (
	(parseFloat(formatCurToDec($('input[name="total_transaksi"]').val())) == NaN) ||
	(parseFloat(formatCurToDec($('input[name="pay"]').val())) == NaN) ||
	($('input[name="total_transaksi"]').val() == '' ) ||
	($('input[name="pay"]').val() == '')
	) {
		$('input[name="payback"]').val(formatDecToCurrency(0));
	} else {
		$('input[name="payback"]').val(formatDecToCurrency(
			parseFloat(formatCurToDec($('input[name="pay"]').val())) -
			parseFloat(formatCurToDec($('input[name="total_transaksi"]').val()))
		));
	}
}

function minProduct(id) {
	$('#preProduk tr td input[name="qty['+ id +']"]').parent().parent().remove();
	updateTotal();
}

function addToList(data) {
	if (curSearch) {
		if (curSearch.attr('id') == 'search-spg') {
			if (parseInt(data.id) == -1) {
				curSearch.val('');
				curSearch.removeData();
			}
			return;
		}
	}
	if ($('#preProduk tr td input[name="qty['+ data.id +']"]').length > 0 )
		return;
	let endTable = $('#preProduk tr:last-child');
	let printRow = '<tr>'+
			'<td>'+ data.barcode +'</td>'+
			'<td>'+ data.name +'</td>'+
			'<td>@ Rp '+ formatDecToCurrency(data.harga_ecer) +'</td>'+
			'<td><input type="number" min="1" data-format="number" name="qty['+
			data.id +']" class="qty" value="1" style="text-align: right;" required> Pcs</td>'+
			'<td><input type="number" min="0" max="100" data-format="number" name="diskon['+
			data.id +']" class="diskon" value="0" required> %</td>'+
			'<td class="total_per" id="total['+ data.id +']">Rp '+ formatDecToCurrency(data.harga_ecer) +'</td>'+
			'<td><img src="<?php echo BASE_URL.PATH_IMG; ?>minus.svg" onClick="minProduct('+
			data.id +')"></td>'
			+'</tr>';
	$(printRow).insertBefore(endTable).data({
		'grosir': data.harga_grosir,
		'ecer': data.harga_ecer,
		'group': data.id_group
	});
	updateTotal();
	if (!curSearch == false)
		curSearch.val('');
	$('input[name="qty['+ data.id + ']"]').focus();
	$('input[name="scan"]').val('');
}

$(document).ready(function() {
	$('.menu > ol > li:nth-child(2)').addClass('menu-active');
	dp = $('input[name="tanggal"]').datepicker({
		'language': 'en',
		'toggleSelected': false,
		'dateFormat': 'dd M yyyy',
		/*'maxDate': new Date(),*/
	});
	$('input[name="tanggal"]').datepicker().data('datepicker').selectDate(new Date());
	$('select[name="jam"]').val(new Date().getHours());
	$('select[name="menit"]').val(new Date().getMinutes());

	$(document).on('change', '.qty, .diskon, input[name="charge_cc"], input[name="diskon_voucher"]', updateTotal);
	$(document).on('keyup', '.qty, .diskon, input[name="charge_cc"], input[name="diskon_voucher"]', updateTotal);
	$(document).on('change', 'input[name="pay"]', updatePayBack);
	$(document).on('keyup', 'input[name="pay"]', updatePayBack);

	$('#editor').on('submit', function(ev, isPrint) {
		ev.preventDefault();
		let submit = $(this).serializeArray();
		updateTotal();
		if (!$('#preProduk').has('.qty').length) {
			showAlert('', 'Mohon inputkan Item penjualan');
			return;
		}
		if (parseFloat(formatCurToDec($('input[name="pay"]').val())) < parseFloat(formatCurToDec($('input[name="total_transaksi"]').val()))) {
			showAlert('', 'Pembayaran kurang');
			return;
		}
		let parser = submit.find(input => input.name == 'pay').value;
		submit.find(input => input.name == 'pay').value = formatCurToDec(parser);
		parser = submit.find(input => input.name == 'diskon_voucher').value;
		submit.find(input => input.name == 'diskon_voucher').value = formatCurToDec(parser);
		submit.find(input => input.name == 'spg').value = $('input[name="spg"]').data('id');
		$.ajax({
			'url': '<?php echo BASE_URL; ?>api.php?data=sales&act=new',
			'method': 'POST',
			'data': submit,
			'success': function(data) {
				if (data.status == 'success') {
					showAlert('', 'Data penjualan berhasil dimasukkan');
					$('#modal-public').off('modal:close');
					$('#modal-public').on('modal:close', function() {
						if (isPrint === true) {

						} else
							location.href = '<?php echo BASE_URL; ?>sales';
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
						curSearch = $('#search-produk');
						data.result.harga_grosir = JSON.parse(data.result.harga_grosir);
						addToList(data.result);
						curSearch.val('');
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
		if (isFake == undefined) {
			if ((ev.originalEvent.key == 'ArrowDown') || (ev.originalEvent.key == 'ArrowUp'))
				return;
		}
		$('.box-search > select').html('');
		$('.box-search > select').attr('size', 1);
		let url;
		if (curSearch.attr('id') == 'search-produk') {
			url = '<?php echo BASE_URL; ?>api.php?data=variant&act=name-fetch';
			function cbSuccess(data) {
				let pilih = '<option data-index="0" value="0"> -- Pilih Produk -- </option>';
				$('.box-search select').append($(pilih));
				if (data.result.length < 7)
					$('.box-search > select').attr('size', data.result.length + 1);
				else
					$('.box-search > select').attr('size', 8);
				data.result.forEach(function(val, index) {
					let opsi = $('<option></option>').attr({
						'data-id_produk': val.id_produk,
						'data-barcode': val.barcode,
						'data-harga_ecer': val.harga_ecer,
						'data-harga_grosir': val.harga_grosir,
						'data-id_group': val.id_group,
						'data-index': index + 1
					});
					opsi.val(val.id_variant);
					opsi.html(val.nama_produk + ' ('+ val.nama_variant +')');
					/*opsi.data('id_produk', val.id_produk);
					opsi.data('barcode', val.barcode);
					opsi.data('ecer', val.harga_ecer);			//Doesn't work when it first init
					opsi.data('grosir', val.harga_grosir);*/
					$('.box-search select').append(opsi);
				});
			}
		} else if (curSearch.attr('id') == 'search-spg') {
			url = '<?php echo BASE_URL; ?>api.php?data=spg&act=name-fetch';
			function cbSuccess(data) {
				let pilih = '<option data-index="0" value="0"> -- Pilih SPG -- </option>';
				$('.box-search select').append($(pilih));
				if (data.result.length < 7)
					$('.box-search > select').attr('size', data.result.length + 1);
				else
					$('.box-search > select').attr('size', 8);
				$('.box-search select').append($('<option data-index="1" value="-1"> (Kosongi) </option>'));
				data.result.forEach(function(val, index) {
					let opsi = $('<option></option>').attr({
						'data-index': index + 2
					});
					opsi.val(val.id);
					opsi.html(val.nama);
					$('.box-search select').append(opsi);
				});
			}
		}
		$.ajax({
			'url': url,
			'method': 'POST',
			'data': {
				'search': $(this).val()
			},
			'success': function(data) {
				if (data.status == 'success') {
					cbSuccess(data);
				} else if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
			}
		});
	});

});
</script>
