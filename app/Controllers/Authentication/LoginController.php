<?php

namespace App\Controllers\Authentication;

use App\Models\crm\UserModel;
use App\Models\Customer\CustomerModel;
use CodeIgniter\RESTful\ResourceController;
helper('token');
helper('auth');
helper('schema');
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
        $role = $this->request->getPost('role') ?? "";
        $activity = $this->request->getPost('activity') ?? json_encode((object)[]);

        $activity = json_decode($activity);
        
        
        if ($username == '' || $password == '' || $role == '') {
            return $this->response->setJSON([
                "status" => false,
                "message" => "All fields are required",
                "data" => (object) [],
            ]);
        }
        
        if($role == 'employee') {

            $user = employee_login($username, $password);

            if($user) {

                $recordLogin = login_time_record("employee", $user['emp_id'], $activity->user_ip_address, $activity->user_agent, 'working', $activity->user_lat_long);


                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Welcome " . $user['emp_first_name'] . '! - ',
                    "login_by" => "employee",
                    "record" => $recordLogin,
                    "activity" => $activity,
                    "employee" => employee_without_password($user),
                    "token" => $user['token'],
                ]);
            }else{
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Employee login failed",
                    "token" => null
                ]);
            }

        }else{

            $userModel = new UserModel();

            try {
                $user = $userModel->where(['user_name' => $username, 'user_pass' => hash('sha256', $password)])->first();
            
                if ($user) {
        
                    $jwt = encode_jwt($user);

                    $recordLogin = login_time_record("employee", $user['emp_id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], 'working');
        
                    return $this->response->setJSON([
                        "status" => true,
                        "message" => "Welcome " . $user['user_name'] . '! - ' . $role,
                        "recorded" => $recordLogin,
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
 
}
