<?php $this->load->view('header'); ?>
<section class="content-header">
	<h1>
		<?= $judul ?>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active"><?= $judul ?></li>
	</ol>
</section>
<section class="content-header">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="box-body">
						<form action="<?= base_url() ?>index.php/Latihan/list_jvcoz" method="post">
							<div class="col-xs-2">
								<select type="text" id="thn" name="thn" class="form-control getchange">
									<?php
									$thn = $thn_aktif;
									for ($i = 2022; $i <= date("Y")+1; $i++) {
										if ($thn == $i) {
											echo "<option selected value='$i'>$i</option>";
										} else {
											echo "<option value='$i'>$i</option>";
										}
									}
									?>
								</select>
							</div>
							<div class="col-xs-2">
								<select type="text" id="bln" name="bln" class="form-control getchange">
									<?php
									$nm_bulan = array('All', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
									$bln = $bln_aktif;
									for ($i = 0; $i <= 12; $i++) {
										if ($i == $bln) {
											echo "<option selected value='$i'>" . $nm_bulan[$i] . "</option>";
										} else {
											echo "<option value='$i'>" . $nm_bulan[$i] . "</option>";
										}
									}
									?>
								</select>
								&nbsp;&nbsp;&nbsp;
							</div>
						</form>
						<a href="<?= base_url() ?>index.php/jurnal/jvcost" class="btn btn-warning">Input JV</a>&nbsp;&nbsp;
					</div>

					<div class="box-body">
						<div class="box-body table-responsive no-padding">
							<table class="table table-bordered table-hover dataTable example1">
								<thead>
									<tr bgcolor='#9acfea'>
										<th>
											<center>Nomor</center>
										</th>
										<th>
											<center>Tanggal</center>
										</th>
										<th>
											<center>Keterangan</center>
										</th>
										<th>
											<center>Memo</center>
										</th>
										<th>
											<center>Nilai</center>
										</th>
										<th>
											<center>Action</center>
										</th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="show_stock">
				<div>
</section>
<?php $this->load->view('footer'); ?>
<link rel="stylesheet" href="../../plugins/timepicker/bootstrap-timepicker.min.css">
<script src="../../plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/bootstrap-clockpicker.min.css">
<!-- bootstrap datepicker -->
<script src="<?= base_url() ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= base_url() ?>dist/js/bootstrap-clockpicker.min.js"></script>

<!-- <script src="<?= base_url() ?>dist/jquery.min.js"></script> -->
<script type="text/javascript" src="<?= base_url(); ?>dist/jquery.timepicker.min.js"></script>

<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/dataTables.bootstrap.min.js"></script>

<!-- SlimScroll -->
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		var thn = $('#thn').val();
		var bln= $('#bln').val();
		DataTables(thn, bln);

		$(document).on('change','.getchange', function(e){
			e.preventDefault();
			var thn = $('#thn').val();
			var bln= $('#bln').val();
			DataTables(thn, bln);
		});
	});
	function DataTables(thn=null, bln=null){
		var dataTable = $('.example1').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 0, "desc" ]],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : '<?= base_url() ?>index.php/jurnal/get_data_json_list_jv_new',
				type: "post",
				data: function(d){
					d.thn = thn,
					d.bln = bln
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$(".my-grid").append('<tbody class="my-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$(".my-grid_processing").css("display","none");
				}
			}
		});
	}
</script>
