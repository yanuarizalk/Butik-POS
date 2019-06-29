<?php
    if (!isset($nodirect)) die('nope');
?>

<div class="page">
	<h2>
		<img src="<?php echo BASE_URL.PATH_IMG; ?>T-Shirt.svg" alt="">
		Group Produk
		<div class="right">
			<button class="btn" id="btn-add" type="button">Buat Group</button>
		</div>
	</h2>

	<table id="viewer">
		<thead>
			<tr>
				<th style="width: 30%">Nama Group</th>
				<th style="width: 15%">Group ID</th>
				<th style="width: 45%">List Produk</th>
				<th style="width: 10%">Aksi</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

</div>

<div id="modal-editor" class="modal" style="min-width: 400px;">
	<form id="editor" action=""><div class="modal-head">
		<h4>Tambah Group</h4>
	</div>
	<div class="modal-text">
		<table class="commonTable">
			<tr>
				<td style="width: 30%;">Nama Group </td>
				<td style="width: 70%;">
					<input type="text" name="nama" style="min-width: 150px; width: 150px;" maxlength="<?php echo INPUT_GROUP_NAMA_MAX; ?>">
				</td>
			</tr>
			<tr>
				<td>Produk *</td>
				<td>
					<input type="text" id="search" class="search" readonly placeholder="Pilih Produk">
					<div class="box-search">
						<input type="text">
						<select>

						</select>
					</div>
					<div id="list-produk" class="tagPlace">
						<!--div class="tag">Produk Baru
						<img src="<?php echo BASE_URL.PATH_IMG; ?>cross.svg" ></div-->
					</div>
				</td>
			</tr>
			<input type="hidden" name="id">
		</table>
	</div>
	<div class="modal-foot">
		<a href="#0" rel="modal:close"><button type="button" class="btn">Batal</button></a>
		<button class="btn" type="submit">Simpan</button>
	</div></form>
</div>



<style>
	#viewer tr td {
		text-align: left;
	}
	#viewer tr td:nth-child(4) {
		text-align: center;
	}
	#viewer tr td:nth-child(3) {
		font-weight: bold;
	}
	#editor .commonTable tr:nth-child(2) {
		vertical-align: top;
	}
</style>

<script>

let deletedTag = [];

function delTag(id)	{
	deletedTag.push(id);
	$('#list-produk .tag[data-id="'+ id +'"]').remove();
}

function addToList(data) {
	if ($('#list-produk .tag[data-id="'+ data.id +'"]').length > 0 )
		return;
	let ls = $('#list-produk');
	let tagClose = '<img src="<?php echo BASE_URL.PATH_IMG; ?>cross.svg" onClick="delTag('+ data.id +')">';
	let printTag = '<div class="tag" data-id="'+ data.id +'">'+ data.name + ' &nbsp; ' + tagClose +'</div>';
	$(printTag).appendTo(ls);
	if (deletedTag.indexOf(parseInt(data.id)) >= 0)
		deletedTag.splice(deletedTag.indexOf(parseInt(data.id)), 1);
	curSearch.val('');
}

function add() {
	$('#modal-editor .modal-head h4').html('Tambah Group');
	$('input[name="id"]').val('');

	$('#editor').trigger('reset');
	$('#list-produk').html('');

	$('#modal-editor').modal({
		showClose: false,
		clickClose: false,
		blockerClass: 'nope'
	});
}
function edit(id) {
	$('#modal-editor .modal-head h4').html('Ubah Group');
	$('input[name="id"]').val(id);

	$('#editor').trigger('reset');
	$('#list-produk').html('');

	$.ajax({
		url: '<?php echo BASE_URL; ?>api.php?data=product_group&act=fetch',
		method: 'POST',
		data: {
			'id': id
		},
		dataType: 'json',
		success: function(data, status) {
			if (data.status == 'error') {
				showAlert('', data.desc);
				console.log(data.msg);
			}
			else if (data.status == 'success') {
				$('input[name="nama"]').val(data.name);
				deletedTag = [];
				for (let iFor in data.list) {
					addToList(data.list[iFor]);
				}
				$('#modal-editor').modal({
					showClose: false,
					clickClose: false,
					blockerClass: 'nope'
				});
			}
		}, error: function(xhr, status) {}
	});
}
function del(id) {
	showConfirm('', 'Anda yakin ingin menghapus Group ini?');
	$('#btn-yes').click(function() {
		$.ajax({
			url: '<?php echo BASE_URL; ?>api.php?data=product_group&act=del',
			method: 'POST',
			dataType: 'json',
			data: {'id': id},
			success: function(data) {
				if (data.status == "error") {
					showAlert('', data.desc);
					console.log(data.msg);
				}
				else if (data.status == "success") {
					showAlert('', 'Group telah dihapus!');
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
	$('.menu > ol li:nth-child(3) li:nth-child(3)').addClass('menu-active');
	$('#btn-add').click(add);

	$('#editor').submit(function(ev) {
		ev.preventDefault();
		let submit = $(this).serializeArray();
		let url;

		if (!$('#list-produk').has($('.tag')).length) {
			showAlert('', 'Mohon, pilih Produk yang ingin ditambahkan<br>ke dalam Group terlebih dahulu');
			$('#modal-public').off('modal:after-close');
			return;
		}

		$('#list-produk .tag').each(function() {
			submit.push({
				'name': 'added_product[]',
				'value': $(this).data('id')
			});
		});
		if ($('input[name="id"]').val() == '') {
//tambah
			url = '<?php echo BASE_URL; ?>api.php?data=product_group&act=add';
			function cbSuccess() {
				showAlert('', 'Group baru berhasil dibuat!');
				$('#viewer').DataTable().ajax.reload();
				$('#modal-public').on('modal:after-close', function(ev, modal) {
					$.modal.close();
				});
			}
    } else if (isNaN(parseInt($('input[name="id"]').val())) == false) {
//edit
			url = '<?php echo BASE_URL; ?>api.php?data=product_group&act=edit';
			function cbSuccess() {
				showAlert('', 'Produk Group berhasil diubah!');
				$('#viewer').DataTable().ajax.reload();
				$('#modal-public').on('modal:after-close', function(ev, modal) {
					$.modal.close();
				});
			}
			for(let iFor in deletedTag) {
				submit.push({
					'name': 'deleted_product[]',
					'value': deletedTag[iFor]
				});
			}
    }
		$.ajax({
			url: url,
			method: 'POST',
			data: submit,
			dataType: 'json',
			success: function(data, status) {
				if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
				else if (data.status == 'success') {
					cbSuccess();
				}
			}, error: function(xhr, status) {}
		});
	});

	let viewer = $('#viewer').dataTable({
		//dom: '<"top"lf>tp<"result"i><"pagination"p>'
		'language': {
			'lengthMenu': 'Tampilkan _MENU_ Group Produk',
			'search': '',
			'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Group Produk',
			'infoEmpty': '',
			'infoFiltered': '',
			'emptyTable': 'Tidak ada Group Produk yang dapat ditampilkan',
			'zeroRecords': 'Pencarian tidak ditemukan',
			'paginate': {
					'next': '&raquo;',
					'previous': '&laquo;'
			}
		},
		'serverSide': true,
		'ajax': {
			'url': '<?php echo BASE_URL; ?>api.php?data=product_group&act=dt',
			'type': 'POST'
		},
		'columns': [
			{'data': 'nama'},
			{'data': 'id'},
			{
				'data': 'list', 'render': function(data, type, row, meta) {
					let list = '';
					for (let iFor in data) {
						list += '<li>'+ data[iFor] +'</li>';
					};
					return '<ul>'+ list +'</ul>';
				}
			},
			{
				'data': 'id', 'orderable': false,
				'searchable': 'false', 'render': function(data, type, row, meta) {
						//console.log(data);
						//console.log(type);
						//console.log(row.DT_RowAttr['data-id_produk']);
						//console.log(meta);
						let elEdit = '<img onClick="edit('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg">';
						let elDel = '<img onClick="del('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>cross.svg">';
						return elEdit + ' ' + elDel;
				}
			}
		],
		'order': [[0, 'asc']],
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

		let dataSearch = {
			'search': $(this).val(),
			'options': {}
		}
		if ($('input[name="id"]').val() == '') {
			dataSearch.options.filter = 'group';
		} else if (isNaN(parseInt($('input[name="id"]').val())) == false) {
			dataSearch.options.filter = 'group&id';
			dataSearch.options.filterId = parseInt($('input[name="id"]').val());
		}

		$.ajax({
			'url': '<?php echo BASE_URL; ?>api.php?data=product&act=name-fetch',
			'method': 'POST',
			'data': dataSearch,
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
						/*let opsi = '<option value="'+ val.id_variant +
							'" data-index="'+ (index + 1) +'" data-id_produk="'+
							val.id_produk +'" data-barcode="'+ val.barcode +'">'+
							val.nama_produk +'</option>';*/
						//let opsi = '<option value="'+ val.id +'" data-id_produk="'+ val.id +'" data-index="'+ (index + 1) +'">'+
						let opsi = '<option value="'+ val.id +'" data-index="'+ (index + 1) +'">'+
							val.nama_produk +'</option>';
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
