<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_kegiatan extends CI_Model {
	function __construct()
	{	
		date_default_timezone_set('Asia/Jakarta');
		//error_reporting(0);
	}

	public function no($value)
	{
		$this->session->set_flashdata('alert', '<div class="alert alert-danger animated bounceIn"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>'.$value.'</strong></div>');
	}

	public function ok($value)
	{
		$this->session->set_flashdata('alert', '<div class="alert alert-success animated bounceIn"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>'.$value.'</strong></div>');
	}

	public function tampilKontak()
	{
		return $this->db->get('kontak');
	}

	public function tampilSuratKegiatan()
	{
		$this->db->select('*');
		$this->db->from('surat_kegiatan');
		$this->db->join('kontak', 'kontak.id_kontak = surat_kegiatan.id_kontak_surat_kegiatan');
		$this->db->order_by('surat_kegiatan.tgl_arsip_surat_kegiatan', 'desc');		
		return $this->db->get();

	}

	public function add()
	{

		$kepada     = $this->input->post('kepada');
		$nomor      = $this->input->post('nomor');
		$perihal    = $this->input->post('perihal');
		$tgl_surat  = $this->input->post('tgl_surat');

		$data = array(
        'id_surat_kegiatan' => '',
        'nomor_surat_kegiatan' => $nomor,
        'perihal_surat_kegiatan' => $perihal,
        'id_kontak_surat_kegiatan' => $kepada,
        'tgl_surat_kegiatan' => date('Y-m-d',strtotime($tgl_surat)),
        'tgl_arsip_surat_kegiatan' => date('Y-m-d'),
        'upload_surat_kegiatan' => '' 
        );

		$sql = $this->db->insert('surat_kegiatan', $data);
		if ( !$sql ) 
			{
				$this->no("Gagal menambahkan");
				$this->riwayat->add( 'KI0' );
			}
		else
			{
				$this->ok("Berhasil menambahkan");
				$this->riwayat->add( 'KI1' );
			}
	}

	public function tampilEdit($id)
	{
		$this->db->select('*');
		$this->db->from('surat_kegiatan');
		$this->db->where('id_surat_kegiatan', $id);
		return $this->db->get();
	}

	public function update($id)
	{

		$kepada     = $this->input->post('kepada');
		$nomor      = $this->input->post('nomor');
		$perihal    = $this->input->post('perihal');
		$tgl_surat  = $this->input->post('tgl_surat');

		$data = array(
        'nomor_surat_kegiatan' => $nomor,
        'perihal_surat_kegiatan' => $perihal,
        'id_kontak_surat_kegiatan' => $kepada,
        'tgl_surat_kegiatan' => date('Y-m-d',strtotime($tgl_surat)),
        'tgl_arsip_surat_kegiatan' => date('Y-m-d')
        );

		$this->db->where('id_surat_kegiatan', $id);

		$sql = $this->db->update('surat_kegiatan', $data);
		if ( !$sql ) 
			{
				$this->no("Gagal mengubah");
				$this->riwayat->add( 'KU0' );
			}
		else
			{
				$this->ok("Berhasil mengubah");
				$this->riwayat->add( 'KU1' );
			}


	}

	public function updateFile($id, $file)
	{

		$data = array(
        'upload_surat_kegiatan' => $file 
        );

		$this->db->where('id_surat_kegiatan', $id);
		return $this->db->update('surat_kegiatan', $data);
	}

	public function buatNamaFile($id)
	{
		$this->db->select('surat_kegiatan.id_surat_kegiatan as id, surat_kegiatan.perihal_surat_kegiatan as perihal , kontak.kontak as kontak');
		$this->db->from('surat_kegiatan');
		$this->db->join('kontak', 'kontak.id_kontak = surat_kegiatan.id_kontak_surat_kegiatan', 'left');
		$this->db->where('surat_kegiatan.id_surat_kegiatan', $id);
		$query = $this->db->get();

		foreach ($query->result() as $name )
		 return str_replace(' ', '_', $name->kontak ).'_'.$name->id.'_'.str_replace(' ', '_', $name->perihal ).'.pdf';
	}

	public function hapusfile($id, $file)
	{
		unlink('./arsip_kegiatan/'.$file);

		$sql = $this->updateFile($id, '');

		if ( !$sql ) 
			{
				$this->no("Gagal menghapus file");
				$this->riwayat->add( 'KDF0' );
			}
		else
			{
				$this->ok("Berhasil menghapus file");
				$this->riwayat->add( 'KDF1' );
			}
	}

	public function hapusdata($id)
	{
		$this->db->where('id_surat_kegiatan', $id);

		$sql = $this->db->delete('surat_kegiatan');

		if ( !$sql ) 
			{
				$this->no("Gagal menghapus data surat kegiatan");
				$this->riwayat->add( 'KDD0' );
			}
		else
			{
				$this->ok("Berhasil menghapus data surat kegiatan");
				$this->riwayat->add( 'KDD1' );
			}
	}

	

	public function up($id)
	{
		$config['upload_path']   = './arsip_kegiatan/';
		$config['allowed_types'] = 'pdf';
		$config['max_size']      = '2000000';
		$config['file_name']     = $this->buatNamaFile($id);
		
		$this->load->library('upload', $config);
		
		$sql = $this->upload->do_upload('file');

		if ( !$sql ) 
			{
				$this->no("Gagal mengunggah file");
				$this->riwayat->add( 'KF0' );
			}
		else
			{
				$this->ok("Berhasil mengunggah file");
				$this->riwayat->add( 'KF1' );
				$this->updateFile($id, $config['file_name']); 
			}

	}

	public function cariSuratKegiatan($id = NULL, $kategori = NULL , $mulai = NULL , $sampai = NULL )
	{
		switch ($kategori) {
			case '1':
				$kateg = 'surat_kegiatan.tgl_surat_kegiatan';
				break;
			
			default:
				$kateg = 'surat_kegiatan.tgl_arsip_surat_kegiatan';
				break;
		}

		$this->db->select('*');
		$this->db->from('surat_kegiatan');
		$this->db->join('kontak', 'kontak.id_kontak = surat_kegiatan.id_kontak_surat_kegiatan');
		$this->db->order_by($kateg, 'desc');
		if (!empty($id)) {
			$this->db->where('surat_kegiatan.id_kontak_surat_kegiatan', $id );
			$this->db->where($kateg.' >=', $mulai );
			$this->db->where($kateg.' <=', $sampai );
		}else{
			$this->db->where($kateg.' >=', $mulai );
			$this->db->where($kateg.' <=', $sampai );
		}
		
		return $query = $this->db->get();


	}

	public function cetak( $mulai , $sampai )
	{
		$this->db->select('*');
		$this->db->from('surat_kegiatan');
		$this->db->join('kontak', 'kontak.id_kontak = surat_kegiatan.id_kontak_surat_kegiatan');
		$this->db->order_by('surat_kegiatan.tgl_arsip_surat_kegiatan', 'desc');
		$this->db->where('surat_kegiatan.tgl_arsip_surat_kegiatan >=', $mulai );
		$this->db->where('surat_kegiatan.tgl_arsip_surat_kegiatan <=', $sampai );
		
		
		return $query = $this->db->get();
	}




}

/* End of file sql.php */
/* Location: ./application/models/sql.php */