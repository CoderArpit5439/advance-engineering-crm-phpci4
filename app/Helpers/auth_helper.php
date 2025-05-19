<?php 

    // AUTHENTICATION HELPER
    

use App\Models\Crm\EmployeModel;
use App\Models\Crm\LoginActivityModel;

    function employee_login($username, $password) {

        $employeeModel = new EmployeModel();

        $employee = $employeeModel->where('emp_username', $username)->where('emp_password', base64_encode($password))->first();

        if($employee) {
            // $employee['user_type'] = 'employee';
            
            $employee_jwt = encode_jwt($employee);

            $employee['token'] = $employee_jwt;

            return $employee;

        }else{
            return false;
        }

    }

    function login_time_record($type, $id, $ipaddress, $agent = '', $addInformation = '', $lat_long = '') {

        $la_data = [
            "la_type" => $type,
            "la_user_id" => $id,
            "la_ip_address" => $ipaddress,
            "la_agent" => $agent,
            "la_add_information" => $addInformation,
            "la_lat_long" => $lat_long
        ];

        $loginActivityModel = new LoginActivityModel();

        $insertLoginActivity = $loginActivityModel->insert($la_data);

        if($insertLoginActivity) {
            return true;
        }else{
            return false;
        }

    }

?>