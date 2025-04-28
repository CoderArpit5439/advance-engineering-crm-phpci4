<?php

namespace App\Controllers\Inventory;

helper("token");

use App\Models\crm\InventoryModel;
use CodeIgniter\RESTful\ResourceController;

class InventoryController extends ResourceController
{
    public function addInventory()
    {
        $InventoryModel = new InventoryModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "in_name" => $this->request->getVar("in_name") ?? "",
            "in_code" => $this->request->getVar("in_code") ?? "",
            "in_cat" => $this->request->getVar("in_cat") ?? "",
            "in_qty" => $this->request->getVar("in_qty") ?? "",
            "in_rate" => $this->request->getVar("in_rate") ?? "",
            "in_value" => $this->request->getVar("in_value") ?? "",

        ];

        try {
            $inventoryAdd = $InventoryModel->insert($data);
            if ($inventoryAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Inventory added successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Inventory not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function updateInventory()
    {
        $InventoryModel = new InventoryModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $inventoryId = $this->request->getVar("in_id");

        if (!$inventoryId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inventory ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $InventoryModel->find($inventoryId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inventory  not found"
            ]);
        }

        $data = [
            "in_name" => $this->request->getVar("in_name") ?? "",
            "in_code" => $this->request->getVar("in_code") ?? "",
            "in_cat" => $this->request->getVar("in_cat") ?? "",
            "in_qty" => $this->request->getVar("in_qty") ?? "",
            "in_rate" => $this->request->getVar("in_rate") ?? "",
            "in_value" => $this->request->getVar("in_value") ?? "",
        ];

        try {
            // Perform the update
            $updateSuccess = $InventoryModel->update($inventoryId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Inventory updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to update inventory"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function removeInventory()
    {
        $InventoryModel = new InventoryModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $inventoryId = $this->request->getVar("in_id");

        if (!$inventoryId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "inquiry ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $InventoryModel->find($inventoryId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inventoryrecord not found"
            ]);
        }

        try {
            // Perform the delete
            $deleteSuccess = $InventoryModel->delete($inventoryId);

            // Check if the delete was successful
            if ($deleteSuccess) {
              
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Inventory deleted successfully",
                    
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to delete inventory"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function fetchInventory()
    {
        $InventoryModel = new InventoryModel();

        try {

            $page = $this->request->getGet("page") ?? 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;


            $allInventory = $InventoryModel->orderBy("in_id", "DESC")
                ->findAll($limit, $offset); // Find records with limit and offset

            // Count the total number of records to calculate pagination
            $countInventory = $InventoryModel->countAllResults();

            // If records found, return data with pagination info
            if ($allInventory) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allInventory) . " inventory's  found",
                    "data" => $allInventory,
                    "count" => $countInventory // Total count for pagination purposes
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No Inventory found',
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
