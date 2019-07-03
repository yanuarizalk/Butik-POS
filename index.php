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
	<?php
		include 'includes.php';
	?>
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
