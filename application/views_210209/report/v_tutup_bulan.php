<?php $this->load->view('header');?> 

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
							<form method="post" action="<?=base_url()?>index.php/report/proses_tutup_bulan" autocomplete="off">
								<table class="table table-bordered">
									<tr>
										<td colspan="2" align="center"><input type="submit" name="tombol_proses_ttp_bln" value="Proses" class="btn btn-primary btn-lg btn-block" target="_blank"></td>									
										<!-- btn-success pull-center -->
									</tr>							
								</table>						
							</form>            
			</div>
					<?php
						//$proses=0;					
						if($proses == 1){				
							echo "<div class='alert alert-success' role='alert'>Selesai Proses Tutup Bulan</div>";					
						}
					?>
		</div>
		</div>		
	</section>

<?php $this->load->view('footer');?>

<link rel="stylesheet" href="<?=base_url()?>plugins/datepicker/datepicker3.css">
<script src="<?=base_url()?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?=base_url()?>dist/moment.min.js"></script>
<script src="<?=base_url()?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script>
	function check(){
		if($("#bulan_posting").val()=="0"){
			alert("Silahkan Pilih Bulan");
			return false;
		}else if($("#tahun_posting").val()=="0"){
			alert("Silahkan Pilih Tahun");
			return false;
		}
	}
	/*
	$(function () {
    $(".example1").DataTable();
  });
	*/
</script>
