<?php

namespace App\Controllers\Products;

use App\Models\Product\CategoryModel;
use App\Models\Product\ProductModel;
use CodeIgniter\RESTful\ResourceController;

class ProductController extends ResourceController
{

    public function fetchProduct()
    {
        $ProductModel = new ProductModel();

        try {

            $page = $this->request->getGet("page") ?? 1;
            $page = ($page * 20) - 20;

            $search = $this->request->getGet("search") ?? "";

            // $allProduct = $ProductModel->findAll();
            $allProduct = $ProductModel->orderBy("p_id", "DESC")->like("p_name", $search)->orLike("p_unique_id", $search)->findAll(20, $page);
            $countProduct = $ProductModel->like("p_name", $search)->orLike("p_unique_id", $search)->countAllResults();


            if ($allProduct) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allProduct) . ' Product found',
                    "data" => $allProduct,
                    "count" => $countProduct
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Product not found',
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

    // public function fetchCustomerNameList()
    // {
    //     $ProductModel = new ProductModel();

    //     try {
    //         $customerArr = [];
    //         $allProduct = $ProductModel->findAll();

    //         foreach ($allProduct as $customer) {
    //             $obj = [];
    //             $obj['p_name'] = $customer['p_name'];
    //             $obj['p_id'] = $customer['p_id'];
    //             $customerArr[] = $obj;
    //         }

    //         if ($allProduct) {
    //             return $this->response->setJSON([
    //                 "status" => "success",
    //                 "message" => count($allProduct) . ' Customer found',
    //                 "data" => $customerArr,
    //             ]);
    //         } else {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'data' => null,
    //                 'message' => 'Customer not found',
    //             ]);
    //         }
    //     } catch (\Throwable $th) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => $th->getTrace(),
    //             'data' => null
    //         ]);
    //     }
    // }

    public function creatProduct()
    {
        $ProductModel = new ProductModel();
        // return $this->response->setJSON([
        //     "status" => "success",
        //     "message" => "Product uploaded successfully",
           
        // ]);
   // --------------------------- THIS CODE FOR WHEN UPLOAD MULTIPLE IMAGE ------------- START --------------> 
   $uploadPath = FCPATH . 'public/assets/img/product/';
   $uploadedFiles = [];
   if ($this->request->getVar('p_media') && is_string($this->request->getVar('p_media'))) {
       $productPic = $this->request->getVar('p_media');
   } else if ($this->request->getFileMultiple('p_media')) {
       foreach ($this->request->getFileMultiple('p_media') as $imageFile) {
           if ($imageFile->isValid() && !$imageFile->hasMoved()) {
               $newName = $imageFile->getRandomName();
               $imageFile->move($uploadPath, $newName);
            //    $uploadedFiles[] = 'https://domain url/public/assets/img/uploads/documents/' . $newName;
               $uploadedFiles[] = 'public/assets/img/product/' . $newName;
           } else {
               return $this->response->setJSON([
                   'status' => false,
                   'message' => 'Failed to upload one or more images.'
               ]);
           }
       }
       // If all files are uploaded successfully
       $productPic = json_encode($uploadedFiles);
   }


   // --------------------------- THIS CODE FOR WHEN UPLOAD MULTIPLE IMAGE ------------- END --------------> 
   $p_category = $this->request->getVar("p_category") ?? "";
   $p_name = $this->request->getVar("p_name") ?? "";

   $productSlug = url_title($p_name . ' ' . $p_category, '-', true);

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
            "p_media" => $productPic,
        ];

        try {
            $productAdd = $ProductModel->insert($data);
            if ($productAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Product uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Product not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function updateCustomer()
    {
        // $isToken = check_jwt_authentication();

        // if (!$isToken) {
        //     return $this->response->setJSON([
        //         "status" => "error",
        //         "message" => "Authentication failed"
        //     ]);
        // }

        $ProductModel = new ProductModel();

        $c_password = $this->request->getVar("c_password") ?? "Advance123";

        $data = [
            "p_name" => $this->request->getVar("p_name") ?? "",
            "c_password" => hash('sha256', $c_password),
            "p_unique_id" => $this->request->getVar("p_unique_id") ?? "",
            "c_email" => $this->request->getVar("c_email") ?? "",
            "c_mobile" => $this->request->getVar("c_mobile") ?? "",
            "c_post" => $this->request->getVar("c_post") ?? "",
            "c_department" => $this->request->getVar("c_department") ?? "",
            "c_status" => $this->request->getVar("c_status") ?? "",
            "c_gender" => $this->request->getVar("c_gender") ?? "",
            "c_description" => $this->request->getVar("c_description") ?? "",
            "c_dob" => $this->request->getVar("c_dob") ?? "",
            "c_address" => $this->request->getVar("c_address") ?? "",
            "c_rank" => $this->request->getVar("c_rank") ?? "",
            "c_no_of_quotation" => $this->request->getVar("c_no_of_quotation") ?? "",
        ];

        $customerId = $this->request->getGet("id");

        try {

            $ProductModel->update($customerId, $data);
            $updateCustomer = $ProductModel->where("p_id", $customerId)->first();
            if ($updateCustomer) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Customer updated",
                    "data" => $updateCustomer
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Customer not update"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage() . " " . $th->getLine()
            ]);
        }
    }
     
    public function fetchHome()
    {
        $ProductModel = new ProductModel();
        $categoryModel = new CategoryModel();

        try {

            $allProduct = $ProductModel->orderBy("p_id", "DESC")->findAll(20,0);
            $countProduct = $ProductModel->countAllResults();

            $allCategory = $categoryModel->orderBy("cat_id", "DESC")->findAll(20,0);
            $countCategory = $categoryModel->countAllResults();


            if ($allProduct) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => 'Home page details fetched successfully',
                    "productArray" => $allProduct,
                    "productCount" => $countProduct,
                    "categoryArray" => $allCategory,
                    "categoryCount" => $countCategory,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Home page details not found',
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

}
