<style type="text/css">
	.str {
		mso-number-format: \@;
	}
</style>
<?php
error_reporting(E_ALL & ~E_NOTICE);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Laporan_Ledger_$bln_ledger-$thn_ledger.xls"); //ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda


?>
<table width='50%' border="1" cellpadding="5" cellspacing="0">
	<?php
	//
	$Arr_Bulan	= array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	if ($bln_ledger > 0) {
		$nm_bln = $bln_ledger;
		$Name_Bulan		= $Arr_Bulan[$nm_bln];
		echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br>Periode : ".ucwords(strtolower($Name_Bulan))." " . $thn_ledger . "</center></th></tr>";
		
	}
	
	$COA_Warehouse	= $this->Report_model->GetWarehouseCOa();
	$OK_Warehouse1	= $OK_Warehouse2 = 'N';
	if($COA_Warehouse){
		if(!empty($COA_Warehouse[$filter_nokir])){
			$OK_Warehouse1	= 'Y';
		}
		
		if(!empty($COA_Warehouse[$filter_nokir2])){
			$OK_Warehouse2	= 'Y';
		}
	}
	?>
	</tr>
	<!-- DATA DARI COA -->
	<?php
	//$count=0;
	$var_bulan = $this->uri->segment(3);
	$var_tahun = $this->uri->segment(4);
	$var_tgl_awal=date("Y-m-d", strtotime($var_tahun.'-'.$var_bulan.'-01'));
	$var_tgl_akhir=date("Y-m-t", strtotime($var_tgl_awal));
	if ($coa_sa > 0) {
		foreach ($coa_sa as $row_sa) {
			//$count++;
			$nokir_induk 	= $row_sa->no_perkiraan;
			$nama_perkiraan	= $row_sa->nama;
			$saldo_awal		= $row_sa->saldoawal;
	?>
			<tr>
				<td colspan=7><b>NAMA COA : <?= $nama_perkiraan ?></b></td>
			</tr>
			<tr>
				<td colspan=7><b>No. COA : <?= $nokir_induk ?></b></td>
			</tr>
			<tr>
				<td>
					<center><b>KETERANGAN</b></center>
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
			</tr>
			<tr>
				<td colspan="4">Saldo Awal -></td>
				<td></td>
				<td></td>
				<td align="right"><?= number_format($saldo_awal, 0); ?></td>
			</tr>
			<!-- DATA DARI JURNAL -->
			<?php
			$sum_debet = 0;
			$sum_kredit = 0;
			$saldo_akhir = 0;
			// $sum_debet = array();
			// $sum_kredit = array();
			// $nilai_debet = array();
			// $nilai_kredit = array();
			$detail_jurnal	= $this->Report_model->get_detail_jurnal2($nokir_induk, $var_tgl_awal, $var_tgl_akhir);
			if ($detail_jurnal > 0) {
				$count2 = 0;
				foreach ($detail_jurnal as $row_dj) {
					$count2++;
					//$nokir 					= $row_dj->no_perkiraan;
					$nama_perkiraan2[$count2] 	= $row_dj->keterangan;
					$tgl_bukti[$count2]			= $row_dj->tanggal;
					$nomor_bukti[$count2] 		= $row_dj->nomor;
					$tipe_sm[$count2] 			= $row_dj->tipe;
					$nilai_debet[$count2] 		= $row_dj->debet;
					$nilai_kredit[$count2] 		= $row_dj->kredit;
					$sum_debet					+= $nilai_debet[$count2];
					$sum_kredit 				+= $nilai_kredit[$count2];
					$current_saldo				= $saldo_awal + $row_dj->debet - $row_dj->kredit;
					$saldo_awal					= $current_saldo;
					$saldo_akhir				= $current_saldo;
			?>
					<tr>
						<td><?= $nama_perkiraan2[$count2] ?></td>
						<td align="center"><?= date_format(new DateTime($tgl_bukti[$count2]), "d-m-Y") ?></td>
						<td align="center"><?= $nomor_bukti[$count2] ?></td>
						<td align="center"><?= $tipe_sm[$count2] ?></td>
						<td align="right"><?= $nilai_debet[$count2]; ?></td>
						<td align="right"><?= $nilai_kredit[$count2]; ?></td>
						<td align="right"><?= $current_saldo; ?></td>
						<td></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
					</tr>
					<?php $nojur = $row_dj->nomor;
					      $nokir = $row_dj->no_perkiraan;
					$det = $this->db->query("select a.* FROM view_jurnal a 
					WHERE a.nomor='$nojur' AND a.no_perkiraan <> '$nokir'")->result(); 
					foreach ($det as $baris) { ?>
					<tr>
					    <td></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="center"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
						<td></td>
						<td align="center"><?= $baris->nomor ?></td>
						<td align="center"><?= "'".$baris->no_perkiraan ?></td>
						<td align="center"><?= $baris->nama ?></td>
						<td align="center"><?= $baris->keterangan ?></td>
						<td align="center"><?=$baris->debet ?></td>
						<td align="center"><?=$baris->kredit ?></td>
					</tr>
					<?php
					}
					?>
			<?php
				}
			}else{
				$saldo_akhir = $saldo_awal;
			}
			?>
			<tr>
				<td colspan="4">Saldo Akhir -></td>
				<td align="right"><?= number_format($sum_debet, 0); ?></td>
				<td align="right"><?= number_format($sum_kredit, 0); ?></td>
				<td align="right"><?= number_format($saldo_akhir, 0); ?></td>
			</tr>
			<tr>
				<td align="right" colspan="7"></td>
			</tr>
	<?php
		}
	}
	//$count++;
	?>
</table>