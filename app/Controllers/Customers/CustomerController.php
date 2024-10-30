<?php

namespace App\Controllers\Customers;

use App\Controllers\BaseController;
use App\Models\Customer\CustomerModel;
use CodeIgniter\RESTful\ResourceController;

class CustomerController extends ResourceController
{

    public function fetchCustomer()
    {
        $customerModel = new CustomerModel();

        try {

            $page = $this->request->getGet("page") ?? 1;
            $page = ($page * 20) - 20;

            $search = $this->request->getGet("search") ?? "";

            // $allCustomer = $customerModel->findAll();
            $allCustomer = $customerModel->orderBy("c_id", "DESC")->like("c_fullname", $search)->orLike("c_company_name", $search)->findAll(20, $page);
            $countCustomer = $customerModel->like("c_fullname", $search)->orLike("c_company_name", $search)->countAllResults();


            if ($allCustomer) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allCustomer) . ' Customer found',
                    "data" => $allCustomer,
                    "count" => $countCustomer
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Customer not found',
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

    public function fetchCustomerNameList()
    {
        $customerModel = new CustomerModel();

        try {
            $customerArr = [];
            $allCustomer = $customerModel->findAll();

            foreach ($allCustomer as $customer) {
                $obj = [];
                $obj['c_fullname'] = $customer['c_fullname'];
                $obj['c_id'] = $customer['c_id'];
                $customerArr[] = $obj;
            }

            if ($allCustomer) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allCustomer) . ' Customer found',
                    "data" => $customerArr,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Customer not found',
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

    public function creatCustomer()
    {
        $customerModel = new CustomerModel();

        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- START ------------>

        $imageFile = $this->request->getFile('c_image');

        if (!$imageFile) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Please Upload Image"
            ]);
        }


        if (isset($imageFile) && !empty($imageFile)) {

            $uploadPath = FCPATH . 'public/assets/img/uploads/customerImage/';

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
                $fileName = $imageFile->getName();
                // $fileName = 'https://OUR DOMAIN/public/assets/img/uploads/customerImage/' . $imageFile->getName();
                // $imageFile = $fileName;
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to upload Profile Image.'
                ]);
            }

            // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ END ---------------> 

        }

        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- END ------------>

        $c_password = $this->request->getVar("c_password") ?? "Advance123";

        $data = [
            "c_fullname" => $this->request->getVar("c_fullname") ?? "",
            "c_password" => hash('sha256', $c_password),
            "c_company_name" => $this->request->getVar("c_company_name") ?? "",
            "c_email" => $this->request->getVar("c_email") ?? "",
            "c_mobile" => $this->request->getVar("c_mobile") ?? "",
            "c_post" => $this->request->getVar("c_post") ?? "",
            "c_department" => $this->request->getVar("c_department") ?? "",
            "c_status" => $this->request->getVar("c_status") ?? "",
            "c_gender" => $this->request->getVar("c_gender") ?? "",
            "c_description" => $this->request->getVar("c_description") ?? "",
            "c_dob" => $this->request->getVar("c_dob") ?? "",
            "c_address" => $this->request->getVar("c_address") ?? "",
            "c_rank" => $this->request->getVar("c_rank") ?? "",
            "c_no_of_quotation" => $this->request->getVar("c_no_of_quotation") ?? "",
            "c_image" => $fileName ?? "",
        ];

        try {
            $customerAdd = $customerModel->insert($data);
            if ($customerAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Customer uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Customer not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function updateCustomer()
    {
        // $isToken = check_jwt_authentication();

        // if (!$isToken) {
        //     return $this->response->setJSON([
        //         "status" => "error",
        //         "message" => "Authentication failed"
        //     ]);
        // }

        $customerModel = new CustomerModel();

        $c_password = $this->request->getVar("c_password") ?? "Advance123";

        $data = [
            "c_fullname" => $this->request->getVar("c_fullname") ?? "",
            "c_password" => hash('sha256', $c_password),
            "c_company_name" => $this->request->getVar("c_company_name") ?? "",
            "c_email" => $this->request->getVar("c_email") ?? "",
            "c_mobile" => $this->request->getVar("c_mobile") ?? "",
            "c_post" => $this->request->getVar("c_post") ?? "",
            "c_department" => $this->request->getVar("c_department") ?? "",
            "c_status" => $this->request->getVar("c_status") ?? "",
            "c_gender" => $this->request->getVar("c_gender") ?? "",
            "c_description" => $this->request->getVar("c_description") ?? "",
            "c_dob" => $this->request->getVar("c_dob") ?? "",
            "c_address" => $this->request->getVar("c_address") ?? "",
            "c_rank" => $this->request->getVar("c_rank") ?? "",
            "c_no_of_quotation" => $this->request->getVar("c_no_of_quotation") ?? "",
        ];

        $customerId = $this->request->getGet("id");

        try {

            $customerModel->update($customerId, $data);
            $updateCustomer = $customerModel->where("c_id", $customerId)->first();
            if ($updateCustomer) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Customer updated",
                    "data" => $updateCustomer
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Customer not update"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage() . " " . $th->getLine()
            ]);
        }
    }
}
