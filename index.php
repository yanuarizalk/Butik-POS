<?php
    $nodirect = true;
    include 'init.php';
    include 'controller.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Gravis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>jquery.dataTables.min.css"-->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>dataTables.checkboxes.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>jquery.modal.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>datepicker.min.css">
    <!--link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>iziModal.min.css"-->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo BASE_URL.PATH_CSS; ?>main.css">
    <!--Issue 4350, ojo nganggo iki
    	<script src="<?php echo BASE_URL.PATH_JS; ?>jquery-3.4.0.min.js"></script>-->
    <script src="<?php echo BASE_URL.PATH_JS; ?>jquery.min.js"></script>
    <script src="<?php echo BASE_URL.PATH_JS; ?>jquery.dataTables.min.js"></script>
    <script src="<?php echo BASE_URL.PATH_JS; ?>dataTables.checkboxes.min.js"></script>
    <script src="<?php echo BASE_URL.PATH_JS; ?>jquery.modal.min.js"></script>
    <script src="<?php echo BASE_URL.PATH_JS; ?>datepicker.min.js"></script>
    <script src="<?php echo BASE_URL.PATH_JS; ?>i18n/datepicker.en.js"></script>
    <!--script src="<?php echo BASE_URL.PATH_JS; ?>iziModal.min.js"></script-->
    <script src="<?php echo BASE_URL.PATH_JS; ?>main.js"></script>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="index.php"><img src="<?php echo BASE_URL.PATH_IMG; ?>logo.png" alt="Logo"></a>
        </div><!--
     --><div class="menu-mobile" id="mnu-secondary">
            <img src="<?php echo BASE_URL.PATH_IMG; ?>menu.svg" alt="">
        </div><!--
     --><div class="menu" id="mnu-primary">
            <ol>
            <?php
                view_menu();
            ?>
            </ol>
        </div>
    </div>
    <div class="content">
        <?php
            view_content();
        ?>
    </div>
    <div class="footer">
        &copy; 2019 <b>Akun.pro</b> - All Right Reserved
    </div>
    <div id="modal-public" class="modal">
        <div class="modal-head">

        </div>
        <div class="modal-text">

        </div>
        <div class="modal-foot">

        </div>
    </div>
</body>
</html>
