<?php
    if (!isset($nodirect)) die('nope');
?>

<div class="page">
    <h2>
        <img src="<?php echo BASE_URL.PATH_IMG; ?>home.svg" alt="">
        Home
    </h2>
    <div class="page-30">
        <ul class="nav nav-bookcash">
        <?php
            $db['query'] = $db['con'] -> prepare('SELECT id, nama FROM kas');
            if ($db['query'] -> execute()) {
                $db['res'] = $db['query'] -> fetchAll();
                foreach ($db['res'] as $data) {
                ?>
                    <li data-id=<?php echo $data['id']; ?>>
                        <img src="<?php echo BASE_URL.PATH_IMG; ?>notebook-1.svg" alt="">
                        <?php echo $data['nama']; ?>
                    </li>
                <?php
                }
            }
        ?>
        </ul>
    </div><!--
 --><div class="page-10"></div><!--
 --><div class="page-60">
        <h4>Penjualan : <?php ?></h4>
        <div class="statistik">
            statistik later...
        </div>
        <ul class="nav nav-report">
            <li>
            <img src="<?php echo BASE_URL.PATH_IMG; ?>survey.svg" alt="">
                Laporan Kas</li><!--
        --><li>
            <img src="<?php echo BASE_URL.PATH_IMG; ?>survey.svg" alt="">
            Laporan Penjualan</li><!--
        --><li>
            <img src="<?php echo BASE_URL.PATH_IMG; ?>survey.svg" alt="">
            Laporan Laba Rugi</li><!--
        --><li>
            <img src="<?php echo BASE_URL.PATH_IMG; ?>survey.svg" alt="">
            Neraca</li>
        </ul>
    </div>
    <div>

    </div>
</div>

<script>
    $('.menu > ol > li:nth-child(1)').addClass('menu-active');
</script>
