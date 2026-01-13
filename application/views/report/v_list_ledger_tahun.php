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
//echo BASEPATH;
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
				<form method="post" action="<?= base_url() ?>index.php/report/tampilkan_ledger_tahun" autocomplete="off">
					<table class="table table-bordered">
						<tr>
							<td width="25%" align="right" hidden><b>Bulan</b></td>
							<td hidden>
								<select type="text" name="bulan_ledger" class="form-control">
									<?php
									$nm_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
									$bulan = @$this->input->post('bulan_ledger');
									if (empty($bulan)) {
										// $bulan = date("m") + 0;
										$bulan = $bln_ledger;
									}
									for ($i = 1; $i <= 12; $i++) {
										if ($i == $bulan) {
											echo "<option selected value='$i'>" . $nm_bulan[$i] . "</option>";
										} else {
											echo "<option value='$i'>" . $nm_bulan[$i] . "</option>";
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="25%" align="right"><b>Tahun</b></td>
							<td>
								<select type="text" name="tahun_ledger" class="form-control">
									<?php
									$tahun = @$this->input->post('tahun_ledger');
									if (empty($tahun)) {
										// $tahun = date("Y") + 0;
										$tahun = $thn_ledger;
									}
									for ($i = date("Y") - 8; $i <= date("Y") + 2; $i++) {
										if ($tahun == $i) {
											echo "<option selected value='$i'>$i</option>";
										} else {
											echo "<option value='$i'>$i</option>";
										}
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td width="25%"><b>Pilih Dari Nomor COA</b></td>
							<td>
								<select name="filter_nokir" id="filter_nokir" class="form-control input-sm">
									<option value="<?= $filter_nokir ?>" selected><?= str_replace("^", " ", $filter_nokir) ?></option>
									<?php
									foreach ($Arr_Coa as $key => $row2) {
										echo "<option value='" . $key . "'>" . $row2 . "</option>";
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="25%"><b>Pilih Sampai Nomor COA</b></td>
							<td>
								<select name="filter_nokir2" id="filter_nokir2" class="form-control input-sm">
									<option value="<?= $filter_nokir2 ?>" selected><?= str_replace("^", " ", $filter_nokir2) ?></option>
									<?php
									foreach ($Arr_Coa as $key => $row2) {
										echo "<option value='" . $key . "'>" . $row2 . "</option>";
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="25%" align="right"></td>
							<td width="25%" align="left">
								<input type="submit" name="tampilkan" value="Tampilkan" onclick="return check()" class="btn btn-success pull-center">
								<input type="submit" name="tampilkan" value="View Excel" onclick="return check()" class="btn btn-success pull-center">
							</td>
						</tr>
					</table>
					<!-- <a href="<?= base_url() ?>index.php/report/print_ledger" class="btn btn-warning" target="_blank">CETAK</a> -->
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
										//
										
										$Periode_Awal	= date('Y-m-01');
										$Periode_Akhir	= date('Y-m-d');
										$Arr_Bulan	= array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
										if ($bln_ledger > 0) {
											$Periode_Cari	 = date('Y-m',mktime(0,0,0,$bln_ledger,1,$thn_ledger));
											if($Periode_Cari < date('Y-m')){
												$Periode_Awal = date('Y-m-d',mktime(0,0,0,$bln_ledger,1,$thn_ledger));
												$Periode_Akhir = date('Y-m-t',mktime(0,0,0,$bln_ledger,1,$thn_ledger));
											}
											
											
											$nm_bln = $bln_ledger;
											$Name_Bulan		= $Arr_Bulan[$nm_bln];
											echo "<tr><th colspan='8' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br>Periode : ". $thn_ledger . "</center></th></tr>";
											
										}else{
											$Periode_Awal = date('Y-m-d',mktime(0,0,0,1,1,$thn_ledger));
											if($thn_ledger < date('Y')){
												$Periode_Akhir = date('Y-m-t',mktime(0,0,0,12,1,$thn_ledger));
											}
										}
										
										//$COA_Warehouse	= $this->Report_model->GetWarehouseCOa();
										$COA_Warehouse	= $this->Report_model->GetCategoryCoaLedger();
										
										
										?>
									</tr>
									<tr>
										<td>
											<center><b>Nama COA</b></center>
										</td>
										<td>
											<center><b>No. COA</b></center>
										</td>
										<td>
											<center><b>Tanggal Bukti</b></center>
										</td>
										<td>
											<center><b>Nomor Bukti</b></center>
										</td>
										<td>
											<center><b>SM</b></center>
										</td>
										<td>
											<center><b>Debet</b></center>
										</td>
										<td>
											<center><b>Kredit</b></center>
										</td>
										<td>
											<center><b>Saldo</b></center>
										</td>
										<td>
											<center><b>Debet Kurs</b></center>
										</td>
										<td>
											<center><b>Kredit Kurs</b></center>
										</td>
										<td>
											<center><b>Saldo Kurs</b></center>
										</td>
									</tr>
									<!-- DATA DARI COA -->
									<?php
									//$count=0;
									if ($coa_sa > 0) {
										$count = 0;
										foreach ($coa_sa as $row_sa) {
											$count++;
											$nokir_induk 		= $row_sa->no_perkiraan;
											$nama_perkiraan		= $row_sa->nama;
											$saldo_awal[$count]	= $row_sa->saldoawal;
											$saldo_awal_kurs[$count]	= $row_sa->saldo_valas;
											
											$OK_Warehouse		= 'N';
											$Categori_Sub		= '';
											if(!empty($COA_Warehouse[$nokir_induk])){
												$OK_Warehouse	= 'Y';
												$Categori_Sub	= $COA_Warehouse[$nokir_induk];
											}
											
											$Temp_SaldoAwal	= number_format($saldo_awal[$count], 0, ',', '.');
											$Temp_SaldoAwalKurs	= number_format($saldo_awal_kurs[$count], 2, ',', '.');
											$DateFR			= date('Y-m-d', strtotime($Periode_Awal. ' - 1 days'));
											if($OK_Warehouse == 'Y' && $DateFR >= $cutoff_stock && floatval($row_sa->saldoawal) !== 0 && !empty($row_sa->saldoawal)){
												$Code_SaldoAwal	= $row_sa->no_perkiraan.'^'.$DateFR;
												$Temp_SaldoAwal	= '<a href="#" class="text-red" onClick="PreviewDetail({code:\''.$Code_SaldoAwal.'\',kategori:\''.$Categori_Sub.'\',action:\'preview_detail_material_stock\',title:\'PREVIEW DETAIL SALDO AWAL\'});"> '.number_format($saldo_awal[$count], 0, ',', '.').' </a>';
													
											}
									?>
											<tr>
												<td><?= $nama_perkiraan ?></td>
												<td align="center"><?= $nokir_induk ?></td>
												<td align="right" colspan="3">Saldo Awal -></td>
												<td></td>
												<td></td>
												<!-- <td align="right"><?= number_format($saldo_awal[$count]); ?></td> -->
												<td align="right"><?= $Temp_SaldoAwal; ?></td>
												<td></td>
												<td></td>
												<!-- <td align="right"><?= number_format($saldo_awal[$count],2, ',', '.'); ?></td> -->
												<td align="right"><?= $Temp_SaldoAwalKurs; ?></td>
											</tr>
											<!-- DATA DARI JURNAL -->
											<?php
											$sum_debet = 0;
											$sum_kredit = 0;
											$sum_debet = array();
											$sum_kredit = array();
											$nilai_debet = array();
											$nilai_kredit = array();
											$sum_debet_kurs = 0;
											$sum_kredit_kurs = 0;
											$sum_debet_kurs = array();
											$sum_kredit_kurs = array();
											$nilai_debet_kurs = array();
											$nilai_kredit_kurs = array();
											$detail_jurnal	= $this->Report_model->get_detail_jurnal2_tahun($nokir_induk, $var_tgl_awal, $var_tgl_akhir);
											if ($detail_jurnal > 0) {
												$count2 = 0;
												$count3 = 0;
												foreach ($detail_jurnal as $row_dj) {
													$count2++;
													$count3++;
													//$nokir 					= $row_dj->no_perkiraan;
													$nama_perkiraan2[$count2] 	= $row_dj->keterangan;
													$tgl_bukti[$count2]			= $row_dj->tanggal;
													$reff[$count2] 		        = $row_dj->no_reff;
													$nomor_bukti[$count2] 		= $row_dj->nomor;
													$tipe_sm[$count2] 			= $row_dj->tipe;
													$nilai_debet[$count2] 		= $row_dj->debet;
													$nilai_kredit[$count2] 		= $row_dj->kredit;
													$sum_debet[$count]	 		+= $nilai_debet[$count2];
													$sum_kredit[$count]  		+= $nilai_kredit[$count2];
													$current_saldo[$count3]		= $saldo_awal[$count] + $nilai_debet[$count2] - $nilai_kredit[$count2];
													$saldo_akhir				= $current_saldo[$count3];

													$nilai_debet_kurs[$count2] 		= $row_dj->nilai_valas_debet;
													$nilai_kredit_kurs[$count2] 	= $row_dj->nilai_valas_kredit;
													$sum_debet_kurs[$count]	 		+= $nilai_debet_kurs[$count2];
													$sum_kredit_kurs[$count]  		+= $nilai_kredit_kurs[$count2];
													$current_saldo_kurs[$count3]	= $saldo_awal_kurs[$count] + $nilai_debet_kurs[$count2] - $nilai_kredit_kurs[$count2];
													$saldo_akhir_kurs				= $current_saldo_kurs[$count3];
													
													// if ((isset($sum_debet[$count]))  == "" || (isset($sum_kredit[$count])) == "" || (isset($nilai_debet[$count2]))  == "" || (isset($nilai_kredit[$count2])) == "") {
													// 	$sum_debet[$count]	 		+= $nilai_debet[$count2];
													// 	$sum_kredit[$count]  		+= $nilai_kredit[$count2];
													// } else {
													
													//}
													//$current_saldo[$count3]	= $saldo_awal[$count];
													//$current_saldo[$count2]	+= $current_saldo[$count2] + $nilai_debet[$count2] - $nilai_kredit[$count2];
													// $saldo_akhir				= $sum_debet + $saldo_awal[$count] - $sum_kredit;	
													$Code_Unik				= $row_dj->nomor.'^'.$row_dj->tipe;
													$Template_Preview		= '<a href="#" class="text-red" onClick="PreviewDetail({code:\''.$Code_Unik.'\',kategori:\'\',action:\'preview_detail_jurnal_new\',title:\'PREVIEW DETAIL JURNAL\'});"> '.$row_dj->nomor.' </a>';
													
													$Template_Reff			= $row_dj->no_reff;
													$Jenis_Trans			= 'D';
													if($row_dj->kredit > 0){
														$Jenis_Trans		= 'K';
													}
													
													$Tgl_Trans				= $row_dj->tanggal;
													if($OK_Warehouse == 'Y'){
														$Code_Reff			= $row_dj->no_reff.'^'.$row_dj->nomor.'^'.$nokir_induk.'^'.$Jenis_Trans.'^'.$Tgl_Trans;
														$Template_Reff		= '<a href="#" class="text-orange" onClick="PreviewDetail({code:\''.$Code_Reff.'\',kategori:\''.$Categori_Sub.'\',action:\'preview_detail_jurnal_reff\',title:\'PREVIEW DETAIL JURNAL REFERENSI\'});"> '.$row_dj->no_reff.' </a>';													
													}
													?>
													<tr>
														<td><?= $nama_perkiraan2[$count2] ?></td>
														<td><?= $row_dj->no_perkiraan ?></td>
														<td align="center"><?= date_format(new DateTime($tgl_bukti[$count2]), "d-m-Y")  ?></td>
														<td align="center"><?= $Template_Preview; ?></td>
														<td align="center"><?= $Template_Reff ?></td>
														<td align="right"><?= number_format($nilai_debet[$count2], 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($nilai_kredit[$count2], 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($current_saldo[$count3], 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($nilai_debet_kurs[$count2], 2, ',', '.'); ?></td>
														<td align="right"><?= number_format($nilai_kredit_kurs[$count2], 2, ',', '.'); ?></td>
														<td align="right"><?= number_format($current_saldo_kurs[$count3], 2, ',', '.'); ?></td>
													</tr>
											<?php
													$saldo_awal[$count] = $current_saldo[$count3];
													$saldo_awal_kurs[$count] = $current_saldo_kurs[$count3];
												}
											} else {
												$saldo_akhir				= $saldo_awal[$count];
												$saldo_akhir_kurs				= $saldo_awal_kurs[$count];
											}
											
											$Temp_SaldoAkhir	= number_format($saldo_akhir, 0, ',', '.');
											$Temp_SaldoAkhirKurs	= number_format($saldo_akhir_kurs, 2, ',', '.');
											
											if($OK_Warehouse == 'Y' && $Periode_Akhir >= $cutoff_stock && floatval($saldo_akhir) !== 0 && !empty($saldo_akhir)){
												$Code_SaldoAkhir	= $row_sa->no_perkiraan.'^'.$Periode_Akhir;
												$Temp_SaldoAkhir	= '<a href="#" class="text-red" onClick="PreviewDetail({code:\''.$Code_SaldoAkhir.'\',kategori:\''.$Categori_Sub.'\',action:\'preview_detail_material_stock\',title:\'PREVIEW DETAIL SALDO AKHIR\'});"> '.number_format($saldo_akhir, 0, ',', '.').' </a>';
													
											}
											?>
											<tr>
												<td></td>
												<td></td>
												<td align="right" colspan="3">Saldo Akhir -></td>
												<td align="right"><?= number_format($sum_debet[$count], 0, ',', '.'); ?></td>
												<td align="right"><?= number_format($sum_kredit[$count], 0, ',', '.'); ?></td>
												<td align="right"><?= $Temp_SaldoAkhir; ?></td>
												<td align="right"><?= number_format($sum_debet_kurs[$count], 2, ',', '.'); ?></td>
												<td align="right"><?= number_format($sum_kredit_kurs[$count], 2, ',', '.'); ?></td>
												<td align="right"><?= $Temp_SaldoAkhirKurs; ?></td>
											</tr>
											<tr>
												<td></td>
												<td></td>
												<td align="right" colspan="3"></td>
												<td align="right"></td>
												<td align="right"></td>
												<td align="right"></td>
												<td align="right"></td>
												<td align="right"></td>
												<td align="right"></td>
											</tr>
									<?php
											//							$current_saldo[$count3]=0;
										}
									}
									//$count++;
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="v_modal_preview" tabindex="-1" role="dialog" aria-labelledby="MyModal" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		 <div class="modal-content" id="body_modal_preview">
			
		</div>
	</div>
</div>
<?php $this->load->view('footer'); ?>
<link rel="stylesheet" href="<?= base_url() ?>plugins/datepicker/datepicker3.css">
<style>
	.modal {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 1050;
		display: none;
		width: 100%;
		height: 100%;
		overflow: hidden;
		outline: 0
	} 
	
	@media (min-width: 768px) {
		.modal-lg,.modal-xl {
			min-width:980px
		}
	}
	
	@media (min-width: 992px) {
		.modal-lg,.modal-xl {
			min-width:986px
		}
	}

	@media (min-width: 1200px) {
		.modal-lg, .modal-xl {
			min-width:1140px
		}
	}
</style>
<script src="<?= base_url() ?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?= base_url() ?>dist/moment.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
	var site_url_pros            		= '<?php echo site_url(); ?>';
    var active_controller_pros   		= '<?php echo($this->uri->segment(1)); ?>';
	
	const PreviewDetail = (ObjectParam)=>{
		let CodeAction		= ObjectParam.code;
		let CategoryAction	= ObjectParam.kategori;
		let LinkAction 		= ObjectParam.action;
		
		$("#v_modal_preview").modal('hide');	
		loading_spinner();				
        $.post(site_url_pros +'/'+ active_controller_pros+'/'+LinkAction,{'code':CodeAction,'kategori':CategoryAction}, function(response) {
			close_spinner(); 
            $("#body_modal_preview").html(response);
			$("#v_modal_preview").modal('show');		
        });
	}
	
	const DownloadStock = (no_coa,tgl_stok,kategori)=>{
		let Link_Download	= site_url_pros +'/'+ active_controller_pros+'/download_stok_material?coa='+encodeURIComponent(no_coa)+'&tgl='+encodeURIComponent(tgl_stok)+'&kategori='+encodeURIComponent(kategori);
		window.open(Link_Download,'_blank');
	}
	
	
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
</script>