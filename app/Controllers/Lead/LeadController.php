<?php

namespace App\Controllers\Lead;

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
            $page = $this->request->getGet("page") ?? 1;
            $page = ($page * 20) - 20;
            $search = $this->request->getGet("search") ?? "";

            $allLeads = $leadModel->orderBy("l_id", "DESC")
                ->like("l_name", $search)
                ->orLike("l_email", $search)
                ->findAll(20, $page);

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
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Lead added successfully",
                    "data" => $data
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

        $leadId = $this->request->getGet("id");

        try {
            $leadModel->update($leadId, $data);
            $updatedLead = $leadModel->where("l_id", $leadId)->first();
            if ($updatedLead) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Lead updated",
                    "data" => $updatedLead
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Lead not updated"
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
