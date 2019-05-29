<?php

/**
 * Created by PhpStorm.
 * User: win 7
 * Date: 4/8/2017
 * Time: 3:23 PM
 */
App::uses('AppModel', 'Model');
App::uses('Security', 'Utility');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Name is required',
            )
        ),
         'old_password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Old password is required',
            )
        ),
        // 'address' => array(
        //     'required' => array(
        //         'rule' => array('minLength', '15'),
        //         'message' => 'Address must be minimum 15 characters long',
        //     )
        // ),
        'image' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Profile image is required',
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('minLength', '6'),
                'message' => 'Passwords must be minimum 6 characters long',
            )
        ),
        'newPassword' => array(
            'length' => array(
                'rule' => array('minLength', '6'),
                'message' => 'Passwords must be minimum 6 characters long',
                'allowEmpty' => true
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter valid email'
            ),
             'maxLength' => array(
            'rule' => array('maxLength', 25),
            'message' => 'Email cannot be more than 25 characters.'
        ),
            'unique' => array(
                'rule' => array('isUnique', array('email', 'userType'), false),
                'message' => 'This email id is already registered with us. Please enter different email id or goto forgot password to reset your password.'
            ),
        ),
        'username' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter valid username'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'The username is already used, please choose another username'
            ),
        ),
        'phone' => array(
            'required' => array(
                'rule' => array('lengthBetween', 10, 12),
                'message' => 'Mobile number length should be between 10 to 12 characters'
            ),
            'numeric' => array(
                'rule' => 'numeric',
                'message' => 'Phone number should be numeric',
            ),
        ),
        'status' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Status is required',
            )
        ),
      
        'conf_password' => array(
            'compare' => array(
                'rule' => array('validate_passwords'),
                'message' => 'The passwords you entered do not match',
            ),
        ),

       
    );

  

    public function validate_passwords() {
        return $this->data[$this->alias]['password'] === $this->data[$this->alias]['conf_password'];
    }

    // public function validUsername(){
    //  $data = $this->User->find('first',array('conditions'=>array('User.username'=$this->data[$this->alias]['username'])));
    //  if(empty($data)){
    //      return true;
    //  } else {
    //      return false;
    //  }
    // }

    public function beforeSave($options = array()) {
        if (isset($this->data['User']['password'])) {
            $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], 'md5', true);
            return true;
        }
    }
     

}
