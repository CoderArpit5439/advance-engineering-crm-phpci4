<?php

namespace App\Controllers\Quotation;

use App\Models\crm\QuotationModel;
use CodeIgniter\RESTful\ResourceController;

class QuotationController extends ResourceController
{

    public function fetchQuotation()
    {
        $QuotationModel = new QuotationModel();

        try {

            $page = $this->request->getGet("page") ?? 1;
            $page = ($page * 20) - 20;

            $search = $this->request->getGet("search") ?? "";

            // $allQuotation = $QuotationModel->findAll();
            $allQuotation = $QuotationModel->orderBy("quo_id", "DESC")->like("quo_name", $search)->findAll(20, $page);
            $countQuotation = $QuotationModel->like("quo_name", $search)->countAllResults();

            if ($allQuotation) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => count($allQuotation) . ' Quotation found',
                    "data" => $allQuotation,
                    "count" => $countQuotation
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Quotation not found',
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

    
    public function creatQuotation()
    {
        $quotationModel = new QuotationModel();
        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- START ------------>

        $quotationFile = $this->request->getFile('quo_pdf');

        if ($_FILES['quo_pdf']) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'No file uploaded.',
                'data' => $_FILES['quo_pdf'],
            ]);
        }
        


        if (isset($quotationFile) && !empty($quotationFile)) {

            $uploadPath = FCPATH . 'public/assets/img/uploads/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, TRUE);
            }

            if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Upload directory is not writable',
                ]);
            }
        }
        // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE PDF ------------ START ---------------> 

        $quotationFile->move($uploadPath);

        if ($quotationFile->hasMoved()) {
            $fileName = $quotationFile->getName();
            // $fileName = 'https://OUR DOMAIN/public/assets/img/uploads/customerImage/' . $imageFile->getName();
            // $imageFile = $fileName;
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to upload Profile Image.'
            ]);
        }

        // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE PDF ------------ END ---------------> 
        $quo_name = $this->request->getVar("quo_name") ?? "";
        $quo_date = $this->request->getVar("quo_date") ?? "";
        $quo_subject = $this->request->getVar("quo_subject") ?? "";
        $quo_number = $this->request->getVar("quo_number") ?? "";
        $quo_description = $this->request->getVar("quo_description") ?? "[]";
        $quo_quantity = $this->request->getVar("quo_quantity") ?? "";
        $quo_kg = $this->request->getVar("quo_kg") ?? "";
        $quo_subtotal = $this->request->getVar("quo_subtotal") ?? "";
        $quo_discount = $this->request->getVar("quo_discount") ?? "";
        $quo_total = $this->request->getVar("quo_total") ?? "";
        $quo_pdf = $fileName ?? "";

        $randomNumber = rand(0,1000);

        $quotationSlug = url_title($quo_name . ' ' . $randomNumber, '-', true);

        $data = [
            'quo_name' => $quo_name,
            'quo_date' => $quo_date,
            'quo_subject' => $quo_subject,
            'quo_number' => $quo_number,
            'quo_description' => $quo_description,
            'quo_quantity' => $quo_quantity,
            'quo_kg' => $quo_kg,
            'quo_subtotal' => $quo_subtotal,
            'quo_discount' => $quo_discount,
            'quo_total' => $quo_total,
            'quo_slug' => $quotationSlug,
            'quo_pdf' => $fileName,
        ];

        try {
            $quotationAdd = $quotationModel->insert($data);
            if ($quotationAdd) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Quotation uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Quotation not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => false,
                "message" => $th->getMessage()
            ]);
        }
    }


}
