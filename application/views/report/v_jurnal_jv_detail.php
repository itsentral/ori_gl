<div class="box box-warning box-solid">
	<div class="box-header">
		<h5 class="box-title text-center"> <?= $title;?></h5>
	</div>
	
	<?php
	if(empty($rows_header)){
		echo"<div class='box-body'>
			<div class='row'>";
				echo"<div class='col-sm-12'>";
					echo"<h4 class='text-red'><b>NO RECORD WAS FOUND.....</b></h4>";
				echo"</div>";
			echo"</div>
		</div>";
	}else{
	 
	
	?>
	<div class="box-body">
		<table class="table table-striped table-bordered table-sm mb-4">
			
			<tr>
				<td width="15%" class="text-left">Nomor</td>
				<td width="35%" class="text-left text-bold"><?= $rows_header->nomor;?></td>
				<td width="15%" class="text-left">Tanggal</td>
				<td width="35%" class="text-left"><?= date('d-m-Y',strtotime($rows_header->tgl));?></td>
			</tr>
			<tr>
				<td class="text-left">Periode</td>
				<td class="text-left"><?= $arr_month[$rows_header->bulan].' '.$rows_header->tahun;?></td>
				<td class="text-left">Total</td>
				<td class="text-left"><?= number_format($rows_header->jml);?></td>	
			</tr>
			<tr>						
				<td class="text-left">Koreksi No</td>
				<td class="text-left"><?= $rows_header->koreksi_no;?></td>
				<td class="text-left">Description</td>
				<td class="text-left" colspan="3"><?= $rows_header->keterangan;?></td>
			</tr>
			
			
		</table>
		
		<div class="table-responsive">
			<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
				<thead>
					<tr class="bg-navy-active text-white">
						<th class="text-center">No.</th>
						<th class="text-center">COA No.</th>
						<th class="text-center">Description</th>
						<th class="text-center">No. Reff</th>
						<th class="text-center">Debit</th>
						<th class="text-center">Kredit</th>
						<th class="text-center">Debit USD</th>
						<th class="text-center">Kredit USD</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($rows_detail){
							$totDebet = $totKredit = $no = $totDebetUSD = $totKreditUSD =0;
							foreach($rows_detail as $row){
								$no++;
								$no_perkiraan 	= $row['no_perkiraan'];
								$keterangan 	= $row['keterangan'];
								$debet 			= $row['debet'];
								$kredit 		= $row['kredit'];
								$no_reff 		= $row['no_reff'];
								$debetUSD 		= $row['nilai_valas_debet'];
								$kreditUSD 		= $row['nilai_valas_kredit'];
								$Query_COA		= "SELECT nama FROM COA WHERE no_perkiraan = '".$no_perkiraan."' ORDER BY id DESC LIMIT 1";
								$rows_COA		= $this->db->query($Query_COA)->row();
								if($rows_COA){
									$no_perkiraan	.='<br><span class="text-red">'.$rows_COA->nama.'</span>';
								}
								
								echo '<tr>
										<td class="text-center">'.$no.'</td>
										<td class="text-center">'.$no_perkiraan.'</td>
										<td class="text-left">'.$keterangan.'</td>
										<td class="text-center">'.$no_reff.'</td>
										<td class="text-right">'.number_format($debet).'</td>
										<td class="text-right">'.number_format($kredit).'</td>
										<td class="text-right">'.number_format($debetUSD).'</td>
										<td class="text-right">'.number_format($kreditUSD).'</td>
									</tr>';
								
								$totDebet += $debet;
								$totKredit += $kredit;
								$totDebetUSD += $debetUSD;
								$totKreditUSD += $kreditUSD;
							}
							echo '<tr class="text-bold bg-gray">
									<td class="text-center" colspan="4"> TOTAL</td>
									<td class="text-right">'.number_format($totDebet).'</td>
									<td class="text-right">'.number_format($totKredit).'</td>
									<td class="text-right">'.number_format($totDebetUSD).'</td>
									<td class="text-right">'.number_format($totKreditUSD).'</td>
								</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	}
	?>
	<div class="box-footer text-center">
		<button type="button" class="btn btn-md btn-danger" data-dismiss="modal" id="btn-modal-close">
			<i class="fa fa-remove"></i> Close
		</button>
	</div>
</div>

