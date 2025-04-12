<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Posting_new extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		$this->load->helper('menu');
		$this->load->model('Posting_new_model');
		$this->load->model('Report_model');
		$this->folder	= 'report';
	}

	function index()
	{
		$cab_pilih		= $this->session->userdata('singkat_cbg');
		$rows_cabang	= $this->Posting_new_model->get_cabang();
		$rows_data		= array(
			'title'			=> 'Jurnal Posting',
			'rows_cabang'	=> $rows_cabang,
			'cab_pilih'		=> $cab_pilih
		);

		$this->load->view($this->folder . '/main_posting', $rows_data);
	}

	function proses_posting($Jenis_Posting = 'N')
	{
		if ($this->input->post()) {
			//echo"<pre>";print_r($this->input->post());exit;
			$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$Kode_Cabang	= $this->input->post('kode_cabang');
			$current_coa4 = 0;
			$current_coa5 = 0;
			$current_coa6 = 0;
			$current_coa71 = 0;
			$current_coa72 = 0;
			$current_coa8 = 0;
			$current_coa9 = 0;
			
			
			
			$currentval_coa4 = 0;
			$currentval_coa5 = 0;
			$currentval_coa6 = 0;
			$currentval_coa71 = 0;
			$currentval_coa72 = 0;
			$currentval_coa8 = 0;
			$currentval_coa9 = 0;

			/*
			$Periode_Pilih	= $this->input->post('periode_proses');			
			$Pecah_Tgl		= explode(" ",$Periode_Pilih);
			
			$Tahun_Pros		= $Pecah_Tgl[1];
			$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
			*/

			$det_Cabang		= $this->db->query("select * from pastibisa_tb_cabang where kdcab like '%$Kode_Cabang'")->result();

			$Nocab			= $det_Cabang[0]->nocab;
			$Subcab			= $det_Cabang[0]->subcab;

			if ($Jenis_Posting === 'Y') {
				$Tahun_Pros		= date('Y');
				$Month_Pros		= date('n');
				$Periode_Pilih	= date('F Y');
			} else {
				$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O' ORDER BY periode ASC LIMIT 1";
				$get_Periode	= $this->db->query($Query_Periode)->result();
				$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
				$Tahun_Pros		= $Pecah_Periode[1];
				$Month_Pros		= intval($Pecah_Periode[0]);
				$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
			}


			$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
			$Periode_Cari	= date('m-Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

			$Tahun_Lalu	= date('Y', mktime(0, 0, 0, $Month_Pros - 1, 1, $Tahun_Pros));
			$Bulan_Lalu	= date('n', mktime(0, 0, 0, $Month_Pros - 1, 1, $Tahun_Pros));


         
		   

			$Tahun_Now		= date('Y');
			$Bulan_Now		= date('n');
			$Periode_Now	= date('Y-m');

			$OK_Next		= 0;
			if ($Periode_Proses < $Periode_Now) {
				$OK_Next	= 1;
				$Tahun_Next	= date('Y', mktime(0, 0, 0, $Month_Pros + 1, 1, $Tahun_Pros));
				$Bulan_Next	= date('n', mktime(0, 0, 0, $Month_Pros + 1, 1, $Tahun_Pros));
			}

			## AMBIL PERIODE ##
			$det_Periode	= $this->db->query("select * from periode where kdcab like '%$Kode_Cabang' and periode='$Periode_Cari'")->result();
			if ($det_Periode) {
				if ($det_Periode[0]->stsaktif === 'C') {

					$rows_Return	= array(
						'status'		=> 3,
						'pesan'			=> 'Data has been locked....'
					);
				} else {
					$Post_Laba		= $det_Periode[0]->post_laba;
					//echo "$Kode_Cabang $Periode_Cari post laba $Post_Laba";
					//exit;
					$this->db->trans_start();

					## PROSES UNPOSTING ##
					$Qry_UnPost	= "UPDATE COA SET debet=0,kredit=0,nilai_valas_debet=0,nilai_valas_kredit=0 WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "'";
					$this->db->query($Qry_UnPost);

			

					## CEK JURNAL BALANCE ATAU TIDAK ##
					$Qry_Cek_Balance	= "SELECT
												SUM(IF(debet > 0, debet, 0)) AS total_debet,
												SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
											FROM
												jurnal
											WHERE
												tanggal LIKE '" . $Periode_Proses . "%'
											AND (

												IF (debet > 0, debet, 0) > 0
												OR
												IF (kredit > 0, kredit, 0) > 0
											)";
//											AND nomor LIKE '" . $Nocab . "%'

					$det_Balance		= $this->db->query($Qry_Cek_Balance)->result();

					if ($det_Balance[0]->total_debet === $det_Balance[0]->total_kredit) {
						## AMBIL DATA JURNAL BASED ON NO PERKIRAAN ##
						$Qry_Perkiraan	= "SELECT
												no_perkiraan,
												SUM(debet) AS total_debet,
												SUM(kredit) AS total_kredit,
												SUM(nilai_valas_debet) AS total_valas_debet,
												SUM(nilai_valas_kredit) AS total_valas_kredit
											FROM
												jurnal
											WHERE
												tanggal LIKE '" . $Periode_Proses . "%'
											GROUP BY
												no_perkiraan";
//											AND nomor LIKE '" . $Nocab . "%'

						$det_Perkiraan	= $this->db->query($Qry_Perkiraan)->result();
						$Ok_Exists		= 0;
						$Pesan			= '';
						
						$Total_coa4		= $Total_coa5	= $Total_coa6 = $Total_coa71 = $Total_coa72	= $Total_coa9	= $Total_Laba	= 0;
						$Totalval_coa4  = $Totalval_coa5 = $Totalval_coa6 = $Totalval_coa71 = $Totalval_coa72 =$Totalval_coa9 = 0;
						
						if ($det_Perkiraan) {
							$intE		  	= 0;
							$Message_Error	= '';
							$Tot_Debet=0;
							foreach ($det_Perkiraan as $keyI => $valI) {
								$No_Coa		= $valI->no_perkiraan;
								$Tot_Debet	= $valI->total_debet;
								$Tot_Kredit	= $valI->total_kredit;
								$TotVal_Debet	= $valI->total_valas_debet;
								$TotVal_Kredit	= $valI->total_valas_kredit;


								
								
								$Level4		= substr($No_Coa, 0, 7) . '-00';
								$Level3		= substr($No_Coa, 0, 4) . '-00-00';
								$Level1		= substr($No_Coa, 0, 1);
								$Level2		= substr($No_Coa, 0, 2);

								$Coa5101    = substr($No_Coa, 0, 4);
								$Coa5103    = substr($No_Coa, 0, 4);


								

								## CEK EXIST NO PERKIRAAN ##

								$Saldo_Awal				= 0;
								$Faktor_Kali			= 1;
								$Saldo_Awal_Valas		= 0;

								$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $No_Coa))->num_rows();
								if ($num_COA < 1) {
									$intE++;
									if ($intE === 1) {
										$Message_Error	= "<h4 class='alert-heading'>Unbalance Journal !</h4>
															<hr>
															<p>";
									}
									$Ambil_Perkiraan	= "SELECT
																nomor,
																tipe
															FROM
																jurnal
															WHERE
																tanggal LIKE '" . $Periode_Proses . "%'
															
															AND no_perkiraan ='" . $No_Coa . "'";
//															AND nomor LIKE '" . $Nocab . "%'

									$get_Perkiraan	= $this->db->query($Ambil_Perkiraan)->result();
									if ($get_Perkiraan) {
										foreach ($get_Perkiraan as $keyP => $valP) {
											$Message_Error	.= "Account No <b>" . $No_Coa . "</b>  In The Journal  <b><i>" . $valP->nomor . "</i> (" . $valP->tipe . ")</b>  Was Not Found At Master COA .<br>";
										}
									}
									$Ok_Exists		= 1;
								} else {
									$detail_COA		= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $No_Coa))->result();
									if ($detail_COA) {
										$Saldo_Awal		    = $detail_COA[0]->saldoawal;
										$Saldo_Awal_Valas	= $detail_COA[0]->saldo_valas;
										$Faktor_Kali	    = $detail_COA[0]->faktor;
									}
								}
								if ($Ok_Exists === 0) {
									$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $Level4))->num_rows();
									if ($num_COA < 1) {
										$intE++;
										$Message_Error	.= "Account No <b<" . $Level4 . "</b> Was Not Found At Master COA ..<br>";

										$Ok_Exists		= 1;
									}
								}

								if ($Ok_Exists === 0) {
									$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $Level3))->num_rows();
									if ($num_COA < 1) {
										$intE++;
										$Message_Error	.= "Account No " . $Level3 . " Was Not Found At Master COA ...<br>";
										$Ok_Exists		= 1;
									}
								}

								$Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
								$Saldo_Akhir_Valas		= ($Saldo_Awal_Valas + $TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
								
								if ($Ok_Exists === 0) {
									if ($Level1 === '4') {
										$Total_coa4		+= $Saldo_Akhir;
										$current_coa4   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa4		+= $Saldo_Akhir_Valas;
										$currentval_coa4    += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '5') {
										$Total_coa5		+= $Saldo_Akhir;
										$current_coa5   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa5	   += $Saldo_Akhir_Valas;
										$currentval_coa5   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '6') {
										$Total_coa6		+= $Saldo_Akhir;
										$current_coa6   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa6		+= $Saldo_Akhir_Valas;
										$currentval_coa6   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '9') {
										$Total_coa9		+= $Saldo_Akhir;
										$current_coa9   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa9	   += $Saldo_Akhir_Valas;
										$currentval_coa9   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level2 === '71') {
										$Total_coa71		+= $Saldo_Akhir;
										$current_coa71      += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa71		   += $Saldo_Akhir_Valas;
										$currentval_coa71      += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level2 === '72') {
										$Total_coa72		+= $Saldo_Akhir;
										$current_coa72   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa71		+= $Saldo_Akhir_Valas;
										$currentval_coa71   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									}


									if ($Coa5101 === '5101') {
										$det_5101 = $this->db->query("SELECT no_perkiraan,
										 SUM(debet) AS total_debet,
										 SUM(kredit) AS total_kredit,
										 SUM(nilai_valas_debet) AS total_valas_debet,
										 SUM(nilai_valas_kredit) AS total_valas_kredit FROM
										 jurnal WHERE tanggal LIKE '$Periode_Proses%' AND no_perkiraan LIKE '$Coa5101%'")->row();

										$TotalD_5101		= $det_5101->total_debet;
										$TotalK_5101		= $det_5101->total_kredit;
										$current_5101      = ($TotalD_5101 - $TotalK_5101) * $Faktor_Kali;
										
									} else {
										$TotalD_5101		=0;
										$TotalK_5101		=0;
										$current_5101       =0;
									}

									if ($Coa5103 === '5103') {

										
										$det_5103 = $this->db->query("SELECT no_perkiraan,
										SUM(debet) AS total_debet,
										SUM(kredit) AS total_kredit,
										SUM(nilai_valas_debet) AS total_valas_debet,
										SUM(nilai_valas_kredit) AS total_valas_kredit FROM
										jurnal WHERE tanggal LIKE '$Periode_Proses%' AND no_perkiraan LIKE '$Coa5103%'")->row();

										$TotalD_5103		= $det_5103->total_debet;
										$TotalK_5103		= $det_5103->total_kredit;
										$current_5103      = ($TotalD_5103 - $TotalK_5103) * $Faktor_Kali;
									} else {
										$TotalD_5103		= 0;
										$TotalK_5103		= 0;
										$current_5103       = 0;
									}

									$Cogm = $current_5101+$current_5103;
									
									
									if($Tot_Debet==''){										
										$Totdebet =0;
									} else{
										$Totdebet =$Tot_Debet;
									}
									
									if($Tot_Kredit==''){										
										$Totkredit =0;
									} else{
										$Totkredit=$Tot_Kredit;
									}
									
									$Upd_Saldo		= "UPDATE COA SET debet = debet + " . $Totdebet . ", kredit = kredit + " . $Totkredit . ", nilai_valas_debet = nilai_valas_debet + " . $TotVal_Debet . ", nilai_valas_kredit = nilai_valas_kredit + " . $TotVal_Kredit . " WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('" . $No_Coa . "','" . $Level4 . "','" . $Level3 . "')";
									$this->db->query($Upd_Saldo);
								}
							}

							if ($Ok_Exists === 1) {
								$this->db->trans_complete();
								$this->db->trans_rollback();
								if (!empty($Message_Error)) $Message_Error	.= "</p><hr>";
								$rows_Return	= array(
									'status'		=> 2,
									'pesan'			=> $Message_Error
								);
							} else {
								## NEED KONFIRMASI ##

								if ($OK_Next === 1) {
									$Upd_Saldo_next		= "UPDATE COA new_det
															INNER JOIN COA old_det ON new_det.kdcab = old_det.kdcab
															AND new_det.no_perkiraan = old_det.no_perkiraan
															SET new_det.saldoawal = (
																IF (
																	old_det.saldoawal IS NULL
																	OR old_det.saldoawal = '',
																	0,
																	old_det.saldoawal
																) +
																IF (
																	old_det.debet IS NULL
																	OR old_det.debet = '',
																	0,
																	old_det.debet
																) -
																IF (
																	old_det.kredit IS NULL
																	OR old_det.kredit = '',
																	0,
																	old_det.kredit
																)
															),
															 new_det.saldo_valas = (
																IF (
																	old_det.saldo_valas IS NULL
																	OR old_det.saldo_valas = '',
																	0,
																	old_det.saldo_valas
																) +
																IF (
																	old_det.nilai_valas_debet IS NULL
																	OR old_det.nilai_valas_debet = '',
																	0,
																	old_det.nilai_valas_debet
																) -
																IF (
																	old_det.nilai_valas_kredit IS NULL
																	OR old_det.nilai_valas_kredit = '',
																	0,
																	old_det.nilai_valas_kredit
																)
															)
															WHERE
																old_det.bln = '" . $Month_Pros . "'
															AND old_det.thn = '" . $Tahun_Pros . "'
															AND new_det.bln = '" . $Bulan_Next . "'
															AND new_det.thn = '" . $Tahun_Next . "'
															AND old_det.kdcab = '" . $Nocab . '-' . $Subcab . "'
															AND old_det.`level` IN ('5', '4', '3')
															AND old_det.no_perkiraan NOT LIKE '39%'";
									$this->db->query($Upd_Saldo_next);
									
									
								}

								$Total_Laba			= $current_coa4 + $current_coa71 - $current_coa5 - $current_coa6 - $current_coa72 - $current_coa9;

								$det_Coa_Laba		= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => '3903-01-01'))->result();
								$Faktor_Laba		= 1;
								if ($det_Coa_Laba) {
									$Faktor_Laba	= $det_Coa_Laba[0]->faktor;
									$det_Laba_Tahun	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => '3902-01-01'))->result();

									## JIKA POSTING LABA BELUM DIPROSES => LABA TAHUN BERJALAN += LABA BERJALAN##									
									if ($Post_Laba !== '1') {
										$Laba_Uncalc_Laba	= $det_Coa_Laba[0]->saldoawal;
										//echo "laba $Laba_Uncalc_Laba ";
										//exit;
										
										if ($Month_Pros === 1) {  
										    //jika bulan JANUARI
											## AMBIL DATA LABA TAHUN BERJALAN ##
											$Tahun_Berjalan	= $Laba_Uncalc_Laba;
											if ($det_Laba_Tahun) {
												$Tahun_Berjalan	+= $det_Laba_Tahun[0]->saldoawal;
											}
                                     
													
											$Update_Uncalc_Tahun	= "UPDATE COA SET saldoawal= saldoawal + (" . $Tahun_Berjalan . ") WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3901-01-01','3901-01-00','3901-00-00')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Tahun);

											$Update_Uncalc_Laba	= "UPDATE COA SET saldoawal= 0,debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00','3903-00-00','3903-01-00','3903-01-01')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Laba);
										} else {
										
											$Update_Laba		= "UPDATE COA SET saldoawal='0',kredit='" . $Total_Laba . "' WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3903-01-01','3903-01-00','3903-00-00')";
								//echo $Update_Laba;exit;saldoawal=0,debet=0 
								$this->db->query($Update_Laba);

											$Update_Uncalc_Laba	= "UPDATE COA SET saldoawal= saldoawal + (" . $Laba_Uncalc_Laba . ") WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Laba);
										}
									}
								}

								$Update_Laba		= "UPDATE COA SET saldoawal='0',kredit='" . $Total_Laba . "' WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3903-01-01','3903-01-00','3903-00-00')";
								//echo $Update_Laba;exit;saldoawal=0,debet=0 
								$this->db->query($Update_Laba);

								$Laba_Ditahan		= 0;
								$Laba_Tahun_Ini		= 0;
								$Laba_Tahun_Ini_1   = 0;

								## NEED KONFIRMASI ##

								if ($OK_Next === 1) {
									if ($Month_Pros === 12) {
										$Laba_Ditahan	 = ($Total_Laba * $Faktor_Laba);
										$Qry_Laba_Tahan	 = "SELECT * FROM COA WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3901-01-01','3902-01-01') AND `level`='5'";
										$det_Laba_Tahan	 = $this->db->query($Qry_Laba_Tahan)->result();
										if ($det_Laba_Tahan) {
											foreach ($det_Laba_Tahan as $keyL => $valL) {
												$Laba_Ditahan	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
											}
										}

										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Ditahan . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3901-01-01','3901-01-00','3901-00-00')";
									} else if ($Month_Pros === 1) {
										$Laba_Tahun_Ini	 = ($Total_Laba * $Faktor_Laba);

										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
									} else {
										$Qry_Coa_Laba	 = "SELECT * FROM COA WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Lalu . "' AND thn='" . $Tahun_Lalu . "' AND no_perkiraan IN ('3902-01-01','3903-01-01') AND `level`='5'";
										$det_Coa_Laba	 = $this->db->query($Qry_Coa_Laba)->result();
										if ($det_Coa_Laba) {
											foreach ($det_Coa_Laba as $keyL => $valL) {
												if ($valL->no_perkiraan === '3902-01-01') {
													$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
												} else {
													$saldoawal_3903 = $valL->saldoawal * $valL->faktor;
													$kredit_3903 = $valL->kredit;
													$Laba_Tahun_Ini_1	+= ($valL->saldoawal + $valL->debet - $valL->kredit) * $valL->faktor;
													$Laba_Tahun_Ini = $Laba_Tahun_Ini_1+$Cogm;// 3903-01-01
												}
											}
										}
										## Rin 070420 ##
										// $get_data_3903 =$this->db->query("SELECT * FROM coa WHERE kdcab = '".$Nocab.'-'.$Subcab."' AND bln='".$Bulan_Lalu."' AND thn='".$Tahun_Lalu."' AND no_perkiraan = '3903-01-01' AND `level`='5'")->result();

										// if($get_data_3903){
										// 	foreach($get_data_3903 as $keyL=>$valL){
										// 		if($valL->no_perkiraan === '3902-01-01'){
										// 			$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
										// 		}else{
										// 			$saldoawal_3903
										// 			$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit) * $valL->faktor; // 3903-01-01
										// 		}
										// 	}
										// }
										// if ($Laba_Tahun_Ini === "0") {
										// 	$Laba_Tahun_Ini = $saldoawal_3903;
										// 	$Update_Laba_Next	= "UPDATE coa SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
										// } else {
										// echo $Laba_Tahun_Ini;
										// exit;
										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
										// }
										## -Rin 070420- ##
									}

									$this->db->query($Update_Laba_Next);
								}


								$Update_Periode	= "UPDATE periode SET stspost='1', post_laba='1' WHERE kdcab like '%" . $Kode_Cabang . "%' AND periode='" . $Periode_Cari . "'";
								$this->db->query($Update_Periode);
								$this->db->trans_complete();

								if ($this->db->trans_status() !== TRUE) {
									$this->db->trans_rollback();
									$rows_Return	= array(
										'pesan'			=> 'Posting process failed. Please try again later ...',
										'status'		=> 3
									);
								} else {
									$this->db->trans_commit();
									$rows_Return	= array(
										'pesan'		=> 'Posting process success. Thanks ...',
										'status'	=> 1

									);
								}
							}
						} else {
							$this->db->trans_complete();
							$this->db->trans_rollback();

							$rows_Return	= array(
								'status'		=> 3,
								'pesan'			=> 'No records was found......'
							);
						}
					} else {
						$this->db->trans_complete();
						$this->db->trans_rollback();
						$Message_Error	= "<h4 class='alert-heading'>Unbalance Journal !</h4>
										<hr>
										<p>";

						$Ambil_Unbalance	= "SELECT
													nomor,tipe,
													SUM(IF(debet > 0, debet, 0)) AS total_debet,
													SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
												FROM
													jurnal
												WHERE
													tanggal LIKE '" . $Periode_Proses . "%'
												AND (

													IF (debet > 0, debet, 0) > 0
													OR
													IF (kredit > 0, kredit, 0) > 0
												)
												GROUP BY nomor,tipe";
//												AND nomor LIKE '" . $Nocab . "%'

						$rows_Balance		= $this->db->query($Ambil_Unbalance)->result();
						if ($rows_Balance) {
							foreach ($rows_Balance as $key => $vals) {
								if ($vals->total_debet !== $vals->total_kredit) {
									$Message_Error	.= "Journal No : <b>" . $vals->nomor . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Type : <b>" . $vals->tipe . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Debet : <b>" . number_format($vals->total_debet) . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kredit : <b>" . number_format($vals->total_kredit) . "</b><br>";
								}
							}
						}
						$Message_Error	.= "</p><hr>";
						$rows_Return	= array(
							'status'		=> 2,
							'pesan'			=> $Message_Error
						);
					}
				}
			} else {
				$rows_Return	= array(
					'status'		=> 3,
					'pesan'			=> 'Period not found....'
				);
			}
		} else {
			$rows_Return	= array(
				'status'		=> 3,
				'pesan'			=> 'No record was found to process......'
			);
		}
		echo json_encode($rows_Return);
	}


	function proses_unposting()
	{
		$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$Kode_Cabang	= $this->input->post('kode_cabang');
		
		$det_Cabang		= $this->db->get_where('pastibisa_tb_cabang', array('kdcab' => $Kode_Cabang))->result();
		$Nocab			= $det_Cabang[0]->nocab;
		$Subcab			= $det_Cabang[0]->subcab;

		$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O'  ORDER BY periode ASC LIMIT 1";
		$get_Periode	= $this->db->query($Query_Periode)->result();
		$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
		$id_periode     = $get_Periode[0]->id;
		$Tahun_Pros		= $Pecah_Periode[1];
		$Month_Pros		= intval($Pecah_Periode[0]);
		$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		/*
		$Periode_Pilih	= $this->input->post('periode');		
		$Pecah_Tgl		= explode(" ",$Periode_Pilih);		
		$Tahun_Pros		= $Pecah_Tgl[1];
		$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
		
		*/

		$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
		$this->db->trans_start();

		## PROSES UNPOSTING ##
		$Qry_UnPost	= "UPDATE COA SET debet=0,kredit=0,nilai_valas_debet=0,nilai_valas_kredit=0 WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "'";
		$this->db->query($Qry_UnPost);
		
		
		## UPDATE PERIODE ##
		$Qry_postlaba	= "UPDATE periode SET post_laba=0 WHERE id='$id_periode'";
		$this->db->query($Qry_postlaba);

		$this->db->trans_complete();

		if ($this->db->trans_status() !== TRUE) {
			$this->db->trans_rollback();
			$rows_Return	= array(
				'pesan'			=> 'Unposting process failed. Please try again later ...',
				'status'		=> 3
			);
		} else {
			$this->db->trans_commit();
			$rows_Return	= array(
				'pesan'		=> 'Unposting process success. Thanks ...',
				'status'	=> 1

			);
		}
		echo json_encode($rows_Return);
	}

	function detail_unbalance()
	{
		$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$Kode_Cabang	= $this->input->post('cabang');


		$det_Cabang		= $this->db->get_where('pastibisa_tb_cabang', array('kdcab' => $Kode_Cabang))->result();
		$Nocab			= $det_Cabang[0]->nocab;
		$Subcab			= $det_Cabang[0]->subcab;

		$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O' ORDER BY periode ASC LIMIT 1";
		$get_Periode	= $this->db->query($Query_Periode)->result();
		$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
		$Tahun_Pros		= $Pecah_Periode[1];
		$Month_Pros		= intval($Pecah_Periode[0]);
		$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		/*
		$Periode_Pilih	= $this->input->post('periode');		
		$Pecah_Tgl		= explode(" ",$Periode_Pilih);		
		$Tahun_Pros		= $Pecah_Tgl[1];
		$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
		
		*/

		$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		$Qry_Cek_Balance	= "SELECT
									nomor,tipe,
									SUM(IF(debet > 0, debet, 0)) AS total_debet,
									SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
								FROM
									jurnal
								WHERE
									tanggal LIKE '" . $Periode_Proses . "%'
								AND (

									IF (debet > 0, debet, 0) > 0
									OR
									IF (kredit > 0, kredit, 0) > 0
								)
								GROUP BY nomor,tipe";
//								AND nomor LIKE '" . $Nocab . "%'

		$det_Balance		= $this->db->query($Qry_Cek_Balance)->result();

		$rows_data		= array(
			'title'			=> 'List Unbalance Jurnal',
			'cab_pilih'		=> $det_Cabang[0]->cabang,
			'periode_pilih'	=> $Periode_Pilih,
			'rows_header'	=> $det_Balance
		);

		$this->load->view($this->folder . '/main_unbalance', $rows_data);
	}
	
	
	function periode_end()
	{
		$data['judul']			= "Periode End";
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		
		
		
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$data['bln']		= $bln_aktif;
		$data['thn']		= $thn_aktif;
		$data['proses'] = 0;

		$this->load->view("report/v_periode_end", $data);
	}
	
	
	function proses_periode_end()
	{
		$post	= $this->input->post();	
		
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();		
						
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		
		$Keterangan_delete		    = 'Periode End '.$bln_aktif.'-'.$thn_aktif;
		
		$this->db->query("DELETE FROM jurnal WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM javh WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM sentralsistem.tr_kartu_piutang WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM sentralsistem.tr_kartu_hutang WHERE keterangan='$Keterangan_delete'");

		
		$Keterangan_Delete1		    = 'JURNAL TUTUP BULAN FAV '.$bln_aktif.'/'.$thn_aktif;
		
		$this->db->query("DELETE FROM jurnal WHERE keterangan='$Keterangan_Delete1'");
		$this->db->query("DELETE FROM javh WHERE keterangan='$Keterangan_Delete1'");
		
		
		$this->db->query("DELETE FROM periode_end_proses WHERE bulan='$bln_aktif' AND tahun='$thn_aktif'");
		
		$this->proses_posting();
			
		for($i=0;$i < count($this->input->post('matauang'));$i++){
			$datadetail = array(                
                'matauang'     => $this->input->post('matauang')[$i],
				'kurs'         => str_replace(",","",$this->input->post('kurs_periode')[$i]),
				'bulan'        => $bln_aktif,
				'tahun'        => $thn_aktif,
				'created_by'   => $this->session->userdata('pn_name'),
				'created_on'   => date('Y-m-d H:i:s')
                );
             $this->db->insert('periode_end',$datadetail);	
			 
			 $this->db->insert('periode_end_proses',$datadetail);	


			 if($bln_aktif == 10 || $bln_aktif == 11 || $bln_aktif == 12) {
				$bln = $bln_aktif; 
				
			 } else {
				$bln = substr($bln_aktif,-1); 
			 }
			 
			 			 
			 $matauang =  $this->input->post('matauang')[$i];
			 $kurs     =  str_replace(",","",$this->input->post('kurs_periode')[$i]);
			 
			 
			 if($kurs !=0){
			 
						
									 
							$carinokir = $this->db->query("SELECT * FROM coa_master WHERE mata_uang='$matauang'")->result();


							
							  
            
							  foreach ($carinokir as $cs => $val) {
									$no_perkiraan	= $val->no_perkiraan;					
									$carisaldo = $this->db->query("SELECT * FROM COA WHERE bln='$bln' AND thn='$thn_aktif' AND no_perkiraan='$no_perkiraan'")->row();
										
								
								$Saldo_Awal = $carisaldo->saldoawal;
								$Tot_Debet  = $carisaldo->debet;
								$Tot_Kredit = $carisaldo->kredit;
								$Faktor_Kali = $carisaldo->faktor;
								$Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
								
								$Saldo_Awal_Valas = $carisaldo->saldo_valas;
								$TotVal_Debet  = $carisaldo->nilai_valas_debet;
								$TotVal_Kredit = $carisaldo->nilai_valas_kredit;
								$Saldo_Akhir_Valas		= ($Saldo_Awal_Valas + $TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
								

								
								
							if($Saldo_Akhir_Valas !=0){
								$kurs_histori = round($Saldo_Akhir/$Saldo_Akhir_Valas,2);	
								
                                $Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;								
								$saldo_idr_baru = $Saldo_Akhir_Valas*$kurs;								
								$selisih_kurs   = $Saldo_Akhir-$saldo_idr_baru;
								$total          = $saldo_idr_baru+$selisih_kurs;
								
								
									     
										 
										 if($bln_aktif=='01'){
												$hari =31;
											} elseif($bln_aktif=='02'){
												$hari =29;
											} elseif($bln_aktif=='03'){
												$hari =31;
											} elseif($bln_aktif=='04'){
												$hari =30;
											} elseif($bln_aktif=='05'){
												$hari =31;
											} elseif($bln_aktif=='06'){
												$hari =30;
											} elseif($bln_aktif=='07'){
												$hari =31;
											} elseif($bln_aktif=='08'){
												$hari =31;
											} elseif($bln_aktif=='09'){
												$hari =30;
											} elseif($bln_aktif=='10'){
												$hari =31;
											} elseif($bln_aktif=='11'){
												$hari =30;
											} elseif($bln_aktif=='12'){
												$hari =31;
											}
										 
										

										 $Tgl_Inv=$thn_aktif.'-'.$bln_aktif.'-'.$hari ;
										
									   	 $convertDate   = $Tgl_Inv;
										 $nojvcost		= $this->get_Nomor_Jurnal_Sales('101', $convertDate);
										
										 $Keterangan_INV		    = 'Periode End '.$bln_aktif.'-'.$thn_aktif;
										 $Keterangan_INV2	    = 'Periode End '.$bln_aktif.'-'.$thn_aktif.' '.$Saldo_Akhir_Valas.' '.'Kurs'.$kurs;
				 
										 
										 
										 $det_Jurnal				= array(); 
										
										$dp_cust ='2102-01-03';
										$ap_usd  ='2101-01-04';
										
										
										if (strpos($no_perkiraan, '2102-01-03') !== false || strpos($no_perkiraan, '2101-01-04') !== false) {
																								
										 
										 
													if($selisih_kurs > 0){
														 
													$det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => $selisih_kurs,
														   'kredit'        => 0,
														  
							 
													 );									 
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs,
														
							 
													 );
													 
													  $this->db->insert_batch('jurnal',$det_Jurnal);
													  
													  
													  //$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
														$nojvcosthutang		    = 'SYSTEM';
																					 
														$det_Jurnalhutang				= array(); 
														
														
														$det_Jurnalhutang[]			= array(
																   'nomor'         => $nojvcosthutang,
																   'tanggal'       => $Tgl_Inv,
																   'tipe'          => 'JV',
																   'no_perkiraan'  => $no_perkiraan,
																   'keterangan'    => $Keterangan_INV,
																   'no_reff'       => $kurs_histori, 
																   'debet'         => $selisih_kurs,
																   'kredit'        => 0,
																   'id_supplier'   => '-',
																   'nama_supplier' => '-',
																

															 );
															 
														// $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnalhutang);
														
														
													  
													  
													  
																			
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs,
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
													  
													  
													 }
													 
													 
													 
													 
													 elseif($selisih_kurs < 0){
														 
																				 
													
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => $selisih_kurs*(-1),
														   'kredit'        => 0,
														  
							 
													 );
													 
													  $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs*(-1),
														   
							 
													 );
													
																					
													 
							 
													 // $this->db->where('no_reff', $id);
													 // $this->db->where('jenis_jurnal', $ket);
													 // $this->db->delete('jurnal');
											 
													 $this->db->insert_batch('jurnal',$det_Jurnal);
													 
													 
													 
													 
													 //$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
														$nojvcosthutang		    = 'SYSTEM';
																					 
														$det_Jurnalhutang				= array(); 
														
														
														$det_Jurnalhutang[]			= array(
																   'nomor'         => $nojvcosthutang,
																   'tanggal'       => $Tgl_Inv,
																   'tipe'          => 'JV',
																   'no_perkiraan'  => $no_perkiraan,
																   'keterangan'    => $Keterangan_INV,
																   'no_reff'       => $kurs_histori, 
																   'debet'         => 0,
																   'kredit'        => $selisih_kurs*(-1),
																   'id_supplier'   => '-',
																   'nama_supplier' => '-',
																

															 );
															 
														// $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnalhutang);
														
														
													  
													 
													 
													 
																			
																		
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs*(-1),
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
							 
												}
									
									
									} else {
											
											if($selisih_kurs > 0){
														 
													$det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs,
														  
							 
													 );									 
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => $selisih_kurs,
														   'kredit'        => 0,
														
							 
													 );
													 
													  $this->db->insert_batch('jurnal',$det_Jurnal);
													  
													  
													  
																			
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs,
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
													  
													  
													 }
													 
													 
													 
													 
													 elseif($selisih_kurs < 0){
														 
																				 
													
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs*(-1),
														  
							 
													 );
													 
													  $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => $selisih_kurs*(-1),
														   'kredit'        => 0,
														   
							 
													 );
													
																					
													 
							 
													 // $this->db->where('no_reff', $id);
													 // $this->db->where('jenis_jurnal', $ket);
													 // $this->db->delete('jurnal');
											 
													 $this->db->insert_batch('jurnal',$det_Jurnal);
													 
													 
													 
																			
																		
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs*(-1),
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
							 
												}
									}	
							}
								  
						}
			
              
			 $this->update_invoice($matauang,$kurs);
			
			}
			
			
		}
	
	$this->jurnal_favorable();
	
	
	
	}
	
	
	function jurnal_favorable() {
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bulan	= substr($tgl_periode_aktif, 0, 2);
				$tahun	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		
		
		
		$tanggal_akhir = date("$tahun-$bulan-t",time());
		
		if($bulan=='01'){
			$hari =31;
		} elseif($bulan=='02'){
			$hari =29;
		} elseif($bulan=='03'){
			$hari =31;
		} elseif($bulan=='04'){
			$hari =30;
		} elseif($bulan=='05'){
			$hari =31;
		} elseif($bulan=='06'){
			$hari =30;
		} elseif($bulan=='07'){
			$hari =31;
		} elseif($bulan=='08'){
			$hari =31;
		} elseif($bulan=='09'){
			$hari =30;
		} elseif($bulan=='10'){
			$hari =31;
		} elseif($bulan=='11'){
			$hari =30;
		} elseif($bulan=='12'){
			$hari =31;
		}
		
		$tgl = $tahun.'-'.$bulan.'-'.$hari;
		
		
		
		
		
		$consum1 = $this->db->query("select sum(kredit-debet) AS consumable FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-01'")->row();
		$standart_consumable = $consum1->consumable;		
		$consum2 = $this->db->query("select sum(kredit-debet) AS directlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-02'")->row();
		$standart_dl = $consum2->directlabour;
		$consum3 = $this->db->query("select sum(kredit-debet) AS indirectlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-03'")->row();
		$standart_idl = $consum3->indirectlabour;
		$consum4 = $this->db->query("select sum(kredit-debet) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-04'")->row();
		$standart_foh = $consum4->foh;
		
		$consumact1 = $this->db->query("select sum(debet-kredit) AS consumable FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5201%'")->row();
		$aktual_consumable = $consumact1->consumable;
		$consumact2 = $this->db->query("select sum(debet-kredit) AS directlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5202%'")->row();
		$aktual_dl = $consumact2->directlabour;
		$consumact3 = $this->db->query("select sum(debet-kredit) AS indirectlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5203%'")->row();
		$aktual_idl = $consumact3->indirectlabour;
		$consumact4 = $this->db->query("select sum(debet-kredit) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5204%'")->row();
		$aktual_foh = $consumact4->foh;
		$consumact5 = $this->db->query("select sum(debet-kredit) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5209%'")->row();
		$aktual_inst = $consumact5->foh;
		 
		
		$selisih_consumable = $aktual_consumable - $standart_consumable;
		$selisih_dl  = $aktual_dl - $standart_dl;
		$selisih_idl = $aktual_idl - $standart_idl;
		$selisih_foh = ($aktual_foh) - $standart_foh;
		
		
		if($selisih_consumable > 0 ){
		$kreditconsumable  =$selisih_consumable;
		$debetconsumable =0;
		}else{
		$kreditconsumable  =0;
		$debetconsumable =$selisih_consumable*-1;
		}
		
		if($selisih_dl > 0 ){
		$kreditdl  =$selisih_dl;
		$debetdl =0;
		}else{
		$kreditdl  =0;
		$debetdl =$selisih_dl*-1;
		}
		
		if($selisih_idl > 0 ){
		$kreditidl  =$selisih_idl;
		$debetidl =0;
		}else{
		$kreditidl  =0;
		$debetidl =$selisih_idl*-1;
		}
		
		if($selisih_foh > 0 ){
		$kreditfoh  =$selisih_foh;
		$debetfoh =0;
		}else{
		$kreditfoh  =0;
		$debetfoh =$selisih_foh*-1;
		}
		
		$nilaitotaljurnal  = $aktual_consumable + $debetconsumable + $aktual_dl + $debetdl + $aktual_idl + $debetidl + $aktual_foh + $debetfoh;
		
		
		// print($aktual_consumable);
		// echo"<br>";
		// print($aktual_dl);
		// echo"<br>";
		// print($aktual_idl);
		// echo"<br>";
		// print($aktual_foh);
		// echo"<br>";
		
		// exit;
		
		
		$sampleDate = date('Y-m-d');
		$convertDate = date("Y-m-d", strtotime($sampleDate));
		
		
		$Nomor_JV				= $this->get_Nomor_Jurnal_Sales('101', $convertDate);
		$Keterangan_INV1		    = 'JURNAL TUTUP BULAN FAV '.$bulan.'/'.$tahun;
		$nomordoc               =  $bulan.$tahun;
		
		
		//$tahun.'-'.$bulan.'-'.$akhir;
		#JURNAL TUTUP BULAN

		
		//CONSUMABLE        
							
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-01', //CONSUMABLE CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_consumable,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5205-01-01',	//CONSOMABLE PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_consumable
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-03',	//FAVORABLE / UNFAVORABLE CONSOMABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditconsumable,
					'kredit'        => $debetconsumable  
					//'jenis_jurnal'  => 'invoicing'
				);
				
		//DL			
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-02', //DIRECT LABOUR CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_dl,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5206-01-01', //	DIRECT LABOUR PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_dl
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-01', //	FAVORABLE / UNFAVORABLE DIRECT LABOUR
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditdl,
					'kredit'        => $debetdl   
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
		//IDL			
				
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-03', // INDIRECT LABOUR CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_idl,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5207-01-01', //	INDIRECT LABOUR PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_idl
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-02', //	FAVORABLE / UNFAVORABLE INDIRECT LABOUR
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditidl,
					'kredit'        => $debetidl   
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
				
			//FOH			
				
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-04', //FACTORY OVERHEAD CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_foh,
					'kredit'        => 0 
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5208-01-01', //	FOH PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_foh
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-04', //	FAVORABLE / UNFAVORABLE FACTORY OVERHEAD
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditfoh,
					'kredit'        => $debetfoh   
					//'jenis_jurnal'  => 'invoicing'
				);
				
			

		
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $nilaitotaljurnal,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $bulan,
			'tahun'				=> $tahun,
			'user_id'			=> '01',
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);
		// print_r($datajurnal1);
		// exit;
			$this->db->insert('javh',$dataJVhead);
			$this->db->insert_batch('jurnal',$det_Jurnaltes1);

			// $datapiutang = array(
				// 'tipe'       	 => 'JV',
				// 'nomor'       	 => $Nomor_JV,
				// 'tanggal'        => $tgl,
				//'no_perkiraan'    => $coa_penjualan,
				// 'no_perkiraan'  => '1102-01-01',
				// 'keterangan'    => $Keterangan_INV1,
				// 'no_reff'       => $nomordoc,
				// 'debet'         => $Jml_Ttl,
				// 'kredit'        =>  0,
				// 'id_supplier'   => $Id_klien,
				// 'nama_supplier' => $Nama_klien,
			// );
			// $this->db->insert('tutup_bulan',$datapiutang);
			
		// $this->proses_unposting();
		// $this->proses_posting();
		$data['judul']			= "Periode End";
	    $data['bln']		= $bulan;
		$data['thn']		= $tahun;
		$data['proses'] = 1;
		$this->load->view('report/v_periode_end_proses', $data); 
	}
	
	
	function get_Nomor_Jurnal_Sales($Cabang='',$Tgl_Inv=''){
		//$db2 = $this->load->database('accounting', TRUE);
		$nocab			= 'A';
		$bulan_Proses	= date('Y',strtotime($Tgl_Inv));
		$Urut			= 1;
		$Query_Cab		= "SELECT subcab,nomorJC FROM pastibisa_tb_cabang WHERE nocab='".$Cabang."'";
		$Pros_Cab		= $this->db->query($Query_Cab);
		$det_Cab		= $Pros_Cab->result_array();
		if($det_Cab){
			$nocab		= $det_Cab[0]['subcab'];
			$Urut		= intval($det_Cab[0]['nomorJC']) + 1;
		}
		$Format			= $Cabang.'-'.$nocab.'JV'.date('y',strtotime($Tgl_Inv));
		$Nomor_JS		= $Format.str_pad($Urut, 5, "0", STR_PAD_LEFT);
		$Query_Cab ="UPDATE pastibisa_tb_cabang SET nomorJC=(nomorJC + 1),lastupdate='".date("Y-m-d")."' WHERE nocab='".$Cabang."'";
		$this->db->query($Query_Cab);
		return $Nomor_JS;
	}
	
	
	function update_invoice($matauang,$kurs) {
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bulan	= substr($tgl_periode_aktif, 0, 2);
				$tahun	= substr($tgl_periode_aktif, 3, 4);
			}
		}	

		if($bulan=='01'){
			$hari =31;
		} elseif($bulan=='02'){
			$hari =29;
		} elseif($bulan=='03'){
			$hari =31;
		} elseif($bulan=='04'){
			$hari =30;
		} elseif($bulan=='05'){
			$hari =31;
		} elseif($bulan=='06'){
			$hari =30;
		} elseif($bulan=='07'){
			$hari =31;
		} elseif($bulan=='08'){
			$hari =31;
		} elseif($bulan=='09'){
			$hari =30;
		} elseif($bulan=='10'){
			$hari =31;
		} elseif($bulan=='11'){
			$hari =30;
		} elseif($bulan=='12'){
			$hari =31;
		}
		
		$tgl = $tahun.'-'.$bulan.'-'.$hari;
				
		
		$invoice = $this->db->query("SELECT * FROM view_invoice WHERE base_cur='$matauang'")->result();
		
		foreach($invoice AS $inv => $val){
			
			$no_inv = $val->no_invoice;
			$id_cust = $val->id_customer;
			$nm_cust = $val->nm_customer;
			
			
			$nilai_invoice = $val->sisa_invoice;
			$nilai_retensi = $val->sisa_invoice_retensi2;
			$kurs_awal     = $val->kurs_jual;
			$kurs_akhir    = $kurs;
			
			$nilai_inv_awal  = $kurs_awal*$nilai_invoice;
			$nilai_inv_akhir = $kurs_akhir*$nilai_invoice;
			$selisih_inv         = $nilai_inv_awal - $nilai_inv_akhir;
			
			$nilai_ret_awal  = $kurs_awal*$nilai_retensi;
			$nilai_ret_akhir = $kurs_akhir*$nilai_retensi;
			$selisih_ret         = $nilai_ret_awal - $nilai_ret_akhir;
			
			
			
			$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
			$nojvcost		    = 'SYSTEM';
										 
			$det_Jurnal				= array(); 
			
			if($selisih_inv > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-02',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv,
					   'id_supplier'        => $id_cust,
					   'nama_supplier'        => $nm_cust,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			elseif($selisih_inv < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-02',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => $selisih_inv*(-1),
					   'kredit'        => 0,
					   'id_supplier'   => $id_cust,
					   'nama_supplier' => $nm_cust,
					
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			
			
			if($selisih_ret > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-04',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv,
					   'id_supplier'        => $id_cust,
					   'nama_supplier'        => $nm_cust,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			elseif($selisih_ret < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-04',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => $selisih_inv*(-1),
					   'kredit'        => 0,
					   'id_supplier'   => $id_cust,
					   'nama_supplier' => $nm_cust,
					
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			
		}
		
		
		$hutang = $this->db->query("SELECT * FROM view_hutang WHERE mata_uang='$matauang'")->result();
		
		foreach($hutang AS $htg => $val1){
			
			$no_po = $val1->no_po;
			$id_supp = $val1->id_supplier;
			$nm_supp = $val1->nm_supplier;
			
			
			$nilai_invoice2 = $val1->sisa_hutang_kurs;
			$kurs_awal2     = $val1->kurs_terima;
			$kurs_akhir2    = $kurs;
			
			$nilai_inv_awal2  = $kurs_awal2*$nilai_invoice2;
			$nilai_inv_akhir2 = $kurs_akhir2*$nilai_invoice2;
			$selisih_inv2         = $nilai_inv_awal2 - $nilai_inv_akhir2;
			
						
			
			
			$Keterangan_INV2		    = 'Periode End '.$bulan.'-'.$tahun;
			$nojvcost2		    = 'SYSTEM';
										 
			$det_Jurnal				= array(); 
			
			if($selisih_inv2 < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost2,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '2101-01-04',
					   'keterangan'    => $Keterangan_INV2,
					   'no_reff'       => $no_po, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv2*(-1),
					   'id_supplier'        => $id_supp,
					   'nama_supplier'        => $nm_supp,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnal);
			
			}
			
			elseif($selisih_inv2 > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost2,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '2101-01-04',
					   'keterangan'    => $Keterangan_INV2,
					   'no_reff'       => $no_po, 
					   'debet'         => $selisih_inv2,
					   'kredit'        => 0,
					   'id_supplier'   => $id_supp,
					   'nama_supplier' => $nm_supp,
					

				 );
		    $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnal);
			
			}
			
			
			
			
			
			
		}
		
	}
}
=======
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Posting_new extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		$this->load->helper('menu');
		$this->load->model('Posting_new_model');
		$this->load->model('Report_model');
		$this->folder	= 'report';
	}

	function index()
	{
		$cab_pilih		= $this->session->userdata('singkat_cbg');
		$rows_cabang	= $this->Posting_new_model->get_cabang();
		$rows_data		= array(
			'title'			=> 'Jurnal Posting',
			'rows_cabang'	=> $rows_cabang,
			'cab_pilih'		=> $cab_pilih
		);

		$this->load->view($this->folder . '/main_posting', $rows_data);
	}

	function proses_posting($Jenis_Posting = 'N')
	{
		if ($this->input->post()) {
			//echo"<pre>";print_r($this->input->post());exit;
			$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$Kode_Cabang	= $this->input->post('kode_cabang');
			$current_coa4 = 0;
			$current_coa5 = 0;
			$current_coa6 = 0;
			$current_coa71 = 0;
			$current_coa72 = 0;
			$current_coa8 = 0;
			$current_coa9 = 0;
			
			
			
			$currentval_coa4 = 0;
			$currentval_coa5 = 0;
			$currentval_coa6 = 0;
			$currentval_coa71 = 0;
			$currentval_coa72 = 0;
			$currentval_coa8 = 0;
			$currentval_coa9 = 0;

			/*
			$Periode_Pilih	= $this->input->post('periode_proses');			
			$Pecah_Tgl		= explode(" ",$Periode_Pilih);
			
			$Tahun_Pros		= $Pecah_Tgl[1];
			$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
			*/

			$det_Cabang		= $this->db->query("select * from pastibisa_tb_cabang where kdcab like '%$Kode_Cabang'")->result();

			$Nocab			= $det_Cabang[0]->nocab;
			$Subcab			= $det_Cabang[0]->subcab;

			if ($Jenis_Posting === 'Y') {
				$Tahun_Pros		= date('Y');
				$Month_Pros		= date('n');
				$Periode_Pilih	= date('F Y');
			} else {
				$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O' ORDER BY periode ASC LIMIT 1";
				$get_Periode	= $this->db->query($Query_Periode)->result();
				$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
				$Tahun_Pros		= $Pecah_Periode[1];
				$Month_Pros		= intval($Pecah_Periode[0]);
				$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
			}


			$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
			$Periode_Cari	= date('m-Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

			$Tahun_Lalu	= date('Y', mktime(0, 0, 0, $Month_Pros - 1, 1, $Tahun_Pros));
			$Bulan_Lalu	= date('n', mktime(0, 0, 0, $Month_Pros - 1, 1, $Tahun_Pros));


         
		   

			$Tahun_Now		= date('Y');
			$Bulan_Now		= date('n');
			$Periode_Now	= date('Y-m');

			$OK_Next		= 0;
			if ($Periode_Proses < $Periode_Now) {
				$OK_Next	= 1;
				$Tahun_Next	= date('Y', mktime(0, 0, 0, $Month_Pros + 1, 1, $Tahun_Pros));
				$Bulan_Next	= date('n', mktime(0, 0, 0, $Month_Pros + 1, 1, $Tahun_Pros));
			}

			## AMBIL PERIODE ##
			$det_Periode	= $this->db->query("select * from periode where kdcab like '%$Kode_Cabang' and periode='$Periode_Cari'")->result();
			if ($det_Periode) {
				if ($det_Periode[0]->stsaktif === 'C') {

					$rows_Return	= array(
						'status'		=> 3,
						'pesan'			=> 'Data has been locked....'
					);
				} else {
					$Post_Laba		= $det_Periode[0]->post_laba;
					//echo "$Kode_Cabang $Periode_Cari post laba $Post_Laba";
					//exit;
					$this->db->trans_start();

					## PROSES UNPOSTING ##
					$Qry_UnPost	= "UPDATE COA SET debet=0,kredit=0,nilai_valas_debet=0,nilai_valas_kredit=0 WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "'";
					$this->db->query($Qry_UnPost);

			

					## CEK JURNAL BALANCE ATAU TIDAK ##
					$Qry_Cek_Balance	= "SELECT
												SUM(IF(debet > 0, debet, 0)) AS total_debet,
												SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
											FROM
												jurnal
											WHERE
												tanggal LIKE '" . $Periode_Proses . "%'
											AND (

												IF (debet > 0, debet, 0) > 0
												OR
												IF (kredit > 0, kredit, 0) > 0
											)";
//											AND nomor LIKE '" . $Nocab . "%'

					$det_Balance		= $this->db->query($Qry_Cek_Balance)->result();

					if ($det_Balance[0]->total_debet === $det_Balance[0]->total_kredit) {
						## AMBIL DATA JURNAL BASED ON NO PERKIRAAN ##
						$Qry_Perkiraan	= "SELECT
												no_perkiraan,
												SUM(debet) AS total_debet,
												SUM(kredit) AS total_kredit,
												SUM(nilai_valas_debet) AS total_valas_debet,
												SUM(nilai_valas_kredit) AS total_valas_kredit
											FROM
												jurnal
											WHERE
												tanggal LIKE '" . $Periode_Proses . "%'
											GROUP BY
												no_perkiraan";
//											AND nomor LIKE '" . $Nocab . "%'

						$det_Perkiraan	= $this->db->query($Qry_Perkiraan)->result();
						$Ok_Exists		= 0;
						$Pesan			= '';
						
						$Total_coa4		= $Total_coa5	= $Total_coa6 = $Total_coa71 = $Total_coa72	= $Total_coa9	= $Total_Laba	= 0;
						$Totalval_coa4  = $Totalval_coa5 = $Totalval_coa6 = $Totalval_coa71 = $Totalval_coa72 =$Totalval_coa9 = 0;
						
						if ($det_Perkiraan) {
							$intE		  	= 0;
							$Message_Error	= '';
							$Tot_Debet=0;
							foreach ($det_Perkiraan as $keyI => $valI) {
								$No_Coa		= $valI->no_perkiraan;
								$Tot_Debet	= $valI->total_debet;
								$Tot_Kredit	= $valI->total_kredit;
								$TotVal_Debet	= $valI->total_valas_debet;
								$TotVal_Kredit	= $valI->total_valas_kredit;


								
								
								$Level4		= substr($No_Coa, 0, 7) . '-00';
								$Level3		= substr($No_Coa, 0, 4) . '-00-00';
								$Level1		= substr($No_Coa, 0, 1);
								$Level2		= substr($No_Coa, 0, 2);

								$Coa5101    = substr($No_Coa, 0, 4);
								$Coa5103    = substr($No_Coa, 0, 4);


								

								## CEK EXIST NO PERKIRAAN ##

								$Saldo_Awal				= 0;
								$Faktor_Kali			= 1;
								$Saldo_Awal_Valas		= 0;

								$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $No_Coa))->num_rows();
								if ($num_COA < 1) {
									$intE++;
									if ($intE === 1) {
										$Message_Error	= "<h4 class='alert-heading'>Unbalance Journal !</h4>
															<hr>
															<p>";
									}
									$Ambil_Perkiraan	= "SELECT
																nomor,
																tipe
															FROM
																jurnal
															WHERE
																tanggal LIKE '" . $Periode_Proses . "%'
															
															AND no_perkiraan ='" . $No_Coa . "'";
//															AND nomor LIKE '" . $Nocab . "%'

									$get_Perkiraan	= $this->db->query($Ambil_Perkiraan)->result();
									if ($get_Perkiraan) {
										foreach ($get_Perkiraan as $keyP => $valP) {
											$Message_Error	.= "Account No <b>" . $No_Coa . "</b>  In The Journal  <b><i>" . $valP->nomor . "</i> (" . $valP->tipe . ")</b>  Was Not Found At Master COA .<br>";
										}
									}
									$Ok_Exists		= 1;
								} else {
									$detail_COA		= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $No_Coa))->result();
									if ($detail_COA) {
										$Saldo_Awal		    = $detail_COA[0]->saldoawal;
										$Saldo_Awal_Valas	= $detail_COA[0]->saldo_valas;
										$Faktor_Kali	    = $detail_COA[0]->faktor;
									}
								}
								if ($Ok_Exists === 0) {
									$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $Level4))->num_rows();
									if ($num_COA < 1) {
										$intE++;
										$Message_Error	.= "Account No <b<" . $Level4 . "</b> Was Not Found At Master COA ..<br>";

										$Ok_Exists		= 1;
									}
								}

								if ($Ok_Exists === 0) {
									$num_COA	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => $Level3))->num_rows();
									if ($num_COA < 1) {
										$intE++;
										$Message_Error	.= "Account No " . $Level3 . " Was Not Found At Master COA ...<br>";
										$Ok_Exists		= 1;
									}
								}

								$Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
								$Saldo_Akhir_Valas		= ($Saldo_Awal_Valas + $TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
								
								if ($Ok_Exists === 0) {
									if ($Level1 === '4') {
										$Total_coa4		+= $Saldo_Akhir;
										$current_coa4   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa4		+= $Saldo_Akhir_Valas;
										$currentval_coa4    += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '5') {
										$Total_coa5		+= $Saldo_Akhir;
										$current_coa5   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa5	   += $Saldo_Akhir_Valas;
										$currentval_coa5   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '6') {
										$Total_coa6		+= $Saldo_Akhir;
										$current_coa6   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa6		+= $Saldo_Akhir_Valas;
										$currentval_coa6   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level1 === '9') {
										$Total_coa9		+= $Saldo_Akhir;
										$current_coa9   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa9	   += $Saldo_Akhir_Valas;
										$currentval_coa9   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level2 === '71') {
										$Total_coa71		+= $Saldo_Akhir;
										$current_coa71      += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa71		   += $Saldo_Akhir_Valas;
										$currentval_coa71      += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									} else if ($Level2 === '72') {
										$Total_coa72		+= $Saldo_Akhir;
										$current_coa72   += ($Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
										$Totalval_coa71		+= $Saldo_Akhir_Valas;
										$currentval_coa71   += ($TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
									}


									if ($Coa5101 === '5101') {
										$det_5101 = $this->db->query("SELECT no_perkiraan,
										 SUM(debet) AS total_debet,
										 SUM(kredit) AS total_kredit,
										 SUM(nilai_valas_debet) AS total_valas_debet,
										 SUM(nilai_valas_kredit) AS total_valas_kredit FROM
										 jurnal WHERE tanggal LIKE '$Periode_Proses%' AND no_perkiraan LIKE '$Coa5101%'")->row();

										$TotalD_5101		= $det_5101->total_debet;
										$TotalK_5101		= $det_5101->total_kredit;
										$current_5101      = ($TotalD_5101 - $TotalK_5101) * $Faktor_Kali;
										
									} else {
										$TotalD_5101		=0;
										$TotalK_5101		=0;
										$current_5101       =0;
									}

									if ($Coa5103 === '5103') {

										
										$det_5103 = $this->db->query("SELECT no_perkiraan,
										SUM(debet) AS total_debet,
										SUM(kredit) AS total_kredit,
										SUM(nilai_valas_debet) AS total_valas_debet,
										SUM(nilai_valas_kredit) AS total_valas_kredit FROM
										jurnal WHERE tanggal LIKE '$Periode_Proses%' AND no_perkiraan LIKE '$Coa5103%'")->row();

										$TotalD_5103		= $det_5103->total_debet;
										$TotalK_5103		= $det_5103->total_kredit;
										$current_5103      = ($TotalD_5103 - $TotalK_5103) * $Faktor_Kali;
									} else {
										$TotalD_5103		= 0;
										$TotalK_5103		= 0;
										$current_5103       = 0;
									}

									$Cogm = $current_5101+$current_5103;
									
									
									if($Tot_Debet==''){										
										$Totdebet =0;
									} else{
										$Totdebet =$Tot_Debet;
									}
									
									if($Tot_Kredit==''){										
										$Totkredit =0;
									} else{
										$Totkredit=$Tot_Kredit;
									}
									
									$Upd_Saldo		= "UPDATE COA SET debet = debet + " . $Totdebet . ", kredit = kredit + " . $Totkredit . ", nilai_valas_debet = nilai_valas_debet + " . $TotVal_Debet . ", nilai_valas_kredit = nilai_valas_kredit + " . $TotVal_Kredit . " WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('" . $No_Coa . "','" . $Level4 . "','" . $Level3 . "')";
									$this->db->query($Upd_Saldo);
								}
							}

							if ($Ok_Exists === 1) {
								$this->db->trans_complete();
								$this->db->trans_rollback();
								if (!empty($Message_Error)) $Message_Error	.= "</p><hr>";
								$rows_Return	= array(
									'status'		=> 2,
									'pesan'			=> $Message_Error
								);
							} else {
								## NEED KONFIRMASI ##

								if ($OK_Next === 1) {
									$Upd_Saldo_next		= "UPDATE COA new_det
															INNER JOIN COA old_det ON new_det.kdcab = old_det.kdcab
															AND new_det.no_perkiraan = old_det.no_perkiraan
															SET new_det.saldoawal = (
																IF (
																	old_det.saldoawal IS NULL
																	OR old_det.saldoawal = '',
																	0,
																	old_det.saldoawal
																) +
																IF (
																	old_det.debet IS NULL
																	OR old_det.debet = '',
																	0,
																	old_det.debet
																) -
																IF (
																	old_det.kredit IS NULL
																	OR old_det.kredit = '',
																	0,
																	old_det.kredit
																)
															),
															 new_det.saldo_valas = (
																IF (
																	old_det.saldo_valas IS NULL
																	OR old_det.saldo_valas = '',
																	0,
																	old_det.saldo_valas
																) +
																IF (
																	old_det.nilai_valas_debet IS NULL
																	OR old_det.nilai_valas_debet = '',
																	0,
																	old_det.nilai_valas_debet
																) -
																IF (
																	old_det.nilai_valas_kredit IS NULL
																	OR old_det.nilai_valas_kredit = '',
																	0,
																	old_det.nilai_valas_kredit
																)
															)
															WHERE
																old_det.bln = '" . $Month_Pros . "'
															AND old_det.thn = '" . $Tahun_Pros . "'
															AND new_det.bln = '" . $Bulan_Next . "'
															AND new_det.thn = '" . $Tahun_Next . "'
															AND old_det.kdcab = '" . $Nocab . '-' . $Subcab . "'
															AND old_det.`level` IN ('5', '4', '3')
															AND old_det.no_perkiraan NOT LIKE '39%'";
									$this->db->query($Upd_Saldo_next);
									
									
								}

								$Total_Laba			= $current_coa4 + $current_coa71 - $current_coa5 - $current_coa6 - $current_coa72 - $current_coa9;

								$det_Coa_Laba		= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => '3903-01-01'))->result();
								$Faktor_Laba		= 1;
								if ($det_Coa_Laba) {
									$Faktor_Laba	= $det_Coa_Laba[0]->faktor;
									$det_Laba_Tahun	= $this->db->get_where('COA', array('bln' => $Month_Pros, 'thn' => $Tahun_Pros, 'kdcab' => $Nocab . '-' . $Subcab, 'no_perkiraan' => '3902-01-01'))->result();

									## JIKA POSTING LABA BELUM DIPROSES => LABA TAHUN BERJALAN += LABA BERJALAN##									
									if ($Post_Laba !== '1') {
										$Laba_Uncalc_Laba	= $det_Coa_Laba[0]->saldoawal;
										//echo "laba $Laba_Uncalc_Laba ";
										//exit;
										
										if ($Month_Pros === 1) {  
										    //jika bulan JANUARI
											## AMBIL DATA LABA TAHUN BERJALAN ##
											$Tahun_Berjalan	= $Laba_Uncalc_Laba;
											if ($det_Laba_Tahun) {
												$Tahun_Berjalan	+= $det_Laba_Tahun[0]->saldoawal;
											}
                                     
													
											$Update_Uncalc_Tahun	= "UPDATE COA SET saldoawal= saldoawal + (" . $Tahun_Berjalan . ") WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3901-01-01','3901-01-00','3901-00-00')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Tahun);

											$Update_Uncalc_Laba	= "UPDATE COA SET saldoawal= 0,debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00','3903-00-00','3903-01-00','3903-01-01')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Laba);
										} else {
										
											$Update_Laba		= "UPDATE COA SET saldoawal='0',kredit='" . $Total_Laba . "' WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3903-01-01','3903-01-00','3903-00-00')";
								//echo $Update_Laba;exit;saldoawal=0,debet=0 
								$this->db->query($Update_Laba);

											$Update_Uncalc_Laba	= "UPDATE COA SET saldoawal= saldoawal + (" . $Laba_Uncalc_Laba . ") WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
											//echo $Update_Laba;exit;
											$this->db->query($Update_Uncalc_Laba);
										}
									}
								}

								$Update_Laba		= "UPDATE COA SET saldoawal='0',kredit='" . $Total_Laba . "' WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3903-01-01','3903-01-00','3903-00-00')";
								//echo $Update_Laba;exit;saldoawal=0,debet=0 
								$this->db->query($Update_Laba);

								$Laba_Ditahan		= 0;
								$Laba_Tahun_Ini		= 0;
								$Laba_Tahun_Ini_1   = 0;

								## NEED KONFIRMASI ##

								if ($OK_Next === 1) {
									if ($Month_Pros === 12) {
										$Laba_Ditahan	 = ($Total_Laba * $Faktor_Laba);
										$Qry_Laba_Tahan	 = "SELECT * FROM COA WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "' AND no_perkiraan IN ('3901-01-01','3902-01-01') AND `level`='5'";
										$det_Laba_Tahan	 = $this->db->query($Qry_Laba_Tahan)->result();
										if ($det_Laba_Tahan) {
											foreach ($det_Laba_Tahan as $keyL => $valL) {
												$Laba_Ditahan	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
											}
										}

										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Ditahan . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3901-01-01','3901-01-00','3901-00-00')";
									} else if ($Month_Pros === 1) {
										$Laba_Tahun_Ini	 = ($Total_Laba * $Faktor_Laba);

										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
									} else {
										$Qry_Coa_Laba	 = "SELECT * FROM COA WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Lalu . "' AND thn='" . $Tahun_Lalu . "' AND no_perkiraan IN ('3902-01-01','3903-01-01') AND `level`='5'";
										$det_Coa_Laba	 = $this->db->query($Qry_Coa_Laba)->result();
										if ($det_Coa_Laba) {
											foreach ($det_Coa_Laba as $keyL => $valL) {
												if ($valL->no_perkiraan === '3902-01-01') {
													$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
												} else {
													$saldoawal_3903 = $valL->saldoawal * $valL->faktor;
													$kredit_3903 = $valL->kredit;
													$Laba_Tahun_Ini_1	+= ($valL->saldoawal + $valL->debet - $valL->kredit) * $valL->faktor;
													$Laba_Tahun_Ini = $Laba_Tahun_Ini_1+$Cogm;// 3903-01-01
												}
											}
										}
										## Rin 070420 ##
										// $get_data_3903 =$this->db->query("SELECT * FROM coa WHERE kdcab = '".$Nocab.'-'.$Subcab."' AND bln='".$Bulan_Lalu."' AND thn='".$Tahun_Lalu."' AND no_perkiraan = '3903-01-01' AND `level`='5'")->result();

										// if($get_data_3903){
										// 	foreach($get_data_3903 as $keyL=>$valL){
										// 		if($valL->no_perkiraan === '3902-01-01'){
										// 			$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit);
										// 		}else{
										// 			$saldoawal_3903
										// 			$Laba_Tahun_Ini	+= ($valL->saldoawal + $valL->debet - $valL->kredit) * $valL->faktor; // 3903-01-01
										// 		}
										// 	}
										// }
										// if ($Laba_Tahun_Ini === "0") {
										// 	$Laba_Tahun_Ini = $saldoawal_3903;
										// 	$Update_Laba_Next	= "UPDATE coa SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
										// } else {
										// echo $Laba_Tahun_Ini;
										// exit;
										$Update_Laba_Next	= "UPDATE COA SET saldoawal='" . $Laba_Tahun_Ini . "',debet=0,kredit=0 WHERE kdcab = '" . $Nocab . '-' . $Subcab . "' AND bln='" . $Bulan_Next . "' AND thn='" . $Tahun_Next . "' AND no_perkiraan IN ('3902-01-01','3902-01-00','3902-00-00')";
										// }
										## -Rin 070420- ##
									}

									$this->db->query($Update_Laba_Next);
								}


								$Update_Periode	= "UPDATE periode SET stspost='1', post_laba='1' WHERE kdcab like '%" . $Kode_Cabang . "%' AND periode='" . $Periode_Cari . "'";
								$this->db->query($Update_Periode);
								$this->db->trans_complete();

								if ($this->db->trans_status() !== TRUE) {
									$this->db->trans_rollback();
									$rows_Return	= array(
										'pesan'			=> 'Posting process failed. Please try again later ...',
										'status'		=> 3
									);
								} else {
									$this->db->trans_commit();
									$rows_Return	= array(
										'pesan'		=> 'Posting process success. Thanks ...',
										'status'	=> 1

									);
								}
							}
						} else {
							$this->db->trans_complete();
							$this->db->trans_rollback();

							$rows_Return	= array(
								'status'		=> 3,
								'pesan'			=> 'No records was found......'
							);
						}
					} else {
						$this->db->trans_complete();
						$this->db->trans_rollback();
						$Message_Error	= "<h4 class='alert-heading'>Unbalance Journal !</h4>
										<hr>
										<p>";

						$Ambil_Unbalance	= "SELECT
													nomor,tipe,
													SUM(IF(debet > 0, debet, 0)) AS total_debet,
													SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
												FROM
													jurnal
												WHERE
													tanggal LIKE '" . $Periode_Proses . "%'
												AND (

													IF (debet > 0, debet, 0) > 0
													OR
													IF (kredit > 0, kredit, 0) > 0
												)
												GROUP BY nomor,tipe";
//												AND nomor LIKE '" . $Nocab . "%'

						$rows_Balance		= $this->db->query($Ambil_Unbalance)->result();
						if ($rows_Balance) {
							foreach ($rows_Balance as $key => $vals) {
								if ($vals->total_debet !== $vals->total_kredit) {
									$Message_Error	.= "Journal No : <b>" . $vals->nomor . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Type : <b>" . $vals->tipe . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Debet : <b>" . number_format($vals->total_debet) . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kredit : <b>" . number_format($vals->total_kredit) . "</b><br>";
								}
							}
						}
						$Message_Error	.= "</p><hr>";
						$rows_Return	= array(
							'status'		=> 2,
							'pesan'			=> $Message_Error
						);
					}
				}
			} else {
				$rows_Return	= array(
					'status'		=> 3,
					'pesan'			=> 'Period not found....'
				);
			}
		} else {
			$rows_Return	= array(
				'status'		=> 3,
				'pesan'			=> 'No record was found to process......'
			);
		}
		echo json_encode($rows_Return);
	}


	function proses_unposting()
	{
		$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$Kode_Cabang	= $this->input->post('kode_cabang');
		
		$det_Cabang		= $this->db->get_where('pastibisa_tb_cabang', array('kdcab' => $Kode_Cabang))->result();
		$Nocab			= $det_Cabang[0]->nocab;
		$Subcab			= $det_Cabang[0]->subcab;

		$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O'  ORDER BY periode ASC LIMIT 1";
		$get_Periode	= $this->db->query($Query_Periode)->result();
		$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
		$id_periode     = $get_Periode[0]->id;
		$Tahun_Pros		= $Pecah_Periode[1];
		$Month_Pros		= intval($Pecah_Periode[0]);
		$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		/*
		$Periode_Pilih	= $this->input->post('periode');		
		$Pecah_Tgl		= explode(" ",$Periode_Pilih);		
		$Tahun_Pros		= $Pecah_Tgl[1];
		$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
		
		*/

		$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));
		$this->db->trans_start();

		## PROSES UNPOSTING ##
		$Qry_UnPost	= "UPDATE COA SET debet=0,kredit=0,nilai_valas_debet=0,nilai_valas_kredit=0 WHERE kdcab='" . $Nocab . '-' . $Subcab . "' AND bln='" . $Month_Pros . "' AND thn='" . $Tahun_Pros . "'";
		$this->db->query($Qry_UnPost);
		
		
		## UPDATE PERIODE ##
		$Qry_postlaba	= "UPDATE periode SET post_laba=0 WHERE id='$id_periode'";
		$this->db->query($Qry_postlaba);

		$this->db->trans_complete();

		if ($this->db->trans_status() !== TRUE) {
			$this->db->trans_rollback();
			$rows_Return	= array(
				'pesan'			=> 'Unposting process failed. Please try again later ...',
				'status'		=> 3
			);
		} else {
			$this->db->trans_commit();
			$rows_Return	= array(
				'pesan'		=> 'Unposting process success. Thanks ...',
				'status'	=> 1

			);
		}
		echo json_encode($rows_Return);
	}

	function detail_unbalance()
	{
		$rows_Bulan		= array(1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		$Kode_Cabang	= $this->input->post('cabang');


		$det_Cabang		= $this->db->get_where('pastibisa_tb_cabang', array('kdcab' => $Kode_Cabang))->result();
		$Nocab			= $det_Cabang[0]->nocab;
		$Subcab			= $det_Cabang[0]->subcab;

		$Query_Periode	= "SELECT * FROM periode WHERE kdcab like '%" . $Kode_Cabang . "%' AND stsaktif='O' ORDER BY periode ASC LIMIT 1";
		$get_Periode	= $this->db->query($Query_Periode)->result();
		$Pecah_Periode	= explode('-', $get_Periode[0]->periode);
		$Tahun_Pros		= $Pecah_Periode[1];
		$Month_Pros		= intval($Pecah_Periode[0]);
		$Periode_Pilih	= date('F Y', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		/*
		$Periode_Pilih	= $this->input->post('periode');		
		$Pecah_Tgl		= explode(" ",$Periode_Pilih);		
		$Tahun_Pros		= $Pecah_Tgl[1];
		$Month_Pros		= array_search($Pecah_Tgl[0],$rows_Bulan);
		
		*/

		$Periode_Proses	= date('Y-m', mktime(0, 0, 0, $Month_Pros, 1, $Tahun_Pros));

		$Qry_Cek_Balance	= "SELECT
									nomor,tipe,
									SUM(IF(debet > 0, debet, 0)) AS total_debet,
									SUM(IF(kredit > 0, kredit, 0)) AS total_kredit
								FROM
									jurnal
								WHERE
									tanggal LIKE '" . $Periode_Proses . "%'
								AND (

									IF (debet > 0, debet, 0) > 0
									OR
									IF (kredit > 0, kredit, 0) > 0
								)
								GROUP BY nomor,tipe";
//								AND nomor LIKE '" . $Nocab . "%'

		$det_Balance		= $this->db->query($Qry_Cek_Balance)->result();

		$rows_data		= array(
			'title'			=> 'List Unbalance Jurnal',
			'cab_pilih'		=> $det_Cabang[0]->cabang,
			'periode_pilih'	=> $Periode_Pilih,
			'rows_header'	=> $det_Balance
		);

		$this->load->view($this->folder . '/main_unbalance', $rows_data);
	}
	
	
	function periode_end()
	{
		$data['judul']			= "Periode End";
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		
		
		
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$data['bln']		= $bln_aktif;
		$data['thn']		= $thn_aktif;
		$data['proses'] = 0;

		$this->load->view("report/v_periode_end", $data);
	}
	
	
	function proses_periode_end()
	{
		$post	= $this->input->post();	
		
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();		
						
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		
		$Keterangan_delete		    = 'Periode End '.$bln_aktif.'-'.$thn_aktif;
		
		$this->db->query("DELETE FROM jurnal WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM javh WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM sentralsistem.tr_kartu_piutang WHERE keterangan='$Keterangan_delete'");
		$this->db->query("DELETE FROM sentralsistem.tr_kartu_hutang WHERE keterangan='$Keterangan_delete'");

		
		$Keterangan_Delete1		    = 'JURNAL TUTUP BULAN FAV '.$bln_aktif.'/'.$thn_aktif;
		
		$this->db->query("DELETE FROM jurnal WHERE keterangan='$Keterangan_Delete1'");
		$this->db->query("DELETE FROM javh WHERE keterangan='$Keterangan_Delete1'");
		
		
		$this->db->query("DELETE FROM periode_end_proses WHERE bulan='$bln_aktif' AND tahun='$thn_aktif'");
		
		$this->proses_posting();
			
		for($i=0;$i < count($this->input->post('matauang'));$i++){
			$datadetail = array(                
                'matauang'     => $this->input->post('matauang')[$i],
				'kurs'         => str_replace(",","",$this->input->post('kurs_periode')[$i]),
				'bulan'        => $bln_aktif,
				'tahun'        => $thn_aktif,
				'created_by'   => $this->session->userdata('pn_name'),
				'created_on'   => date('Y-m-d H:i:s')
                );
             $this->db->insert('periode_end',$datadetail);	
			 
			 $this->db->insert('periode_end_proses',$datadetail);	


			 if($bln_aktif == 10 || $bln_aktif == 11 || $bln_aktif == 12) {
				$bln = $bln_aktif; 
				
			 } else {
				$bln = substr($bln_aktif,-1); 
			 }
			 
			 			 
			 $matauang =  $this->input->post('matauang')[$i];
			 $kurs     =  str_replace(",","",$this->input->post('kurs_periode')[$i]);
			 
			 
			 if($kurs !=0){
			 
						
									 
							$carinokir = $this->db->query("SELECT * FROM coa_master WHERE mata_uang='$matauang'")->result();


							
							  
            
							  foreach ($carinokir as $cs => $val) {
									$no_perkiraan	= $val->no_perkiraan;					
									$carisaldo = $this->db->query("SELECT * FROM COA WHERE bln='$bln' AND thn='$thn_aktif' AND no_perkiraan='$no_perkiraan'")->row();
										
								
								$Saldo_Awal = $carisaldo->saldoawal;
								$Tot_Debet  = $carisaldo->debet;
								$Tot_Kredit = $carisaldo->kredit;
								$Faktor_Kali = $carisaldo->faktor;
								$Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;
								
								$Saldo_Awal_Valas = $carisaldo->saldo_valas;
								$TotVal_Debet  = $carisaldo->nilai_valas_debet;
								$TotVal_Kredit = $carisaldo->nilai_valas_kredit;
								$Saldo_Akhir_Valas		= ($Saldo_Awal_Valas + $TotVal_Debet - $TotVal_Kredit) * $Faktor_Kali;
								

								
								
							if($Saldo_Akhir_Valas !=0){
								$kurs_histori = round($Saldo_Akhir/$Saldo_Akhir_Valas,2);	
								
                                $Saldo_Akhir			= ($Saldo_Awal + $Tot_Debet - $Tot_Kredit) * $Faktor_Kali;								
								$saldo_idr_baru = $Saldo_Akhir_Valas*$kurs;								
								$selisih_kurs   = $Saldo_Akhir-$saldo_idr_baru;
								$total          = $saldo_idr_baru+$selisih_kurs;
								
								
									     
										 
										 if($bln_aktif=='01'){
												$hari =31;
											} elseif($bln_aktif=='02'){
												$hari =28;
											} elseif($bln_aktif=='03'){
												$hari =31;
											} elseif($bln_aktif=='04'){
												$hari =30;
											} elseif($bln_aktif=='05'){
												$hari =31;
											} elseif($bln_aktif=='06'){
												$hari =30;
											} elseif($bln_aktif=='07'){
												$hari =31;
											} elseif($bln_aktif=='08'){
												$hari =31;
											} elseif($bln_aktif=='09'){
												$hari =30;
											} elseif($bln_aktif=='10'){
												$hari =31;
											} elseif($bln_aktif=='11'){
												$hari =30;
											} elseif($bln_aktif=='12'){
												$hari =31;
											}
										 
										

										 $Tgl_Inv=$thn_aktif.'-'.$bln_aktif.'-'.$hari ;
										
									   	 $convertDate   = $Tgl_Inv;
										 $nojvcost		= $this->get_Nomor_Jurnal_Sales('101', $convertDate);
										
										 $Keterangan_INV		    = 'Periode End '.$bln_aktif.'-'.$thn_aktif;
										 $Keterangan_INV2	    = 'Periode End '.$bln_aktif.'-'.$thn_aktif.' '.$Saldo_Akhir_Valas.' '.'Kurs'.$kurs;
				 
										 
										 
										 $det_Jurnal				= array(); 
										
										$dp_cust ='2102-01-03';
										$ap_usd  ='2101-01-04';
										
										
										if (strpos($no_perkiraan, '2102-01-03') !== false || strpos($no_perkiraan, '2101-01-04') !== false  || strpos($no_perkiraan, '2101-01-05') !== false) {
																								
										 
										 
													if($selisih_kurs > 0){
														 
													$det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => $selisih_kurs,
														   'kredit'        => 0,
														  
							 
													 );									 
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs,
														
							 
													 );
													 
													  $this->db->insert_batch('jurnal',$det_Jurnal);
													  
													  
													  //$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
														$nojvcosthutang		    = 'SYSTEM';
																					 
														$det_Jurnalhutang				= array(); 
														
														
														$det_Jurnalhutang[]			= array(
																   'nomor'         => $nojvcosthutang,
																   'tanggal'       => $Tgl_Inv,
																   'tipe'          => 'JV',
																   'no_perkiraan'  => $no_perkiraan,
																   'keterangan'    => $Keterangan_INV,
																   'no_reff'       => $kurs_histori, 
																   'debet'         => $selisih_kurs,
																   'kredit'        => 0,
																   'id_supplier'   => '-',
																   'nama_supplier' => '-',
																

															 );
															 
														// $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnalhutang);
														
														
													  
													  
													  
																			
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs,
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
													  
													  
													 }
													 
													 
													 
													 
													 elseif($selisih_kurs < 0){
														 
																				 
													
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => $selisih_kurs*(-1),
														   'kredit'        => 0,
														  
							 
													 );
													 
													  $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs*(-1),
														   
							 
													 );
													
																					
													 
							 
													 // $this->db->where('no_reff', $id);
													 // $this->db->where('jenis_jurnal', $ket);
													 // $this->db->delete('jurnal');
											 
													 $this->db->insert_batch('jurnal',$det_Jurnal);
													 
													 
													 
													 
													 //$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
														$nojvcosthutang		    = 'SYSTEM';
																					 
														$det_Jurnalhutang				= array(); 
														
														
														$det_Jurnalhutang[]			= array(
																   'nomor'         => $nojvcosthutang,
																   'tanggal'       => $Tgl_Inv,
																   'tipe'          => 'JV',
																   'no_perkiraan'  => $no_perkiraan,
																   'keterangan'    => $Keterangan_INV,
																   'no_reff'       => $kurs_histori, 
																   'debet'         => 0,
																   'kredit'        => $selisih_kurs*(-1),
																   'id_supplier'   => '-',
																   'nama_supplier' => '-',
																

															 );
															 
														// $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnalhutang);
														
														
													  
													 
													 
													 
																			
																		
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs*(-1),
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
							 
												}
									
									
									} else {
											
											if($selisih_kurs > 0){
														 
													$det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs,
														  
							 
													 );									 
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => $selisih_kurs,
														   'kredit'        => 0,
														
							 
													 );
													 
													  $this->db->insert_batch('jurnal',$det_Jurnal);
													  
													  
													  
																			
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs,
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
													  
													  
													 }
													 
													 
													 
													 
													 elseif($selisih_kurs < 0){
														 
																				 
													
													
													 
													 $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => '7101-01-02',
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs_histori, 
														   'debet'         => 0,
														   'kredit'        => $selisih_kurs*(-1),
														  
							 
													 );
													 
													  $det_Jurnal[]			= array(
														   'nomor'         => $nojvcost,
														   'tanggal'       => $Tgl_Inv,
														   'tipe'          => 'JV',
														   'no_perkiraan'  => $no_perkiraan,
														   'keterangan'    => $Keterangan_INV,
														   'no_reff'       => $kurs, 
														   'debet'         => $selisih_kurs*(-1),
														   'kredit'        => 0,
														   
							 
													 );
													
																					
													 
							 
													 // $this->db->where('no_reff', $id);
													 // $this->db->where('jenis_jurnal', $ket);
													 // $this->db->delete('jurnal');
											 
													 $this->db->insert_batch('jurnal',$det_Jurnal);
													 
													 
													 
																			
																		
														$dataJVhead = array(
															'nomor' 	    	=> $nojvcost,
															'tgl'	         	=> $Tgl_Inv,
															'jml'	            => $selisih_kurs*(-1),
															'koreksi_no'		=> '-',
															'kdcab'				=> '101',
															'jenis'			    => 'JV',
															'keterangan' 		=> $Keterangan_INV,
															'bulan'				=> $bln_aktif,
															'tahun'				=> $thn_aktif,
															'user_id'			=> $this->session->userdata('pn_name'),
															'memo'			    => $Keterangan_INV2,
															'tgl_jvkoreksi'	    => $Tgl_Inv,
															'ho_valid'			=> ''
														);


														$this->db->insert('javh',$dataJVhead);
							 
												}
									}	
							}
								  
						}
			
              
			 $this->update_invoice($matauang,$kurs);
			
			}
			
			
		}
	
	$this->jurnal_favorable();
	
	
	
	}
	
	
	function jurnal_favorable() {
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bulan	= substr($tgl_periode_aktif, 0, 2);
				$tahun	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		
		
		
		$tanggal_akhir = date("$tahun-$bulan-t",time());
		
		if($bulan=='01'){
			$hari =31;
		} elseif($bulan=='02'){
			$hari =28;
		} elseif($bulan=='03'){
			$hari =31;
		} elseif($bulan=='04'){
			$hari =30;
		} elseif($bulan=='05'){
			$hari =31;
		} elseif($bulan=='06'){
			$hari =30;
		} elseif($bulan=='07'){
			$hari =31;
		} elseif($bulan=='08'){
			$hari =31;
		} elseif($bulan=='09'){
			$hari =30;
		} elseif($bulan=='10'){
			$hari =31;
		} elseif($bulan=='11'){
			$hari =30;
		} elseif($bulan=='12'){
			$hari =31;
		}
		
		$tgl = $tahun.'-'.$bulan.'-'.$hari;
		
		
		
		
		
		$consum1 = $this->db->query("select sum(kredit-debet) AS consumable FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-01'")->row();
		$standart_consumable = $consum1->consumable;		
		$consum2 = $this->db->query("select sum(kredit-debet) AS directlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-02'")->row();
		$standart_dl = $consum2->directlabour;
		$consum3 = $this->db->query("select sum(kredit-debet) AS indirectlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-03'")->row();
		$standart_idl = $consum3->indirectlabour;
		$consum4 = $this->db->query("select sum(kredit-debet) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan ='2107-01-04'")->row();
		$standart_foh = $consum4->foh;
		
		$consumact1 = $this->db->query("select sum(debet-kredit) AS consumable FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5201%'")->row();
		$aktual_consumable = $consumact1->consumable;
		$consumact2 = $this->db->query("select sum(debet-kredit) AS directlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5202%'")->row();
		$aktual_dl = $consumact2->directlabour;
		$consumact3 = $this->db->query("select sum(debet-kredit) AS indirectlabour FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5203%'")->row();
		$aktual_idl = $consumact3->indirectlabour;
		$consumact4 = $this->db->query("select sum(debet-kredit) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5204%'")->row();
		$aktual_foh = $consumact4->foh;
		$consumact5 = $this->db->query("select sum(debet-kredit) AS foh FROM jurnal WHERE tanggal between'$tahun-$bulan-01' AND '$tgl' AND no_perkiraan like'5209%'")->row();
		$aktual_inst = $consumact5->foh;
		 
		
		$selisih_consumable = $aktual_consumable - $standart_consumable;
		$selisih_dl  = $aktual_dl - $standart_dl;
		$selisih_idl = $aktual_idl - $standart_idl;
		$selisih_foh = ($aktual_foh) - $standart_foh;
		
		
		if($selisih_consumable > 0 ){
		$kreditconsumable  =$selisih_consumable;
		$debetconsumable =0;
		}else{
		$kreditconsumable  =0;
		$debetconsumable =$selisih_consumable*-1;
		}
		
		if($selisih_dl > 0 ){
		$kreditdl  =$selisih_dl;
		$debetdl =0;
		}else{
		$kreditdl  =0;
		$debetdl =$selisih_dl*-1;
		}
		
		if($selisih_idl > 0 ){
		$kreditidl  =$selisih_idl;
		$debetidl =0;
		}else{
		$kreditidl  =0;
		$debetidl =$selisih_idl*-1;
		}
		
		if($selisih_foh > 0 ){
		$kreditfoh  =$selisih_foh;
		$debetfoh =0;
		}else{
		$kreditfoh  =0;
		$debetfoh =$selisih_foh*-1;
		}
		
		$nilaitotaljurnal  = $aktual_consumable + $debetconsumable + $aktual_dl + $debetdl + $aktual_idl + $debetidl + $aktual_foh + $debetfoh;
		
		
		// print($aktual_consumable);
		// echo"<br>";
		// print($aktual_dl);
		// echo"<br>";
		// print($aktual_idl);
		// echo"<br>";
		// print($aktual_foh);
		// echo"<br>";
		
		// exit;
		
		
		$sampleDate = date('Y-m-d');
		$convertDate = date("Y-m-d", strtotime($sampleDate));
		
		
		$Nomor_JV				= $this->get_Nomor_Jurnal_Sales('101', $convertDate);
		$Keterangan_INV1		    = 'JURNAL TUTUP BULAN FAV '.$bulan.'/'.$tahun;
		$nomordoc               =  $bulan.$tahun;
		
		
		//$tahun.'-'.$bulan.'-'.$akhir;
		#JURNAL TUTUP BULAN

		
		//CONSUMABLE        
							
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-01', //CONSUMABLE CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_consumable,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5205-01-01',	//CONSOMABLE PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_consumable
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-03',	//FAVORABLE / UNFAVORABLE CONSOMABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditconsumable,
					'kredit'        => $debetconsumable  
					//'jenis_jurnal'  => 'invoicing'
				);
				
		//DL			
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-02', //DIRECT LABOUR CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_dl,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5206-01-01', //	DIRECT LABOUR PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_dl
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-01', //	FAVORABLE / UNFAVORABLE DIRECT LABOUR
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditdl,
					'kredit'        => $debetdl   
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
		//IDL			
				
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-03', // INDIRECT LABOUR CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_idl,
					'kredit'        => 0
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5207-01-01', //	INDIRECT LABOUR PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_idl
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-02', //	FAVORABLE / UNFAVORABLE INDIRECT LABOUR
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditidl,
					'kredit'        => $debetidl   
					//'jenis_jurnal'  => 'invoicing'
				);
				
				
				
			//FOH			
				
				
				
			
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '2107-01-04', //FACTORY OVERHEAD CONTROL
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $standart_foh,
					'kredit'        => 0 
					//'jenis_jurnal'  => 'invoicing'
				);
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5208-01-01', //	FOH PC PAYABLE
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => 0,
					'kredit'        => $aktual_foh
					//'jenis_jurnal'  => 'invoicing'
				);
				
				$det_Jurnaltes1[]  = array(
					'nomor'         => $Nomor_JV,
					'tanggal'       => $tgl,
					'tipe'          => 'JV',
					'no_perkiraan'  => '5105-01-04', //	FAVORABLE / UNFAVORABLE FACTORY OVERHEAD
					'keterangan'    => $Keterangan_INV1,
					'no_reff'       => $nomordoc,
					'debet'         => $kreditfoh,
					'kredit'        => $debetfoh   
					//'jenis_jurnal'  => 'invoicing'
				);
				
			

		
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tgl,
			'jml'	            => $nilaitotaljurnal,
			'koreksi_no'		=> '-',
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan' 		=> $Keterangan_INV1,
			'bulan'				=> $bulan,
			'tahun'				=> $tahun,
			'user_id'			=> '01',
			'memo'			    => $nomordoc,
			'tgl_jvkoreksi'	    => $tgl,
			'ho_valid'			=> ''
		);
		// print_r($datajurnal1);
		// exit;
			$this->db->insert('javh',$dataJVhead);
			$this->db->insert_batch('jurnal',$det_Jurnaltes1);

			// $datapiutang = array(
				// 'tipe'       	 => 'JV',
				// 'nomor'       	 => $Nomor_JV,
				// 'tanggal'        => $tgl,
				//'no_perkiraan'    => $coa_penjualan,
				// 'no_perkiraan'  => '1102-01-01',
				// 'keterangan'    => $Keterangan_INV1,
				// 'no_reff'       => $nomordoc,
				// 'debet'         => $Jml_Ttl,
				// 'kredit'        =>  0,
				// 'id_supplier'   => $Id_klien,
				// 'nama_supplier' => $Nama_klien,
			// );
			// $this->db->insert('tutup_bulan',$datapiutang);
			
		// $this->proses_unposting();
		// $this->proses_posting();
		$data['judul']			= "Periode End";
	    $data['bln']		= $bulan;
		$data['thn']		= $tahun;
		$data['proses'] = 1;
		$this->load->view('report/v_periode_end_proses', $data); 
	}
	
	
	function get_Nomor_Jurnal_Sales($Cabang='',$Tgl_Inv=''){
		//$db2 = $this->load->database('accounting', TRUE);
		$nocab			= 'A';
		$bulan_Proses	= date('Y',strtotime($Tgl_Inv));
		$Urut			= 1;
		$Query_Cab		= "SELECT subcab,nomorJC FROM pastibisa_tb_cabang WHERE nocab='".$Cabang."'";
		$Pros_Cab		= $this->db->query($Query_Cab);
		$det_Cab		= $Pros_Cab->result_array();
		if($det_Cab){
			$nocab		= $det_Cab[0]['subcab'];
			$Urut		= intval($det_Cab[0]['nomorJC']) + 1;
		}
		$Format			= $Cabang.'-'.$nocab.'JV'.date('y',strtotime($Tgl_Inv));
		$Nomor_JS		= $Format.str_pad($Urut, 5, "0", STR_PAD_LEFT);
		$Query_Cab ="UPDATE pastibisa_tb_cabang SET nomorJC=(nomorJC + 1),lastupdate='".date("Y-m-d")."' WHERE nocab='".$Cabang."'";
		$this->db->query($Query_Cab);
		return $Nomor_JS;
	}
	
	
	function update_invoice($matauang,$kurs) {
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bulan	= substr($tgl_periode_aktif, 0, 2);
				$tahun	= substr($tgl_periode_aktif, 3, 4);
			}
		}	

		if($bulan=='01'){
			$hari =31;
		} elseif($bulan=='02'){
			$hari =28;
		} elseif($bulan=='03'){
			$hari =31;
		} elseif($bulan=='04'){
			$hari =30;
		} elseif($bulan=='05'){
			$hari =31;
		} elseif($bulan=='06'){
			$hari =30;
		} elseif($bulan=='07'){
			$hari =31;
		} elseif($bulan=='08'){
			$hari =31;
		} elseif($bulan=='09'){
			$hari =30;
		} elseif($bulan=='10'){
			$hari =31;
		} elseif($bulan=='11'){
			$hari =30;
		} elseif($bulan=='12'){
			$hari =31;
		}
		
		$tgl = $tahun.'-'.$bulan.'-'.$hari;
				
		
		$invoice = $this->db->query("SELECT * FROM view_invoice WHERE base_cur='$matauang'")->result();
		
		foreach($invoice AS $inv => $val){
			
			$no_inv = $val->no_invoice;
			$id_cust = $val->id_customer;
			$nm_cust = $val->nm_customer;
			
			
			$nilai_invoice = $val->sisa_invoice;
			$nilai_retensi = $val->sisa_invoice_retensi2;
			$kurs_awal     = $val->kurs_jual;
			$kurs_akhir    = $kurs;
			
			$nilai_inv_awal  = $kurs_awal*$nilai_invoice;
			$nilai_inv_akhir = $kurs_akhir*$nilai_invoice;
			$selisih_inv         = $nilai_inv_awal - $nilai_inv_akhir;
			
			$nilai_ret_awal  = $kurs_awal*$nilai_retensi;
			$nilai_ret_akhir = $kurs_akhir*$nilai_retensi;
			$selisih_ret         = $nilai_ret_awal - $nilai_ret_akhir;
			
			
			
			$Keterangan_INV		    = 'Periode End '.$bulan.'-'.$tahun;
			$nojvcost		    = 'SYSTEM';
										 
			$det_Jurnal				= array(); 
			
			if($selisih_inv > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-02',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv,
					   'id_supplier'        => $id_cust,
					   'nama_supplier'        => $nm_cust,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			elseif($selisih_inv < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-02',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => $selisih_inv*(-1),
					   'kredit'        => 0,
					   'id_supplier'   => $id_cust,
					   'nama_supplier' => $nm_cust,
					
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			
			
			if($selisih_ret > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-04',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv,
					   'id_supplier'        => $id_cust,
					   'nama_supplier'        => $nm_cust,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			elseif($selisih_ret < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '1102-01-04',
					   'keterangan'    => $Keterangan_INV,
					   'no_reff'       => $no_inv, 
					   'debet'         => $selisih_inv*(-1),
					   'kredit'        => 0,
					   'id_supplier'   => $id_cust,
					   'nama_supplier' => $nm_cust,
					
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_piutang',$det_Jurnal);
			
			}
			
			
		}
		
		
		$hutang = $this->db->query("SELECT * FROM view_hutang WHERE mata_uang='$matauang'")->result();
		
		foreach($hutang AS $htg => $val1){
			
			$no_po = $val1->no_po;
			$id_supp = $val1->id_supplier;
			$nm_supp = $val1->nm_supplier;
			
			
			$nilai_invoice2 = $val1->sisa_hutang_kurs;
			$kurs_awal2     = $val1->kurs_terima;
			$kurs_akhir2    = $kurs;
			
			$nilai_inv_awal2  = $kurs_awal2*$nilai_invoice2;
			$nilai_inv_akhir2 = $kurs_akhir2*$nilai_invoice2;
			$selisih_inv2         = $nilai_inv_awal2 - $nilai_inv_akhir2;
			
						
			
			
			$Keterangan_INV2		    = 'Periode End '.$bulan.'-'.$tahun;
			$nojvcost2		    = 'SYSTEM';
										 
			$det_Jurnal				= array(); 
			
			if($selisih_inv2 < 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost2,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '2101-01-04',
					   'keterangan'    => $Keterangan_INV2,
					   'no_reff'       => $no_po, 
					   'debet'         => 0,
					   'kredit'        => $selisih_inv2*(-1),
					   'id_supplier'        => $id_supp,
					   'nama_supplier'        => $nm_supp,
					

				 );
				 
		    $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnal);
			
			}
			
			elseif($selisih_inv2 > 0){
			$det_Jurnal[]			= array(
					   'nomor'         => $nojvcost2,
					   'tanggal'       => $tgl,
					   'tipe'          => 'JV',
					   'no_perkiraan'  => '2101-01-04',
					   'keterangan'    => $Keterangan_INV2,
					   'no_reff'       => $no_po, 
					   'debet'         => $selisih_inv2,
					   'kredit'        => 0,
					   'id_supplier'   => $id_supp,
					   'nama_supplier' => $nm_supp,
					

				 );
		    $this->db->insert_batch('sentralsistem.tr_kartu_hutang',$det_Jurnal);
			
			}
			
			
			
			
			
			
		}
		
	}
}
