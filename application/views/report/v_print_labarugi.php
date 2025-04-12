<style type="text/css">
	@page {
		margin-top: 0.5cm;
		margin-bottom: 0.5cm;
		margin-left: 1cm;
		margin-right: 1cm;
	}

	.font {
		font-family: verdana, arial, sans-serif, tahoma;
		font-size: 14px;
	}

	.fontheader {
		font-family: verdana, arial, sans-serif;
		font-size: 14px;
	}

	table.gridtable2 {
		font-family: verdana, arial, sans-serif;
		font-size: 11px;
		color: #333333;
		border-width: thin;
		border-color: #666666;
		border-collapse: collapse;
	}

	table.gridtable2 th {

		padding: 10px;
		border-style: solid;
		background-color: #ffffff;
		font-family: tahoma;
		font-size: 11px;
	}

	table.gridtable2 td {
		border-width: 1px;
		padding: 8px;
		border-style: solid;
		border-color: #666666;
		background-color: #ffffff;
		font-family: verdana, arial, sans-serif;
		font-size: 10px;
	}
</style>
<table width="100%">

	<tr class="widget-user-header bg-yellow">
		<th width="20%" style="text-align:left; border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
			<!-- <img src="<?= base_url() ?>/assets/logo duta_transparan2.png" height="80" width="100"> -->
		</th>

		<?php
		$nocab = $this->session->userdata('nomor_cabang');
		$cek_cabang = $this->db->query("SELECT nocab,namacabang FROM pastibisa_tb_cabang WHERE nocab='$nocab'")->row();
		$nama_cabang = $cek_cabang->namacabang;

		if ($data_bulan_post > 0) {
			$nm_bln = $data_bulan_post;
			if ($nm_bln == 1) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Januari " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 2) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Februari " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 3) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Maret " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 4) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : April " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 5) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Mei " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 6) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Juni " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 7) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Juli " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 8) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Agustus " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 9) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : September " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 10) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Oktober " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} elseif ($nm_bln == 11) {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : November " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			} else {
				echo "<th width='60%' style='text-align:center;font-size:15px;'><center>INCOME STATEMENT LEVEL " . $level . "<br>Periode : Desember " . $data_tahun_post . "<br>" . $nama_cabang . "</center></th>";
			}
		}

		function tgl_indo($tanggal)
		{
			$hari = date('l');
			if ($hari == "Monday") {
				$hari = "Senin";
			} elseif ($hari == "Tuesday") {
				$hari = "Selasa";
			} elseif ($hari == "Wednesday") {
				$hari = "Rabu";
			} elseif ($hari == "Thursday") {
				$hari = "Kamis";
			} elseif ($hari == "Friday") {
				$hari = "Jumat";
			} elseif ($hari == "Saturday") {
				$hari = "Sabtu";
			} else {
				$hari = "Minggu";
			}

			$tgl = date('d');

			$bulan = date('F');
			if ($bulan == "January") {
				$bulan = "Januari";
			} elseif ($bulan == "February") {
				$bulan = "Februari";
			} elseif ($bulan == "March") {
				$bulan = "Maret";
			} elseif ($bulan == "April") {
				$bulan = "April";
			} elseif ($bulan == "May") {
				$bulan = "Mei";
			} elseif ($bulan == "June") {
				$bulan = "Juni";
			} elseif ($bulan == "July") {
				$bulan = "Juli";
			} elseif ($bulan == "August") {
				$bulan = "Agustus";
			} elseif ($bulan == "September") {
				$bulan = "September";
			} elseif ($bulan == "November") {
				$bulan = "November";
			} else {
				$bulan = "Desember";
			}

			$tahun = date('Y');
			$jam = date('H:i:s');

			return $hari . ", " . $tgl . " " . $bulan . " " . $tahun . " " . $jam;
		}
		?>
		<td width="20" style="font-size:11px; text-align:right; border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= tgl_indo(date('l, d F Y H:i:s')); ?></td>
	</tr>
</table>

<table class="gridtable2" width="100%">

	<tr>
		<td style="border-right:none;">
			<b>Account Code</b>
		</td>
		<td style="border-right:none; border-left-style:none;">
			<b>Description</b>
		</td>
		<td style="border-right:none; border-left-style:none;">
			<b>Previous (Year-To-Date)</b>
		</td>
		<td style="border-right:none; border-left-style:none;">
			<b>Current Month</b>
		</td>
		<td style="border-left-style:none;">
			<b>Year-To-Date</b>
		</td>
	</tr>
	<!-- PENDAPATAN -->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>41</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>REVENUE</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								$total_pYTD_pdptn = 0;
								$total_cmonth_pdptn = 0;
								$total_YTD_pdptn = 0;

								if ($data_nokir_pdptn > 0) {
									foreach ($data_nokir_pdptn as $row) {
										$nokir_pdptn				= $row->no_perkiraan;
										$nm_perkiraan_pdptn			= $row->nama;
										$v_faktor41					= $row->faktor;
										$pYTD_pdptn					= ($row->saldoawal) * $v_faktor41;
										$cmonth_pdptn				= ($row->debet - $row->kredit) * $v_faktor41;
										// $cmonth_pdptn				= ($row->kredit) - ($row->debet);
										$YTD_pdptn					= $pYTD_pdptn + $cmonth_pdptn;

										$total_pYTD_pdptn 	+=  $pYTD_pdptn;
										$total_cmonth_pdptn +=  $cmonth_pdptn;
										$total_YTD_pdptn 		+=  $YTD_pdptn;

										$rp_pYTD_pdptn			= "Rp. " . number_format($pYTD_pdptn, 0, ',', '.');
										$rp_cmonth_pdptn		= "Rp. " . number_format($cmonth_pdptn, 0, ',', '.');
										$rp_YTD_pdptn				= "Rp. " . number_format($YTD_pdptn, 0, ',', '.');

										$rp_total_pYTD_pdptn			= "Rp. " . number_format($total_pYTD_pdptn, 0, ',', '.');
										$rp_total_cmonth_pdptn		= "Rp. " . number_format($total_cmonth_pdptn, 0, ',', '.');
										$rp_total_YTD_pdptn				= "Rp. " . number_format($total_YTD_pdptn, 0, ',', '.');
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_pdptn ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_pdptn ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_pYTD_pdptn ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_cmonth_pdptn ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_YTD_pdptn ?></td>
										</tr>
								<?php
									}
								} else {
									$rp_total_pYTD_pdptn	= 0;
									$rp_total_cmonth_pdptn	= 0;
									$rp_total_YTD_pdptn		= 0;
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total PENDAPATAN</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_total_pYTD_pdptn ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_total_cmonth_pdptn ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_total_YTD_pdptn ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<!-- HPP -->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>50</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>COST OF GOODS SOLD</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>51</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>MATERIAL</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								$total_pYTD_hpp 	= 0;
								$total_cmonth_hpp = 0;
								$total_YTD_hpp 		= 0;
								$SubTotalHpp_pYTD			= 0;
								$SubTotalHpp_cmonth			= 0;
								$SubTotalHpp_YTD			= 0;

								$Saldo_pYTD = 0;
								$Saldo_cmonth = 0;
								$Saldo_YTD = 0;

								if ($data_nokir_hpp > 0) {
									foreach ($data_nokir_hpp as $row2) {
										$nokir_hpp 				= $row2->no_perkiraan;
										$nm_perkiraan_hpp	= $row2->nama;
										$v_faktor51					= $row2->faktor;
										$pYTD_hpp					= $row2->saldoawal * $v_faktor51;
										$cmonth_hpp				= ($row2->debet - $row2->kredit) * $v_faktor51;
										$YTD_hpp					= $pYTD_hpp + $cmonth_hpp;

										$SubTotalHpp_pYTD 	+=  $pYTD_hpp;
										$SubTotalHpp_cmonth +=  $cmonth_hpp;
										$SubTotalHpp_YTD 		+=  $YTD_hpp;

										$Saldo_pYTD		= ($total_pYTD_pdptn) - ($SubTotalHpp_pYTD);
										$Saldo_cmonth	= ($total_cmonth_pdptn) - ($SubTotalHpp_cmonth);
										$Saldo_YTD		= ($total_YTD_pdptn) - ($SubTotalHpp_YTD);

										$total_pYTD_hpp 	+=  $pYTD_hpp;
										$total_cmonth_hpp   +=  $cmonth_hpp;
										$total_YTD_hpp 		+=  $YTD_hpp;

										$rp_pYTD_hpp			= "Rp. " . number_format($pYTD_hpp, 0, ',', '.');
										$rp_cmonth_hpp		= "Rp. " . number_format($cmonth_hpp, 0, ',', '.');
										$rp_YTD_hpp				= "Rp. " . number_format($YTD_hpp, 0, ',', '.');

										$rp_total_pYTD_hpp			= "Rp. " . number_format($total_pYTD_hpp, 0, ',', '.');
										$rp_total_cmonth_hpp		= "Rp. " . number_format($total_cmonth_hpp, 0, ',', '.');
										$rp_total_YTD_hpp				= "Rp. " . number_format($total_YTD_hpp, 0, ',', '.');
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_hpp ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_hpp ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_pYTD_hpp ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_cmonth_hpp ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_YTD_hpp ?></td>
										</tr>
								<?php
									}
								} else {
									$SubTotalHpp_pYTD	= 0;
									$SubTotalHpp_cmonth	= 0;
									$SubTotalHpp_YTD	= 0;

									$Saldo_pYTD	= 0;
									$Saldo_cmonth	= 0;
									$Saldo_YTD	= 0;

									$rp_SubTotalHpp_pYTD	= 0;
									$rp_SubTotalHpp_cmonth	= 0;
									$rp_SubTotalHpp_YTD		= 0;

									$RpSaldo_pYTD	= 0;
									$RpSaldo_cmonth	= 0;
									$RpSaldo_YTD	= 0;
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total MATERIAL</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya51_pYTD		= "Rp. " . number_format($total_pYTD_hpp, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya51_cmonth		= "Rp. " . number_format($total_cmonth_hpp, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya51_YTD		= "Rp. " . number_format($total_YTD_hpp, 0, ',', '.'); ?></b></td>
						        </tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!-- HPP  52-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>52</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>CONSUMABLE</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

<?php

								$total_pYTD_biaya52 = 0;
								$total_cmonth_biaya52 = 0;
								$total_YTD_biaya52 = 0;

								if ($data_nokir_biaya52 > 0) {

									foreach ($data_nokir_biaya52 as $row52) {
										$nokir_biaya52 				= $row52->no_perkiraan;
										$nm_perkiraan_biaya52		= $row52->nama;
										$v_faktor52					= $row52->faktor;
										$pYTD_biaya52					= $row52->saldoawal * $v_faktor52;
										$cmonth_biaya52				= ($row52->debet - $row52->kredit) * $v_faktor52;
										$YTD_biaya52					= $pYTD_biaya52 + $cmonth_biaya52;

										$total_pYTD_biaya52 += $pYTD_biaya52;
										$total_cmonth_biaya52 += $cmonth_biaya52;
										$total_YTD_biaya52 += $YTD_biaya52;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya52 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya52 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya52, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya52, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya52, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total CONSUMABLE</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya52_pYTD		= "Rp. " . number_format($total_pYTD_biaya52, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya52_cmonth		= "Rp. " . number_format($total_cmonth_biaya52, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya52_YTD		= "Rp. " . number_format($total_YTD_biaya52, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 52 -->
								
								<!--HPP  53-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>53</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>DIRECT LABOUR</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

<?php

								$total_pYTD_biaya53 = 0;
								$total_cmonth_biaya53 = 0;
								$total_YTD_biaya53 = 0;

								if ($data_nokir_biaya53 > 0) {

									foreach ($data_nokir_biaya53 as $row53) {
										$nokir_biaya53 				= $row53->no_perkiraan;
										$nm_perkiraan_biaya53		= $row53->nama;
										$v_faktor53					= $row53->faktor;
										$pYTD_biaya53					= $row53->saldoawal * $v_faktor53;
										$cmonth_biaya53				= ($row53->debet - $row53->kredit) * $v_faktor53;
										$YTD_biaya53					= $pYTD_biaya53 + $cmonth_biaya53;

										$total_pYTD_biaya53 += $pYTD_biaya53;
										$total_cmonth_biaya53 += $cmonth_biaya53;
										$total_YTD_biaya53 += $YTD_biaya53;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya53 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya53 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya53, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya53, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya53, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total DIRECT LABOUR</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya53_pYTD		= "Rp. " . number_format($total_pYTD_biaya53, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya53_cmonth	= "Rp. " . number_format($total_cmonth_biaya53, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya53_YTD		= "Rp. " . number_format($total_YTD_biaya53, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 53 -->
								
								<!--HPP  54-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>54</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>INDIRECT LABOUR</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

<?php

								$total_pYTD_biaya54 = 0;
								$total_cmonth_biaya54 = 0;
								$total_YTD_biaya54 = 0;

								if ($data_nokir_biaya54 > 0) {

									foreach ($data_nokir_biaya54 as $row54) {
										$nokir_biaya54 				= $row54->no_perkiraan;
										$nm_perkiraan_biaya54		= $row54->nama;
										$v_faktor54					= $row54->faktor;
										$pYTD_biaya54					= $row54->saldoawal * $v_faktor54;
										$cmonth_biaya54				= ($row54->debet - $row54->kredit) * $v_faktor54;
										$YTD_biaya54					= $pYTD_biaya54 + $cmonth_biaya54;

										$total_pYTD_biaya54 += $pYTD_biaya54;
										$total_cmonth_biaya54 += $cmonth_biaya54;
										$total_YTD_biaya54 += $YTD_biaya54;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya54 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya54 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya54, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya54, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya54, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total INDIRECT LABOUR</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya54_pYTD		= "Rp. " . number_format($total_pYTD_biaya54, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya54_cmonth		= "Rp. " . number_format($total_cmonth_biaya54, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya54_YTD		= "Rp. " . number_format($total_YTD_biaya54, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 54 -->
								
								<!--HPP  55-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>55</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>DIRECT DEPRECIATION</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php

								$total_pYTD_biaya55 = 0;
								$total_cmonth_biaya55 = 0;
								$total_YTD_biaya55 = 0;

								if ($data_nokir_biaya55 > 0) {

									foreach ($data_nokir_biaya55 as $row55) {
										$nokir_biaya55 				= $row55->no_perkiraan;
										$nm_perkiraan_biaya55		= $row55->nama;
										$v_faktor55					= $row55->faktor;
										$pYTD_biaya55					= $row55->saldoawal * $v_faktor55;
										$cmonth_biaya55				= ($row55->debet - $row55->kredit) * $v_faktor55;
										$YTD_biaya55					= $pYTD_biaya55 + $cmonth_biaya55;

										$total_pYTD_biaya55 += $pYTD_biaya55;
										$total_cmonth_biaya55 += $cmonth_biaya55;
										$total_YTD_biaya55 += $YTD_biaya55;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya55 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya55 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya55, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya55, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya55, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total DIRECT DEPRECIATION</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya55_pYTD		= "Rp. " . number_format($total_pYTD_biaya55, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya55_cmonth		= "Rp. " . number_format($total_cmonth_biaya55, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya55_YTD		= "Rp. " . number_format($total_YTD_biaya55, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 55 -->
								
								<!--HPP  56-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>56</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>MOULDING DAN MANDRIL</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

<?php

								$total_pYTD_biaya56 = 0;
								$total_cmonth_biaya56 = 0;
								$total_YTD_biaya56 = 0;

								if ($data_nokir_biaya56 > 0) {

									foreach ($data_nokir_biaya56 as $row56) {
										$nokir_biaya56 				= $row56->no_perkiraan;
										$nm_perkiraan_biaya56		= $row56->nama;
										$v_faktor56					= $row56->faktor;
										$pYTD_biaya56					= $row56->saldoawal * $v_faktor56;
										$cmonth_biaya56				= ($row56->debet - $row56->kredit) * $v_faktor56;
										$YTD_biaya56					= $pYTD_biaya56 + $cmonth_biaya56;

										$total_pYTD_biaya56 += $pYTD_biaya56;
										$total_cmonth_biaya56 += $cmonth_biaya56;
										$total_YTD_biaya56 += $YTD_biaya56;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya56 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya56 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya56, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya56, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya56, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total MOULDING DAN MANDRIL</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya56_pYTD		= "Rp. " . number_format($total_pYTD_biaya56, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya56_cmonth		= "Rp. " . number_format($total_cmonth_biaya56, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya56_YTD		= "Rp. " . number_format($total_YTD_biaya56, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 56 -->

                                <!--HPP  57-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>57</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>LOGISTIC EXPENSE</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php

								$total_pYTD_biaya57 = 0;
								$total_cmonth_biaya57 = 0;
								$total_YTD_biaya57 = 0;

								if ($data_nokir_biaya57 > 0) {

									foreach ($data_nokir_biaya57 as $row57) {
										$nokir_biaya57 				= $row57->no_perkiraan;
										$nm_perkiraan_biaya57		= $row57->nama;
										$v_faktor57					= $row57->faktor;
										$pYTD_biaya57					= $row57->saldoawal * $v_faktor57;
										$cmonth_biaya57				= ($row57->debet - $row57->kredit) * $v_faktor57;
										$YTD_biaya57					= $pYTD_biaya57 + $cmonth_biaya57;

										$total_pYTD_biaya57 += $pYTD_biaya57;
										$total_cmonth_biaya57 += $cmonth_biaya57;
										$total_YTD_biaya57 += $YTD_biaya57;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya57 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya57 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya57, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya57, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya57, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total LOGISTIC EXPENSE</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya57_pYTD		= "Rp. " . number_format($total_pYTD_biaya57, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya57_cmonth		= "Rp. " . number_format($total_cmonth_biaya57, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya57_YTD		= "Rp. " . number_format($total_YTD_biaya57, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 57 -->
								
								<!--HPP  58-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>58</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>FACTORY OVERHEAD</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php

								$total_pYTD_biaya58 = 0;
								$total_cmonth_biaya58 = 0;
								$total_YTD_biaya58 = 0;

								if ($data_nokir_biaya58 > 0) {

									foreach ($data_nokir_biaya58 as $row58) {
										$nokir_biaya58 				= $row58->no_perkiraan;
										$nm_perkiraan_biaya58		= $row58->nama;
										$v_faktor58					= $row58->faktor;
										$pYTD_biaya58					= $row58->saldoawal * $v_faktor58;
										$cmonth_biaya58				= ($row58->debet - $row58->kredit) * $v_faktor58;
										$YTD_biaya58					= $pYTD_biaya58 + $cmonth_biaya58;

										$total_pYTD_biaya58 += $pYTD_biaya58;
										$total_cmonth_biaya58 += $cmonth_biaya58;
										$total_YTD_biaya58 += $YTD_biaya58;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya58 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya58 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya			= "Rp. " . number_format($pYTD_biaya58, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya		= "Rp. " . number_format($cmonth_biaya58, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya				= "Rp. " . number_format($YTD_biaya58, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total FACTORY OVERHEAD</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya58_pYTD		= "Rp. " . number_format($total_pYTD_biaya58, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya58_cmonth		= "Rp. " . number_format($total_cmonth_biaya58, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya58_YTD		= "Rp. " . number_format($total_YTD_biaya58, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								
								<!--END HPP 58 -->
								
						

								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Total COST OF GOODS SOLD</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalHpp_pYTD		= "Rp. " . number_format($SubTotalHpp_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalHpp_cmonth	= "Rp. " . number_format($SubTotalHpp_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalHpp_YTD			= "Rp. " . number_format($SubTotalHpp_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>LABA/RUGI KOTOR</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $RpSaldo_pYTD			= "Rp. " . number_format($Saldo_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $RpSaldo_cmonth			= "Rp. " . number_format($Saldo_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $RpSaldo_YTD			= "Rp. " . number_format($Saldo_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<!-- BIAYA PENJUALAN 61-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>61</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>SALES DAN MARKETING EXPENSES</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								$total_pYTD_biaya61 = 0;
								$total_cmonth_biaya61 = 0;
								$total_YTD_biaya61 = 0;
								if ($data_nokir_biaya61 > 0) {
									foreach ($data_nokir_biaya61 as $row61) {
										$nokir_biaya61 				= $row61->no_perkiraan;
										$nm_perkiraan_biaya61			= $row61->nama;
										$v_faktor61					= $row61->faktor;
										$pYTD_biaya61					= $row61->saldoawal * $v_faktor61;
										$cmonth_biaya61				= ($row61->debet - $row61->kredit) * $v_faktor61;
										$YTD_biaya61					= $pYTD_biaya61 + $cmonth_biaya61;

										$total_pYTD_biaya61 += $pYTD_biaya61;
										$total_cmonth_biaya61 += $cmonth_biaya61;
										$total_YTD_biaya61 += $YTD_biaya61;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya61 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya61 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya61			= "Rp. " . number_format($pYTD_biaya61, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya61		= "Rp. " . number_format($cmonth_biaya61, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya61				= "Rp. " . number_format($YTD_biaya61, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								} else {
									$pYTD_biaya61			= 0;
									$cmonth_biaya61			= 0;
									$YTD_biaya61			= 0;
								}
								?>

								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total SALES EXPENSE</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya_pYTD		= "Rp. " . number_format($total_pYTD_biaya61, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya_cmonth		= "Rp. " . number_format($total_cmonth_biaya61, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya_YTD		= "Rp. " . number_format($total_YTD_biaya61, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								<?php

								?>

								<!-- BIAYA KANTOR DAN UMUM-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>62</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>GENERAL DAN ADMIN EXPENSE</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								// $total_pYTD_biaya2 	=  0;
								// $total_cmonth_biaya2 =  0;
								// $total_YTD_biaya2		=  0;
								$SubTotalBiaya2_pYTD			= 0;
								$SubTotalBiaya2_cmonth			= 0;
								$SubTotalBiaya2_YTD			= 0;

								$TotalBiaya_pYTD			= 0;
								$TotalBiaya_cmonth68			= 0;
								$TotalBiaya_YTD			= 0;

								if ($data_nokir_biaya2 > 0) {

									foreach ($data_nokir_biaya2 as $row4) {
										$nokir_biaya2 				= $row4->no_perkiraan;
										$nm_perkiraan_biaya2	= $row4->nama;

										$pYTD_biaya2					= $row4->saldoawal * $row4->faktor;
										$cmonth_biaya2_debet		= $row4->debet;
										$cmonth_biaya2_kredit		= $row4->kredit;
										$cmonth_biaya2				= ($cmonth_biaya2_debet - $cmonth_biaya2_kredit) * $row4->faktor;
										// $cmonth_biaya2				= $cmonth_biaya2_debet;
										$YTD_biaya2					= $pYTD_biaya2 + $cmonth_biaya2;

										$SubTotalBiaya2_pYTD		+=  $pYTD_biaya2;
										$SubTotalBiaya2_cmonth	+=  $cmonth_biaya2;
										$SubTotalBiaya2_YTD 		+=  $YTD_biaya2;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya2 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya2 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya2			= "Rp. " . number_format($pYTD_biaya2, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya2		= "Rp. " . number_format($cmonth_biaya2, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya2				= "Rp. " . number_format($YTD_biaya2, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								}
								
								
								$TotalBiaya_pYTD		= $total_pYTD_biaya61 + $SubTotalBiaya2_pYTD + $total_pYTD_biaya52;
								$TotalBiaya_cmonth68	= $total_cmonth_biaya61 + $SubTotalBiaya2_cmonth + $total_cmonth_biaya52;
								$TotalBiaya_YTD			= $total_YTD_biaya61 + $SubTotalBiaya2_YTD + $total_YTD_biaya52;

								// TOTAL LABA OPERASI
								$laba_operasi_pYTD	= $Saldo_pYTD - $TotalBiaya_pYTD;
								$laba_operasi_cmonth = $Saldo_cmonth - $TotalBiaya_cmonth68;
								$laba_operasi_YTD	= $Saldo_YTD - $TotalBiaya_YTD;

								$rp_laba_operasi_pYTD	= $laba_operasi_pYTD;
								$rp_laba_operasi_cmonth	= $laba_operasi_cmonth;
								$rp_laba_operasi_YTD	= $laba_operasi_YTD;
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total GENERAL DAN ADMIN EXPENSE</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya2_pYTD		= "Rp. " . number_format($SubTotalBiaya2_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya2_cmonth		= "Rp. " . number_format($SubTotalBiaya2_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya2_YTD		= "Rp. " . number_format($SubTotalBiaya2_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Total EXPENSES</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_TotalBiaya_pYTD		= "Rp. " . number_format($TotalBiaya_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_TotalBiaya_cmonth		= "Rp. " . number_format($TotalBiaya_cmonth68, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_TotalBiaya_YTD		= "Rp. " . number_format($TotalBiaya_YTD, 0, ',', '.'); ?></b></td>
								</tr>

								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>LABA OPERASI</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_laba_operasi_pYTD		= "Rp. " . number_format($laba_operasi_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_laba_operasi_cmonth		= "Rp. " . number_format($laba_operasi_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_laba_operasi_YTD		= "Rp. " . number_format($laba_operasi_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>
								<?php

								?>

								<!-- PENDAPATAN LAIN-LAIN -->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>71</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>OTHER INCOME</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								$total_pYTD_pdptn2 = 0;
								$total_cmonth_pdptn2 = 0;
								$total_YTD_pdptn2 = 0;

								$SubTotalPdptn2_pYTD			= 0;
								$SubTotalPdptn2_cmonth71			= 0;
								$SubTotalPdptn2_YTD			= 0;

								if ($data_nokir_pdptn2 > 0) {

									foreach ($data_nokir_pdptn2 as $row5) {

										$nokir_pdptn2				= $row5->no_perkiraan;
										$nm_perkiraan_pdptn2	= $row5->nama;
										$pYTD_pdptn2					= ($row5->saldoawal) * $row5->faktor;
										$cmonth_pdptn2				= ($row5->debet - $row5->kredit) * $row5->faktor;
										$YTD_pdptn2					= $pYTD_pdptn2 + $cmonth_pdptn2;

										$SubTotalPdptn2_pYTD 	+=  $pYTD_pdptn2;
										$SubTotalPdptn2_cmonth71 +=  $cmonth_pdptn2;
										$SubTotalPdptn2_YTD 		+=  $YTD_pdptn2;


										$total_pYTD_pdptn2 	+=  $pYTD_pdptn2;
										$total_cmonth_pdptn2 +=  $cmonth_pdptn2;
										$total_YTD_pdptn2 		+=  $YTD_pdptn2;

										$rp_pYTD_pdptn2			= "Rp. " . number_format($pYTD_pdptn2, 0, ',', '.');
										$rp_cmonth_pdptn2		= "Rp. " . number_format($cmonth_pdptn2, 0, ',', '.');
										$rp_YTD_pdptn2			= "Rp. " . number_format($YTD_pdptn2, 0, ',', '.');

										$rp_total_pYTD_pdptn2			= "Rp. " . number_format($total_pYTD_pdptn2, 0, ',', '.');
										$rp_total_cmonth_pdptn2		= "Rp. " . number_format($total_cmonth_pdptn2, 0, ',', '.');
										$rp_total_YTD_pdptn2				= "Rp. " . number_format($total_YTD_pdptn2, 0, ',', '.');
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_pdptn2 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_pdptn2 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_pYTD_pdptn2 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_cmonth_pdptn2 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_YTD_pdptn2 ?></td>
										</tr>
								<?php
									}
								} else {
									$SubTotalPdptn2_pYTD		= 0;
									$SubTotalPdptn2_cmonth71	= 0;
									$SubTotalPdptn2_YTD			= 0;

									$rp_SubTotalPdptn2_pYTD		= 0;
									$rp_SubTotalPdptn2_cmonth	= 0;
									$rp_SubTotalPdptn2_YTD		= 0;
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total OTHER INCOME</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalPdptn2_pYTD		= "Rp. " . number_format($SubTotalPdptn2_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalPdptn2_cmonth		= "Rp. " . number_format($SubTotalPdptn2_cmonth71, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalPdptn2_YTD		= "Rp. " . number_format($SubTotalPdptn2_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<!-- FEE ORGANISASI -->
								<!-- <tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>91</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>FEE ORGANISASI</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr> -->

								<?php
								$total_pYTD_FEE = 0;
								$total_cmonth_FEE = 0;
								$total_YTD_FEE = 0;

								$SubTotalFEE_pYTD			= 0;
								$SubTotalFEE_cmonth91			= 0;
								$SubTotalFEE_YTD			= 0;

								if ($data_nokir_fee > 0) {

									foreach ($data_nokir_fee as $row_fee) {

										$nokir_FEE			= $row_fee->no_perkiraan;
										$nm_perkiraan_FEE	= $row_fee->nama;
										$pYTD_FEE			= $row_fee->saldoawal * $row_fee->faktor; // * $v_faktor;
										$cmonth_FEE			= ($row_fee->debet - $row_fee->kredit) * $row_fee->faktor;
										$YTD_FEE			= $pYTD_FEE + $cmonth_FEE;

										$SubTotalFEE_pYTD 	+=  $pYTD_FEE;
										$SubTotalFEE_cmonth91 +=  $cmonth_FEE;
										$SubTotalFEE_YTD 		+=  $YTD_FEE;


										$total_pYTD_FEE 	+=  $pYTD_FEE;
										$total_cmonth_FEE +=  $cmonth_FEE;
										$total_YTD_FEE 		+=  $YTD_FEE;

										$rp_pYTD_FEE			= "Rp. " . number_format($pYTD_FEE, 0, ',', '.');
										$rp_cmonth_FEE		= "Rp. " . number_format($cmonth_FEE, 0, ',', '.');
										$rp_YTD_FEE			= "Rp. " . number_format($YTD_FEE, 0, ',', '.');

										$rp_total_pYTD_FEE			= "Rp. " . number_format($total_pYTD_FEE, 0, ',', '.');
										$rp_total_cmonth_FEE		= "Rp. " . number_format($total_cmonth_FEE, 0, ',', '.');
										$rp_total_YTD_FEE				= "Rp. " . number_format($total_YTD_FEE, 0, ',', '.');
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_FEE ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_FEE ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_pYTD_FEE ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_cmonth_FEE ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $rp_YTD_FEE ?></td>
										</tr>
								<?php
									}
								} else {
									$SubTotalFEE_pYTD		= 0;
									$SubTotalFEE_cmonth91	= 0;
									$SubTotalFEE_YTD			= 0;

									$rp_SubTotalFEE_pYTD		= 0;
									$rp_SubTotalFEE_cmonth	= 0;
									$rp_SubTotalFEE_YTD		= 0;
								}
								?>
								<!-- <tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total FEE ORGANISASI</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalFEE_pYTD		= "Rp. " . number_format($SubTotalFEE_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalFEE_cmonth		= "Rp. " . number_format($SubTotalFEE_cmonth91, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalFEE_YTD		= "Rp. " . number_format($SubTotalFEE_YTD, 0, ',', '.'); ?></b></td>
								</tr> -->
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>


								<!-- BIAYA LAIN-LAIN-->
								<tr>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>72</b></td>
									<td class="teksBiru" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><b>OTHER EXPENSE</b></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>

								<?php
								$total_pYTD_biaya3		=  0;
								$total_cmonth_biaya3	=  0;
								$total_YTD_biaya3		=  0;
								$SubTotalBiaya3_pYTD	= 0;
								$SubTotalBiaya3_cmonth	= 0;
								$SubTotalBiaya3_YTD		= 0;
								// $TotalBiaya_pYTD		= 0;
								//$TotalBiaya_cmonth72	= 0;
								//$TotalBiaya_YTD		= 0;
								$SaldoBersih_pYTD			= 0;
								$SaldoBersih_cmonth			= 0;
								$SaldoBersih_YTD			= 0;

								if ($data_nokir_biaya3 > 0) {
									foreach ($data_nokir_biaya3 as $row6) {
										$nokir_biaya3 				= $row6->no_perkiraan;
										$nm_perkiraan_biaya3		= $row6->nama;
										$pYTD_biaya3				= $row6->saldoawal * $row6->faktor;
										$cmonth_biaya3				= ($row6->debet - $row6->kredit) * $row6->faktor;
										$YTD_biaya3					= $pYTD_biaya3 + $cmonth_biaya3;

										$SubTotalBiaya3_pYTD		+=  $pYTD_biaya3;
										$SubTotalBiaya3_cmonth		+=  $cmonth_biaya3;
										$SubTotalBiaya3_YTD 		+=  $YTD_biaya3;

										//$TotalBiaya_pYTD			= $SubTotalBiaya_pYTD + $SubTotalBiaya2_pYTD;
										//$TotalBiaya_cmonth72		= $SubTotalBiaya_cmonth + $SubTotalBiaya3_cmonth;
										//$TotalBiaya_YTD			= $SubTotalBiaya_YTD + $SubTotalBiaya3_YTD;

										$SaldoBersih_pYTD			= $Saldo_pYTD - $TotalBiaya_pYTD + $SubTotalPdptn2_pYTD - $SubTotalBiaya3_pYTD - $SubTotalFEE_pYTD;

										$SaldoBersih_cmonth			= $Saldo_cmonth - $TotalBiaya_cmonth68 + $SubTotalPdptn2_cmonth71 - $SubTotalBiaya3_cmonth - $SubTotalFEE_cmonth91;

										$SaldoBersih_YTD			= $Saldo_YTD - $TotalBiaya_YTD + $SubTotalPdptn2_YTD - $SubTotalBiaya3_YTD - $SubTotalFEE_YTD;

										// echo $Saldo_cmonth."<br>";
										// echo $TotalBiaya_cmonth."<br>";
										// echo $SubTotalPdptn2_cmonth."<br>";
										// echo $SubTotalBiaya3_cmonth."<br>";
										// exit;

										$total_pYTD_biaya3 	+=  $pYTD_biaya3;
										$total_cmonth_biaya3 +=  $cmonth_biaya3;
										$total_YTD_biaya3 		+=  $YTD_biaya3;
								?>
										<tr>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nokir_biaya3 ?></td>
											<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"><?= $nm_perkiraan_biaya3 ?></td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?= $rp_pYTD_biaya3			= "Rp. " . number_format($pYTD_biaya3, 0, ',', '.'); ?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_cmonth_biaya3		= "Rp. " . number_format($cmonth_biaya3, 0, ',', '.');
												?>
											</td>
											<td align="right" style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;">
												<?=
													$rp_YTD_biaya3				= "Rp. " . number_format($YTD_biaya3, 0, ',', '.');
												?>
											</td>
										</tr>
								<?php
									}
								} else {
									$SubTotalBiaya3_pYTD	= 0;
									$SubTotalBiaya3_cmonth	= 0;
									$SubTotalBiaya3_YTD		= 0;

									$SaldoBersih_pYTD	= 0;
									$SaldoBersih_cmonth	= 0;
									$SaldoBersih_YTD	= 0;

									$rp_SubTotalBiaya3_pYTD		= 0;
									$rp_SubTotalBiaya3_cmonth	= 0;
									$rp_SubTotalBiaya3_YTD		= 0;

									$rp_SaldoBersih_pYTD		= 0;
									$rp_SaldoBersih_cmonth		= 0;
									$rp_SaldoBersih_YTD			= 0;

									$SaldoBersih_pYTD			= $Saldo_pYTD - $TotalBiaya_pYTD + $SubTotalPdptn2_pYTD - $SubTotalBiaya3_pYTD - $SubTotalFEE_pYTD;
									$SaldoBersih_cmonth			= $Saldo_cmonth - $TotalBiaya_cmonth68 + $SubTotalPdptn2_cmonth71 - $SubTotalBiaya3_cmonth - $SubTotalFEE_cmonth91;
									// echo $Saldo_cmonth."<br>";
									// echo $TotalBiaya_cmonth."<br>";
									// echo $SubTotalPdptn2_cmonth."<br>";
									// echo $SubTotalBiaya3_cmonth."<br>";
									// exit;

									$SaldoBersih_YTD			= $Saldo_YTD - $TotalBiaya_YTD + $SubTotalPdptn2_YTD - $SubTotalBiaya3_YTD - $SubTotalFEE_YTD;
								}
								?>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>Sub Total OTHER EXPENSE</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya3_pYTD		= "Rp. " . number_format($SubTotalBiaya3_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya3_cmonth	= "Rp. " . number_format($SubTotalBiaya3_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SubTotalBiaya3_YTD		= "Rp. " . number_format($SubTotalBiaya3_YTD, 0, ',', '.'); ?></b></td>
								</tr>

								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td class="teksBiru" align="center" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b>LABA/RUGI BERSIH</b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SaldoBersih_pYTD		= "Rp. " . number_format($SaldoBersih_pYTD, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SaldoBersih_cmonth		= "Rp. " . number_format($SaldoBersih_cmonth, 0, ',', '.'); ?></b></td>
									<td class="teksBiru" align="right" style="border-right:none; border-bottom-style:none; border-left-style:none;"><b><?= $rp_SaldoBersih_YTD			= "Rp. " . number_format($SaldoBersih_YTD, 0, ',', '.'); ?></b></td>
								</tr>
								<tr>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
									<td style="border-top-style:none; border-right:none; border-bottom-style:none; border-left-style:none;"></td>
								</tr>


         	</tbody>

</table>