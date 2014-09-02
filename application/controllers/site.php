<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Site extends CI_Controller 
{
	public function __construct( )
	{
		parent::__construct();
		
		$this->is_logged_in();
	}
	function is_logged_in( )
	{
		$is_logged_in = $this->session->userdata( 'logged_in' );
		if ( $is_logged_in !== 'true' || !isset( $is_logged_in ) ) {
			redirect( base_url() . 'index.php/login', 'refresh' );
		} //$is_logged_in !== 'true' || !isset( $is_logged_in )
	}
	function checkaccess($access)
	{
		$accesslevel=$this->session->userdata('accesslevel');
		if(!in_array($accesslevel,$access))
			redirect( base_url() . 'index.php/site?alerterror=You do not have access to this page. ', 'refresh' );
	}
	public function index()
	{
		//$access = array("1","2");
		$access = array("1","2");
		$this->checkaccess($access);
		$data[ 'page' ] = 'dashboard';
		$data[ 'title' ] = 'Welcome';
		$this->load->view( 'template', $data );	
	}
	public function createuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data[ 'page' ] = 'createuser';
		$data[ 'title' ] = 'Create User';
		$this->load->view( 'template', $data );	
	}
	function createusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('firstname','First Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('lastname','Last Name','trim|max_length[30]');
		$this->form_validation->set_rules('password','Password','trim|required|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|required|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[user.email]');
		$this->form_validation->set_rules('contact','contactno','trim');
		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
		$this->form_validation->set_rules('description','Description','trim|');
		$this->form_validation->set_rules('address','Address','trim|');
		$this->form_validation->set_rules('city','City','trim|max_length[30]');
		$this->form_validation->set_rules('pincode','Pincode','trim|max_length[20]');
		$this->form_validation->set_rules('facebookuserid','facebookuserid','trim|max_length[20]');
		
		$this->form_validation->set_rules('email','Email','trim|valid_email');
		$this->form_validation->set_rules('status','Status','trim');
		$this->form_validation->set_rules('dob','DOB','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
			$data['page']='createuser';
			$data['title']='Create New User';
			$this->load->view('template',$data);
		}
		else
		{
            $website=$this->input->post('website');
            $description=$this->input->post('description');
            $address=$this->input->post('address');
            $city=$this->input->post('city');
            $pincode=$this->input->post('pincode');
			$password=$this->input->post('password');
			if($dob != "")
			{
				$dob = date("Y-m-d",strtotime($dob));
			}
			$accesslevel=$this->input->post('accesslevel');
			$email=$this->input->post('email');
			$contact=$this->input->post('contact');
			$status=$this->input->post('status');
			$facebookuserid=$this->input->post('facebookuserid');
			$firstname=$this->input->post('firstname');
			$lastname=$this->input->post('lastname');
			if($this->user_model->create($firstname,$lastname,$dob,$password,$accesslevel,$email,$contact,$status,$facebookuserid,$website,$description,$address,$city,$pincode)==0)
			$data['alerterror']="New user could not be created.";
			else
			$data['alertsuccess']="User created Successfully.";
			
			$data['table']=$this->user_model->viewusers();
			$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function viewusers()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->user_model->viewusers();
		$data['page']='viewusers';
		$data['title']='View Users';
		$this->load->view('template',$data);
	}
    
    function viewsponsor()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->sponsor_model->viewall();
		$data['page']='viewsponsor';
		$data['title']='View Sponsor';
		$this->load->view('template',$data);
	}
	function viewuserinterestevents()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['table']=$this->user_model->userinterestevents($this->input->get('id'));
		$data['page']='viewuserinterestevents';
		$data['page2']='block/userblock';
		$data['title']='View User Interest Events';
		$this->load->view('template',$data);
	}
	function edituser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['page']='edituser';
		$data['page2']='block/userblock';
		$data['title']='Edit User';
		$this->load->view('template',$data);
	}
	function editusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('password','Password','trim|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[user.email]');
		$this->form_validation->set_rules('contact','contactno','trim');
		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
		$this->form_validation->set_rules('description','Description','trim|');
		$this->form_validation->set_rules('address','Address','trim|');
		$this->form_validation->set_rules('city','City','trim|max_length[30]');
		$this->form_validation->set_rules('pincode','Pincode','trim|max_length[20]');
        
		$this->form_validation->set_rules('fname','First Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('lname','Last Name','trim|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|valid_email');
		$this->form_validation->set_rules('status','Status','trim');
		$this->form_validation->set_rules('dob','DOB','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
			$data['before']=$this->user_model->beforeedit($this->input->post('id'));
			$data['page']='edituser';
			$data['page2']='block/userblock';
			$data['title']='Edit User';
			$this->load->view('template',$data);
		}
		else
		{
            $website=$this->input->post('website');
            $description=$this->input->post('description');
            $address=$this->input->post('address');
            $city=$this->input->post('city');
            $pincode=$this->input->post('pincode');
			$id=$this->input->post('id');
			$password=$this->input->post('password');
			$dob=$this->input->post('dob');
			if($dob != "")
			{
				$dob = date("Y-m-d",strtotime($dob));
			}
			$accesslevel=$this->input->post('accesslevel');
			$contact=$this->input->post('contact');
			$status=$this->input->post('status');
			$facebookuserid=$this->input->post('facebookuserid');
			$fname=$this->input->post('fname');
			$lname=$this->input->post('lname');
			if($this->user_model->edit($id,$fname,$lname,$dob,$password,$accesslevel,$contact,$status,$facebookuserid,$website,$description,$address,$city,$pincodes)==0)
			$data['alerterror']="User Editing was unsuccesful";
			else
			$data['alertsuccess']="User edited Successfully.";
			
			$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	function editaddress()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['page']='editaddress';
		$data['page2']='block/userblock';
		$data['title']='Edit User';
		$this->load->view('template',$data);
	}
	function editaddresssubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('address','address','trim');
		$this->form_validation->set_rules('facebookuserid','facebookuserid','trim');
		$this->form_validation->set_rules('city','city','trim');
		$this->form_validation->set_rules('pincode','pincode','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
			$data['before']=$this->user_model->beforeedit($this->input->post('id'));
			$data['page']='editaddress';
			$data['page2']='block/userblock';
			$data['title']='Edit User';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$address=$this->input->post('address');
			$city=$this->input->post('city');
			$pincode=$this->input->post('pincode');
			if($this->user_model->editaddress($id,$address,$city,$pincode)==0)
			$data['alerterror']="User Editing was unsuccesful";
			else
			$data['alertsuccess']="User edited Successfully.";
			$data['table']=$this->user_model->viewusers();
			$data['redirect']="site/editaddress?id=".$id;
			//$data['other']="template=$template";
			$this->load->view("redirect2",$data);
			
		}
	}
	function deleteuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->deleteuser($this->input->get('id'));
		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="User Deleted Successfully";
		$data['page']='viewusers';
		$data['title']='View Users';
		$this->load->view('template',$data);
	}
	function changeuserstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->changestatus($this->input->get('id'));
		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="Status Changed Successfully";
		$data['redirect']="site/viewusers";
        $data['other']="template=$template";
        $this->load->view("redirect",$data);
	}
    
    function changesponsorstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->sponsor_model->changestatus($this->input->get('user'),$this->input->get('event'));
		$data['table']=$this->sponsor_model->viewall();
		$data['alertsuccess']="Status Changed Successfully";
        
		$data['redirect']="site/viewsponsor";
        
        $this->load->view("redirect",$data);
	}
    
    
    /*-----------------User/Organizer Finctions added by avinash for frontend APIs---------------*/
    public function update()
	{
        $id=$this->input->get('id');
        $firstname=$this->input->get('firstname');
        $lastname=$this->input->get('lastname');
        $password=$this->input->get('password');
        $password=md5($password);
        $email=$this->input->get('email');
        $website=$this->input->get('website');
        $description=$this->input->get('description');
        $eventinfo=$this->input->get('eventinfo');
        $contact=$this->input->get('contact');
        $address=$this->input->get('address');
        $city=$this->input->get('city');
        $pincode=$this->input->get('pincode');
        $dob=$this->input->get('dob');
       // $accesslevel=$this->input->get('accesslevel');
        $accesslevel=2;
        $timestamp=$this->input->get('timestamp');
        $facebookuserid=$this->input->get('facebookuserid');
        $newsletterstatus=$this->input->get('newsletterstatus');
        $status=$this->input->get('status');
        $logo=$this->input->get('logo');
        $showwebsite=$this->input->get('showwebsite');
        $eventsheld=$this->input->get('eventsheld');
        $topeventlocation=$this->input->get('topeventlocation');
        $data['json']=$this->user_model->update($id,$firstname,$lastname,$password,$email,$website,$description,$eventinfo,$contact,$address,$city,$pincode,$dob,$accesslevel,$timestamp,$facebookuserid,$newsletterstatus,$status,$logo,$showwebsite,$eventsheld,$topeventlocation);
        print_r($data);
		//$this->load->view('json',$data);
	}
	public function finduser()
	{
        $data['json']=$this->user_model->viewall();
        print_r($data);
		//$this->load->view('json',$data);
	}
    public function findoneuser()
	{
        $id=$this->input->get('id');
        $data['json']=$this->user_model->viewone($id);
        print_r($data);
		//$this->load->view('json',$data);
	}
    public function deleteoneuser()
	{
        $id=$this->input->get('id');
        $data['json']=$this->user_model->deleteone($id);
		//$this->load->view('json',$data);
	}
    public function login()
    {
        $email=$this->input->get("email");
        $password=$this->input->get("password");
        $data['json']=$this->user_model->login($email,$password);
        //$this->load->view('json',$data);
    }
    public function authenticate()
    {
        $data['json']=$this->user_model->authenticate();
        //$this->load->view('json',$data);
    }
    public function signup()
    {
        $email=$this->input->get_post("email");
        $password=$this->input->get_post("password");
        $data['json']=$this->user_model->signup($email,$password);
        //$this->load->view('json',$data);
        
    }
    public function logout()
    {
        $this->session->sess_destroy();
        $data['json']=true;
        //$this->load->view('json',$data);
    }
    
    
    
    /*-----------------End of User/Organizer functions----------------------------------*/
    
    
    
	//category
    
	function viewcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->category_model->viewcategory();
		$data['page']='viewcategory';
		$data['title']='View category';
		$this->load->view('template',$data);
	}
	function viewsubcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		//$data['table']=$this->category_model->viewsubcategory();
        $brandid=$this->input->get('brandid');
        $categoryid=$this->input->get('id');
        $data['check']=$this->category_model->selectedcategory($brandid,$categoryid);
        $data['brandcategoryid']=$this->category_model->getbrandcategoryid($brandid,$categoryid);
		$data['page']='viewsubcategory';
		$data['title']='View Sub-category';
		$this->load->view('template',$data);
	}
     function editsubcategorysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('brandcategoryid','brandcategoryid','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$brandid=$this->input->get('brandid');
        $categoryid=$this->input->get('id');
        $data['check']=$this->category_model->selectedcategory($brandid,$categoryid);
        $data['brandcategoryid']=$this->category_model->getbrandcategoryid($brandid,$categoryid);
		$data['page']='viewsubcategory';
		$data['title']='View Sub-category';
		$this->load->view('template',$data);
		}
		else
		{
			$brandcategoryid=$this->input->post('brandcategoryid');
			$men=$this->input->post('men');
			$women=$this->input->post('women');
			$kids=$this->input->post('kids');
            echo "men=".$men;
            if($men=="1")
               {
                $this->category_model->editsubcategorysubmit($brandcategoryid,$men);
                
               }
               else
               {
                   echo "else";
               $this->category_model->deletesubcategorysubmit($brandcategoryid,1);
               }
               
            if($women=="2")
               {
                $this->category_model->editsubcategorysubmit($brandcategoryid,$women);
               }
               else
               {
               $this->category_model->deletesubcategorysubmit($brandcategoryid,2);
               }
            if($kids=="3")
               {
                $this->category_model->editsubcategorysubmit($brandcategoryid,$kids);
               }
               else
               {
               $this->category_model->deletesubcategorysubmit($brandcategoryid,3);
               }
			$brandid=$this->input->get('brandid');
        $categoryid=$this->input->get('id');
        $data['check']=$this->category_model->selectedcategory($brandid,$categoryid);
        $data['brandcategoryid']=$this->category_model->getbrandcategoryid($brandid,$categoryid);
		$data['page']='viewsubcategory';
		$data['title']='View Sub-category';
		$this->load->view('template',$data);
			//$data['other']="template=$template";
			//$this->load->view("redirect",$data);
			/*$data['page']='viewusers';
			$data['title']='View Users';
			$this->load->view('template',$data);*/
		}
	}
	public function createcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->category_model->getstatusdropdown();
		$data['category']=$this->category_model->getcategorydropdown();
		$data[ 'page' ] = 'createcategory';
		$data[ 'title' ] = 'Create category';
		$this->load->view( 'template', $data );	
	}
    public function createbrandcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->category_model->getstatusdropdown();
		$data['category']=$this->category_model->getcategorydropdown();
		$data[ 'page' ] = 'createbrandcategory';
		$data[ 'title' ] = 'Create Brand category';
		$this->load->view( 'template', $data );	
	}
	function createcategorysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('parent','parent','trim|');
		$this->form_validation->set_rules('status','status','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->category_model->getstatusdropdown();
			$data['category']=$this->category_model->getcategorydropdown();
			$data[ 'page' ] = 'createcategory';
			$data[ 'title' ] = 'Create category';
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$parent=$this->input->post('parent');
			$status=$this->input->post('status');
			if($this->category_model->createcategory($name,$parent,$status)==0)
			$data['alerterror']="New category could not be created.";
			else
			$data['alertsuccess']="category  created Successfully.";
			$data['table']=$this->category_model->viewcategory();
			$data['redirect']="site/viewcategory";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    function createbrandcategorysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		//$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('brandid','Brandid','trim|');
		$this->form_validation->set_rules('category','Category','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->category_model->getstatusdropdown();
			$data['category']=$this->category_model->getcategorydropdown();
			$data[ 'page' ] = 'createcategory';
			$data[ 'title' ] = 'Create category';
			$this->load->view('template',$data);
		}
		else
		{
			$brandid=$this->input->get_post('brandid');
			$categoryid=$this->input->post('category');
			$parent=$this->input->post('parent');
			$status=$this->input->post('status');
			if($this->category_model->createbrandcategory($brandid,$categoryid)==0)
			$data['alerterror']="New Brand category could not be created.";
			else
			$data['alertsuccess']="Brand category  created Successfully.";
			$data['table']=$this->category_model->viewonebrandcategories($brandid);
			$data['redirect']="site/viewonebrandcategories?brandid=".$brandid;
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function viewonebrandcategories()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->category_model->viewonebrandcategories($this->input->get('brandid'));
		$data['tablemain']=$this->category_model->viewmaincategory();
		$data['hastypes']=$this->category_model->viewcategorytypes();
		$data['subcategory']=$this->category_model->viewallsubcategory();
        $data['category']=$this->brand_model->getcategory();
		$data['page']='viewonebrandcategories';
		$data['title']='View category';
		$this->load->view('template',$data);
	}
	function editcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->category_model->beforeeditcategory($this->input->get('id'));
		$data['category']=$this->category_model->getcategorydropdown();
		$data[ 'status' ] =$this->category_model->getstatusdropdown();
		$data['page']='editcategory';
		$data['title']='Edit category';
		$this->load->view('template',$data);
	}
	function editcategorysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('parent','parent','trim|');
		$this->form_validation->set_rules('status','status','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->category_model->getstatusdropdown();
			$data['category']=$this->category_model->getcategorydropdown();
			$data['before']=$this->category_model->beforeeditcategory($this->input->post('id'));
			$data['page']='editcategory';
			$data['title']='Edit category';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$name=$this->input->post('name');
			$parent=$this->input->post('parent');
			$status=$this->input->post('status');
			
			if($this->category_model->editcategory($id,$name,$parent,$status)==0)
			$data['alerterror']="category Editing was unsuccesful";
			else
			$data['alertsuccess']="category edited Successfully.";
			$data['table']=$this->category_model->viewcategory();
			$data['redirect']="site/viewcategory";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			/*$data['page']='viewusers';
			$data['title']='View Users';
			$this->load->view('template',$data);*/
		}
	}
   
	function deletecategory()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->category_model->deletecategory($this->input->get('id'));
		$data['table']=$this->category_model->viewcategory();
		$data['alertsuccess']="category Deleted Successfully";
		$data['page']='viewcategory';
		$data['title']='View category';
		$this->load->view('template',$data);
	}
	
	//topic
    //Offer
	public function createoffer()
	{
		$access = array("1");
		$this->checkaccess($access);
		//$data[ 'status' ] =$this->user_model->getstatusdropdown();
		//$data['topic']=$this->topic_model->gettopicdropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
		$data[ 'page' ] = 'createoffer';
		$data[ 'title' ] = 'Create offer';
		$this->load->view( 'template', $data );	
	}
	function createoffersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
        print_r($_POST);
		$this->form_validation->set_rules('header','header','trim|required');
		$this->form_validation->set_rules('description','description','trim|');
		$this->form_validation->set_rules('from','From','trim');
		$this->form_validation->set_rules('to','To','trim');
		$this->form_validation->set_rules('brand','Brand','trim');
		$this->form_validation->set_rules('storeid','storeid','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data[ 'status' ] =$this->user_model->getstatusdropdown();
//			$data['offer']=$this->offer_model->getofferdropdown();
			$data[ 'page' ] = 'createoffer';
			$data[ 'title' ] = 'Create offer';
			$this->load->view('template',$data);
		}
		else
		{
			$header=$this->input->post('header');
			$description=$this->input->post('description');
			$from=$this->input->post('from');
			$to=$this->input->post('to');
			$brand=$this->input->post('brand');
			$storeid=$this->input->post('storeid');
            if($from != "")
			{
				$from = date("Y-m-d",strtotime($from));
			}
            if($to != "")
			{
				$to = date("Y-m-d",strtotime($to));
			}
			if($this->offer_model->createoffer($header,$description,$from,$to,$brand,$storeid)==0)
			$data['alerterror']="New offer could not be created.";
			else
			$data['alertsuccess']="offer  created Successfully.";
			$data['table']=$this->offer_model->viewoffer();
			$data['redirect']="site/viewoffer";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
    //image gallery
     function viewgallery()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->imagegallery_model->viewgallery();
		$data['page']='viewgallery';
		$data['title']='View gallery';
		$this->load->view('template',$data);
	}     
    
	public function creategallery()
	{
		$access = array("1");
		$this->checkaccess($access);
		//$data[ 'status' ] =$this->user_model->getstatusdropdown();
		//$data['topic']=$this->topic_model->gettopicdropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
		$data[ 'page' ] = 'creategallery';
		$data[ 'title' ] = 'Create Gallery';
		$this->load->view( 'template', $data );	
	}
    function creategallerysubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		//$this->form_validation->set_rules('image','Image','trim|required');
		$this->form_validation->set_rules('description','Description','trim|required');
		$this->form_validation->set_rules('brand','brand','trim');
		$this->form_validation->set_rules('storeid','storeid','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			
            $data['brand']=$this->brand_model->getbranddropdown();
            $data[ 'page' ] = 'creategallery';
            $data[ 'title' ] = 'Create Gallery';
            $this->load->view( 'template', $data );	
		}
		else
		{
			//$image=$this->input->post('image');
			$brand=$this->input->post('brand');
			$description=$this->input->post('description');
			$storeid=$this->input->post('storeid');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$logo="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
			}
			if($this->imagegallery_model->create($image,$description,$brand,$storeid)==0)
			$data['alerterror']="New Image in gallery could not be created.";
			else
			$data['alertsuccess']="Image in gallery created Successfully.";
			
			$data['table']=$this->imagegallery_model->viewgallery();
			$data['redirect']="site/viewgallery";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}

    
	function editgallery()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->imagegallery_model->beforeedit($this->input->get('id'));
        $data['store']=$this->store_model->getstorebybrand($data['before']->brandid);
        $data['brand']=$this->brand_model->getbranddropdown();
		$data['page']='editgallery';
		$data['title']='Edit Gallery';
		$this->load->view('template',$data);
	}
    
    function editgallerysubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
        
		$this->form_validation->set_rules('description','Description','trim|required');
		$this->form_validation->set_rules('brand','brand','trim');
		$this->form_validation->set_rules('storeid','storeid','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
            $data['alerterror'] = validation_errors();
			
            $data['brand']=$this->brand_model->getbranddropdown();
			$data['before']=$this->imagegallery_model->beforeedit($this->input->post('id'));
//			$data['page2']='block/eventblock';
			$data['page']='editgallery';
			$data['title']='Edit Gallery';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$brand=$this->input->post('brand');
			$description=$this->input->post('description');
			$storeid=$this->input->post('storeid');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
			}
            if($image=="")
            {
            $image=$this->imagegallery_model->getimagebyid($id);
               // print_r($image);
                $image=$image->image;
            }
			if($this->imagegallery_model->edit($id,$image,$description,$brand,$storeid)==0)
			$data['alerterror']="Image Gallery Editing was unsuccesful";
			else
			$data['alertsuccess']="Image Gallery edited Successfully.";
			
			$data['redirect']="site/viewgallery";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletegallery()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->imagegallery_model->deletegallery($this->input->get('id'));
		$data['table']=$this->imagegallery_model->viewgallery();
		$data['alertsuccess']="Image Deleted Successfully";
		$data['page']='viewgallery';
		$data['title']='View Image Gallery';
		$this->load->view('template',$data);
	}
    
    
    
    //new in
    
     function viewnewin()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->imagegallery_model->viewnewin();
		$data['page']='viewnewin';
		$data['title']='View New In';
		$this->load->view('template',$data);
	}  
    
     
	public function createnewin()
	{
		$access = array("1");
		$this->checkaccess($access);
        $data['brand']=$this->brand_model->getbranddropdown();
		$data[ 'page' ] = 'createnewin';
		$data[ 'title' ] = 'Create New In';
		$this->load->view( 'template', $data );	
	}
    function createnewinsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		//$this->form_validation->set_rules('image','Image','trim|required');
		$this->form_validation->set_rules('description','Description','trim|required');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			
            $data['brand']=$this->brand_model->getbranddropdown();
            $data[ 'page' ] = 'createnewin';
            $data[ 'title' ] = 'Create New In';
            $this->load->view( 'template', $data );	
		}
		else
		{
			//$image=$this->input->post('image');
			$description=$this->input->post('description');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$logo="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
			}
			if($this->imagegallery_model->createnewin($image,$description)==0)
			$data['alerterror']="New In could not be created.";
			else
			$data['alertsuccess']="New In created Successfully.";
			
			$data['table']=$this->imagegallery_model->viewnewin();
			$data['redirect']="site/viewnewin";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}

    
	function editnewin()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->imagegallery_model->beforeeditnewin($this->input->get('id'));
        $data['brand']=$this->brand_model->getbranddropdown();
		$data['page']='editnewin';
		$data['title']='Edit New in';
		$this->load->view('template',$data);
	}
    
    function editnewinsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
        
		$this->form_validation->set_rules('description','Description','trim|required');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
            $data['alerterror'] = validation_errors();
			
            $data['brand']=$this->brand_model->getbranddropdown();
			$data['before']=$this->imagegallery_model->beforeeditnewin($this->input->post('id'));
//			$data['page2']='block/eventblock';
			$data['page']='editnewin';
			$data['title']='Edit New in';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$description=$this->input->post('description');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
			}
            
            if($image=="")
            {
            $image=$this->imagegallery_model->getnewinimagebyid($id);
               // print_r($image);
                $image=$image->image;
            }
			if($this->imagegallery_model->editnewin($id,$image,$description)==0)
			$data['alerterror']="New In Editing was unsuccesful";
			else
			$data['alertsuccess']="New In edited Successfully.";
			
			$data['redirect']="site/viewnewin";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletenewin()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->imagegallery_model->deletenewin($this->input->get('id'));
		$data['table']=$this->imagegallery_model->viewnewin();
		$data['alertsuccess']="New In Deleted Successfully";
		$data['page']='viewnewin';
		$data['title']='View New In';
		$this->load->view('template',$data);
	}
    
    
    
    
    
	function viewoffer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->offer_model->viewoffer();
		$data['page']='viewoffer';
		$data['title']='View offer';
		$this->load->view('template',$data);
	}
	function editoffer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->offer_model->beforeeditoffer($this->input->get('id'));
//        print_r($data);
//        echo $data['before']->brandid;
        $data['store']=$this->store_model->getstorebybrand($data['before']->brandid);
//		$data['offer']=$this->offer_model->getofferdropdown();
//		$data[ 'status' ] =$this->user_model->getstatusdropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
		$data['page']='editoffer';
		$data['title']='Edit offer';
		$this->load->view('template',$data);
	}
	function editoffersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('header','header','trim|required');
		$this->form_validation->set_rules('description','description','trim|');
		$this->form_validation->set_rules('from','From','trim');
		$this->form_validation->set_rules('to','To','trim');
		$this->form_validation->set_rules('brand','Brand','trim');
		$this->form_validation->set_rules('storeid','storeid','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data[ 'status' ] =$this->user_model->getstatusdropdown();
//			$data['topic']=$this->topic_model->gettopicdropdown();
			$data['before']=$this->offer_model->beforeeditoffer($this->input->post('id'));
			$data['page']='editoffer';
			$data['title']='Edit offer';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$header=$this->input->post('header');
			$description=$this->input->post('description');
			$from=$this->input->post('from');
			$to=$this->input->post('to');
			$brand=$this->input->post('brand');
			$storeid=$this->input->post('storeid');
            if($from != "")
			{
				$from = date("Y-m-d",strtotime($from));
			}
            if($to != "")
			{
				$to = date("Y-m-d",strtotime($to));
			}
			if($this->offer_model->editoffer($id,$header,$description,$from,$to,$brand,$storeid)==0)
			$data['alerterror']="offer Editing was unsuccesful";
			else
			$data['alertsuccess']="offer edited Successfully.";
			$data['table']=$this->offer_model->viewoffer();
			$data['redirect']="site/viewoffer";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function deleteoffer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->offer_model->deleteoffer($this->input->get('id'));
		$data['table']=$this->offer_model->viewoffer();
		$data['alertsuccess']="offer Deleted Successfully";
		$data['page']='viewoffer';
		$data['title']='View offer';
		$this->load->view('template',$data);
	}
	//discountcoupon
	public function creatediscountcoupon()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'ticketevent' ] =$this->ticketevent_model->getticketevent();
		$data[ 'page' ] = 'creatediscountcoupon';
		$data[ 'title' ] = 'Create discountcoupon';
		$this->load->view( 'template', $data );	
	}
	function creatediscountcouponsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','name','trim|');
		$this->form_validation->set_rules('couponcode','couponcode','trim|');
		$this->form_validation->set_rules('percent','percent','trim|');
		$this->form_validation->set_rules('amount','amount','trim|');
		$this->form_validation->set_rules('minimumticket','minimumticket','trim|');
		$this->form_validation->set_rules('maximumticket','maximumticket','trim|');
		$this->form_validation->set_rules('ticketevent','ticketevent','trim|');
		$this->form_validation->set_rules('userperuser','userperuser','trim|');
		$this->form_validation->set_rules('starttime','Start Time','trim|required');
		$this->form_validation->set_rules('endtime','End Time','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'ticketevent' ] =$this->ticketevent_model->getticketevent();
			$data[ 'page' ] = 'creatediscountcoupon';
			$data[ 'title' ] = 'Create discountcoupon';
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$percent=$this->input->post('percent');
			$amount=$this->input->post('amount');
			$couponcode=$this->input->post('couponcode');
			$minimumticket=$this->input->post('minimumticket');
			$maximumticket=$this->input->post('maximumticket');
			$ticketevent=$this->input->post('ticketevent');
			$userperuser=$this->input->post('userperuser');
			$starttime=date("H:i",strtotime($this->input->post('starttime')));
			$starttime = $starttime.":00";
			$starttime = date("H:i:s",strtotime($starttime));
			$endtime=date("H:i",strtotime($this->input->post('endtime')));
			$endtime = $endtime.":00";
			$endtime = date("H:i:s",strtotime($endtime));
			if($this->discountcoupon_model->creatediscountcoupon($name,$percent,$amount,$minimumticket,$maximumticket,$ticketevent,$couponcode,$userperuser,$starttime,$endtime)==0)
			$data['alerterror']="New discountcoupon could not be created.";
			else
			$data['alertsuccess']="discountcoupon  created Successfully.";
			$data['table']=$this->discountcoupon_model->viewdiscountcoupon();
			$data['redirect']="site/viewdiscountcoupon";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function viewdiscountcoupon()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->discountcoupon_model->viewdiscountcoupon();
		$data['page']='viewdiscountcoupon';
		$data['title']='View discountcoupon';
		$this->load->view('template',$data);
	}
	function editdiscountcoupon()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->discountcoupon_model->beforeeditdiscountcoupon($this->input->get('id'));
		$data[ 'ticketevent' ] =$this->ticketevent_model->getticketevent();
		$data['page']='editdiscountcoupon';
		$data['title']='Edit discountcoupon';
		$this->load->view('template',$data);
	}
	function editdiscountcouponsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','name','trim|');
		$this->form_validation->set_rules('couponcode','couponcode','trim|');
		$this->form_validation->set_rules('percent','percent','trim|');
		$this->form_validation->set_rules('amount','amount','trim|');
		$this->form_validation->set_rules('minimumticket','minimumticket','trim|');
		$this->form_validation->set_rules('maximumticket','maximumticket','trim|');
		$this->form_validation->set_rules('ticketevent','ticketevent','trim|');
		$this->form_validation->set_rules('userperuser','userperuser','trim|');
		$this->form_validation->set_rules('starttime','Start Time','trim|required');
		$this->form_validation->set_rules('endtime','End Time','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['before']=$this->discountcoupon_model->beforeeditdiscountcoupon($this->input->post('id'));
			$data[ 'ticketevent' ] =$this->ticketevent_model->getticketevent();
			$data['page']='editdiscountcoupon';
			$data['title']='Edit discountcoupon';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$name=$this->input->post('name');
			$percent=$this->input->post('percent');
			$amount=$this->input->post('amount');
			$couponcode=$this->input->post('couponcode');
			$minimumticket=$this->input->post('minimumticket');
			$maximumticket=$this->input->post('maximumticket');
			$ticketevent=$this->input->post('ticketevent');
			$userperuser=$this->input->post('userperuser');
			$starttime=date("H:i",strtotime($this->input->post('starttime')));
			$starttime = $starttime.":00";
			$starttime = date("H:i:s",strtotime($starttime));
			$endtime=date("H:i",strtotime($this->input->post('endtime')));
			$endtime = $endtime.":00";
			$endtime = date("H:i:s",strtotime($endtime));
			if($this->discountcoupon_model->editdiscountcoupon($id,$name,$percent,$amount,$minimumticket,$maximumticket,$ticketevent,$couponcode,$userperuser,$starttime,$endtime)==0)
			$data['alerterror']="discountcoupon Editing was unsuccesful";
			else
			$data['alertsuccess']="discountcoupon edited Successfully.";
			$data['table']=$this->discountcoupon_model->viewdiscountcoupon();
			$data['redirect']="site/viewdiscountcoupon";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			/*$data['discountcoupon']='viewusers';
			$data['title']='View Users';
			$this->load->view('template',$data);*/
		}
	}
	function deletediscountcoupon()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->discountcoupon_model->deletediscountcoupon($this->input->get('id'));
		$data['table']=$this->discountcoupon_model->viewdiscountcoupon();
		$data['alertsuccess']="discountcoupon Deleted Successfully";
		$data['page']='viewdiscountcoupon';
		$data['title']='View discountcoupon';
		$this->load->view('template',$data);
	}
	public function createorganizer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createorganizer';
		$data[ 'title' ] = 'Create organizer';
		$data['user']=$this->user_model->getorganizeruser();
		$this->load->view( 'template', $data );	
	}
	function createorganizersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|');
		$this->form_validation->set_rules('contact','contactno','trim');
		
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('description','description','trim');
		$this->form_validation->set_rules('info','info','trim');
		$this->form_validation->set_rules('website','website','trim');
		$this->form_validation->set_rules('user','user','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createorganizer';
			$data['title']='Create New organizer';
			$data['user']=$this->user_model->getorganizeruser();
			$this->load->view('template',$data);
		}
		else
		{
			$info=$this->input->post('info');
			$email=$this->input->post('email');
			$website=$this->input->post('website');
			$contact=$this->input->post('contact');
			$name=$this->input->post('name');
			$description=$this->input->post('description');
			$user=$this->input->post('user');
			if($this->organizer_model->create($name,$description,$email,$contact,$info,$website,$user)==0)
			$data['alerterror']="New organizer could not be created.";
			else
			$data['alertsuccess']="organizer created Successfully.";
			
			$data['table']=$this->organizer_model->vieworganizers();
			$data['redirect']="site/vieworganizers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	
	function editorganizer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->organizer_model->beforeedit($this->input->get('id'));
		$data['user']=$this->user_model->getorganizeruser();
		$data['page']='editorganizer';
		$data['title']='Edit organizer';
		$this->load->view('template',$data);
	}
	function editorganizersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|');
		$this->form_validation->set_rules('contact','contactno','trim');
		
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('description','description','trim');
		$this->form_validation->set_rules('info','info','trim');
		$this->form_validation->set_rules('website','website','trim');
		$this->form_validation->set_rules('user','user','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['user']=$this->user_model->getorganizeruser();
			$data['before']=$this->organizer_model->beforeedit($this->input->post('id'));
			$data['page']='editorganizer';
			$data['title']='Edit organizer';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$info=$this->input->post('info');
			$email=$this->input->post('email');
			$website=$this->input->post('website');
			$contact=$this->input->post('contact');
			$name=$this->input->post('name');
			$description=$this->input->post('description');
			$user=$this->input->post('user');
			if($this->organizer_model->edit($id,$name,$description,$email,$contact,$info,$website,$user)==0)
			$data['alerterror']="organizer Editing was unsuccesful";
			else
			$data['alertsuccess']="organizer edited Successfully.";
			
			$data['redirect']="site/vieworganizers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteorganizer()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->organizer_model->deleteorganizer($this->input->get('id'));
		$data['table']=$this->organizer_model->vieworganizers();
		$data['alertsuccess']="organizer Deleted Successfully";
		$data['page']='vieworganizers';
		$data['title']='View organizers';
		$this->load->view('template',$data);
	}
    
	//City
    
    function viewcity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->city_model->viewcity();
		$data['page']='viewcity';
		$data['title']='View City';
		$this->load->view('template',$data);
	} 
    function viewonecitylocations()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->city_model->viewonecitylocations($this->input->get('cityid'));
		$data['page']='viewonecitylocations';
		$data['title']='View Locations';
		$this->load->view('template',$data);
	}
	public function createcity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createcity';
		$data[ 'title' ] = 'Create city';
//		$data['location']=$this->location_model->getlocation();
//        $data['category']=$this->category_model->getcategory();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
    function createcitysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createcity';
			$data['title']='Create New City';
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			if($this->city_model->create($name)==0)
			$data['alerterror']="New City could not be created.";
			else
			$data['alertsuccess']="City created Successfully.";
			
			$data['table']=$this->city_model->viewcity();
			$data['redirect']="site/viewcity";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    public function createlocation()
	{
		$access = array("1");
		$this->checkaccess($access);
        $data['cityid']=$this->input->get('cityid');
		$data[ 'page' ] = 'createlocation';
		$data[ 'title' ] = 'Create Location';
//		$data['location']=$this->location_model->getlocation();
//        $data['category']=$this->category_model->getcategory();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
    function createlocationsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('pincode','Pincode','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createlocation';
			$data['title']='Create New Location';
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$pincode=$this->input->post('pincode');
			$cityid=$this->input->get_post('cityid');
			if($this->city_model->createlocation($name,$cityid,$pincode)==0)
			$data['alerterror']="New Location could not be created.";
			else
			$data['alertsuccess']="Location created Successfully.";
			
			$data['table']=$this->city_model->viewonecitylocations($cityid);
			$data['redirect']="site/viewonecitylocations?cityid=".$cityid;
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
    function editcity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->city_model->beforeedit($this->input->get('id'));
//		$data['organizer']=$this->organizer_model->getorganizer();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
//		$data['page2']='block/eventblock';
		$data['page']='editcity';
		$data['title']='Edit City';
		$this->load->view('template',$data);
	}
	function editcitysubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$data['before']=$this->city_model->beforeedit($this->input->post('id'));
//			$data['page2']='block/eventblock';
			$data['page']='editcity';
			$data['title']='Edit City';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$name=$this->input->post('name');
			if($this->city_model->edit($id,$name)==0)
			$data['alerterror']="City Editing was unsuccesful";
			else
			$data['alertsuccess']="City edited Successfully.";
			
			$data['redirect']="site/viewcity";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	function editlocation()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->city_model->beforeeditlocation($this->input->get('id'));
//		$data['organizer']=$this->organizer_model->getorganizer();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
//		$data['page2']='block/eventblock';
		$data['page']='editlocation';
		$data['title']='Edit Location';
		$this->load->view('template',$data);
	}
	function editlocationsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('pincode','Pincode','trim|required');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$data['before']=$this->city_model->beforeedit($this->input->post('id'));
//			$data['page2']='block/eventblock';
			$data['page']='editcity';
			$data['title']='Edit City';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->get_post('id');
			$cityid=$this->input->get_post('cityid');
			$name=$this->input->post('name');
			$pincode=$this->input->post('pincode');
			if($this->city_model->editlocation($id,$cityid,$name,$pincode)==0)
			$data['alerterror']="Location Editing was unsuccesful";
			else
			$data['alertsuccess']="Location edited Successfully.";
			
			$data['redirect']="site/viewonecitylocations?cityid=".$cityid;
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
    
	function deletecity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->city_model->deletecity($this->input->get('id'));
		$data['table']=$this->city_model->viewcity();
		$data['alertsuccess']="City Deleted Successfully";
		$data['page']='viewcity';
		$data['title']='View City';
		$this->load->view('template',$data);
	}
     
	function deletelocation()
	{
		$access = array("1");
		$this->checkaccess($access);
        $cityid=$this->input->get('cityid');
		$this->city_model->deletelocation($this->input->get('id'));
		$data['table']=$this->city_model->viewonecitylocations($cityid);
		$data['alertsuccess']="City Deleted Successfully";
		$data['page']='viewonecitylocations';
		$data['title']='View Location';
		$this->load->view('template',$data);
	}
    
    //Brand
    
    function viewbrand()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->brand_model->viewbrand();
		$data['page']='viewbrand';
		$data['title']='View Brand';
		$this->load->view('template',$data);
	} 
    
    public function createbrand()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createbrand';
		$data[ 'title' ] = 'Create Brand';
        $data['category']=$this->brand_model->getcategory();
//		$data['location']=$this->location_model->getlocation();
//        $data['category']=$this->category_model->getcategory();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
    function createbrandsubmit()
	{
        $access = array("1");
		$this->checkaccess($access);
        $this->form_validation->set_rules('name','Name','trim|required');
        $this->form_validation->set_rules('website','website','trim');
        $this->form_validation->set_rules('twitter','twitter','trim');
        $this->form_validation->set_rules('pininterest','pininterest','trim');
        $this->form_validation->set_rules('googleplus','googleplus','trim');
        $this->form_validation->set_rules('instagram','instagram','trim');
        $this->form_validation->set_rules('blog','blog','trim');
        $this->form_validation->set_rules('description','description','trim');
		
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createbrand';
			$data['title']='Create New Brand';
        $data['category']=$this->brand_model->getcategory();
			$this->load->view('template',$data);
		}
		else
		{
           //$id=$this->input->get_post('id');
			$name=$this->input->post('name');
			$website=$this->input->post('website');
			$facebook=$this->input->post('facebook');
			$twitter=$this->input->post('twitter');
			$pininterest=$this->input->post('pininterest');
			$googleplus=$this->input->post('googleplus');
			$instagram=$this->input->post('instagram');
			$blog=$this->input->post('blog');
			$description=$this->input->post('description');
            $id=$this->brand_model->create($name,$website,$facebook,$twitter,$pininterest,$googleplus,$instagram,$blog,$description);
            if($id==0)
			$data['alerterror']="New brand could not be created.";
			else
			$data['alertsuccess']="brand created Successfully.";
            
            foreach ($_POST as $key => $value) {
             if(is_array($value)){
//                 echo "hi";
             foreach ($_POST[$key] as $key => $value) {
        //        echo "<tr>";
        //        echo "<td>";
//                echo $key;
        //        echo "</td>";
        //        echo "<td>";
               // echo $value;
                 $this->brand_model->createsubcategory($id,$value);
        //        echo "</td>";
        //        echo "</tr>";
                     }


                     }
                     else{
        //        echo "<tr>";
        //        echo "<td>";
//                echo $key;
        //        echo "</td>";
        //        echo "<td>";
                //echo $value;
                         if($key!="name")
                $this->brand_model->createsubcategory($id,$value);
                         
        //        echo "</td>";
        //        echo "</tr>";
                     }
             
                }
			
//			
			$data['table']=$this->brand_model->viewbrand();
			$data['redirect']="site/viewbrand";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
    
    function editbrand()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->brand_model->beforeedit($this->input->get('id'));
        $data['category']=$this->brand_model->getcategory();
        $data['brandcategory']=$this->brand_model->getbrandcategory($this->input->get('id'));
//		$data['organizer']=$this->organizer_model->getorganizer();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
//		$data['page2']='block/eventblock';
		$data['page']='editbrand';
		$data['title']='Edit brand';
		$this->load->view('template',$data);
	}
	function editbrandsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
        $this->form_validation->set_rules('website','website','trim');
        $this->form_validation->set_rules('twitter','twitter','trim');
        $this->form_validation->set_rules('pininterest','pininterest','trim');
        $this->form_validation->set_rules('googleplus','googleplus','trim');
        $this->form_validation->set_rules('instagram','instagram','trim');
        $this->form_validation->set_rules('blog','blog','trim');
        $this->form_validation->set_rules('description','description','trim');
		
        print_r($_POST);
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createbrand';
			$data['title']='Create New Brand';
        $data['category']=$this->brand_model->getcategory();
			$this->load->view('template',$data);
		}
		else
		{
           $id=$this->input->get_post('id');
			$name=$this->input->post('name');
            $website=$this->input->post('website');
			$facebook=$this->input->post('facebook');
			$twitter=$this->input->post('twitter');
			$pininterest=$this->input->post('pininterest');
			$googleplus=$this->input->post('googleplus');
			$instagram=$this->input->post('instagram');
			$blog=$this->input->post('blog');
			$description=$this->input->post('description');
            $id1=$this->brand_model->editbrand($id,$name,$website,$facebook,$twitter,$pininterest,$googleplus,$instagram,$blog,$description);
            $this->brand_model->deletesubcategory($id);
            if($id1==0)
			$data['alerterror']="New brand could not be Updated.";
			else
			$data['alertsuccess']="brand Updated Successfully.";
            
            foreach ($_POST as $key => $value) {
             if(is_array($value)){
//                 echo "hi";
             foreach ($_POST[$key] as $key => $value) {
                 echo $value;
                 $this->brand_model->createsubcategory($id,$value);
                     }

                     }
                     else{
                         if($key!="name")
                $this->brand_model->createsubcategory($id,$value);
               
                     }
             
                }
			
//			
			$data['table']=$this->brand_model->viewbrand();
			$data['redirect']="site/viewbrand";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
	function deletebrand()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->brand_model->deletebrand($this->input->get('id'));
		$data['table']=$this->brand_model->viewbrand();
		$data['alertsuccess']="brand Deleted Successfully";
		$data['page']='viewbrand';
		$data['title']='View brand';
		$this->load->view('template',$data);
	}
    
    function deletebrandcategory()
	{
		$access = array("1");
		$this->checkaccess($access);
        $id=$this->input->get('id');
        $brandid=$this->input->get('brandid');
		$this->category_model->deletebrandcategory($this->input->get('id'),$this->input->get('brandid'));
		$data['table']=$this->category_model->viewonebrandcategories($this->input->get('brandid'));
		$data['page']='viewonebrandcategories';
		$data['title']='View Brand category';
		$this->load->view('template',$data);
	}
    
    
    
    
    //Mall
    public function createmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createmall';
		$data[ 'title' ] = 'Create mall';
        $data['location']=$this->city_model->getlocationdropdown();
//		$data['location']=$this->location_model->getlocation();
//        $data['category']=$this->category_model->getcategory();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
	function createmallsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('address','Address','trim|required');
		$this->form_validation->set_rules('description','Description','trim|required');
		$this->form_validation->set_rules('specialoffers','specialoffers','trim');
		$this->form_validation->set_rules('events','events','trim');
		$this->form_validation->set_rules('cinemaoffer','Cinemaoffer','trim');
		$this->form_validation->set_rules('facebookpage','Facebookpage','trim');
		$this->form_validation->set_rules('pininterest','pininterest','trim');
		$this->form_validation->set_rules('instagram','instagram','trim');
		$this->form_validation->set_rules('twitterpage','twitterpage','trim');
		$this->form_validation->set_rules('location','location','trim|required');
		$this->form_validation->set_rules('latitude','Latitude','trim');
		$this->form_validation->set_rules('longitude','Longitude','trim|');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[mall.email]');
		$this->form_validation->set_rules('contactno','contactno','trim');
		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
		$this->form_validation->set_rules('parkingfacility','Parkingfacility','trim');
		$this->form_validation->set_rules('cinema','Cinema','trim');
		$this->form_validation->set_rules('restaurant','Restaurant','trim');
		$this->form_validation->set_rules('entertainment','Entertainment','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createmall';
			$data['title']='Create New Mall';
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$address=$this->input->post('address');
			$description=$this->input->post('description');
			$specialoffers=$this->input->post('specialoffers');
			$events=$this->input->post('events');
			$cinemaoffer=$this->input->post('cinemaoffer');
			$pininterest=$this->input->post('pininterest');
			$instagram=$this->input->post('instagram');
			$twitterpage=$this->input->post('twitterpage');
			$facebookpage=$this->input->post('facebookpage');
			$location=$this->input->post('location');
			$latitude=$this->input->post('latitude');
			$longitude=$this->input->post('longitude');
			$contactno=$this->input->post('contactno');
			$parkingfacility=$this->input->post('parkingfacility');
			$cinema=$this->input->post('cinema');
			$restaurant=$this->input->post('restaurant');
			$entertainment=$this->input->post('entertainment');
			$website=$this->input->post('website');
			$email=$this->input->post('email');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="logo";
			$logo="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$logo=$uploaddata['file_name'];
			}
			if($this->mall_model->create($name,$address,$location,$latitude,$longitude,$contactno,$parkingfacility,$cinema,$restaurant,$entertainment,$website,$email,$logo,$description,$specialoffers,$events,$cinemaoffer,$facebookpage,$pininterest,$instagram,$twitterpage)==0)
			$data['alerterror']="New Mall could not be created.";
			else
			$data['alertsuccess']="Mall created Successfully.";
			
			$data['table']=$this->mall_model->viewmall();
			$data['redirect']="site/viewmall";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function viewmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->mall_model->viewmall();
		$data['page']='viewmall';
		$data['title']='View Malls';
		$this->load->view('template',$data);
	}
	function editmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->mall_model->beforeedit($this->input->get('id'));
        $data['location']=$this->city_model->getlocationdropdown();
//		$data['organizer']=$this->organizer_model->getorganizer();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
//		$data['page2']='block/eventblock';
		$data['page']='editmall';
		$data['title']='Edit Mall';
		$this->load->view('template',$data);
	}
	function editmallsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('address','Address','trim|required');
		$this->form_validation->set_rules('description','Description','trim|required');
		$this->form_validation->set_rules('specialoffers','specialoffers','trim');
		$this->form_validation->set_rules('events','events','trim');
		$this->form_validation->set_rules('cinemaoffer','Cinemaoffer','trim');
		$this->form_validation->set_rules('facebookpage','Facebookpage','trim');
		$this->form_validation->set_rules('pininterest','pininterest','trim');
		$this->form_validation->set_rules('instagram','instagram','trim');
		$this->form_validation->set_rules('twitterpage','twitterpage','trim');
		$this->form_validation->set_rules('location','location','trim|required');
		$this->form_validation->set_rules('latitude','Latitude','trim');
		$this->form_validation->set_rules('longitude','Longitude','trim|');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('contactno','contactno','trim');
		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
		$this->form_validation->set_rules('parkingfacility','Parkingfacility','trim');
		$this->form_validation->set_rules('cinema','Cinema','trim');
		$this->form_validation->set_rules('restaurant','Restaurant','trim');
		$this->form_validation->set_rules('entertainment','Entertainment','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
//			$data['organizer']=$this->organizer_model->getorganizer();
//			$data['listingtype']=$this->event_model->getlistingtype();
//			$data['remainingticket']=$this->event_model->getremainingticket();
			$data['before']=$this->mall_model->beforeedit($this->input->post('id'));
//			$data['page2']='block/eventblock';
			$data['page']='editmall';
			$data['title']='Edit Mall';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$name=$this->input->post('name');
			$address=$this->input->post('address');
			$description=$this->input->post('description');
			$specialoffers=$this->input->post('specialoffers');
			$events=$this->input->post('events');
			$facebookpage=$this->input->post('facebookpage');
			$cinemaoffer=$this->input->post('cinemaoffer');
			$pininterest=$this->input->post('pininterest');
			$instagram=$this->input->post('instagram');
			$twitterpage=$this->input->post('twitterpage');
			$location=$this->input->post('location');
			$latitude=$this->input->post('latitude');
			$longitude=$this->input->post('longitude');
			$contactno=$this->input->post('contactno');
			$parkingfacility=$this->input->post('parkingfacility');
			$cinema=$this->input->post('cinema');
			$restaurant=$this->input->post('restaurant');
			$entertainment=$this->input->post('entertainment');
			$website=$this->input->post('website');
			$email=$this->input->post('email');
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="logo";
			$logo="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$logo=$uploaddata['file_name'];
			}
			if($this->mall_model->edit($id,$name,$address,$location,$latitude,$longitude,$contactno,$parkingfacility,$cinema,$restaurant,$entertainment,$website,$email,$logo,$description,$specialoffers,$events,$cinemaoffer,$facebookpage,$pininterest,$instagram,$twitterpage)==0)
			$data['alerterror']="Mall Editing was unsuccesful";
			else
			$data['alertsuccess']="Mall edited Successfully.";
			
			$data['redirect']="site/viewmall";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletemall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->mall_model->deletemall($this->input->get('id'));
		$data['table']=$this->mall_model->viewmall();
		$data['alertsuccess']="Mall Deleted Successfully";
		$data['page']='viewmall';
		$data['title']='View Malls';
		$this->load->view('template',$data);
	}
    
    /*-----------------Event functions Addes by Avinash------------------------*/
    public function showalleventsbyuserid()
    {
        $id=$this->input->get('id');
        $data['json']=$this->event_model->showalleventsbyuserid($id);
        print_r ($data);
		//$this->load->view('json',$data);
    }
    public function findone()
	{
        $id=$this->input->get('id');
        $data['json']=$this->event_model->viewone($id);
        print_r($data);
		//$this->load->view('json',$data);
	}
    
    /*-----------------End of event functions----------------------------------*/
    
	function editeventcategorytopic()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->event_model->beforeedit($this->input->get('id'));
		$data['category']=$this->category_model->getcategory();
		$data['topic']=$this->topic_model->gettopic();
		$data['page2']='block/eventblock';
		$data['page']='eventcategorytopic';
		$data['title']='Edit event category';
		$this->load->view('template',$data);
	}
	function editeventcategorytopicsubmit()
	{
		$this->form_validation->set_rules('id','id','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['before']=$this->event_model->beforeeditevent($this->input->post('id'));
			$data['category']=$this->category_model->getcategory();
			$data['topic']=$this->topic_model->gettopic();
			$data['page2']='block/eventblock';
			$data['page']='eventcategorytopic';
			$data['title']='Edit Related events';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			
			$category=$this->input->post('category');
			$topic=$this->input->post('topic');
			if($this->event_model->editeventcategorytopic($id,$category,$topic)==0)
			$data['alerterror']=" Event category-topic Editing was unsuccesful";
			else
			$data['alertsuccess']=" Event category-topic edited Successfully.";
			
			$data['redirect']="site/editeventcategorytopic?id=".$id;
			//$data['other']="template=$template";
			$this->load->view("redirect2",$data);
		}
	}
	//ticketevent
	public function createticketevent()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createticketevent';
		$data[ 'title' ] = 'Create ticketevent';
		$data['event']=$this->event_model->getevent();
		$data['tickettype']=$this->ticketevent_model->gettickettype();
		$this->load->view( 'template', $data );	
	}
	function createticketeventsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('event','event','trim|');
		$this->form_validation->set_rules('tickettype','tickettype','trim');
		$this->form_validation->set_rules('ticket','ticket','trim|');
		$this->form_validation->set_rules('ticketname','ticketname','trim');
		$this->form_validation->set_rules('amount','amount','trim');
		$this->form_validation->set_rules('starttime','Start Time','trim|required');
		$this->form_validation->set_rules('endtime','End Time','trim|required');
		$this->form_validation->set_rules('quantity','quantity','trim');
		$this->form_validation->set_rules('description','description','trim');
		$this->form_validation->set_rules('ticketmaxallowed','ticketmaxallowed','trim');
		$this->form_validation->set_rules('ticketminallowed','ticketminallowed','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createticketevent';
			$data['title']='Create New ticketevent';
			$data['event']=$this->event_model->getevent();
			$data['tickettype']=$this->ticketevent_model->gettickettype();
			$this->load->view('template',$data);
		}
		else
		{
			$event=$this->input->post('event');
			$ticket=$this->input->post('ticket');
			$tickettype=$this->input->post('tickettype');
			$amount=$this->input->post('amount');
			$ticketname=$this->input->post('ticketname');
			$quantity=$this->input->post('quantity');
			$description=$this->input->post('description');
			$ticketmaxallowed=$this->input->post('ticketmaxallowed');
			$ticketminallowed=$this->input->post('ticketminallowed');
			$starttime=date("H:i",strtotime($this->input->post('starttime')));
			$starttime = $starttime.":00";
			$starttime = date("H:i:s",strtotime($starttime));
			$endtime=date("H:i",strtotime($this->input->post('endtime')));
			$endtime = $endtime.":00";
			$endtime = date("H:i:s",strtotime($endtime));
			if($this->ticketevent_model->create($event,$ticket,$tickettype,$amount,$ticketname,$quantity,$description,$ticketmaxallowed,$ticketminallowed,$starttime,$endtime)==0)
			$data['alerterror']="New ticketevent could not be created.";
			else
			$data['alertsuccess']="ticketevent created Successfully.";
			
			$data['table']=$this->ticketevent_model->viewticketevent();
			$data['redirect']="site/viewticketevent";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function viewticketevent()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->ticketevent_model->viewticketevent();
		$data['page']='viewticketevent';
		$data['title']='View ticketevent';
		$this->load->view('template',$data);
	}
	function editticketevent()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->ticketevent_model->beforeedit($this->input->get('id'));
		$data['event']=$this->event_model->getevent();
		$data['tickettype']=$this->ticketevent_model->gettickettype();
		$data['page']='editticketevent';
		$data['title']='Edit ticketevent';
		$this->load->view('template',$data);
	}
	function editticketeventsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('event','event','trim|');
		$this->form_validation->set_rules('tickettype','tickettype','trim');
		$this->form_validation->set_rules('ticket','ticket','trim|');
		$this->form_validation->set_rules('ticketname','ticketname','trim');
		$this->form_validation->set_rules('amount','amount','trim');
		$this->form_validation->set_rules('starttime','Start Time','trim|required');
		$this->form_validation->set_rules('endtime','End Time','trim|required');
		$this->form_validation->set_rules('quantity','quantity','trim');
		$this->form_validation->set_rules('description','description','trim');
		$this->form_validation->set_rules('ticketmaxallowed','ticketmaxallowed','trim');
		$this->form_validation->set_rules('ticketminallowed','ticketminallowed','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['event']=$this->event_model->getevent();
			$data['tickettype']=$this->ticketevent_model->gettickettype();
			$data['before']=$this->ticketevent_model->beforeedit($this->input->post('id'));
			$data['page']='editticketevent';
			$data['title']='Edit ticketevent';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$event=$this->input->post('event');
			$ticket=$this->input->post('ticket');
			$tickettype=$this->input->post('tickettype');
			$amount=$this->input->post('amount');
			$ticketname=$this->input->post('ticketname');
			$quantity=$this->input->post('quantity');
			$description=$this->input->post('description');
			$ticketmaxallowed=$this->input->post('ticketmaxallowed');
			$ticketminallowed=$this->input->post('ticketminallowed');
			$starttime=date("H:i",strtotime($this->input->post('starttime')));
			$starttime = $starttime.":00";
			$starttime = date("H:i:s",strtotime($starttime));
			$endtime=date("H:i",strtotime($this->input->post('endtime')));
			$endtime = $endtime.":00";
			$endtime = date("H:i:s",strtotime($endtime));
			if($this->ticketevent_model->edit($id,$event,$ticket,$tickettype,$amount,$ticketname,$quantity,$description,$ticketmaxallowed,$ticketminallowed,$starttime,$endtime)==0)
			$data['alerterror']="ticketevent Editing was unsuccesful";
			else
			$data['alertsuccess']="ticketevent edited Successfully.";
			
			$data['redirect']="site/viewticketevent";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteticketevent()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->ticketevent_model->deleteticketevent($this->input->get('id'));
		$data['table']=$this->ticketevent_model->viewticketevent();
		$data['alertsuccess']="ticketevent Deleted Successfully";
		$data['page']='viewticketevent';
		$data['title']='View ticketevent';
		$this->load->view('template',$data);
	}
	//Newsletter
	public function createnewsletter()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createnewsletter';
		$data[ 'title' ] = 'Create newsletter';
		$this->load->view( 'template', $data );	
	}
	public function createnewslettersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('title','title','trim|');
		$this->form_validation->set_rules('subject','subject','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'page' ] = 'createnewsletter';
			$data[ 'title' ] = 'Create newsletter';
			$this->load->view('template',$data);
		}
		else
		{
			$title=$this->input->post('title');
			$subject=$this->input->post('subject');
			if($this->newsletter_model->createnewsletter($title,$subject)==0)
			$data['alerterror']="New newsletter could not be created.";
			else
			$data['alertsuccess']="newsletter  created Successfully.";
			$data['redirect']="site/viewnewsletter";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	public function editnewsletter()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->newsletter_model->beforeeditnewsletter($this->input->get('id'));
		$data[ 'page' ] = 'editnewsletter';
		$data[ 'title' ] = 'Edit newsletter';
		$this->load->view( 'template', $data );	
	}
	function editnewslettersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('title','title','trim|');
		$this->form_validation->set_rules('subject','subject','trim|');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['before']=$this->newsletter_model->beforeeditnewsletter($this->input->post('id'));
			$data['page']='editnewsletter';
			$data['title']='Edit newsletter';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$title=$this->input->post('title');
			$subject=$this->input->post('subject');
			
			if($this->newsletter_model->editnewsletter($id,$title,$subject)==0)
			$data['alerterror']="newsletter Editing was unsuccesful";
			else
			$data['alertsuccess']="newsletter edited Successfully.";
			$data['table']=$this->newsletter_model->viewnewsletter();
			$data['redirect']="site/viewnewsletter";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			/*$data['page']='viewusers';
			$data['title']='View Users';
			$this->load->view('template',$data);*/
		}
	}
	function deletenewsletter()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->newsletter_model->deletenewsletter($this->input->get('id'));
		$data['table']=$this->newsletter_model->viewnewsletter();
		$data['alertsuccess']="newsletter Deleted Successfully";
		$data['page']='viewnewsletter';
		$data['title']='View newsletter';
		$this->load->view('template',$data);
	}
	function viewnewsletter()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->newsletter_model->viewnewsletter();
		$data['page']='viewnewsletter';
		$data['title']='View newsletter';
		$this->load->view('template',$data);
	}
    
    
    //store
    
    function viewindividualstore()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->store_model->viewindividualstore();
		$data['page']='viewindividualstore';
		$data['title']='View Individualstore';
		$this->load->view('template',$data);
	}
    function viewstoreinmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->store_model->viewstoreinmall();
		$data['page']='viewstoreinmall';
		$data['title']='View Stores in mall';
		$this->load->view('template',$data);
	}
    
     public function createstoreinmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createstoreinmall';
		$data[ 'title' ] = 'Create Store in mall';
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
        $data['mall']=$this->mall_model->getmalldropdown();
        $data['floor']=$this->mall_model->getfloordropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
	function editstoreinmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->store_model->beforeeditstoreinmall($this->input->get('id'));
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['mall']=$this->mall_model->getmalldropdown();
        $data['floor']=$this->mall_model->getfloordropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
		$data['page']='editstoreinmall';
		$data['title']='Edit store in mall';
		$this->load->view('template',$data);
	}
	function editstoreinmallsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');
		$this->form_validation->set_rules('brand','brand','trim|required');
		$this->form_validation->set_rules('mall','mall','trim|required');
		$this->form_validation->set_rules('floor','floor','trim|required');
		$this->form_validation->set_rules('offer','offer','trim|');
		$this->form_validation->set_rules('description','Description','trim|');
		$this->form_validation->set_rules('shopclosedon','Shopclosedon','trim|');
		$this->form_validation->set_rules('workinghoursfrom','Workinghoursfrom','trim|');
		$this->form_validation->set_rules('workinghoursto','Workinghoursto','trim|');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[mall.email]');
		$this->form_validation->set_rules('contactno','contactno','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editstoreinmall';
			$data['title']='Edit Store in Mall';
            $data['before']=$this->store_model->beforeeditstoreinmall($this->input->get('id'));
            $data['city']=$this->city_model->getcitydropdown();
            $data['brand']=$this->brand_model->getbranddropdown();
            $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
            $data['mall']=$this->mall_model->getmalldropdown();
            $data['floor']=$this->mall_model->getfloordropdown();
			$this->load->view('template',$data);
		}
		else
		{
            $id=$this->input->post('id');
			$name=$this->input->post('name');
			$city=$this->input->post('city');
			$brand=$this->input->post('brand');
			$mall=$this->input->post('mall');
			$floor=$this->input->post('floor');
			$offer=$this->input->post('offer');
			$contactno=$this->input->post('contactno');
			$description=$this->input->post('description');
			$email=$this->input->post('email');
			$format=$this->input->post('format');
			$shopclosedon=$this->input->post('shopclosedon');
			$workinghoursfrom=$this->input->post('workinghoursfrom');
			$workinghoursto=$this->input->post('workinghoursto');
			$email=$this->input->post('email');
			$format=$this->input->post('format');
			if($this->store_model->editstoreinmall($id,$name,$city,$brand,$mall,$floor,$contactno,$email,$format,$offer,$shopclosedon,$workinghoursfrom,$workinghoursto,$description)==0)
			$data['alerterror']="Store in Mall could not be edited.";
			else
			$data['alertsuccess']="Store in Mall Updated Successfully.";
			
			$data['table']=$this->store_model->viewstoreinmall();
			$data['redirect']="site/viewstoreinmall";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function createstoreinmallsubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');
		$this->form_validation->set_rules('brand','brand','trim|required');
		$this->form_validation->set_rules('mall','mall','trim|required');
		$this->form_validation->set_rules('floor','floor','trim|required');
		$this->form_validation->set_rules('offer','offer','trim|');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[mall.email]');
		$this->form_validation->set_rules('contactno','contactno','trim');
		$this->form_validation->set_rules('workinghoursfrom','workinghoursFrom','trim');
		$this->form_validation->set_rules('workinghoursto','workinghoursTo','trim');
		$this->form_validation->set_rules('description','Description','trim');
		$this->form_validation->set_rules('shopclosedon','shopclosedon','trim');
        
//		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
//		$this->form_validation->set_rules('facebookpage','facebookpage','trim');
//		$this->form_validation->set_rules('twitterpage','twitterpage','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createstoreinmall';
			$data['title']='Create New Store in Mall';
            $data['city']=$this->city_model->getcitydropdown();
            $data['brand']=$this->brand_model->getbranddropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
            $data['mall']=$this->mall_model->getmalldropdown();
            $data['floor']=$this->mall_model->getfloordropdown();
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$city=$this->input->post('city');
			$brand=$this->input->post('brand');
			$mall=$this->input->post('mall');
			$floor=$this->input->post('floor');
			$offer=$this->input->post('offer');
			$contactno=$this->input->post('contactno');
			$description=$this->input->post('description');
//			$facebookpage=$this->input->post('facebookpage');
//			$twitterpage=$this->input->post('twitterpage');
//			$website=$this->input->post('website');
			$email=$this->input->post('email');
			$format=$this->input->post('format');
			$shopclosedon=$this->input->post('shopclosedon');
			$workinghoursfrom=$this->input->post('workinghoursfrom');
			$workinghoursto=$this->input->post('workinghoursto');
//			$config['upload_path'] = './uploads/';
//			$config['allowed_types'] = 'gif|jpg|png|jpeg';
//			$this->load->library('upload', $config);
//			$filename="logo";
//			$logo="";
//			if (  $this->upload->do_upload($filename))
//			{
//				$uploaddata = $this->upload->data();
//				$logo=$uploaddata['file_name'];
//			}
			if($this->store_model->createstoreinmall($name,$city,$brand,$mall,$floor,$contactno,$email,$format,$offer,$shopclosedon,$workinghoursfrom,$workinghoursto,$description)==0)
			$data['alerterror']="New Store in Mall could not be created.";
			else
			$data['alertsuccess']="Store in Mall created Successfully.";
			
			$data['table']=$this->store_model->viewstoreinmall();
			$data['redirect']="site/viewstoreinmall";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
     public function createindividualstore()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createindividualstore';
		$data[ 'title' ] = 'Create Individual Store';
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
//        $data['mall']=$this->mall_model->getmalldropdown();
//        $data['floor']=$this->mall_model->getfloordropdown();
        $data['location']=$this->city_model->getlocationdropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
     public function editindividualstore()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'editindividualstore';
		$data[ 'title' ] = 'Edit Individual Store';
		$data['before']=$this->store_model->beforeeditindividualstore($this->input->get('id'));
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
//        $data['mall']=$this->mall_model->getmalldropdown();
//        $data['floor']=$this->mall_model->getfloordropdown();
        $data['location']=$this->city_model->getlocationdropdown();
//        $data['topic']=$this->topic_model->gettopic();
//		$data['listingtype']=$this->event_model->getlistingtype();
//		$data['remainingticket']=$this->event_model->getremainingticket();
		$this->load->view( 'template', $data );	
	}
	function createindividualstoresubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');
		$this->form_validation->set_rules('brand','brand','trim|required');
		$this->form_validation->set_rules('offer','offer','trim|');
		$this->form_validation->set_rules('workinghoursfrom','Workinghoursfrom','trim|');
		$this->form_validation->set_rules('workinghoursto','Workinghoursto','trim|');
		$this->form_validation->set_rules('shopclosedon','shopclosedon','trim|');
		$this->form_validation->set_rules('address','Address','trim|required');
		$this->form_validation->set_rules('description','Description','trim');
		$this->form_validation->set_rules('location','Location','trim|required');
		$this->form_validation->set_rules('latitude','Latitude','trim|required');
		$this->form_validation->set_rules('longitude','Longitude','trim|required');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[mall.email]');
		$this->form_validation->set_rules('contactno','contactno','trim');
//		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
//		$this->form_validation->set_rules('facebookpage','facebookpage','trim');
//		$this->form_validation->set_rules('twitterpage','twitterpage','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'page' ] = 'createindividualstore';
		$data[ 'title' ] = 'Create Individual Store';
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
        $data['mall']=$this->mall_model->getmalldropdown();
        $data['floor']=$this->mall_model->getfloordropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
        $data['location']=$this->city_model->getlocationdropdown();
		$this->load->view( 'template', $data );
		}
		else
		{
			$name=$this->input->post('name');
			$city=$this->input->post('city');
			$brand=$this->input->post('brand');
			$offer=$this->input->post('offer');
			$workinghoursfrom=$this->input->post('workinghoursfrom');
			$workinghoursto=$this->input->post('workinghoursto');
			$shopclosedon=$this->input->post('shopclosedon');
			$address=$this->input->post('address');
			$description=$this->input->post('description');
			$location=$this->input->post('location');
			$latitude=$this->input->post('latitude');
			$longitude=$this->input->post('longitude');
			$contactno=$this->input->post('contactno');
            
//			$facebookpage=$this->input->post('facebookpage');
//			$twitterpage=$this->input->post('twitterpage');
//			$website=$this->input->post('website');
			$email=$this->input->post('email');
			$format=$this->input->post('format');
//			$config['upload_path'] = './uploads/';
//			$config['allowed_types'] = 'gif|jpg|png|jpeg';
//			$this->load->library('upload', $config);
//			$filename="logo";
//			$logo="";
//			if (  $this->upload->do_upload($filename))
//			{
//				$uploaddata = $this->upload->data();
//				$logo=$uploaddata['file_name'];
//			}
			if($this->store_model->createindividualstore($name,$city,$brand,$address,$location,$latitude,$longitude,$contactno,$email,$format,$offer,$workinghoursfrom,$workinhhoursto,$shopclosedon,$description)==0)
			$data['alerterror']="New Individual Store could not be created.";
			else
			$data['alertsuccess']="Individual Store created Successfully.";
			
			$data['table']=$this->store_model->viewindividualstore();
			$data['redirect']="site/viewindividualstore";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
	function editindividualstoresubmit()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');
		$this->form_validation->set_rules('brand','brand','trim|required');
		$this->form_validation->set_rules('offer','offer','trim|required');
		$this->form_validation->set_rules('workinghoursfrom','Workinghoursfrom','trim|');
		$this->form_validation->set_rules('workinghoursto','Workinghoursto','trim|');
		$this->form_validation->set_rules('address','Address','trim|required');
		$this->form_validation->set_rules('description','Description','trim');
		$this->form_validation->set_rules('location','Location','trim|required');
		$this->form_validation->set_rules('latitude','Latitude','trim|required');
		$this->form_validation->set_rules('longitude','Longitude','trim|required');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[mall.email]');
		$this->form_validation->set_rules('contactno','contactno','trim');
//		$this->form_validation->set_rules('website','Website','trim|max_length[50]');
//		$this->form_validation->set_rules('facebookpage','facebookpage','trim');
//		$this->form_validation->set_rules('twitterpage','twitterpage','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'page' ] = 'editindividualstore';
		$data[ 'title' ] = 'Edit Individual Store';
		$data['before']=$this->store_model->beforeeditindividualstore($this->input->get('id'));
		$data['city']=$this->city_model->getcitydropdown();
        $data['brand']=$this->brand_model->getbranddropdown();
        $data['shopclosedon']=$this->store_model->getshopclosedondropdown();
        $data['offer']=$this->offer_model->getofferdropdown();
//        $data['mall']=$this->mall_model->getmalldropdown();
//        $data['floor']=$this->mall_model->getfloordropdown();
        $data['location']=$this->city_model->getlocationdropdown();
		$this->load->view( 'template', $data );
		}
		else
		{
			$id=$this->input->get_post('id');
			$name=$this->input->post('name');
			$city=$this->input->post('city');
			$brand=$this->input->post('brand');
			$offer=$this->input->post('offer');
			$workinghoursfrom=$this->input->post('workinghoursfrom');
			$workinghoursto=$this->input->post('workinghoursto');
			$shopclosedon=$this->input->post('shopclosedon');
			$address=$this->input->post('address');
			$description=$this->input->post('description');
			$location=$this->input->post('location');
			$latitude=$this->input->post('latitude');
			$longitude=$this->input->post('longitude');
			$contactno=$this->input->post('contactno');
			$facebookpage=$this->input->post('facebookpage');
			$twitterpage=$this->input->post('twitterpage');
			$website=$this->input->post('website');
			$email=$this->input->post('email');
			$format=$this->input->post('format');
			if($this->store_model->editindividualstore($id,$name,$city,$brand,$address,$location,$latitude,$longitude,$contactno,$email,$format,$offer,$workinghoursfrom,$workinhhoursto,$shopclosedon,$description)==0)
			$data['alerterror']=" Individual Store could not be Updated.";
			else
			$data['alertsuccess']="Individual Store Updated Successfully.";
			
			$data['table']=$this->store_model->viewindividualstore();
			$data['redirect']="site/viewindividualstore";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
	function deletestoreinmall()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->store_model->deletestoreinmall($this->input->get('id'));
		$data['table']=$this->store_model->viewstoreinmall();
		$data['page']='viewstoreinmall';
		$data['title']='View Stores in mall';
		$this->load->view('template',$data);
	}
	function deleteindividualstore()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->store_model->deleteindividualstore($this->input->get('id'));
		$data['table']=$this->store_model->viewindividualstore();
		$data['page']='viewindividualstore';
		$data['title']='View Individual Stores';
		$this->load->view('template',$data);
	}
    
    //filters
    function filterstorebyofferid()
    {
		$id=$this->input->get_post('id');
        $this->store_model->filterstorebyofferid($this->input->get_post('id'));
        
    
    }
    function filterbrandbycategoryid()
    {
		$id=$this->input->get_post('id');
        $this->brand_model->filterbrandbycategoryid($this->input->get_post('id'));
        
    
    }
    
    
    public function getstore($id)
    {
   $data=$this->store_model->getstore($id);
        
        if($data!="No Store"){
         $options = array("Please Select");
        foreach ( $data as $data1 ) {
        $options[$data1->id] = $data1->name;
        }
          //  print_r($options);
//       echo "hdoiwhdawISAHDSHA";
    
            
             echo "<div class='form-group'>
                <label class='col-sm-2 control-label'>Store Name</label>
                <div class='col-sm-4'>";
              
                  
                echo form_dropdown('storeid',$options,'id="select1"  class="form-control populate placeholder select2-                      offscreen"');
                    
              
                    

               // echo form_dropdown('data',$options,set_value('id'),"id='select1' onChange='changechapter()' class='form-control populate placeholder select2-offscreen'");

              
               echo "</div>
                </div>";
        }
        else{
        echo "<div class='form-group'>
                <label class='col-sm-2 control-label'>Store Name</label>
                <div class='col-sm-4'>No Store for This Brand";

              
               echo "</div>
                </div>";
        
        
        
        }
    }
    
    //Shop 
    
	function viewshop()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['table']=$this->shop_model->viewshop();
		$data['page']='viewshop';
		$data['title']='View Shop';
		$this->load->view('template',$data);
	}
        
     public function createshop()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createshop';
		$data[ 'title' ] = 'Create Shop';
        $data['user']=$this->user_model->getuserdropdown();
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$this->load->view( 'template', $data );	
	}
	function createshopsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('alias','Alias','trim');
		$this->form_validation->set_rules('status','status','trim');
		$this->form_validation->set_rules('metatitle','metatitle','trim');
		$this->form_validation->set_rules('metadescription','metadescription','trim');
		$this->form_validation->set_rules('banner','banner','trim');
		$this->form_validation->set_rules('bannerdescription','bannerdescription','trim');
		$this->form_validation->set_rules('defaulttax','defaulttax','trim');
		$this->form_validation->set_rules('user','user','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='createshop';
			$data['title']='Create New Shop';
            $data['user']=$this->user_model->getuserdropdown();
            $data[ 'status' ] =$this->user_model->getstatusdropdown();
			$this->load->view('template',$data);
		}
		else
		{
			$name=$this->input->post('name');
			$alias=$this->input->post('alias');
			$status=$this->input->post('status');
			$metatitle=$this->input->post('metatitle');
			$metadescription=$this->input->post('metadescription');
			$bannerdescription=$this->input->post('bannerdescription');
			$defaulttax=$this->input->post('defaulttax');
			$user=$this->input->post('user');
			
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="banner";
			$banner="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$banner=$uploaddata['file_name'];
			}
			if($this->shop_model->create($name,$alias,$status,$metatitle,$metadescription,$banner,$bannerdescription,$defaulttax,$user)==0)
			$data['alerterror']="New Shop could not be created.";
			else
			$data['alertsuccess']="Shop created Successfully.";
			
			$data['table']=$this->shop_model->viewshop();
			$data['redirect']="site/viewshop";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
		}
	}
    
    function editshop()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['before']=$this->shop_model->beforeedit($this->input->get('id'));
        $data[ 'status' ] =$this->user_model->getstatusdropdown();
        $data['user']=$this->user_model->getuserdropdown();
		$data['page']='editshop';
		$data['title']='Edit Shop';
		$this->load->view('template',$data);
	}
	function editshopsubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('alias','Alias','trim');
		$this->form_validation->set_rules('status','status','trim');
		$this->form_validation->set_rules('metatitle','metatitle','trim');
		$this->form_validation->set_rules('metadescription','metadescription','trim');
		$this->form_validation->set_rules('banner','banner','trim');
		$this->form_validation->set_rules('bannerdescription','bannerdescription','trim');
		$this->form_validation->set_rules('defaulttax','defaulttax','trim');
		$this->form_validation->set_rules('user','user','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['before']=$this->store_model->beforeedit($this->input->post('id'));
            $data[ 'status' ] =$this->user_model->getstatusdropdown();
//			$data['page2']='block/eventblock';
			$data['page']='editshop';
			$data['title']='Edit Shop';
			$this->load->view('template',$data);
		}
		else
		{
			$id=$this->input->post('id');
			$name=$this->input->post('name');
			$alias=$this->input->post('alias');
			$status=$this->input->post('status');
			$metatitle=$this->input->post('metatitle');
			$metadescription=$this->input->post('metadescription');
			$bannerdescription=$this->input->post('bannerdescription');
			$defaulttax=$this->input->post('defaulttax');
			$user=$this->input->post('user');
            
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="banner";
			$banner="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$banner=$uploaddata['file_name'];
			}
            
            if($banner=="")
            {
            $banner=$this->shop_model->getbannerbyid($id);
               // print_r($image);
                $banner=$banner->bannername;
            }
			if($this->shop_model->edit($id,$name,$alias,$status,$metatitle,$metadescription,$banner,$bannerdescription,$defaulttax,$user)==0)
			$data['alerterror']="shop Editing was unsuccesful";
			else
			$data['alertsuccess']="shop edited Successfully.";
			
			$data['redirect']="site/viewshop";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
    
    
	function changeshopstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->shop_model->changeshopstatus($this->input->get('id'));
		$data['table']=$this->shop_model->viewshop();
		$data['alertsuccess']="Status Changed Successfully";
		$data['redirect']="site/viewshop";
        $this->load->view("redirect",$data);
	}
    
	function deleteshop()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->shop_model->deleteshop($this->input->get('id'));
		$data['table']=$this->shop_model->viewshop();
		$data['alertsuccess']="shop Deleted Successfully";
		$data['page']='viewshop';
		$data['title']='View shops';
		$this->load->view('template',$data);
	}
}
?>