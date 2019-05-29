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
class BannersController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array('User');
    public $layout = 'admin';
    public $components = array('Paginator');

   

    public function admin_listBanner() {
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
               'Banner.name like' => '%' . $this->passedArgs['text'] . '%',
               
            );
            $text = $this->passedArgs['text'];
        }
        $banners = $this->paginate = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => 'Banner.id DESC',
        );
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $banners = $this->paginate('Banner');
             $this->set(compact('banners', 'text'));
            $this->render('/Elements/list_banners');
        }

        $banners = $this->paginate('Banner');
        $this->set(compact('banners', 'text'));
        //pr($users);die;
        $this->set('layoutTitle', __('List Banner'));
  }

public function admin_addBanner(){

         if ($this->request->is('post')) {
             
          if(isset($this->request->data['Banner']['image']) && $this->request->data['Banner']['image']['name'] !== '' && !empty($this->request->data['Banner']['image']['name'])) { 
               $dimension =  getimagesize($this->request->data['Banner']['image']['tmp_name']);
               $width = $dimension[0];
               $height = $dimension[1];
                // echo '<pre>';
                // print_r($dimension[0]); exit;
                if($width >= 1080 && $height >= 600){  

              $name = $this->request->data['Banner']['image'];
              if($name['size'] >= 2000000){
                    $this->Session->setFlash(__('Photo size must be less then 2MB'), 'default', array(), 'error');
                    goto a;
                }
                $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                $filename = time().'_'.$file;
                $ext = substr(strtolower(strrchr($file, '.')), 1);
                $arr_ext = array('jpg', 'jpeg','png');

               if(in_array($ext, $arr_ext))
               {
                   //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                   $path = 'img/banner/';
                    if(move_uploaded_file($name['tmp_name'],$path.$filename)){
                        $this->request->data['Banner']['image'] = $path.$filename;
                        $multi_image[] = $filename;
                    }
               }else{
                 $this->Session->setFlash(__('Please only upload images (png, jpg, jpeg)'), 'default', array(), 'error');
                              goto a;
                }
              } else{
                  $this->Session->setFlash(__('Please upload the image of more then 1080*600 dimensions'), 'default', array(), 'error');
                 goto a;

              }

            } else{
                $this->request->data['Banner']['image'] = '';
            }
          $this->request->data['Banner']['status'] = '1';
          $banner =  $this->Banner->find('all', array('conditions' => array('banner.status' => 1)));
          $count_banner = count($banner);

          if($count_banner <= 4){
           $this->Banner->create();
            if ($this->Banner->save($this->request->data)) {           
                $this->Session->setFlash(__('New banner is added successfully'), 'default', array(), 'success');
                $this->redirect(array('action' => 'admin_listBanner'));
            } else {
                $errorArray = "<ul>";
                foreach ($this->Banner->validationErrors as $error) {
                    $errorArray .= "<li>";
                    $errorArray .= $error[0];
                    $errorArray .= "</li>";
                } $errorArray .= "</ul>";
                $this->Session->setFlash(__($error[0]), 'default', array(), 'errror');
            }
          }else{
            $this->Session->setFlash(__('You can not exceed the banner more than 5'), 'default', array(), 'error');
            goto a;

          }

        }
        a:
      $this->set('layoutTitle', __('Add Banner'));
  }

  public function admin_editBanner($id = null, $action = null){
         if (!$id || !$action) {
            $this->Session->setFlash(__('Please provide a artist id'), 'default', array(), 'error');
            $this->redirect(array("controller" => "banners",
                "action" => $action,
                "page" => $this->passedArgs['page']));
        }
    
        $banner = $this->Banner->findById($id);
       // pr($User); exit;
        if ($this->request->is('post') || $this->request->is('put')) {

            
            if(isset($this->request->data['Banner']['image']) && $this->request->data['Banner']['image']['name'] !== '' && !empty($this->request->data['Banner']['image']['name']))
               { 
                  $name = $this->request->data['Banner']['image'];

                  if($name['size'] >= 2000000){
                      $this->Session->setFlash(__("Photo size must be less then 2MB"), 'default', array(), 'error');
                  
                  goto a;
                  }

                  $file = preg_replace("/[^a-zA-Z0-9.]/", "", $name['name']);
                  $filename = time().'_'.$file;
                  $ext = substr(strtolower(strrchr($file, '.')), 1);
                  $arr_ext = array('jpg', 'jpeg','png');

                  if(in_array($ext, $arr_ext))
                  {
                  //$upload_pathx = FULL_BASE_URL.$this->webroot.'img/restaurants/';
                  $path = 'img/banner/';
                  if(move_uploaded_file($name['tmp_name'],$path.$filename)){
                  $this->request->data['Banner']['image'] = $path.$filename;
                  $multi_image[] = $filename;
                  }
                  }
                  else{
                    $this->Session->setFlash(__('Please only upload images (png, jpg, jpeg)'), 'default', array(), 'error');
                  
                  $this->redirect( Router::url( $this->referer(), true ) );

                  }
              } else{
                if(!empty($this->request->data['Banner']['oldImage'])){
                   $this->request->data['Banner']['image'] = $this->request->data['Banner']['oldImage'];
                 }else{
                   $this->request->data['Banner']['image'] = '';
                 }
               
            }
       
      
            $this->Banner->id = $id;
            //pr($this->request->data); exit;
            if ($this->Banner->save($this->request->data)) {
                $this->Session->setFlash(__('Banner has been updated successfully'), 
            'default', array(), 'success');
                $this->redirect(array("controller" => "banners",
                    "action" => $action,
                    "page" => $this->passedArgs['page']));
            } else {
                $this->Session->setFlash(__('The Banner can not updated error occured.'),'default', array(), 'error');
            }
        }
        
        if (!$this->request->data) {
            $this->request->data = $banner;
            
        }
        a:
        $this->set('layoutTitle', __('Edit Banner'));

  }

  public function admin_changebannerStatus(){
        $id = $_REQUEST['id'];
        $status = $_REQUEST['sts'];
//        $this->Promotion->id = $id;
        $data['Banner']['status'] = $status;
        $this->Banner->id = $id;
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
               'Banner.name like' => '%' . $this->passedArgs['text'] . '%',
              
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->Banner->save($data);
            $banners = $this->paginate = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => 'Banner.id DESC',
            );
            //$text = $this->passedArgs['text'];
            $banners = $this->paginate('Banner');
            $this->set(compact('banners','text'));
            $this->render('/Elements/list_banners');
        }
    }

     public function admin_deleteBanner() {
        $cond = array();
        if (!empty($this->passedArgs['text'])) {
            $cond['or'] = array(
                'Banner.name like' => '%' . $this->passedArgs['text'] . '%',
              
            );
            $text = $this->passedArgs['text'];
        }
        if ($this->request->is("ajax")) {
            $this->autoRender = false;
            $id = $_REQUEST['id'];
            $this->Banner->delete($id);
           $banners = $this->paginate = array(
                'conditions' => $cond,
                'order' => 'Banner.id DESC',
                'limit' => 10,
            );
            $banners = $this->paginate('Banner');
            $this->set(compact('banners', 'text'));
            $this->render('/Elements/list_banners');
        }
    }

     
}
