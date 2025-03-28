<div class="box box-warning box-solid">
	<div class="box-header">
		<h5 class="box-title text-center"> <?= $title;?></h5>
		
	</div>
	
	<?php
	if(empty($rows_material) && empty($rows_nonmaterial)){
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
				<td width="15%" class="text-left">Nomor Perkiraan</td>
				<td width="35%" class="text-left text-bold"><?= $rows_coa->no_perkiraan;?></td>
				<td width="15%" class="text-left">Nama Perkiraan</td>
				<td width="35%" class="text-left text-bold"><?= $rows_coa->nama;?></td>
			</tr>
			<tr>
				<td class="text-left">Tanggal Stok</td>
				<td class="text-left text-bold"><?= date('d-m-Y',strtotime($tgl_stock));?></td>
				<td class="text-left">&nbsp;</td>
				<td class="text-left">&nbsp;</td>	
			</tr>
			
		</table>
		<br>
		<div class="table-responsive">
			<?php
			if(strtolower($type_find) == 'material' || strtolower($type_find) == 'consumable'){
				echo'
				<table id="grid_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
					<thead>
						<tr class="bg-navy-active text-white">
							<th class="text-center">No.</th>
							<th class="text-center">Kode Material</th>
							<th class="text-center">Nama Material</th>
							<th class="text-center">Harga</th>
							<th class="text-center">Qty</th>
							<th class="text-center">Total</th>
							<th class="text-center">Gudang</th>
						</tr>
					</thead>
					<tbody>
				';
					$Total_Qty 	= $Total_Price  = 0;
					$no			= 0;
					if($rows_material){
						
						foreach($rows_material as $row){
							$no++;
							
							$Code_Material		= $row['id_material'];
							$Code_MaterialReal	= $row['idmaterial'];
							$Name_Material		= $row['nm_material'];
							$Cat_Material		= $row['nm_category'];
							$Code_Gudang		= $row['id_gudang'];
							$Name_Gudang		= $row['nm_gudang'];
							
							$Nilai_HPP			= (!empty($row['harga']) && floatval($row['harga']) !== 0)?$row['harga']:0;
							$SaldoAwal_HPP		= (!empty($row['nilai_awal_rp']) && floatval($row['nilai_awal_rp']) !== 0)?$row['nilai_awal_rp']:0;
							$SaldoAkhir_HPP		= (!empty($row['nilai_akhir_rp']) && floatval($row['nilai_akhir_rp']) !== 0)?$row['nilai_akhir_rp']:0;
							$Total_Trans		= (!empty($row['nilai_trans_rp']) && floatval($row['nilai_trans_rp']) !== 0)?$row['nilai_trans_rp']:0;
							$Qty_Awal			= (!empty($row['qty_stock_awal']) && floatval($row['qty_stock_awal']) !== 0)?$row['qty_stock_awal']:0;
							$Qty_In				= (!empty($row['qty_in']) && floatval($row['qty_in']) !== 0)?$row['qty_in']:0;
							$Qty_Out			= (!empty($row['qty_out']) && floatval($row['qty_out']) !== 0)?$row['qty_out']:0;
							$Qty_Akhir			= (!empty($row['qty_stock_akhir']) && floatval($row['qty_stock_akhir']) !== 0)?$row['qty_stock_akhir']:0;
							
							$Harga_HPP			= $Nilai_HPP;
							if((floatval($Qty_Akhir) > 0 || floatval($Qty_Akhir) < 0) && (floatval($SaldoAkhir_HPP) > 0 || floatval($SaldoAkhir_HPP) < 0)){
								$Harga_HPP		= $SaldoAkhir_HPP / $Qty_Akhir;
							}
							
							
							$Total_Qty			+=$Qty_Akhir;
							$Total_Price		+=$SaldoAkhir_HPP;
							
							echo '<tr>
									<td class="text-center">'.$no.'</td>
									<td class="text-left">'.$Code_MaterialReal.'</td>
									<td class="text-left">'.$Name_Material.'</td>
									<td class="text-right">'.number_format($Harga_HPP).'</td>										
									<td class="text-center">'.$Qty_Akhir.'</td>
									<td class="text-right">'.number_format($SaldoAkhir_HPP).'</td>
									<td class="text-center">'.$Name_Gudang.'</td>
								</tr>';
							
							
						}
					}
					
					if(!empty($rows_nonmaterial) && (strtolower($type_find) == 'material' || strtolower($type_find) == 'consumable')){
						
						foreach($rows_nonmaterial as $row){
							$no++;
							$Code_Material 	= '';
							$Code_MateERP 	= $row->code_group;
							$Name_Material	= $row->material_name;
							$Qty_Material	= $row->stock;
							$Qty_Rusak		= $row->rusak;
							$Code_Category	= $row->category_code;
							$Name_Category	= '';
							$Code_Gudang	= $row->gudang;
							$Harga_HPP		= $row->harga;
							
							$Nama_Warehouse	= '-';
							$Jenis_Gudang	= 'pusat';
							$Query_Warehouse= "SELECT nm_gudang,category FROM warehouse WHERE id = '".$Code_Gudang."'";
							$rows_Warehouse	= $this->ori_operasional->query($Query_Warehouse)->row();
							if($rows_Warehouse){
								$Nama_Warehouse	= strtoupper($rows_Warehouse->nm_gudang);
								$Jenis_Gudang	= $rows_Warehouse->category;
							}
							
							$Table_Gudang		= 'price_book';
							if(strtolower($Jenis_Gudang) == 'subgudang'){
								$Table_Gudang		= 'price_book_subgudang';
							}else if(strtolower($Jenis_Gudang) == 'produksi'){
								$Table_Gudang		= 'price_book_produksi';
							}else if(strtolower($Jenis_Gudang) == 'project'){
								$Table_Gudang		= 'price_book_project';
							}
							
							
							$Query_Price		= "SELECT * FROM ".$Table_Gudang." WHERE id_material = '".$Code_MateERP."' AND DATE(updated_date) <= '".$tgl_stock."' ORDER BY id DESC LIMIT 1";
							$rows_Price			= $this->ori_operasional->query($Query_Price)->row();
							if($rows_Price){
								if(empty($rows_Price->price_book) || floatval($rows_Price->price_book) > 0){
									$Harga_HPP		= $rows_Price->price_book;
								}
								
							}
							
							$Total_HPP			= $Qty_Material * $Harga_HPP;
							
							$Total_Qty			+=$Qty_Material;
							$Total_Price		+=$Total_HPP;
							
							echo '<tr>
									<td class="text-center">'.$no.'</td>
									<td class="text-left">'.$Code_MateERP.'</td>
									<td class="text-left">'.$Name_Material.'</td>
									<td class="text-right">'.number_format($Harga_HPP).'</td>										
									<td class="text-center">'.$Qty_Material.'</td>
									<td class="text-right">'.number_format($Total_HPP).'</td>
									<td class="text-center">'.$Nama_Warehouse.'</td>
								</tr>';
							
							
						}
					}
					echo'
					</tbody>
					<tfoot>
						<tr class="text-bold bg-gray">
							<td class="text-center" colspan="4"> TOTAL</td>
							<td class="text-center">'.$Total_Qty.'</td>
							<td class="text-right">'.number_format($Total_Price).'</td>
							<td class="text-center">&nbsp;</td>
						</tr>
					</tfoot>
				</table>
					';
			}else if(strtolower($type_find) == 'wip_butt' || strtolower($type_find) == 'wip_pipa' || strtolower($type_find) == 'wip_fitting' || strtolower($type_find) == 'wip_spool' || strtolower($type_find) == 'wip_tank'){
				echo'
				<table id="grid_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
					<thead>
						<tr class="bg-navy-active text-white">
							<th class="text-center">No.</th>
							<th class="text-center">No SPK</th>
							<th class="text-center">No SO</th>
							<th class="text-center">Produk</th>
							<th class="text-center">Qty Order</th>
							<th class="text-center">Total Order</th>
							<th class="text-center">Qty WIP</th>
							<th class="text-center">Total WIP</th>
						</tr>
					</thead>
					<tbody>
				';
				
				$Total_Qty	= $Total_Price	= 0;
				if($rows_nonmaterial){
						$no = 0;
					foreach($rows_nonmaterial as $row){
						$no++;
						
						
						$Total_Qty			+=$row->qty_open;
						$Total_Price		+=$row->nil_open;
						
						echo '<tr>
								<td class="text-center">'.$no.'</td>
								<td class="text-center">'.$row->no_spk.'</td>
								<td class="text-center">'.$row->no_so.'</td>
								<td class="text-left">'.$row->product.'</td>
								<td class="text-center">'.$row->qty.'</td>
								<td class="text-right">'.number_format($row->nilai_wip).'</td>										
								<td class="text-center">'.$row->qty_open.'</td>
								<td class="text-right">'.number_format($row->nil_open).'</td>
							</tr>';
						
						
					}
				}else{
					echo'
					<tr>
						<th class="text-center text-red" colspan="8">DATA TIDAK DITEMUKAN</th>
					</tr>
					';
				}
				
				echo'
					</tbody>
					<tfoot>
						<tr class="text-bold bg-gray">
							<td class="text-center" colspan="6"> TOTAL</td>
							<td class="text-center">'.$Total_Qty.'</td>
							<td class="text-right">'.number_format($Total_Price).'</td>
						</tr>
					</tfoot>
				</table>
					';
			}else if(strtolower($type_find) == 'finish_good'){
				echo'
				<table id="grid_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
					<thead>
						<tr class="bg-navy-active text-white">
							<th class="text-center">No.</th>
							<th class="text-center">No SPK</th>
							<th class="text-center">No SO</th>
							<th class="text-center">ID Trans</th>
							<th class="text-center">ID Produk</th>
							<th class="text-center">Produk</th>
							<th class="text-center">Qty Ke</th>
							<th class="text-center">Nilai Produk</th>
							<th class="text-center">Tgl In</th>
						</tr>
					</thead>
					<tbody>
				';
				
				$Total_Qty	= $Total_Price	= 0;
				if($rows_nonmaterial){
						$no = 0;
					foreach($rows_nonmaterial as $row){
						$no++;
						
						$Nama_Material	= (!empty($row->product) && $row->product !=='-')?$row->product:$row->nm_material;
						$Harga_Material	= ($row->nilai_unit > 0)?$row->nilai_unit:$row->cost_book;
								
						$Total_Price		+=$Harga_Material;
						
						echo '<tr>
								<td class="text-center">'.$no.'</td>
								<td class="text-center">'.$row->no_spk.'</td>
								<td class="text-center">'.$row->no_so.'</td>
								<td class="text-center">'.(!empty($row->id_trans)?$row->id_trans:$row->kode_trans).'</td>
								<td class="text-center">'.$row->id_pro.'</td>
								<td class="text-left">'.$Nama_Material.'</td>
								<td class="text-center">'.$row->qty_ke.'</td>
								<td class="text-right">'.number_format($Harga_Material).'</td>	
								<td class="text-center">'.$row->date_in.'</td>
							</tr>';
						
						
					}
				}else{
					echo'
					<tr>
						<th class="text-center text-red" colspan="9">DATA TIDAK DITEMUKAN</th>
					</tr>
					';
				}
				
				echo'
					</tbody>
					<tfoot>
						<tr class="text-bold bg-gray">
							<td class="text-center" colspan="7"> TOTAL</td>
							<td class="text-right" colspan="2">'.number_format($Total_Price).'</td>
						</tr>
					</tfoot>
				</table>
					';
			}else if(strtolower($type_find) == 'intransit'){
				echo'
				<table id="grid_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
					<thead>
						<tr class="bg-navy-active text-white">
							<th class="text-center">No.</th>
							<th class="text-center">No Delivery</th>
							<th class="text-center">No SJ</th>
							<th class="text-center">Tgl Lock</th>
							<th class="text-center">ID Produksi</th>
							<th class="text-center">Kode Produk</th>
							<th class="text-center">Produk</th>
							<th class="text-center">Nilai Produk</th>
						</tr>
					</thead>
					<tbody>
				';
				
				$Total_Qty	= $Total_Price	= 0;
				if($rows_nonmaterial){
						$no = 0;
					foreach($rows_nonmaterial as $row){
						$no++;
						
						$Nama_Material	= (!empty($row->product) && $row->product !=='-')?$row->product:$row->nm_material;
						$Nilai_Produk 	= ($row->nilai_cogs > 0)?$row->nilai_cogs:$row->unit_value;
						$Total_Price	+=$Nilai_Produk;
						echo '<tr>
								<td class="text-center">'.$no.'</td>
								<td class="text-center">'.$row->kode_delivery.'</td>
								<td class="text-center">'.$row->nomor_sj.'</td>
								<td class="text-center">'.$row->tgl_lock.'</td>
								<td class="text-center">'.$row->id_produksi.'</td>								
								<td class="text-center">'.$row->product_code.'</td>
								<td class="text-left">'.$Nama_Material.'</td>
								<td class="text-right">'.number_format($Nilai_Produk).'</td>
							</tr>';
						
						
					}
				}else{
					echo'
					<tr>
						<th class="text-center text-red" colspan="8">DATA TIDAK DITEMUKAN</th>
					</tr>
					';
				}
				
				echo'
					</tbody>
					<tfoot>
						<tr class="text-bold bg-gray">
							<td class="text-center" colspan="7"> TOTAL</td>
							<td class="text-right">'.number_format($Total_Price).'</td>
						</tr>
					</tfoot>
				</table>
					';
			}else if(strtolower($type_find) == 'incustomer'){
				echo'
				<table id="grid_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
					<thead>
						<tr class="bg-navy-active text-white">
							<th class="text-center">No.</th>
							<th class="text-center">No Delivery</th>
							<th class="text-center">No SJ</th>
							<th class="text-center">Tgl Confirm</th>
							<th class="text-center">ID Produksi</th>
							<th class="text-center">Kode Produk</th>
							<th class="text-center">Produk</th>
							<th class="text-center">Nilai Produk</th>
						</tr>
					</thead>
					<tbody>
				';
				
				$Total_Qty	= $Total_Price	= 0;
				if($rows_nonmaterial){
						$no = 0;
					foreach($rows_nonmaterial as $row){
						$no++;
						
						$Nama_Material	= (!empty($row->product) && $row->product !=='-')?$row->product:$row->nm_material;
						$Nilai_Produk 	= ($row->nilai_cogs > 0)?$row->nilai_cogs:$row->unit_value;
						$Total_Price	+=$Nilai_Produk;
						echo '<tr>
								<td class="text-center">'.$no.'</td>
								<td class="text-center">'.$row->kode_delivery.'</td>
								<td class="text-center">'.$row->nomor_sj.'</td>
								<td class="text-center">'.$row->tgl_confrim.'</td>
								<td class="text-center">'.$row->id_produksi.'</td>								
								<td class="text-center">'.$row->product_code.'</td>
								<td class="text-left">'.$Nama_Material.'</td>
								<td class="text-right">'.number_format($Nilai_Produk).'</td>
							</tr>';
						
						
					}
				}else{
					echo'
					<tr>
						<th class="text-center text-red" colspan="8">DATA TIDAK DITEMUKAN</th>
					</tr>
					';
				}
				
				echo'
					</tbody>
					<tfoot>
						<tr class="text-bold bg-gray">
							<td class="text-center" colspan="7"> TOTAL</td>
							<td class="text-right">'.number_format($Total_Price).'</td>
						</tr>
					</tfoot>
				</table>
					';
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
		<?php
		if(!empty($rows_material) || !empty($rows_nonmaterial)){
			echo"
			&nbsp;&nbsp;<button class='btn btn-md btn-primary' type='button' onClick='DownloadStock(\"".$rows_coa->no_perkiraan."\",\"".$tgl_stock."\",\"".$type_find."\");'> DOWNLOAD EXCEL </button>
			";
		}
		?>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#grid_tb').DataTable({
			lengthMenu: [
				[10, 50, 100, -1],
				[10, 50, 100, 'All']
			]
		});
	});
</script>
