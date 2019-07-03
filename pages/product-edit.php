<?php
    if (!isset($nodirect)) die('nope');

    $title = "Produk Baru";
    $state = 0; //0 = tambah; 1 = update;

		switch (strtolower($path[1])) {
			case 'edit':
					if (isset($_GET['id'])) {
							if (is_nan($_GET['id'])) break;
					} else break;
					$db['query'] = $db['con'] -> prepare('SELECT * FROM produk WHERE id=:id');
					$db['query'] -> execute([
							':id' => $_GET['id']
					]);
					if ($db['query'] -> rowCount() > 0) {
							$val = $db['query'] -> fetchAll();
							$title = "Ubah Produk";
							$state = 1;
					}
					break;
			//case 'tambah':
			default:
		}
?>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>T-Shirt.svg" alt="">

        <?php echo $title; ?>

        <div style="float: right">
            <a href="<?php echo BASE_URL; ?>products"><button class="btn">&laquo; Kembali</button></a>
        </div>
    </h2>

    <form action="" id="editor" style="margin-top: 20px;">
        <div class="page-45">
            <table class="commonTable">
                <tr>
                    <td style="width: 25%;">Nama Produk *</td>
                    <td style="width: 75%;"><input type="text" name="nama" minlength="<?php echo INPUT_PRODUK_NAMA_PRODUK_MIN; ?>" maxlength="<?php echo INPUT_PRODUK_NAMA_PRODUK_MAX; ?>" style="width: 45%; min-width: 150px;" required ></td>
                </tr>
                <tr>
                    <td>Nama Struk *</td>
                    <td><input type="text" name="nama_struk" minlength="<?php echo INPUT_PRODUK_NAMA_STRUK_MIN; ?>" maxlength="<?php echo INPUT_PRODUK_NAMA_STRUK_MAX; ?>" style="width: 45%; min-width: 150px;" required ></td>
                </tr>
                <tr>
                    <td>Keterangan </td>
                    <td><textarea name="keterangan" style="width: 100%; min-height: 60px;" maxlength="<?php echo INPUT_PRODUK_KETERANGAN_MAX; ?>" ></textarea></td>
                </tr>
                <tr>
                	<td><b>Harga</b></td>
                </tr>
                <tr>
                    <td>HPP (Rp)</td>
                    <td><input data-type="currency" type="text" name="harga_pokok" minlength="<?php echo INPUT_PRODUK_HARGA_MIN; ?>" maxlength="<?php echo INPUT_PRODUK_HARGA_MAX; ?>" style="width: 25%; min-width: 125px; text-align: right;" required ></td>
                </tr>
                <tr>
                    <td>Harga Ecer (Rp)</td>
                    <td><input data-type="currency" type="text" name="harga_ecer" minlength="<?php echo INPUT_PRODUK_HARGA_MIN; ?>" maxlength="<?php echo INPUT_PRODUK_HARGA_MAX; ?>" style="width: 25%; min-width: 125px; text-align: right;" required ></td>
                </tr>
                <tr>
                		<td>Harga Grosir (Rp)</td>
                		<td id="grosir-input">
                			<div id="grosir-row" data-cur="0">
												<input type="number" data-format="number" id="grosir-min" name="harga_grosir_min[0]" placeholder="Min"> <b>-</b>
												<input type="number" data-format="number" id="grosir-max" name="harga_grosir_max[0]" placeholder="Max">
												<input data-type="currency" type="text" id="grosir-price" name="harga_grosir[0]" maxlength="<?php echo INPUT_PRODUK_HARGA_MAX; ?>" placeholder="Harga"> &nbsp;
												<button type="button" class="btn2" id="grosir-add" >+</button>
												<button type="button" class="btn2" id="grosir-rem" >-</button>
                			</div>
                		</td>
                </tr>
            </table>
        </div><div class="page-10"></div><!--
     --><div class="page-45">
            <table class="commonTable">
                <tr>
                    <td style="width: 10%"><b>Foto</b></td>
                </tr>
            </table><br>
            <input type="file" style="display: none;" name="photos" accept="image/*" multiple>
            <button class="btn2" id="upload" type="button">Upload Foto &raquo;</button><br>
            <label class="desc-smol">Note: Foto 400px X 500px atau yang sebanding</label>
            <div id="photo-group" class="photos">

            </div>
        </div>
        <?php
            if ($state == 1) {
                ?>
        <input type="hidden" name="id" value="<?php echo $val[0]['id']; ?>">
                <?php
            }
        ?>
        <div class="bottom" style="float: right;">
            <a href="<?php echo BASE_URL; ?>products"><button type="button" class="btn">
                &laquo; Batal
            </button></a>
            <button class="btn" type="submit" id="btn-submit">
                Simpan & buat Variant &raquo;
            </button>
        </div>
        <div style="clear: both;"></div>
    </form>
</div>

<style>
	#grosir-input input[type="text"] {
		width: 40%; min-width: 100px; text-align: right;
	}
	#grosir-input input[type="number"] {
		width: 10%; min-width: 50px; text-align: center;
	}
	#grosir-row {
		line-height: 30px;
		display: block;
	}
</style>

<script>
let curGrosir = 0;
let files = [], submit, curPhoto = [];
let errImage = false, isChanged = false;

$(document).on('change', '#editor input, #editor textarea', function() {
	isChanged = true;
});

function delPhoto(id)	{
	isChanged = true;
	if ($('#photo-group div[data-file="'+ id +'"]').data('file') == undefined) {
		curPhoto.splice(curPhoto.indexOf(id), 1);
		$('#photo-group div[data-exist="'+ id +'"]').remove();
	} else {
		delete files[id];
		$('#photo-group div[data-file="'+ id +'"]').remove();
	}
}

//0: OK, 1: field grosir ra valid
function validProduct() {
	/*if (files.length == 0) {
		return 1;
	}*/
	let grosirRow = $('#grosir-input > div');
	$(grosirRow).each(function(id) {
		let cur = $(this).data('cur');
		if (($(this).children('#grosir-min').val() != '') ||
				($(this).children('#grosir-max').val() != '') ||
				($(this).children('#grosir-price').val() != '')
				) {
		}
	});
}

function newPhoto(exist, fName) {
	let divPhoto = $('<div><img class="close" src="<?php echo BASE_URL.PATH_IMG; ?>cross.svg"></div>');
	let imgProduk = document.createElement('img');
	let reader = new FileReader();
	let iMax = Math.max.apply(null, curPhoto);
	if (!isFinite(iMax)) iMax = 0;
	imgProduk.className = 'photo';
	//$(divPhoto).data('file', files.length - 1);
	imgProduk.addEventListener('error', function() {
		let idFile = $(this).parent().data('file');
		if (idFile != undefined) {
			if (!$.modal.isActive()) {
				showAlert('', 'Error, tidak dapat memuat gambar');
			}
			delPhoto(idFile);
		} else {
			delPhoto($(this).parent().data('exist'));
		}
	});

	if (typeof(exist) == 'object') {
		$(divPhoto).attr('data-file', (iMax + files.length) - 1);
		reader.onload = function() {
			imgProduk.src = reader.result;
		}
		reader.readAsDataURL(exist);
	} else {
		imgProduk.src = '<?php echo BASE_URL.PATH_IMG_PRODUCT; ?>' + fName;
		$(divPhoto).attr('data-exist', exist);
	}

	$(divPhoto).append(imgProduk);
	$('#photo-group').append($(divPhoto));
}

$(document).ready(function() {
    $('.menu > ol > li:nth-child(3)').addClass('menu-active');
    $('.menu > ol > li:nth-child(3) li:nth-child(1)').addClass('menu-active');

		$('#upload').click(function() {
			$('input[name="photos"]').trigger('click');
		});

		$(document).on('click', '#photo-group div .close', function() {
			//if ($('#photo-group div[data-file="'+ id +'"]').data('file') == undefined) {
			if ($(this).parent().data('exist') != undefined) {
				delPhoto($(this).parent().data('exist'));
			} else {
				delPhoto($(this).parent().data('file'));
			}
		});

		$('input[name="photos"]').change(function() {
			//Array.from(this.files).every(function(file) {
			for (let iFor = 0; iFor < this.files.length; iFor++) {
				let file = this.files[iFor];
				if (file.size > <?php echo INPUT_PRODUK_IMG_SIZE_MAX; ?>) {
					showAlert('', 'File tidak boleh melebihi <?php echo (INPUT_PRODUK_IMG_SIZE_MAX / 1024 / 1024);  ?>MB');
					return;
				} else {
					files.push(file);
					newPhoto(file);
				}
			};
		});

		function validGrosirHandler() {
			let datCur = $(this).parent().data('cur');
			let elMin = $('#grosir-row[data-cur="'+ datCur +'"] #grosir-min');
			let elMax = $('#grosir-row[data-cur="'+ datCur +'"] #grosir-max');
			let elPrice = $('#grosir-row[data-cur="'+ datCur +'"] #grosir-price');

			if ($(this).val() != '') {
				$(elMin).attr('required', true);
				$(elMax).attr('required', true);
				$(elPrice).attr('required', true);
			}
			if ($(elMin).val() == '' && $(elMax).val() == '' && ($(elPrice).val() == '' || $(elPrice).val() == '0,00') ) {
				$(elMin).attr('required', false);
				$(elMax).attr('required', false);
				$(elPrice).attr('required', false);
			}
			$(elMax).attr('min', $(elMin).val());
			$('#grosir-row[data-cur="'+ (datCur + 1) +'"] #grosir-min').attr('min', parseInt($(elMax).val()) + 1);
		}
		$(document).on('change', '#grosir-price', validGrosirHandler);
		$(document).on('change', '#grosir-min', validGrosirHandler);
		$(document).on('change', '#grosir-max', validGrosirHandler);

		$(document).on('click', '#grosir-rem', function() {
			let datCur = $(this).parent().data('cur');
			if (datCur == 0) {
				$('#grosir-row[data-cur="0"] input').val('');
			}
			else {
				$('#grosir-row[data-cur="'+ curGrosir +'"]').remove();
				curGrosir--;
				$('#grosir-row[data-cur="'+ curGrosir +'"] #grosir-add').css('display', 'inline-block');
				$('#grosir-row[data-cur="'+ curGrosir +'"] #grosir-rem').css('display', 'inline-block');
			}
		});

		$(document).on('click', '#grosir-add', function() {
			$('#grosir-row[data-cur="'+ curGrosir +'"] #grosir-add').css('display', 'none');
			$('#grosir-row[data-cur="'+ curGrosir +'"] #grosir-rem').css('display', 'none');
			let curMin = parseInt($('#grosir-row[data-cur="'+ curGrosir +'"] #grosir-max').val()) + 1;
			curGrosir++;
			let inpMin = '<input type="number" data-format="number" id="grosir-min" name="harga_grosir_min['+ curGrosir +']" placeholder="Min" min="'+ curMin +'">';
			let inpMax = '<input type="number" data-format="number" id="grosir-max" name="harga_grosir_max['+ curGrosir +']" placeholder="Max">';
			let inpHarga = '<input type="text" data-type="currency" id="grosir-price" name="harga_grosir['+ curGrosir +']" maxlength="<?php echo INPUT_PRODUK_HARGA_MAX; ?>" placeholder="Harga">';
			let btnAdd = '<button class="btn2" type="button" id="grosir-add" >+</button>';
			let btnRem = '<button class="btn2" type="button" id="grosir-rem" >-</button>';
			let parDiv = '<div id="grosir-row" data-cur="'+ curGrosir +'">'+
					inpMin + ' <b>-</b> ' + inpMax + ' ' + inpHarga + ' &nbsp; ' + btnAdd
					+ btnRem +'</div>';
			$('#grosir-input').append(parDiv);
		});

    <?php
        switch ($state) {
//update lawas
            case 1:
                ?>
		let url = '<?php echo BASE_URL; ?>api.php?data=product&act=edit';

		function preSubmit() {
			submit = new FormData(document.getElementById('editor'));
			submit.delete('photos');
			files = files.filter(function(el) {return el != null});
			files.forEach(function(el, key) {
				submit.set('photos['+ key +']', el);
			});
			submit.set('harga_pokok', formatCurToDec(submit.get('harga_pokok')) );
			submit.set('harga_ecer', formatCurToDec(submit.get('harga_ecer')) );
			for(let iFor = 0; iFor <= curGrosir; iFor++) {
				submit.set('harga_grosir['+ iFor +']', formatCurToDec(submit.get('harga_grosir['+ iFor +']')));
			}
			submit.set('photos-exist', JSON.stringify(curPhoto));
		}

		function cbSuccess(data) {
			if (isChanged == true) {
				showAlert('', 'Produk berhasil diubah!');
				$('#modal-public').off('modal:after-close');
				$('#modal-public').on('modal:after-close', function(ev, modal) {
						location.href = '<?php echo BASE_URL; ?>products/variant/' + data;
				});
			} else location.href = '<?php echo BASE_URL; ?>products/variant/' + data;
		}

    $('input[name="nama"]').val('<?php echo $val[0]['nama_produk']; ?>');
    $('input[name="nama_struk"]').val('<?php echo $val[0]['nama_struk']; ?>');
    $('input[name="harga_pokok"]').val(formatDecToCurrency(<?php echo $val[0]['harga_pp']; ?>));
    $('input[name="harga_ecer"]').val(formatDecToCurrency('<?php echo $val[0]['harga_ecer']; ?>'));
    $('textarea[name="keterangan"]').val('<?php echo nlTo($val[0]['keterangan'], '\\n')/*preg_replace('/(\r\n)|\r|\n/', '\\n', $val[0]['keterangan'])*/; ?>');

		let grosirParser = '<?php echo $val[0]['harga_grosir']; ?>';
		grosirParser = JSON.parse(grosirParser);
		$.each(grosirParser, function(id, val) {
			$('input[name="harga_grosir_min['+ curGrosir +']"]').val(val[0]);
			$('input[name="harga_grosir_max['+ curGrosir +']"]').val(val[1]);
			$('input[name="harga_grosir['+ curGrosir +']"]').val(formatDecToCurrency(val[2]));
			$('#grosir-add').trigger('click');
		});

		let fotoParser = '<?php echo $val[0]['foto']; ?>';
		fotoParser = JSON.parse(fotoParser);
		$.each(fotoParser, function(id, val) {
			curPhoto.push(val);
			newPhoto(val, '<?php echo $val[0]['id'] ?>-' + val);
		});

                <?php
                break;
//tambah anyar
            case 0:
            default:
                ?>
	let url = '<?php echo BASE_URL; ?>api.php?data=product&act=add';
		function preSubmit() {
			submit = new FormData(document.getElementById('editor'));
			submit.delete('photos');
			files = files.filter(function(el) {return el != null});
			files.forEach(function(el, key) {
				submit.set('photos['+ key +']', el);
				// TODO: Ngefix tambah produk
				//submit.set('photos-state['+ key +']', 'state opo');
			});
			submit.set('harga_pokok', formatCurToDec(submit.get('harga_pokok')) );
			submit.set('harga_ecer', formatCurToDec(submit.get('harga_ecer')) );
			for(let iFor = 0; iFor <= curGrosir; iFor++) {
				submit.set('harga_grosir['+ iFor +']', formatCurToDec(submit.get('harga_grosir['+ iFor +']')));
			}
		}

		function cbSuccess(id) {
			if (isChanged == true) {
				showAlert('', 'Produk baru berhasil ditambahkan!');
				$('#modal-public').off('modal:after-close');
				$('#modal-public').on('modal:after-close', function(ev, modal) {
						location.href = '<?php echo BASE_URL; ?>products/variant/' + id;
				});
			} else location.href = '<?php echo BASE_URL; ?>products/variant/' + id;
		}
                <?php
                break;
        }
    ?>

    $('#editor').submit(function(ev) {
			ev.preventDefault();
			preSubmit();
			$.ajax({
				url: url,
				method: 'POST',
				processData: false,
				contentType: false,
				data: submit,
				dataType: 'json',
				success: function(data, status) {
						if (data.status == 'success') {
								cbSuccess(data.id);
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
