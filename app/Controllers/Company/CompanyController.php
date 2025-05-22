<?php
namespace App\Controllers\Company;

use App\Models\crm\CompanyModel;
use App\Models\crm\InquiryModel;
use App\Models\crm\InvoiceModel;
use App\Models\crm\OrderModel;
use App\Models\Crm\PlantModel;
use App\Models\crm\QuotationModel;
use App\Models\Crm\UnitModel;
use App\Models\Customer\CustomerModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
class CompanyController extends ResourceController
{
    public function AddCompany()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error",
            ]);
        }

        $imageFile = $this->request->getFile('c_image');

        if (isset($imageFile) && !empty($imageFile)) {
            $uploadPath = FCPATH . 'public/assets/img/uploads/CompanyImage/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Upload directory is not writable',
                ]);
            }

            $imageFile->move($uploadPath);

            if ($imageFile->hasMoved()) {
                $fileName = base_url() . '/public/assets/img/uploads/CompanyImage/' . $imageFile->getName();
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to upload Profile Image.',
                ]);
            }
        } else {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Please Upload Image",
            ]);
        }

        $data = [

            "c_name" => $this->request->getVar("c_name") ?? "",
            "c_image" => $fileName ?? "",
            "c_website" => $this->request->getVar("c_website") ?? "",
            "c_head_office_address" => $this->request->getVar("c_head_office_address") ?? "",
            "c_head_office_contact" => $this->request->getVar("c_head_office_contact") ?? "",
            "total_country_plant" => $this->request->getVar("total_country_plant") ?? "",
            "total_india_plant" => $this->request->getVar("total_india_plant") ?? "",
            "c_type_of_manufacturing" => $this->request->getVar("c_type_of_manufacturing") ?? "",
            "c_bank_name" => $this->request->getVar("c_bank_name") ?? "",
            "c_bank_account_no" => $this->request->getVar("c_bank_account_no") ?? "",
            "c_bank_branch" => $this->request->getVar("c_bank_branch") ?? "",
            "c_bank_ifsc" => $this->request->getVar("c_bank_ifsc") ?? "",
            "c_international_domestic" => $this->request->getVar("c_international_domestic") ?? "",

        ];

        try {
            $ComapanyModal = new CompanyModel();
            $addCompany = $ComapanyModal->insert($data);

            if ($addCompany) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Company added successfully",
                    "data" => $data,
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "company not uploaded",
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage(),
            ]);
        }
    }

    public function GetCompany()
    {
        try {

            $companyModel = new CompanyModel();
            $allCompany = $companyModel->orderBy("c_id", "DESC")->findAll();
            $countcompany = $companyModel->countAllResults();

            $plantModel = new PlantModel();
            $allPlant = $plantModel->orderBy("p_id", "DESC")->findAll();
            $countPlant = $plantModel->countAllResults();

            $unitModel = new UnitModel();
            $allUnit = $unitModel->orderBy("u_id", "DESC")->findAll();
            $countUnit = $unitModel->countAllResults();
            
            // TO GET THE CUSTOMERS HERE
            $customerModel = new CustomerModel();
            $allCustomer = $customerModel->orderBy("c_id", "DESC")->findAll();
            $countCustomer = $customerModel->countAllResults();
        

            $cards = [
                "totalPlant" => $countPlant,
                "totalSell" => 0,
                "totalCompany" => $countcompany,
                "totalUnit" => $countUnit,
                "totalCustomer" => $countCustomer,
            ];

            if ($allCompany) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allCompany) . ' company found',
                    "data" => $allCompany,
                    "plantData" => $allPlant,
                    "count" => $countcompany,
                    "plantCount" => $countPlant,
                    "cards" => $cards,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'company not found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getTrace(),
                'data' => null,
            ]);
        }
    }

    public function fetchSingleCompany()
    {

        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error",
            ]);
        }

        try {
            $company_id = $this->request->getVar('id');
            $companyModel = new CompanyModel();
            $plantModel = new PlantModel();
            $unitModel = new UnitModel();
            $customerModel = new CustomerModel();
            $quotationModel = new QuotationModel();
            $orderModel = new OrderModel();
            $inquiryModel = new InquiryModel();
            $invoiceModel = new InvoiceModel();
            $singleCompany = $companyModel->where('c_id', $company_id)->first();

            $plantList = $plantModel->where('c_id', $company_id)->findAll();
            $unitList = [];
            $customerList = [];
            $quotationNewList = [];
            $invoiceNewList = [];
            $orderNewList = [];
            $inquiryNewList = [];

            foreach ($plantList as $plant) {
                $unitDetails = $unitModel->where('u_plant_id', $plant['p_id'])->findAll();
                if ($unitDetails) {
                    foreach ($unitDetails as $unit) {
                        $unit['plant_name'] = $plant['p_city'];
                        $unitList[] = $unit;
                        $customerDetails = $customerModel->where('c_unit_id', $unit['u_id'])->findAll();
                        foreach ($customerDetails as $customer) {
                            $customerList[] = $customer;

                            $quotationList = $quotationModel
                                ->orderBy('quo_id', 'DESC')
                                ->where('quo_customer_id', $customer['c_id'])
                                ->findAll();

                            if ($quotationList) {
                                foreach ($quotationList as $quotation) {
                                    $quotationNewList[] = $quotation;
                                }
                            }

                            $orderList = $orderModel
                                ->orderBy('or_id', 'DESC')
                                ->where('or_customer_id', $customer['c_id'])
                                ->findAll();

                            if ($orderList) {
                                foreach ($orderList as $order) {
                                    $orderNewList[] = $order;
                                }
                            }

                            $inquiryList = $inquiryModel
                                ->orderBy('inq_id', 'DESC')
                                ->where('inq_customer_id', $customer['c_id'])
                                ->findAll();

                            if ($inquiryList) {
                                foreach ($inquiryList as $inquiry) {
                                    $inquiryNewList[] = $inquiry;
                                }
                            }

                            $invoiceList = $invoiceModel
                                ->orderBy('inv_id', 'DESC')
                                ->where('inv_customer_id', $customer['c_id'])
                                ->findAll();

                            if ($invoiceList) {
                                foreach ($invoiceList as $invoice) {
                                    $invoiceNewList[] = $invoice;
                                }
                            }



                        }
                    }
                }
            }

            $cards = [
                "Sell" => 0,
                "Unit" => count($unitList),
                "Quotation" => count($quotationNewList),
                "Customer" => count($customerList),
                "Plant" => count($plantList),
                "Order" => count($orderNewList),
                "Inquiry" => count($inquiryNewList),
            ];

            // Convert associative array to array of objects
            $cardObjects = [];

            foreach ($cards as $name => $count) {
                $cardObjects[] = (object) [
                    'name' => $name,
                    'count' => (string) $count // Convert to string as per your requirement
                ];
            }

            // $all_quotation_of_company =

            if ($singleCompany) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => 'company found',
                    "data" => $singleCompany,
                    "plantList" => $plantList,
                    "unitList" => $unitList,
                    "customerList" => $customerList,
                    "quotationList" => $quotationNewList,
                    "inquiryList" => $inquiryNewList,
                    "invoiceList" => $invoiceNewList,
                    "orderList" => $orderNewList,
                    "cards" => $cardObjects,

                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'company not found',
                ]);
            }

        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getTrace(),
                'data' => null,
            ]);
        }

    }

    public function UpdateCompany()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error",
            ]);
        }
        $id = $this->request->getVar('c_id');
        if (!$id) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Company ID is required",
            ]);
        }

        $companyModel = new CompanyModel();
        $existingCompany = $companyModel->find($id);

        if (!$existingCompany) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Company not found",
            ]);
        }

        $imageFile = $this->request->getFile('c_image');
        $fileName = $existingCompany['c_image'];

        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $uploadPath = FCPATH . 'public/assets/img/uploads/CompanyImage/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (!is_writable($uploadPath)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Upload directory is not writable',
                ]);
            }

            $imageFile->move($uploadPath);

            if ($imageFile->hasMoved()) {
                $fileName = 'https://api.advanceengineerings.com/public/assets/img/uploads/CompanyImage/' . $imageFile->getName();
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to upload image.',
                ]);
            }
        }

        $data = [
            "c_name" => $this->request->getVar("c_name") ?? $existingCompany['c_name'],
            "c_image" => $fileName,
            "c_website" => $this->request->getVar("c_website") ?? $existingCompany['c_website'],
            "c_head_office_address" => $this->request->getVar("c_head_office_address") ?? $existingCompany['c_head_office_address'],
            "c_head_office_contact" => $this->request->getVar("c_head_office_contact") ?? $existingCompany['c_head_office_contact'],
            "total_country_plant" => $this->request->getVar("total_country_plant") ?? $existingCompany['total_country_plant'],
            "total_india_plant" => $this->request->getVar("total_india_plant") ?? $existingCompany['total_india_plant'],
            "c_type_of_manufacturing" => $this->request->getVar("c_type_of_manufacturing") ?? $existingCompany['c_type_of_manufacturing'],
            "c_bank_name" => $this->request->getVar("c_bank_name") ?? $existingCompany['c_bank_name'],
            "c_bank_account_no" => $this->request->getVar("c_bank_account_no") ?? $existingCompany['c_bank_account_no'],
            "c_bank_branch" => $this->request->getVar("c_bank_branch") ?? $existingCompany['c_bank_branch'],
            "c_bank_ifsc" => $this->request->getVar("c_bank_ifsc") ?? $existingCompany['c_bank_ifsc'],
            "c_international_domestic" => $this->request->getVar("c_international_domestic") ?? $existingCompany['c_international_domestic'],
        ];

        try {
            $updated = $companyModel->update($id, $data);

            if ($updated) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Company updated successfully",
                    "data" => $data,
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "No changes were made or update failed",
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage(),
            ]);
        }
    }
}
