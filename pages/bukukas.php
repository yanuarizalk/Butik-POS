<?php
    if (!isset($nodirect)) die('nope');
?>
<ul id="tabPage" class="tab">
    <a href="<?php echo BASE_URL; ?>settings/users"><li>Pengguna</li></a>
    <a href="<?php echo BASE_URL; ?>settings/spg"><li>SPG</li></a>
    <a href="<?php echo BASE_URL; ?>settings/kategori"><li>Kategori</li></a>
    <a href="<?php echo BASE_URL; ?>settings/hh"><li>Happy Hour</li></a>
    <a href="#0"><li class="active">Buku Kas</li></a>
    <a href="<?php echo BASE_URL; ?>settings/umum"><li>Umum</li></a>
</ul>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>notebook-1.svg" alt="">
        Daftar Buku Kas

        <div style="float: right">
            <button id="btn-add" onClick="add()" class="btn">Buat Buku Kas</button>
        </div>
    </h2>

    <table id="kas">
        <thead>
            <th style="width: 20%">Nama</th>
            <th style="width: 20%">Keterangan</th>
            <th style="width: 15%">Saldo Awal</th>
            <th style="width: 15%">Saldo Sekarang</th>
            <th style="width: 15%">Jml. Aktifitas</th>
            <th style="width: 7%">ID</th>
            <th style="width: 8%">Aksi</th>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<div id="modal-kas" class="modal" style="min-width: 400px;">
    <form id="kas-form" action=""><div class="modal-head">
        <h4>BUAT BUKU KAS</h4>
    </div>
    <div class="modal-text">
        <table class="commonTable">
            <tr>
                <td style="width: 30%;">Nama Kas *</td>
                <td style="width: 70%;">
                    <input type="text" name="nama" style="min-width: 150px; width: 150px;" minlength="<?php echo INPUT_CASHBOOK_NAMA_MIN; ?>" maxlength="<?php echo INPUT_CASHBOOK_NAMA_MAX; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
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
        <a href="#0" rel="modal:close"><button type="button" class="btn">Batal</button></a>
        <button class="btn" type="submit">Simpan</button>
    </div></form>
</div>

<style>
    #kas tr td:nth-child(3), #kas tr td:nth-child(4) {
        text-align: right;
    }
    #kas tr td:nth-child(5), #kas tr td:nth-child(6) {
        text-align: center;
    }
</style>

<script>

$('#kas-form').submit(function(ev) {
    ev.preventDefault();
		let submit = $('#kas-form').serializeArray();
    if ($('input[name="id"]').val() == '') {
//tambah
			let parser = submit.find(input => input.name == 'saldo_awal').value;
			submit.find(input => input.name == 'saldo_awal').value = formatCurToDec(parser);
			$.ajax({
				url: '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=add',
				method: 'POST',
				data: submit,
				dataType: 'json',
				success: function(data, status) {
					if (data.status == 'error') {
						showAlert('', data.desc);
						console.log(data.msg);
					}
					else if (data.status == 'success') {
						showAlert('', 'Buku Kas telah ditambahkan!');
								$('#kas').DataTable().ajax.reload();
						$('#modal-public').on('modal:after-close', function(ev, modal) {
								$.modal.close();
						});
					}
				}, error: function(xhr, status) {}
			});
    } else if (isNaN(parseInt($('input[name="id"]').val())) == false) {
//edit
			$.ajax({
				url: '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=edit',
				method: 'POST',
				data: submit,
				dataType: 'json',
				success: function(data, status) {
					if (data.status == 'error') {
						showAlert('', data.desc);
						console.log(data.msg);
					}
					else if (data.status == 'success') {
						showAlert('', 'Buku Kas telah diubah!');
								$('#kas').DataTable().ajax.reload();
						$('#modal-public').on('modal:after-close', function(ev, modal) {
								$.modal.close();
						});
					}
				}, error: function(xhr, status) {}
			});
    }
});

function add() {
    $('#access').html('');
    $.ajax({
        url: '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=fetch',
        method: 'POST',
        dataType: 'json',
        success: function(data) {
            if (data.status == "error") {
                showAlert('', data.desc);
                console.log(data.msg);
            } else if (data.status == "success") {
                $('#saldo_awal').css('display', 'table-row');
                /*$('input[name="nama"]').val('');
                $('input[name="keterangan"]').val('');
                $('input[name="saldo_awal"]').val('');*/
                $('#kas-form').trigger('reset');
                $('input[name="id"]').val('');
                let listUser = '';
                data.users.forEach(user => {
                    listUser += '<input type="checkbox" name="access_kas['+ user.id +']" checked>' +
                                '<label for="access_kas['+ user.id +']">'+ user.nama +'</label><br>'
                });
                $('#access').html(listUser);
                $('#modal-kas .modal-head h4').html('BUAT BUKU KAS');
                $('#modal-kas').modal({
                    showClose: false,
                    clickClose: false,
                    blockerClass: 'nope'
                });
            }
        },
        error: function(xhr, status) {}
    });
}

function edit(id) {
    $('#access').html('');
    $.ajax({
        url: '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=fetch',
        method: 'POST',
        dataType: 'json',
        data: {'id': id},
        success: function(data) {
            if (data.status == "error") {
                showAlert('', data.desc);
                console.log(data.msg);
            }
            else if (data.status == "success") {
                $('#saldo_awal').css('display', 'none');
                $('input[name="nama"]').val(data.nama);
                $('input[name="keterangan"]').val(data.keterangan);
                $('input[name="saldo_awal"]').val('0');
                $('input[name="id"]').val(id);
                let listUser = '';
                data.users.forEach(user => {
                    listUser += '<input type="checkbox" name="access_kas['+ user.id +']">' +
                                '<label for="access_kas['+ user.id +']">'+ user.nama +'</label><br>'
                });
                $('#access').html(listUser);
                let access_kas = JSON.parse(data.access_kas);
                access_kas.forEach(user => {
                    if (user == '*') {
                        $('#access input[type="checkbox"]').attr('checked', true);
                        return;
                    }
                    $('input[name="access_kas['+ user +']"]').attr('checked', true);
                });
                $('#modal-kas .modal-head h4').html('UBAH BUKU KAS');
                $('#modal-kas').modal({
                    showClose: false,
                    clickClose: false,
                    blockerClass: 'nope'
                });
            }
        },
        error: function(xhr, status) {}
    });
}

function del(id) {
	showConfirm('', 'Anda yakin ingin menghapus Buku Kas ini?');
	$('#btn-yes').click(function() {
		$.ajax({
			url: '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=del',
			method: 'POST',
			dataType: 'json',
			data: {'id': id},
			success: function(data) {
				if (data.status == "error") {
					showAlert('', data.desc);
					console.log(data.msg);
				}
				else if (data.status == "success") {
					showAlert('', 'Buku Kas telah dihapus!');
							$('#kas').DataTable().ajax.reload();
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
    $('.menu > ol li:nth-child(9)').addClass('menu-active');
    $('#btn-add').click(function() {
        $('#modal-kas').modal({
            showClose: false,
            clickClose: false,
            blockerClass: 'nope'
        });
    });

    $('#kas').dataTable({
        'language': {
            'lengthMenu': 'Tampilkan _MENU_ Buku Kas',
            'search': '',
            'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Buku Kas',
            'infoEmpty': '',
            'infoFiltered': '',
            'emptyTable': 'Tidak ada Buku Kas yang dapat ditampilkan',
            'zeroRecords': 'Pencarian tidak ditemukan',
            'paginate': {
                'next': '&raquo;',
                'previous': '&laquo;'
            }
        },
        'serverSide': true,
        'ajax': {
            'url': '<?php echo BASE_URL; ?>api.php?data=buku_kas&act=dt',
            'type': 'POST'
        },
        'columns': [
            {'data': 'nama'},
            {'data': 'keterangan'},
            {'data': 'saldo_awal', render: function(data, type, row, meta) {
                return 'Rp ' + formatDecToCurrency(parseFloat(data));
            }},
            {'data': 'saldo_now', render: function(data, type, row, meta) {
                return 'Rp ' + formatDecToCurrency(parseFloat(data));
            }},
            {'data': 'act'},
            {'data': 'id'},
            {
                'data': 'id', 'orderable': false,
                'searchable': 'false', 'render': function(data, type, row, meta) {
                    let elHapus = '<img onClick="del('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>trash.svg">';
                    if (data <= 5) elHapus = '';
                    return '<img onClick="edit('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg"> '+ elHapus;
                }
            }
        ],
        'order': [[0, 'asc']],
        'orderMulti': false
    });

});
</script>
