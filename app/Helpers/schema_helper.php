<?php 


    function employee_without_password($employee) {

        if($employee) {
            $emp_data = [
                "employee_name" => $employee['emp_name'] ?? "",
                "employee_username" => $employee['emp_username'] ?? "",
                "employee_role" => $employee['emp_role'] ?? "norole",
                "employee_image" => $employee['emp_image'] ?? "noimage",
                "pages" => json_decode($employee['allow_page']) ?? []
            ];

            return $emp_data;

        }else{
            return false;
        }


    }


?>