<style type="text/css">
	.str {
		mso-number-format: \@;
	}
</style>
<?php
error_reporting(E_ALL & ~E_NOTICE);
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Laporan_material_stock_".$tgl_stock.".xls"); //ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda

?>
<table width='50%' border="0" cellpadding="5" cellspacing="0">
	<tr>
		<th colspan="7" style='text-align:center;font-size:18px;'><center><?php echo $title;?></center></th>
	</tr>
	<tr>
		<td align="left" colspan="2">No Perkiraan</td>
		<td align="left" colspan="5">: <b> <?= @$rows_coa->no_perkiraan;?></b></td>
	</tr>
	<tr>
		<td align="left" colspan="2">Nama Perkiraan</td>
		<td align="left" colspan="5">: <b> <?= @$rows_coa->nama;?></b></td>
	</tr>
	<tr>
		<td align="left" colspan="2">Tanggal Stok</td>
		<td align="left" colspan="5">: <b> <?= @date('d-m-Y',strtotime($tgl_stock));?></b></td>
	</tr>
</table>
<br>
<table width='50%' border="1" cellpadding="5" cellspacing="0">
	<tr>
		<th align="center">No.</th>
		<th align="center">Kode Material</th>
		<th align="center">Nama Material</th>
		<th align="center">Harga</th>
		<th align="center">Qty</th>
		<th align="center">Total</th>
		<th align="center">Gudang</th>
	</tr>
	<?php
	if(empty($rows_material) && empty($rows_nonmaterial)){
		echo'
		<tr>
			<th align="left" colspan="7"> DATA TIDAK DITEMUKAN</th>
		</tr>';
	}else{
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
						<td align="center">'.$no.'</td>
						<td align="left">'.$Code_MaterialReal.'</td>
						<td align="left">'.$Name_Material.'</td>
						<td align="right">'.number_format($Harga_HPP).'</td>										
						<td align="center">'.$Qty_Akhir.'</td>
						<td align="right">'.number_format($SaldoAkhir_HPP).'</td>
						<td align="center">'.$Name_Gudang.'</td>
					</tr>';
				
				
			}
		}
		
		if($rows_nonmaterial){
			
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
						<td align="center">'.$no.'</td>
						<td align="left">'.$Code_MateERP.'</td>
						<td align="left">'.$Name_Material.'</td>
						<td align="right">'.number_format($Harga_HPP).'</td>										
						<td align="center">'.$Qty_Material.'</td>
						<td align="right">'.number_format($Total_HPP).'</td>
						<td align="center">'.$Nama_Warehouse.'</td>
					</tr>';
				
				
			}
		}
		echo'
		
			<tr>
				<td align="center" colspan="4"> TOTAL</td>
				<td align="center">'.$Total_Qty.'</td>
				<td align="right">'.number_format($Total_Price).'</td>
				<td align="center">&nbsp;</td>
			</tr>
		
		';
	
	}
	
	?>
</table>
	