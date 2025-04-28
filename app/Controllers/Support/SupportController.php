<?php

namespace App\Controllers\Support;
helper("token");
use App\Models\crm\SupportModel;
use CodeIgniter\RESTful\ResourceController;

class SupportController extends ResourceController
{
    public function addSupport()
    {
        $supportModel = new SupportModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "s_order_no" => $this->request->getVar("s_order_no") ?? "",
            "s_contact" => $this->request->getVar("s_contact") ?? "",
            "s_poc" => $this->request->getVar("s_poc") ?? "",
            "s_item" => $this->request->getVar("s_item") ?? "",
            "s_due_date" => $this->request->getVar("s_due_date") ?? "",
            "s_qty" => $this->request->getVar("s_qty") ?? "",
            "s_pndg" => $this->request->getVar("s_pndg") ?? "",
            "s_done" => $this->request->getVar("s_done") ?? "",
            "s_unit" => $this->request->getVar("s_unit") ?? "",
            "s_status" => $this->request->getVar("s_status") ?? "",
            "s_total" => $this->request->getVar("s_total") ?? "",

        ];

        try {
            $addSuport = $supportModel->insert($data);
            if ($addSuport) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => " added successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Not added somthing went wrong"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function updateSupport()
    {
        $supportModel = new SupportModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $supportId = $this->request->getVar("s_id");

        if (!$supportId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => " ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $supportModel->find($supportId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Support inquiry  not found"
            ]);
        }

        $data = [
            "s_order_no" => $this->request->getVar("s_order_no") ?? "",
            "s_contact" => $this->request->getVar("s_contact") ?? "",
            "s_poc" => $this->request->getVar("s_poc") ?? "",
            "s_item" => $this->request->getVar("s_item") ?? "",
            "s_due_date" => $this->request->getVar("s_due_date") ?? "",
            "s_qty" => $this->request->getVar("s_qty") ?? "",
            "s_pndg" => $this->request->getVar("s_pndg") ?? "",
            "s_done" => $this->request->getVar("s_done") ?? "",
            "s_unit" => $this->request->getVar("s_unit") ?? "",
            "s_status" => $this->request->getVar("s_status") ?? "",
            "s_total" => $this->request->getVar("s_total") ?? "",

        ];

        try {
            // Perform the update
            $updateSuccess = $supportModel->update($supportId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to update "
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function removeSupport()
    {
        $supportModel = new  SupportModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $Id = $this->request->getVar("s_id");

        if (!$Id) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $supportModel->find($Id);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Id not found"
            ]);
        }

        try {
            // Perform the delete
            $deleteSuccess = $supportModel->delete($Id);

            // Check if the delete was successful
            if ($deleteSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "deleted successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to delete "
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function fetchSupport()
    {
        $supportModel = new SupportModel();

        try {
            // Get the page number from the query string, default is 1
            $page = $this->request->getGet("page") ?? 1;
            $limit = 10; // Number of records per page
            $offset = ($page - 1) * $limit; // Calculate offset

            // Fetch manufacturing data with limit and offset for pagination
            $all = $supportModel->orderBy("s_id", "DESC")
                ->findAll($limit, $offset); // Find records with limit and offset

            // Count the total number of records to calculate pagination
            $count = $supportModel->countAllResults();

            // If records found, return data with pagination info
            if ($all) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($all) . ' supoort  found',
                    "data" => $all,
                    "count" => $count // Total count for pagination purposes
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No support inquiry found',
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
