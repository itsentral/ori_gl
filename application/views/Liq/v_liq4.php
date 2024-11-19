<link rel="stylesheet" href="<?= base_url("assets/pdf/style.css"); ?>">
<?php
error_reporting(E_ALL & ~E_NOTICE);
?>
<div id="space"></div>
<!-- <table class="gridtable" width="100%"> -->
<div class="col-lg">
	<?php
	$kode_cabang	= $this->session->userdata('kode_cabang');
	$get_namacoa3	= $this->db->query("SELECT nama FROM COA WHERE kdcab='$kode_cabang' AND bln='$bln_aktif' AND thn='$thn_aktif' AND no_perkiraan LIKE '$nokir3%' AND level='3'")->row();

	$namacoa3					= $get_namacoa3->nama;
	$data3						= $nokir3 . "-00-00" . " ( " . $namacoa3 . " ) ";

	?>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<label class="control-label col-lg-7">COA Level 4 dari No. COA : <?= $data3 ?></label>
</div>
<table id="my-grid" class="table table-striped table-bordered table-hover" width="100%">
	<thead>
		<tr bgcolor='#0073B7'>
			<th style="color: white;">Nama COA</th>
			<th style="color: white;">No. COA</th>
			<th style="color: white;">Saldo Awal</th>
			<th style="color: white;">Debet</th>
			<th style="color: white;">Kredit</th>
			<th style="color: white;">Saldo Akhir</th>
			<th style="color: white;">Aksi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		if ($data_liq > 0) {
			foreach ($data_liq as $row) {
				$i++;
				$nokir_segmen1		= substr($row->no_perkiraan, 0, 4); // 1101-01-00
				$nokir_segmen2		= substr($row->no_perkiraan, 5, 2); // 01
				// $nokir		= str_replace("-", "_", $row->no_perkiraan); // 1101-01-00
				$sa			= $row->saldoawal;

				$debet		= $row->debet;
				$kredit		= $row->kredit;
				$saldoakhir = $row->saldoakhir;

		?>
				<tr>
					<td><?= $row->nama ?></td>
					<td><?= $row->no_perkiraan ?></td>
					<td align="right"><?= number_format($sa, 0, ',', '.'); ?></td>
					<td align="right" width="12%"><?= number_format($debet, 0, ',', '.'); ?></td>
					<td align="right" width="12%"><?= number_format($kredit, 0, ',', '.'); ?></td>
					<td align="right" width="12%"><?= number_format($saldoakhir, 0, ',', '.'); ?></td>
					<td><button class='btn btn-info btn-sm' onclick="return detail5(<?= $nokir_segmen1 ?>,<?= $nokir_segmen2 ?>)">
							View Detail <i class="fa fa-search"></i>
						</button></td>
				</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>
<div class="modal fade" id="Mymodal5">
	<div class="modal-dialog modal-dialog-scrollable" style="width:90%; height:1000px">
		<div class="modal-content">
			<div class="modal-body" id="Mymodal-list5">

			</div>
			<div class="modal-footer">
				<button class='btn btn-secondary' onclick="return detail4(<?= $nokir3 ?>)">
					Close</button>
				<!-- <a href="<?= base_url() ?>index.php/Ledger_in_query/list_liq4_backlink/<?= $nokir3 ?>" class="btn btn-success" role="button">Close</a> -->
				<!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	//var active_controller = '<?php echo ($this->uri->segment(1)); ?>';

	function detail4(nokir) {
		$.ajax({
			url: base_url + 'index.php/' + active_controller + '/list_liq4/' + nokir,
			cache: false,
			type: "GET",
			success: function(data) {
				$('#Mymodal-list4').html(data);
				$("#Mymodal4").modal('show');


			}
		});
	}

	function detail5(nokir_segmen1, nokir_segmen2) {
		$.ajax({
			url: base_url + 'index.php/Ledger_in_query/list_liq5/' + nokir_segmen1 + '/' + nokir_segmen2,
			cache: false,
			type: "GET",
			success: function(data) {
				$('#Mymodal-list5').html(data);
				$("#Mymodal5").modal('show');


			}
		});
	}
</script>