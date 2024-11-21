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
						<?php
						$data_header = $this->db->query("SELECT * FROM javh WHERE nomor='$nomor_jurnal'")->row();
						?>
						<form action="<?= base_url() ?>index.php/report/filter_query_jurnal" method="post">
							<table width="100%">
								<tr>
									<td width="15%">
										<b>No. Jurnal</b>
									</td>

									<td width="40%">
										<input type="text" class="form-control input-sm" size='1' id="filter_text" name="filter_text" value="<?= $data_header->nomor ?>" readonly>
									</td>

									<td width="5%">

									</td>
									<td width="15%">
										<b>Koreksi Data No. </b>
									</td>
									<td width="20%">
										<input type="text" class="form-control input-sm" size='1' id="filter_text" name="filter_text" value="<?= $data_header->koreksi_no ?>" readonly>
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
										<b>Keterangan</b>
									</td>

									<td>
										<input type="text" class="form-control input-sm" size='1' id="filter_text" name="filter_text" value="<?= $data_header->keterangan ?>" readonly>
									</td>
									<td></td>
									<td>
										<b>Tanggal</b>
									</td>
									<td>
										<input type="text" class="form-control input-sm" size='1' id="filter_text" name="filter_text" value="<?= date("d-m-Y", strtotime($data_header->tgl)) ?>" readonly>
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div class="box-body">
						<div class="box-body table-responsive no-padding">
							<table class="table table-bordered table-hover">
								<thead>
									<tr bgcolor='#9acfea'>
										<!-- <th>
											<center>Tanggal</center>
										</th>
										<th>
											<center>Nomor Jurnal</center>
										</th> -->
										<th>
											<center>Id</center>
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
										<th>
											<center>Debit Kurs</center>
										</th>
										<th>
											<center>Kredit Kurs</center>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i = 0;
									$sum_debet = 0;
									$sum_kredit = 0;
									if ($list_data > 0) {
										$no = 0;
										foreach ($list_data as $row) {
											$no++;
											$id = $row->id;
											$sum_debet	+= $row->debet;
											$sum_kredit	+= $row->kredit;

											$format_debet = number_format($row->debet, 0, ',', '.');
											$format_kredit = number_format($row->kredit, 0, ',', '.');

											$format_debet_kurs = number_format($row->nilai_valas_debet, 2, ',', '.');
											$format_kredit_kurs = number_format($row->nilai_valas_kredit, 2, ',', '.');

											$format_sumdebet = number_format($sum_debet, 0, ',', '.');
											$format_sumkredit = number_format($sum_kredit, 0, ',', '.');

											$periode_jv	= $row->tanggal;
											$bln_jv		= substr($row->tanggal, 5, 2); // 2019-11-20
											$thn_jv		= substr($row->tanggal, 0, 4); // 2019-11-20
									?>
											<tr bgcolor='#DCDCDC'>
												<!-- <td align="center"><?= date_format(new DateTime($row->tanggal), "d-m-Y") ?></td>
												<td align="center"><?= $row->nomor ?></td>
												<td align="center"><?= $row->tipe ?></td> -->
												<td align="center"><input id="id" name="id" value="<?= $row->id ?>" readonly></td>
												<td><?= $row->no_perkiraan.' | '.$row->nama ?></td>
												<td><?= $row->keterangan ?></td>
												<td align="center"><?= $row->no_reff ?></td>
												<td align="right"><input id="debet_<?= $row->id ?>" name="debet_<?= $row->id ?>" value="<?= $format_debet ?>" onblur="edit('<?php echo $row->id?>','<?= $row->nomor ?>')"></td>
												<td align="right"><input id="kredit_<?= $row->id ?>" name="kredit_<?= $row->id ?>" value="<?= $format_kredit ?>" onblur="edit('<?php echo $row->id?>','<?= $row->nomor ?>')"></td>
												<td align="right"><input id="debet_kurs_<?= $row->id ?>" name="debet_kurs_<?= $row->id ?>" value="<?= $format_debet_kurs ?>" onblur="edit('<?php echo $row->id?>','<?= $row->nomor ?>')"></td>
												<td align="right"><input id="kredit_kurs_<?= $row->id ?>" name="kredit_kurs_<?= $row->id ?>" value="<?= $format_kredit_kurs ?>" onblur="edit('<?php echo $row->id?>','<?= $row->nomor ?>')"></td>
											</tr>
									<?php
										}
									}
									// else {
									// 	$format_sumdebet = 0;
									// 	$format_sumkredit = 0;
									// }
									?>

									<tr bgcolor='#DCDCDC'>
										<td colspan="4" align="right"><b>TOTAL</b></td>
										<td align="right"><b><?= $format_sumdebet ?></b></td>
										<td align="right"><b><?= $format_sumkredit ?></b></td>
									</tr>
									<tr bgcolor='#DCDCDC'>
										<td colspan="8" align="left">
											<form action="<?= base_url() ?>index.php/Latihan/list_jvcoz" method="post">
											<input type="hidden" name="thn" value="<?=$thn_jv?>" />
											<input type="hidden" name="bln" value="<?=date("n",strtotime($periode_jv))?>" />
											<input type="submit" class="btn btn-success" value="Kembali" />
											</form>
										</td>
									</tr>
									<?php
									// 	}
									// }
									// else {
									// 	$format_sumdebet = 0;
									// 	$format_sumkredit = 0;
									// }
									?>
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
<script src="<?= base_url(); ?>dist/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>dist/jquery.timepicker.min.js"></script>

<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/dataTables.bootstrap.min.js"></script>

<!-- SlimScroll -->
<script>
	$(function() {
		$(".example1").DataTable({
			"ordering": true, // Set true agar bisa di sorting
			"order": [
				[0, 'desc']
			] // Default sortingnya berdasarkan kolom / field ke 0 (paling pertama)
		});
	});
</script>

<script>
	$(function() {
		$('#datepicker').datepicker({
			dateFormat: 'yyyy-mm-dd'
		});

		$('#datepicker2').datepicker({
			dateFormat: 'yyyy-mm-dd'
		});
	});
	
	
	function edit(id,nomor){
		var debet  =$('#debet_'+id).val();
		var kredit =$('#kredit_'+id).val();
		var debetkurs  =$('#debet_kurs_'+id).val();
		var kreditkurs =$('#kredit_kurs_'+id).val();
		
		swal({
          title: "Peringatan !",
          text: "Pastikan data benar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Ya, simpan!",
          cancelButtonText: "Batal!",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
			if(isConfirm) {
				$.ajax({
					url: base_url+"index.php/jurnal/save_edit_jv",
					dataType : "json",
					type: 'POST',
					data: "id="+id+"&debet="+debet+"&kredit="+kredit+"&debetkurs="+debetkurs+"&kreditkurs="+kreditkurs,
					success: function(data){
						if(data.status == 1){
						swal({
						  title	: "Save Success!",
						  text	: data.pesan,
						  type	: "success",
						  timer	: 15000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
						window.location.href = base_url +'index.php/jurnal/edit_jv/'+nomor;
					  }else{

						if(data.status == 2){
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}else{
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}

					  }
					},
					error: function(){
						swal({
							title: "Gagal!",
							text: "Batal Proses, Data bisa diproses nanti",
							type: "error",
							timer: 1500,
							showConfirmButton: false
						});
					}
				});
			}
        });
		
	}
</script>