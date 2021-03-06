<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisa_soal extends Member_Controller {
	private $kode_menu = 'analisa_soal';
	private $kelompok = 'analisa_soal';
	private $url = 'manager/analisa_soal';
	
    function __construct(){
		parent:: __construct();
		$this->load->model('cbt_user_model');
		$this->load->model('cbt_user_grup_model');
		$this->load->model('cbt_tes_model');
		$this->load->model('cbt_tes_token_model');
		$this->load->model('cbt_nilai_model');
		$this->load->model('cbt_tes_topik_set_model');
		$this->load->model('cbt_tes_user_model');
		$this->load->model('cbt_tesgrup_model');
		$this->load->model('cbt_soal_model');
		$this->load->model('cbt_jawaban_model');
		$this->load->model('cbt_tes_soal_model');
		$this->load->model('cbt_konfigurasi_model');
		$this->load->model('cbt_tes_soal_jawaban_model');

        parent::cek_akses($this->kode_menu);
	}
	
    public function index($page=null, $id=null){
	
        $data['kode_menu'] = $this->kode_menu;
		$data['url'] = $this->url;
		$data['th'] ='no';
		if(!$this->db->table_exists('cbt_remidi')){
$data['pn'] = '<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header bg-info">
	<h5 class="modal-title">Nilai Peserta Ujian!</h5>
   
		</div>
		<div class="modal-body">
			<div class="row-fluid">
				<div class="card-body">
				<strong>Informasi!</strong>
				   Nilai akan ditampilkan jika ujian sudah berjalan.
					<br /><br />
				   </div>
			</div>
		</div>
	   
	</div>
</div>';
$data['th'] ='no';
   }else{
	$data['pn'] = "";
	$data['th'] ='table-hasil';
   }
        $tanggal_awal = date('Y-m-d H:i', strtotime('- 1 days'));
        $tanggal_akhir = date('Y-m-d H:i', strtotime('+ 1 days'));
        
        $data['rentang_waktu'] = $tanggal_awal.' - '.$tanggal_akhir;

        $query_group = $this->cbt_user_grup_model->get_groupnilai();
        $select = '<option value="semua">Pilih Kelas</option>';
        if($query_group->num_rows()>0){
        	$query_group = $query_group->result();
        	foreach ($query_group as $temp) {
        		$select = $select.'<option value="'.$temp->grup_id.'">'.$temp->grup_nama.'</option>';
        	}

        }else{
        	$select = '<option value="0">Tidak Ada Group</option>';
        }
        $data['select_group'] = $select;

        $query_tes = $this->cbt_tes_user_model->get_by_kelas();
        $select = '<option>Pilih Mapel</option>';
        if($query_tes->num_rows()>0){
        	$query_tes = $query_tes->result();
        	foreach ($query_tes as $temp) {
        		$select = $select.'<option value="'.$temp->modul_id.'">'.$temp->modul_nama.'</option>';
        	}
        }
        $data['select_tes'] = $select;
        
        $this->template->display_admin($this->kelompok.'/analisa_soal_view', 'Hasil Tes', $data);
    }

    function edit_tes(){
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('edit-testuser-id[]', 'Hasil Tes','required|strip_tags');
        $this->form_validation->set_rules('edit-pilihan', 'Pilihan','required|strip_tags');
        
        if($this->form_validation->run() == TRUE){
            $pilihan = $this->input->post('edit-pilihan', true);
            $tesuser_id = $this->input->post('edit-testuser-id', TRUE);

            if($pilihan=='hapus'){
                foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                        $this->cbt_tes_user_model->delete('tesuser_id', $kunci);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Hasil tes berhasil dihapus';
            }else if($pilihan=='hentikan'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$data_tes['tesuser_status']=4;
            			$this->cbt_tes_user_model->update('tesuser_id', $kunci, $data_tes);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Tes berhasil dihentikan';
            }else if($pilihan=='buka'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$data_tes['tesuser_status']=1;
            			$this->cbt_tes_user_model->update('tesuser_id', $kunci, $data_tes);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Tes berhasil dibuka, user bisa mengerjakan kembali';
            }else if($pilihan=='waktu'){
            	foreach( $tesuser_id as $kunci => $isi ) {
                    if($isi=="on"){
                    	$waktu = intval($this->input->post('waktu-menit', TRUE));

            			$this->cbt_tes_user_model->update_menit($kunci, $waktu);
                    }
                }
            	$status['status'] = 1;
            	$status['pesan'] = 'Waktu Tes berhasil ditambah';
            }

        }else{
            $status['status'] = 0;
            $status['pesan'] = validation_errors();
        }
        
        echo json_encode($status);
    }
function export($tes_id=null, $grup_id=null,$urutkan=null){
    	$data['gambar'] =  $this->cbt_konfigurasi_model->get_by_kolom('konfigurasi_id', 5);
    	$data['gambar2'] =  $this->cbt_konfigurasi_model->get_by_kolom('konfigurasi_id', 9);
    	$query = $this->cbt_konfigurasi_model->get_by_kolom_limit('konfigurasi_kode', 'cbt_nama', 1);
    	$query2 = $this->cbt_konfigurasi_model->get_by_kolom_limit('konfigurasi_kode', 'cbt_tahun', 7);
    	$data['site_name']=$query->row()->konfigurasi_isi;
    	$data['thn_sekolah']=$query2->row()->konfigurasi_isi;
        if(!empty($tes_id) AND !empty($grup_id) AND !empty($urutkan)){
            $this->load->library('excel');
         
       $search = "";
		$start = 0;
		$rows = 10;
    $indikator = '';
           //$this->cbt_jawaban_model->get_jwbsoal($tes_id);
          $query = $this->cbt_jawaban_model->get_jwbsoal($tes_id);
            $inputFileName = './public/form/form-data-analisa.xls';
            $tt = array();	
            $excel = PHPExcel_IOFactory::load($inputFileName);
            $worksht = $excel->getSheet(0);
            $worksht->setCellValueByColumnAndRow(2, 1, $data['site_name']);
             $worksht->setCellValueByColumnAndRow(2, 2, $data['thn_sekolah']);
            //$worksht->setCellValueByColumnAndRow(2, 2, 'ini isi cell A2');
            $worksheet = $excel->getSheet(0);

            if($query->num_rows()>0){
                $query = $query->result();
                $row = 8;
                $i=1;
                foreach ($query as $temp) {
                	$query2 = $this->cbt_jawaban_model->get_jwbrespon($tes_id,$temp->jawaban_soal_id);
                    $query3 = $this->cbt_jawaban_model->get_jwbbenar($tes_id,$temp->jawaban_soal_id);
                    $query4 = $this->cbt_jawaban_model->get_jwbsalah($tes_id,$temp->jawaban_soal_id);
                    $ceksoal = strip_tags($temp->soal_detail);
                	 $worksheet->setCellValueByColumnAndRow(0, $row, $i++);
                	 $worksheet->setCellValueByColumnAndRow(1, $row, $ceksoal);
                	     $query2 = $query2->result();
                	     foreach ($query2 as $temp2) 
                	     	
     {
  $cek0 = $temp2->hasil;
   $worksheet->setCellValueByColumnAndRow(2, $row, $cek0);
   }
  $query3 = $query3->result();
                	     foreach ($query3 as $temp3) 
                	     	
     {
  $cek1 = $temp3->hasil;
   $worksheet->setCellValueByColumnAndRow(3, $row, $cek1);
   }
    $query4 = $query4->result();
                   foreach ($query4 as $temp4) 
                 	     	
     {
  $cek2 = $temp4->hasil;
   $worksheet->setCellValueByColumnAndRow(4, $row, $cek2);
   }   
   if($cek2 != 0 or $cek1 !=0){
   $indikator=$cek1*100/$cek0;

    $worksheet->setCellValueByColumnAndRow(5, $row, $indikator.' %');
 } 	   
   $nilai = $indikator;
 if($nilai>=50){
 	$analisa = "Sedang";
 }elseif($nilai>=70){
$analisa = "Mudah";
}else{
	$analisa = "Sulit";
}           
 $worksheet->setCellValueByColumnAndRow(6, $row, $analisa);

                    $row++;

                }
                
            }
            $filename='Data Hasil Tes - '.date('Y-m-d H:i').'.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
                 
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
        }
    }
 
	function get_datatable(){
		// variable initialization
		$tes_id = $this->input->get('tes');
		$grup_id = $this->input->get('group');
	//	$urutkan = $this->input->get('urutkan');
		$waktu = $this->input->get('waktu');
	//	$keterangan = $this->input->get('keterangan');
		$status = $this->input->get('status');
		$tanggal = explode(" - ", $waktu);

		$search = "";
		$start = 0;
		$rows = 10;

		// get search value (if any)
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
			$search = $_GET['sSearch'];
		}

		// limit
		$start = $this->get_start();
		$rows = $this->get_rows();

		// run query to get user listing
	
			$query = $this->cbt_jawaban_model->get_jwbsoal($tes_id);
			$iTotal= $this->cbt_jawaban_model->get_countjwbsoal($tes_id)->row()->hasil;
		
		
		
		$iFilteredTotal = $query->num_rows();
	    
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
	        "iTotalRecords" => $iTotal,
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

	    // get result after running query and put it in array
		$i=$start;
		$query = $query->result();
	   foreach ($query as $temp) {			
			 // $m[] = array($temp->modul_nama);
//	   $nl[] = array(number_format($temp->nilai));
		$r = array();	
		$s = array();
		$t = array();					
		$record = array();
		$record[] = ++$i;
			 $record[] = $temp->soal_detail;
	   

 
  $query2 = $this->cbt_jawaban_model->get_jwbrespon($tes_id,$temp->jawaban_soal_id);
  $query3 = $this->cbt_jawaban_model->get_jwbbenar($tes_id,$temp->jawaban_soal_id);
  $query4 = $this->cbt_jawaban_model->get_jwbsalah($tes_id,$temp->jawaban_soal_id);

  $query2 = $query2->result();
	   foreach ($query2 as $temp2) 
	
  {
  	$cek0 = $temp2->hasil;
   $record[] = $cek0;
}
 
  $query3 = $query3->result();
	   foreach ($query3 as $temp3) 
  {
  	 $cek1 = $temp3->hasil;
   $record[] = $cek1;
}
    $query4 = $query4->result();
	   foreach ($query4 as $temp4) 
  {
  	$cek2 = $temp4->hasil;
   $record[] = $cek2;
}
if($cek2 != 0 or $cek1 !=0){
   $indikator=$cek1*100/$cek0;

     $record[] = $indikator.' %';
 }
 $nilai = $indikator;
 if($nilai>=50){
 	$analisa = "<div class='badge badge-warning'>Sedang</div>";
 }elseif($nilai>=70){
$analisa = "<div class='badge badge-blue'>Mudah</div>";
}else{
	$analisa = "<div class='badge badge-danger'>Sulit</div>";
}

      $record[] = $analisa;
	  

			$output['aaData'][] = $record;
		}
		// format it to JSON, this output will be displayed in datatable
        
		echo json_encode($output);
	}
	
	function cek(){
		$pilihan = $_POST["pilih-tes"];



$mpl = $this->cbt_jawaban_model->get_jwbkelas($pilihan);
			foreach ($mpl->result() as $mp=>$mplp)
{

 $status['mpl'] = '<b>'.$mplp->modul_nama.'</b>&nbsp;&nbsp;';
 	      
}


		$kls = $this->cbt_jawaban_model->get_jwbkelas($pilihan);
			foreach ($kls->result() as $rk=>$rkl)
{

 $status['kls'][] = '<b><div class="badge badge-danger">'.$rkl->grup_nama.'</div></b>&nbsp;&nbsp;';
 	      
}
		  	
         
           $tkt = $this->cbt_jawaban_model->get_jwbtkt($pilihan);
		  	foreach ($tkt->result() as $tn=>$tnk)
{

 $status['tkt'][] = '<b>'.$tnk->level_kode_kelas.'</b>&nbsp;&nbsp;';	      
}

          echo json_encode($status);
	}
	
	function get_start() {
		$start = 0;
		if (isset($_GET['iDisplayStart'])) {
			$start = intval($_GET['iDisplayStart']);

			if ($start < 0)
				$start = 0;
		}

		return $start;
	}

	function get_rows() {
		$rows = 10;
		if (isset($_GET['iDisplayLength'])) {
			$rows = intval($_GET['iDisplayLength']);
			if ($rows < 5 || $rows > 500) {
				$rows = 10;
			}
		}

		return $rows;
	}

	function get_sort_dir() {
		$sort_dir = "ASC";
		$sdir = strip_tags($_GET['sSortDir_0']);
		if (isset($sdir)) {
			if ($sdir != "asc" ) {
				$sort_dir = "DESC";
			}
		}

		return $sort_dir;
	}
}