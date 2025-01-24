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
		<?php
		if(strtolower($type_find) == 'material' || strtolower($type_find) == 'consumable'){
		?>
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
									if(strtolower($type_find) == 'material'){
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
									}else if(strtolower($type_find) == 'consumable'){
										$Code_Material 	= $row['id_material'];
										$Code_MateERP 	= $row['id_material'];
										$Name_Material	= $row['nm_material'];
										$Qty_Awal 		= $row['qty_order'];
										$Qty_In			= $row['qty_oke'];
										$Qty_Out 		= $row['check_qty_oke'];
										$Harga_HPP 		= $row['harga'];
										
										$Qty_Proses		= $Qty_Awal;
										if($Qty_Out > 0 && $rows_header->checked == 'Y'){
											$Qty_Proses		= $Qty_Out;
										}
										
										$Qry_Material	= "SELECT * FROM con_nonmat_new WHERE code_group = '".$Code_Material."' AND (deleted_date IS NULL OR TRIM(deleted_date) = '' OR TRIM(deleted_date) ='-')";
										$rows_Material	= $this->ori_operasional->query($Qry_Material)->row();
										if($rows_Material){
											$Name_Material	= $rows_Material->material_name.' '.$rows_Material->spec.' '.$rows_Material->brand;
										}
										$Query_Price		= "SELECT * FROM price_book WHERE id_material = '".$Code_Material."' AND DATE(updated_date) <= '".$rows_header->tanggal."' ORDER BY id DESC LIMIT 1";
										$rows_Price			= $this->ori_operasional->query($Query_Price)->row();
										if($rows_Price){
											if(empty($rows_Price->price_book) || floatval($rows_Price->price_book) > 0){
												$Harga_HPP		= $rows_Price->price_book;
											}
											
										}
										
										$Total_HPP		= round($Harga_HPP * $Qty_Proses);
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
			<?php
			}else if(strtolower($type_find) == 'wip_butt' || strtolower($type_find) == 'wip_pipa' || strtolower($type_find) == 'wip_fitting' || strtolower($type_find) == 'wip_spool' || strtolower($type_find) == 'wip_tank'){
				echo'
				<table class="table table-striped table-bordered table-sm mb-4">
					<tr>
						<td width="15%" class="text-left">Nomor Transaksi</td>
						<td width="35%" class="text-left text-bold">'.$rows_header->id_trans.'</td>
						<td width="15%" class="text-left">Tanggal</td>
						<td width="35%" class="text-left">'.date('d-m-Y',strtotime($rows_header->tanggal)).'</td>
					</tr>
					<tr>
						<td width="15%" class="text-left">Nomor SPK</td>
						<td width="35%" class="text-left text-bold">'.$rows_header->no_spk.'</td>
						<td width="15%" class="text-left">Nomor SO</td>
						<td width="35%" class="text-left">'.$rows_header->no_so.'</td>
					</tr>
					<tr>
						<td width="15%" class="text-left">Produk</td>
						<td width="35%" class="text-left text-bold">'.strtoupper($rows_header->product).'</td>
						<td width="15%" class="text-left">Qty Order</td>
						<td width="35%" class="text-left">'.$rows_header->qty.'</td>
					</tr>
					<tr>
						<td width="15%" class="text-left">Total Nilai</td>
						<td width="35%" class="text-left text-bold">'.number_format($rows_header->nilai_wip).'</td>
						<td width="15%" class="text-left">&nbsp;</td>
						<td width="35%" class="text-left">&nbsp;</td>
					</tr>
				</table>
				<div class="table-responsive">
					<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
						<thead>
							<tr class="bg-navy-active text-white">
								<th class="text-center">No.</th>
								<th class="text-center">No IPP</th>
								<th class="text-center">Material</th>
								<th class="text-center">Cost Book</th>
								<th class="text-center">Berat</th>								
								<th class="text-center">Total</th>
							</tr>
						</thead>
						<tbody>
				';
					if($rows_detail){
						$no	= 0;
						$Total_Qty = $Total_Price  = 0;
						foreach($rows_detail as $row){
							$no++;
							$Nama_Material	= $row['nm_material'];
							$Code_Material	= $row['id_material'];
							if(!empty($Code_Material) && $Code_Material !=='-'){
								$Nama_Material	.='<br><span class="text-red">'.$Code_Material.'</span>';
							}
							
							
							
							echo '<tr>
									<td class="text-center">'.$no.'</td>
									<td class="text-center">'.$row['no_ipp'].'</td>
									<td class="text-left">'.$Nama_Material.'</td>
									<td class="text-right">'.number_format($row['costbook'],2).'</td>										
									<td class="text-center">'.$row['no_ipp'].'</td>
									<td class="text-right">'.number_format($row['total_price'],2).'</td>
								</tr>';
							
							$Total_Price  	+=$row['total_price'];
						}
						echo '<tr class="text-bold bg-gray">
								<td class="text-center" colspan="5"> TOTAL</td>
								<td class="text-right">'.number_format($Total_Price,2).'</td>
							</tr>';
					}
				echo'
						</tbody>
					</table>
				</div>
				';
				
				$rows_Finish	= $this->ori_operasional->get_where('data_erp_fg',array('id_trans'=>$rows_header->id_trans,'LOWER(jenis)'=>'in'))->result();
				if($rows_Finish){
					echo'
					<div class="table-responsive">
						<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
							<thead>
								<tr class="bg-navy-active text-white">
									<th class="text-center" colspan="5">WIP To Finish Good</th>
								</tr>
								<tr class="bg-navy-active text-white">
									<th class="text-center">No.</th>
									<th class="text-center">No ID</th>
									<th class="text-center">Urut Ke</th>
									<th class="text-center">Tanggal</th>							
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
					';
					$intFin	= 0;
					foreach($rows_Finish as $rowFin){
						$intFin++;
						
						echo '<tr>
								<td class="text-center">'.$intFin.'</td>
								<td class="text-center">'.$rowFin->id_pro.'</td>
								<td class="text-center">'.$rowFin->qty_ke.'</td>
								<td class="text-center">'.date('d-m-Y H:i',strtotime($rowFin->created_date)).'</td>
								<td class="text-right">'.number_format($rowFin->nilai_unit,2).'</td>
							</tr>';
						
						
					}
					echo'
							</tbody>
						</table>
					</div>
					';
				}
			}
			?>
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

