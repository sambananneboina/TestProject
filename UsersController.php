<?php
App::uses('AppController', 'Controller');


/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	var $helpers = array('Html', 'Form','Csv');
 
	

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$user=$this->Session->read("user");
		if($user['role']=="client"){
			$this->Paginator->settings = array(
			"conditions"=>array("User.client_id"=>$user['client_id'])
			);			
		}
		//echo "<pre>";print_r($this->Paginator->paginate());exit;
		$this->set('users', $this->Paginator->paginate());
	}

	
	 public function admin_user_risk_reports(){
		
	 }
	 
	 
	   public function admin_user_risk_ranking_report(){
	    $ranking_report_settings = $this->risk_ranking();
	    $this->paginate = $ranking_report_settings;
		//echo "<pre>";print_r( $ranking_report_settings);exit;
	    $this->set('get_risks',$this->paginate('Risk'));
		}
		
		function risk_ranking(){
	    $this->loadModel('Risk');
	    $this->loadModel('RiskEvent');
		$this->loadModel('RiskTreatment');
		$this->loadModel('Treatment');
		$this->loadModel('Event');
		$this->loadModel('RiskClassification');
		$user_id = $this->Session->read("user.id");
		$get_risks =    array('joins'=>array(
										array('table' => 'risk_events',
										'alias' => 'RiskEvent',
										'type' => 'LEFT',
										'conditions' => array('RiskEvent.risk_id = Risk.id'),
										),
										array('table' => 'risk_treatments',
										'alias' => 'RiskTreatment',
										'type' => 'LEFT',
										'conditions' => array('RiskTreatment.risk_id = Risk.id'),
										),
										array('table' => 'treatments',
										'alias' => 'Treatment',
										'type' => 'LEFT',
										'conditions' => array('Treatment.id = RiskTreatment.treatment_id','Treatment.user_id' =>$user_id),
										),
										array('table' => 'events',
										'alias' => 'Event',
										'type' => 'LEFT',
										'conditions' => array('Event.id = RiskEvent.event_id','Event.user_id' => $user_id),
										),
										array('table' => 'risk_classifications',
										'alias' => 'RiskClassification',
										'type' => 'LEFT',
										'conditions' => array('RiskClassification.id = Risk.in_level_2'),
									 ),	
									 ), 
		'fields'=>array('Risk.risk_score,Risk.title,User.username,Event.title,Treatment.title,RiskClassification.item_title'),
		'conditions'=>array('Risk.user_id'=>$user_id),
		'order'=>array('Risk.risk_score'=>'desc'),
									);
	     return $get_risks;    exit;
	    }
		
		
		 public function admin_risk_ranking_report_exports(){
         $ranking_report = $this->risk_ranking();
		 $risk_data = $this->Risk->find(all,$ranking_report);
		 $this->set('get_risks',$risk_data);
		 $this->layout = "";
		 $this->autoLayout = false;
	     Configure::write('debug', '0');
	 }
	 
	 
	 
	 function risk_score_by_user(){
	    $this->loadModel('Risk');
	    $this->loadModel('RiskEvent');
		$this->loadModel('RiskTreatment');
		$this->loadModel('Treatment');
		$this->loadModel('Event');
		$this->loadModel('RiskClassification');
		$user_id = $this->Session->read("user.id");
		$get_risks =    array('joins'=>array(
										array('table' => 'risk_events',
										'alias' => 'RiskEvent',
										'type' => 'LEFT',
										'conditions' => array('RiskEvent.risk_id = Risk.id'),
										),
										array('table' => 'risk_treatments',
										'alias' => 'RiskTreatment',
										'type' => 'LEFT',
										'conditions' => array('RiskTreatment.risk_id = Risk.id'),
										),
										array('table' => 'treatments',
										'alias' => 'Treatment',
										'type' => 'LEFT',
										'conditions' => array('Treatment.id = RiskTreatment.treatment_id','Treatment.user_id' =>$user_id),
										),
										array('table' => 'events',
										'alias' => 'Event',
										'type' => 'LEFT',
										'conditions' => array('Event.id = RiskEvent.event_id','Event.user_id' => $user_id),
										),
										array('table' => 'risk_classifications',
										'alias' => 'RiskClassification',
										'type' => 'LEFT',
										'conditions' => array('RiskClassification.id = Risk.in_level_2'),
									 ),	
									 ),
			'fields'=>array('Risk.risk_score,Risk.title,User.username,Event.title,Treatment.title,RiskClassification.item_title'),
			'conditions'=>array('Risk.user_id'=>$user_id,'User.id'=>$user_id),
			'order'=>array(
            'User.username' => 'ASC',
            'Risk.risk_score' => 'DESC',
         
        ),
	);
								 
	     return $get_risks;    exit;
	    }	
		
		
	public function admin_risk_score_by_user_report(){
	$ranking_report_settings = $this->risk_score_by_user();
	$this->paginate = $ranking_report_settings;
	$this->set('get_risks',$this->paginate('Risk'));
	}
	
	
	public function admin_risk_score_by_user_report_exports(){
		$ranking_report = $this->risk_score_by_user();
		$risk_data = $this->Risk->find(all,$ranking_report);
		$this->set('get_risks',$risk_data);
		$this->layout = "";
		$this->autoLayout = false;
	    Configure::write('debug', '0'); 
	}
	
	
	function risk_overdue_task($conditions){
			$this->loadModel('Risk');
			$this->loadModel('RiskEvent');
			$this->loadModel('RiskTreatment');
			$this->loadModel('Treatment');
			$this->loadModel('Event');
			$this->loadModel('RiskClassification');
			$user_id = $this->Session->read("user.id");
			$get_risks =    array('joins'=>array(
										array('table' => 'risk_events',
										'alias' => 'RiskEvent',
										'type' => 'LEFT',
										'conditions' => array('RiskEvent.risk_id = Risk.id'),
										),
										array('table' => 'risk_treatments',
										'alias' => 'RiskTreatment',
										'type' => 'LEFT',
										'conditions' => array('RiskTreatment.risk_id = Risk.id'),
										),
										array('table' => 'treatments',
										'alias' => 'Treatment',
										'type' => 'LEFT',
										'conditions' => array('Treatment.id = RiskTreatment.treatment_id','Treatment.user_id' =>$user_id),
										),
										array('table' => 'events',
										'alias' => 'Event',
										'type' => 'LEFT',
										'conditions' => array('Event.id = RiskEvent.event_id','Event.user_id' => $user_id),
										),
										array('table' => 'risk_classifications',
										'alias' => 'RiskClassification',
										'type' => 'LEFT',
										'conditions' => array('RiskClassification.id = Risk.in_level_2'),
									 ),	
									 
									 ),
									 	 'conditions'=>$conditions,
						'fields'=>array('Risk.risk_score,Risk.title,Risk.review_date,User.username,Event.title,Treatment.*,RiskClassification.item_title'),
						'conditions'=>array('Risk.user_id'=>$user_id,'User.id'=>$user_id),
						'order'=>array('RiskClassification.item_title'=>'asc'),
									);
	     return $get_risks;    exit; 
	    }
	
	
	public function admin_risk_task_by_user_report(){
		$conditions = array('');
		$ranking_report_settings = $this->risk_overdue_task($conditions);
		$this->paginate = $ranking_report_settings;
		$this->set('get_risks',$this->paginate('Risk'));
	}
	
	public function admin_risk_task_by_user_report_exports(){
		$conditions = array('');
		$ranking_report = $this->risk_overdue_task($conditions);
		$risk_data = $this->Risk->find(all,$ranking_report);
		$this->set('get_risks',$risk_data);
		$this->layout = "";
		$this->autoLayout = false;
		Configure::write('debug', '0'); 
	 }
	
	
	public function admin_risk_overdue_task_by_user_report(){
		$conditions = array('Treatment.status != "Completed"');
		$ranking_report_settings = $this->risk_overdue_task($conditions);
		$this->paginate = $ranking_report_settings;
		$this->set('get_risks',$this->paginate('Risk'));
	}
	
	
	public function admin_risk_overdue_task_by_user_report_exports(){
		$conditions = array('Treatment.status != "Completed"');
		$ranking_report = $this->risk_overdue_task($conditions);
		$risk_data = $this->Risk->find(all,$ranking_report);
		$this->set('get_risks',$risk_data);
		$this->layout = "";
		$this->autoLayout = false;
		Configure::write('debug', '0'); 
	 }
	 
	 
	 public function admin_risk_user_filter_report(){
		if(!empty($this->request->data)) {
			
			$search = $this->request->data['Risk']['search'];
			$conditions = array('OR' => array(
			'User.username LIKE' => '%'.$search.'%',
			'RiskClassification.item_title LIKE' => '%'.$search.'%',
			'Risk.title LIKE' => '%'.$search.'%',
			'Event.title LIKE' => '%'.$search.'%',
			'Treatment.title LIKE' => '%'.$search.'%',
			'Treatment.status LIKE' => '%'.$search.'%',
			'Treatment.priority LIKE' => '%'.$search.'%',
			'Risk.review_date LIKE' => '%'.$search.'%',
		));
		$this->set('search',$search);
		
		}else{
		$conditions = array('');
		}
	$ranking_report_settings = $this->risk_overdue_task($conditions);
	$this->paginate = $ranking_report_settings;
	$this->set('get_risks',$this->paginate('Risk'));
	}
	
	 
	 public function admin_risk_user_filter_report_exports($search){
		 if(!empty($search)) {
			 
			$conditions = array('OR' => array(
			'User.username LIKE' => '%'.$search.'%',
			'RiskClassification.item_title LIKE' => '%'.$search.'%',
			'Risk.title LIKE' => '%'.$search.'%',
			'Event.title LIKE' => '%'.$search.'%',
			'Treatment.title LIKE' => '%'.$search.'%',
			'Treatment.status LIKE' => '%'.$search.'%',
			'Treatment.priority LIKE' => '%'.$search.'%',
			'Risk.review_date LIKE' => '%'.$search.'%',
		));
		$this->set('search',$search);
		}else{
		 $conditions = array('');
		}
	 
		$ranking_report = $this->risk_overdue_task($conditions);
		$risk_data = $this->Risk->find(all,$ranking_report);
		$this->set('get_risks',$risk_data);
		$this->layout = "";
		$this->autoLayout = false;
		Configure::write('debug', '0'); 
	 } 
	 
	 
	 function risk_score_client_government_category(){
			$this->loadModel('Risk');
			$this->loadModel('RiskEvent');
			$this->loadModel('RiskTreatment');
			$this->loadModel('Treatment');
			$this->loadModel('Event');
			$this->loadModel('RiskClassification');
			$user_id = $this->Session->read("user.id");
			$get_risks =    array('joins'=>array(
										array('table' => 'risk_events',
										'alias' => 'RiskEvent',
										'type' => 'LEFT',
										'conditions' => array('RiskEvent.risk_id = Risk.id'),
										),
										array('table' => 'risk_treatments',
										'alias' => 'RiskTreatment',
										'type' => 'LEFT',
										'conditions' => array('RiskTreatment.risk_id = Risk.id'),
										),
										array('table' => 'treatments',
										'alias' => 'Treatment',
										'type' => 'LEFT',
										'conditions' => array('Treatment.id = RiskTreatment.treatment_id','Treatment.user_id' =>$user_id),
										),
										array('table' => 'events',
										'alias' => 'Event',
										'type' => 'LEFT',
										'conditions' => array('Event.id = RiskEvent.event_id','Event.user_id' => $user_id),
										),
										array('table' => 'risk_classifications',
										'alias' => 'RiskClassification',
										'type' => 'LEFT',
										'conditions' => array('RiskClassification.id = Risk.in_level_2'),
									 ),	
									 ),
		'fields'=>array('Risk.risk_score,Risk.title,User.username,Event.title,Treatment.title,RiskClassification.item_title'),
		'conditions'=>array('Risk.user_id'=>$user_id,'User.id'=>$user_id),
		'order'=>array('RiskClassification.item_title'=>'asc'),
								);
	     return $get_risks;    exit;
	    }
		
		
	public function admin_client_government_category_report(){
	$ranking_report_settings = $this->risk_score_client_government_category();
	$this->paginate = $ranking_report_settings;
	$this->set('get_risks',$this->paginate('Risk'));
	}
	
	
	 public function admin_client_government_category_report_exports(){
		$ranking_report = $this->risk_score_client_government_category();
		$risk_data = $this->Risk->find(all,$ranking_report);
		$this->set('get_risks',$risk_data);
		$this->layout = "";
		$this->autoLayout = false;
		Configure::write('debug', '0'); 
	 }
/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
 
	public function admin_login() {
	$this->layout='login';
	if ($this->request->is('post')) {   
	$username =$this->data['User']['username'];
	$password =$this->data['User']['password'];
	//$password_hash =Security::hash($this->request->data['User']['password'], 'sha256', true);
	$password = md5($this->request->data['User']['password']);
		//$password_hash =Security::hash($this->request->data['User']['password_hash'], 'sha256', true);
	$this->loadModel('User');
	$user = $this->User->find('all',array('conditions'=>array('username'=>$username,'password'=>$password),array('recursive'=>-1)));
   //echo "<pre>";print_r($user);  exit;

	if(!empty($user)){
	$this->Session->write('user',$user[0]['User']);
	if($user[0]['User']['role']=='admin'){
		$this->redirect('/admin');
	}
	else if($user[0]['User']['role']=='agent'){
		$this->redirect('/admin/clients');
	}
	else{
		$this->redirect('/admin');
	}
	}else{
		$this->Session->setFlash('Username or Password do not match');  	
	}
	}	
	}
	
	
 
 
	public function admin_add() {
		
		  $this->loadModel('SharedPosition');
		$this->loadModel('Client');
		if ($this->request->is('post')) {
			//echo "<pre>";print_r($this->request->data);exit;
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-1'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		
		
		
		$this->set('inlevel1',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-2'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel2',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-3'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel3',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-4'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel4',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-5'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel5',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-6'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel6',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-7'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel7',$data);
		$sharedpositions = $this->SharedPosition->find('list');
		
		
		
		$clients = $this->Client->find('list');
		$this->set(compact('clients','sharedpositions')); 
		
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->loadModel('SharedPosition');
		$this->loadModel('Client');
		
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-1'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel1',$data);
		
		 
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-2'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel2',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-3'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel3',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-4'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel4',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-5'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel5',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-6'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel6',$data);
		
		$this->loadModel('RiskClassification');
		$data=$this->RiskClassification->find('list',array('conditions'=>array('RiskClassification.type'=>'In-level-7'),'fields'=>array('RiskClassification.id','RiskClassification.title')));
		$this->set('inlevel7',$data);
		$sharedpositions = $this->SharedPosition->find('list');
		$clients = $this->User->Client->find('list');
		$this->set(compact('clients','sharedpositions')); 
	}

	
	public function admin_agents_edit($id = null){
		
		$this->loadModel('SharedPosition');
		$this->loadModel('Client');
		
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		
		
		
	}
/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('The user has been deleted.'));
		} else {
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function admin_dashboard(){
		
		$this->loadModel('Client');
		$client_details = $this->Session->read('user.client_id');
        $get_client_details = $this->Client->find('all',array('conditions'=>array('Client.id'=>$client_details)));
		
	//echo "<pre>"; print_r($get_client_details); exit;
	    $this->set('client_details',$get_client_details);
		
		
		
		$this->loadModel('Risk');	
		$this->Risk->recursive = 0;
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		//echo $client_id;exit;
		 
		if($user=="user"){
			$risks = $this->Risk->find('all',array(
			"conditions"=>array("Risk.user_id"=>$user_id))
			); 
			//echo "<pre>";print_r($risks);exit;
			$this->set('risks', $risks);	
		} else if($user=="client"){
		    $risks = $this->Risk->find('all',array('conditions'=>array("Risk.client_id"=>$client_id),'order'=>array("Risk.risk_score DESC"),'limit'=>10));
			//echo "<pre>";print_r($risks);exit;
			$values = array();
			$i = 0; 
			foreach($risks as $risk){
				$values[$i][x] = (int) $risk['Risk']['id'];
				$values[$i][y] = (int) $risk['Risk']['risk_score'];		
				$i++; 
			} 
			//echo "<pre>"; print_r($values);exit;
			
			$vals = json_encode($values);
			//echo $vals; exit;
			$this->set("graph_data", $vals); 
   		
		
		} else{
		$this->set('risks', $options);	
		}
		
		
		
	 $this->loadModel('ClientQuestionsResponse');
	  $this->ClientQuestionsResponse->recursive = 0;
	  //echo "<pre>";print_r($user_id);exit;
	  if($user == "user"){
	  $questions = $this->ClientQuestionsResponse->find('all',array("conditions"=>array("ClientQuestionsResponse.user_id"=>$user_id)));
	 //echo "<pre>";print_r($questions);exit;
	  $this->set('questions',$questions);
	
	      }
	}
	
	public function admin_risk_reporting(){
		$this->loadModel('Risk');
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$this->Paginator->settings = array(
         'conditions' => array("Risk.user_id"=>$user_id,"Risk.client_id"=>$client_id),
         'limit' => 10,
		 'order'=>'Risk.risk_score DESC'
		);
        $risk = $this->Paginator->paginate('Risk');
		$this->set('risks',$risk);
	}
	
	public function admin_risk_csv_report(){
		$this->loadModel('Risk');
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$risk = $this->Risk->find("all",array(
		'conditions' => array("Risk.user_id"=>$user_id,"Risk.client_id"=>$client_id),
		'order'=>'Risk.risk_score DESC'
		));
		
		$this->set('risks',$risk);
		
        $this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug', '0');
	}
	
	public function admin_compliance_due_date(){
	  $this->loadModel('ClientQuestionsResponse');
	  $current_date  =  date("Y-m-d");
	 // echo $current_date; exit;
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$this->Paginator->settings = array(
		'conditions' => array("ClientQuestionsResponse.user_id"=>$user_id,"ClientQuestionsResponse.client_id"=> $client_id,"ClientQuestionsResponse.expiry_date <=" => $current_date),
			'limit' => 10,
			'order'=>'ClientQuestionsResponse.expiry_date DESC'
			);
		
		$client_question_responses = $this->Paginator->paginate('ClientQuestionsResponse');
		$this->set('client_question_responses',$client_question_responses);	
		
     }
	 public function admin_compliance_due_date_csv_report(){
		$this->loadModel('ClientQuestionsResponse');
		$current_date  =  date("Y-m-d");
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$client_question_response = $this->ClientQuestionsResponse->find("all",array(
		'conditions' => array("ClientQuestionsResponse.user_id"=>$user_id,"ClientQuestionsResponse.client_id"=>$client_id,"ClientQuestionsResponse.expiry_date <=" => $current_date),
		'order'=>'ClientQuestionsResponse.expiry_date DESC'
		));
		
		$this->set('client_question_responses',$client_question_response);
		
        $this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug', '0');
	}
	public function admin_compliance_ranking(){
	  $this->loadModel('ClientQuestionsResponse');
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$this->Paginator->settings = array(
		'conditions' => array("ClientQuestionsResponse.user_id"=>$user_id,"ClientQuestionsResponse.client_id"=> $client_id),
			'limit' => 10,
			'order'=>'ClientQuestionsResponse.weighting DESC'
			);
		$client_question_responses = $this->Paginator->paginate('ClientQuestionsResponse');
		$this->set('client_question_responses',$client_question_responses);	
		
     }
	  public function admin_compliance_ranking_csv_report(){
		$this->loadModel('ClientQuestionsResponse');
		$user = $this->Session->read("user.role");
		$user_id = $this->Session->read("user.id");
		$client_id = $this->Session->read("user.client_id");
		
		$client_question_response = $this->ClientQuestionsResponse->find("all",array(
		'conditions' => array("ClientQuestionsResponse.user_id"=>$user_id,"ClientQuestionsResponse.client_id"=>$client_id),
		'order'=>'ClientQuestionsResponse.weighting DESC'
		));
		
		$this->set('client_question_responses',$client_question_response);
		
        $this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug', '0');
	}
	
	
	
	
	function admin_logout() {

	
		$this->Session->delete('Admin');
		$this->Session->delete('Manager');
		$this->Session->delete('Client');
		$this->Session->delete('User');
		$this->Session->delete('Agent');		
		$this->Session->destroy();
		$this->redirect('/admin/login');

	}
	
	
	public function admin_profile() {
		 
		$id = $this->Session->read('user.id');
		if (!$this->User->exists($id)) {

			throw new NotFoundException(__('Invalid user'));

		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->User->save($this->request->data)) {

				$this->Session->setFlash(__('The user has been saved.'));
				
				if($this->Session->read("user.role")=='user'){				
					//return $this->redirect('/admin');
				}
				return $this->redirect('/admin');
				//return $this->redirect(array('action' => 'index'));

			} else {

				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));

			}

		} else {

			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));

			$this->request->data = $this->User->find('first', $options);

		}
		 
		 
		/*$id = $this->Session->read('user.id');
		$get=$this->User->find('all',array('conditions'=>array('User.id'=>$uid)));
		$this->set('currentuser',$get);*/
	 } 
	 
	 
	 
	function admin_changepassword() {
		 
			$session=$this->Session->read();
            
			if ($this->request->is('post')) {
				$id=$this->Session->read('user.id');
				$user=$this->User->find('first',array('conditions' => array('User.id' => $id)));
			
			
			$old_pass=$user['User']['password']; 
			 
			$error=array();			
			
			 $old_pass_enter=md5($this->request->data['User']['password']);
			 $new_pass_enter=md5($this->request->data['User']['password_update']);
			 $confirm_pass_enter=md5($this->request->data['User']['password_confirm_update']);
			 if($this->request->data['User']['password']=='')
			 {
				 $error['password']="Invalid Old Password";
			 }
			 if($old_pass_enter!=$old_pass)
			 {
				 $error['password']="Old and New password should be match";
			 }
			if($this->request->data['User']['password_update']=='')
			{
				$error['password_update']="New Password Should not be blank";
			 }
			 if($this->request->data['User']['password_confirm_update']=='')
			{
				$error['password_confirm_update']="confirm Password Should not be blank";
			 }
			 if($new_pass_enter!=$confirm_pass_enter)
			 {
				 $error['password_update']="New Password and confirm password should be match";
			 }
			  
			if(!empty($error)) {    
               $this->set('errors', $error);
			 } 
			  else  
			  {
			   $this->request->data['User']['password']=md5($this->request->data['User']['password_update']);  
				$this->request->data['User']['id']=$id;
				$this->User->save($this->request->data);
				$this->Session->setFlash('Password changed.');
				 $this->redirect(array('controller'=>'Users','action' => 'admin_profile')); 
				 } 
					  	
			
			
			}
			
    }
	 
	 
	 public function admin_agents() {
		$this->User->recursive = 0;
		$user=$this->Session->read("user");
		//echo $user['role']; exit;
		if($user['role']=="admin"){
			$this->Paginator->settings = array(
			"conditions"=>array("User.role"=>'agent')
			);			
		}
		//echo "<pre>";print_r($this->Paginator->paginate());exit;
		$this->set('users', $this->Paginator->paginate());
	}
		
	public function admin_auto_login($id=null){
  	
			if($this->Session->read('user.role')=="agent"||$this->Session->read('user.role')=="client") {   
			 
				$this->loadModel('User');
				$get_client_details = $this->User->find('all',array('conditions'=>array('User.id'=>$id,'User.role'=>array('client','agent')),"fields"=>array("User.*, Client.*")));
				 
			    //echo "<pre>"; print_r($get_client_details); exit;

				if(!empty($get_client_details)){					
				//echo $get_client_details[0]['Client']['agent_id']; exit;
					if($get_client_details[0]['User']['role']=="client"){	
						$this->Session->write('user',$get_client_details[0]['User']);
						$this->Session->write('agent',$get_client_details[0]['Client']['agent_id']); 
						$this->redirect('/admin');
					}
					else
					{
						$this->Session->write('user',$get_client_details[0]['User']);						
						$this->Session->delete('agent');
						$this->redirect('/admin/clients');
					}
				}
			}		
			
			exit;			 
		}
	
		
	}
