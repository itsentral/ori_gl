<?php $this->load->view('header');
error_reporting(E_ALL & ~E_NOTICE);
?>
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
						<form action="<?= base_url() ?>index.php/jurnal/filter_tgl_buk" method="post">
							<div class="col-xs-3">
								<b>Tampilkan Berdasarkan Tanggal :</b>
							</div>
							<br><br>
							<div class="col-xs-2">
								Dari Tanggal :<input type="text" class="form-control tanggal" size='1' id="tanggal" format="dd-mm-yyyy" data-date-format="dd-mm-yyyy" name="tanggal" value="<?=date("01-m-Y")?>" readonly>
							</div>
							<div class="col-xs-2">
								Sampai Tanggal :<input type="text" class="form-control tanggal" size='1' id="tanggal2" format="dd-mm-yyyy" data-date-format="dd-mm-yyyy" name="tanggal2" value="<?=date("t-m-Y")?>" readonly>
							</div>
							<br>
							<div class="col-xs-4">
								<input class="btn btn-success" type="button" name="view" value="View List">
								<input class="btn btn-success" type="submit" name="view" value="View List Excel" target="blank">
							</div>
						</form>
						<a href="<?= base_url() ?>index.php/jurnal/dana_keluar" class="btn btn-warning">Input Bukti Uang Keluar (BUK)</a>
						<!-- <a href="<?= base_url() ?>index.php/jurnal/multi_print_buk" class="btn btn-success">Multiprint</a> -->
					</div>

					<div class="box-body">
						<div class="box-body table-responsive no-padding">
							<table class="table table-bordered table-hover dataTable example1">
								<thead>
									<tr bgcolor='#9acfea'>
										<th>
											<center>NO. BUK</center>
										</th>
										<th>
											<center>TANGGAL</center>
										</th>
										<th>
											<center>METODE BAYAR</center>
										</th>
										<th>
											<center>UANG KELUAR DARI</center>
										</th>
										<th>
											<center>BAYAR KE</center>
										</th>
										<th>
											<center>NOTE</center>
										</th>
										<th>
											<center>JUMLAH (Rp.)</center>
										</th>
										<th>
											<center>NO REFF</center>
										</th>
										<th>
											<center>OPSI</center>
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
			</div>
			<div class="modal fade" id="modal-buk">
				<div class="modal-dialog modal-dialog-scrollable" style="width:90%; height:1000px">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="Mymodal-title">Detail Jurnal</h4>
						</div>
						<div class="modal-body" id="modal-buklist">

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div><!-- /.modal-content -->
				</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
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
	function view_detail(nomorjurnal) {
		$.ajax({
			url: base_url + 'index.php/jurnal/modal_detailbuk/' + nomorjurnal,
			cache: false,
			type: "GET",
			success: function(data) {
				$('#modal-buklist').html(data);
				$("#modal-buk").modal('show');
			}
		});
	}

	$(document).ready(function(){
		$('#spinnerx').hide();
		var tanggal = $('#tanggal').val();
		var tanggal2 = $('#tanggal2').val();
		DataTables(tanggal, tanggal2);

		$(document).on('change','#tanggal, #tanggal2', function(e){
			e.preventDefault();
			var tanggal = $('#tanggal').val();
			var tanggal2 = $('#tanggal2').val();
			DataTables(tanggal, tanggal2);
		});
	});

	function DataTables(tanggal=null, tanggal2=null){
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
				url : '<?= base_url() ?>index.php/jurnal/get_data_json_dana_keluar_new',
				type: "post",
				data: function(d){
					d.tanggal = tanggal,
					d.tanggal2 = tanggal2
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

	function batal(id) {
		$.get("<?= base_url() ?>index.php/jurnal/vmodal_batal_buk", {
			option: id
		}, function(data) {
			$('#show_stock').html(data);
			$('#myModal').modal('show');
		});
	}

	function validasi_hapus() {
		var dd = confirm("Yakin di Batalkan ?");
		if (dd == false) {
			return false;
		}
	}
</script>
<script>
	$(function() {
		$('.tanggal').datepicker({
			dateFormat: 'yyyy-mm-dd',autoclose: true,clearBtn: true
		});
	});
</script>