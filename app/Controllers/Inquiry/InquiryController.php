<?php

namespace App\Controllers\Inquiry;

use App\Models\crm\QuotationProductionModel;

helper("token");

use App\Models\crm\InquiryModel;
use App\Models\Product\ProductModel;
use CodeIgniter\RESTful\ResourceController;

class InquiryController extends ResourceController
{

    public function addInquiry()
    {
        $InquiryModel = new InquiryModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "inq_contact" => $this->request->getVar("inq_contact") ?? "",
            "inq_message" => $this->request->getVar("inq_message") ?? "",
            "inq_name" => $this->request->getVar("inq_name") ?? "",
            "inq_email" => $this->request->getVar("inq_email") ?? "",
            "inq_status" => $this->request->getVar("inq_status") ?? "",
            "p_size" => $this->request->getVar("p_size") ?? "",
            "p_moc" => $this->request->getVar("p_moc") ?? "",
            "p_thickness" => $this->request->getVar("p_thickness") ?? "",
            "p_drg" => $this->request->getVar("p_drg") ?? "",
            "p_code" => $this->request->getVar("p_code") ?? "",
            "p_info" => $this->request->getVar("p_info") ?? "",

        ];

        try {
            $InquiryAdd = $InquiryModel->insert($data);
            if ($InquiryAdd) {
                $insertedId = $InquiryModel->getInsertID();
                $newId['id'] = $insertedId;
                $quotationProduction = new QuotationProductionModel();
                $data = [
                    "inq_id" => $newId ?? "",
                    "p_category" => $p_category ?? "",
                    "p_unique_id" => $this->request->getVar("p_unique_id") ?? "",
                    "p_name" => $this->request->getVar("p_name") ?? "",
                    "p_price" => $this->request->getVar("p_price") ?? "",
                    "p_material" => $this->request->getVar("p_material") ?? "",
                    "p_moc" => $this->request->getVar("p_moc") ?? "",
                    "p_dimension" => $this->request->getVar("p_dimension") ?? "",
                    "p_brand" => $this->request->getVar("p_brand") ?? "",
                    "p_color" => $this->request->getVar("p_color") ?? "",
                    "p_weight" => $this->request->getVar("p_weight") ?? "",
                    "p_description" => $this->request->getVar("p_description") ?? "",
                    "p_manufacturer" => $this->request->getVar("p_manufacturer") ?? "",
                    "p_country" => $this->request->getVar("p_country") ?? "",
                    "p_code" => $this->request->getVar("p_code") ?? "",
                    "p_drawing_no" => $this->request->getVar("p_drawing_no") ?? "",
                    "p_finish_type" => $this->request->getVar("p_finish_type") ?? "",
                    "p_status" => $this->request->getVar("p_status") ?? "",
                    "customerId" => $this->request->getVar("customerId") ?? "",
                    "customerName" => $this->request->getVar("customerName") ?? ""
                ];

                try {
                    $productAdd = $quotationProduction->insert($data);
                    if ($productAdd) {
                        return $this->response->setJSON([
                            "status" => true,
                            "message" => " uploaded successfully",
                            "data" => $data
                        ]);
                    } else {
                        return $this->response->setJSON([
                            "status" => false,
                            "message" => "Something went wrong"
                        ]);
                    }
                } catch (\Throwable $th) {
                    return $this->response->setJSON([
                        "status" => false,
                        "message" => $th->getMessage()
                    ]);
                }
              
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Inquiry not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function updateInquiry()
    {
        $InquiryModel = new InquiryModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $InquiryId = $this->request->getVar("inq_id");

        if (!$InquiryId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inquiry ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $InquiryModel->find($InquiryId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inquiry record not found"
            ]);
        }

        // Prepare the data for updating
        $data = [
            "inq_contact" => $this->request->getVar("inq_contact") ?? "",
            "inq_message" => $this->request->getVar("inq_message") ?? "",
            "inq_name" => $this->request->getVar("inq_name") ?? "",
            "inq_email" => $this->request->getVar("inq_email") ?? "",
            "inq_status" => $this->request->getVar("inq_status") ?? "",

        ];

        try {
            // Perform the update
            $updateSuccess = $InquiryModel->update($InquiryId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Inquiry updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to update Inquiry"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function removeInquiry()
    {
        $InquiryModel = new InquiryModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $inquiryId = $this->request->getVar("inq_id");

        if (!$inquiryId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "inquiry ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $InquiryModel->find($inquiryId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "inquiry record not found"
            ]);
        }

        try {
            // Perform the delete
            $deleteSuccess = $InquiryModel->delete($inquiryId);

            // Check if the delete was successful
            if ($deleteSuccess) {
                $findAllData = $InquiryModel->findAll();
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "inquiry deleted successfully",
                    "data" => $findAllData
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to delete inquiry"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function inquiryFetch()
    {
        $InquiryModel = new InquiryModel();

        try {
         
            $page = $this->request->getGet("page") ?? 1;
            $limit = 10; 
            $offset = ($page - 1) * $limit; 
            $allInquiry = $InquiryModel->orderBy("inq_id", "DESC")
                ->findAll($limit, $offset); 
            $countInquiry = $InquiryModel->countAllResults();
            if ($allInquiry) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allInquiry) . ' inquiry records found',
                    "data" => $allInquiry,
                    "count" => $countInquiry // Total count for pagination purposes
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No inquiry records found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ]);
        }
    }
}
