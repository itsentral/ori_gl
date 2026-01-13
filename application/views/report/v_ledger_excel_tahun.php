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
	if ($bln_ledger > 0) {
		$nm_bln = $bln_ledger;
		if ($nm_bln == 1) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br>Periode : Januari " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 2) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Februari " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 3) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Maret " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 4) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : April " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 5) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Mei " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 6) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Juni " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 7) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Juli " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 8) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Agustus " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 9) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : September " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 10) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Oktober " . $thn_ledger . "</center></th></tr>";
		} elseif ($nm_bln == 11) {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : November " . $thn_ledger . "</center></th></tr>";
		} else {
			echo "<tr><th colspan='7' style='text-align:center;font-size:15px;'><center>LAPORAN LEDGER<br><br>Periode : Desember " . $thn_ledger . "</center></th></tr>";
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
				<td align="right"><?= round($saldo_awal); ?></td>
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
			$detail_jurnal	= $this->Report_model->get_detail_jurnal2_tahun($nokir_induk, $var_tgl_awal, $var_tgl_akhir);
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
					$current_saldo				= round($saldo_awal + $row_dj->debet - $row_dj->kredit);
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
					</tr>
			<?php
				}
			}else{
				$saldo_akhir = $saldo_awal;
			}
			?>

			<tr>
				<td colspan="4">Saldo Akhir -></td>
				<td align="right"><?= $sum_debet; ?></td>
				<td align="right"><?= $sum_kredit; ?></td>
				<td align="right"><?= $saldo_akhir; ?></td>
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