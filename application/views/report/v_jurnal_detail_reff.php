<div class="box box-warning box-solid">
	<div class="box-header">
		<h5 class="box-title text-center"> <?= $title;?></h5>
	</div>
	
	<?php
	if(empty($rows_detail)){
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
				<td width="15%" class="text-left">Nomor Transaksi</td>
				<td width="35%" class="text-left text-bold"><?= $rows_header->kode_trans;?></td>
				<td width="15%" class="text-left">Tanggal</td>
				<td width="35%" class="text-left"><?= date('d-m-Y',strtotime($rows_header->tanggal));?></td>
			</tr>
			<tr>
				<td class="text-left">Kategori</td>
				<td class="text-left"><?= $rows_header->category;?></td>
				<td class="text-left">No Reff</td>
				<td class="text-left"><?= $rows_header->no_ipp;?></td>	
			</tr>
			<tr>						
				<td class="text-left">Gudang Asal</td>
				<td class="text-left"><?= $rows_header->kd_gudang_dari;?></td>
				<td class="text-left">Gudang Tujuan</td>
				<td class="text-left"><?= $rows_header->kd_gudang_ke;?></td>
			</tr>
			<tr>						
				<td class="text-left">Check Oleh</td>
				<td class="text-left"><?= $rows_header->checked_by;?></td>
				<td class="text-left">Notes</td>
				<td class="text-left" colspan="3"><?= $rows_header->note;?></td>
			</tr>
			
		</table>
		
		<div class="table-responsive">
			<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
				<thead>
					<tr class="bg-navy-active text-white">
						<th class="text-center">No.</th>
						<th class="text-center">Kode Item</th>
						<th class="text-center">Nama Item</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($rows_detail){
							$no	= 0;
							$Total_Qty = $Total_Price  = 0;
							foreach($rows_detail as $row){
								$no++;
								$Code_Material 	= $row['id_material'];
								$Code_MateERP 	= $row['idmaterial'];
								$Name_Material	= $row['nm_material'];
								$Qty_Awal 		= $row['qty_stock_awal'];
								$Qty_In			= $row['qty_in'];
								$Qty_Out 		= $row['qty_out'];
								$Qty_Akhir 		= $row['qty_stock_akhir'];
								$Harga_HPP 		= $row['harga'];
								$Total_Awal		= $row['nilai_awal_rp'];
								$Total_HPP 		= $row['nilai_trans_rp'];
								$Total_Akhir	= $row['nilai_akhir_rp'];
								$Nomor_Jurnal	= $row['no_jurnal'];
								
								$Qty_Proses		= $Qty_In;
								if($Qty_Out > 0){
									$Qty_Proses		= $Qty_Out;
								}
								
								
								echo '<tr>
										<td class="text-center">'.$no.'</td>
										<td class="text-center">'.$Code_MateERP.'</td>
										<td class="text-left">'.$Name_Material.'</td>
										<td class="text-right">'.number_format($Harga_HPP,2).'</td>										
										<td class="text-center">'.$Qty_Proses.'</td>
										<td class="text-right">'.number_format($Total_HPP,2).'</td>
									</tr>';
								
								$Total_Qty 		+=$Qty_Proses;
								$Total_Price  	+=$Total_HPP;
							}
							echo '<tr class="text-bold bg-gray">
									<td class="text-center" colspan="4"> TOTAL</td>
									<td class="text-center">'.$Total_Qty.'</td>
									<td class="text-right">'.number_format($Total_Price,2).'</td>
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

