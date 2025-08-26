<style type="text/css">
	.str {
		mso-number-format: \@;
	}
</style>
<?php
error_reporting(E_ALL & ~E_NOTICE);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Rekap_umur_kartu_piutang.xls"); //ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda

?>
<table width='50%' border="1" cellpadding="5" cellspacing="0">

																
								<tbody>

									<tr>
										<?php
										
										$awal 		= $datawal;
										$akhir		= $datakhir;
																			
																	
										echo "<tr><th colspan='8' style='text-align:center;font-size:15px;'><center>REKAP UMUR KARTU PIUTANG<br>Periode :  " . date_format(new DateTime($akhir), "d-m-Y") . "</center></th></tr>";
										
										?>
									</tr>

									<tr>
									    <td>
										<center><b>Kode Supplier</b></center>
										</td>
										<td>
											<center><b>Nama Supplier</b></center>
										</td>
										<td>
											<center><b>0-30 hari</b></center>
										</td>
										<td>
											<center><b>31-60 hari</b></center>
										</td>
										<td>
											<center><b>61-90 hari</b></center>
										</td>
										<td>
											<center><b>91-120 hari</b></center>
										</td>
										<td>
											<center><b>>120 hari</b></center>
										</td>
										<td>
											<center><b>Jumlah</b></center>
										</td>
									</tr>
									
									<?php
									    $this->db->query("TRUNCATE TABLE umur_piutang");
										
									    if ($coa_sa > 0) {
										$count = 0;
										foreach ($coa_sa as $row_sa) {
											$count++;
     										$bukti	     = $row_sa->no_reff;
											$id_supplier = $row_sa->id_supplier;
											$nm_supplier = $row_sa->nama_supplier;
																		
											
											   $detail_jurnal	= $this->Kartupiutang_model->get_detail_umur_kartu_piutang($awal,$akhir,$id_supplier, $bukti);
											
										
											
												foreach ($detail_jurnal as $row_dj) {
													
													$tgl_j       		= $row_dj->tanggal;
													$nilai_debet 		= $row_dj->debet;
													$nilai_kredit 		= $row_dj->kredit;
													$keterangan         = $row_dj->keterangan;
                                                    $saldo30         = $row_dj->saldo30;
													$saldo31         = $row_dj->saldo31;
													$saldo60         = $row_dj->saldo60;
													$saldo90         = $row_dj->saldo90;
													$saldo120         = $row_dj->saldo120;
													
													
													
													if  ($saldo30 != 0 ){
													$totsaldo30     = $saldo30;
													$totsaldo31     = 0;
													$totsaldo60     = 0;
													$totsaldo90     = 0;
													$totsaldo120    = 0;
													}
													
													if  ($saldo30 == 0 ){
													$totsaldo30     = 0;													
													}
													
													if ($saldo31 != 0 ) {
													$totsaldo30     = 0;
													$totsaldo31     = $saldo31+$saldo30;
													$totsaldo60     = 0;
													$totsaldo90     = 0;
													$totsaldo120    = 0;
													}
													if ($saldo31 == 0 ) {
													$totsaldo31     = 0;
													}
													if  ( $saldo60 != 0 ) {
													$totsaldo30     = 0;
													$totsaldo31     = 0;
													$totsaldo60     = $saldo60+$saldo31+$saldo30;
													$totsaldo90     = 0;
													$totsaldo120    = 0;
													}
													if ($saldo60 == 0 ) {
													$totsaldo60     = 0;
													}
													if ( $saldo90 != 0 ) {
													$totsaldo30     = 0;
													$totsaldo31     = 0;
													$totsaldo60     = 0;
													$totsaldo90     = $saldo90+$saldo60+$saldo31+$saldo30;
													$totsaldo120    = 0;
													}
													if ($saldo90 == 0 ) {
													$totsaldo90     = 0;
													}
													if ($saldo120 != 0) {
													$totsaldo30     = 0;
													$totsaldo31     = 0;
													$totsaldo60     = 0;
													$totsaldo90     = 0;
													$totsaldo120     = $saldo120+$saldo90+$saldo60+$saldo31+$saldo30;
													}
													if ($saldo90 == 0 ) {
													$totsaldo120     = 0;
													}
													
													
													
													
													$this->db->query("INSERT INTO umur_piutang (id_supplier,nama_supplier,saldo30,saldo31,saldo90,saldo120,saldo120plus) 
													VALUES ('$id_supplier','$nm_supplier','$totsaldo30','$totsaldo31','$totsaldo60','$totsaldo90','$totsaldo120')");
					
															
												 
												
									
												}
											
										   }
										}
										
										
									
									?>
									
									    <?php   
										   
										   $dt_jurnal	= $this->Kartupiutang_model->get_umur_piutang($id_supplier);
											         
												if ($dt_jurnal > 0) {
												
												     foreach ($dt_jurnal as $row) {
													 $total = $row->saldo30+$row->saldo31+$row->saldo90+$row->saldo120+$row->saldo120plus;
										
											?>
													<tr>
													   
													    <td align="center"><?= $row->id_supplier  ?></td>
														<td align="left"><?= $row->nama_supplier ?></td>
													    <td align="right"><?= number_format($row->saldo30, 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($row->saldo31, 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($row->saldo90, 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($row->saldo120, 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($row->saldo120plus, 0, ',', '.'); ?></td>
														<td align="right"><?= number_format($total, 0, ',', '.'); ?></td>
														
													</tr>
											
									
									       
									         <?php
													$tot30 += $row->saldo30;
													$tot31 += $row->saldo31;
													$tot60 += $row->saldo90;
													$tot90 += $row->saldo120;
													$tot120+= $row->saldo120plus;
													$totall+= $total;
                          
													} 
												}
											 ?>


								</tbody>
								
												<tr>
													    <td align="center" colspan="2"><b>TOTAL</td>
														<td align="right"><b><?= number_format($tot30, 0, ',', '.'); ?></td>
														<td align="right"><b><?= number_format($tot31, 0, ',', '.'); ?></td>
														<td align="right"><b><?= number_format($tot60, 0, ',', '.'); ?></td>
														<td align="right"><b><?= number_format($tot90, 0, ',', '.'); ?></td>
														<td align="right"><b><?= number_format($tot120, 0, ',', '.'); ?></td>
														<td align="right"><b><?= number_format($totall, 0, ',', '.'); ?></td>
														
												</tr>
							</table>

             