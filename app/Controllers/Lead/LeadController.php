<?php

namespace App\Controllers\Lead;

helper("token");

use App\Models\crm\LeadModel;
use CodeIgniter\RESTful\ResourceController;

class LeadController extends ResourceController
{
    public function fetchLeads()
    {
        $leadModel = new LeadModel();
        $isToken = check_jwt_authentication();
    
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    
        try {
          
            $offset = (int)($this->request->getGet("offset") ?? 0); 
            $limit = (int)($this->request->getGet("limit") ?? 10); 
            $search = $this->request->getGet("search") ?? "";
    
            // Make sure offset and limit are non-negative
            if ($offset < 0) {
                $offset = 0;
            }
            if ($limit <= 0) {
                $limit = 10;
            }
    
            // Fetch leads with pagination and search
            $allLeads = $leadModel->orderBy("l_id", "DESC")
                ->like("l_name", $search)
                ->orLike("l_email", $search)
                ->findAll($limit, $offset); // Correct the parameters: LIMIT, OFFSET
    
            // Count total leads for pagination purposes
            $countLeads = $leadModel->like("l_name", $search)
                ->orLike("l_email", $search)
                ->countAllResults();
    
            if ($allLeads) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allLeads) . ' Leads found',
                    "data" => $allLeads,
                    "count" => $countLeads
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No leads found',
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


    public function createLead()
    {
        $leadModel = new LeadModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "l_name" => $this->request->getVar("l_name") ?? "",
            "l_source" => $this->request->getVar("l_source") ?? "",
            "l_mobile" => $this->request->getVar("l_mobile") ?? "",
            "l_email" => $this->request->getVar("l_email") ?? "",
            "l_address" => $this->request->getVar("l_address") ?? "",
            "l_type" => $this->request->getVar("l_type") ?? "",
            "l_join" => $this->request->getVar("l_join") ?? "",
        ];

        try {
            $leadAdded = $leadModel->insert($data);
            if ($leadAdded) {
                // Fetch all leads after addition
                $allLeads = $leadModel->findAll();

                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Lead added successfully",
                    "data" => $allLeads // Return all leads after addition
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Lead not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }


    public function updateLead()
    {
        $leadModel = new LeadModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "l_name" => $this->request->getVar("l_name") ?? "",
            "l_source" => $this->request->getVar("l_source") ?? "",
            "l_mobile" => $this->request->getVar("l_mobile") ?? "",
            "l_email" => $this->request->getVar("l_email") ?? "",
            "l_address" => $this->request->getVar("l_address") ?? "",
            "l_type" => $this->request->getVar("l_type") ?? "",
            "l_join" => $this->request->getVar("l_join") ?? "",
        ];

        $leadId = $this->request->getVar("l_id");
        if (!$leadId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Lead ID is required"
            ]);
        }
        $existingRecord = $leadModel->find($leadId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "lead not found"
            ]);
        }

        try {
            // Perform the update
            $updateSuccess = $leadModel->update($leadId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                $allLeads = $leadModel->findAll();

                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Lead update successfully",
                    "data" => $allLeads
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



    public function removeLead()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        // Get lead ID from request
        $leadId = $this->request->getVar("lead_id");

        if (!$leadId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Lead ID is required"
            ]);
        }

        try {
            $leadModel = new LeadModel();
            $existingLead = $leadModel->find($leadId);

            if (!$existingLead) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Lead not found"
                ]);
            }

            // Delete lead record
            $deleteLead = $leadModel->delete($leadId);

            if ($deleteLead) {

                $allLeads = $leadModel->findAll();

                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Lead removed successfully",
                    "data" => $allLeads
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to remove lead"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    
    public function searchLeadByMobile()
    {
        $leadModel = new LeadModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        try {

            $search = $this->request->getGet("search") ?? "";

            if (empty($search)) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Search parameter is required"
                ]);
            }


            if (preg_match('/^[0-9]{10}$/', $search)) {

                $leads = $leadModel->like("l_mobile", $search)->findAll();
            } else {

                $leads = $leadModel->like("l_name", $search)->findAll();
            }

            if ($leads) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($leads) . ' Lead(s) found',
                    "data" => $leads
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'false',
                    'data' => [],
                    'message' => 'No leads found with the provided input',
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
