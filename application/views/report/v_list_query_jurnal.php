<?php $this->load->view('header');
error_reporting(E_ALL & ~E_NOTICE);
?>
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/jquery.timepicker.css">
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
					<div class="box-body" style="background-color:lightblue">
						<form action="<?= base_url() ?>index.php/report/filter_query_jurnal" method="post">
							<table>
								<tr>
									<td width="20%">
										<b>Cari Berdasarkan</b>
									</td>

									<td width="20%">
										<select name="filter_by" id="filter_by" class="form-control input-sm">
											<!-- <option value="" selected>-Pilih Level-</option> -->
											<option value="no_reff">No. Reff</option>
											<option value="nomor">No. Jurnal</option>
											<option value="no_perkiraan">No. Perkiraan</option>
											<option value="keterangan">Keterangan</option>
											<option value="tipe">Tipe</option>
										</select>
									</td>
									<td width="5%">

									</td>
									<td width="20%">
										<input type="text" class="form-control input-sm" size='1' id="filter_text" name="filter_text" value="" required>
									</td>
								</tr>
								<tr>
									<td><br></td>
									<td><br></td>
									<td><br></td>
									<td><br></td>
								</tr>
								<tr>
									<td>
										<b>Tanggal Jurnal</b>
									</td>
									<td>
										<input type="text" class="form-control tanggal" size='1' id="tanggal" format="dd-mm-yyyy" data-date-format="dd-mm-yyyy" name="tanggal" value="<?=date("01-m-Y")?>" readonly>
									</td>
									<td width="5%">
										&nbsp; s/d
									</td>
									<td>
										<input type="text" class="form-control tanggal" size='1' id="tanggal2" format="dd-mm-yyyy" data-date-format="dd-mm-yyyy" name="tanggal2" value="<?=date("t-m-Y")?>" readonly>
									</td>
									<td>&nbsp;&nbsp;</td>
									<td>
										<input class="btn btn-success" type="button" name="cari" value="CARI">
									</td>
									<td>&nbsp;&nbsp;</td>
									<td>
										<input class="btn btn-success" type="submit" name="cari" value="EXPORT TO EXCEL">
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div class="box-body">
						<div class="box-body table-responsive no-padding">
							<table class="table table-bordered table-hover dataTable example1">
								<thead>
									<tr bgcolor='#9acfea'>
										<th>
											<center>Tipe</center>
										</th>
										<th>
											<center>Tanggal</center>
										</th>
										<th>
											<center>No. Jurnal</center>
										</th>
										<th>
											<center>No. COA</center>
										</th>
										<th>
											<center>Keterangan</center>
										</th>
										<th>
											<center>No. Reff</center>
										</th>
										<th>
											<center>Debit</center>
										</th>
										<th>
											<center>Kredit</center>
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
		</div>
	</div>
	<div id="show_stock"></div>
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
<script type="text/javascript" src="<?= base_url(); ?>dist/jquery.timepicker.min.js"></script>

<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/dataTables.bootstrap.min.js"></script>

<!-- SlimScroll -->
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		var filter_text = $('#filter_text').val();
		var filter_by = $('#filter_by').val();
		var tanggal = $('#tanggal').val();
		var tanggal2 = $('#tanggal2').val();
		DataTables(filter_text, filter_by, tanggal, tanggal2);

		$(document).on('change','#filter_text, #filter_by, #tanggal, #tanggal2', function(e){
			e.preventDefault();
			var filter_text = $('#filter_text').val();
			var filter_by = $('#filter_by').val();
			var tanggal = $('#tanggal').val();
			var tanggal2 = $('#tanggal2').val();
			DataTables(filter_text, filter_by, tanggal, tanggal2);
		});
	});

	function DataTables(filter_text=null, filter_by=null, tanggal=null, tanggal2=null){
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
			"aaSorting": [[ 2, "desc" ]],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : '<?= base_url() ?>index.php/report/get_data_json_query_jurnal',
				type: "post",
				data: function(d){
					d.filter_text = filter_text,
					d.filter_by = filter_by,
					d.tanggal = tanggal,
					d.tanggal2 = tanggal2
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$(".my-grid").append('<tbody class="my-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$(".my-grid_processing").css("display","none");
				}
			}
		});
	}

	$(function() {
		$('.tanggal').datepicker({
			dateFormat: 'yyyy-mm-dd',autoclose: true,clearBtn: true
		});
	});
</script>