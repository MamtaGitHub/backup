<?php

/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array('User', 'Category', 'Term', 'About', 'Privacy');
    public $layout = 'admin';
    public $components = array('Paginator');


  

    public function admin_login() {
        $this->layout = 'login';
        if ($this->Auth->user('userType') == '3') {
            $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        } else if ($this->Auth->user('userType') == '1' && $this->Auth->user('status') == '1') {
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'editArtist',
                'page' => !empty($this->passedArgs['page']) ? $this->passedArgs['page'] : '1',
                $this->Session->read('Auth.User.id'), 'listArtistuser'));
        }

        if ($this->request->is('post')) {
             // 
            if ($this->Auth->login()) {

                  if ($this->Auth->user('userType') == '1' && $this->Auth->user('status') == '1') {
                   

                    $this->redirect(array(
                        'controller' => 'users',
                        'action' => 'editArtist',
                        'page' => !empty($this->passedArgs['page']) ? $this->passedArgs['page'] : '1',
                        $this->Session->read('Auth.User.id'), 'listArtistuser'));


                } elseif($this->Auth->user('userType') == '3') {
                    $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
                }
                else{
                     $this->Session->setFlash('<a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('Invalid email or password').'</span>','default',array('class'=>'alert  alert-danger'));
                   
                }
            } 
            else {
               $this->Session->setFlash(' <a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('Invalid email or password').'</span>','default',array('class'=>'alert  alert-danger'));
            }
        }
        $this->set('layoutTitle', __('Login Form'));
    }

    public function admin_logout() {
        $this->Session->setFlash(__('You are now logged in successfully'), 'default', array(), 'success');
       
        return $this->redirect($this->Auth->logout());
    }
     public function admin_resetPassword(){
            $email = $this->request->data['email'];
            if(empty($email)){
                $this->Session->setFlash('<a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('Please fill email field in order to forgot password').'</span>','default',array('class'=>'alert  alert-danger'));
                            return $this->redirect($this->Auth->logout());
            }
            $user = $this->User->find('first',array('conditions'=>array('User.email'=>$email)));
            //pr( $user); die;
            if(count($user)>0){
                    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $key = array(); //remember to declare $pass as an array
                    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                    for ($i = 0; $i < 8; $i++) {
                            $n = rand(0, $alphaLength);
                            $key[] = $alphabet[$n];
                    }
                    $forgotKey = Security::hash(implode($key), 'md5', true);
                    $this->request->data['User']['forgot_key'] = $forgotKey;
                    $this->User->id = $user['User']['id'];
                    if ($this->User->save($this->request->data)){
                          $link = FULL_BASE_URL.$this->base.'/admin/users/forgotAccount?forgot='.urlencode(base64_encode($user['User']['id'])).'&forgotid='.$forgotKey;
                    $project_name = PROJECT_NAME;
                    $to = $_REQUEST['email'];
                    $from = 'localhost@postman';
                    $subject = 'Forget password';
                    $EmailBody = array('email'=>$_REQUEST['email'],'link'=>$link,'project_name'=>$project_name,'name'=>$name);

                   $emailsend=$this->sendEmail($to,$from,$subject,$EmailBody,'emailTemplate');
                            $this->Session->setFlash('<a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('A link has been sent to your email id, follow that link to reset your password').'</span>','default',array('class'=>'alert  alert-danger'));
                                        return $this->redirect($this->Auth->logout());
                        //  $results = array('mesg'=>"A link has been sent to your email id, follow that link to reset your password",'response'=>'1');
                    } else{
                        $this->Session->setFlash('<a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('Invalid email account').'</span>','default',array('class'=>'alert  alert-danger'));
                                    return $this->redirect($this->Auth->logout());
                    }
            }
            else{
                $this->Session->setFlash('<a href="#" class="close" data-dismiss="alert">&times;</a><span>'.__('Invalid email id').'</span>','default',array('class'=>'alert  alert-danger'));
                            return $this->redirect($this->Auth->logout());
            }
            $this->set('layoutTitle', __('Login Form'));
        }

    public function admin_listUser() {
        $cond = array();
        $cond['User.userType'] = '2';
        $cond['User.social_id'] = '';
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
		'User.phone like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        $users = $this->paginate = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => 'User.id DESC',
        );
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_users');
        }
        $users = $this->paginate('User');
        $this->set(compact('users', 'text'));
//pr($users);die;
        $this->set('layoutTitle', __('List Customers'));
    }
    public function admin_viewUser($id = null) {
        if (!$id) {
            $this->Session->setFlash('Page not found', 'default', array(), 'error');
            return $this->redirect($this->referer());
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('User does not exist', 'default', array(), 'error');
            return $this->redirect($this->referer());
        }
        $users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
        $this->set(compact('users', 'text'));
        if (!empty($users['User']['socialType'])) {
            $this->set('layoutTitle', 'Social User Detail');
        } else {
            echo $this->set('layoutTitle', 'Customers Detail');
        }
    }

    public function admin_deleteUser() {
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->User->delete($id);
            $cond['User.userType '] = '2';
            $cond['User.social_id '] = '';
            $users = $this->paginate = array(
                'conditions' => $cond,
                'order' => 'User.id DESC',
                'limit' => 10,
            );
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_users');
        }
    }

    
    
     public function admin_changeStatus() {
        $id = $_REQUEST['id'];
        $status = $_REQUEST['sts'];
        $this->User->id = $id;
        $data['User']['status'] = $status;
        $this->User->id = $id;
        $cond = array();
        $cond['User.userType'] = '2';
        $cond['User.social_id'] = '';
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
                'User.phone like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->User->save($data);
            $users = $this->paginate = array(
                'conditions' => $cond,
                'limit' => 10,
                'order' => 'User.id DESC',
            );
          //$text = $this->passedArgs['text'];
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_users');
        }
    }

    public function admin_listSocialUser() {
        $cond = array();
        $cond['User.userType '] = '2';
        $cond['User.social_id !='] = '';
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
				'User.phone like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
                
            );
            $text = $this->passedArgs['text'];
        }
        $users = $this->paginate = array(
            'conditions' => $cond,
            'order' => 'User.id DESC',
            'limit' => 10,
        );
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_social_users');
        }
        $users = $this->paginate('User');
        $this->set(compact('users', 'text'));
//pr($users);die;
        $this->set('layoutTitle', __('List Social Users'));
    }
	
	public function admin_deleteSocialUser() {
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->User->delete($id);
            $cond['User.userType '] = '2';
            $cond['User.social_id !='] = '';
            $users = $this->paginate = array(
                'conditions' => $cond,
                'order' => 'User.id DESC',
                'limit' => 10,
            );
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_social_users');
        }
    }
    
    /******************************************************Artist User****************************************************/
    public function admin_listArtistuser() {
        $cond = array();
        $cond['User.userType'] = '1';

        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
                'User.phone like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        $users = $this->paginate = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => 'User.id DESC',
        );
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_artist');
        }
        $users = $this->paginate('User');
        $this->set(compact('users', 'text'));
        //pr($users);die;
        $this->set('layoutTitle', __('List Artist'));
    }

    public function admin_addArtist() {

        if ($this->request->is('post')) {
          if (isset($this->request->data['User']['gallery_image']) && $this->request->data['User']['gallery_image'][0]['name'] != '') {
                $image_container = array();
                 $filesCount = count($this->request->data['User']['gallery_image']); 

                for ($i = 0; $i < $filesCount; $i++) {
                    $file_data = $this->request->data['User']['gallery_image'][$i];
                    $name = strtotime(date("Y-m-d h:i:s A")) . '_' . str_ireplace(' ', '_', $file_data['name']);

                    if ($file_data['size'] >= 2000000) {
                        $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error');
                        return $this->redirect($this->referer());
                    }

                    $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name);
                    $filename = time() . '_' . $file;
                    $ext = substr(strtolower(strrchr($file, '.')), 1);
                    $arr_ext = array('jpg', 'jpeg', 'png');

                    if (in_array($ext, $arr_ext)) {
                        $path = 'img/artist/';
                        if (move_uploaded_file($file_data['tmp_name'], $path . $filename)) {
                            array_push($image_container, $path . $filename);
                        }
                    }
                    else {
                        
                        $this->Session->setFlash(__('Gallery images must be jpg, jpeg, png.'), 'default', array(), 'error');
                         goto a;
                    }
                }
                //pr($image_container); exit;
                if (count($image_container) > 0) {
                    if (count($image_container) == 1) {
                        $thumb_image = $image_container[0];
                    } else {
                        $thumb_image = implode(',', $image_container);
                    }
                } else {
                    $thumb_image = '';
                }

                $this->request->data['User']['gallery_image'] = $thumb_image;
            } 
            
            else{
                 $this->request->data['User']['gallery_image']='';
            }
          
          

     //add vedio thubnail
    
        
       
     if (isset($this->request->data['User']['image']) && $this->request->data['User']['image']['name'] !== '' && !empty($this->request->data['User']['image']['name'])) {
                $name = $this->request->data['User']['image'];

                if ($name['size'] >= 2000000) {
                $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error'); 
                goto a;

                }

                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('jpg', 'jpeg', 'png');

                if (in_array($ext, $arr_ext)) {

                    $path = 'img/artist/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {
                        $this->request->data['User']['image'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                } 
                else 
                {
                     $this->Session->setFlash(__('Please only upload images ( png, jpg, jpeg) for profile image'), 'default', array(), 'error');
                    goto a;
                }
            } else {
                $this->request->data['User']['image'] = '';
            }

            if(isset($this->request->data['User']['video']) && $this->request->data['User']['video']['name'] !== '' && !empty($this->request->data['User']['video']['name'])) {
                $name = $this->request->data['User']['video'];
                if ($name['size'] >= 2000000) {
                     $this->Session->setFlash(__('File size must be less then 2MB'), 'default', array(), 'error');
                  
                    goto a;
                }
                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('mp4');

                if (in_array($ext, $arr_ext)) {
                    //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                    $path = 'img/artist/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {

                        $this->request->data['User']['video'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                if (isset($this->request->data['User']['video_thumbnail']) && $this->request->data['User']['video_thumbnail']['name'] !== '' && !empty($this->request->data['User']['video_thumbnail']['name'])) {
                $name = $this->request->data['User']['video_thumbnail'];

                if ($name['size'] >= 2000000) {
                     $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error');
                    goto a;
                }
                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('jpg', 'jpeg', 'png');

                if (in_array($ext, $arr_ext)) {
                    $path = 'img/video_thumbnail/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {
                        $this->request->data['User']['video_thumbnail'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                } else {

                $this->Session->setFlash(__('Please only upload Video thumbnail ( png, jpg, jpeg)'), 'default', array(), 'error');
                 goto a;
                }
            } else {
                 $this->Session->setFlash(__('Please add video thumbnail for uploaded video'), 'default', array(), 'error');
                 goto a;
            }
        
              } else {
                    $this->Session->setFlash(__('Please only upload file (mp4) for video'), 'default', array(), 'error');
                    goto a;
                }
            } 
            else 
            {
                $this->request->data['User']['video'] = '';
                $this->request->data['User']['video_thumbnail'] = '';
            }



            $this->request->data['User']['userType'] = 1;
            $this->request->data['User']['username'] = $this->request->data['User']['email'];
            $mypass =  $this->request->data['User']['password'];
            if(!empty($this->request->data['User']['guest_user_status'])){
                if($this->request->data['User']['guest_user_status']= 'on'){
                   $this->request->data['User']['guest_user_status'] = '1';
                }else{
                    $this->request->data['User']['guest_user_status'] = '1';
                }
            if(!empty($this->request->data['daterange'])){
                 $daterange = $this->request->data['daterange'];
               $date = explode('-', $daterange);

            if(!empty($date['0'])){
            $this->request->data['User']['start_date'] =  date("Y-m-d", strtotime($date['0']));
            }
            else
            {
             $this->request->data['User']['start_date'] = "0000:00:00";
            }

            if(!empty($date['1'])){
            $this->request->data['User']['end_date'] =  date("Y-m-d", strtotime($date['1']));;
            } 
            else
            {
            $this->request->data['User']['end_date'] = "0000:00:00";
            }

            }
            }
             else{
                $this->request->data['User']['guest_user_status'] = '0';
                $this->request->data['User']['end_date'] = "0000:00:00";
                $this->request->data['User']['start_date'] = "0000:00:00";
            }
            


              // echo '<pre>';
              // print_r($this->request->data); exit;



            if ($this->User->save($this->request->data)) 
            {
                if(!empty($this->User->getLastInsertID())){
                $lastId = $this->User->getLastInsertID();
                }
             $artist_data = $this->User->find('first',array('conditions'=>array('User.id'=> $lastId)));
             $link = FULL_BASE_URL.$this->base;
             $project_name = PROJECT_NAME;
             $to = $artist_data['User']['email'];
             $from = 'postmaster@localhost';
             $subject = 'Artist account details';
             
             $EmailBody = array('email'=>$artist_data['User']['email'],'project_name'=>$project_name,'link'=>$link,'name'=>$artist_data['User']['name'],'password'=>$mypass);
             $emailsend = $this->sendEmail($to,$from,$subject,$EmailBody,'artistAccount');

            $user = $this->User->find('all',array('conditions'=>array('User.userType'=>2,'User.notification'=>1),'fields'=>array('User.id')));
            
            foreach ($user as $value) {
                 $notifyuser = $value['User']['id'];
                 $title = 'Artist added';
                 $message = 'New artist '.$this->getUsername($lastId).' has been added. Check it into app under artist section.';
                 $this->saveNotify($notifyuser,$message,$title,'Artist','');

            }
            $this->Session->setFlash(__('The Artist has been created successfully'), 'default', array(), 'success');
            $this->redirect(array('action' => 'listArtistuser'));

            } 
            else 
            {
                $errorArray = "<ul>";
                foreach ($this->User->validationErrors as $error) {
                    $errorArray .= "<li>";
                    $errorArray .= $error[0];
                    $errorArray .= "</li>";
                } $errorArray .= "</ul>";
                $this->Session->setFlash(__($error[0]), 'default', array(), 'errror');
            }
        }
        a:

        $this->set('layoutTitle', __('Add Artist'));
    }

    public function admin_editArtist($id = null, $action = null) {
            
        if (!$id || !$action) {
            $this->Session->setFlash(__('Please provide a artist id'), 'default', array(), 'error');
            $this->redirect(array("controller" => "Users",
                "action" => $action,
                "page" => $this->passedArgs['page']));
        }

        $users = $this->User->findById($id);
       // pr($users); exit;

      
        if ($this->request->is('post') || $this->request->is('put')) {
           if(!empty($this->request->data['User']['name'])){
          $this->Session->write($this->request->data['User']['name']);
           }  
          
            if (isset($this->request->data['User']['video']) && $this->request->data['User']['video']['name'] !== '' && !empty($this->request->data['User']['video']['name'])) {
                $name = $this->request->data['User']['video'];
               if ($name['size'] >= 2000000) {
                    $this->Session->setFlash(__('File size must be less then 2MB'), 'default', array(), 'error');
                    
                    goto a;
                }
                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('mp4');

                if (in_array($ext, $arr_ext)) {
                    //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                    $path = 'img/artist/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {

                        $this->request->data['User']['video'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                } else {
                    $this->Session->setFlash(__('Please only upload file (mp4) for video'), 'default', array(), 'error');
                    $this->request->data['User']['video'] = $this->request->data['User']['videos'];
                    goto a;
                }
                //mamta
                if (isset($this->request->data['User']['video_thumbnail']) && $this->request->data['User']['video_thumbnail']['name'] !== '' && !empty($this->request->data['User']['video_thumbnail']['name'])) {
                $name = $this->request->data['User']['video_thumbnail'];

                // if ($name['size'] >= 2000000) {
                //     $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error');
                    
                //     goto a;
                // }

                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('jpg', 'jpeg', 'png');

                if (in_array($ext, $arr_ext)) {
                    //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                    $path = 'img/video_thumbnail/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {
                        $this->request->data['User']['video_thumbnail'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                } else {
                      $this->Session->setFlash(__('Please only upload video thumbnail (png, jpg, jpeg)'), 'default', array(), 'error');
                    $this->redirect(Router::url($this->referer(), true));
                }
            } 
            else
            {
                 $this->Session->setFlash(__('Please add the video thumbnail for uploaded video'), 'default', array(), 'error');
                    $this->redirect(Router::url($this->referer(), true));
             }
         } 
         else {
                if (!empty($this->request->data['User']['videos'])) {
                    $this->request->data['User']['video'] = $this->request->data['User']['videos'];
                    if(!empty($this->request->data['User']['oldvideo_thumbnail'])){

                     $this->request->data['User']['video_thumbnail'] = $this->request->data['User']['oldvideo_thumbnail'];
                    }
                    else{
                       $this->Session->setFlash(__('Please add the video thumbnail for uploaded video'), 'default', array(), 'error');
                       $this->redirect(Router::url($this->referer(), true));
                    }

                } 
                else
                {
                    $this->request->data['User']['video'] = '';
                     $this->request->data['User']['video_thumbnail'] = '';
                }
            }

            if (isset($this->request->data['User']['image']) && $this->request->data['User']['image']['name'] !== '' && !empty($this->request->data['User']['image']['name'])) {
                $name = $this->request->data['User']['image'];

                if ($name['size'] >= 2000000) {
                    $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error');
                    
                    goto a;
                }

                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time() . '_' . $file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('jpg', 'jpeg', 'png');

                if (in_array($ext, $arr_ext)) {
                    //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                    $path = 'img/artist/';
                    if (move_uploaded_file($name['tmp_name'], $path . $filename)) {
                        $this->request->data['User']['image'] = $path . $filename;
                        $multi_image[] = $filename;
                    }
                } else {
                    $this->Session->setFlash(__('Please only upload images ( png, jpg, jpeg)'), 'default', array(), 'error');
                    
                    $this->redirect(Router::url($this->referer(), true));
                }
            } 
            else
            {
                $this->request->data['User']['image'] = $this->request->data['User']['oldImage'];
            }

            /**********************video thumbnail images******************************/
            
            
            
            
            /** *****************************multiple image*********************************************** */
            if (isset($this->request->data['User']['gallery_image']) 
                    && $this->request->data['User']['gallery_image'][0]['name'] != '') {

                $image_container = array();
                $filesCount = count($this->request->data['User']['gallery_image']);

                if (!empty($filesCount)) {
                    for ($i = 0; $i < $filesCount; $i++) {
                        $file_data = $this->request->data['User']['gallery_image'][$i];
                        $name = strtotime(date("Y-m-d h:i:s A")) . '_' . str_ireplace(' ', '_', $file_data['name']);

                        if ($file_data['size'] >= 2000000) {
                            $this->Session->setFlash(__("Photo size must be less then 2MB"), 'default', array(), 'error');
                            
                            goto a;
                        }

                        $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name);
                        $filename = time() . '_' . $file;
                        $ext = substr(strtolower(strrchr($file, '.')), 1);
                        $arr_ext = array('jpg', 'jpeg', 'png');

                        if (in_array($ext, $arr_ext)) {
                            $path = 'img/artist/';
                            if (move_uploaded_file($file_data['tmp_name'], $path . $filename)) {
                                $image_container[] = $path . $filename;
                            }
                        } else {
                            $this->Session->setFlash(__('Gallery images must be jpg, jpeg, png.'), 'default', array(), 'error');
                            goto a;
                        }
                    }
                }
                if (!empty($image_container)) {
                    if (!empty($this->request->data['User']['mage'])) {

                        $image_container_new = array_merge($image_container, $this->request->data['User']['mage']);
                        $this->request->data['User']['gallery_image'] = implode(',', $image_container_new);
                    } else {
                        $this->request->data['User']['gallery_image'] = implode(',', $image_container);
                    }
                } else {
                    $this->request->data['User']['gallery_image'] = '';
                }
            } else {
                if (!empty($this->request->data['User']['mage']))
                    if (count($this->request->data['User']['mage']) == 1) {
                        $this->request->data['User']['gallery_image'] = $this->request->data['User']['mage'][0];
                    } else {
                        $this->request->data['User']['gallery_image'] = implode(',', $this->request->data['User']['mage']);
                    }
            }
           
            if(!empty($this->request->data['User']['newpassword'])){
                $this->request->data['User']['password'] = $this->request->data['User']['newpassword'];
            }
            if(!empty($this->request->data['User']['guest_user_status'])){
                if($this->request->data['User']['guest_user_status']= 'on'){
                   $this->request->data['User']['guest_user_status'] = '1';
                   if(!empty($this->request->data['daterange'])){
              $daterange = $this->request->data['daterange'];
              $date = explode('-', $daterange);
             // print_r($daterange); exit;
                if(!empty($date['0'])){
                 $this->request->data['User']['start_date'] =  date("Y-m-d", strtotime($date['0']));
                }
                if(!empty($date['1'])){
                 $this->request->data['User']['end_date'] =  date("Y-m-d", strtotime($date['1']));
                } 
            }
           }
            }else{
                $this->request->data['User']['guest_user_status'] = '0';
            }
            
           $this->User->id = $id;
              // echo '<pre>';
              // print_r($this->request->data); exit;

            if ($this->User->save($this->request->data)) {
                
                $this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
            
              $user = $this->User->find('all',array('conditions'=>array('User.userType'=>2,'User.notification'=>1),'fields'=>array('User.id')));
              foreach ($user as $value) {
                $notifyuser = $value['User']['id'];
                $title = 'Artist Updated';
                $message = ' Artist '.$this->getUsername($id).' updated his work. Go into app to check his work';
                $this->saveNotify($notifyuser,$message,$title,'Artist','');
               }

              $this->Session->setFlash(__("Artist has been updated successfully"), 'default', array(), 'success');
                 //pr($this->Auth->user('userType')); exit;
                 if ($this->Auth->user('userType') == '1') {
                 	return $this->redirect($this->referer());
                 } else {
                 	$this->redirect(array('action' => 'listArtistuser'));
                 }
              
            } 
            else 
            {
                $this->Session->setFlash(__("The User can not updated error occured."), 'default', array(), 'error');
            }
        }
        if(!$this->request->data) {
            $this->request->data = $users;
        }

		if($this->Auth->user('userType') == '1') {
			$utype = '1';
		} else {
            $utype = '3';
        }

        a:
        //print_r($utype); exit;
        $this->set('utype',$utype);
        $this->set('layoutTitle', __('Edit Artist'));
    }

    

    public function admin_deleteArtist() {
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
                'User.phone like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->User->delete($id);
            $cond['User.userType '] = '1';
            $users = $this->paginate = array(
                'conditions' => $cond,
                'order' => 'User.id DESC',
                'limit' => 10,
            );
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_artist');
        }
    }

    public function admin_changeartistStatus() {
        $id = $_REQUEST['id'];
        $status = $_REQUEST['sts'];
        $this->User->id = $id;
        $data['User']['status'] = $status;
        $this->User->id = $id;
        $cond = array();
        $cond['User.userType'] = '1';
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'User.email like' => '%' . $this->passedArgs['text'] . '%',
                'User.name like' => '%' . $this->passedArgs['text'] . '%',
                'User.phone like' => '%' . $this->passedArgs['text'] . '%',
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->User->save($data);
            $users = $this->paginate = array(
                'conditions' => $cond,
                'limit' => 10,
                'order' => 'User.id DESC',
            );
//$text = $this->passedArgs['text'];
            $users = $this->paginate('User');
            $this->set(compact('users', 'text'));
            $this->render('/Elements/list_artist');
        }
    }

   

    public function admin_viewArtist($id = null) {
        if (!$id) {
            $this->Session->setFlash('Page not found', 'default', array(), 'error');
            return $this->redirect($this->referer());
        }

        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('Artist does not exist', 'default', array(), 'error');
            return $this->redirect($this->referer());
        }

        $users = $this->User->find('first', array('conditions' => array('User.id' => $id)));
        $this->set(compact('users'));

        echo $this->set('layoutTitle', 'Artist detail');
    }

   public function admin_changePassword($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Please provide a user id'), 'default', array(), 'error');
            $this->redirect(array('action' => 'dashboard'));
        }

        //$user = $this->User->findById($id);
        $user = $this->User->find('first', array('conditions' => array('User.id'=>$id)));
        if (!$user) {
            $this->Session->setFlash(__('Invalid user id provided'), 'default', array(), 'error');
            $this->redirect(array('action' => 'dashboard'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $old_pass = $this->request->data['User']['old_password'];
            $pass=Security::hash($old_pass, 'md5', true);
            $userData = $this->User->find('first',array('conditions'=>array('User.id'=>$id,'User.password'=>$pass)));
			if(!$userData){
				 $this->Session->setFlash('Old password is not valid', 'default', array(), 'error');
				 goto a;
            }

            //pr($userData); exit;
            $this->User->id = $id;
            if ($this->User->save($this->request->data)) {
                if($this->Auth->user('userType') == '1') {
                    $this->Session->setFlash(__('Your password has been changed successfully'), 'default', array(),'success');
                    return $this->redirect($this->referer());
                }
                else{
                    $this->Session->setFlash(__('Your password has been changed successfully'), 'default', array(),'success');
                    $this->redirect(array('action' => 'dashboard'));

                }
                

            } else {
                $errorArray = "<ul>";
                foreach ($this->User->validationErrors as $error) {
                    $errorArray .= "<li>";
                    $errorArray .= $error[0];
                    $errorArray .= "</li>";
                }
                $errorArray .= "</ul>";
                $this->Session->setFlash(__($error[0]), 'default', array(), 'error');
   
            }
        }
        
		a:
        if (!$this->request->data) {
            $this->request->data = $user;
        }
        $this->set('layoutTitle', __('Change Password'));
	}

    public function api_changePassword() {
    //http://webmantechnologies.com/himanshu/alamal/api/users/changePassword?ID&password
        $user = $this->User->findById($_REQUEST['ID']);
        if (!empty($user)) {
            $this->User->id = $_REQUEST['ID'];
            $this->request->data['User']['password'] = $_REQUEST['password'];
            $user = $this->User->save($this->request->data);
            if ($user) {
                $results = array('mesg' => 'Password updated successfully', 'responce' => '1');
            } else {
                foreach ($this->User->validationErrors as $error) {
                    $results = array('mesg' => __($error[0]), 'responce' => '0');
                }
            }
        } else {
            $results = array('mesg' => 'User not exsists', 'responce' => '0');
        }
        echo json_encode($results);
        exit;
    }

    public function admin_forgotAccount() {
        $this->layout = 'login';
        if (!empty($_REQUEST['forgot'])) {
            $id = base64_decode($_REQUEST['forgot']);
        } else {
            $id = '';
        }
        if (!empty($_REQUEST['forgotid'])) {
            $aId = $_REQUEST['forgotid'];
        } else {
            $aId = '';
        }
        if (!$this->request->is('post')) {
            $user = $this->User->find('all', array('conditions' => array('User.id' => $id, 'User.forgot_key' => $aId)));
            if (!$user) {
                $this->redirect(array('action' => 'redirection', 'admin' => true, '?' => array('msg' => 'Link may be expired.'
                )));
            }
        }
        if ($this->request->is('post')) {
            if (empty($this->request->params['pass'][0]) || empty($this->request->params['pass'][0])) {
                $this->redirect(array('action' => 'redirection', 'admin' => true, '?' => array('msg' => 'Link may be expired.'
                )));
            } else {
                $user = $this->User->find('all', array('conditions' => array('User.ID' => base64_decode($this->request->params['pass'][0]))));
                if ($user) {
                    if (count($user) > 0) {
                        $this->request->data['User']['password'] = $this->request->data['User']['password'];
                        $this->request->data['User']['forgot_key'] = '';
                        $this->User->id = $user[0]['User']['id'];
//  pr($this->request->data); die;
                        if ($this->User->validates()) {
                            if ($this->User->save($this->request->data)) {

                                $this->redirect(array('action' => 'redirection', 'admin' => true, '?' => array('msg' => 'Your password has been changed successfully.'
                                )));
                            } else {
                                $errorArray = "<ul>";
                                foreach ($this->User->validationErrors as $error) {
                                    $errorArray .= "<li>";
                                    $errorArray .= $error[0];
                                    $errorArray .= "</li>";
                                }
                                $errorArray .= "</ul>";
                                $this->Session->setFlash(__($error[0]), 'default', array(), 'error');
                                
                                $this->redirect(array('action' => 'forgotAccount', 'main' => true, '?' => array('forgot' => $this->request->params['pass'][0], 'forgotid' => $this->request->params['pass'][1]
                                )));
                            }
                        } else {

                            $this->redirect(array('action' => 'redirection', 'admin' => true, '?' => array('msg' => 'Something went wrong,Contact to admin.'
                            )));
                        }
                    }
                } else {
                    $this->redirect(array('action' => 'redirection', 'admin' => true, '?' => array('msg' => 'Link may be expired.'
                    )));
                }
            }
        }
    }

    public function admin_about() {
        if ($this->request->is('post')) {
            $updateRec = $this->About->find('first');
            if (!empty($updateRec)) {
                $this->About->id = $updateRec['About']['id'];
            } else
                $this->About->create();
            if ($this->About->save($this->request->data)) {
                $datas = $updateRec;
                $this->Session->setFlash(__('About has been saved successfully'), 'default', array(), 'success');
                $this->set('datas', $datas);
            } else {
                $datas = $this->requested->data;
                $this->set('datas', $datas);
                $this->Session->setFlash(__('Error in saving, remove following error occured'), 'default', array(), 'error');
            }
        }
        $updateRec = $this->About->find('first');
        if (!empty($updateRec)) {
            $datas = $updateRec;
            $this->set('datas', $datas);
        }
        a:
        $this->set('layoutTitle', __('About us'));
    }

    public function admin_policy() {
        if ($this->request->is('post')) {
            $updateRec = $this->Privacy->find('first');
            if (!empty($updateRec)) {
                $this->Privacy->id = $updateRec['Privacy']['id'];
            } else
                $this->Privacy->create();
            if ($this->Privacy->save($this->request->data)) {
                $datas = $updateRec;
                $this->Session->setFlash(__('Data has been saved successfully'), 'default', array(), 'success');
                $this->set('datas', $datas);
            } else {
                $datas = $this->requested->data;
                $this->set('datas', $datas);
                $this->Session->setFlash(__('Error in saving, remove following error occured'), 'default', array(), 'error');
            }
        }
        $updateRec = $this->Privacy->find('first');
        if (!empty($updateRec)) {
            $datas = $updateRec;
            $this->set('datas', $datas);
        }
        a:
        $this->set('layoutTitle', __('Privacy Policy'));
    }

    public function admin_term() {
        if ($this->request->is('post')) {
            $updateRec = $this->Term->find('first');
            if (!empty($updateRec)) {
                $this->Term->id = $updateRec['Term']['id'];
            } else
                $this->Term->create();
            if ($this->Term->save($this->request->data)) {
                $datas = $updateRec;
                $this->Session->setFlash(__('Terms has been saved successfully'), 'default', array(), 'success');
                $this->set('datas', $datas);
            } else {
                $datas = $this->requested->data;
                $this->set('datas', $datas);
                $this->Session->setFlash(__('Error in saving, remove following error occured'), 'default', array(), 'error');
            }
        }
        $updateRec = $this->Term->find('first');
        if (!empty($updateRec)) {
            $datas = $updateRec;
            $this->set('datas', $datas);
        }
        a:
        $this->set('layoutTitle', __('Terms'));
    }

    public function admin_search() {
        $cond = array('User.status' => 1);
        if ($this->request->is('post')) {
            if (!empty($this->request->data['User']['q'])) {
                $cond['or'] = array(
                    'User.email like' => '%' . $this->request->data['User']['q'] . '%',
                    'User.name like' => '%' . $this->request->data['User']['q'] . '%',
                );
            }
        }
        $users = $this->paginate = array(
            'conditions' => $cond,
            'order' => 'User.modified DESC',
            'limit' => 10,
//'sortWhitelist' => 'id'
        );
        $users = $this->paginate('User');
//pr($users);
//die;
        $this->set(compact('users'));
        $this->set('layoutTitle', __('Search'));
    }

    public function admin_dashboard() {
    $current_date =  date("Y-m-d");
    $ongoing_promotion =  $this->Promotion->find('all', array('conditions' => array('Promotion.expirary_date >=' => $current_date)));
    $expired_promotion =  $this->Promotion->find('all', array('conditions' => array('Promotion.expirary_date <' => $current_date)));

        $apps_user = $this->User->find('all', array('conditions' => array('User.userType' => 2)));
        $artist_user = $this->User->find('all', array('conditions' => array('User.userType' => 1)));

        $expir_prom = count($expired_promotion);
        $on_go = count($ongoing_promotion);
        $app_user = count($apps_user);
        $artist = count($artist_user);
        $promo = count($this->Promotion->find('all'));
        
        $this->set(compact('expir_prom'));
        $this->set(compact('on_go'));
        $this->set(compact('promo'));
        $this->set(compact('artist'));
        $this->set(compact('app_user'));
        $this->set('layoutTitle', __('Dashboard'));
    }

    public function api_makePay() {
        $this->Basic->stripePay($_REQUEST['token'], '10', 'himanshukumar.orem@gmail.com');
    }

    public function api_updateTransaction() {

        # Get All Accounts

        $accountData = $this->Account->find('all', array('conditions' => array('account_id !=' => '', 'access_token !=' => '', 'Account.status' => '1')));

        if (!empty($accountData)) {
            foreach ($accountData as $key => $account) { //pr($account); die;
                $accesToken = $account['Account']['access_token'];
                $account_id = $account['Account']['account_id'];
                $getLastdate = $this->Transaction->find('first', array('order' => 'Transaction.id DESC'));
                if (empty($getLastdate)) {
                    $end_date = date('Y-m-d');
                    $start_date = date('Y-m-d', strtotime('-45 day'));
                } else {
                    $end_date = date('Y-m-d');
                    $start_date = date('Y-m-d', strtotime($getLastdate['Transaction']['record_on']));
                }
                $ch = curl_init();
                $data_string = '{
           "end_date": "' . $end_date . '",
           "start_date": "' . $start_date . '",
           "access_token": "' . $accesToken . '",
           "client_id": "5ab1a0388d9239521f158de2",
           "secret": "346401f303d4060e70603671ed931a",
           "options": {
             "account_ids": [
               "' . $account_id . '"
             ]
           }
         }';
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_URL, 'https://sandbox.plaid.com/transactions/get');
                $result = curl_exec($ch);
                curl_close($ch);
                $json = json_decode($result);
                // pr($json); die;

                if (!empty($json->transactions)) {
                    foreach ($json->transactions as $key => $value) {

                        if ($value->pending === false) {

                            if (!$this->Transaction->findById($value->transaction_id)) {
                                $tran['Transaction'] = array('name' => $value->name,
                                    'record_on' => $value->date,
                                    'amount' => $value->amount,
                                    'user_id' => $account['Account']['user_id'],
                                    'tx_id' => $value->transaction_id
                                );
                                $this->Transaction->create();
                                $this->Transaction->save($tran);
                            }
                        }
                    }
                }
            }
        }

        $this->api_deductRoundUpValue('fgdg');
        echo 'Success';
        die;
    }

function getUsername($id){
    $userdata = $this->User->findById($id);
   if(!empty($userdata)){
    $username =$userdata['User']['name'];

   }else{
    $username = '';
   }
   return $username;
}

function saveNotify($user_id,$message,$title,$type=null,$requestid=null){

    $data['Notification']['user_id']  =  $user_id;
    $data['Notification']['message']  =  $message;
    $data['Notification']['title']    =  $title;
    $data['Notification']['type']   =    $type;
    $data['Notification']['request_id']   =    $requestid;

    $this->Notification->create();
    if($this->Notification->save($data)){

      $notifyuser = $this->User->findById($user_id);

      if($notifyuser['User']['deviceType'] == 'ios'){
        $this->Basic->send_inotification($notifyuser['User']['deviceToken'],$message,$title,$type,$requestid);
      } else if($notifyuser['User']['deviceType'] == 'Android'){
        $this->Basic->send_notification($notifyuser['User']['deviceToken'],$message,$title,$type,$requestid);
      }
    }
    return true;

}


    public function admin_redirection() {
        // pr($_REQUEST[]); exit;
        $this->layout = 'login';
        //  $this->set(compact('layout_msg',$_REQUEST['msg']));
        $this->set('layout_msg', $_REQUEST['msg']);
    }

}
