<?php

namespace App\Controllers\Employee;

use App\Models\crm\EmployeModel;
use CodeIgniter\RESTful\ResourceController;
helper('token');
class EmployeeController extends ResourceController
{
    public function AddEmployee(){
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    
        $imageFile = $this->request->getFile('emp_image');
        
        if (isset($imageFile) && !empty($imageFile)) {
            $uploadPath = FCPATH . 'public/assets/img/uploads/UserImage/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, TRUE);
            }
            
            if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Upload directory is not writable',
                ]);
            }
    
            // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ START --------------->  
            $imageFile->move($uploadPath);
            
            if ($imageFile->hasMoved()) {
                $fileName = 'https://api.advanceengineerings.com/public/assets/img/uploads/UserImage/' . $imageFile->getName();
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to upload Profile Image.'
                ]);
            }
            // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ END --------------->  
        } else {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Please Upload Image"
            ]);
        }
    
        // Get input data
        $empPassword = $this->request->getVar("emp_password");
    
        $data = [
            "emp_name" => $this->request->getVar("emp_name") ?? "",
            "emp_password" => (null !== $empPassword) ? base64_encode($empPassword) : "",
            "emp_role" => $this->request->getVar("emp_role"),
            "emp_image" => $fileName ?? "",
        ];
    
        try {
            $employeeModal = new EmployeModel();
            $addEmployee = $employeeModal->insert($data);
    
            if ($addEmployee) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Employee uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Employee not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function GetEmployee(){
        try {

            $employeeModel = new EmployeModel();
            $allEmployee = $employeeModel->orderBy("emp_id", "DESC")->findAll();
            $countEmployee = $employeeModel->countAllResults();
            
            if ($allEmployee) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allEmployee) . ' Employes found',
                    "data" => $allEmployee,
                    "count" => $countEmployee
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Employe not found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getTrace(),
                'data' => null
            ]);
        }
    }

    public function UpdateEmployee() {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    
        // Get employee ID from request
        $empId = $this->request->getVar("emp_id");
    
        if (!$empId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Employee ID is required"
            ]);
        }
    
        try {
            $employeeModel = new EmployeModel();
            $existingEmployee = $employeeModel->find($empId);
    
            if (!$existingEmployee) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Employee not found"
                ]);
            }
    
            // Get input data for update
            $empName = $this->request->getVar("emp_name") ?? $existingEmployee['emp_name'];
            $empRole = $this->request->getVar("emp_role") ?? $existingEmployee['emp_role'];
            $empPassword = $this->request->getVar("emp_password");
    
            $updateData = [
                "emp_name" => $empName,
                "emp_role" => $empRole,
            ];
    
            if ($empPassword) {
                $updateData["emp_password"] = base64_encode($empPassword);
            }
    
            // Handle image file if provided
            $imageFile = $this->request->getFile('emp_image');
            if (isset($imageFile) && !empty($imageFile)) {
                $uploadPath = FCPATH . 'public/assets/img/uploads/UserImage/';
    
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, TRUE);
                }
    
                if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Upload directory is not writable',
                    ]);
                }
    
                $imageFile->move($uploadPath);
    
                if ($imageFile->hasMoved()) {
                    $updateData['emp_image'] = 'https://api.advanceengineerings.com/public/assets/img/uploads/UserImage/' . $imageFile->getName();
                } else {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Failed to upload Profile Image.'
                    ]);
                }
            }
    
            // Update employee record
            $employeeModel->update($empId, $updateData);
    
            return $this->response->setJSON([
                "status" => "success",
                "message" => "Employee updated successfully",
                "data" => $updateData
            ]);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    

    public function RemoveEmployee() {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    
        // Get employee ID from request
        $empId = $this->request->getVar("emp_id");
    
        if (!$empId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Employee ID is required"
            ]);
        }
    
        try {
            $employeeModel = new EmployeModel();
            $existingEmployee = $employeeModel->find($empId);
    
            if (!$existingEmployee) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Employee not found"
                ]);
            }
    
            // Delete employee record
            $deleteEmployee = $employeeModel->delete($empId);
    
            if ($deleteEmployee) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Employee removed successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to remove employee"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    
}
