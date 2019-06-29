<?php
    if (!isset($nodirect)) die('nope');
?>
<ul id="tabPage" class="tab">
    <a href="#0"><li class="active">Pengguna</li></a>
    <a href="<?php echo BASE_URL; ?>settings/spg"><li>SPG</li></a>
    <a href="<?php echo BASE_URL; ?>settings/kategori"><li>Kategori</li></a>
    <a href="<?php echo BASE_URL; ?>settings/hh"><li>Happy Hour</li></a>
    <a href="<?php echo BASE_URL; ?>settings/cashbook"><li>Buku Kas</li></a>
    <a href="<?php echo BASE_URL; ?>settings/umum"><li>Umum</li></a>
</ul>
<?php
    if (isset($_GET['act'])) {
        switch (strtolower($_GET['act'])) {
            case 'edit':
            case 'add':
                include 'user_update.php'; return; break;
            case '':
            default:
                break;
        }
    }
?>
<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>manager.svg" alt="">
        Daftar Pengguna

        <div style="float: right">
            <a href="<?php echo BASE_URL; ?>settings/users/add"><button class="btn">Tambah Pengguna</button></a>
        </div>
    </h2>

    <table id="users">
        <thead>
            <th style="width: 20%">Nama</th>
            <th style="width: 30%">Email</th>
            <th style="width: 5%">Pic</th>
            <th style="width: 15%">Keterangan</th>
            <th style="width: 10%">Peran</th>
            <th style="width: 7%">ID</th>
            <th style="width: 8%">Aksi</th>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<style>
    #users tr td:nth-child(3), #users tr td:nth-child(6) {
        text-align: center;
    }
</style>

<script>
$(document).ready(function() {
    $('.menu > ol li:nth-child(9)').addClass('menu-active');
    $('#users').dataTable({
        //dom: '<"top"lf>tp<"result"i><"pagination"p>'
        'language': {
            'lengthMenu': 'Tampilkan _MENU_ Pengguna',
            'search': '',
            'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ data Pengguna',
            'infoEmpty': '',
            'infoFiltered': '',
            'emptyTable': 'Tidak ada data Pengguna yang dapat ditampilkan',
            'zeroRecords': 'Pencarian tidak ditemukan',
            'paginate': {
                'next': '&raquo;',
                'previous': '&laquo;'
            }
        },
        'serverSide': true,
        'ajax': {
            'url': '<?php echo BASE_URL; ?>api.php?data=admins&act=dt',
            'type': 'POST'
        },
        'columns': [
            {'data': 'nama',},
            {'data': 'email',},
            {
                'data': 'pic', 'searchable': false,
                'orderable': false, 'render': function(data, type, row, meta) {
                    return '<img src="<?php echo BASE_URL.PATH_IMG; ?>profiles/' + data + '.svg">';
                }
            },
            {'data': 'keterangan'},
            {'data': 'peran'},
            {'data': 'id'},
            {
                'data': 'id', 'orderable': false,
                'searchable': 'false', 'render': function(data, type, row, meta) {
                    //console.log(data); console.log(type); console.log(row); console.log(meta);
                    //$('#user tr:nth-child(' + (meta.row + 1) + ') td:nth-child(' + (meta.col + 1) + ')').attr('data-id');
                    return '<a href="<?php echo BASE_URL; ?>settings/users/edit/'+ data +'"><img src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg"></a>';
                }
            }
        ],
        'order': [[0, 'asc']],
        'orderMulti': false
    });

});
</script>
