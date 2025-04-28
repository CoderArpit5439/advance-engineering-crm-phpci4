<?php

namespace App\Controllers\Manufacturing;

use App\Models\crm\ManufacturingModel;

helper("token");

use CodeIgniter\RESTful\ResourceController;

class ManufacturingController extends ResourceController
{
    // Add manufacturing record
    public function addManufacturing()
    {
        $ManufacturingModel = new ManufacturingModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "m_category" => $this->request->getVar("m_category") ?? "",
            "m_product" => $this->request->getVar("m_product") ?? "",
            "m_code" => $this->request->getVar("m_code") ?? "",
            "m_customer" => $this->request->getVar("m_customer") ?? "",
            "m_launch" => $this->request->getVar("m_launch") ?? "",
            "m_target" => $this->request->getVar("m_target") ?? "",
            "m_stage" => $this->request->getVar("m_stage") ?? "",
            "m_quantity" => $this->request->getVar("m_quantity") ?? "",
            "m_unit" => $this->request->getVar("m_unit") ?? "",
        ];

        try {
            $manufacturingAdd = $ManufacturingModel->insert($data);
            if ($manufacturingAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Manufacturing added successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Manufacturing not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    // Update manufacturing record
    public function updateManufacturing()
    {
        $ManufacturingModel = new ManufacturingModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $manufacturingId = $this->request->getVar("m_id");

        if (!$manufacturingId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Manufacturing ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $ManufacturingModel->find($manufacturingId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Manufacturing record not found"
            ]);
        }

        // Prepare the data for updating
        $data = [
            "m_category" => $this->request->getVar("m_category") ?? "",
            "m_product" => $this->request->getVar("m_product") ?? "",
            "m_code" => $this->request->getVar("m_code") ?? "",
            "m_customer" => $this->request->getVar("m_customer") ?? "",
            "m_launch" => $this->request->getVar("m_launch") ?? "",
            "m_target" => $this->request->getVar("m_target") ?? "",
            "m_stage" => $this->request->getVar("m_stage") ?? "",
            "m_quantity" => $this->request->getVar("m_quantity") ?? "",
            "m_unit" => $this->request->getVar("m_unit") ?? "",
        ];

        try {
            // Perform the update
            $updateSuccess = $ManufacturingModel->update($manufacturingId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Manufacturing record updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to update manufacturing record"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }


    public function removemanufacturing()
    {
        $ManufacturingModel = new ManufacturingModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $manufacturingId = $this->request->getVar("m_id");

        if (!$manufacturingId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Manufacturing ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $ManufacturingModel->find($manufacturingId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Manufacturing record not found"
            ]);
        }

        try {
            // Perform the delete
            $deleteSuccess = $ManufacturingModel->delete($manufacturingId);

            // Check if the delete was successful
            if ($deleteSuccess) {
                $findAllData = $ManufacturingModel->findAll();
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Manufacturing record deleted successfully",
                    "data" => $findAllData
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to delete manufacturing record"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function manufacturingFetch()
    {
        $manufacturingModel = new ManufacturingModel();

        try {

            $page = $this->request->getGet("page") ?? 1;
            $limit = 10; // Number of records per page
            $offset = ($page - 1) * $limit; // Calculate offset

            $search = $this->request->getGet("search") ?? "";

            $allManufacturing = $manufacturingModel->orderBy("m_id", "DESC")
                ->like("m_product", $search)
                ->orLike("m_category", $search)
                ->findAll($limit, $offset);

            $countManufacturing = $manufacturingModel->like("m_product", $search) // Same search condition for count
                ->orLike("m_category", $search) // Apply search to other fields as well
                ->countAllResults();

            // If records found, return data with pagination info
            if ($allManufacturing) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allManufacturing) . ' manufacturing records found',
                    "data" => $allManufacturing,
                    "count" => $countManufacturing // Total count for pagination purposes
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No manufacturing records found',
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
