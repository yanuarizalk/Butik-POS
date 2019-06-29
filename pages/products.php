<?php
    if (!isset($nodirect)) die('nope');
?>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>T-Shirt.svg" alt="">
        Daftar Produk

    </h2>
    <div class="nav-inside">
    	<select>
				<option value="0">Semua Produk</option>
				<option value="1">Semua Variant</option>
				<option value="2">Semua Group</option>
				<option value="3">-</option>
			</select>
			<button class="btn">Go</button>
			<div class="right">
				<a href="<?php echo BASE_URL; ?>products/new"><div class="btn2 btn-big">BUAT PRODUK</div></a>
			</div>
    </div>
    <table id="viewer">
        <thead>
           	<th style="width: 4%"></th>
            <th style="width: 6%">ID</th>
            <th style="width: 20%">Nama Produk</th>
            <th style="width: 20%">Nama Struk</th>
            <th style="width: 15%">Harga Satuan</th>
            <th style="width: 25%">Harga Grosir</th>
            <th style="width: 10%">Aksi</th>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<style>
    #viewer tr td:nth-child(2), #viewer tr td:nth-child(7) {
        text-align: center;
    }
    #viewer tr td:nth-child(5), #viewer tr td:nth-child(6) {
        text-align: right;
    }
</style>

<script>
function del(id) {
	showConfirm('', 'Anda yakin ingin menghapus Produk ini?<br><label class="desc-smol">Note: Varian dalam produk juga akan ikut terhapus.</label>');
    $('#btn-yes').click(function() {
			$.ajax({
				url: '<?php echo BASE_URL; ?>api.php?data=product&act=del',
				method: 'POST',
				dataType: 'json',
				data: {'id': id},
				success: function(data) {
					if (data.status == "error") {
						showAlert('', data.desc);
						console.log(data.msg);
					}
					else if (data.status == "success") {
						showAlert('', 'Produk telah dihapus!');
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
    $('.menu > ol li:nth-child(3) li:nth-child(1)').addClass('menu-active');
    let viewer = $('#viewer').dataTable({
        //dom: '<"top"lf>tp<"result"i><"pagination"p>'
        'language': {
            'lengthMenu': 'Tampilkan _MENU_ Produk',
            'search': '',
            'info' : 'Menampilkan _START_ sampai _END_ dari total _MAX_ Produk',
            'infoEmpty': '',
            'infoFiltered': '',
            'emptyTable': 'Tidak ada Produk yang dapat ditampilkan',
            'zeroRecords': 'Pencarian tidak ditemukan',
            'paginate': {
                'next': '&raquo;',
                'previous': '&laquo;'
            }
        },
        'serverSide': true,
        'ajax': {
            'url': '<?php echo BASE_URL; ?>api.php?data=product&act=dt',
            'type': 'POST'
        },
				'select': {
						'style': 'multi'
				},
				'columnDefs': [
						{
								'targets': 0,
								'checkboxes': {
									 'selectRow': true
								}
						}
				],
        'columns': [
						{'data': 'id'},
            {'data': 'id'},
            {'data': 'nama'},
            {'data': 'nama_struk'},
            {
							'data': 'harga_satuan',
							'render': function(data, type, row, meta) {
								return 'Rp' + formatDecToCurrency(parseFloat(data));
							}
						},
            {
							'data': 'harga_grosir',
							'render': function(data, type, row, meta) {
								if (data == '[]') return '';
								let parser = JSON.parse(data);
								let cb = '';
								for(iFor = 0; iFor < parser.length; iFor++) {
									/*cb += 'Minimal Pembelian: ' + parser[iFor][0] + '<br>';
									cb += 'Maximal Pembelian: ' + parser[iFor][1] + '<br>';
									cb += 'Harga Jadi: Rp.' + formatDecToCurrency(parseFloat(parser[iFor][2])) + '<hr>';*/
									cb += 'Harga min '+ parser[iFor][0] +' - '+
												parser[iFor][1] +' pcs : Rp' + formatDecToCurrency(parser[iFor][2]) + '<br>';
								}
								return cb;
							}
						},
            {
                'data': 'id', 'orderable': false,
                'searchable': 'false', 'render': function(data, type, row, meta) {
										let elTransfer = '<a href="<?php echo BASE_URL; ?>products/transfer/'+ data +'"><img src="<?php echo BASE_URL.PATH_IMG; ?>shuffle.svg"></a>';
										let elDetail = '<a href="<?php echo BASE_URL; ?>products/detail/'+ data +'"><img src="<?php echo BASE_URL.PATH_IMG; ?>search-1.svg"></a>';
										let elEdit = '<a href="<?php echo BASE_URL; ?>products/edit/'+ data +'"><img src="<?php echo BASE_URL.PATH_IMG; ?>edit.svg"></a>';
										let elDel = '<img src="<?php echo BASE_URL.PATH_IMG; ?>trash.svg" onClick="del('+ data +');">';
                    //console.log(data); console.log(type); console.log(row); console.log(meta);
                    //$('#user tr:nth-child(' + (meta.row + 1) + ') td:nth-child(' + (meta.col + 1) + ')').attr('data-id');
                    return elTransfer +' '+ elDetail +' '+ elEdit +' '+ elDel;
                }
            }
        ],
        'order': [[1, 'asc']],
        'orderMulti': false
    });
		viewer.on('select', function(ev, dt, type, index) {
			console.log($(this));
			console.log(ev);
			console.log(dt);
			console.log(type);
			console.log(index);
		});
});
</script>
