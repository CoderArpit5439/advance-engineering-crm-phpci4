<?php

namespace App\Controllers\Graphic;

use App\Models\crm\GraphicModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');

class GraphicController extends ResourceController
{
   public function addGraphic()
   {
      $isToken = check_jwt_authentication();
      if (!$isToken) {
         return $this->response->setJSON([
            "message" => "Authentication failed",
            "status" => "error"
         ]);
      }
      $imageFile = $this->request->getFile('graphic_image');


      if (isset($imageFile) && !empty($imageFile)) {

         $uploadPath = FCPATH . 'public/assets/img/uploads/graphicImage/';

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

            $fileName = 'https://api.advanceengineerings.com/public/assets/img/uploads/graphicImage/' . $imageFile->getName();
         } else {
            return $this->response->setJSON([
               'status' => false,
               'message' => 'Failed to upload Profile Image.'
            ]);
         }

         // --------------------------- THIS CODE FOR WHEN UPLOAD SINGLE IMAGE ------------ END ---------------> 

      } else {
         return $this->response->setJSON([
            "status" => "error",
            "message" => "Please Upload Image"
         ]);
      }

      $data = [
         "g_name" => $isToken->user_name ?? "",
         "g_title" => $this->request->getVar("graphic_title") ?? "",
         "g_approved" =>"no",
         "g_image" => $fileName ?? "",
      ];
      try {
         $graphicModel = new GraphicModel();

         $graphicImageData = $graphicModel->insert($data);
         if ($graphicImageData) {
            return $this->response->setJSON([
               "status" => "success",
               "message" => "Graphic Image uploaded successfully",
               "data" => $data
            ]);
         } else {
            return $this->response->setJSON([
               "status" => "error",
               "message" => "Garphic Image not uploaded"
            ]);
         }
      } catch (\Throwable $th) {
         return $this->response->setJSON([
            "status" => "error",
            "message" => $th->getMessage()
         ]);
      }
   }

   // public function fetchGraphic()
   // {
   //     $graphicModel = new GraphicModel();

   //     try {

   //         // $page = $this->request->getGet("page") ?? 1;
   //         // $page = ($page * 20) - 20;

   //         // $search = $this->request->getGet("search") ?? "";

   //         // $allCategory = $graphicModel->findAll();
   //         $allCategory = $graphicModel->orderBy("g_id", "DESC")->findAll();
   //         $countGraphic = $graphicModel->countAllResults();


   //         if ($allCategory) {
   //             return $this->response->setJSON([
   //                 "status" => "success",
   //                 "message" => count($allCategory) . ' Graphic Images found',
   //                 "data" => $allCategory,
   //                 "count" => $countGraphic
   //             ]);
   //         } else {
   //             return $this->response->setJSON([
   //                 'status' => 'error',
   //                 'data' => null,
   //                 'message' => 'Graphic Images not found',
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
   public function fetchGraphic()
{
    $graphicModel = new GraphicModel();

    try {
        // Get the page and search parameters with defaults
        $page = (int) ($this->request->getGet("page") ?? 1); // Default to page 1
        $page = ($page > 0) ? $page : 1; // Ensure page is a positive number

        $search = $this->request->getGet("search") ?? ""; // Default to an empty string

        // Calculate offset for pagination
        $limit = 20; // Items per page
        $offset = ($page - 1) * $limit;

        // Fetch graphics based on pagination and search
        if ($search) {
            $allCategory = $graphicModel
                ->like("title", $search)
                ->orderBy("g_id", "DESC")
                ->findAll($limit, $offset);
        } else {
            $allCategory = $graphicModel
                ->orderBy("g_id", "DESC")
                ->findAll($limit, $offset);
        }

        $countGraphic = $graphicModel->countAllResults();

        if ($allCategory) {
            return $this->response->setJSON([
                "status" => "success",
                "message" => count($allCategory) . ' Graphic Images found',
                "data" => $allCategory,
                "count" => $countGraphic,
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'data' => null,
                'message' => 'Graphic Images not found',
            ]);
        }
    } catch (\Throwable $th) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $th->getMessage(), // Send the actual error message
            'data' => null
        ]);
    }
}

}
