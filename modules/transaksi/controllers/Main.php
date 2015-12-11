<?php

class Main extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Mod_pemetaan');
		
		if(!$this->session->userdata('username'))
		{
			header(sprintf("Location: %s", base_url().'index.php/dashboard/Mainindex/Logout'));
		}
		
    }

    public function index() {
        $this->load->view('main');
    }

    function entri_sa()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("entri_sa", $data);
    }

	function get_data_SA()
    {
		$query = "
			select m_sa.*, 
			concat(m_spbbe.kode_spbbe,'-',m_spbbe.nama_spbbe) as NAMA_SPBBE,
			concat(m_sales_office.so_code,'-',m_sales_office.so_name) as SALES_OFFICE,
			concat(m_sales_group.sg_code,'-',m_sales_group.sg_name) as SALES_GROUP,
			m_uom.code_uom_sa as UOM,
			concat(shipto.sa_client_code,'-',shipto.sa_company) as KIRIMKE,
			concat(soldto.sa_client_code,'-',soldto.sa_company) as JUALKE
			from m_sa
			inner join m_spbbe on (m_spbbe.id_spbbe = m_sa.id_spbbe)
			inner join m_sales_office on (m_sales_office.id_so = m_sa.id_so)
			inner join m_sales_group on (m_sales_group.id_sg = m_sa.id_sg)
			inner join m_uom on (m_uom.id_uom = m_sa.uom_sa)
			inner join m_sa_client as shipto on (shipto.id_sa_client = m_sa.ship_to)
			inner join m_sa_client as soldto on (soldto.id_sa_client = m_sa.sold_to)		
			where 1
		";
		$this->getData($query, array('no_sa','material_code'), 'id_sa');
	}

    function get_detail_SA()
    {		
		$query = sprintf('
			SELECT t_sa.*,
			m_sa.NO_SA, m_uom.CODE_UOM_SA as UOM, m_sa.ID_SPBBE,
			CONCAT(m_spbbe.kode_spbbe,"-",m_spbbe.nama_spbbe) as PLANT_SPBBE
			FROM t_sa
			INNER JOIN m_sa ON (m_sa.id_sa = t_sa.id_sa)
			INNER JOIN m_uom ON (m_uom.id_uom = m_sa.uom_sa)
			INNER JOIN m_spbbe ON (m_spbbe.id_spbbe = m_sa.id_spbbe)				
			WHERE t_sa.id_sa = "%d"', $this->session->userdata('ID_SA'));
		$sql = $this->db->query($query);
		if ($sql->num_rows() > 0) {
			$res = $sql->result_array();
			echo json_encode(array('total'=>$sql->num_rows(), 'data'=>$res));
		} else echo json_encode(array('total'=>0, 'data'=>''));
    }
	
    function set_idSA()
    {
		$this->session->set_userdata('ID_SA', $this->input->post('ID_SA'));
    }

    function get_sessidSA()
    {
		return $this->session->userdata('ID_SA');
    }

    function set_idSA_DO()
    {
		$this->session->set_userdata('ID_SA_DO', $this->input->post('ID_SA'));
    }

    function get_sessidSA_DO()
    {
		return $this->session->userdata('ID_SA_DO');
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
	
	function simpan_SA()
	{
		//$this->db->debug = true;
		$ret = false;
		if($this->input->post('NO_SA'))
		{		
			$nid = $this->autoID('ID_SA', 'm_sa');
			$where = array("ID_SA" => $this->input->post('ID_SA'));
			if($this->cek_is_ada('m_sa',$where))
			{
				if($this->db->update('m_sa',$this->input->post(),$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				$arr = $this->input->post();
				if($this->input->post('ID_SA') == '')
				{
					$arr['ID_SA'] = $nid;
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");					
				}
				if($this->db->insert('m_sa',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";	
	}

	function simpan_detailSA()
	{
		//$this->db->debug = true;
		$ret = false;
		$idsa = $this->get_sessidSA();
		if($this->input->post('ALLOC_QTY'))
		{		
			$nid = $this->autoID('ID_T_SA', 't_sa');
			$where = array("ID_T_SA" => $this->input->post('ID_T_SA'));
			if($this->cek_is_ada('t_sa',$where))
			{
				if($this->db->update('t_sa',$this->input->post(),$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				$arr = $this->input->post();
				if($this->input->post('ID_T_SA') == '')
				{
					$tgl = $this->input->post('DATE_TSA');
					$arr['ID_T_SA'] = $nid;
					$arr['ID_SA'] = $idsa;
					$arr['BULAN_SA'] = date("m", strtotime($tgl));;
					$arr['TAHUN_SA'] = date("Y", strtotime($tgl));
				}
				if($this->db->insert('t_sa',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";	
	}
				
	function del_SA()
	{
		$this->_del('ID_SA', 'm_sa');
	}

	function del_SA_DO()
	{
		$this->_del('ID_REALISASI_DO', 't_realisasi_pengambilan_do');
	}
	
	function _del($id, $table)
	{
		if($this->input->post('id'))
		{
			if($this->db->delete(sprintf('%s', $table),sprintf('%s in(%s)', $id, $this->input->post('id'))))
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus.";
		}
	}
		
	function del_detailSA()
	{
		if($this->input->post('id'))
		{
			if($this->db->delete('t_sa',sprintf('ID_T_SA in(%s)', $this->input->post('id'))))
			{
				echo "Data berhasil dihapus.";
			} else echo "Data GAGAL dihapus.";
		}
	}
	
	# SA DO	
	function set_idTSA(){
		$this->session->set_userdata('ID_SA', $this->input->post('ID_SA'));
		$this->session->set_userdata('ID_T_SA', $this->input->post('ID_T_SA'));		
		$this->session->set_userdata('DATE_TSA', $this->input->post('DATE_TSA'));		
		$this->session->set_userdata('ID_SPBBE', $this->input->post('ID_SPBBE'));
	}

	function get_idTSA(){
		return $this->session->userdata('ID_T_SA');
	}
	
	function get_sessTSA()
	{
		return array(
			'ID_SPBBE' => $this->session->userdata('ID_SPBBE'),
			'ID_SA' => $this->session->userdata('ID_SA'),
			'ID_T_SA' => $this->session->userdata('ID_T_SA'),
			'DATE_TSA' => $this->session->userdata('DATE_TSA')
		);
	}
	
	function get_data_SA_DO_old()
	{
		$query = sprintf("SELECT tdo.*, m_sa.no_sa as NO_SA, m_spbbe.ID_SPBBE,
			CONCAT(m_spbbe.kode_spbbe,'-',m_spbbe.nama_spbbe) as NAMA_SPBBE,
			m_kendaraan.NOPOL_VHC
			from t_realisasi_pengambilan_do as tdo
			inner join m_sa on (m_sa.id_sa = tdo.id_sa)
			inner join m_spbbe on (m_spbbe.id_spbbe = m_sa.id_spbbe)			
			inner join m_kendaraan on (m_kendaraan.id_kendaraan = tdo.id_kendaraan)
			inner join m_uom on (m_uom.id_uom = m_sa.id_uom)
			where tdo.id_sa = '%d'", $this->session->userdata('ID_SA'));
		$this->getData($query, array('NO_SA','NAMA_SUPIR','NO_LO'), 'ID_REALISASI_DO');
	}

	function get_data_SA_DO()
	{
		$query = sprintf("
			SELECT tdo.*, m_sa.no_sa as NO_SA, m_spbbe.ID_SPBBE, t_sa.ID_SA,
			CONCAT(m_spbbe.kode_spbbe,'-',m_spbbe.nama_spbbe) as NAMA_SPBBE, 
			m_kendaraan.NOPOL_VHC, m_uom.code_uom_sa as UOM
			FROM 
			t_realisasi_pengambilan_do as tdo 
			LEFT JOIN t_sa on (t_sa.ID_T_SA = tdo.ID_T_SA) 
			LEFT JOIN m_sa on (m_sa.id_sa = t_sa.id_sa) 
			LEFT JOIN m_spbbe on (m_spbbe.id_spbbe = m_sa.id_spbbe) 
			LEFT JOIN m_kendaraan on (m_kendaraan.id_kendaraan = tdo.id_kendaraan) 
			LEFT JOIN  m_uom on (m_uom.id_uom = m_sa.uom_sa) 
			WHERE t_sa.ID_SA = '%d' AND tdo.id_t_sa = '%d'		
			", 
			$this->session->userdata('ID_SA'), $this->session->userdata('ID_T_SA'));
		$this->getData($query, array('NO_SA','NAMA_SUPIR','NO_LO'), 'ID_REALISASI_DO');
	}
	
	function simpan_SA_DO(){
		$ret = false;
		$idsa = $this->get_sessidSA_DO();
		$arr = $this->input->post();
		if($this->input->post('NO_LO'))
		{		
			$nid = $this->autoID('ID_REALISASI_DO', 't_realisasi_pengambilan_do');
			$where = array("ID_REALISASI_DO" => $this->input->post('ID_REALISASI_DO'));
			if($this->cek_is_ada('t_realisasi_pengambilan_do',$where))
			{
				$tgl = $this->session->userdata('DATE_TSA');
				$tsa = $this->get_sessTSA();
				$arr['ID_SPBBE'] = $tsa['ID_SPBBE'];
				$arr['ID_SA'] = $tsa['ID_SA'];
				$arr['TGL_REALIASI'] = $tgl;
				$arr['ID_T_SA'] = $this->get_idTSA();
				$arr['BULAN_REALISASI'] = date("m", strtotime($tgl));;
				$arr['TAHUN_REALISASI'] = date("Y", strtotime($tgl));
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				if($this->db->update('t_realisasi_pengambilan_do',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_REALISASI_DO') == '')
				{
					//$tgl = $this->input->post('TGL_REALIASI');
					$tgl = $this->session->userdata('DATE_TSA');					
					$tsa = $this->get_sessTSA();
					$arr['ID_SPBBE'] = $tsa['ID_SPBBE'];
					$arr['ID_SA'] = $tsa['ID_SA'];
					$arr['TGL_REALIASI'] = $tgl;
					$arr['ID_REALISASI_DO'] = $nid;
					$arr['ID_T_SA'] = $this->get_idTSA();
					$arr['BULAN_REALISASI'] = date("m", strtotime($tgl));;
					$arr['TAHUN_REALISASI'] = date("Y", strtotime($tgl));
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('t_realisasi_pengambilan_do',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";			
	}
	
	# penyalur
	function get_data_penyalur()
	{
		$query = "
			select m_penyalur.*,
			m_provinsi.NAMA_PROVINSI,
			m_kabupaten.NAMA_KABUPATEN,
			m_kecamatan.NAMA_KECAMATAN,
			m_desa.NAMA_DESA,
			m_tipe_penyalur.TIPE_PENYALUR,
			CONCAT(m_sa_client.sa_client_code,' - ',m_sa_client.sa_company) as SP_AGEN
			from m_penyalur
			INNER JOIN m_provinsi on (m_provinsi.id_provinsi = m_penyalur.id_provinsi)
			INNER JOIN m_kabupaten on (m_kabupaten.id_kabupaten = m_penyalur.id_kabupaten)
			INNER JOIN m_kecamatan on (m_kecamatan.id_kecamatan = m_penyalur.id_kecamatan)
			INNER JOIN m_desa on (m_desa.id_desa = m_penyalur.id_desa)
			INNER JOIN m_tipe_penyalur on (m_tipe_penyalur.id_tipe_penyalur = m_penyalur.id_tipe_penyalur)
			INNER JOIN m_sa_client on (m_sa_client.id_sa_client = m_penyalur.id_sp_agen)
			where 1
		";
		$this->getData($query, array('m_penyalur.NAMA_PEMILIK','m_penyalur.SUB_PENYALUR'), 'm_penyalur.ID_PENYALUR');
	}

	function simpan_data_penyalur(){
		$ret = false;
		$arr = $this->input->post();
		if($this->input->post('SUB_PENYALUR') && $this->input->post('NAMA_PEMILIK'))
		{		
			$nid = $this->autoID('ID_PENYALUR', 'm_penyalur');
			$where = array("ID_PENYALUR" => $this->input->post('ID_PENYALUR'));
			if($this->cek_is_ada('m_penyalur',$where))
			{
				$arr['ID_PENYALUR'] = $this->input->post('ID_PENYALUR');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				if($this->db->update('m_penyalur',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_PENYALUR') == '')
				{
					$arr['ID_PENYALUR'] = $nid;
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('m_penyalur',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";					
	}
	
	function del_penyalur(){
		$this->_del("ID_PENYALUR", "m_penyalur");
	}

	function del_deposit_pangkalan(){
		$this->_del("ID_DEPOSIT", "t_deposit_pangkalan");
	}
	
	function get_deposit_pangkalan(){
		$query = "
			select t_deposit_pangkalan.*, m_penyalur.SUB_PENYALUR
			from t_deposit_pangkalan
			inner join m_penyalur on m_penyalur.id_penyalur = t_deposit_pangkalan.id_penyalur
		";
		$this->getData($query, array(), 'ID_DEPOSIT');		
	}
	
	function simpan_deposit_pangkalan(){
		$ret = false;
		$arr = $this->input->post();
		if($this->input->post('ID_PENYALUR'))
		{		
			$nid = $this->autoID('ID_DEPOSIT', 't_deposit_pangkalan');
			$where = array("ID_DEPOSIT" => $this->input->post('ID_DEPOSIT'));
			if($this->cek_is_ada('t_deposit_pangkalan',$where))
			{
				$arr['ID_DEPOSIT'] = $this->input->post('ID_DEPOSIT');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				if($this->db->update('t_deposit_pangkalan',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_DEPOSIT') == '')
				{
					$arr['ID_DEPOSIT'] = $nid;
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('t_deposit_pangkalan',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";
	}
	
	# penjualan
	function simpan_data_penjualan(){
		$ret = false;
		$arr = $this->input->post();
		if($this->input->post('JL_LO'))
		{		
			$nid = $this->autoID('ID_LAP_PENJUALAN', 't_lap_penjualan');
			$where = array("ID_LAP_PENJUALAN" => $this->input->post('ID_LAP_PENJUALAN'));
			if($this->cek_is_ada('t_lap_penjualan',$where))
			{
				$arr['ID_LAP_PENJUALAN'] = $this->input->post('ID_LAP_PENJUALAN');
				$arr['JL_JUMLAH'] = $this->input->post('JL_ISI') * $this->input->post('JL_HARGA');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				$tgl = $this->input->post('JL_TGL');
				$arr['JL_BULAN'] = date("m", strtotime($tgl));;
				$arr['JL_TAHUN'] = date("Y", strtotime($tgl));
				if($this->db->update('t_lap_penjualan',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_LAP_PENJUALAN') == '')
				{
					$tgl = $this->input->post('JL_TGL');
					$arr['JL_BULAN'] = date("m", strtotime($tgl));;
					$arr['JL_TAHUN'] = date("Y", strtotime($tgl));
					$arr['ID_LAP_PENJUALAN'] = $nid;
					$arr['JL_JUMLAH'] = $this->input->post('JL_ISI') * $this->input->post('JL_HARGA');
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('t_lap_penjualan',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";		
	}
	
	function get_data_penjualan(){
		$query = "
		SELECT 
		t_lap_penjualan.*, m_uom.code_uom_sa as UOM,
		CONCAT( m_kendaraan.NOPOL_VHC,' - ', m_kendaraan.JENIS_KENDARAAN) as NAMA_KENDARAAN,
		m_penyalur.SUB_PENYALUR as NAMA_PENYALUR, m_driver.NAMA_DRIVER
		FROM t_lap_penjualan
		INNER JOIN m_uom on (m_uom.id_uom = t_lap_penjualan.id_uom)
		INNER JOIN m_penyalur on (m_penyalur.id_penyalur = t_lap_penjualan.id_penyalur)
		INNER JOIN m_kendaraan on (m_kendaraan.id_kendaraan = t_lap_penjualan.id_kendaraan)
		INNER JOIN m_driver on (m_driver.id_driver = t_lap_penjualan.jl_driver)
		where 1
		";
		$this->getData($query, array('m_penyalur.sub_penyalur', 't_lap_penjualan.JL_LO', 'm_kendaraan.NOPOL_VHC'), 'ID_LAP_PENJUALAN');
	}

	function del_data_penjualan(){
		$this->_del("ID_LAP_PENJUALAN", "t_lap_penjualan");		
	}

	function get_pinjaman_tabung(){
		$query = "
			SELECT 
			t_rekap_pinjaman_tabung.*, m_uom.code_uom_sa as UOM,
			m_penyalur.SUB_PENYALUR as NAMA_PENYALUR, m_driver.NAMA_DRIVER
			FROM t_rekap_pinjaman_tabung
			LEFT JOIN m_uom on (m_uom.id_uom = t_rekap_pinjaman_tabung.id_uom)
			LEFT JOIN m_penyalur on (m_penyalur.id_penyalur = t_rekap_pinjaman_tabung.id_penyalur)
			LEFT JOIN m_driver on (m_driver.id_driver = t_rekap_pinjaman_tabung.ID_DRIVER)
			WHERE 1
		";
		$this->getData($query, array('m_penyalur.SUB_PENYALUR', 'm_driver.NAMA_DRIVER'), 'ID_REKAP_PINJAM_TABUNG');		
	}
	
	function simpan_pinjaman_tabung(){
		$ret = false;
		$arr = $this->input->post();
		if($this->input->post('JML_PINJAM'))
		{		
			$nid = $this->autoID('ID_REKAP_PINJAM_TABUNG', 't_rekap_pinjaman_tabung');
			$where = array("ID_REKAP_PINJAM_TABUNG" => $this->input->post('ID_REKAP_PINJAM_TABUNG'));
			if($this->cek_is_ada('t_rekap_pinjaman_tabung',$where))
			{
				$arr['ID_REKAP_PINJAM_TABUNG'] = $this->input->post('ID_REKAP_PINJAM_TABUNG');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				$tgl = $this->input->post('JL_TGL');
				if($this->db->update('t_rekap_pinjaman_tabung',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_REKAP_PINJAM_TABUNG') == '')
				{
					$arr['ID_REKAP_PINJAM_TABUNG'] = $nid;
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('t_rekap_pinjaman_tabung',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";		
	}

	function del_pinjam_tabung(){
		$this->_del("ID_REKAP_PINJAM_TABUNG", "t_rekap_pinjaman_tabung");
	}

	function set_sess_pinjam_tabung(){
		$this->session->set_userdata('ID_PINJAM_TABUNG', $this->input->post('ID_PINJAM_TABUNG'));
		$this->session->set_userdata('ID_PENYALUR', $this->input->post('ID_PENYALUR'));		
		$this->session->set_userdata('ID_DRIVER', $this->input->post('ID_DRIVER'));		
		$this->session->set_userdata('TGL_PINJAM', $this->input->post('TGL_PINJAM'));		
	}

	function get_data_pengembalian_tabung(){
		$query = "
			SELECT 
			t_pengembalian_tabung.*, m_uom.code_uom_sa as UOM,
			m_penyalur.SUB_PENYALUR as NAMA_PENYALUR, m_driver.NAMA_DRIVER
			FROM t_pengembalian_tabung
			INNER JOIN t_rekap_pinjaman_tabung on (t_rekap_pinjaman_tabung.ID_REKAP_PINJAM_TABUNG=t_pengembalian_tabung.ID_PINJAM_TABUNG)
			LEFT JOIN m_uom on (m_uom.id_uom = t_rekap_pinjaman_tabung.id_uom)
			LEFT JOIN m_penyalur on (m_penyalur.id_penyalur = t_pengembalian_tabung.id_penyalur)
			LEFT JOIN m_driver on (m_driver.id_driver = t_pengembalian_tabung.ID_DRIVER)
			WHERE 1		
		";
		$this->getData($query, array('m_penyalur.SUB_PENYALUR', 'm_driver.NAMA_DRIVER'), 'ID_PENGEMBALIAN_TABUNG');
	}
	
	function simpan_pengembalian_tabung(){
		$ret = false;
		$arr = $this->input->post();
		if($this->input->post('JML_KEMBALI'))
		{		
			$nid = $this->autoID('ID_PENGEMBALIAN_TABUNG', 't_pengembalian_tabung');
			$where = array("ID_PENGEMBALIAN_TABUNG" => $this->input->post('ID_PENGEMBALIAN_TABUNG'));
			if($this->cek_is_ada('t_pengembalian_tabung',$where))
			{
				$arr['ID_PENGEMBALIAN_TABUNG'] = $this->input->post('ID_PENGEMBALIAN_TABUNG');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				$tgl = $this->input->post('TGL_PENGEMBALIAN_TABUNG');
				if($this->db->update('t_pengembalian_tabung',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_PENGEMBALIAN_TABUNG') == '')
				{
					$arr['ID_PENGEMBALIAN_TABUNG'] = $nid;
					$arr['ID_PINJAM_TABUNG'] = $this->session->userdata('ID_PINJAM_TABUNG');
					$arr['ID_PENYALUR'] = $this->session->userdata('ID_PENYALUR');
					$arr['ID_DRIVER'] = $this->session->userdata('ID_DRIVER');
					$arr['TGL_PINJAM'] = $this->session->userdata('TGL_PINJAM');
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
				} 
				if($this->db->insert('t_pengembalian_tabung',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";
	}

	function del_kembali_tabung(){
		$this->_del("ID_PENGEMBALIAN_TABUNG", "t_pengembalian_tabung");		
	}
		
		
	function get_data_financial_record(){
		$query = "
			SELECT t_fir_agen.*,
			m_penyalur.SUB_PENYALUR as NAMA_PENYALUR
			FROM t_fir_agen
			LEFT JOIN m_penyalur on (m_penyalur.id_penyalur = t_fir_agen.id_penyalur)
			WHERE 1		
		";
		$this->getData($query, array('m_penyalur.SUB_PENYALUR'), 'ID_FIR_AGEN');		
	}
	
	function simpan_financial_record(){
		$ret = false;
		$arr = $this->input->post();
		$tgl = $this->input->post('TGL_FIR');
		if($this->input->post('TGL_FIR'))
		{		
			$nid = $this->autoID('ID_FIR_AGEN', 't_fir_agen');
			$where = array("ID_FIR_AGEN" => $this->input->post('ID_FIR_AGEN'));
			if($this->cek_is_ada('t_fir_agen',$where))
			{
				$arr['ID_FIR_AGEN'] = $this->input->post('ID_FIR_AGEN');
				$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");									
				$arr['FIR_BULAN'] = date("m", strtotime($tgl));;
				$arr['FIR_TAHUN'] = date("Y", strtotime($tgl));
				$arr['JML_HARGA'] = $this->input->post('FIR_HARGA_PCS') * $this->input->post('FIR_VOL');
				$arr['FIR_SALDO'] = ($arr['FIR_BANK'] + $arr['FIR_CASH']) - $arr['JML_HARGA'];
				if($this->db->update('t_fir_agen',$arr,$where))
				{
					$ret = true;
				} else $ret= false;
			} else 
			{				
				if($this->input->post('ID_FIR_AGEN') == '')
				{
					$arr['ID_FIR_AGEN'] = $nid;
					$arr['FIR_BULAN'] = date("m", strtotime($tgl));;
					$arr['FIR_TAHUN'] = date("Y", strtotime($tgl));					
					$arr['TGL_ENTRI'] = date("Y-m-d H:i:s");
					$arr['JML_HARGA'] = $this->input->post('FIR_HARGA_PCS') * $this->input->post('FIR_VOL');
					$arr['FIR_SALDO'] = ($arr['FIR_BANK'] + $arr['FIR_CASH']) - $arr['JML_HARGA'];
				} 
				if($this->db->insert('t_fir_agen',$arr))
				{
					$ret = true;
				} else $ret = false;
			} 
		}
		if($ret) echo "Data BERHASIL disimpan.";
			else echo "Data GAGAL disimpan.";
	}
	
	function hapus_financial_record(){
		$this->_del("ID_FIR_AGEN", "t_fir_agen");			
	}
		
	function getData($query, $w, $orderby) {
        $start = $this->input->get('start') ? $this->input->get('start') : 0;
        $limit = $this->input->get('limit') ? $this->input->get('limit') : 100;
        $total = $this->db->query($query)->num_rows();
		
		$where = " AND ";		
		$hitung = 0;		
		for($c=0; $c < count($w); $c++)
		{		
			$where .= $w[$c];
			$where .= " like '%". $this->input->get('query')."%'";
			if($hitung == (count($where) - 1)) $where .= " OR ";
				else $where .= " ";
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
	
    function dump($data)
    {
        print("<pre>");
        print_r($data);
        print("</pre>");
    }
    
    function entri_realisasi_do()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("realisasi_do", $data);
    }

    function data_agen()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("data_agen", $data);
    }

    function deposit_pangkalan()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("deposit_pangkalan", $data);
    }

    function pengiriman_do()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("pengiriman_do_agen", $data);
    }

    function pinjaman_tabung()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("pinjaman_tabung", $data);
    }

    function financial_record()
    {
        //$this->_dump($this->session->userdata);
        $data = array('kode_instansi'=>$this->session->userdata('kode_instansi'), 'nama_instansi'=>$this->session->userdata('nama_instansi'));		
        $this->load->view("financial_record", $data);
    }
				
	function backupdb()
	{
		$this->load->view('backupdb');
	}
	
	function download_sql()
	{
		$this->load->dbutil();
		$backup =& $this->dbutil->backup(); 
		$this->load->helper('file');
		$fn = sprintf("backup-anjab-%s.gz", date('dmYHis'));
		$file = sprintf('C:\\xampp\\tmp\\%s',$fn);
		write_file($file, $backup); 
		$this->load->helper('download');
		force_download($fn, $backup);	
	}
	
	function cek_is_ada($table, $where)
	{
		$this->db->where($where);
		$this->db->from($table);
		$c = $this->db->count_all_results();
		if($c>0) return true;
			else return false;
	}
	
	function import_db()
	{
		echo "Under Construction";
	}

	function export_xls()
	{
	}
		
	function optimize_tbl()
	{
		if($this->input->post('action'))
		{
			$this->load->dbutil();
			$result = $this->dbutil->optimize_database();
			if ($result !== FALSE)
			{
				//$this->load->view('backupdb', array('status'=>$result));
				echo "Data berhasil dioptimisasi.";
			} else echo $this->_dump($result);
		}
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