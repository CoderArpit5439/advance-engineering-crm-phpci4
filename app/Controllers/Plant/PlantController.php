<?php

namespace App\Controllers\Plant;

use App\Models\crm\CompanyModel;
use App\Models\crm\PlantModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
class PlantController extends ResourceController
{
    public function AddPlant()
{
    // Step 1: Check JWT Token
    $isToken = check_jwt_authentication();
    if (!$isToken) {
        return $this->response->setJSON([
            "status" => "error",
            "message" => "Authentication failed",
        ]);
    }

    // Step 2: Get company ID from request
    $companyId = $this->request->getVar("company_id");

    // Step 3: Fetch company name by ID
    $companyModel = new CompanyModel();
    $company = $companyModel->find($companyId);

    if (!$company) {
        return $this->response->setJSON([
            "status" => "error",
            "message" => "Invalid company ID",
        ]);
    }

    $companyName = $company['c_name']; 
    $data = [
        "c_id" => $companyId,
        "company_name" => $companyName,
        "p_state" => $this->request->getVar("p_state") ?? "",
        "p_city" => $this->request->getVar("p_city") ?? "",
        "p_area_working" => $this->request->getVar("p_area_working") ?? "",
        "p_tax_type" => $this->request->getVar("p_tax_type") ?? "",
        "p_pincode" => $this->request->getVar("p_pincode") ?? "",
        "p_address" => $this->request->getVar("p_address") ?? "",
        "p_gst" => $this->request->getVar("p_gst") ?? "",
        "p_security_contact" => $this->request->getVar("p_security_contact") ?? "",
        "p_account_contact" => $this->request->getVar("p_account_contact") ?? "",
        "p_store_contact" => $this->request->getVar("p_store_contact") ?? "",
        "p_other_contact" => $this->request->getVar("p_other_contact") ?? "",
        "p_security_email" => $this->request->getVar("p_security_email") ?? "",
        "p_account_email" => $this->request->getVar("p_account_email") ?? "",
        "p_store_email" => $this->request->getVar("p_store_email") ?? "",
        "p_other_email" => $this->request->getVar("p_other_email") ?? "",
        "p_international_domestic" => $this->request->getVar("p_international_domestic") ?? "",

    ];

    // Step 5: Insert into database
    try {
        $plantModel = new PlantModel();
        $inserted = $plantModel->insert($data);

        if ($inserted) {
            return $this->response->setJSON([
                "status" => "true",
                "message" => "Plant added successfully",
                "data" => $data,
            ]);
        } else {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Failed to add plant.",
            ]);
        }
    } catch (\Throwable $th) {
        return $this->response->setJSON([
            "status" => "error",
            "message" => $th->getMessage(),
        ]);
    }
}


    public function GetPlant()
    {
        try {

            $plantModel = new PlantModel();
            $allPlant = $plantModel->orderBy("p_id", "DESC")->findAll();
            $countPlant = $plantModel->countAllResults();

            if ($allPlant) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allPlant) . ' plants found',
                    "data" => $allPlant,
                    "count" => $countPlant
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'plants not found',
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

    public function UpdatePlants()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        $id = $this->request->getVar('p_id');
        if (!$id) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Plant ID is required"
            ]);
        }

        $plantModel = new PlantModel();
        $existingPlant = $plantModel->find($id);

        if (!$existingPlant) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Plant not found"
            ]);
        }

    $companyId = $this->request->getVar("c_id");

    $companyModel = new CompanyModel();
    $company = $companyModel->find($companyId);

    if (!$company) {
        return $this->response->setJSON([
            "status" => "error",
            "message" => "Invalid company ID",
        ]);
    }

    $companyName = $company['c_name']; 

        $data = [
            "c_id" => $companyId,
            "company_name" =>$companyName,
            "p_state" => $this->request->getVar("p_state") ?? $existingPlant['p_state'],
            "p_city" => $this->request->getVar("p_city") ?? $existingPlant['p_city'],
            "p_area_working" => $this->request->getVar("p_area_working") ?? $existingPlant['p_area_working'],
            "p_tax_type" => $this->request->getVar("p_tax_type") ?? $existingPlant['p_tax_type'],
            "p_pincode" => $this->request->getVar("p_pincode") ?? $existingPlant['p_pincode'],
            "p_address" => $this->request->getVar("p_address") ?? $existingPlant['p_address'],
            "p_gst" => $this->request->getVar("p_gst") ?? $existingPlant['p_gst'],
            "p_security_contact" => $this->request->getVar("p_security_contact") ?? $existingPlant['p_security_contact'],
            "p_account_contact" => $this->request->getVar("p_account_contact") ?? $existingPlant['p_account_contact'],
            "p_store_contact" => $this->request->getVar("p_store_contact") ?? $existingPlant['p_store_contact'],
            "p_other_contact" => $this->request->getVar("p_other_contact") ?? $existingPlant['p_other_contact'],
            "p_security_email" => $this->request->getVar("p_security_email") ?? $existingPlant['p_security_email'],
            "p_account_email" => $this->request->getVar("p_account_email") ?? $existingPlant['p_account_email'],
            "p_store_email" => $this->request->getVar("p_store_email") ?? $existingPlant['p_store_email'],
            "p_other_email" => $this->request->getVar("p_other_email") ?? $existingPlant['p_other_email'],
            "p_international_domestic" => $this->request->getVar("p_international_domestic") ?? $existingPlant['p_international_domestic'],
        ];

        try {
            $updated = $plantModel->update($id, $data);

            if ($updated) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Plant updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "No changes were made or update failed"
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
