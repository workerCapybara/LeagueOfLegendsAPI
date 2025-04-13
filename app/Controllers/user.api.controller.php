<?php
require_once 'app/Controllers/api.controller.php';
require_once 'app/Helpers/auth.api.helper.php';
require_once 'app/Models/user.model.php';

class UserApiController extends ApiController {
    private $model;
    private $authHelper;

    function __construct() {
        parent::__construct();
        $this->authHelper = new AuthHelper();
        $this->model = new UserModel();
    }

    function getToken(){
        $basic = $this->authHelper->getAuthHeaders(); //Returns me the header
                                                      //'Authorization:' 'Basic: base64(usr:pass)'
        if(empty($basic)){  //Verify that header is not empty
            $this->view->response('Did not send authentication headers.', 401);
            return;
        }

        $basic = explode(" ", $basic); //Separates header into ["Basic", "base64(usr:pass)"]

        if($basic[0]!="Basic") {    //Verify that header type is Basic
            $this->view->response('Los encabezados de autenticación son incorrectos.', 401);
            return;
        }

        $userpass = base64_decode($basic[1]); //Decodes the "base64(usr:pass)", leaving usr:pass
        $userpass = explode(":", $userpass); //Separates usr and pass, leaving ["usr", "pass"]

        $user = $userpass[0];
        
        $pass = $userpass[1];
        
        $userData = $this->model->getUserByUsername($user); //Returns user form the database
        var_dump($userData);
        
        var_dump($user);
       
        //echo password_hash("admin", PASSWORD_DEFAULT);
        //Verify user and password
        if ($user == $userData->user_name && password_verify($pass, $userData->password)) {
            $token = $this->authHelper->createToken($userData);
            $this->view->response($token, 200);
        } else{
            $this->view->response('Password or user incorrect', 401);
        }
    }
}

    