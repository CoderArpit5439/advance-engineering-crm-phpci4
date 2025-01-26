<?php

namespace App\Controllers\Inquiry;
helper("token");
use App\Models\crm\InquiryModel;
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

        ];

        try {
            $InquiryAdd = $InquiryModel->insert($data);
            if ($InquiryAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Inquiry added successfully",
                    "data" => $data
                ]);
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
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "inquiry deleted successfully"
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
            // Get the page number from the query string, default is 1
            $page = $this->request->getGet("page") ?? 1;
            $limit = 10; // Number of records per page
            $offset = ($page - 1) * $limit; // Calculate offset

            // Fetch manufacturing data with limit and offset for pagination
            $allInquiry = $InquiryModel->orderBy("inq_id", "DESC")
                ->findAll($limit, $offset); // Find records with limit and offset

            // Count the total number of records to calculate pagination
            $countInquiry = $InquiryModel->countAllResults();

            // If records found, return data with pagination info
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
