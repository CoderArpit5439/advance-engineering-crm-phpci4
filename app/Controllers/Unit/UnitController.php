<?php
namespace App\Controllers\Unit;

use App\Models\Crm\CompanyModel;
use App\Models\crm\PlantModel;
use App\Models\Crm\UnitModel;
use App\Models\Customer\CustomerModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
class UnitController extends ResourceController
{
    public function AddUnit()
    {
        // Step 1: Check JWT Token
        $isToken = check_jwt_authentication();
        if (! $isToken) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Authentication failed",
            ]);
        }

        // Step 2: Get company ID from request
        // $companyId = $this->request->getVar("company_id");
        $plantId = $this->request->getVar("u_plant_id") ?? 0;

        // Step 3: Fetch company name by ID
        $plantModel  = new PlantModel();
        $plantDetail = $plantModel->find($plantId);

        if (! $plantDetail) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Invalid company ID",
            ]);
        }

        $data = [
            "u_name"       => $this->request->getVar("u_name") ?? "",
            "u_company_id" => $this->request->getVar("u_company_id") ?? "",
            "u_plant_id"   => $plantId ?? "",
        ];

        // Step 5: Insert into database
        try {
            $unitModel = new UnitModel();
            $inserted  = $unitModel->insert($data);

            if ($inserted) {
                return $this->response->setJSON([
                    "status"  => "true",
                    "message" => "Unit added successfully",
                    "data"    => $data,
                ]);
            } else {
                return $this->response->setJSON([
                    "status"  => "error",
                    "message" => "Failed to add Unit.",
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => $th->getMessage(),
            ]);
        }
    }

    public function GetUnit()
    {
        try {
            $unitModel    = new UnitModel();
            $companyModel = new CompanyModel();
            $plantModel   = new PlantModel();
            $allUnit      = $unitModel->orderBy("u_id", "DESC")->findAll();
            $countUnit    = $unitModel->countAllResults();
            $updatedArray = [];
            foreach ($allUnit as $unit) {
                $companyDetails = $companyModel->where('c_id', $unit['u_company_id'])->first();
                if ($companyDetails) {
                    $unit['companyDetails'] = $companyDetails;
                }

                $plantDetails = $plantModel->where('p_id', $unit['u_plant_id'])->first();
                if ($plantDetails) {
                    $unit['plantDetails'] = $plantDetails;
                }

                $updatedArray[] = $unit;

            }

            if ($allUnit) {
                return $this->response->setJSON([
                    "status"  => "success",
                    "message" => count($allUnit) . ' units found',
                    "data"    => $updatedArray,
                    "count"   => $countUnit,
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'data'    => null,
                    'message' => 'units not found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $th->getTrace(),
                'data'    => null,
            ]);
        }
    }

    public function UpdateUnit()
    {
        $isToken = check_jwt_authentication();
        if (! $isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status"  => "error",
            ]);
        }

        $id = $this->request->getVar('u_id');
        if (! $id) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "unit ID is required",
            ]);
        }

        $unitModel    = new UnitModel();
        $existingUnit = $unitModel->find($id);

        if (! $existingUnit) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Unit not found",
            ]);
        }

        $plantId = $this->request->getVar("c_id");

        $PlantModel  = new PlantModel();
        $plantDetail = $PlantModel->find($plantId);

        if (! $plantDetail) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => "Invalid Plant ID",
            ]);
        }

        $data = [
            "u_name"       => $this->request->getVar("u_name") ?? $existingUnit['u_name'],
            "u_company_id" => $this->request->getVar("u_company_id") ?? $existingUnit['u_company_id'],
            "u_plant_id"   => $plantId ?? "",
        ];

        try {
            $updated = $unitModel->update($id, $data);

            if ($updated) {
                return $this->response->setJSON([
                    "status"  => "success",
                    "message" => "Unit updated successfully",
                    "data"    => $data,
                ]);
            } else {
                return $this->response->setJSON([
                    "status"  => "error",
                    "message" => "No changes were made or update failed",
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status"  => "error",
                "message" => $th->getMessage(),
            ]);
        }
    }

    public function fetchSingleUnit()
    {

        $isToken = check_jwt_authentication();
        if (! $isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status"  => "error",
            ]);
        }

        try {
            $unit_id       = $this->request->getVar('id');
            $UnitModel     = new UnitModel();
            $customerModel = new CustomerModel();
            $plantModel    = new PlantModel();
            $companyModel    = new CompanyModel();
            // $quotationModel = new QuotationModel();
            $singleUnit = $UnitModel->where('u_id', $unit_id)->first();

            $customerList = [];
            if ($singleUnit) {

                $plantDetails = $plantModel->where('p_id', $singleUnit['u_plant_id'])->first();
                if ($plantDetails) {
                    $singleUnit['plantDetails'] = $plantDetails;
                }
                
                $companyDetails = $companyModel->where('c_id', $singleUnit['u_company_id'])->first();
                if ($companyDetails) {
                    $singleUnit['companyDetails'] = $companyDetails;
                }

                $customerDetails = $customerModel->where('c_unit_id', $unit_id)->findAll();
                foreach ($customerDetails as $customer) {
                    $customerList[] = $customer;
                }
            }
            // $all_quotation_of_company =

            if ($singleUnit) {
                return $this->response->setJSON([
                    "status"       => "success",
                    "message"      => 'Unit found',
                    "data"         => $singleUnit,
                    "customerList" => $customerList,

                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'data'    => null,
                    'message' => 'Unit not found',
                ]);
            }

        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $th->getTrace(),
                'data'    => null,
            ]);
        }

    }
}
