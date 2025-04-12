<?php $this->load->view('header');
	
	$bulan = $bln;
	$tahun = $thn;
	
?> 

	<section class="content-header">
      <h1>
       <?=$judul?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><?=$judul?></li>
      </ol>
  </section>

	<section class="content-header">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">	
                      <?php
						//$proses=0;					
						if($proses == 1){				
							echo "<div class='alert alert-success' role='alert'>Proses Periode End $bln-$thn Sukses</div>";					
						}
					?>		
            <!-- /.box-header -->
           <!-- <div class="box-body table-responsive no-padding"> -->
						<form method="post" action="<?=base_url()?>index.php/Posting_new/proses_periode_end" autocomplete="off">
              				<table class="table table-bordered">
							  	<thead>
								<tr>
								    <td width="10">No</td>
									<td width="10">Mata Uang</td>
									<td width="10">Kurs Periode End</td>
								</tr>
								</thead>								
								<tbody>
								<?php 
								$kurs2 = $this->db->query("SELECT matauang, kurs FROM periode_end_proses WHERE bulan ='$bln' AND tahun='$thn'")->result();
								$kurs = $this->db->query("SELECT mata_uang FROM coa_master WHERE mata_uang !='null' GROUP BY mata_uang")->result();
								?>
								<tr>
								<?php
								$no=0;
								
								if(!empty($kurs2)){
								foreach($kurs2 as $key=>$val){
								$no++;
								?>
								<tr>
								    <td width="10" align="center"><?=$no?></td>
									<td width="10"><input type="text" name="matauang[]" id="matauang" value="<?= $val->matauang?>" tabindex="-<?=$no?>" readonly></td>
									<td width="10"><input type="text" name="kurs_periode[]" id="kurs_periode" class="kurs<?=$no?>"  value="<?= number_format($val->kurs,2)?>" onblur = 'formatHarga(<?=$no?>);' ></td>
								</tr>									
								<?php	
								}
								}else{
								foreach($kurs as $key=>$val){
								$no++;
								?>
								<tr>
								    <td width="10" align="center"><?=$no?></td>
									<td width="10"><input type="text" name="matauang[]" id="matauang" value="<?= $val->mata_uang?>" tabindex="-<?=$no?>" readonly></td>
									<td width="10"><input type="text" name="kurs_periode[]" id="kurs_periode" class="kurs<?=$no?>"  value="0" onblur = 'formatHarga(<?=$no?>);' ></td>
								</tr>									
								<?php	
								}
								}
								?>
								</tr>
								</tbody>															
							</table>	
								  <div width="25%" align="center">
										<input type="submit" name="tampilkan" value="PROSES" onclick="return check()" class="btn btn-success pull-center">                             
										
									</div>					
			  </form>            
        </div>
    </div>
    </div>
</section>
<?php $this->load->view('footer');?>

<link rel="stylesheet" href="<?=base_url()?>plugins/datepicker/datepicker3.css">
<script src="<?=base_url()?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?=base_url()?>dist/moment.min.js"></script>
<script>
	$(".divide").divide();
	
	function number_format (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
		}
		
	function formatHarga(id)
		{
			var harga=$('.kurs'+id).val();
			$('.kurs'+id).val(number_format(harga,2)); 

		}
	
</script>
