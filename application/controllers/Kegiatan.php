<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		if ( empty( $this->session->userdata('level')) || ( $this->session->userdata('level') == 'user2' ) ) {
			$this->riwayat->add( 4 );
			$this->session->set_flashdata('alert', '<div class="alert alert-danger animated bounceIn"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>Session dilanggar</strong></div>');
			redirect( base_url() );
		}
	}

	public function index()
	{

		$this->load->model('surat_kegiatan');
		$data['kontak'] = $this->surat_kegiatan->tampilKontak();
		$data['sk'] = $this->surat_kegiatan->tampilSuratkegiatan();

		$this->load->view('header');
		$this->load->view('kegiatan/home', $data );
		$this->load->view('footer');
	}

	public function tambah()
	{

		$this->load->model('surat_kegiatan');
		$data['kontak'] = $this->surat_kegiatan->tampilKontak();

		$this->load->view('header');
		$this->load->view('kegiatan/tambah', $data);
		$this->load->view('footer');
	}

	public function edit()
	{
		$id             = $this->uri->segment(3);

		$this->load->model('surat_kegiatan');
		$data['kontak'] = $this->surat_kegiatan->tampilKontak();
		$data['edit']   = $this->surat_kegiatan->tampilEdit($id);

		$this->load->view('header');
		$this->load->view('kegiatan/edit', $data);
		$this->load->view('footer');
	}

	public function add()
	{
		$this->load->model('surat_kegiatan');
		$this->surat_kegiatan->add();

		redirect( base_url().'kegiatan/tambah' );
	}

	public function update()
	{
		$id     = $this->uri->segment(3);

		$this->load->model('surat_kegiatan');
		$this->surat_kegiatan->update($id);

		redirect( base_url().'kegiatan' );
	}

	public function upload()
	{
		$this->load->model('surat_kegiatan');

		$this->load->view('header');
		$this->load->view('kegiatan/upload');
		$this->load->view('footer');
	}

	public function hapusfile()
	{
		$this->load->model('surat_kegiatan');

		$id   = $this->uri->segment(3);
		$file = $this->uri->segment(4);

		$this->surat_kegiatan->hapusfile($id, $file);

		redirect( base_url().'kegiatan' );
	}

	public function hapus()
	{
		$this->load->model('surat_kegiatan');

		$id   = $this->uri->segment(3);
		$file = $this->uri->segment(4);

		$this->surat_kegiatan->hapusfile($id, $file);
		$this->surat_kegiatan->hapusdata($id);

		redirect( base_url().'kegiatan' );
	}

	public function up()
	{
		$this->load->model('surat_kegiatan');

		$id = $this->input->post('id');
		$this->surat_kegiatan->up($id);
		
		redirect( base_url().'kegiatan' );
	}

	public function cari()
	{
		$this->load->model('surat_kegiatan');
		$data['kontak'] = $this->surat_kegiatan->tampilKontak();
		$data['csk'] = $this->surat_kegiatan->cariSuratkegiatan($this->input->get('i'), $this->input->get('k'), $this->input->get('m'), $this->input->get('s'));

		$this->load->view('header');
		$this->load->view('kegiatan/cari', $data );
		$this->load->view('footer');
	}
	

}

/* End of file Surat_kegiatan.php */
/* Location: ./application/controllers/Surat_kegiatan.php */