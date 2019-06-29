<?php
    if (!isset($nodirect)) die('nope');

?>
<form id="login" class="page-small">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>lock.svg" alt="">
        Login Page
    </h2>
    <!--input type="text" name="user" placeholder="Username / Email" pattern="[@._A-Za-z0-9]{3,200}" minlength="3" maxlength="200" required-->
    <input type="email" name="user" placeholder="Email" minlength="3" maxlength="200" required>
    <input type="password" name="pass" placeholder="Password" maxlength="200" minlength="5" required>
    <button id="btn-login" class="btn" type="submit">
        Login
    </button>
</form>

<!--div id="modal" class="modal">
    <div class="modal-head">

    </div>
    <div class="modal-text">
        Email / Password salah
    </div>
    <div class="modal-foot">
        <button class="btn" id="btn-ok">OK</button>
    </div>
</div-->

<script>
$(document).ready(function() {
		$('.menu ol li:nth-child(1)').addClass('menu-active');

    $('#login').submit(function(ev) {
        ev.preventDefault();
        $.ajax({
            url: '<?php echo BASE_URL; ?>api.php?data=admins&act=login',
            method: 'POST',
            beforeSend: function() {
                $('#btn-login').attr('disabled', true);
                $('#btn-login').html('...');
            },
            data: $('#login').serializeArray(),
            success: function(data, status) {
                if (data.status == 'success') {
                    $('#login h2 > img').attr('src', '<?php echo BASE_URL.PATH_IMG.'unlock.svg' ?>');
                    wait(2000).then( () => {
                        location.href = '<?php echo BASE_URL; ?>home';
                    });
                } else if (data.status == 'wrong') {
                    showAlert('', 'Email / Password Salah');
                    $('#modal-public').off('modal:after-close');
                    $('#modal-public').on('modal:after-close', function(ev, modal) {
                        $('input[name="user"]').focus();
                        $('#btn-login').attr('disabled', false);
                        $('#btn-login').html('Login');
                    });
                    /*$('#modal .modal-text').html('Email / Password salah');
                    $('#modal').css('left', 'calc( 50vw - '+ ($('#modal').width() / 2 + 25) +'px )' );
                    $('#modal').css('top', 'calc( 50vh - '+ ($('#modal').height() / 2 + 20) +'px )' );
                    $('#modal').css('display', 'block');*/
                } else if (data.status == 'error') {

                }
            },
            error: function() {
                $('#modal .modal-text').html('Terjadi kesalahan');
                $('#modal').css('left', 'calc( 50vw - '+ ($('#modal').width() / 2 + 25) +'px )' );
                $('#modal').css('top', 'calc( 50vh - '+ ($('#modal').height() / 2 + 20) +'px )' );
                $('#modal').css('display', 'block');
            },
        });
    });
});


</script>
