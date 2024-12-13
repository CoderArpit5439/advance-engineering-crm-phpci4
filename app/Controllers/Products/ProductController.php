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


            $newProductArray = [];
            foreach ($allProduct as $product) {

                if ($product["p_media"]) {
                    $decodedProductImage = json_decode($product['p_media'], true);
                    $product['p_image'] = "https://api.advanceengineerings.com/" . $decodedProductImage[0];
                    $newProductArray[] = $product;
                }
            }

            if ($allProduct) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => count($newProductArray) . ' Product found',
                    "data" => $newProductArray,
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

    public function singleProduct()
    {

        $ProductModel = new ProductModel();

        $id = $this->request->getGet('id');
        try {

            // -------------------- EMPLOYEES NAME LIST --------------------->


            $productDetail = $ProductModel->where('p_id', $id)->first();


            if ($productDetail) {

                // if($productDetail['p_media']){

                //  $decodeMedia = json_decode($productDetail['p_media']);
                //   foreach($decodeMedia as $media){
                //     $productDetail['p_media'][] = "https://api.advanceengineerings.com/" .  $media;
                //   }
                // }

                if ($productDetail["p_media"]) {
                    $decodedProductImage = json_decode($productDetail['p_media'], true);
                    foreach ($decodedProductImage as $media) {
                        $productDetail['p_medias'][] = "https://api.advanceengineerings.com/" . $media;
                    }
                }
                $relatedProducts = $ProductModel->where('p_category', $productDetail['p_category'])->findAll(5, 0);
                if (!$relatedProducts) {
                    $relatedProducts = $ProductModel->findAll(5, 0);
                }

                $newRelatedProductArray = [];
                foreach ($relatedProducts as $product) {

                    if ($product["p_media"]) {
                        $decodedProductImage = json_decode($product['p_media'], true);
                        $product['p_image'] = "https://api.advanceengineerings.com/" . $decodedProductImage[0];
                        $newRelatedProductArray[] = $product;
                    }
                }

                return $this->response->setJSON([
                    "status" => true,
                    "message" => 'Product Details Found',
                    "data" => $productDetail,
                    "relatedProduct" => $newRelatedProductArray ?? [],
                ]);

            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => 'Product not found in database',
                    "data" => [],
                    "relatedProduct" => [],
                ]);

            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $th->getMessage() . " " . $th->getLine(),
                'data' => null
            ]);
        }
    }

    public function creatProduct()
    {
        $ProductModel = new ProductModel();
        // return $this->response->setJSON([
        //     "status" => true,
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
                    "status" => true,
                    "message" => "Product uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Product not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function updateProduct()
    {

        $ProductModel = new ProductModel();
        $productId = $this->request->getGet("id");

        $p_category = $this->request->getVar("p_category") ?? "";
        $p_name = $this->request->getVar("p_name") ?? "";

        $productSlug = url_title($p_name . ' ' . $p_category, '-', true);

        $data = [
            "p_category" => $p_category ?? "",
            "p_unique_id" => $this->request->getVar("p_unique_id") ?? "",
            "p_name" => $p_name ?? "",
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
        ];

        try {
            $productUpdate = $ProductModel->update($productId, $data);
            if ($productUpdate) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Product Update successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Product not Updated"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function fetchHome()
    {
        $ProductModel = new ProductModel();
        $categoryModel = new CategoryModel();

        try {
            
            $allProduct = $ProductModel->orderBy("p_id", "DESC")->findAll(20, 0);
            $countProduct = $ProductModel->countAllResults();

            $allCategory = $categoryModel->orderBy("cat_id", "DESC")->findAll(20, 0);
            $countCategory = $categoryModel->countAllResults();

            $newProductArray = [];
            foreach ($allProduct as $product) {

                if ($product["p_media"]) {
                    $decodedProductImage = json_decode($product['p_media'], true);
                    $product['p_image'] = "https://api.advanceengineerings.com/" . $decodedProductImage[0];
                    $newProductArray[] = $product;
                }
            }

            $extraFeatureOne = [];
            $extraFeatureTwo = [];
            $extraFeatureThree = [];
            $dealZone = [];

            foreach ($allProduct as $product) {
                if ($product["p_media"]) {
                    $decodedProductImage = json_decode($product['p_media'], true);
                    $product['p_image'] = "https://api.advanceengineerings.com/" . $decodedProductImage[0];
                }
                if (count($dealZone) < 5) {
                    $dealZone[] = $product;
                }
            }

            foreach ($allCategory as $category) {
                if (count($extraFeatureOne) < 7) {
                    $newArr = [];
                    $newArr["cat_name"] = $category['cat_name'] ;
                    $newArr["cat_id"] = $category['cat_id'] ?? false;
                    $extraFeatureOne[] = $newArr;
                } else if (count($extraFeatureTwo) < 7) {
                    $newArr = [];
                    $newArr["cat_name"] = $category['cat_name'];
                    $newArr["cat_id"] = $category['cat_id'] ?? false;
                    $extraFeatureTwo[] = $newArr;
                } else if (count($extraFeatureThree) < 7) {
                    $newArr = [];
                    $newArr["cat_name"] = $category['cat_name'];
                    $newArr["cat_id"] = $category['cat_id'] ?? false;
                    $extraFeatureThree[] = $newArr;
                }

            }

            if ($allProduct) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => 'Home page details fetched successfully',
                    "productArray" => $newProductArray,
                    "productCount" => $countProduct,
                    "categoryArray" => $allCategory,
                    "categoryCount" => $countCategory,
                    "extraFeatureOne" => $extraFeatureOne,
                    "extraFeatureTwo" => $extraFeatureTwo,
                    "extraFeatureThree" => $extraFeatureThree,
                    "dealZone" => $dealZone,
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
