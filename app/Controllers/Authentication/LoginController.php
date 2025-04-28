<?php

namespace App\Controllers\Authentication;

use App\Models\crm\UserModel;
use App\Models\Customer\CustomerModel;
use CodeIgniter\RESTful\ResourceController;
helper('token');
class LoginController extends ResourceController
{

    public function login()
    {

        $username = $this->request->getPost('username') ?? "";
        $password = $this->request->getPost('password') ?? "";
        
        
        if ($username == '' || $password == '') {
            return $this->response->setJSON([
                "status" => false,
                "message" => "Enter username name or password",
                "data" => (object) [],
            ]);
        }
        
        $customerModel = new CustomerModel();
        
        try {
            $customer = $customerModel->where(['c_email' => $username, 'c_password' => hash('sha256', $password)])->first();
        
            if ($customer) {
    
                $jwt = encode_jwt($customer);
    
                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Welcome " . $customer['c_fullname'] . '!',
                    "token" => $jwt,
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Invalid username or password",
                    "data" => (object) [],
                    'password' => $password,
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $th->getMessage() . " " . $th->getLine(),
                'data' => null
            ]);
        }
       
    }
    public function crmLogin()
    {
        $username = $this->request->getPost('username') ?? "";
        $password = $this->request->getPost('password') ?? "";
        
        
        if ($username == '' || $password == '') {
            return $this->response->setJSON([
                "status" => false,
                "message" => "Enter username number or password",
                "data" => (object) [],
            ]);
        }
        
        $userModel = new UserModel();
        
        try {
            $user = $userModel->where(['user_name' => $username, 'user_pass' => hash('sha256', $password)])->first();
        
            if ($user) {
    
                $jwt = encode_jwt($user);
    
                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Welcome " . $user['user_name'] . '!',
                    "token" => $jwt,
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Invalid username or password",
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $th->getMessage() . " " . $th->getLine(),
                'data' => null
            ]);
        }
       
    }
 
}
