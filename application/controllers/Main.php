<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Madmin');
		
		$this->load->library('cart');
		$this->load->helper('url');
	}

	public function index()
	{
		$data['produk'] = $this->Madmin->get_produk()->result();
		$data['kategori'] = $this->Madmin->get_all_data('tbl_kategori')->result();
		$this->load->view('home/layout/header',$data);
		$this->load->view('home/layanan');
		$this->load->view('home/home');
		$this->load->view('home/layout/footer');
	}

	public function detail_produk($idProduk)
	{
		$dataWhere = array('idProduk'=>$idProduk);
		$data['produk']=$this->Madmin->get_by_id('tbl_produk',$dataWhere)->row_object();
		$data['kategori']=$this->Madmin->get_all_data('tbl_kategori')->result();
		$this->load->view('home/layout/header',$data);
		$this->load->view('home/detail_produk',$data);
		$this->load->view('home/layout/footer');
	}

	public function add_cart($idProduk)
	{
		if(empty($this->session->userdata('idKonsumen'))){
			echo "<script>alert('Anda harus login dulu untuk add cart');history.back()</script>";
			exit();
		}

		$dataWhere = array('idProduk'=>$idProduk);
		$produk = $this->Madmin->get_by_id('tbl_produk',$dataWhere)->row_object();
		$kota = $this->Madmin->get_kota_penjual($produk->idToko)->row_object();
	

		$this->session->set_userdata('idKotaAsal',$kota->idKota);
		$this->session->set_userdata('idTokoPenjual',$produk->idToko);

		$data = array(
			'id' => $produk->idProduk,
			'qty' => 1,
			'price' => $produk->harga,
			'name' => $produk->namaProduk,
			'image' => $produk->foto
		);

		$this->cart->insert($data);
		redirect("main/cart");
	}

	public function cart()
	{
		if(empty($this->session->userdata('idKonsumen'))){
			echo "<script>alert('Anda harus login dulu untuk add cart');history.back()</script>";
			exit();
		}

		$data['kota_asal'] = $this->session->userdata('idKotaAsal');
		$data['kota_tujuan'] = $this->session->userdata('idKotaTujuan');

		$data['cartItems'] = $this->cart->contents();
		$data['kategori']=$this->Madmin->get_all_data('tbl_kategori')->result();
		$data['total'] = $this->cart->total();

		$this->load->view('home/layout/header',$data);
		$this->load->view('home/cart',$data);
		$this->load->view('home/layout/footer');
	}

	public function delete_cart($rowid)
	{
		$remove = $this->cart->remove($rowid);
		redirect("main/cart");
	}

	public function register()
	{
		$this->load->view('home/layout/header');
		$this->load->view('home/register');
		$this->load->view('home/layout/footer');
	}

	public function getProvince(){
		$curl = curl_init(); 
		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://api.rajaongkir.com/starter/province",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
			"key: fc0f35540f77edb443c7e7b8546fb8f2"
			),
		));
		$response = curl_exec($curl);
		
		$err = curl_error($curl);

		curl_close($curl);
		$data = json_decode($response, true);
		echo "<option value=''>Pilih Provinsi</option>";
		for ($i=0; $i < count($data['rajaongkir']['results']); $i++) { 
		echo "<option value='".$data['rajaongkir']['results'][$i]['province_id']."'>".$data['rajaongkir']['results'][$i]['province']."</option>";
		} 
	}

	public function getCity($province){
		$curl = curl_init(); 
		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://api.rajaongkir.com/starter/city?province=".$province,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
			"key: fc0f35540f77edb443c7e7b8546fb8f2"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		$data = json_decode($response, true);
		echo "<option value=''>Pilih Kota</option>";
		for ($i=0; $i < count($data['rajaongkir']['results']); $i++) { 
		echo "<option value='".$data['rajaongkir']['results'][$i]['city_id']."'>".$data['rajaongkir']['results'][$i]['city_name']."</option>";
		} 
	}



	public function save_reg(){
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$telpon = $this->input->post('telpon');
		$idKota = $this->input->post('city');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$alamat = $this->input->post('alamat');

		$dataInput=array('username'=>$username,'password'=>$password,'idKota'=>$idKota,'namaKonsumen'=>$nama,'alamat'=>$alamat,'email'=>$email,'tlpn'=>$telpon,'statusAktif'=>'Y');
		$this->Madmin->insert('tbl_member', $dataInput);
		echo "OK";
	}

	public function login(){
		$this->load->view('home/layout/header');
		$this->load->view('home/login');
		$this->load->view('home/layout/footer');	
	}

	public function login_member(){
		$this->load->model('Madmin');
		$u= $this->input->post('username');
		$p= $this->input->post('password');
		
		$cek = $this->Madmin->cek_login_member($u, $p)->num_rows();
		$result = $this->Madmin->cek_login_member($u, $p)->row_object();
	
		if($cek==1){ 
			$data_session = array(
				'idKonsumen' => $result->idKonsumen,
				'idKotaTujuan' => $result->idKota,
				'Member' => $u,
				'status' => 'login'
			);
			$this->session->set_userdata($data_session);
			redirect('main/dashboard');
		} else {
			redirect('main/login');
		}
	}

	public function dashboard(){
		$this->load->view('home/layout/header');
		$this->load->view('home/dashboard');
		$this->load->view('home/layout/footer');
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('main/login');
	}

	public function proses_transaksi(){
		$dataWhere = array('idKonsumen'=>$this->session->userdata('idKonsumen'));
		$member = $this->Madmin->get_by_id('tbl_member', $dataWhere)->row_object();

		$kota_asal = $this->session->userdata('idKotaAsal');
		$kota_tujuan = $this->session->userdata('idKotaTujuan');

		$this->load->helper('toko');
		$ongkir = getOngkir($kota_asal,$kota_tujuan,'1000','jne');
		$ongkir_value = $ongkir['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'];
		
		$dataInput=array(
			'idKonsumen'=>$member->idKonsumen,
			'idToko'=>$this->session->userdata('idTokoPenjual'),
			'tglOrder'=>date("Y-m-d"),
			'statusOrder'=>"Belum Bayar",
			'kurir'=>"JNE Oke",
			'ongkir'=>$ongkir_value,

		);
		$this->Madmin->insert('tbl_order', $dataInput);
		$insert_id = $this->db->insert_id();

		$transaction_details = array(
			'order_id' => $insert_id,
			'gross_amount' => $ongkir_value + $this->cart->total(),
		);

		$item_details = [];
		foreach($this->cart->contents() as $item){
			$item_details[] = array(
				'id'	=> $item["id"],
				'price'	=> $item["price"],
				'quantity'	=> $item["qty"],
				'name'	=>	$item["name"]
			);
		}

		$item_details[] = array(
			'id'	=> "ONGKIR",
			'price'	=> $ongkir_value,
			'quantity'	=> 1,
			'name'	=>	"Ongkos Kirim JNE Oke"
		);
	}


}
