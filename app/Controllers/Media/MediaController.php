<?php

namespace App\Controllers\MediaController;

use App\Models\Crm\MediaModel;
use CodeIgniter\RESTful\ResourceController;

class MediaController extends ResourceController
{
    // Add Media 
    public function addMedia()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        $imageFile = $this->request->getFile('media_image');

        if (isset($imageFile) && !empty($imageFile)) {
            $uploadPath = FCPATH . 'public/assets/img/uploads/mediaImage/';

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
                $fileName = 'https://api.advanceengineerings.com/public/assets/img/uploads/mediaImage/' . $imageFile->getName();
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
            "m_product_id" => $this->request->getVar("media_product_id") ?? "",
            "m_image" => $fileName ?? "",
        ];

        try {
            $mediaModal = new MediaModel();
            $addMedia = $mediaModal->insert($data);

            if ($addMedia) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Media uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Media not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    //Get Media 
    public function getMedia()
    {
        try {
            $isToken = check_jwt_authentication();
            if (!$isToken) {
                return $this->response->setJSON([
                    "message" => "Authentication failed",
                    "status" => "error"
                ]);
            }


            $page = (int) $this->request->getVar('page') ?? 1;

            if ($page <= 0) {
                return $this->failValidationError('Page number must be greater than 0');
            }

            $perPage = 10;


            $offset = ($page - 1) * $perPage;

            $mediaModel = new MediaModel();
            $mediaData = $mediaModel->orderBy('m_id', 'DESC')
                ->findAll($perPage, $offset);


            if (empty($mediaData)) {
                return $this->failNotFound('No media records found for this page');
            }

            $totalMedia = $mediaModel->countAllResults();

            $totalPages = ceil($totalMedia / $perPage);


            return $this->respond([
                'status'    => 'success',
                'message'   => 'Media fetched successfully',
                'data'      => $mediaData,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages'  => $totalPages,
                    'total_records' => $totalMedia,
                    'per_page'     => $perPage
                ]
            ]);
        } catch (\Exception $e) {

            return $this->failServerError('An error occurred while fetching media: ' . $e->getMessage());
        }
    }

    // Remove Media 
    public function RemoveMedia() {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    
        // Get employee ID from request
        $mediaId = $this->request->getVar("media_id");
    
        if (!$mediaId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Media ID is required"
            ]);
        }
    
        try {
            $mediaModel = new MediaModel();
            $existingMedia = $mediaModel->find($mediaId);
    
            if (!$existingMedia) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Media not found"
                ]);
            }
    
            // Delete employee record
            $deleteMedia = $mediaModel->delete($mediaId);
    
            if ($deleteMedia) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "media removed successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to remove media"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    // Update Media 

    // Update Media 
public function updateMedia()
{
    // Check if the token is valid
    $isToken = check_jwt_authentication();
    if (!$isToken) {
        return $this->response->setJSON([
            "message" => "Authentication failed",
            "status" => "error"
        ]);
    }

    // Get media ID from request
    $mediaId = $this->request->getVar("media_id");
    if (!$mediaId) {
        return $this->response->setJSON([
            "status" => "error",
            "message" => "Media ID is required"
        ]);
    }

    // Get the new data for media
    $newProductId = $this->request->getVar("media_product_id");
    $newImageFile = $this->request->getFile("media_image");

    // If a new image is uploaded, handle the image upload
    $fileName = null;
    if ($newImageFile && $newImageFile->isValid() && !$newImageFile->hasMoved()) {
        $uploadPath = FCPATH . 'public/assets/img/uploads/mediaImage/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Check if the directory is writable
        if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Upload directory is not writable'
            ]);
        }

        // Move the uploaded file
        $newImageFile->move($uploadPath);

        if ($newImageFile->hasMoved()) {
            $fileName = 'https://api.advanceengineerings.com/public/assets/img/uploads/mediaImage/' . $newImageFile->getName();
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to upload media image'
            ]);
        }
    }

    // Prepare the data to update
    $data = [];
    if ($newProductId) {
        $data["m_product_id"] = $newProductId;
    }
    if ($fileName) {
        $data["m_image"] = $fileName;
    }

    try {
        // Instantiate the media model
        $mediaModel = new MediaModel();

        // Check if the media exists in the database
        $existingMedia = $mediaModel->find($mediaId);
        if (!$existingMedia) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Media not found"
            ]);
        }

        // Update the media record
        $updateMedia = $mediaModel->update($mediaId, $data);

        // Check if the update was successful
        if ($updateMedia) {
            return $this->response->setJSON([
                "status" => "success",
                "message" => "Media updated successfully",
                "data" => $data
            ]);
        } else {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Failed to update media"
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
