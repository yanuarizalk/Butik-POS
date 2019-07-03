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
			<li data-id="0">
				<img src="<?php echo BASE_URL.PATH_IMG; ?>notebook-5.svg" alt="">
				Buku Penjualan
			</li>
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
		<h3 id="info-sales"></h3><br>
		<div class="statistik">
			<div id="stats">
				
			</div>
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

<style>
	#stats {
		max-width: 800px;
		width: 100%;
		height: 300px;
	}
	#info-sales {
		color: var(--text-orange);
	}
</style>

<script>
	$(document).ready(function () {
		$('.menu > ol > li:nth-child(1)').addClass('menu-active');
		let stats = [], dt = new Date();
		$('#info-sales').html('Penjualan : '+ dt.toLocaleDateString('en', {month: 'long'}) +' '+ dt.getFullYear() );
		$.ajax({
			url: '<?php echo BASE_URL; ?>api.php?data=stats&act=all_sales',
			method: 'POST',
			data: {
				'range': 'month',
				'month': dt.getMonth() + 1,
				'year': dt.getFullYear()
			},
			success: function(data, status) {
				if (data.status == 'success') {
					for (let iFor in data.stats.data) {
						data.stats.data[iFor][0] = parseInt(data.stats.data[iFor][0]);
					}
					$.plot($('#stats'), [data.stats], {
						series: {
							bars: {
								show: true,
								fill: true,
								fillColor: 'rgb(245,135,60)',
								barWidth: 0.6
							}
						},
						xaxis: {
							mode: 'time',
							timeBase: 'seconds',
							timeformat: '%d',
							tickSize: [1, 'day']
						},
						yaxis: {
							tickFormatter: function (val, axis) {
								return 'Rp '+ formatDecToCurrency(val);
							}
						}
					});
				} else if (data.status == 'error') {
					showAlert('', data.desc);
					console.log(data.msg);
				}
			}, error: function(xhr, status) {
				
			}
		});
	});
</script>
