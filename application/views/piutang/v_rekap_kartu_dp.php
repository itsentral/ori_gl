<?php $this->load->view('header');
error_reporting(E_ALL & ~E_NOTICE);
$Arr_Coa		= array();
$Arr_Project	= array();
if ($data_perkiraan) {
	foreach ($data_perkiraan as $key => $vals) {
		$kode_Coa			= $vals->no_perkiraan . '^' . $vals->nama;
		$Arr_Coa[$kode_Coa]	= $vals->no_perkiraan . '  ' . $vals->nama;
	}
}
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
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
					<b>PERIODE : </b><br><br>
            <!-- /.box-header -->
           <!-- <div class="box-body table-responsive no-padding"> -->
						<form method="post" action="<?=base_url()?>index.php/kartu_piutang/rekap_kartu_dp" autocomplete="off">
              <table class="table table-bordered">			 
								<tr>
									<td width="15%" align="right"><b>Periode Awal</b></td>
									<td width="15%">
									 <input type="text" id="tgl_awal" name="tgl_awal"  value="<?=$datawal?>" class="datepicker" /> 
                                    </td>
									<td width="15%">
									</td>
									<td width="15%">																			 
									</td>
								</tr>
								<tr>
									<td width="15%" align="right"><b>Periode Akhir</b></td>
									<td width="15%">
									<input type="text" id="tgl_akhir" name="tgl_akhir" value="<?=$datakhir?>" class="datepicker" />	
                                    </td>
									<td width="15%">																			 
									</td>
									<td width="15%">																			 
									</td>
								</tr>
								<tr>
									<td width="15%" align="right"><b>Tipe</b></td>
									<td width="15%">
									<select name="tipe">
										<?php
											foreach($combo_coa as $keys => $val){
												$selected='';
												if($tipe==$val) $selected=' selected';
												echo '<option value="'.$val.'"'.$selected.'>'.$keys.'</option>';
											}
										?>
									</select>
                                    </td>
									<td width="15%">
									</td>
									<td width="15%">
									</td>
								</tr>

					 <tr>
					<td width="15%" align="right"></td>
					<td width="25%" align="left">
						<input type="submit" name="tampilkan" value="Tampilkan" onclick="return check()" class="btn btn-success pull-center">                             
					    <input type="submit" name="tampilkan" value="View Excel" onclick="return check()" class="btn btn-success pull-center">
					
					</td>
					
				</tr>
				
				</table>
			  
			  </form>
            
        </div>
    </div>
    </div>
</section>

<section class="content-header">

	<div class="col-xs-12">

		<div class="box">

			<div class="row">

				<div class="box-header">

					<div class="box-body">

						<div class="box-body table-responsive no-padding">

							<table class="table table-bordered table-hover dataTable example1">

								<tbody>

									<tr>
										<?php
										
										$awal 		= $datawal;
										$akhir		= $datakhir;
																			
																	
										echo "<tr><th colspan='8' style='text-align:center;font-size:15px;'><center>REKAP KARTU DP CUSTOMER<br>Periode :  " . date_format(new DateTime($awal), "d-m-Y") . " S/D ". date_format(new DateTime($akhir), "d-m-Y") ."</center></th></tr>";
										
										?>
									</tr>

									<tr>
									    <td>
										<center><b>Kode Customer</b></center>
										</td>
										<td>
											<center><b>Nama Customer</b></center>
										</td>
										<td>
											<center><b>Saldo Awal</b></center>
										</td>
										
										<td>
											<center><b>Debet</b></center>
										</td>
										<td>
											<center><b>Kredit</b></center>
										</td>
										<td>
											<center><b>Saldo Akhir</b></center>
										</td>
									</tr>
									
									<?php
									
										$count = 0;
										foreach ($vendor as $vend) {
											$count++;
     										$id_supplier   = $vend->id_supplier;
											$nm_supplier   = $vend->nama_supplier;
											$tipe		= $tipe;
											
											$detail_jurnal	= $this->Kartupiutang_model->get_rekap_kartu_piutang($awal,$akhir,$id_supplier,$tipe);
											
											if ($detail_jurnal > 0) {
												$count2 = 0;
												$count3 = 0;

												foreach ($detail_jurnal as $row_dj) {
													$count2++;
													$count3++;
													$saldo_awal	= $row_dj->saldo_awal;
													$debet 	= $row_dj->debet;
													$kredit	= $row_dj->kredit;
													$saldo_akhir	= $row_dj->saldo_akhir;
													$salakh			= $saldo_awal+$saldo_akhir;
													
													$tot_saldoawal    += $saldo_awal;
													$tot_debet        += $debet;
													$tot_kredit       += $kredit;
													$tot_saldoakhir   += $saldo_akhir;
													$tot_salakh       = $tot_saldoawal + $tot_saldoakhir;
									?>
																									
												<tr>
												<td><a href="<?=base_url("kartu_piutang/dtl_invoice/".$id_supplier."/".$datakhir)?>" target="_blank"><?= $id_supplier ?></a> </td>
												<td><?= $nm_supplier ?> </td>
												<td align="right"><?= number_format($saldo_awal, 2, ',', '.'); ?></td>
												<td align="right"><?= number_format($debet, 2, ',', '.'); ?></td>
												<td align="right"><?= number_format($kredit, 2, ',', '.'); ?></td>
												<td align="right"><?= number_format($salakh, 2, ',', '.'); ?></td>
												</tr>
												
												
									<?php
									
												}
												
											}
											
										}
									
									?>
									         <tr>
												<td> </td>
												<td></td>
												<td align="right"><b><?= number_format($tot_saldoawal, 2, ',', '.'); ?></td>
												<td align="right"><b><?= number_format($tot_debet, 2, ',', '.'); ?></td>
												<td align="right"><b><?= number_format($tot_kredit, 2, ',', '.'); ?></td>
												<td align="right"><b><?= number_format($tot_salakh, 2, ',', '.'); ?></td>
											</tr>


								</tbody>

							</table>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</section>
<?php $this->load->view('footer'); ?>

<link rel="stylesheet" href="<?= base_url() ?>plugins/datepicker/datepicker3.css">
<script src="<?= base_url() ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= base_url() ?>dist/moment.min.js"></script>
<script>
	function check() {
		if ($("#bulan_ledger").val() == "0") {
			alert("Silahkan Pilih Bulan");
			return false;
		} else if ($("#tahun_ledger").val() == "0") {
			alert("Silahkan Pilih Tahun");
			return false;
		} else if ($("#filter_nokir").val() == "0") {
			alert("Silahkan Pilih Dari Nomor Perkiraan Mana");
			return false;
		} else if ($("#filter_nokir2").val() == "0") {
			alert("Silahkan Pilih Sampai Nomor Perkiraan Mana");
			return false;
		}
	}
	
	$(function() {
		$('.datepicker').datepicker({
			format   : 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true
		});

		$('#datepicker2').datepicker({
			format   : 'yyyy-mm-dd',
		});
	});
</script>