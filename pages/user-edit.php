<?php
    if (!isset($nodirect)) die('nope');

    $title = "Tambah Pengguna";
    $state = 0; //0 = tambah; 1 = update;

    //if (isset($_GET['act'])) {
        //switch (strtolower($_GET['act'])) {
        switch (strtolower($path[2])) {
            case 'edit':
                if (isset($_GET['id'])) {
                    if (is_nan($_GET['id'])) break;
                } else break;
                $db['query'] = $db['con'] -> prepare('SELECT * FROM users WHERE id=:id');
                $db['query'] -> execute([
                    ':id' => $_GET['id']
                ]);
                if ($db['query'] -> rowCount() > 0) {
                    $val = $db['query'] -> fetchAll();
                    $title = "Edit Pengguna";
                    $state = 1;
                }
                break;
            //case 'tambah':
            default:
                //$state = 0; break;
                //$title = 'Tambah Pengguna'; break;

        }
    //}
?>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>manager.svg" alt="">

        <?php echo $title; ?>

        <div style="float: right">
            <a href="<?php echo BASE_URL; ?>settings/users"><button class="btn">&laquo; Kembali</button></a>
        </div>
    </h2>

    <form action="" id="users">
        <div style="display: inline-block; width: 49%;">
            <table class="commonTable">
                <tr>
                    <td style="width: 25%;">Nama *</td>
                    <td style="width: 75%;"><input type="text" name="nama" pattern="[ A-Za-z]{<?php echo INPUT_USER_NAMA_MIN; ?>,<?php echo INPUT_USER_NAMA_MAX; ?>}" minlength="<?php echo INPUT_USER_NAMA_MIN; ?>" maxlength="<?php echo INPUT_USER_NAMA_MAX; ?>" style="width: 30%; min-width: 150px;" required ></td>
                </tr>
                <tr>
                    <td>Email *</td>
                    <td><input type="email" name="email" minlength="<?php echo INPUT_EMAIL_MIN; ?>" maxlength="<?php echo INPUT_EMAIL_MAX; ?>" style="width: 45%; min-width: 150px;" required ></td>
                </tr>
                <?php
                    if ($state == 1) {
                        ?>
                <tr>
                    <td>Password Lama *</td>
                    <td><input type="password" name="pass0" minlength="<?php echo INPUT_USER_PASS_MIN; ?>" maxlength="<?php echo INPUT_USER_PASS_MAX; ?>" style="width: 45%; min-width: 150px;" required ></td>
                </tr>
                        <?php
                    }
                ?>
                <tr>
                    <td>Password *</td>
                    <td><input type="password" name="pass1" minlength="<?php echo INPUT_USER_PASS_MIN; ?>" maxlength="<?php echo INPUT_USER_PASS_MAX; ?>" style="width: 25%; min-width: 125px;" required ></td>
                </tr>
                <tr>
                    <td>Ulangi Password *</td>
                    <td><input type="password" name="pass2" minlength="<?php echo INPUT_USER_PASS_MIN; ?>" maxlength="<?php echo INPUT_USER_PASS_MAX; ?>" style="width: 25%; min-width: 125px;" required ></td>
                </tr>
                <tr>
                    <td>Akses Buku Kas *</td>
                    <td>
                        <?php
                            $db['query'] = $db['con'] -> prepare('SELECT id, nama FROM kas');
                            $db['query'] -> execute();
                            $db['res'] = $db['query'] -> fetchAll();
                            foreach ($db['res'] as $data) {
                                ?>
                        <input type="checkbox" class="access_kas" name="kas[<?php echo $data['id']; ?>]" value="oh yeah :v" id=""><label id="label_kas" for="kas[<?php echo $data['id']; ?>]">Kas <?php echo $data['nama']; ?></label><br>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Akses Penjualan *</td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>Keterangan </td>
                    <td><textarea name="keterangan" style="width: 100%; min-height: 60px;" maxlength="<?php echo INPUT_USER_KETERANGAN_MAX; ?>" ></textarea></td>
                </tr>
            </table>
        </div><!--
     --><div style="display: inline-block; vertical-align: top; width: 49%; margin: 0 0 0 auto; float: right;">
            <table class="commonTable">
                <tr>
                    <td style="width: 10%">Peran</td>
                    <td>
                        <select name="access" id="">
                            <option value="Administrator">Administrator</option>
                            <option value="Moderator">Moderator</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <?php
            if ($state == 1) {
                ?>
        <input type="hidden" name="id" value="<?php echo $val[0]['id']; ?>">
                <?php
            }
        ?>
        <div class="bottom">
            <a href="<?php echo BASE_URL; ?>settings/users"><button type="button" class="btn">
                &laquo; Batal
            </button></a>
            <button class="btn" type="submit" id="btn-submit">
                Simpan
            </button>
        </div>
    </form>

</div>
&#13;
<script>
$(document).ready(function() {
    $('.menu > ol li:nth-child(9)').addClass('menu-active');

    <?php
        switch ($state) {
//update lawas
            case 1:
                ?>
    $('input[name="nama"]').val('<?php echo $val[0]['nama']; ?>');
    $('input[name="email"]').val('<?php echo $val[0]['email']; ?>');
    $('textarea[name="keterangan"]').val('<?php echo preg_replace('/(\r\n)|\r|\n/', '\\n', $val[0]['keterangan']); ?>');
    $('select[name="access"]').val('<?php echo $val[0]['access']; ?>');

    $('#users').submit(function(ev) {
        ev.preventDefault();
        if ($('input[name="pass1"]').val() != $('input[name="pass2"]').val() ) {
            showAlert('', 'Password baru tidak sama<br>Mohon cek kembali');
            $('#modal-public').off('modal:after-close');
            $('#modal-public').on('modal:after-close', function(ev, modal) {
                $('input[name="pass1"]').focus();
            });
            return;
        }
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=admins&act=edit',
            method: 'POST',
            data: $('#users').serializeArray(),
            dataType: 'json',
            success: function(data, status) {
                if (data.status == 'success') {
                    showAlert('', 'Data user berhasil diubah!');
                    $('#modal-public').off('modal:after-close');
                    $('#modal-public').on('modal:after-close', function(ev, modal) {
                        location.href = '<?php echo BASE_URL; ?>settings/users';
                    });
                } else if (data.status == 'error') {
                    showAlert('', data.desc);
                    console.log(data.msg);
                }
            }, error: function(xhr, status) {

            }
        });
    });
    <?php
        foreach (json_decode($val[0]['access_kas'], true) as $kas) {
            if ($kas == '*') {
                ?>
    $('.access_kas').each(function(index, elm) {
        $(elm).attr('checked', true);
    });
                <?php
                break;
            }
            ?>
    $('input[name="kas[<?php echo $kas; ?>]"]').attr('checked', true);

            <?php
        }
    ?>
                <?php
                break;
//tambah anyar
            case 0:
            default:
                ?>
    $('#users').submit(function(ev) {
        ev.preventDefault();
        if ($('input[name="pass1"]').val() != $('input[name="pass2"]').val() ) {
            showAlert('', 'Password tidak sama<br>Mohon cek kembali');
            $('#modal-public').off('modal:after-close');
            $('#modal-public').on('modal:after-close', function(ev, modal) {
                $('input[name="pass1"]').focus();
            });
            return;
        }
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=admins&act=add',
            method: 'POST',
            data: $('#users').serializeArray(),
            dataType: 'json',
            success: function(data, status) {
                if (data.status == 'success') {
                    showAlert('', 'Data user berhasil ditambahkan!');
                    $('#modal-public').off('modal:after-close');
                    $('#modal-public').on('modal:after-close', function(ev, modal) {
                        location.href = '<?php echo BASE_URL; ?>settings/users';
                    });
                } else if (data.status == 'error') {
                    showAlert('', data.desc);
                    console.log(data.msg);
                }
            }, error: function(xhr, status) {

            }
        });
    });
                <?php
                break;
        }
    ?>

    $('#users').submit(function(ev) {
        /*ev.preventDefault();
        $.ajax({
            url: 'api.php?data=admins&'
        });*/
    });
});
</script>
