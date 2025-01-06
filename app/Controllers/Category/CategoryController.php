<?php

namespace App\Controllers\Category;

use App\Models\Product\CategoryModel;
use App\Models\Product\ProductModel;
use CodeIgniter\RESTful\ResourceController;

class CategoryController extends ResourceController
{

    public function fetchCategory()
    {
        $categoryModel = new CategoryModel();

        try {

            // $page = $this->request->getGet("page") ?? 1;
            // $page = ($page * 20) - 20;

            // $search = $this->request->getGet("search") ?? "";

            // $allCategory = $categoryModel->findAll();
            $allCategory = $categoryModel->orderBy("cat_id", "DESC")->findAll();
            $countCategory = $categoryModel->countAllResults();


            if ($allCategory) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allCategory) . ' Category found',
                    "data" => $allCategory,
                    "count" => $countCategory
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Category not found',
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

    public function singleCategory()
    {

        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        $id = $this->request->getGet('id');
        try {

            // -------------------- EMPLOYEES NAME LIST --------------------->


            $categoryDetail = $categoryModel->where('cat_id', $id)->first();
            $allCategory = $categoryModel->findAll();
            
            $productArr = $productModel->where('p_category', $id)->findAll();


            return $this->response->setJSON([
                "status" => "success",
                "message" => 'Category and their product Details Found',
                "categoryList" => $allCategory ?? null,
                "categoryDetail" => $categoryDetail ?? null,
                "productArr" => $productArr ?? [],
            ]);

        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage() . " " . $th->getLine(),
                'data' => null
            ]);
        }
    }
    public function creatCategory()
    {
        $categoryModel = new CategoryModel();

        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- START ------------>

        $imageFile = $this->request->getFile('cat_image');

        if (!$imageFile) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Please Upload Image"
            ]);
        }


        if (isset($imageFile) && !empty($imageFile)) {

            $uploadPath = FCPATH . 'public/assets/img/uploads/categoryImage/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, TRUE);
            }

            if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Upload directory is not writable',
                ]);
            }
            // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ START ---------------> 

            $imageFile->move($uploadPath);

            if ($imageFile->hasMoved()) {
                $fileName = $imageFile->getName();
                // $fileName = 'https://OUR DOMAIN/public/assets/img/uploads/customerImage/' . $imageFile->getName();
                // $imageFile = $fileName;
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Failed to upload Profile Image.'
                ]);
            }

            // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ END ---------------> 

        }

        // ------------------------------------ PROFILE IMAGE UPLOAD CODE -------------------- END ------------>


        $data = [
            "cat_name" => $this->request->getVar("cat_name") ?? "",
            "cat_code" => $this->request->getVar("cat_code") ?? "",
            "cat_image" => $fileName ?? "",
        ];

        try {
            $customerAdd = $categoryModel->insert($data);
            if ($customerAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Customer uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Customer not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function fetchCategoryList()
        {
            $categoryModel = new CategoryModel();
    
            try {
                $categoArr = [];
                $allCategory = $categoryModel->findAll();
    
                foreach ($allCategory as $customer) {
                    $obj = [];
                    $obj['cat_name'] = $customer['cat_name'];
                    $obj['cat_id'] = $customer['cat_id'];
                    $categoArr[] = $obj;
                }
    
                if ($allCategory) {
                    return $this->response->setJSON([
                        "status" => "success",
                        "message" => count($allCategory) . ' Category found',
                        "data" => $categoArr,
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'data' => null,
                        'message' => 'Customer not found',
                    ]);
                }
            } catch (\Throwable $th) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'a' => $th->getMessage(),
                    'message' => $th->getTrace(),
                    'data' => null
                ]);
            }
        }
}
