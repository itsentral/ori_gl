<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
		$this->load->model('Jurnal_model');
		$this->load->model('Order_model');
		//$this->load->model('custom_model');
		//$this->load->model('master_model');
		//$this->load->model('komisi_model');
		$this->load->helper('menu');
		$this->load->model('Invoice_model');
		$this->load->model('Report_model');
		
		$this->ori_operasional 		= $this->load->database("ori_ops",TRUE);
		$this->arr_bulan			= array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		
		$this->cutoff_stock			= '2024-09-01';
	}

	function query_jurnal()
	{
		$data['judul']		= "Query Jurnal";
		$data['list_data'] = '';//$this->Jurnal_model->list_jurnal();

		$this->load->view("report/v_list_query_jurnal", $data);
	}

	public function get_data_json_query_jurnal(){
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_query_jurnal(
			$requestData['filter_text'],
			$requestData['filter_by'],
			$requestData['tanggal'],
			$requestData['tanggal2'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];
		$data	= array();
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
			$nestedData 	= array();
			$nestedData[]	= "<div align='left'>".($row['tipe'])."</div>";
			$nestedData[]	= "<div align='left'>".date('d-M-Y', strtotime($row['tanggal']))."</div>";
			$nestedData[]	= "<div align='left'>".($row['nomor'])."</div>";
			$nestedData[]	= "<div align='left'>".($row['no_perkiraan'])." - ".($row['nama'])."</div>";
			$nestedData[]	= "<div align='left'>".($row['keterangan'])."</div>";
			$nestedData[]	= "<div align='left'>".($row['no_reff'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['debet'])."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['kredit'])."</div>";
			$data[] = $nestedData;
		}
		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);
		echo json_encode($json_data);
	}

	public function query_data_query_jurnal($filter_text, $filter_by,  $tanggal,  $tanggal2, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where_text = "";
		if($filter_text <> ''){
			$where_text.= " AND a.".$filter_by." like '%".$filter_text."%' ";
		}
		if($tanggal <> ''){
			$where_text.= " AND a.tanggal>='".cdates($tanggal)."' ";
		}
		if($tanggal2 <> ''){
			$where_text.= " AND a.tanggal<='".cdates($tanggal2)."' ";
		}
		$sql = "
				SELECT a.*, b.nama 
				FROM jurnal a
				INNER JOIN coa_master b ON a.no_perkiraan=b.no_perkiraan 
				WHERE 1=1 ".$where_text."
				AND (
				a.nomor LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_perkiraan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'tipe',
			1 => 'tanggal',
			2 => 'nomor',
			3 => 'nama',
			4 => 'keterangan',
			5 => 'no_reff',
			6 => 'debet',
			7 => 'kredit'
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);//echo $sql;
		return $data;
	}
	function filter_query_jurnal()
	{
		$data['judul']		= "Query Jurnal";
		$filter_by 		= $this->input->post('filter_by');
		$filter_text 	= $this->input->post('filter_text');
		$tanggal_		= $this->input->post('tanggal');
		$tanggal		= date_format(new DateTime($tanggal_), "Y-m-d");
		$tanggal2_		= $this->input->post('tanggal2');
		$tanggal2		= date_format(new DateTime($tanggal2_), "Y-m-d");

		if ($this->input->post('cari') == "EXPORT TO EXCEL") {
			redirect('report/excel_qjurnal/' . $tanggal . '/' . $tanggal2 . '/' . $filter_by . '/' . $filter_text);
		} else {

			if ($filter_by == "no_reff") {
				$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_noreff($tanggal, $tanggal2, $filter_text);
			} elseif ($filter_by == "no_jurnal") {
				$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_nojur($tanggal, $tanggal2, $filter_text);
			} elseif ($filter_by == "no_perkiraan") {
				$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_nokir($tanggal, $tanggal2, $filter_text);
			} elseif ($filter_by == "keterangan") {
				$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_ket($tanggal, $tanggal2, $filter_text);
			} elseif ($filter_by == "tipe") {
				$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_tipe($tanggal, $tanggal2, $filter_text);
			}
		}

		$data['tanggal']		= $tanggal_;
		$data['tanggal2']		= $tanggal2_;
		$data['filter_text']	= $filter_text;
		$data['filter_by']		= $filter_by;

		$this->load->view("report/v_list_qjurnal_filter", $data);
	}

	function detail_qjurnal()
	{
		$tipe_jurnal		= $this->uri->segment(3);
		$nomor_jurnal		= $this->uri->segment(4);
		$data['judul']		= "Detail Jurnal " . $tipe_jurnal . " " . $nomor_jurnal;
		$data['list_data'] 	= $this->Jurnal_model->get_detail_jurnal($tipe_jurnal, $nomor_jurnal);

		$this->load->view("report/v_detail_qjurnal", $data);
	}
	function excel_qjurnal()
	{
		$tanggal = $this->uri->segment(3);

		if (empty($tanggal)) {
			$tanggal = date('Y-m-d');
		}

		$tanggal2 = $this->uri->segment(4);

		if (empty($tanggal2)) {
			$tanggal2 = date('Y-m-d');
		}

		$filter_by = $this->uri->segment(5);
		$filter_text = $this->uri->segment(6);

		if ($filter_by == "no_reff") {
			$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_noreff($tanggal, $tanggal2, $filter_text);
		} elseif ($filter_by == "no_jurnal") {
			$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_nojur($tanggal, $tanggal2, $filter_text);
		} elseif ($filter_by == "no_perkiraan") {
			$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_nokir($tanggal, $tanggal2, $filter_text);
		} elseif ($filter_by == "keterangan") {
			$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_ket($tanggal, $tanggal2, $filter_text);
		} elseif ($filter_by == "tipe") {
			$data['filter_query']	= $this->Jurnal_model->filter_query_jurnal_tipe($tanggal, $tanggal2, $filter_text);
		}

		$data['tanggal']		= $tanggal;
		$data['tanggal2']		= $tanggal2;
		$data['filter_text']	= $filter_text;
		$data['filter_by']		= $filter_by;

		$data['filter_tgl']	= $this->Jurnal_model->filter_tgl_jurnal($tanggal, $tanggal2);

		$this->load->view("report/view_excel_qjurnal", $data);
	}

	function konsolidasi_trial_balance()
	{

		$data['judul'] = "Konsolidasi Trial Balance";
		$data['judul2'] = "Inisialisasi Trial Balance";
		//$data['list_data'] = $this->Order_model->list_request_stock();
		$data['pesan_on'] 	= 0;
		$data['proses'] 	= 0;
		$this->load->view('report/v_konsolidasi_trial_balance', $data);
	}

	function tampilkan_konsolidasi_trial_balance()
	{

		$data['judul'] = "Konsolidasi Trial Balance";
		$data['judul2'] = "Inisialisasi Trial Balance";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_trial_balance');
			$var_thn					= $this->input->post('tahun_trial_balance');
			$level						= $this->input->post('level');
			redirect('report/excel_konsolidasi_trial_balance/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {

			$var_bulan					= $this->input->post('bulan_trial_balance');
			$var_tahun					= $this->input->post('tahun_trial_balance');

			$data['data_bulan']		= $this->Report_model->get_bulan_coa();
			$data['data_tahun']		= $this->Report_model->get_tahun_coa();
			$level						= $this->input->post('level');

			$data['data_HartaLancar1101']	= $this->Report_model->get_HartaLancar_kons1101($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1102']	= $this->Report_model->get_HartaLancar_kons1102($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1104']	= $this->Report_model->get_HartaLancar_kons1104($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1105']	= $this->Report_model->get_HartaLancar_kons1105($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1106']	= $this->Report_model->get_HartaLancar_kons1106($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1107']	= $this->Report_model->get_HartaLancar_kons1107($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1108']	= $this->Report_model->get_HartaLancar_kons1108($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1110']	= $this->Report_model->get_HartaLancar_kons1110($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1111']	= $this->Report_model->get_HartaLancar_kons1111($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1301']	= $this->Report_model->get_AktivaTetap_kons1301($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1302']	= $this->Report_model->get_AktivaTetap_kons1302($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1303']	= $this->Report_model->get_AktivaTetap_kons1303($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1304']	= $this->Report_model->get_AktivaTetap_kons1304($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1305']	= $this->Report_model->get_AktivaTetap_kons1305($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1306']	= $this->Report_model->get_AktivaTetap_kons1306($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1307']	= $this->Report_model->get_AktivaTetap_kons1307($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1309']	= $this->Report_model->get_AktivaTetap_kons1309($var_bulan, $var_tahun, $level);
			$data['data_AktivaLain']		= $this->Report_model->get_AktivaLain_kons($var_bulan, $var_tahun, $level);
			$data['data_nokir_totalaktiva']		= $this->Report_model->kons_nokir_totalaktiva($var_bulan, $var_tahun, $level);
			$data['data_nokir_totalpassiva']	= $this->Report_model->kons_nokir_totalpassiva($var_bulan, $var_tahun, $level);
			$data['data_Hutang2101']		= $this->Report_model->get_Hutang_kons2101($var_bulan, $var_tahun, $level);
			$data['data_Hutang2102']		= $this->Report_model->get_Hutang_kons2102($var_bulan, $var_tahun, $level);
			$data['data_Hutang2107']		= $this->Report_model->get_Hutang_kons2107($var_bulan, $var_tahun, $level);
			$data['data_Hutang2108']		= $this->Report_model->get_Hutang_kons2108($var_bulan, $var_tahun, $level);
			$data['data_Modal']				= $this->Report_model->get_Modal_kons($var_bulan, $var_tahun, $level);
			$data['data_Laba3901']			= $this->Report_model->get_Laba_kons3901($var_bulan, $var_tahun, $level);
			$data['data_Laba3902']			= $this->Report_model->get_Laba_kons3902($var_bulan, $var_tahun, $level);
			$data['data_Laba3903']			= $this->Report_model->get_Laba_kons3903($var_bulan, $var_tahun, $level);
			$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6811_konsolidasi']	= $this->Report_model->get_nokir_6811tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6821_konsolidasi']	= $this->Report_model->get_nokir_6821tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6831_konsolidasi']	= $this->Report_model->get_nokir_6831tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91tb_konsolidasi($var_bulan, $var_tahun, $level);
			$data['data_nokir_4']				= $this->Report_model->kons_nokir_4($var_bulan, $var_tahun, $level);
			$data['data_nokir_5']				= $this->Report_model->kons_nokir_5($var_bulan, $var_tahun, $level);
			$data['data_nokir_6']				= $this->Report_model->kons_nokir_6($var_bulan, $var_tahun, $level);
			$data['data_nokir_71']				= $this->Report_model->kons_nokir_71($var_bulan, $var_tahun, $level);
			$data['data_nokir_72']				= $this->Report_model->kons_nokir_72($var_bulan, $var_tahun, $level);
			$data['data_nokir_9']				= $this->Report_model->kons_nokir_9($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;


			$this->load->view("report/v_list_konsolidasi_trial_balance", $data);
			//redirect('report/print_labarugi');
		}
	}

	function excel_konsolidasi_trial_balance()
	{
		$data['judul'] = "Konsolidasi Trial Balance";
		$data['judul2'] = "Inisialisasi Trial Balance";

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);
		$level = $this->uri->segment(5);

		$data['data_HartaLancar1101']	= $this->Report_model->get_HartaLancar_kons1101($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1102']	= $this->Report_model->get_HartaLancar_kons1102($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1104']	= $this->Report_model->get_HartaLancar_kons1104($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1105']	= $this->Report_model->get_HartaLancar_kons1105($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1106']	= $this->Report_model->get_HartaLancar_kons1106($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1107']	= $this->Report_model->get_HartaLancar_kons1107($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1108']	= $this->Report_model->get_HartaLancar_kons1108($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1110']	= $this->Report_model->get_HartaLancar_kons1110($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1111']	= $this->Report_model->get_HartaLancar_kons1111($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1301']	= $this->Report_model->get_AktivaTetap_kons1301($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1302']	= $this->Report_model->get_AktivaTetap_kons1302($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1303']	= $this->Report_model->get_AktivaTetap_kons1303($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1304']	= $this->Report_model->get_AktivaTetap_kons1304($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1305']	= $this->Report_model->get_AktivaTetap_kons1305($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1306']	= $this->Report_model->get_AktivaTetap_kons1306($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1307']	= $this->Report_model->get_AktivaTetap_kons1307($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1309']	= $this->Report_model->get_AktivaTetap_kons1309($var_bulan, $var_tahun, $level);
		$data['data_AktivaLain']		= $this->Report_model->get_AktivaLain_kons($var_bulan, $var_tahun, $level);
		$data['data_nokir_totalaktiva']		= $this->Report_model->kons_nokir_totalaktiva($var_bulan, $var_tahun, $level);
		$data['data_nokir_totalpassiva']	= $this->Report_model->kons_nokir_totalpassiva($var_bulan, $var_tahun, $level);
		$data['data_Hutang2101']		= $this->Report_model->get_Hutang_kons2101($var_bulan, $var_tahun, $level);
		$data['data_Hutang2102']		= $this->Report_model->get_Hutang_kons2102($var_bulan, $var_tahun, $level);
		$data['data_Hutang2107']		= $this->Report_model->get_Hutang_kons2107($var_bulan, $var_tahun, $level);
		$data['data_Hutang2108']		= $this->Report_model->get_Hutang_kons2108($var_bulan, $var_tahun, $level);
		$data['data_Modal']				= $this->Report_model->get_Modal_kons($var_bulan, $var_tahun, $level);
		$data['data_Laba3901']			= $this->Report_model->get_Laba_kons3901($var_bulan, $var_tahun, $level);
		$data['data_Laba3902']			= $this->Report_model->get_Laba_kons3902($var_bulan, $var_tahun, $level);
		$data['data_Laba3903']			= $this->Report_model->get_Laba_kons3903($var_bulan, $var_tahun, $level);
		$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_6811_konsolidasi']	= $this->Report_model->get_nokir_6811_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_6821_konsolidasi']	= $this->Report_model->get_nokir_6821_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_6831_konsolidasi']	= $this->Report_model->get_nokir_6831_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72_konsolidasi($var_bulan, $var_tahun, $level);
		$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91_konsolidasi($var_bulan, $var_tahun, $level);
		$data['data_nokir_4']				= $this->Report_model->kons_nokir_4($var_bulan, $var_tahun, $level);
		$data['data_nokir_5']				= $this->Report_model->kons_nokir_5($var_bulan, $var_tahun, $level);
		$data['data_nokir_6']				= $this->Report_model->kons_nokir_6($var_bulan, $var_tahun, $level);
		$data['data_nokir_71']				= $this->Report_model->kons_nokir_71($var_bulan, $var_tahun, $level);
		$data['data_nokir_72']				= $this->Report_model->kons_nokir_72($var_bulan, $var_tahun, $level);
		$data['data_nokir_9']				= $this->Report_model->kons_nokir_9($var_bulan, $var_tahun, $level);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_konsolidasi_trial_balance_excel", $data);
		//redirect('report/print_labarugi');		
	}

	function konsolidasi_neraca()
	{
		$data['judul']			= "Laporan Konsolidasi Neraca";
		$this->load->view("report/v_konsolidasi_neraca", $data);
	}

	function tampilkan_konsolidasi_neraca()
	{
		$data['judul']			= "Laporan Konsolidasi Neraca";

		if ($this->input->post('tampilkan') == "Print Preview") {
			$var_bulan					= $this->input->post('bulan_neraca');
			$var_tahun					= $this->input->post('tahun_neraca');
			$level						= $this->input->post('level');
			redirect('report/print_konsolidasi_neraca/' . $var_bulan . '/' . $var_tahun . '/' . $level);
		} else {
			$data['judul']			= "Laporan Konsolidasi Neraca";

			$var_bulan					= $this->input->post('bulan_neraca');
			$var_tahun					= $this->input->post('tahun_neraca');
			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;
			$level						= $this->input->post('level');

			$data['data_HartaLancar1101']	= $this->Report_model->get_HartaLancar_kons1101($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1102']	= $this->Report_model->get_HartaLancar_kons1102($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1104']	= $this->Report_model->get_HartaLancar_kons1104($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1105']	= $this->Report_model->get_HartaLancar_kons1105($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1106']	= $this->Report_model->get_HartaLancar_kons1106($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1107']	= $this->Report_model->get_HartaLancar_kons1107($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1108']	= $this->Report_model->get_HartaLancar_kons1108($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1110']	= $this->Report_model->get_HartaLancar_kons1110($var_bulan, $var_tahun, $level);
			$data['data_HartaLancar1111']	= $this->Report_model->get_HartaLancar_kons1111($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1301']	= $this->Report_model->get_AktivaTetap_kons1301($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1302']	= $this->Report_model->get_AktivaTetap_kons1302($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1303']	= $this->Report_model->get_AktivaTetap_kons1303($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1304']	= $this->Report_model->get_AktivaTetap_kons1304($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1305']	= $this->Report_model->get_AktivaTetap_kons1305($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1306']	= $this->Report_model->get_AktivaTetap_kons1306($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1307']	= $this->Report_model->get_AktivaTetap_kons1307($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap1309']	= $this->Report_model->get_AktivaTetap_kons1309($var_bulan, $var_tahun, $level);
			$data['data_AktivaLain']		= $this->Report_model->get_AktivaLain_kons($var_bulan, $var_tahun, $level);
			$data['data_Hutang2101']		= $this->Report_model->get_Hutang_kons2101($var_bulan, $var_tahun, $level);
			$data['data_Hutang2102']		= $this->Report_model->get_Hutang_kons2102($var_bulan, $var_tahun, $level);
			$data['data_Hutang2107']		= $this->Report_model->get_Hutang_kons2107($var_bulan, $var_tahun, $level);
			$data['data_Hutang2108']		= $this->Report_model->get_Hutang_kons2108($var_bulan, $var_tahun, $level);
			$data['data_Modal']				= $this->Report_model->get_Modal_kons($var_bulan, $var_tahun, $level);
			$data['data_Laba3901']			= $this->Report_model->get_Laba_kons3901($var_bulan, $var_tahun, $level);
			$data['data_Laba3902']			= $this->Report_model->get_Laba_kons3902($var_bulan, $var_tahun, $level);
			$data['data_Laba3903']			= $this->Report_model->get_Laba_kons3903($var_bulan, $var_tahun, $level);

			$this->load->view("report/v_list_konsolidasi_neraca", $data);
		}
	}

	function print_konsolidasi_neraca()
	{
		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);
		$level = $this->uri->segment(5);
		$data['data_HartaLancar1101']	= $this->Report_model->get_HartaLancar_kons1101($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1102']	= $this->Report_model->get_HartaLancar_kons1102($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1104']	= $this->Report_model->get_HartaLancar_kons1104($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1105']	= $this->Report_model->get_HartaLancar_kons1105($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1106']	= $this->Report_model->get_HartaLancar_kons1106($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1107']	= $this->Report_model->get_HartaLancar_kons1107($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1108']	= $this->Report_model->get_HartaLancar_kons1108($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1110']	= $this->Report_model->get_HartaLancar_kons1110($var_bulan, $var_tahun, $level);
		$data['data_HartaLancar1111']	= $this->Report_model->get_HartaLancar_kons1111($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1301']	= $this->Report_model->get_AktivaTetap_kons1301($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1302']	= $this->Report_model->get_AktivaTetap_kons1302($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1303']	= $this->Report_model->get_AktivaTetap_kons1303($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1304']	= $this->Report_model->get_AktivaTetap_kons1304($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1305']	= $this->Report_model->get_AktivaTetap_kons1305($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1306']	= $this->Report_model->get_AktivaTetap_kons1306($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1307']	= $this->Report_model->get_AktivaTetap_kons1307($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap1309']	= $this->Report_model->get_AktivaTetap_kons1309($var_bulan, $var_tahun, $level);
		$data['data_AktivaLain']		= $this->Report_model->get_AktivaLain_kons($var_bulan, $var_tahun, $level);
		$data['data_Hutang2101']		= $this->Report_model->get_Hutang_kons2101($var_bulan, $var_tahun, $level);
		$data['data_Hutang2102']		= $this->Report_model->get_Hutang_kons2102($var_bulan, $var_tahun, $level);
		$data['data_Hutang2107']		= $this->Report_model->get_Hutang_kons2107($var_bulan, $var_tahun, $level);
		$data['data_Hutang2108']		= $this->Report_model->get_Hutang_kons2108($var_bulan, $var_tahun, $level);
		$data['data_Modal']				= $this->Report_model->get_Modal_kons($var_bulan, $var_tahun, $level);
		$data['data_Laba3901']			= $this->Report_model->get_Laba_kons3901($var_bulan, $var_tahun, $level);
		$data['data_Laba3902']			= $this->Report_model->get_Laba_kons3902($var_bulan, $var_tahun, $level);
		$data['data_Laba3903']			= $this->Report_model->get_Laba_kons3903($var_bulan, $var_tahun, $level);
		// $var_bulan					= $this->input->post('bulan_neraca');
		// $var_tahun					= $this->input->post('tahun_neraca');
		$data['data_bulan_post']	= $var_bulan;
		$data['data_tahun_post']	= $var_tahun;

		$html			= $this->load->view('report/v_print_konsolidasi_neraca', $data, true);
		$pdfFilePath	= "Laporan_Konsolidasi_Neraca_" . $var_bulan . "_" . $var_tahun . ".pdf";
		$this->load->library('m_pdf');
		$pdf			= $this->m_pdf->load();
		//$mpdf = new mPDF('c', 'A4-L'); 
		$pdf->AddPage(
			'L',
			'',
			'',
			'',
			10, // margin_left
			10, // margin right
			20, // margin top
			10, // margin bottom
			10, // margin header
			10
		); // margin footer
		$pdf->WriteHTML($html);
		$pdf->Output($pdfFilePath, "I");
		//redirect('report/tampilkan_labarugi');
	}

	function konsolidasi_labarugi()
	{
		$data['judul']		= "Konsolidasi Laba/Rugi";
		$this->load->view("report/v_konsolidasi_labarugi", $data);
	}

	function tampilkan_konsolidasi_labarugi()
	{
		$data['judul']			= "Konsolidasi Laba/Rugi";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_konsolidasi_labarugi');
			$var_thn					= $this->input->post('tahun_konsolidasi_labarugi');
			$level						= $this->input->post('level');
			redirect('report/excel_konsolidasi_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {

			$var_bulan					= $this->input->post('bulan_konsolidasi_labarugi');
			$var_tahun					= $this->input->post('tahun_konsolidasi_labarugi');
			$level						= $this->input->post('level');
			if ($level == "3") {
				$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_6811a_konsolidasi']	= $this->Report_model->get_nokir_6811a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_6821a_konsolidasi']	= $this->Report_model->get_nokir_6821a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_6831a_konsolidasi']	= $this->Report_model->get_nokir_6831a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91_konsolidasi($var_bulan, $var_tahun, $level);

				$data['data_bulan_post']			= $var_bulan;
				$data['data_tahun_post']			= $var_tahun;

				$this->load->view("report/v_list2_konsolidasi_labarugi", $data);
			} else {
				// $data['nokir_68all_konsolidasi']	= $this->Report_model->get_nokir_68all_konsolidasi($var_bulan,$var_tahun,$level);
				$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_68_konsolidasi']	= $this->Report_model->get_nokir_68a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72a_konsolidasi($var_bulan, $var_tahun, $level);
				$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91_konsolidasi($var_bulan, $var_tahun, $level);
				$data['data_bulan_post']			= $var_bulan;
				$data['data_tahun_post']			= $var_tahun;

				$this->load->view("report/v_list2_konsolidasi_labarugi5", $data);
			}

			//redirect('report/print_labarugi');
		}
	}

	function excel_konsolidasi_labarugi()
	{
		$data['judul']			= "Laporan Konsolidasi Laba/Rugi";

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$var_bulan	= $this->uri->segment(3);
		$var_tahun	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		if ($level == "3") {
			$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6811a_konsolidasi']	= $this->Report_model->get_nokir_6811a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6821a_konsolidasi']	= $this->Report_model->get_nokir_6821a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_6831a_konsolidasi']	= $this->Report_model->get_nokir_6831a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91_konsolidasi($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$this->load->view("report/v_konsolidasi_labarugi_excel", $data);
		} else {
			$data['nokir_41_konsolidasi']	= $this->Report_model->get_nokir_41a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_51_konsolidasi']	= $this->Report_model->get_nokir_51a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_61_konsolidasi']	= $this->Report_model->get_nokir_61a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_68_konsolidasi']	= $this->Report_model->get_nokir_68a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_71_konsolidasi']	= $this->Report_model->get_nokir_71a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_72_konsolidasi']	= $this->Report_model->get_nokir_72a_konsolidasi($var_bulan, $var_tahun, $level);
			$data['nokir_91_konsolidasi']	= $this->Report_model->get_nokir_91_konsolidasi($var_bulan, $var_tahun, $level);
			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$this->load->view("report/v_konsolidasi_labarugi_excel5", $data);
		}

		//redirect('report/print_labarugi');		
	}

	function jurnal()
	{
		$data['judul']		= "List Jurnal";
		$data['list_data'] = $this->Jurnal_model->list_jurnal(1000);
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}

		$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);

		$this->load->view("report/v_list_jurnal", $data);
	}

	function filter_tgl_jurnal()
	{
		$data['list_data'] = $this->Jurnal_model->list_jurnal(1000);

		$data['judul']		= "List Jurnal";
		if ($this->input->post('view') == "View List Excel") {
			$date_ = $this->input->post('tanggal');
			$date = str_replace(" - ", "_", $date_);
			$date2_ = $this->input->post('tanggal2');
			$date2 = str_replace(" - ", "_", $date2_);
			redirect('report/view_excel/' . $date . '/' . $date2);
		} else {
			$tanggal_	= $this->input->post('tanggal');
			$tanggal	= date_format(new DateTime($tanggal_), "Y-m-d");
			$tanggal2_	= $this->input->post('tanggal2');
			$tanggal2	= date_format(new DateTime($tanggal2_), "Y-m-d");

			if (empty($tanggal)) {
				$tanggal = date('Y-m-d');
			}

			if (empty($tanggal2)) {
				$tanggal2 = date('Y-m-d');
			}

			$data['tanggal']		= $tanggal;
			$data['tanggal2']		= $tanggal2;

			$data['filter_tgl']	= $this->Jurnal_model->filter_tgl_jurnal($tanggal, $tanggal2);
			$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
			if ($cek_periode_aktif > 0) {
				foreach ($cek_periode_aktif as $row_periode_aktif) {
					$tgl_periode_aktif	= $row_periode_aktif->periode;
					$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
					$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
				}
			}
			$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);

			$this->load->view("report/v_list_jurnal_filter", $data);
		}
	}

	function view_excel()
	{

		$tanggal_ = $this->uri->segment(3);
		$tanggal	= date_format(new DateTime($tanggal_), "Y-m-d");
		if (empty($tanggal)) {
			$tanggal = date('Y-m-d');
		}

		$tanggal2_ = $this->uri->segment(4);
		$tanggal2	= date_format(new DateTime($tanggal2_), "Y-m-d");

		if (empty($tanggal2)) {
			$tanggal2 = date('Y-m-d');
		}

		$data['tanggal']			= $tanggal;
		$data['tanggal2']			= $tanggal2;

		$data['filter_tgl']	= $this->Jurnal_model->filter_tgl_jurnal($tanggal, $tanggal2);

		$this->load->view("report/view_excel", $data);
	}

	function detail_jurnal()
	{
		$tipe_jurnal		= $this->uri->segment(3);
		$nomor_jurnal		= $this->uri->segment(4);
		$data['judul']		= "Detail Jurnal " . $tipe_jurnal . " " . $nomor_jurnal;
		$data['list_data'] 	= $this->Jurnal_model->get_detail_jurnal($tipe_jurnal, $nomor_jurnal);
		// $cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		// if ($cek_periode_aktif > 0) {
		// 	foreach ($cek_periode_aktif as $row_periode_aktif) {
		// 		$tgl_periode_aktif	= $row_periode_aktif->periode;
		// 		$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
		// 		$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
		// 	}
		// }
		//$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);
		$this->load->view("report/v_detail_jurnal", $data);
	}

	function filter_noperkiraan()
	{
		$data['judul']		= "List Jurnal";

		$data['filter_nok']	= $this->Jurnal_model->get_nokir_filter();
		$data['list_data'] = $this->Jurnal_model->list_jurnal();
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);

		$this->load->view("report/v_list_jurnal_filter_nokir", $data);
	}

	function trial_balance()
	{

		$data['judul'] = "Inisialisasi Trial Balance";
		$data['judul2'] = "Trial Balance " . $this->session->userdata('kode_cabang');
		//$data['list_data'] = $this->Order_model->list_request_stock();		
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['bln_aktif']	= substr($tgl_periode_aktif, 0, 2);
				$data['thn_aktif']	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$data['pesan_on'] 	= 0;
		$data['proses'] 	= 0;
		$this->load->view('report/v_trial_balance', $data);
	}

	function tampilkan_trial_balance()
	{

		$data['judul']	= "Trial Balance Report";
		$data['judul2'] = "Trial Balance " . $this->session->userdata('kode_cabang');

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_trial_balance');
			$var_thn					= $this->input->post('tahun_trial_balance');
			redirect('report/excel_trial_balance/' . $var_bln . '/' . $var_thn);
		} elseif ($this->input->post('tampilkan') == "View Pdf") {
			$var_bln					= $this->input->post('bulan_trial_balance');
			$var_thn					= $this->input->post('tahun_trial_balance');
			redirect('report/print_trial_balance/' . $var_bln . '/' . $var_thn);
		} else {

			$var_bulan					= $this->input->post('bulan_trial_balance');
			$var_tahun					= $this->input->post('tahun_trial_balance');

			$data['data_bulan']		= $this->Report_model->get_bulan_coa();
			$data['data_tahun']		= $this->Report_model->get_tahun_coa();

			$data['data_nokir_hartalancar11']	= $this->Report_model->get_nokir_hartalancar11($var_bulan, $var_tahun);
			$data['data_nokir_aktivatetap13']	= $this->Report_model->get_nokir_aktivatetap13($var_bulan, $var_tahun);
			$data['data_nokir_aktivalain19']	= $this->Report_model->get_nokir_aktivalain19($var_bulan, $var_tahun);
			$data['data_nokir_totalaktiva']		= $this->Report_model->get_nokir_totalaktiva($var_bulan, $var_tahun);
			$data['data_nokir_totalpassiva']	= $this->Report_model->get_nokir_totalpassiva($var_bulan, $var_tahun);
			$data['data_nokir_hutang21']		= $this->Report_model->get_nokir_hutang21($var_bulan, $var_tahun);
			$data['data_nokir_modal31']			= $this->Report_model->get_nokir_modal31($var_bulan, $var_tahun);
			$data['data_nokir_laba39']			= $this->Report_model->get_nokir_laba39($var_bulan, $var_tahun);
			$data['data_nokir_pendapatan41']	= $this->Report_model->get_nokir_pendapatan41($var_bulan, $var_tahun);
			$data['data_nokir_hpp51']			= $this->Report_model->get_nokir_hpp51($var_bulan, $var_tahun);
			$data['data_nokir_biayapenjualan61'] = $this->Report_model->get_nokir_biayapenjualan61($var_bulan, $var_tahun);
			$data['data_nokir_biayakantor68']	= $this->Report_model->get_nokir_biayakantor68($var_bulan, $var_tahun);
			$data['data_nokir_taksiranpajak91']	= $this->Report_model->get_nokir_taksiranpajak91($var_bulan, $var_tahun);
			$data['data_nokir_4']				= $this->Report_model->get_nokir_4($var_bulan, $var_tahun);
			$data['data_nokir_5']				= $this->Report_model->get_nokir_5($var_bulan, $var_tahun);
			$data['data_nokir_6']				= $this->Report_model->get_nokir_6($var_bulan, $var_tahun);
			$data['data_nokir_71']				= $this->Report_model->get_nokir_71($var_bulan, $var_tahun);
			$data['data_nokir_72']				= $this->Report_model->get_nokir_72($var_bulan, $var_tahun);
			$data['data_nokir_9']				= $this->Report_model->get_nokir_9($var_bulan, $var_tahun);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;


			$this->load->view("report/v_list_trial_balance", $data);
			//redirect('report/print_labarugi');
		}
	}

	function excel_trial_balance()
	{
		$data['judul']			= "Trial Balance";

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);

		$data['data_nokir_hartalancar11']	= $this->Report_model->get_nokir_hartalancar11($var_bulan, $var_tahun);
		$data['data_nokir_aktivatetap13']	= $this->Report_model->get_nokir_aktivatetap13($var_bulan, $var_tahun);
		$data['data_nokir_aktivalain19']	= $this->Report_model->get_nokir_aktivalain19($var_bulan, $var_tahun);
		$data['data_nokir_totalaktiva']		= $this->Report_model->get_nokir_totalaktiva($var_bulan, $var_tahun);
		$data['data_nokir_totalpassiva']	= $this->Report_model->get_nokir_totalpassiva($var_bulan, $var_tahun);
		$data['data_nokir_hutang21']		= $this->Report_model->get_nokir_hutang21($var_bulan, $var_tahun);
		// $data['data_nokir_modal32']			= $this->Report_model->get_nokir_modal32($var_bulan, $var_tahun);
		$data['data_nokir_modal31']			= $this->Report_model->get_nokir_modal31($var_bulan, $var_tahun);
		$data['data_nokir_laba39']			= $this->Report_model->get_nokir_laba39($var_bulan, $var_tahun);
		$data['data_nokir_pendapatan41']	= $this->Report_model->get_nokir_pendapatan41($var_bulan, $var_tahun);
		$data['data_nokir_hpp51']			= $this->Report_model->get_nokir_hpp51($var_bulan, $var_tahun);
		$data['data_nokir_biayapenjualan61'] = $this->Report_model->get_nokir_biayapenjualan61($var_bulan, $var_tahun);
		$data['data_nokir_biayakantor68']	= $this->Report_model->get_nokir_biayakantor68($var_bulan, $var_tahun);
		$data['data_nokir_taksiranpajak91']	= $this->Report_model->get_nokir_taksiranpajak91($var_bulan, $var_tahun);
		$data['data_nokir_4']				= $this->Report_model->get_nokir_4($var_bulan, $var_tahun);
		$data['data_nokir_5']				= $this->Report_model->get_nokir_5($var_bulan, $var_tahun);
		$data['data_nokir_6']				= $this->Report_model->get_nokir_6($var_bulan, $var_tahun);
		$data['data_nokir_71']				= $this->Report_model->get_nokir_71($var_bulan, $var_tahun);
		$data['data_nokir_72']				= $this->Report_model->get_nokir_72($var_bulan, $var_tahun);
		$data['data_nokir_9']				= $this->Report_model->get_nokir_9($var_bulan, $var_tahun);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_trial_balance_excel", $data);
		//redirect('report/print_labarugi');		
	}

	function print_trial_balance()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$data['data_nokir_hartalancar11']	= $this->Report_model->get_nokir_hartalancar11($var_bulan, $var_tahun);
		$data['data_nokir_aktivatetap13']	= $this->Report_model->get_nokir_aktivatetap13($var_bulan, $var_tahun);
		$data['data_nokir_aktivalain19']	= $this->Report_model->get_nokir_aktivalain19($var_bulan, $var_tahun);
		$data['data_nokir_totalaktiva']		= $this->Report_model->get_nokir_totalaktiva($var_bulan, $var_tahun);
		$data['data_nokir_totalpassiva']	= $this->Report_model->get_nokir_totalpassiva($var_bulan, $var_tahun);
		$data['data_nokir_hutang21']		= $this->Report_model->get_nokir_hutang21($var_bulan, $var_tahun);
		$data['data_nokir_modal31']			= $this->Report_model->get_nokir_modal31($var_bulan, $var_tahun);
		$data['data_nokir_laba39']			= $this->Report_model->get_nokir_laba39($var_bulan, $var_tahun);
		$data['data_nokir_pendapatan41']	= $this->Report_model->get_nokir_pendapatan41($var_bulan, $var_tahun);
		$data['data_nokir_hpp51']			= $this->Report_model->get_nokir_hpp51($var_bulan, $var_tahun);
		$data['data_nokir_biayapenjualan61'] = $this->Report_model->get_nokir_biayapenjualan61($var_bulan, $var_tahun);
		$data['data_nokir_biayakantor68']	= $this->Report_model->get_nokir_biayakantor68($var_bulan, $var_tahun);
		$data['data_nokir_taksiranpajak91']	= $this->Report_model->get_nokir_taksiranpajak91($var_bulan, $var_tahun);
		$data['data_nokir_4']				= $this->Report_model->get_nokir_4($var_bulan, $var_tahun);
		$data['data_nokir_5']				= $this->Report_model->get_nokir_5($var_bulan, $var_tahun);
		$data['data_nokir_6']				= $this->Report_model->get_nokir_6($var_bulan, $var_tahun);
		$data['data_nokir_71']				= $this->Report_model->get_nokir_71($var_bulan, $var_tahun);
		$data['data_nokir_72']				= $this->Report_model->get_nokir_72($var_bulan, $var_tahun);
		$data['data_nokir_9']				= $this->Report_model->get_nokir_9($var_bulan, $var_tahun);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$html			= $this->load->view('report/v_print_trial_balance', $data, true);
		$pdfFilePath	= "Laporan_Trial_Balance_" . $var_bulan . "_" . $var_tahun . ".pdf";
		$this->load->library('m_pdf');
		$pdf			= $this->m_pdf->load();
		//$mpdf = new mPDF('c', 'A4-L'); 
		$pdf->AddPage(
			'L',
			'',
			'',
			'',
			30, // margin_left
			30, // margin right
			20, // margin top
			10, // margin bottom
			10, // margin header
			10
		); // margin footer
		$pdf->WriteHTML($html);
		$pdf->Output($pdfFilePath, "I");
		//redirect('report/tampilkan_trial_balance');
	}

	function marketing()
	{
		$data	= [
			'judul'			=> "Report Marketing",
			'list_marketing' => $this->db->query("SELECT * FROM dk_user WHERE sts_marketing='Y'")->result()
		];
		$this->load->view('report/marketing', $data);
	}

	function gedung()
	{
		$tahun	= empty($this->input->post('tahun')) ? date('Y') : $this->input->post('tahun');

		$data	= [
			'judul'			=> "Report Gedung",
			'list_gedung'	=> $this->Order_model->get_list_tempat_lokasi($tahun)
		];
		$this->load->view('report/gedung', $data);
	}

	function refresh_periode()
	{
		$data['judul'] = "Refresh Periode";
		//$data['list_data'] = $this->Order_model->list_request_stock();
		$data['proses'] = 0;
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['$bln_aktif']			= substr($tgl_periode_aktif, 0, 2);
				$data['$thn_aktif']			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$this->load->view('report/v_refresh_periode', $data);
	}

	function proses_refresh_periode()
	{
		$data['judul'] = "Proses Refresh Periode";
		$bln_periode = $this->input->post('bulan_periode');
		$thn_periode = $this->input->post('tahun_periode');
		//$blnthn_periode = $bln_periode."-".$thn_periode;
		
		 $kode_cabang = $this->session->userdata('singkat_cbg');

		if ($bln_periode > 9) {
			$blnthn_periode = $bln_periode . "-" . $thn_periode;
		} else {
			$blnthn_periode = "0" . $bln_periode . "-" . $thn_periode;
		}

		$cek_periode = $this->Report_model->get_periode($blnthn_periode);
		//$cek_periode = $this->db->query("SELECT * FROM periode WHERE periode='$blnthn_periode'")->result();
	    if ($cek_periode > 0) {
			$this->db->query("UPDATE periode set stsaktif='C', kdcab='$kode_cabang' ");
			$this->db->query("UPDATE periode set stsaktif='O', kdcab='$kode_cabang' WHERE periode='$blnthn_periode'");
		} else {
			$this->db->query("UPDATE periode set stsaktif='C' ");
			$this->db->query("INSERT INTO periode (periode,stsaktif,kdcab) VALUES ('$blnthn_periode','O','$kode_cabang')");
			//periode,noJS,noJP,noJO,noJC,noJM,noJD,open_date,closing_date,user,stsaktif,stspost,stslock,kdcab,post_laba,company
			//'$blnthn_periode',0,0,0,0,0,0,'0000-00-00','0000-00-00','','O',0,0,'SUP',1,'AAMT'
		}
		//$data['list_data'] = $this->Order_model->list_request_stock();
		//echo $blnthn_periode;
		//exit;

		$data['proses'] = 2;
		$this->load->view('report/v_refresh_periode', $data);
	}

	function ledger()
	{
		$data['judul']			= "Laporan Ledger";
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		
		
		
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);

		$this->load->view("report/v_ledger", $data);
	}

	function tampilkan_ledger()
	{
		$data['judul']			= "Laporan Ledger";
		
		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_ledger');
			$var_thn					= $this->input->post('tahun_ledger');
			$var_filter_nokir			= $this->input->post('filter_nokir');
			$var_filter_nokir2			= $this->input->post('filter_nokir2');
			$filter_nokir = substr($var_filter_nokir, 0, 10);
			$filter_nokir2 = substr($var_filter_nokir2, 0, 10);
			redirect('report/excel_ledger/' . $var_bln . '/' . $var_thn . '/' . $filter_nokir . '/' . $filter_nokir2);
			//redirect('report/ledger');

		} elseif ($this->input->post('tampilkan') == "View Detail") {
			$var_bln					= $this->input->post('bulan_ledger');
			$var_thn					= $this->input->post('tahun_ledger');
			$var_filter_nokir			= $this->input->post('filter_nokir');
			$var_filter_nokir2			= $this->input->post('filter_nokir2');
			$filter_nokir = substr($var_filter_nokir, 0, 10);
			$filter_nokir2 = substr($var_filter_nokir2, 0, 10);
			redirect('report/detail_ledger/' . $var_bln . '/' . $var_thn . '/' . $filter_nokir . '/' . $filter_nokir2);
			//redirect('report/ledger');

		}
		
		else {
			$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
			//print_r( $cek_periode_aktif);
			if ($cek_periode_aktif > 0) {
				foreach ($cek_periode_aktif as $row_periode_aktif) {
					$tgl_periode_aktif	= $row_periode_aktif->periode;
					$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
					$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
				}
			}
			$data['data_perkiraan']		= $this->Jurnal_model->get_noperkiraan($bln_aktif, $thn_aktif);

			$var_bulan					= $this->input->post('bulan_ledger');
			$var_tahun					= $this->input->post('tahun_ledger');
			$data['bln_ledger']			= $this->input->post('bulan_ledger');
			$data['thn_ledger']			= $this->input->post('tahun_ledger');
			$var_filter_nokir			= $this->input->post('filter_nokir');
			$var_filter_nokir2			= $this->input->post('filter_nokir2');
			$data['filter_nokir']			= $this->input->post('filter_nokir');
			$data['filter_nokir2']			= $this->input->post('filter_nokir2');
			$filter_nokir = substr($var_filter_nokir, 0, 10);
			$filter_nokir2 = substr($var_filter_nokir2, 0, 10);

			$awal = 1;
			$akhir = date("t",strtotime($var_tahun . "-" . $var_bulan ."-01"));
			$enol = 0;
			if ($var_bulan > 9) {
				$var_tgl_awal = $var_tahun . "-" . $var_bulan . "-0" . $awal;
				$data['var_tgl_awal'] = $var_tahun . "-" . $var_bulan . "-0" . $awal;
				$var_tgl_akhir = $var_tahun . "-" . $var_bulan . "-" . $akhir;
				$data['var_tgl_akhir'] = $var_tahun . "-" . $var_bulan . "-" . $akhir;
			} else {
				$var_tgl_awal = $var_tahun . "-" . $enol . $var_bulan . "-0" . $awal;
				$data['var_tgl_awal'] = $var_tahun . "-" . $enol . $var_bulan . "-0" . $awal;
				$var_tgl_akhir = $var_tahun . "-" . $enol . $var_bulan . "-" . $akhir;
				$data['var_tgl_akhir'] = $var_tahun . "-" . $enol . $var_bulan . "-" . $akhir;
			}

			$data['coa_sa']				= $this->Report_model->get_coa_sa($filter_nokir, $filter_nokir2, $var_bulan, $var_tahun);
			// $data['detail_jurnal']		= $this->Report_model->get_detail_jurnal($filter_nokir,$filter_nokir2,$var_tgl_awal,$var_tgl_akhir);
			
			$data['cutoff_stock']		= $this->cutoff_stock;
			$this->load->view("report/v_list_ledger", $data);
		}
	}

	function excel_ledger()
	{
		$data['judul']			= "Laporan Laba Rugi";

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);

		$data['bln_ledger']			= $var_bulan;
		$data['thn_ledger']			= $var_tahun;
		$filter_nokir			= $this->uri->segment(5);
		$filter_nokir2			= $this->uri->segment(6);

		$awal = 1;
		$akhir = date("t",strtotime($var_tahun . "-" . $var_bulan ."-01"));
		$enol = 0;
		if ($var_bulan > 9) {
			$var_tgl_awal = $var_tahun . "-" . $var_bulan . "-0" . $awal;
			$var_tgl_akhir = $var_tahun . "-" . $var_bulan . "-" . $akhir;
		} else {
			$var_tgl_awal = $var_tahun . "-" . $enol . $var_bulan . "-0" . $awal;
			$var_tgl_akhir = $var_tahun . "-" . $enol . $var_bulan . "-" . $akhir;
		}

		$data['coa_sa']				= $this->Report_model->get_coa_sa($filter_nokir, $filter_nokir2, $var_bulan, $var_tahun);
//		$data['detail_jurnal']		= $this->Report_model->get_detail_jurnal($filter_nokir, $filter_nokir2, $var_tgl_awal, $var_tgl_akhir);

		$this->load->view("report/v_ledger_excel", $data);
		//redirect('report/print_labarugi');		
	}
	
	function detail_ledger()
	{
		$data['judul']			= "Laporan Laba Rugi";

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);

		$data['bln_ledger']			= $var_bulan;
		$data['thn_ledger']			= $var_tahun;
		$filter_nokir			= $this->uri->segment(5);
		$filter_nokir2			= $this->uri->segment(6);
		$data['coa_filter1']			= $filter_nokir;
		$data['coa_filter2']			= $filter_nokir2;

		$awal = 1;
		$akhir = date("t",strtotime($var_tahun . "-" . $var_bulan ."-01"));
		$enol = 0;
		if ($var_bulan > 9) {
			$var_tgl_awal = $var_tahun . "-" . $var_bulan . "-0" . $awal;
			$var_tgl_akhir = $var_tahun . "-" . $var_bulan . "-" . $akhir;
		} else {
			$var_tgl_awal = $var_tahun . "-" . $enol . $var_bulan . "-0" . $awal;
			$var_tgl_akhir = $var_tahun . "-" . $enol . $var_bulan . "-" . $akhir;
		}

		$data['coa_sa']				= $this->Report_model->get_coa_sa($filter_nokir, $filter_nokir2, $var_bulan, $var_tahun);
//		$data['detail_jurnal']		= $this->Report_model->get_detail_jurnal($filter_nokir, $filter_nokir2, $var_tgl_awal, $var_tgl_akhir);
		//echo"<pre>";print_r($data);exit;
		$this->load->view("report/v_ledger_detail", $data);
		//redirect('report/print_labarugi');		
	}

	function tutup_tahun()
	{
		$data['judul'] = "Proses Tutup Tahun";
		$data['proses'] = "0";
		$this->load->view('report/v_tutup_tahun', $data);
	}
	function proses_tutup_tahun()
	{
		$data['judul'] = "Proses Tutup Tahun";

		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}

		if ($bln_aktif == 1) {
			$nolkan_nokir_4_9	= $this->db->query("UPDATE COA SET saldoawal=0 WHERE (no_perkiraan LIKE '4%' OR no_perkiraan LIKE '5%' OR no_perkiraan LIKE '6%' OR no_perkiraan LIKE '7%' OR no_perkiraan LIKE '8%' OR no_perkiraan LIKE '9%') AND bln=1 AND thn='$thn_aktif'");

			$data['proses'] = "1";
			$this->load->view('report/v_tutup_tahun', $data);
		} else {
			$data['proses'] = "2";
			$this->load->view('report/v_tutup_tahun', $data);
		}
	}

	function tutup_bulan()
	{
		$data['judul'] = "Proses Tutup Bulan";
		$data['proses'] = "0";
		$this->load->view('report/v_tutup_bulan', $data);
	}

	function proses_tutup_bulan()
	{
		$data['judul'] = "Proses Tutup Bulan";

		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$bln_aktif			= substr($tgl_periode_aktif, 0, 2);
				$thn_aktif			= substr($tgl_periode_aktif, 3, 4);
			}
		}

		$awal = 1;
		$akhir = 31;
		$enol = 0;
		if ($bln_aktif > 9) {
			$var_tgl_awal = $thn_aktif . "-" . $bln_aktif . "-0" . $awal;
			$var_tgl_akhir = $thn_aktif . "-" . $bln_aktif . "-" . $akhir;
		} else {
			$var_tgl_awal = $thn_aktif . "-" . $enol . $bln_aktif . "-0" . $awal;
			$var_tgl_akhir = $thn_aktif . "-" . $enol . $bln_aktif . "-" . $akhir;
		}
		$kode_cabang = $this->session->userdata('kode_cabang');
		if ($bln_aktif <= 11) {
			$nu_bln_aktif = $bln_aktif + 1;
			$nu_thn_aktif = $thn_aktif;
		} else {
			$nu_bln_aktif = 1;
			$nu_thn_aktif = $thn_aktif + 1;
		}

		$this->db->query("delete from COA WHERE bln='$nu_bln_aktif' AND thn='$nu_thn_aktif' and kdcab like '%$kode_cabang%'");


		$data_coa					= $this->Report_model->get_coa($bln_aktif, $thn_aktif);
		if ($data_coa > 0) {
			$query 	= "SELECT * from pastibisa_tb_cabang where nocab='$kode_cabang' limit 1";
			$newkodecabang=$this->db->query($query)->row();
			$kode_cabang=$newkodecabang->nocab.'-'.$newkodecabang->subcab;
			foreach ($data_coa as $row_coa) {
				$nokir			= $row_coa->no_perkiraan;
				$nm_perkiraan	= $row_coa->nama;
				$lvl			= $row_coa->level;
				$grup			= $row_coa->grup;
				$faktor			= $row_coa->faktor;
				$saldo_awal		= $row_coa->saldoawal;
				$debet			= $row_coa->debet;
				$kredit			= $row_coa->kredit;
				$saldo_akhir	= $saldo_awal + $debet - $kredit;
				$saldo_valas	= $row_coa->saldo_valas;
				$valasdebet		= $row_coa->nilai_valas_debet;
				$valaskredit	= $row_coa->nilai_valas_kredit;
				$saldo_valas_akhir	= $saldo_valas + $valasdebet- $valaskredit;

				//$data_jurnal	= $this->Report_model->get_jurnal($nokir, $var_tgl_awal, $var_tgl_akhir);
				/*
				$data_jurnal	= $this->Report_model->get_coa($bln_aktif, $thn_aktif);
				
				if ($data_jurnal > 0) {
					foreach ($data_jurnal as $row_jurnal) {
						$jumlah_debet	= $row_jurnal->debet;
						$jumlah_kredit	= $row_jurnal->kredit;
						$saldo_akhir = $saldo_awal + $jumlah_debet - $jumlah_kredit;
					}
				}
				*/

				$data_newcoa					= $this->Report_model->get_newcoa($nu_bln_aktif, $nu_thn_aktif, $nokir); // cek coa utk bln & thn yg baru sdh ada apa belum
				if ($data_newcoa > 0) { // jika sudah ada

					$this->db->query("INSERT INTO COA (no_perkiraan,nama,kdcab,saldoawal,bln,thn,debet,kredit,tmp,tipe,level,grup,faktor,nilai_valas_debet,nilai_valas_kredit,saldo_valas) VALUES ('$nokir','$nm_perkiraan','$kode_cabang','$saldo_akhir','$nu_bln_aktif','$nu_thn_aktif',0,0,'O','A','$lvl','$grup','$faktor',0,0,'$saldo_valas_akhir')");
				} else {		// jika belum ada


					$this->db->query("INSERT INTO COA (no_perkiraan,nama,kdcab,saldoawal,bln,thn,debet,kredit,tmp,tipe,level,grup,faktor,nilai_valas_debet,nilai_valas_kredit,saldo_valas) VALUES ('$nokir','$nm_perkiraan','$kode_cabang','$saldo_akhir','$nu_bln_aktif','$nu_thn_aktif',0,0,'O','A','$lvl','$grup','$faktor',0,0,'$saldo_valas_akhir')");
					// echo "INSERT INTO COA (no_perkiraan,nama,kdcab,saldoawal,bln,thn,debet,kredit,tmp,tipe,level,grup,faktor) VALUES ('$nokir','$nm_perkiraan','$kode_cabang','$saldo_akhir','$nu_bln_aktif','$nu_thn_aktif',0,0,'O','A','$lvl','$grup','$faktor')";
				}
			}

			$enol = 0;
			if ($nu_bln_aktif > 9) {
				$nu_tgl_periode_aktif = $nu_bln_aktif . "-" . $nu_thn_aktif;
			} else {
				$nu_tgl_periode_aktif = $enol . $nu_bln_aktif . "-" . $nu_thn_aktif;
			}
			$singkat_cbg = $this->session->userdata('singkat_cbg');

			$this->db->query("UPDATE periode set stsaktif='C' WHERE periode='$tgl_periode_aktif' and kdcab='$singkat_cbg'");

			$nu_periode	= $this->Report_model->get_periode($nu_tgl_periode_aktif); // cek apakah periode yg baru sudah ada apa belum
			if ($nu_periode > 0) { // jika sudah ada

				$this->db->query("UPDATE periode set stsaktif='O',stspost='1',stslock='0' WHERE periode='$nu_tgl_periode_aktif' and kdcab='$singkat_cbg'");
			} else { // jika belum ada

				$this->db->query("INSERT INTO periode (periode,stsaktif,stspost,stslock,kdcab) VALUES ('$nu_tgl_periode_aktif','O','1','0','$singkat_cbg')");
			}
		}

		$data['proses'] = 1;
		$this->load->view('report/v_tutup_bulan', $data);
	}

	function labarugi()
	{
		$data['judul']			= "Income Statement";
		$cek_periode_aktif		= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['bln_aktif']	= substr($tgl_periode_aktif, 0, 2);
				$data['thn_aktif']	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$this->load->view("report/v_labarugi", $data);
	}

	function tampilkan_labarugi()
	{
		$data['judul']			= "Income Statement";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('report/excel_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} elseif ($this->input->post('tampilkan') == "View Pdf") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('report/print_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {

			$var_bulan					= $this->input->post('bulan_labarugi');
			$var_tahun					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			$data['level']				= $this->input->post('level');

			$data['data_bulan']		= $this->Report_model->get_bulan_coa();
			$data['data_tahun']		= $this->Report_model->get_tahun_coa();

			$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn($var_bulan, $var_tahun, $level);
			$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya52'] = $this->Report_model->get_nokir_biaya52($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Report_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Report_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Report_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Report_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Report_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Report_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Report_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Report_model->get_nokir_fee($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$this->load->view("report/v_list_labarugi", $data);
			//redirect('report/print_labarugi');
		}
	}

	function excel_labarugi()
	{
		$data['judul']			= "Income Statement";

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$var_bulan = $this->uri->segment(3);
		$var_tahun = $this->uri->segment(4);
		$level = $this->uri->segment(5);
		$data['level']				= $this->input->post('level');

		$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn($var_bulan, $var_tahun, $level);
		$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
		$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya52'] = $this->Report_model->get_nokir_biaya52($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya61']	= $this->Report_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
		$data['data_nokir_fee']		= $this->Report_model->get_nokir_fee($var_bulan, $var_tahun, $level);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_labarugi_excel", $data);
		//redirect('report/print_labarugi');		
	}

	function print_labarugi()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		$data['level']				= $level;

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn($var_bulan, $var_tahun, $level);
		$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
		$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya52'] = $this->Report_model->get_nokir_biaya52($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya53'] = $this->Report_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya54'] = $this->Report_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya55'] = $this->Report_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya56'] = $this->Report_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya57'] = $this->Report_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya58'] = $this->Report_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya61']	= $this->Report_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
		$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
		$data['data_nokir_fee']		= $this->Report_model->get_nokir_fee($var_bulan, $var_tahun, $level);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$html			= $this->load->view('report/v_print_labarugi', $data, true);
		$pdfFilePath	= "Laporan_Laba_Rugi_" . $var_bulan . "_" . $var_tahun . ".pdf";
		$this->load->library('m_pdf');
		$pdf			= $this->m_pdf->load();
		//$mpdf = new mPDF('c', 'A4-L'); 
		$pdf->AddPage(
			'P', // L = Landscape, P = Potrait
			'',
			'',
			'',
			30, // margin_left
			30, // margin right
			20, // margin top
			10, // margin bottom
			10, // margin header
			10
		); // margin footer
		$pdf->WriteHTML($html);
		$pdf->Output($pdfFilePath, "I");
		//redirect('report/tampilkan_labarugi');
	}

	function labarugi_event()
	{
		$data['judul']				= "Laporan Laba Rugi Per Event";
		$data['data_project']		= $this->Report_model->ambil_data_project();
		$this->load->view("report/v_labarugi_event", $data);
	}

	function tampilkan_labarugi_event()
	{
		$data['judul']			= "Laporan Laba Rugi Per Event";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_project					= $this->input->post('project');
			$var_project_ = str_replace("|", "_", $var_project);

			redirect('report/excel_labarugi_event/' . $var_project_);
		} else {

			$var_project					= $this->input->post('project');

			$data['data_project']		= $this->Report_model->ambil_data_project();
			$data['data_project2']		= $this->Report_model->ambil_data_project2($var_project);

			$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn_event($var_project);
			$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2_event($var_project);
			$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp_event($var_project);
			$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya_event($var_project);
			$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2_event($var_project);
			$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3_event($var_project);

			$this->load->view("report/v_list_labarugi_event", $data);
			//redirect('report/print_labarugi');
		}
	}

	function change_lr_event()
	{

		$id = $this->input->get('option');

		$data_nganten	= $this->Report_model->get_nganten($id);
		if ($data_nganten > 0) {
			foreach ($data_nganten as $row_nganten) {
				$pria		= $row_nganten->pengantin_pria;
				$wanita		= $row_nganten->pengantin_wanita;
				$nganten	= $pria . " & " . $wanita;
			}
		}

		echo "<input class='form-control' name='nganten' id='nganten' value='" . $nganten . "'readonly/>";
		echo "</td>";
	}

	function excel_labarugi_event()
	{
		$data['judul']			= "Laporan Laba Rugi Per Event";

		$var_project					= $this->input->post('project');
		$var_project_					= $this->uri->segment(3);
		$data['var_project_']			= $this->uri->segment(3);
		$var_project 					= str_replace("_", "|", $var_project_);

		$data['data_project']		= $this->Report_model->ambil_data_project();
		$data['data_project2']		= $this->Report_model->ambil_data_project2($var_project);

		$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn_event($var_project);
		$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2_event($var_project);
		$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp_event($var_project);
		$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya_event($var_project);
		$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2_event($var_project);
		$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3_event($var_project);

		$this->load->view("report/v_labarugi_event_excel", $data);
		//redirect('report/print_labarugi');		
	}

	function neraca()
	{
		$data['judul']			= "Laporan Neraca";
		$cek_periode_aktif			= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['bln_aktif']	= substr($tgl_periode_aktif, 0, 2);
				$data['thn_aktif']	= substr($tgl_periode_aktif, 3, 4);
			}
		}

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$this->load->view("report/v_neraca", $data);
	}

	function tampilkan_neraca()
	{
		$data['judul']			= "Laporan Neraca";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_neraca');
			$var_thn					= $this->input->post('tahun_neraca');
			$level						= $this->input->post('level');
			redirect('report/excel_neraca/' . $var_bln . '/' . $var_thn . '/' . $level);
		} elseif ($this->input->post('tampilkan') == "View Pdf") {
			$var_bln					= $this->input->post('bulan_neraca');
			$var_thn					= $this->input->post('tahun_neraca');
			$level						= $this->input->post('level');
			redirect('report/print_neraca/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {
			$data['judul']			= "Laporan Neraca";

			$var_bulan					= $this->input->post('bulan_neraca');
			$var_tahun					= $this->input->post('tahun_neraca');
			$level						= $this->input->post('level');
			$data['level']						= $this->input->post('level');
			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$data['data_HartaLancar']	= $this->Report_model->get_HartaLancar($var_bulan, $var_tahun, $level);
			$data['data_tdkHartaLancar'] = $this->Report_model->get_tdkHartaLancar($var_bulan, $var_tahun, $level);
			$data['data_AktivaTetap']	= $this->Report_model->get_AktivaTetap($var_bulan, $var_tahun, $level);
			$data['data_AktivaLain']	= $this->Report_model->get_AktivaLain($var_bulan, $var_tahun, $level);
			$data['data_Hutang']		= $this->Report_model->get_Hutang($var_bulan, $var_tahun, $level);
			$data['data_Hutang22']		= $this->Report_model->get_Hutang22($var_bulan, $var_tahun, $level);
			$data['data_Modal']			= $this->Report_model->get_Modal($var_bulan, $var_tahun, $level);
			$data['data_Laba']			= $this->Report_model->get_Laba($var_bulan, $var_tahun, $level);

			$this->load->view("report/v_list_neraca", $data);
		}
	}
	function print_neraca()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		$data['level']				= $level;

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$data['data_HartaLancar']	= $this->Report_model->get_HartaLancar($var_bulan, $var_tahun, $level);
		$data['data_tdkHartaLancar'] = $this->Report_model->get_tdkHartaLancar($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap']	= $this->Report_model->get_AktivaTetap($var_bulan, $var_tahun, $level);
		$data['data_AktivaLain']	= $this->Report_model->get_AktivaLain($var_bulan, $var_tahun, $level);
		$data['data_Hutang']		= $this->Report_model->get_Hutang($var_bulan, $var_tahun, $level);
		$data['data_Hutang22']		= $this->Report_model->get_Hutang22($var_bulan, $var_tahun, $level);
		$data['data_Modal']			= $this->Report_model->get_Modal($var_bulan, $var_tahun, $level);
		$data['data_Laba']			= $this->Report_model->get_Laba($var_bulan, $var_tahun, $level);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$html			= $this->load->view('report/v_print_neraca', $data, true);
		$pdfFilePath	= "Laporan_Neraca_" . $var_bulan . "_" . $var_tahun . ".pdf";
		$this->load->library('m_pdf');
		$pdf			= $this->m_pdf->load();
		//$mpdf = new mPDF('c', 'A4-L'); 
		$pdf->AddPage(
			'P', // L = Landscape, P = Potrait
			'',
			'',
			'',
			10, // margin_left
			10, // margin right
			10, // margin top
			10, // margin bottom
			10, // margin header
			10
		); // margin footer
		$pdf->WriteHTML($html);
		$pdf->Output($pdfFilePath, "I");
		//redirect('report/tampilkan_labarugi');
	}

	function excel_neraca()
	{
		$var_bulan 	= $this->uri->segment(3);
		$var_tahun 	= $this->uri->segment(4);
		$level		= $this->uri->segment(5);

		$data['level']				= $level;

		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();

		$data['data_HartaLancar']	= $this->Report_model->get_HartaLancar($var_bulan, $var_tahun, $level);
		$data['data_tdkHartaLancar'] = $this->Report_model->get_tdkHartaLancar($var_bulan, $var_tahun, $level);
		$data['data_AktivaTetap']	= $this->Report_model->get_AktivaTetap($var_bulan, $var_tahun, $level);
		$data['data_AktivaLain']	= $this->Report_model->get_AktivaLain($var_bulan, $var_tahun, $level);
		$data['data_Hutang']		= $this->Report_model->get_Hutang($var_bulan, $var_tahun, $level);
		$data['data_Hutang22']		= $this->Report_model->get_Hutang22($var_bulan, $var_tahun, $level);
		$data['data_Modal']			= $this->Report_model->get_Modal($var_bulan, $var_tahun, $level);
		$data['data_Laba']			= $this->Report_model->get_Laba($var_bulan, $var_tahun, $level);

		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_neraca_excel", $data);
	}

	// SYAM TERKAIT REPORT KARTU PIUTANG

	function ar()
	{
		$data['judul']			= "Laporan Piutang";
		$data['datklien']     = $this->Report_model->pilih_klien();
		$this->load->view("report/v_ar", $data);
	}

	function tampilkan_ar()
	{
		$data['judul']			= "Laporan Piutang";

		if ($this->input->post('tampilkan') == "View Excel") {
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=LaporanPiutang.xls");
		}

		$var_bulan					= $this->input->post('bulan_labarugi');
		$var_tahun					= $this->input->post('tahun_labarugi');
		$cust						= $this->input->post('id_klien');
		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();
		$data['datklien']     = $this->Report_model->pilih_klien();
		$data['data_ar']	= $this->Report_model->get_ar($var_bulan, $var_tahun, $cust);



		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_list_ar", $data);
	}

	function tampilkan_ar_total()
	{
		$data['judul']			= "Laporan Piutang";

		if ($this->input->post('tampilkan') == "View Excel") {
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=LaporanPiutang.xls");
		}

		$var_bulan					= $this->input->post('bulan_labarugi');
		$var_tahun					= $this->input->post('tahun_labarugi');
		$cust						= $this->input->post('id_klien');

		// print_r($cust);
		// exit;




		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();
		$data['datklien']     = $this->Report_model->pilih_klien();
		$data['data_ar']	= $this->Report_model->get_ar_total($var_bulan, $var_tahun, $cust);



		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_list_ar_total", $data);
	}


	function ap()
	{
		$data['judul']			= "Laporan Hutang";
		$data['datklien']     = $this->Report_model->pilih_vendor();
		$this->load->view("report/v_ap", $data);
	}

	function tampilkan_ap()
	{
		$data['judul']			= "Laporan Hutang";

		if ($this->input->post('tampilkan') == "View Excel") {
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=LaporanPiutang.xls");
		}

		$var_bulan					= $this->input->post('bulan_labarugi');
		$var_tahun					= $this->input->post('tahun_labarugi');
		$cust						= $this->input->post('id_klien');
		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();
		$data['datklien']     = $this->Report_model->pilih_vendor();
		$data['data_ar']	= $this->Report_model->get_ap($var_bulan, $var_tahun, $cust);



		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_list_ap", $data);
	}

	function tampilkan_ap_total()
	{
		$data['judul']			= "Laporan Hutang";

		if ($this->input->post('tampilkan') == "View Excel") {
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=LaporanPiutang.xls");
		}

		$var_bulan					= $this->input->post('bulan_labarugi');
		$var_tahun					= $this->input->post('tahun_labarugi');
		$cust						= $this->input->post('id_klien');

		// print_r($cust);
		// exit;




		$data['data_bulan']		= $this->Report_model->get_bulan_coa();
		$data['data_tahun']		= $this->Report_model->get_tahun_coa();
		$data['datklien']     = $this->Report_model->pilih_vendor();
		$data['data_ar']	= $this->Report_model->get_ap_total($var_bulan, $var_tahun, $cust);



		$data['data_bulan_post']			= $var_bulan;
		$data['data_tahun_post']			= $var_tahun;

		$this->load->view("report/v_list_ap_total", $data);
	}

	function umur_piutang()
	{
		if ($this->input->get('tampilkan') == "View Excel") {
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=LaporanUmurPiutang.xls");
		}
		$data['judul']			= "Laporan Umur Piutang";
		$cust = '';
		$var_bulan				= $this->input->get('bulan');
		$var_tahun				= $this->input->get('tahun');
		$rekap					= $this->input->get('rekap');
		$data['datklien']		= $this->Report_model->pilih_klien();
		if ($rekap == 'rekap') {
			$data['data_ar']		= $this->Report_model->get_umur_piutang_rekap($var_bulan, $var_tahun);
		} else {
			$cust					= $this->input->get('id_klien');
			$data['data_ar']		= $this->Report_model->get_umur_piutang($var_bulan, $var_tahun, $cust);
		}
		$data['data_bulan_post'] = $var_bulan;
		$data['data_tahun_post'] = $var_tahun;
		$data['id_klien'] = $cust;
		$data['rekap'] = $rekap;
		$this->load->view("report/v_list_umur_piutang", $data);
	}
	
	
	
	//SYAMSUDIN LABA RUGI WIP 27-09-2022
	
	function labarugi2()
	{
		$data['judul']			= "Income Statement";
		$cek_periode_aktif		= $this->Report_model->cek_periode_aktif();
		if ($cek_periode_aktif > 0) {
			foreach ($cek_periode_aktif as $row_periode_aktif) {
				$tgl_periode_aktif	= $row_periode_aktif->periode;
				$data['bln_aktif']	= substr($tgl_periode_aktif, 0, 2);
				$data['thn_aktif']	= substr($tgl_periode_aktif, 3, 4);
			}
		}
		$this->load->view("report/v_labarugi2", $data);
	}

	function tampilkan_labarugi2()
	{
		$data['judul']			= "Income Statement";

		if ($this->input->post('tampilkan') == "View Excel") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('report/excel_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} elseif ($this->input->post('tampilkan') == "View Pdf") {
			$var_bln					= $this->input->post('bulan_labarugi');
			$var_thn					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			redirect('report/print_labarugi/' . $var_bln . '/' . $var_thn . '/' . $level);
		} else {

			$var_bulan					= $this->input->post('bulan_labarugi');
			$var_tahun					= $this->input->post('tahun_labarugi');
			$level						= $this->input->post('level');
			$data['level']				= $this->input->post('level');

			$data['data_bulan']		= $this->Report_model->get_bulan_coa();
			$data['data_tahun']		= $this->Report_model->get_tahun_coa();

			$data['data_nokir_pdptn']	= $this->Report_model->get_nokir_pdptn($var_bulan, $var_tahun, $level);
			$data['data_nokir_pdptn2']	= $this->Report_model->get_nokir_pdptn2($var_bulan, $var_tahun, $level);
			$data['data_nokir_hpp']		= $this->Report_model->get_nokir_hpp($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya52'] = $this->Report_model->get_nokir_biaya52($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya53'] = $this->Report_model->get_nokir_biaya53($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya54'] = $this->Report_model->get_nokir_biaya54($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya55'] = $this->Report_model->get_nokir_biaya55($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya56'] = $this->Report_model->get_nokir_biaya56($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya57'] = $this->Report_model->get_nokir_biaya57($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya58'] = $this->Report_model->get_nokir_biaya58($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya61']	= $this->Report_model->get_nokir_biaya61($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya']	= $this->Report_model->get_nokir_biaya($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya2']	= $this->Report_model->get_nokir_biaya2($var_bulan, $var_tahun, $level);
			$data['data_nokir_biaya3']	= $this->Report_model->get_nokir_biaya3($var_bulan, $var_tahun, $level);
			$data['data_nokir_fee']		= $this->Report_model->get_nokir_fee($var_bulan, $var_tahun, $level);
			$data['data_nokir_1105']	= $this->Report_model->get_nokir_1105($var_bulan, $var_tahun, $level);

			$data['data_bulan_post']			= $var_bulan;
			$data['data_tahun_post']			= $var_tahun;

			$this->load->view("report/v_list_labarugi2", $data);
			//redirect('report/print_labarugi');
		}
	}
	
	
	
	function lastOfMonth($year, $month) {
		return date("Y-m-d", strtotime('-1 second', strtotime('+1 month',strtotime($month . '/01/' . $year. ' 00:00:00'))));
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
	
	function preview_detail_jurnal_new(){
		$rows_Header	= $rows_Detail	= array();
		$Name_View		= 'v_jurnal_jv_detail';
		$Title_Jurnal	= 'PREVIEW DETAIL JURNAL';
		if($this->input->post()){
			$Code_Find	= $this->input->post('code');
			$Split_Find	= explode('^',$Code_Find);
			
			$Nomor_Jurnal	= $Split_Find[0];
			$Tipe_Jurnal	= $Split_Find[1];
			
			$rows_Detail	= $this->db->get_where('jurnal',array('nomor'=>$Nomor_Jurnal,'tipe'=>$Tipe_Jurnal))->result_array();
			if(strtolower($Tipe_Jurnal) == 'jv'){
				$rows_Header	= $this->db->get_where('javh',array('nomor'=>$Nomor_Jurnal))->row();
				$Name_View		= 'v_jurnal_jv_detail';
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL VOUCHER';
			}else if(strtolower($Tipe_Jurnal) == 'bum'){
				$rows_Header	= $this->db->get_where('jarh',array('nomor'=>$Nomor_Jurnal))->row();
				$Name_View		= 'v_jurnal_bum_detail';
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL BUM';
			}else if(strtolower($Tipe_Jurnal) == 'buk'){
				$rows_Header	= $this->db->get_where('japh',array('nomor'=>$Nomor_Jurnal))->row();
				$Name_View		= 'v_jurnal_buk_detail';
				$Title_Jurnal	= 'PREVIEW DETAIL JURNAL BUK';
			}
		}
		
		$data		= array(
			'title'			=> $Title_Jurnal,
			'rows_header'	=> $rows_Header,
			'rows_detail'	=> $rows_Detail,
			'arr_month'		=> $this->arr_bulan
		);
		
		$this->load->view('report/'.$Name_View, $data);
	}
	
	function preview_detail_jurnal_reff(){
		$rows_Header	= $rows_Detail	= array();
		$Name_View		= 'v_jurnal_detail_reff';
		$Title_Jurnal	= 'PREVIEW DETAIL JURNAL REF';
		$Type_Find		= '';
		if($this->input->post()){
			$Code_Find	= $this->input->post('code');
			$Type_Find	= $this->input->post('kategori');
			$Split_Find	= explode('^',$Code_Find);
			
			$Nomor_Trans	= $Split_Find[0];
			$Nomor_Jurnal	= $Split_Find[1];
			$Nomor_COA		= $Split_Find[2];
			
			if(strtolower($Type_Find) == 'material'){
				$rows_Header	= $this->ori_operasional->get_where('warehouse_adjustment',array('kode_trans'=>$Nomor_Trans))->row();			
				$Query_Detail	= "SELECT * FROM tran_warehouse_jurnal_detail WHERE kode_trans = '".$Nomor_Trans."' AND coa_gudang = '".$Nomor_COA."' AND no_jurnal = '".$Nomor_Jurnal."'";
				$rows_Detail	= $this->ori_operasional->query($Query_Detail)->result_array();
			}else if(strtolower($Type_Find) == 'consumable'){
				$rows_Header	= $this->ori_operasional->get_where('warehouse_adjustment',array('kode_trans'=>$Nomor_Trans))->row();
				$rows_Detail	= $this->ori_operasional->get_where('warehouse_adjustment_detail',array('kode_trans'=>$Nomor_Trans))->result_array();
			}else if(strtolower($Type_Find) == 'wip_butt' || strtolower($Type_Find) == 'wip_pipa' || strtolower($Type_Find) == 'wip_fitting' || strtolower($Type_Find) == 'wip_spool' || strtolower($Type_Find) == 'wip_tank'){
				$rows_Header	= $this->ori_operasional->get_where('data_erp_wip_group',array('id_trans'=>$Nomor_Trans))->row();
				$rows_Detail	= $this->ori_operasional->get_where('data_erp_wip',array('id_trans'=>$Nomor_Trans))->result_array();
			}
			
		}
		
		$data		= array(
			'title'			=> $Title_Jurnal,
			'rows_header'	=> $rows_Header,
			'rows_detail'	=> $rows_Detail,
			'arr_month'		=> $this->arr_bulan,
			'type_find'		=> $Type_Find
		);
		
		$this->load->view('report/'.$Name_View, $data);
	}
	
	function GetTablePrice($Jenis_Gudang = ''){
		$Table_Gudang		= 'price_book';
		if(strtolower($Jenis_Gudang) == 'subgudang'){
			$Table_Gudang		= 'price_book_subgudang';
		}else if(strtolower($Jenis_Gudang) == 'produksi'){
			$Table_Gudang		= 'price_book_produksi';
		}else if(strtolower($Jenis_Gudang) == 'project'){
			$Table_Gudang		= 'price_book_project';
		}
		
		return $Table_Gudang;
	}
	
	function SubQueryStock($WHERE_Sub = ""){
		$WHERE_Find_Sub	= "1=1";
		if($WHERE_Sub){
			if(!empty($WHERE_Find_Sub))$WHERE_Find_Sub	.=" AND ";
			$WHERE_Find_Sub	.=$WHERE_Sub;
		}
		
		$Query_Sub	= "SELECT
							tras_stock.id_material,
							tras_stock.id_gudang,
							MAX(tras_stock.id) AS last_kode,
							head_whr.coa_1,
							head_whr.nm_gudang,
							head_whr.category AS category_gudang
						FROM
							tran_warehouse_jurnal_detail tras_stock
							LEFT JOIN warehouse head_whr ON tras_stock.id_gudang=head_whr.id
						WHERE
							".$WHERE_Find_Sub."
						GROUP BY
							tras_stock.id_material,
							tras_stock.id_gudang";
		return	$Query_Sub;
		
	}
	
	function preview_detail_material_stock(){
		$rows_COA	= $rows_Material = $rows_NonMaterial = array();
		$Name_View		= 'v_jurnal_detail_stock';
		$Title_Jurnal	= 'PREVIEW DETAIL STOCK';
		$Tgl_Stock		= '';
		$Type_Find		= '';
		if($this->input->post()){
			$Code_Find	= $this->input->post('code');
			$Type_Find	= $this->input->post('kategori');
			$Split_Find	= explode('^',$Code_Find);
			
			$Nomor_COA		= $Split_Find[0];
			$Tgl_Stock		= $Split_Find[1];
			
			$Tgl_Stock		= date('Y-m-d');
			$Title_Jurnal	.=' '.date('d-m-Y',strtotime($Tgl_Stock));
			
			$Query_COA		= "SELECT * FROM COA WHERE no_perkiraan = '".$Nomor_COA."' ORDER BY id DESC LIMIT 1";
			$rows_COA		= $this->db->query($Query_COA)->row();
			
			if(strtolower($Type_Find) == 'material' || strtolower($Type_Find) == 'consumable'){
				$Query_WHR		= "SELECT GROUP_CONCAT(id) AS kode_wh FROM warehouse WHERE coa_1 = '".$Nomor_COA."'";
				$rows_WHR		= $this->ori_operasional->query($Query_WHR)->row();
				if($rows_WHR){
					$Code_WHR		= $rows_WHR->kode_wh;
					if(strtolower($Type_Find) == 'material'){
						$Title_Jurnal	= 'PREVIEW DETAIL STOCK MATERIAL';
						$Sub_Find		= "tras_stock.id_gudang IN('".str_replace(",","','",$Code_WHR)."')";	
						$WHERE			= "1=1";
						 
						if($Tgl_Stock){
							if(!empty($Sub_Find))$Sub_Find	.=" AND ";
							$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Tgl_Stock."'";
						}
						
						$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
						$Query_Material		= "SELECT
													head_mstr.idmaterial,
													head_mstr.id_material,
													head_mstr.nm_material,
													head_mstr.nm_category,
													head_stock.qty_stock_awal,
													head_stock.qty_in,
													head_stock.qty_out,
													head_stock.qty_stock_akhir,
													head_stock.harga,
													head_stock.harga_bm,
													head_stock.nilai_akhir_rp,
													head_stock.nilai_awal_rp,
													head_stock.nilai_trans_rp,
													det_stock.id_gudang,
													det_stock.nm_gudang
												FROM
													tran_warehouse_jurnal_detail head_stock
												INNER JOIN (
													".$Query_Sub_Find."
												)det_stock ON head_stock.id=det_stock.last_kode
												LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material
												WHERE ".$WHERE;
						$rows_Material		= $this->ori_operasional->query($Query_Material)->result_array();
					}else if(strtolower($Type_Find) == 'consumable'){
						$Title_Jurnal	= 'PREVIEW DETAIL STOCK CONSUMABLE';
						if($Tgl_Stock < date('Y-m-d')){
							
							$Query_NonMaterial	= "SELECT * FROM warehouse_rutin_stock_per_day WHERE gudang IN('".str_replace(",","','",$Code_WHR)."') AND stock <> 0 AND hist_date LIKE '".$Tgl_Stock."%' ORDER BY material_name ASC";
							$rows_NonMaterial	= $this->ori_operasional->query($Query_NonMaterial)->result();
							
						}else{							
							$Query_NonMaterial	= "SELECT * FROM warehouse_rutin_stock WHERE gudang IN('".str_replace(",","','",$Code_WHR)."') AND stock <> 0 ORDER BY material_name ASC";
							$rows_NonMaterial	= $this->ori_operasional->query($Query_NonMaterial)->result();
							
						}
					}
					
					
				}
			}else if(strtolower($Type_Find) == 'wip_butt' || strtolower($Type_Find) == 'wip_pipa' || strtolower($Type_Find) == 'wip_fitting' || strtolower($Type_Find) == 'wip_spool'){
				$Query_Sub	= "
							SELECT
								head_trans.*,
								DATE_FORMAT( head_trans.created_date, '%Y-%m-%d' ) AS date_in,
								x_detail.nil_close,
								x_detail.qty_close,
								x_detail.nil_open,
								x_detail.qty_open,
								head_prod.coa AS no_perkiraan 
							FROM
								data_erp_wip_group head_trans
								LEFT JOIN product_parent head_prod ON LOWER( head_trans.product ) = LOWER( head_prod.product_parent )
								LEFT JOIN (
								SELECT
									id_trans,
									SUM( CASE WHEN DATE_FORMAT( created_date, '%Y-%m-%d' ) <= '".$Tgl_Stock."' THEN nilai_unit ELSE 0 END ) AS nil_close,
									SUM( CASE WHEN DATE_FORMAT( created_date, '%Y-%m-%d' ) <= '".$Tgl_Stock."' THEN 1 ELSE 0 END ) AS qty_close,
									SUM( CASE WHEN DATE_FORMAT( created_date, '%Y-%m-%d' ) > '".$Tgl_Stock."' THEN nilai_unit ELSE 0 END ) AS nil_open,
									SUM( CASE WHEN DATE_FORMAT( created_date, '%Y-%m-%d' ) > '".$Tgl_Stock."' THEN 1 ELSE 0 END ) AS qty_open 
								FROM
									data_erp_fg 
								WHERE
									LOWER( jenis ) = 'in' 
									AND NOT ( id_trans IS NULL OR TRIM( id_trans ) = '' OR id_trans = '0' OR TRIM( id_trans ) = '-' ) 
								GROUP BY
									id_trans 
								ORDER BY
									id_trans ASC 
								) x_detail ON x_detail.id_trans = head_trans.id_trans 
							WHERE
								DATE_FORMAT( head_trans.created_date, '%Y-%m-%d' ) <= '".$Tgl_Stock."' 
								AND LOWER( head_trans.jenis ) = 'in' 
								AND NOT (
									head_trans.id_trans IS NULL 
									OR TRIM( head_trans.id_trans ) = '' 
									OR head_trans.id_trans = '0' 
									OR TRIM( head_trans.id_trans ) = '-' 
								) 
								AND head_prod.coa = '".$Nomor_COA."'
							GROUP BY
								head_trans.id_trans 
							ORDER BY
								DATE_FORMAT( head_trans.created_date, '%Y-%m-%d' ) DESC
								
							
							";
							
				$Query_WIP			= "SELECT x_summary.* FROM (".$Query_Sub.")x_summary WHERE x_summary.qty_open > 0";
				
				$rows_NonMaterial	= $this->ori_operasional->query($Query_WIP)->result();
		}
			
			
			
			
		}
		
		$data		= array(
			'title'				=> $Title_Jurnal,
			'rows_coa'			=> $rows_COA,
			'rows_material'		=> $rows_Material,
			'rows_nonmaterial'	=> $rows_NonMaterial,
			'tgl_stock'			=> $Tgl_Stock,
			'type_find'			=> $Type_Find
		);
		
		$this->load->view('report/'.$Name_View, $data);
	}
	
	function download_stok_material(){
		$rows_COA	= $rows_Material = $rows_NonMaterial = array();
		$Name_View		= 'v_detail_excel_stok';
		$Title_Jurnal	= 'REPORT DETAIL STOCK';
		$Tgl_Stock		= $Type_Find = '';
		if($this->input->get()){
			//echo"<pre>";print_r($this->input->get());exit;
			$Nomor_COA	= urldecode($this->input->get('coa'));
			$Tgl_Stock	= urldecode($this->input->get('tgl'));
			$Type_Find	= urldecode($this->input->get('kategori'));
			
			//$Tgl_Stock		= date('Y-m-d');
			
			
			$Query_COA		= "SELECT * FROM COA WHERE no_perkiraan = '".$Nomor_COA."' ORDER BY id DESC LIMIT 1";
			$rows_COA		= $this->db->query($Query_COA)->row();
			
			if(strtolower($Type_Find) == 'material' || strtolower($Type_Find) == 'consumable'){
				$Query_WHR		= "SELECT GROUP_CONCAT(id) AS kode_wh FROM warehouse WHERE coa_1 = '".$Nomor_COA."'";
				$rows_WHR		= $this->ori_operasional->query($Query_WHR)->row();
				if($rows_WHR){
					$Code_WHR		= $rows_WHR->kode_wh;
					if(strtolower($Type_Find) == 'material'){
						$Title_Jurnal	= 'PREVIEW DETAIL STOCK MATERIAL';
						$Sub_Find		= "tras_stock.id_gudang IN('".str_replace(",","','",$Code_WHR)."')";	
						$WHERE			= "1=1";
						 
						if($Tgl_Stock){
							if(!empty($Sub_Find))$Sub_Find	.=" AND ";
							$Sub_Find	.="DATE(tras_stock.tgl_trans ) <= '".$Tgl_Stock."'";
						}
						
						$Query_Sub_Find		= $this->SubQueryStock($Sub_Find);
						$Query_Material		= "SELECT
													head_mstr.idmaterial,
													head_mstr.id_material,
													head_mstr.nm_material,
													head_mstr.nm_category,
													head_stock.qty_stock_awal,
													head_stock.qty_in,
													head_stock.qty_out,
													head_stock.qty_stock_akhir,
													head_stock.harga,
													head_stock.harga_bm,
													head_stock.nilai_akhir_rp,
													head_stock.nilai_awal_rp,
													head_stock.nilai_trans_rp,
													det_stock.id_gudang,
													det_stock.nm_gudang
												FROM
													tran_warehouse_jurnal_detail head_stock
												INNER JOIN (
													".$Query_Sub_Find."
												)det_stock ON head_stock.id=det_stock.last_kode
												LEFT JOIN raw_materials head_mstr ON head_stock.id_material = head_mstr.id_material
												WHERE ".$WHERE;
						$rows_Material		= $this->ori_operasional->query($Query_Material)->result_array();
					}else if(strtolower($Type_Find) == 'consumable'){
						$Title_Jurnal	= 'PREVIEW DETAIL STOCK CONSUMABLE';
						if($Tgl_Stock < date('Y-m-d')){
							
							$Query_NonMaterial	= "SELECT * FROM warehouse_rutin_stock_per_day WHERE gudang IN('".str_replace(",","','",$Code_WHR)."') AND stock <> 0 AND hist_date LIKE '".$Tgl_Stock."%' ORDER BY material_name ASC";
							
							$rows_NonMaterial	= $this->ori_operasional->query($Query_NonMaterial)->result();
							
						}else{							
							$Query_NonMaterial	= "SELECT * FROM warehouse_rutin_stock WHERE gudang IN('".str_replace(",","','",$Code_WHR)."') AND stock <> 0 ORDER BY material_name ASC";
							$rows_NonMaterial	= $this->ori_operasional->query($Query_NonMaterial)->result();
							
						}
						
						//echo"<pre>";print_r($Query_NonMaterial);exit;
					}
					
					
				}
			}
			
			$Title_Jurnal	.=' '.date('d-m-Y',strtotime($Tgl_Stock));
			
			
		}
		
		$data		= array(
			'title'				=> $Title_Jurnal,
			'rows_coa'			=> $rows_COA,
			'rows_material'		=> $rows_Material,
			'rows_nonmaterial'	=> $rows_NonMaterial,
			'tgl_stock'			=> $Tgl_Stock,
			'type_find'			=> $Type_Find
		);
		
		$this->load->view('report/'.$Name_View, $data);
	}
	
}
