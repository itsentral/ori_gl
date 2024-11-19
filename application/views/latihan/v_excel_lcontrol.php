<?php
 header("Content-type: application/vnd-ms-excel");
 header("Content-Disposition: attachment; filename=Trial_Balance.xls"); //ganti nama sesuai keperluan
 header("Pragma: no-cache");
 header("Expires: 0");
 ?>
<div class="box-body table-responsive no-padding">
	<table class="table table-bordered table-hover dataTable example1">
		<thead>
			<tr>
				<th colspan="6">
					<h2><b>TRIAL BALANCE</b>
				</th>
			</tr>
			<tr>
				<th><b>No perkiraan</th>
				<th><b>Nama</th>
				<th><b>Saldo Awal</th>
				<th><b>Debet</th>
				<th><b>Kredit</th>
				<th><b>Saldo Akhir</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i=0;
			$total_saw_0=0;
			$total_sak_0=0;
			$total_saw_1=0;
			$total_sak_1=0;
			$total_deb=0;
			$total_kre=0;
			if($data_ledgr_cont > 0){
				foreach($data_ledgr_cont as $row){
					$i++;
					$nokir = $row->no_perkiraan;
					$sa = $row->saldoawal;
					$debet = $row->debet;
					$kredit= $row->kredit;
					$saldoakhir=$row->saldoakhir;
					//	$saakhir=$sa+$debet-$kredit;
					//	$data_nokir_biaya=$this->db->query("SELECT * FROM jurnal WHERE tipe='BUK' and nomor='$id_buk' and no_perkiraan like '6%'")->result();
					if(substr($nokir,0,1)=="1"){
						$total_saw_0+=$sa;
						$total_sak_0+=$saldoakhir;
					}else{
						$total_saw_1+=$sa;
						$total_sak_1+=$saldoakhir;
					}
						$total_deb+=$debet;
						$total_kre+=$kredit;
					?>
					<tr>
						<td><?=$row->no_perkiraan?></td>
						<td><?=$row->nama?></td>
						<td align="right"><?=($sa);?></td>
						<td align="right" width="12%"><?=($debet);?></td>
						<td align="right" width="12%"><?=($kredit);?></td>
						<td align="right" width="12%"><?=($saldoakhir);?></td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td></td>
				<td></td>
				<td align="right"><?=($total_saw_0+$total_saw_1);?></td>
				<td align="right"><?=($total_deb);?></td>
				<td align="right"><?=($total_kre);?></td>
				<td align="right"><?=($total_sak_0+$total_sak_1);?></td>
			</tr>
		</tbody>
	</table>
</div>
