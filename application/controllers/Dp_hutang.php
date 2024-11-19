<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$combo_coa=array();
class Dp_hutang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		$this->load->model('master_model');
		$this->load->model('menu_model');
		$this->load->model('custom_model');
		$this->load->model('order_model');
		$this->load->helper('menu');
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->model('Report_model');
		$this->load->model('Dphutang_model');
		$this->combo_coa=array(''=>'ALL','DP IDR'=>'1111-01-01','DP USD'=>'1111-01-02');
	}



	function index()
	{
		$data['judul']			= "Laporan DP Supllier";
		$data['datklien']     = $this->Report_model->pilih_vendor();
		$this->load->view("report/v_kartu_dp", $data);
	}

	function tampilkan_dp_hutang(){

		$awal					= date_format(new DateTime($this->input->post('tgl_awal')), "Y-m-d");
		$akhir					= date_format(new DateTime($this->input->post('tgl_akhir')), "Y-m-d");
		$supplier    			= $this->input->post('id_klien');
		// print_r($supplier);
		// exit;
		$tipe					= $this->input->post('tipe');
		$data['combo_coa']		= $this->combo_coa;

		$data['judul']			= "DP Supplier";

		if ($this->input->post('tampilkan') == "View Excel") {
			redirect('kartu_hutang/excel_kartuhutang/' . $awal . '/' . $akhir . '/' . $supplier);
		} else {

            $data['datklien']           = $this->Report_model->pilih_vendor();
			if($supplier =='0'){
			$data['coa_sa']				= $this->Dphutang_model->GetDataAll($awal,$supplier,$tipe);
			}else{
			$data['coa_sa']				= $this->Dphutang_model->GetData($awal,$supplier,$tipe);	
			}
			$data['datawal']            = $awal;
			$data['datakhir']           = $akhir;
			$data['datvendor']          = $supplier;
			$data['tipe']				= $tipe;

			$this->load->view("report/v_dp_hutang", $data);
		}
	}

	function excel_kartuhutang()
	{
		$data['judul']			= "DP Supplier";

		$awal               = $this->uri->segment(3);
		$akhir              = $this->uri->segment(4);
		$supplier			= $this->uri->segment(5);



		$data['datawal']               = $awal;
		$data['datakhir']              = $akhir;
		$data['datvendor']             = $supplier;

	    $data['coa_sa']				= $this->Dphutang_model->GetData($awal,$supplier);
		$data['detail_jurnal']		= $this->Dphutang_model->get_detail_dp_hutang($awal,$akhir,$supplier);

		$this->load->view("report/v_dp_hutang_excel", $data);

	}


	function rekap_dp_hutang(){

		$awal					= date_format(new DateTime($this->input->post('tgl_awal')), "Y-m-d");
		$akhir					= date_format(new DateTime($this->input->post('tgl_akhir')), "Y-m-d");
		$tipe					= $this->input->post('tipe');

		$data['judul']			= "DP Supplier";

		if ($this->input->post('tampilkan') == "View Excel") {

		redirect('kartu_hutang/excel_rekapkartuhutang/' . $awal . '/' . $akhir);


		} else {
          	$data['vendor']					= $this->Dphutang_model->GetVendor();
			$data['datawal']				= $awal;
			$data['datakhir']				= $akhir;
			$data['tipe']					= $tipe;
			$data['combo_coa']				= $this->combo_coa;

			$this->load->view("report/v_rekap_dp_hutang", $data);
		}
	}

	function excel_rekapkartuhutang()
	{
		$data['judul']			= "DP Supplier";

		$awal               = $this->uri->segment(3);
		$akhir              = $this->uri->segment(4);

		$data['datawal']               = $awal;
		$data['datakhir']              = $akhir;

	    $data['vendor']				   = $this->Dphutang_model->GetVendor();

		$this->load->view("report/v_rekap_dp_hutang_excel", $data);

	}


	function tampilkan_umur_kartuhutang(){

		$awal					= $this->input->post('tgl_awal');
		$akhir					= $this->input->post('tgl_akhir');
		$supplier    			= $this->input->post('id_klien');
        $tipe                   = $this->input->post('tipe');
		// print_r($this->input->post());
		// exit;

		$data['judul']			= "Umur Kartu Hutang";

		if ($this->input->post('tampilkan') == "View Excel") {

		redirect('kartu_hutang/excel_umurkartuhutang/'. $supplier.'/'.$akhir);


		} else {

            $data['datklien']           = $this->Report_model->pilih_vendor();
			$data['coa_sa']				= $this->Dphutang_model->GetDataUmur($awal,$akhir,$supplier,$tipe);
			$data['datawal']            = $awal;
			$data['datakhir']           = $akhir;
			$data['datvendor']         	= $supplier;
			$data['combo_coa']		   	= $this->combo_coa;
			$this->load->view("report/v_umur_dp_hutang", $data);
		}
	}

	function excel_umurkartuhutang()
	{
		$data['judul']			= "Umur Kartu Hutang";

		$supplier               = $this->uri->segment(3);
		$akhir              = $this->uri->segment(4);
		$awal    			= $this->uri->segment(5);


		// print_r($awal);
		// print_r($akhir);
		// print_r($supplier);
		// exit;

		$data['datvendor']             = $supplier;

	    $data['coa_sa']				= $this->Dphutang_model->GetDataUmur($awal,$akhir,$supplier);


		$this->load->view("report/v_umur_dp_hutang_excel", $data);

	}

	function rekap_umur_dp_hutang(){

		$akhir					= $this->input->post('tgl_akhir');
		$data['judul']			= "Umur Kartu Hutang";

		if ($this->input->post('tampilkan') == "View Excel") {

		redirect('kartu_hutang/excel_rekapumurkartuhutang/'.$akhir);


		} else {

		    $Vendor  = $this->Dphutang_model->GetVendor();
		    $data['coa_sa']				   = $this->Dphutang_model->GetDataBukti();
          	$data['vendor']				   = $this->Dphutang_model->GetVendor();
			$data['datakhir']              = $akhir;

			$this->load->view("report/v_rekap_umur_dp_hutang", $data);
		}
	}

	function excel_rekapumurkartuhutang()
	{
		$akhir              = $this->uri->segment(3);

		$data['judul']			= "Rekap Umur Kartu Hutang";
		$data['datakhir']       = $akhir;
		$this->load->view("report/v_rekap_umur_dp_hutang_excel", $data);

	}


}