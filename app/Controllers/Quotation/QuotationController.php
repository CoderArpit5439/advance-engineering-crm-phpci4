<?php

namespace App\Controllers\Quotation;

use App\Models\crm\QuotationProductionModel;
use App\Models\crm\QuotationModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
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

    public function getSingleQuotation()
    {
        $qpModel = new QuotationProductionModel();
        $qpId = $this->request->getVar("qp_id");

        if (!$qpId) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Quotation ID is required.',
                'data' => []
            ]);
        }

        try {
            $productDetail = $qpModel->where('quotation_id', $qpId)->findAll();

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


    public function creatQuotation()
    {
        $quotationModel = new QuotationModel();
        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- START ------------>

        $quotationFile = $this->request->getFile('quo_pdf');

        // if ($_FILES['quo_pdf']) {
        //     return $this->response->setJSON([
        //         'status' => false,
        //         'message' => 'No file uploaded.',
        //         'data' => $_FILES['quo_pdf'],
        //     ]);
        // }



        // if (isset($quotationFile) && !empty($quotationFile)) {

        //     $uploadPath = FCPATH . 'public/assets/img/uploads/';

        //     if (!is_dir($uploadPath)) {
        //         mkdir($uploadPath, 0777, TRUE);
        //     }

        //     if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
        //         return $this->response->setJSON([
        //             'status' => false,
        //             'message' => 'Upload directory is not writable',
        //         ]);
        //     }
        // }
        // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE PDF ------------ START ---------------> 

        // $quotationFile->move($uploadPath);

        // if ($quotationFile->hasMoved()) {
        //     $fileName = $quotationFile->getName();
        //     // $fileName = 'https://OUR DOMAIN/public/assets/img/uploads/customerImage/' . $imageFile->getName();
        //     // $imageFile = $fileName;
        // } else {
        //     return $this->response->setJSON([
        //         'status' => false,
        //         'message' => 'Failed to upload Profile Image.'
        //     ]);
        // }

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

        $randomNumber = rand(0, 1000);

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

        ];

        try {
            $quotationAdd = $quotationModel->insert($data);

            if ($quotationAdd) {

                $qp_id = $this->request->getVar("qp_id");
                if (!$qp_id) {
                    return $this->response->setJSON([
                        "status" => "error",
                        "message" => "Id is required"
                    ]);
                }
                $quotationProduction = new QuotationProductionModel();
                $existingRecord = $quotationProduction->find($qp_id);
                if (!$existingRecord) {
                    return $this->response->setJSON([
                        "status" => "error",
                        "message" => "This product not found"
                    ]);
                }

                try {

                    $insertedId = $quotationModel->getInsertID();
                    $newId['id'] = $insertedId;


                    $data = [
                        "q_id" => $newId ?? "",
                        "inq_id" => $this->request->getVar("inq_id") ?? "",
                        "p_category" => $p_category ?? "",
                        "p_unique_id" => $this->request->getVar("p_unique_id") ?? "",
                        "p_name" => $this->request->getVar("p_name") ?? "",
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

                    $updateSuccess = $quotationProduction->update($qp_id, $data);

                    if ($updateSuccess) {

                        return $this->response->setJSON([
                            "status" => true,
                            "message" => "Quotation  uploaded",
                            "data" => $data
                        ]);
                    } else {
                        return $this->response->setJSON([
                            "status" => "error",
                            "message" => "somthing went wrong"
                        ]);
                    }
                } catch (\Throwable $th) {
                    return $this->response->setJSON([
                        "status" => "error",
                        "message" => $th->getMessage()
                    ]);
                }





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

    // public function deleteQuotation()
    // {
    //     $QuotationModel = new QuotationModel();
    //     $quo_id = $this->request->getPost("quo_id");

    //     try {

    //         $quotation = $QuotationModel->find($quo_id);

    //         if ($quotation) {

    //             $deleted = $QuotationModel->delete($quo_id);

    //             if ($deleted) {
    //                 return $this->response->setJSON([
    //                     'status' => true,
    //                     'message' => 'Quotation deleted successfully.',
    //                     'data' => null
    //                 ]);
    //             } else {
    //                 return $this->response->setJSON([
    //                     'status' => 'error',
    //                     'message' => 'Failed to delete quotation.',
    //                     'data' => null
    //                 ]);
    //             }
    //         } else {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'Quotation not found.',
    //                 'data' => null
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => $th->getMessage(),
    //             'data' => null
    //         ]);
    //     }
    // }

    public function deleteQuotation()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        // Get lead ID from request
        $quo_id = $this->request->getVar("quo_id");

        if (!$quo_id) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => " ID is required"
            ]);
        }

        try {
            $leadModel = new QuotationModel();
            $existingquation = $leadModel->find($quo_id);

            if (!$existingquation) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "not found"
                ]);
            }

            // Delete lead record
            $deletequotation = $leadModel->delete($quo_id);

            if ($deletequotation) {



                return $this->response->setJSON([
                    "status" => "success",
                    "message" => " removed successfully",

                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to remove "
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
