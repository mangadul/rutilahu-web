<?php

class Master extends CI_Controller {

    function __construct() {
        parent::__construct();
		
		if(!$this->session->userdata('username'))
		{
			header(sprintf("Location: %s", base_url().'index.php/dashboard/Mainindex/Logout'));
		}
		
    }

    public function index() {
    }

    function get_data_SA()
    {
        $sql = $this->db->query('select * from m_sa');
		if ($sql->num_rows() > 0) {
			$res = $sql->result_array();
			echo json_encode(array('total'=>$sql->num_rows(), 'data'=>$res));
		} else echo json_encode(array('total'=>0, 'data'=>''));
    }

    function get_detail_SA()
    {
        $sql = $this->db->query(sprintf('
				select t_sa.*,m_sa.NO_SA from t_sa 
				inner join m_sa on (m_sa.id_sa = t_sa.id_sa)
				where t_sa.id_sa = "%d"
			', $this->session->userdata('ID_SA')));
		if ($sql->num_rows() > 0) {
			$res = $sql->result_array();
			echo json_encode(array('total'=>$sql->num_rows(), 'data'=>$res));
		} else echo json_encode(array('total'=>0, 'data'=>''));
    }
	
	function get_sa_client()
	{
		$query = "
			select id_sa_client, 
			concat(sa_client_code,' - ', sa_company) as klien
			from m_sa_client
		";
		$this->getData($query);
	}

	function get_sales_office()
	{
		$query = "
			select ID_SO, 
			concat(SO_CODE,' - ', SO_NAME) as SALES_OFFICE
			from m_sales_office
		";
		$this->getData($query);
	}

	function get_sales_group()
	{
		$query = "
			select ID_SG, 
			concat(SG_CODE,' - ', SG_NAME) as SALES_GROUP
			from m_sales_group
		";
		$this->getData($query);
	}

	function get_uom()
	{
		$query = "
			select ID_UOM, 
			concat(CODE_UOM_SA,' - ', DESC_UOM_SA) as UOM
			from m_uom
		";
		$this->getData($query);
	}

	function get_spbbe()
	{
		$query = "
			select ID_SPBBE, 
			concat(KODE_SPBBE,' - ', NAMA_SPBBE) as NAMA_SPBBE
			from m_spbbe
		";
		$this->getData($query);
	}

	function get_supir()
	{
		$query = "select * from m_driver";
		$this->getData($query);
	}

	function get_bulan()
	{
		$query = "select * from m_bulan";
		$this->getData($query);
	}

	function get_tahun()
	{
		$tahun = array();
		$a = 0;
		for($i=date("Y")-1;$i<date("Y")+2;$i++)
		{
			$thn[] = array("tahun"=>$i);
			$a++;
		}
		echo json_encode(array('total'=>count($thn), 'data'=>$thn));
	}

	function get_kendaraan()
	{
		$query = "select ID_KENDARAAN, NOPOL_VHC from m_kendaraan";
		$this->getData($query);
	}

	function get_sa_id()
	{
		$query = "select ID_SA, NO_SA from m_sa";
		$this->getData($query);
	}

	/*
	
	function get_provinsi()
	{
		$query = "select * from m_provinsi where id_provinsi=32";
		$this->getData($query);
	}

	function get_kabupaten()
	{
		$query = "select * from m_kabupaten where id_provinsi = 32";
		$this->getData($query);
	}

	function get_kecamatan()
	{
		$query = sprintf("select * from m_kecamatan where kode_kabupaten = '%s' and kode_provinsi = 32", $this->session->userdata('KODE_KAB'));
		$this->getData($query);
	}
	function get_desa()
	{
		$query = sprintf("select * from m_desa where kode_kecamatan = '%s' AND kode_kabupaten = '%s' AND kode_provinsi = 32", $this->session->userdata('KODE_KEC'), $this->session->userdata('KODE_KAB'));
		$this->getData($query);
	}
	*/

	function set_sess_kab()
	{
		$this->session->set_userdata('KODE_KAB', $this->input->post('KODE_KAB'));
	}
	
	function set_sess_kec()
	{
		$this->session->set_userdata('KODE_KEC', $this->input->post('KODE_KEC'));		
	}

	function get_tipe_penyalur()
	{
		$query = "select * from m_tipe_penyalur";
		$this->getData($query);		
	}
	
	function get_penyalur(){
		$query = "select ID_PENYALUR, SUB_PENYALUR from m_penyalur";
		$this->getData($query);				
	}
	
	function get_lo(){
		$query = sprintf("
			select distinct(no_lo) as NO_LO
			from t_realisasi_pengambilan_do 
			where BULAN_REALISASI = %d and TAHUN_REALISASI = %d		
		", date("m"), date("Y"));
		$this->getData($query);
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

	function m_spbbe()
	{
		$this->load->view('master_spbbe');
	}
	
	function get_sppbe(){
		$query = "select * from m_spbbe";
		$this->getData($query);		
	}
	
	function del_m_spbbe()
	{
		$this->_del('ID_SPBBE', 'm_spbbe');		
	}
	
	function simpan_m_spbbe()
	{
		$ret = false;
		if($this->input->post('NAMA_SPBBE'))
		{		
			$nid = $this->autoID('ID_SPBBE', 'm_spbbe');
			$where = array("ID_SPBBE" => $this->input->post('ID_SPBBE'));
			if($this->cek_is_ada('m_spbbe',$where))
			{
				if($this->db->update('m_spbbe',$this->input->post(),$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				$arr = $this->input->post();
				if($this->input->post('ID_SPBBE') == '')
				{
					$arr['ID_SPBBE'] = $nid;
				}
				if($this->db->insert('m_spbbe',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";			
	}
	
	function get_desa()
	{
		$query = "
			SELECT 
				tbl_desa.*,
				tbl_kecamatan.kecamatan,
				tbl_kabupaten.kabupaten,
				tbl_provinsi.provinsi
			FROM tbl_desa
			INNER JOIN tbl_kecamatan on tbl_kecamatan.kode_kecamatan = tbl_desa.kode_kec
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab = tbl_desa.kode_kab
			INNER JOIN tbl_provinsi on tbl_provinsi.kode_provinsi = tbl_desa.kode_prov
			WHERE 1
		";
		$this->getData($query, array("tbl_desa.desa_kelurahan", "tbl_kecamatan.kecamatan", "tbl_kabupaten.kabupaten"), "tbl_desa.id_kelurahan");		
	}

	function get_kecamatan()
	{
		$query = "
			SELECT 
				tbl_kecamatan.*,
				tbl_kabupaten.kabupaten,
				tbl_provinsi.provinsi
			FROM tbl_kecamatan
			inner join tbl_kabupaten on tbl_kabupaten.kode_kab = tbl_kecamatan.kode_kab
			INNER JOIN tbl_provinsi on tbl_provinsi.kode_provinsi = tbl_kecamatan.kode_prov
			WHERE 1
		";
		$this->getData($query, array("tbl_kecamatan.kecamatan", "tbl_kabupaten.kabupaten"), "tbl_kecamatan.id_kec");		
	}

	function get_kabupaten()
	{
		$query = "
			SELECT 
				tbl_kabupaten.*,
				tbl_provinsi.provinsi
			FROM tbl_kabupaten
			INNER JOIN tbl_provinsi on tbl_provinsi.kode_provinsi = tbl_kabupaten.kode_prov
			WHERE 1
		";
		$this->getData($query, array("tbl_kabupaten.kabupaten"), "tbl_kabupaten.id_kab");
	}

	function get_penerima()
	{
		$query = "
			SELECT * FROM tbl_temp_penerima
			WHERE 1
		";
		$this->getData($query, array("tbl_temp_penerima.namalengkap"), "tbl_temp_penerima.id_penerima");
	}
	
	function m_desa()
	{
		$this->load->view('master_desa');
	}
	
	function m_kecamatan()
	{
		$this->load->view('master_kec');
	}
	
	function m_kabupaten()
	{
		$this->load->view('master_kab');
	}
	
	function m_penerima()
	{
		$this->load->view('master_penerima');
	}

	function m_perangkat()
	{
		$this->load->view('master_perangkat');
	}
	
	function m_uom()
	{
		$this->load->view('master_uom');
	}

	function m_kendaraan()
	{
		$this->load->view('master_kendaraan');
	}

	function m_supir()
	{
		$this->load->view('master_supir');
	}

	function m_sa_client()
	{
		$this->load->view('master_sa_client');
	}

	function m_sales_office()
	{
		$this->load->view('master_sa_so');
	}

	function m_sales_group()
	{
		$this->load->view('master_sa_sg');
	}
	
	function get_master_data($table){
		$query = sprintf("select * from `%s`", $table);
		$this->getData($query);		
	}

	function simpan_master_data($table,$id,$noempty)
	{
		define('ID', $id);
		$ret = false;		
		if($this->input->post(sprintf('%s', $noempty)))
		{		
			$nid = $this->autoID(sprintf('%s', $id), sprintf('%s', $table));
			$where = array(sprintf("%s", $id) => $this->input->post($id));
			if($this->cek_is_ada(sprintf('%s',$table),$where))
			{
				if($this->db->update(sprintf('%s',$table),$this->input->post(),$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				$arr = $this->input->post();
				if($arr[ID] == '')
				{
					$arr[ID] = $nid;
				}
				if($this->db->insert(sprintf('%s',$table),$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";			
	}
	
	function _del($table,$id)
	{
		if($this->input->post('id'))
		{
			if($this->db->delete(sprintf('%s', $table),sprintf('%s in(%s)', $id, $this->input->post('id'))))
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus.";
		}
	}

	function master_del($table,$id)
	{
		if($this->input->post('id'))
		{
			if($this->db->delete(sprintf('%s', $table),sprintf('%s in(%s)', $id, $this->input->post('id'))))
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus.";
		}
	}
	
	function _dump($data){
		print('<pre>');
		print_r($data);
		print('</pre>');
	}
	
	function autoID($id,$table)
	{
		$sql = sprintf("select max(%s) as maks from %s", $id, $table);
		$q = $this->db->query($sql);
		if($q->num_rows()>0)
		{
			$sid = $q->row_array();
			$nid = $sid['maks'] + 1;			
		} else $nid = 1;
		return $nid;
	}
	
	function cek_is_ada($table, $where)
	{
		$this->db->where($where);
		$this->db->from($table);
		$c = $this->db->count_all_results();
		if($c>0) return true;
			else return false;
	}
	
}