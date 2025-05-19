<?php

namespace App\Controllers\LoginActivity;

use App\Controllers\BaseController;
use App\Models\Crm\EmployeModel;
use App\Models\Crm\LoginActivityModel;

helper('token');

class LoginActivityController extends BaseController
{
    public function index()
    {
        //
    }

    public function individualLoginActivities() {

        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        $loginActivityModel = new LoginActivityModel();
        $employeeModel = new EmployeModel();

        $allLoginActivities = $loginActivityModel->where('la_type', 'employee')->where('la_user_id', $isToken->emp_id)->orderBy('la_id', 'desc')->findAll();

        $totalLoginActivities = [];

        foreach ($allLoginActivities as $log) {

            if($log['la_type'] == 'employee') {
                $singleEmployee = $employeeModel->where('emp_id', $log['la_user_id'])->first();
                $log['la_emp_name'] = $singleEmployee['emp_first_name'] . ' ' . $singleEmployee['emp_last_name'];
                $totalLoginActivities[] = $log;
            }

        }

        return $this->response->setJSON([
            "status" => true,
            "message" => "Login Activities",
            "count" => count($totalLoginActivities),
            "loginActivities" => $totalLoginActivities 
        ]);

    }
}
