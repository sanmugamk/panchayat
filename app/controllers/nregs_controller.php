<?php
	class NregsController extends AppController{
		var $uses = array('NregsRegistration', 'Hamlet', 'Jobcard', 'NregsStock', 'NmrRoll','Attendance','Workdetail','AttendanceRegister');
		function beforeFilter(){
			parent::beforeFilter();
		}
		function index(){
			
		}
		function newregistration(){
			$hamlet_op = $this->Hamlet->find('list', array(
				'fields' => array('Hamlet.hamlet_code')
			));
			$this->set(compact('hamlet_op'));
			if(!empty($this->data)){
				$this->NregsRegistration->set($this->data);
				if($this->NregsRegistration->validates()){
					if($this->NregsRegistration->save($this->data)){
						$stock = $this->NregsStock->findById('1');
						$stock['NregsStock']['item_quantity'] = (int)($stock['NregsStock']['item_quantity']) - 1;
						$this->NregsStock->save($stock);
						$this->Session->setFlash(__($GLOBALS['flash_messages']['added'], true));    
		        $this->redirect(array('action'=>'registrationindex'));
					}else{
						$this->Session->setFlash(__($GLOBALS['flash_messages']['add_failed'], true));
						$this->redirect(array('action'=>'registrationindex')); 
					}
				}
			}
		}
		function addjobcard(){
			if(!empty($this->data)){
				$this->Jobcard->set($this->data);
				if($this->Jobcard->validates()){
					if($this->Jobcard->save($this->data)){
						$stock = $this->NregsStock->findById($this->data['Jobcard']['nregs_stock_id']);
						$stock['NregsStock']['item_quantity'] = (int)($stock['NregsStock']['item_quantity']) + (int)($this->data['Jobcard']['jobcard_quantity']);
						$this->NregsStock->save($stock);
						$this->Session->setFlash(__($GLOBALS['flash_messages']['added'], true));    
		        $this->redirect(array('action'=>'jobcardindex'));
					}else{
						$this->Session->setFlash(__($GLOBALS['flash_messages']['add_failed'], true));
						$this->redirect(array('action'=>'jobcardindex'));
					}
				}
			}
		}
		function registrationindex(){
			$this->paginate = array(
				'conditions' => array('NregsRegistration.application_date BETWEEN ? AND ?' => array($this->Session->read('User.acc_opening_year'), $this->Session->read('User.acc_closing_year')),),
				'order' => 'NregsRegistration.application_date DESC',
				'contain' => array('Hamlet')
			);
			$nregs_reg = $this->paginate('NregsRegistration');
			$this->set(compact('nregs_reg'));
		}
		function editregistration($id){
			if(!empty($id)){
				$hamlets = $this->Hamlet->find('all');
				$this->set(compact('hamlets'));
				$this->NregsRegistration->id=$id;
	      if(empty($this->data)) {
	        $this->data = $this->NregsRegistration->read();
	      }else{
	      	$this->NregsRegistration->set($this->data);
					if($this->NregsRegistration->validates()){
		        if($this->NregsRegistration->save($this->data)){
		          $this->Session->setFlash(__($GLOBALS['flash_messages']['edited'], true));    
		          $this->redirect(array('action'=>'registrationindex'));
		        }else{
							$this->Session->setFlash(__($GLOBALS['flash_messages']['edit_failed'], true));
							$this->redirect(array('action'=>'registrationindex'));
						}
					}  
				}      
	    }else {
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'registrationindex'));
			}
		}
		function view($id){
			if(!empty($id)){
				$this->NregsRegistration->id=$id;
	      if(empty($this->data)) {
	        $registration_detail = $this->NregsRegistration->find('first', array(
	        	'conditions' => array('NregsRegistration.id' => $id)
					));
					$hamlets = $this->Hamlet->find('all');
					$this->set(compact('hamlets', 'registration_detail'));
	      }
	    }else {
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'registrationindex'));
			}
		}
		function deleteregistration($id){
			if(!empty($id)){
				$this->NregsRegistration->delete($id);
				$stock = $this->NregsStock->findById('1');
				$stock['NregsStock']['item_quantity'] = (int)($stock['NregsStock']['item_quantity']) + 1;
				$this->NregsStock->save($stock);
				$this->Session->setFlash(__($GLOBALS['flash_messages']['deleted'], true));
				$this->redirect(array('action'=>'registrationindex'));
			}else {
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'registrationindex'));
			}
		}
		function jobcardindex(){
			$this->paginate = array(
				'conditions' => array('Jobcard.jobcard_date BETWEEN ? AND ?' => array($this->Session->read('User.acc_opening_year'), $this->Session->read('User.acc_closing_year')),),
				'order' => 'Jobcard.jobcard_date DESC',
			);
			$nregs_jobcard = $this->paginate('Jobcard');
			$this->set(compact('nregs_jobcard'));
		}
		function editjobcard($id){
			if(!empty($id)){
				$this->Jobcard->id=$id;
	      if(empty($this->data)) {
	        $this->data = $this->Jobcard->read();
	      }else{
	      	$old_data = $this->Jobcard->findById($this->data['Jobcard']['id']);
					$jobcard_to_change = 0;
					$flag = 0;
					if((int)$old_data['Jobcard']['jobcard_quantity'] > (int)$this->data['Jobcard']['jobcard_quantity']){
						$jobcard_to_change = (int)$old_data['Jobcard']['jobcard_quantity'] - (int)$this->data['Jobcard']['jobcard_quantity'];
						$flag =0;						
					}elseif((int)$old_data['Jabcard']['jobcard_quantity'] < (int)$this->data['Jabcard']['jobcard_quantity']){
						$jobcard_to_change = (int)$this->data['Jobcard']['jobcard_quantity'] - (int)$old_data['Jobcard']['jobcard_quantity'];
						$flag =1;
					}
					$this->Jobcard->set($this->data);
					if($this->Jobcard->validates()){
						if($this->Jobcard->save($this->data)){
							if($jobcard_to_change > 0){
								$nrgsstock = $this->NregsStock->findById('1');
								if($flag==0){
									$nrgsstock['NregsStock']['item_quantity'] =(int)$nrgsstock['NregsStock']['item_quantity'] - $jobcard_to_change; 
								}else{
								  $nrgsstock['NregsStock']['item_quantity'] =(int)$nrgsstock['NregsStock']['item_quantity'] + $jobcard_to_change;	
								}
								$this->NregsStock->save($nrgsstock);
							}
							$this->Session->setFlash(__($GLOBALS['flash_messages']['edited'], true));    
		          $this->redirect(array('action'=>'jobcardindex'));
						}else{
							$this->Session->setFlash(__($GLOBALS['flash_messages']['edit_failed'], true));
							$this->redirect(array('action'=>'jobcardindex'));
						}
					}
				}
	    }else{
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'jobcardindex'));
			}
		}
		function deletejobcard($id, $quantity){
			if(!empty($id) && !empty($quantity)){
				$this->Jobcard->delete($id);
				$stock = $this->NregsStock->findById('1');
				$stock['NregsStock']['item_quantity'] = (int)($stock['NregsStock']['item_quantity']) - (int)($quantity);
				$this->NregsStock->save($stock);
				$this->Session->setFlash(__($GLOBALS['flash_messages']['deleted'], true));
				$this->redirect(array('action'=>'jobcardindex'));
			}else {
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'jobcardindex'));
			}
		}
		function nmrrolls(){
			if(!empty($this->data)){
				$this->NmrRoll->set($this->data);
				if($this->NmrRoll->validates()){
					if($this->NmrRoll->save($this->data)){
						$this->Session->setFlash(__('Nmr Rolls added', true));
						$this->redirect(array('action'=>'nmr_roll_index'));
					}else{
						$this->Session->setFlash(__($GLOBALS['flash_messages']['add_failed'], true));
						$this->redirect(array('action'=>'nmr_roll_index'));
					}
				}
			}
		}
		function edit_nmrrolls($id){
			if(!empty($id)){
				$this->NmrRoll->id=$id;
	      if(empty($this->data)) {
	        $this->data = $this->NmrRoll->read();
	      }else{
	      	$this->NmrRoll->set($this->data);
					if($this->NmrRoll->validates()){
		      	if($this->NmrRoll->save($this->data)){
		          $this->Session->setFlash(__($GLOBALS['flash_messages']['edited'], true));    
		          $this->redirect(array('action'=>'nmr_roll_index'));       
		        }else{
							$this->Session->setFlash(__($GLOBALS['flash_messages']['add_failed'], true));
							$this->redirect(array('action'=>'nmr_roll_index'));
						}
					}
				}
			}else{
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'nmr_roll_index'));
			}
		}
		function nmr_roll_index(){
			$this->paginate = array(
				'order' => 'NmrRoll.role_date',
			);
			$nregs_rolls = $this->paginate('NmrRoll');
			$this->set(compact('nregs_rolls'));
		}
		function add_workdetails(){
			if(!empty($this->data)){
				$this->Workdetail->set($this->data);
				if($this->Workdetail->validates()){
					if($this->Workdetail->save($this->data)){
						$this->Session->setFlash(__($GLOBALS['flash_messages']['added'], true));
						$this->redirect(array('action'=>'index_workdetails'));
					}else{
						$this->Session->setFlash(__($GLOBALS['flash_messages']['add_failed'], true));
						$this->redirect(array('action'=>'index_workdetails'));
					}
				}
			}
		}
		function edit_workdetails($id){
			if(!empty($id)){
				if(empty($this->data)){
					$this->Workdetail->id = $id;
					$this->data = $this->Workdetail->read();
				}else{
					$this->Workdetail->set($this->data);
					if($this->Workdetail->validates()){
						if($this->Workdetail->save($this->data)){
							$this->Session->setFlash(__($GLOBALS['flash_messages']['edited'], true));
							$this->redirect(array('action'=>'index_workdetails'));
						}else{
							$this->Session->setFlash(__($GLOBALS['flash_messages']['edit_failed'], true));
							$this->redirect(array('action'=>'index_workdetails'));
						}
					}
				}
			}else{
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'index_workdetails'));
			}
		}
		function delete_workdetails($id){
			if(!empty($id)){
				$this->Workdetail->delete($id);
				$this->Session->setFlash(__($GLOBALS['flash_messages']['deleted'], true));
				$this->redirect(array('action'=>'index_workdetails'));
			}else {
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'index_workdetails'));
			}
		}
		function index_workdetails() {
			$this->paginate = array(
				'conditions' => array('Workdetail.year BETWEEN ? AND ?' => array($this->Session->read('User.acc_opening_year'), $this->Session->read('User.acc_closing_year')),),
				'order' => 'Workdetail.year DESC',
			);
			$workdetails = $this->paginate('Workdetail');
			$this->set(compact('workdetails'));		
		}
		function attendance(){
			$work_details = $this->Workdetail->find('list', array(
				'fields' => array('Workdetail.name_of_work')
			));
			$this->set(compact('work_details'));
		  if(!empty($this->data)){
				if($this->AttendanceRegister->saveAll($this->data)){
					$this->Session->setFlash(__($GLOBALS['flash_messages']['added'], true));
					$this->redirect(array('action'=>'attendance_index'));
				}
			}
		}
    function attendance_index(){
      $this->paginate = array(
        'conditions' => array('AttendanceRegister.to_date BETWEEN ? AND ?' => array($this->Session->read('User.acc_opening_year'), $this->Session->read('User.acc_closing_year')),),
        'order' => 'AttendanceRegister.id DESC',
        'contain' => array('Workdetail')
      );
      $attendances = $this->paginate('AttendanceRegister');
      $this->set(compact('attendances'));   
    }
    function get_jobcard(){
      $this->layout = false;
      $jobcard = $this->NregsRegistration->find('all', array(
        'fields' => array('NregsRegistration.job_card_number'),
        'conditions' => array('NregsRegistration.family_number' => $_POST['family_id']),
      ));
       echo json_encode($jobcard);
       exit;  
    }
		function autofill_attendance(){
			$this->layout = false;
			$details = $this->NregsRegistration->find('first', array(
				'conditions' => array('NregsRegistration.job_card_number' => $_POST['jobcard_no'])
			));
			 echo json_encode($details);
			 exit;	
		}
		function hundred_days_check(){
			$this->layout = false;
			$no_of_days = $this->Attendance->find('first', array(
				'conditions' => array('Attendance.family_number' => $_POST['family_id']),
				'fields' => array(
					'SUM(Attendance.no_of_days_worked) AS no_of_days_worked'
				),
			));
			echo json_encode($no_of_days);
			exit;
		}
    function payment(){
      if(empty($this->data)){
        $attendance = $this->AttendanceRegister->find('all', array(
          'conditions' => array('AttendanceRegister.id' => $this->params['named']['attendance_id']),
          'contain' => array('Attendance' => array(
              'fields' => array(
                'SUM(Attendance.no_of_days_worked) AS no_of_days_worked'
              ),
            ))
        ));
        $this->set(compact('attendance'));
      }else{
				$attendance = $this->AttendanceRegister->find('first', array(
					'conditions' => array('AttendanceRegister.id' => $this->data['Payment']['attendance_register_id']),
					'contain' => false,
				));
				$attendance['AttendanceRegister']['payment_status'] = $this->data['Payment']['payment_status'];
				$attendance['AttendanceRegister']['amount_per_head'] = $this->data['Payment']['amount_per_head'];
				$attendance['AttendanceRegister']['amount_paid'] = $this->data['Payment']['amount_paid'];
				$this->AttendanceRegister->save($attendance);
				$this->Session->setFlash(__($GLOBALS['flash_messages']['added'], true));
				$this->redirect(array('action'=>'attendance_index'));
        
      }
    }
		function view_attendance($id){
			if(!empty($id)){
				$this->AttendanceRegister->id=$id;
				$this->data = $this->AttendanceRegister->read();
				$result = $this->Attendance->find('all', array(
					'conditions' => array('Attendance.attendance_register_id' => $this->data['AttendanceRegister']['id']),
					'order' => 'Attendance.family_number ASC',
					'contain' => array()
				));
				$workers = array();
				foreach($result as $key => $value){
					$row = $this->NregsRegistration->find('first', array(
						'conditions' => array('NregsRegistration.job_card_number' => $value['Attendance']['job_card_number'])
					));
					$workers[$key] = $result[$key]['Attendance'];
					$workers[$key]['name'] = $row['NregsRegistration']['name'];
					$workers[$key]['father_or_husband_name'] = $row['NregsRegistration']['father_or_husband_name'];
				}
				$this->set(compact('workers'));
			}else{
				$this->Session->setFlash(__($GLOBALS['flash_messages']['invalid_operation'], true));
				$this->redirect(array('action'=>'attendance_index'));
			}
		}
	}
?>