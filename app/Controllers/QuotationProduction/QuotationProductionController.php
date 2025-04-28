<?php

namespace App\Controllers\QuotationProduction;

use App\Models\crm\QuotationProductionModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
class QuotationProductionController extends ResourceController
{
    public function addQuotationProductionController()
    {
        $quotationProduction = new QuotationProductionModel();

        $data = [
            "p_category" => $p_category ?? "",
            "p_unique_id" => $this->request->getVar("p_unique_id") ?? "",
            "p_name" => $this->request->getVar("p_name") ?? "",
            "p_slug" => $productSlug ?? "",
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
    }

    public function getSingleQuotationProduction(){
        $qpModel = new QuotationProductionModel();
        $inqId = $this->request->getVar("inq_id");

        if (!$inqId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'inquery ID is required.',
                'data' => []
            ]);
        }

        try {
            $productDetail = $qpModel->where('inq_id', $inqId)->findAll();

            if ($productDetail) {
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Product(s) fetched successfully.',
                    'data' => $productDetail,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'No products found for this quotation ID.',
                    'data' => [],
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

  
}
