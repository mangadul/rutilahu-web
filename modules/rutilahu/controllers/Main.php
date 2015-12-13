<?php

class Main extends CI_Controller {

    function __construct() {
        parent::__construct();
		
		if(!$this->session->userdata('username'))
		{
			header(sprintf("Location: %s", base_url().'index.php/dashboard/Mainindex/Logout'));
		}
    }

    public function index() {
        $this->load->view('main');
    }

    public function upload() {
        $this->load->view('upload_data');
    }
				
	function upload_rutilahu()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'zip';
		$config['max_size']	= '409600';
		$config['remove_spaces']  = true;

		$this->load->library('upload', $config);
		$this->load->library('unzip');
		$this->unzip->allow(array('json', 'zip'));
		try {
			if(!$this->upload->do_upload('upload_data'))
			{
				throw new Exception(sprintf('File Gagal Upload, ukuran file tidak boleh lebih dari 4MB. Tipe file yg diperbolehkan: zip.\nErrors: %s', $this->upload->display_errors()));
			} else
			{
				$data = $this->upload->data();
				$namafile = $data['file_name'];
				$this->unzip->extract(realpath("./uploads/".$namafile), realpath("./uploads/"));
				$listfile = $this->unzip->central_dir_list;
				foreach($listfile as $k=>$v)
				{
					$data = file_get_contents(realpath("./uploads/".$k));
					if($this->import_data($data))
					{
						echo json_encode(array("success"=>true, 
							"message"=>"Data BERHASIL diupload.", 
							"file" => $namafile));
					} else{
						echo json_encode(array(
							'success' => false,
							'message' => 'Sukses diupload GAGAL dimasukan ke database',
							'file' => $namafile
						));														
					}
				}
			}
		} catch(Exception $e)
		{
			echo json_encode(array(
				'success' => false,
				'message' => $e->getMessage(),
				'file' => 'undefined'
			));			
		}	
	}

	function kosongkan_tbl_temp_upload()
	{
		if($this->input->post('proses'))
		{
			$query = "truncate table tmp_penerima";
			if($this->db->query($query))
				echo "Data berhasil dikosongkan.";			
		}
	}
	
	function import_data($datajson)
	{
		$this->db->query("TRUNCATE TABLE tmp_penerima");		
		$data = json_decode($datajson);
		if($this->db->insert_batch('tmp_penerima', $data)) return true;
			else return false;
	}

	function get_kabupaten()
	{
		$query = "
			select kode_kab, 
			concat(kode_kab,' - ', kabupaten) as kabupaten
			from tbl_kabupaten
		";
		$this->getData($query,array(), 'kode_kab');
	}

	function get_data_tahun()
	{
		$tahun = $this->input->get('tahun');
		$query = sprintf("select * from tbl_penerima where tahun_terima = '%d'", $tahun);
		$this->getData($query,array(), 'tahun_terima');		
	}

	function get_tahun_peta()
	{
		$query = "
			select distinct(tahun_terima)
			from tbl_penerima		
		";
		$rw = $this->db->query($query)->result();
		foreach($rw as $k=>$v)
		{
			$tahun[] = $v->tahun_terima;
		}
		echo json_encode(array("data"=>$tahun));
	}

	function get_titiktengah_peta()
	{
		$query = "select * from tbl_peta_titik_tengah";
		$rw = $this->db->query($query)->result();
		echo json_encode($rw);
	}
	
	function get_zonasi_peta()
	{
		$query = sprintf("select * from tbl_peta_zonasi");
		$this->getData($query,array(), 'id_zona');		
	}
	
	function get_tahun()
	{
		$query = "
			select distinct(tahun_terima) as tahun,
			tahun_terima as id_tahun
			from tbl_penerima		
		";
		$this->getData($query,array(), 'tahun_terima');
	}
	
	function getData($query, $w, $orderby)
	{		
        $start = $this->input->get('start') ? $this->input->get('start') : 0;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 100;
        $total = $this->db->query($query)->num_rows();
		
		$where = " AND ";		
		$hitung = 0;		
		for($c=0; $c < count($w); $c++)
		{		
			$where .= $w[$c];
			$where .= " like '%". $this->input->get('query')."%'";
			if($c < (count($w) - 1)) $where .= " OR ";
			$hitung++;
		}
		
        if($this->input->get('query'))
        {
			$query .= $where;
        }
		
        $query .= sprintf(" ORDER BY %s ASC", $orderby);
        $query .= sprintf(" LIMIT %d,%d", $start, $limit);

        $rs = $this->db->query($query);
        $totdata = $rs->num_rows();
        if($totdata)
        {
			echo json_encode(array('total'=>$total, 'data'=>$rs->result_array()));
        } else return false;	
		
	}	
		
	function get_data_upload()
	{
		$query = "
			SELECT * FROM tmp_penerima
			WHERE is_catat = '1'
		";
		$this->getData($query, array("tmp_penerima.namalengkap","tmp_penerima.ktp", "tmp_penerima.kk", "tmp_penerima.jalan_desa", "tmp_penerima.kecamatan", "tmp_penerima.kabupaten"), "tmp_penerima.id_penerima");
	}

	function posting_data_duplikat()
	{
		if($this->input->post('proses_duplikat_data'))
		{
			$del_data = "delete from tbl_penerima where id_penerima in (
				select id_penerima from tmp_penerima where is_catat = '1'
			)";
			if($this->db->query($del_data))
			{
				$this->_posting_data();				
			}
		}
	}
	
	function posting_data_survey()
	{
		if($this->input->post('proses_posting_survey'))
		{
			$sql_duplikat = "
				SELECT GROUP_CONCAT(id_penerima, '->', namalengkap) as duplikat from tmp_penerima
				WHERE id_penerima in (
				SELECT id_penerima from tbl_penerima
				) and is_catat = '1'			
			";
			$data_duplikat = $this->db->query($sql_duplikat)->row();
			$total_duplikat = count(explode(',',$data_duplikat->duplikat));
			if(strlen($data_duplikat->duplikat))
			{
				echo sprintf("Ada total %d data yang duplikat, Data Duplikat: '%s'. Apakah anda akan mengganti data dengan yang baru?", $total_duplikat, $data_duplikat->duplikat);
			} else {
				$this->_posting_data();
			}			
		}
	}

	private function _posting_data()
	{
		$query = "INSERT INTO tbl_penerima
				SELECT * FROM tmp_penerima WHERE is_catat = '1'";
		$rs = $this->db->query($query);
		$q_hitung = "SELECT * FROM tmp_penerima WHERE is_catat = '1'";
		$totdata = $this->db->query($q_hitung)->num_rows();
		if($totdata > 0) {
			$this->db->query("TRUNCATE TABLE tmp_penerima");
			$this->db->query("UPDATE tbl_penerima 
						JOIN tbl_temp_penerima ON tbl_temp_penerima.id_penerima = tbl_penerima.id_penerima
						SET tbl_penerima.no_urut = tbl_temp_penerima.no_urut");			
			echo sprintf("Total data %d berhasil disimpan.", $totdata);
		} else echo "Data GAGAL disimpan!";
	}
	
	function marker_peta()
	{
		$query = "
			select tbl_penerima.*,
			tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
			from tbl_penerima
			inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
			inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
		";
		if($this->db->query($query)->num_rows() > 0)
		{
			$data = $this->db->query($query)->result();
			foreach($data as $row)
			{
				//$this->_dump($row);
				$marker[] = array(
					'lat' => $row->latitude,
					'lng' => $row->longitude,
					'marker' => array('title' => $row->id_penerima.'/'.$row->namalengkap),
					//'listeners' => array(sprintf("click: function(e){return showdatapenerima(%s,e);}", $row->id_penerima)),
				);
			}
			echo json_encode($marker);
		}
	}


	function data_survey()
	{
		$this->load->view('data_penerima');
	}
	
	function get_data_penerima()
	{
		$this->data_peta();
	}

	function get_data_penerima_id()
	{
		$this->data_peta();
	}
	
	function titik_peta()
	{
		$query = "
			select tbl_penerima.*,
			tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
			from tbl_penerima
			inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
			inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
		";
		if($this->db->query($query)->num_rows() > 0)
		{
			$data = $this->db->query($query)->result();
			foreach($data as $row)
			{
				$marker[] = array(
					'id_penerima' => $row->id_penerima,
					'nama_penerima' => $row->namalengkap,
					'latitude' => (float)$row->longitude,
					'longitude' => (float)$row->latitude,
					'title' => $row->id_penerima.'-'.$row->namalengkap,
					'content' => $row->id_penerima.'-'.$row->namalengkap
				);
			}
			header('Content-type: application/json');
			echo json_encode(array('markers'=>$marker));
		}
	}

	function foto_path($foto)
	{
		$id = $this->get_sess_penerima();
		$newfoto = str_replace(".jpg", "_thumb.jpg", $foto);
		$isfoto = realpath(FCPATH.'foto/'.$id.'/'.$foto);
		$pfoto = base_url() . 'foto/' . $id . '/' .$newfoto;
		$nofoto = base_url() .'assets/images/noimage.jpg';
		if(file_exists(realpath(FCPATH.'foto/'.$id.'/'.$foto)))
		{
			if(file_exists(realpath(FCPATH.'foto/'.$id.'/'.$newfoto)))
			{
				return $pfoto;				
			} else {
				$this->image_resize(realpath(FCPATH.'foto/' .$id. '/'.$foto));
				return $pfoto;
			}
		} else {
			return $nofoto;
		}
	}
	
	function get_photo_penerima()
	{
		$id = $this->get_sess_penerima();
		if($id)
		{
			$query = sprintf("
				select 
				img_foto_penerima, img_tampak_depan_rumah, 
				img_tampak_samping_1, img_tampak_samping_2, img_tampak_belakang, 
				img_tampak_dapur, img_tampak_jamban, img_tampak_sumber_air
				from tbl_penerima
				where id_penerima = '%d'
			", $id);
			
			if($this->db->query($query)->num_rows() > 0)
			{
				$data = $this->db->query($query)->row();
				$photo[] = array(
					array(
						'name' => 'Foto Penerima',						
						'url' => $this->foto_path($data->img_foto_penerima)
					),
					array(
						'name' => 'Tampak Depan Rumah',
						'url' => $this->foto_path($data->img_tampak_depan_rumah)
					),
					array(
						'name' => 'Tampak Samping 1',
						'url' => $this->foto_path($data->img_tampak_samping_1)
					),
					array(
						'name' => 'Tampak Samping 2',
						'url' => $this->foto_path($data->img_tampak_samping_2)
					),
					array(
						'name' => 'Tampak Belakang',
						'url' => $this->foto_path($data->img_tampak_belakang)
					),
					array(
						'name' => 'Dapur',
						'url' => $this->foto_path($data->img_tampak_dapur)
					),
					array(
						'name' => 'Foto Jamban',
						'url' => $this->foto_path($data->img_tampak_jamban)
					),
					array(
						'name' => 'Sumber Air',
						'url' => $this->foto_path($data->img_tampak_sumber_air)
					)
				);
				echo json_encode(array('success'=>true,'images'=>$photo[0]));
			}
		}			
	}
	
	function get_kec_peta()
	{
		if($this->input->get('kec'))
		{
			$isdata = 0;
			$sql = sprintf("select * from tbl_penerima where kode_kec = '%s'", $this->input->get('kec'));
			$res = $this->db->query($sql)->result();
			$isdata = $this->db->query($sql)->num_rows();
			if($isdata)
			{
				echo json_encode(array('total'=>$isdata, 'data'=>$res));
			} else echo json_encode(array('total'=>0, 'data'=>array()));
		}
	}

	function get_kec_nama()
	{
		if($this->input->get('kab'))
		{
			$sql = sprintf("select kode_kecamatan, kecamatan from tbl_kecamatan where kode_kab='%s'", $this->input->get('kab'));
			$data = $this->db->query($sql)->result();
			echo json_encode($data);
		}
	}
	
	function get_kab_peta()
	{
		$sql = "select kode_kab, kabupaten from tbl_kabupaten";
		$data = $this->db->query($sql)->result();
		echo json_encode($data);
	}
	
	function get_detail_penerima()
	{
		//$id = $this->get_sess_penerima();
		if($this->input->post('idpenerima'))
		{
			$query = sprintf("
				select tbl_penerima.*,
				tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
				from tbl_penerima
				inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
				inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
				inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
				where tbl_penerima.id_penerima = '%d'
				limit 1
			", $this->input->post('idpenerima'));
			if($this->db->query($query)->num_rows() > 0)
			{
				$data = $this->db->query($query)->row();
				$this->set_sess_penerima($this->input->post('idpenerima'));
				echo json_encode(array("success"=>true, "data"=>$data));
			} else echo json_encode(array("success"=>false, "data"=>array()));
		}
	}

	function get_detail_penerimas()
	{
		$id = $this->get_sess_penerima();
		if($id)
		{
			$query = sprintf("
				select tbl_penerima.*,
				tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
				from tbl_penerima
				inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
				inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
				inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
				where tbl_penerima.id_penerima = '%d'
				limit 1
			", $id);
			if($this->db->query($query)->num_rows() > 0)
			{
				$data = $this->db->query($query)->row();
				echo json_encode(array("success"=>true, "data"=>$data));
			} else echo json_encode(array("success"=>false, "data"=>array()));
		}
	}
	
	function image_resize($imgpath)
	{
		$config['image_library'] = 'gd2';
		$config['source_image']	= $imgpath;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['rotation_angle'] = '45';		
		$config['width'] = 400;
		$config['height'] = 400;
		$this->load->library('image_lib', $config); 
		if($this->image_lib->resize())
		{
			$this->image_lib->rotate();
		} else echo $this->image_lib->display_errors();		
	}

	function set_sess_penerima($id)
	{
		if($id)
		{
			$this->session->userdata('idpenerima', '');
			$this->session->set_userdata('idpenerima', $id);
		}
	}
	
	function get_sess_penerima()
	{
		if($this->session->userdata('idpenerima'))
		{
			$id = $this->session->userdata('idpenerima');
			return $id;
		}
	}
	
	function markers()
	{
		$query = "
			select tbl_penerima.*,
			tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
			from tbl_penerima
			inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
			inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
			limit 10
		";
		if($this->db->query($query)->num_rows() > 0)
		{
			$data = $this->db->query($query)->result();
			foreach($data as $row)
			{
				$marker[] = array(
					'lat' => (float)$row->longitude,
					'lng' => (float)$row->latitude,
					'marker'=> array('title' => $row->id_penerima.'-'.$row->namalengkap),
					//'content' => $row->id_penerima.'-'.$row->namalengkap,
				);
			}
			//header('Content-type: application/json');
			return json_encode($marker);
		}
	}
	
	function data_peta()
	{
		$query = "
			select tbl_penerima.*,
			tbl_kecamatan.kecamatan, tbl_kabupaten.kabupaten, tbl_desa.desa_kelurahan as desa
			from tbl_penerima
			inner join tbl_desa on tbl_desa.kode_desa =  tbl_penerima.kode_desa
			inner join tbl_kecamatan on tbl_kecamatan.kode_kecamatan =  tbl_penerima.kode_kec
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab =  tbl_penerima.kode_kab
		";
		$this->getData($query, array("tbl_penerima.namalengkap","tbl_penerima.ktp", "tbl_penerima.kk", 
				"tbl_penerima.jalan_desa", "tbl_penerima.desa", "tbl_desa.desa_kelurahan","tbl_penerima.tahun_terima",
				"tbl_penerima.kode_desa", "tbl_penerima.kode_kec", "tbl_penerima.kode_kab", 
				"tbl_penerima.kecamatan", "tbl_penerima.kabupaten"), "tbl_penerima.id_penerima");
	}
	
	function peta()
	{
		//$this->load->view("gmap");
		//$this->load->view("peta");
		$this->load->view("tree");
	}
	
	function layer_peta()
	{
		
	}
	
    function dump($data)
    {
        print("<pre>");
        print_r($data);
        print("</pre>");
    }
    
		
	function optimize_tbl()
	{
		if($this->input->post('action'))
		{
			$this->load->dbutil();
			$result = $this->dbutil->optimize_database();
			if ($result !== FALSE)
			{
				echo "Data berhasil dioptimisasi.";
			} else echo $this->_dump($result);
		}
	}

	function getDataCombo($query)
	{
        $sql = $this->db->query($query);
		if ($sql->num_rows() > 0) {
			$res = $sql->result_array();
			echo json_encode(array('total'=>$sql->num_rows(), 'data'=>$this->toCombo($res)));
		} else echo json_encode(array('total'=>0, 'data'=>''));		
	}	
	
	function toCombo($data)
	{
		$i = 0;
		foreach($data as $d)
		{
			$k = array_keys($d);
			$v = array_values($d);
			$cmb[$v[0]] = $v[1];
			$i++;
		}
		if(is_array($cmb)) return $cmb;
	}	
	
	function combo($data)
	{
		$i = 0;
		foreach($data as $d)
		{
			$k = array_keys($d);
			$v = array_values($d);
			$cmb[$v[0]] = $v[1];
			$i++;
		}
		if(is_array($cmb)) return $cmb;
	}
		
	function _gunzip($file)
	{
		$file_name = $file;
		$buffer_size = 4096;
		// filesize()
		$out_file_name = str_replace('.gz', '', $file_name);
		$file = gzopen($file_name, 'rb');
		$out_file = fopen($out_file_name, 'wb');
		while(!gzeof($file)) {
		    fwrite($out_file, gzread($file, $buffer_size));
		}
		fclose($out_file);
		gzclose($file);
	}

	function _dump($s)
	{
		print('<pre>');
		print_r($s);
		print('</pre>');
	}
	
}