<?php
    if (!isset($nodirect)) die('nope');
?>
<ul id="tabPage" class="tab">
    <a href="<?php echo BASE_URL; ?>settings/users"><li>Pengguna</li></a>
    <a href="#0"><li class="active">SPG</li></a>
    <a href="<?php echo BASE_URL; ?>settings/kategori"><li>Kategori</li></a>
    <a href="<?php echo BASE_URL; ?>settings/hh"><li>Happy Hour</li></a>
    <a href="<?php echo BASE_URL; ?>settings/cashbook"><li>Buku Kas</li></a>
    <a href="<?php echo BASE_URL; ?>settings/umum"><li>Umum</li></a>
</ul>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>conference_call.svg" alt="">
        Daftar Spg

        <div style="float: right">
            <button id="btn-add" onClick="add()" class="btn">Buat Spg</button>
        </div>
    </h2>

    <table id="viewer">
        <thead>
            <th style="width: 25%">Nama</th>
            <th style="width: 20%">No. HP</th>
            <th style="width: 35%">Keterangan</th>
            <th style="width: 10%">ID</th>
            <th style="width: 10%">Aksi</th>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<div id="modal-spg" class="modal" style="min-width: 400px;">
    <form id="spg-form" action=""><div class="modal-head">
        <h4></h4>
    </div>
    <div class="modal-text">
        <table class="commonTable">
            <tr>
                <td style="width: 30%;">Nama *</td>
                <td style="width: 70%;">
                    <input type="text" name="nama" style="min-width: 150px; width: 150px;" minlength="<?php echo INPUT_SPG_NAMA_MIN; ?>" maxlength="<?php echo INPUT_SPG_NAMA_MAX; ?>" required>
                </td>
            </tr>
            <tr>
                <td>No HP</td>
                <td>
                    <input type="text" name="nohp" pattern="[+0-9]{0,}" title="Format No. Telepon" style="min-width: 150px; width: 150px;" maxlength="<?php echo INPUT_SPG_NOHP_MAX; ?>">
                </td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>
                    <textarea name="keterangan" id="" maxlength="<?php echo INPUT_SPG_KETERANGAN_MAX; ?>" style="width: 80%;"></textarea>
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
    #viewer tr td:nth-child(4), #viewer tr td:nth-child(5) {
        text-align: center;
    }
</style>

<script>

$('#spg-form').submit(function(ev) {
    ev.preventDefault();
    if ($('input[name="id"]').val() == '') {
//tambah
        let submit = $('#spg-form').serializeArray();
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=spg&act=add',
            method: 'POST',
            data: submit,
            dataType: 'json',
            success: function(data, status) {
                if (data.status == 'error') {
                    showAlert('', data.desc);
                    console.log(data.msg);
                }
                else if (data.status == 'success') {
                    showAlert('', 'Data SPG telah ditambahkan!');
                    $('#viewer').DataTable().ajax.reload();
                    $('#modal-public').on('modal:after-close', function(ev, modal) {
                        $.modal.close();
                    });
                }
            }, error: function(xhr, status) {}
        });
    } else if (isNaN(parseInt($('input[name="id"]').val())) == false) {
//edit
        let submit = $('#spg-form').serializeArray();
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=spg&act=edit',
            method: 'POST',
            data: submit,
            dataType: 'json',
            success: function(data, status) {
                if (data.status == 'error') {
                    showAlert('', data.desc);
                    console.log(data.msg);
                }
                else if (data.status == 'success') {
                    showAlert('', 'Data SPG telah diubah!');
                    $('#viewer').DataTable().ajax.reload();
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
    $('#spg-form').trigger('reset');
    $('input[name="id"]').val('');
    $('#modal-spg .modal-head h4').html('TAMBAH SPG');
    $('#modal-spg').modal({
        showClose: false,
        clickClose: false,
        blockerClass: 'nope'
    });
}

function edit(id) {
    $('#access').html('');
    $.ajax({
        url: '<?php echo BASE_URL; ?>api.php?data=spg&act=fetch',
        method: 'POST',
        dataType: 'json',
        data: {'id': id},
        success: function(data) {
            if (data.status == "error") {
                showAlert('', data.desc);
                console.log(data.msg);
            }
            else if (data.status == "success") {
                $('input[name="nama"]').val(data.nama);
                $('textarea[name="keterangan"]').val(data.keterangan);
                $('input[name="nohp"]').val(data.nohp);
                $('input[name="id"]').val(id);
                $('#modal-spg .modal-head h4').html('UBAH SPG');
                $('#modal-spg').modal({
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
    showConfirm('', 'Anda yakin ingin menghapus SPG ini?');
    $('#btn-yes').click(function() {
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=spg&act=del',
            method: 'POST',
            dataType: 'json',
            data: {'id': id},
            success: function(data) {
                if (data.status == "error") {
                    showAlert('', data.desc);
                    console.log(data.msg);
                }
                else if (data.status == "success") {
                    showAlert('', 'Data SPG telah dihapus!');
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
    $('.menu > ol li:nth-child(9)').addClass('menu-active');
    $('#btn-add').click(function() {
        $('#modal-spg').modal({
            showClose: false,
            clickClose: false,
            blockerClass: 'nope'
        });
    });

    $('#viewer').dataTable({
        'language': {
            'lengthMenu': 'Tampilkan _MENU_ Data SPG',
            'search': '',
            'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Data SPG',
            'infoEmpty': '',
            'infoFiltered': '',
            'emptyTable': 'Tidak ada Data SPG yang dapat ditampilkan',
            'zeroRecords': 'Pencarian tidak ditemukan',
            'paginate': {
                'next': '&raquo;',
                'previous': '&laquo;'
            }
        },
        'serverSide': true,
        'ajax': {
            'url': '<?php echo BASE_URL; ?>api.php?data=spg&act=dt',
            'type': 'POST'
        },
        'columns': [
            {'data': 'nama'},
            {'data': 'nohp'},
            {'data': 'keterangan', 'orderable': false},
            {'data': 'id'},
            {
                'data': 'id', 'orderable': false,
                'searchable': 'false', 'render': function(data, type, row, meta) {
                    let elHapus = '<img onClick="del('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>trash.svg">';
                    let elDetail = '<img onClick="detail('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>search-1.svg">';
                    let elEdit = '<img onClick="edit('+ data +');" src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg">'
                    return elDetail +' '+ elEdit +' '+ elHapus;
                }
            }
        ],
        'order': [[0, 'asc']],
        'orderMulti': false
    });

});
</script>
