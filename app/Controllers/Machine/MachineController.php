<?php
namespace App\Controllers\Company;


use App\Models\Crm\MachineModel;
use CodeIgniter\RESTful\ResourceController;

helper('token');
class MachineController extends ResourceController
{
    public function addMachine()
    {
        // JWT authentication check
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Handle image file upload
        $imageFile = $this->request->getFile('m_image');
        if (!$imageFile || !$imageFile->isValid()) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Please upload a valid image."
            ]);
        }

        // Define upload path
        $uploadPath = FCPATH . 'public/assets/img/uploads/MachineImage/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Check if directory is writable
        if (!is_writable($uploadPath)) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Upload directory is not writable."
            ]);
        }

        // Move uploaded file
        $newFileName = $imageFile->getRandomName(); // optional: randomize filename
        if ($imageFile->move($uploadPath, $newFileName)) {
            $fileName = base_url("public/assets/img/uploads/MachineImage/" . $newFileName);
        } else {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Failed to upload image."
            ]);
        }

        // Prepare data
        $data = [
            "m_name" => $this->request->getVar("m_name") ?? "",
            "m_company_name" => $this->request->getVar("m_company_name") ?? "",
            "m_score" => $this->request->getVar("m_score") ?? "",
            "m_image" => $fileName,
            "m_location" => $this->request->getVar("m_location") ?? "",
            "m_tags" => $this->request->getVar("m_tags") ?? ""
        ];

        // Insert into DB
        try {
            $machineModel = new MachineModel();
            $addMachine = $machineModel->insert($data);

            if ($addMachine) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Machine information added successfully.",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Something went wrong while saving data."
                ]);
            }

        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function UpdateMachineData()
    {
        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        $m_Id = $this->request->getVar("m_id");

        if (!$m_Id) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Machine ID is required"
            ]);
        }

        try {
            $MachineModel = new MachineModel();
            $existingData = $$MachineModel->find($m_Id);

            if (!$existingData) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Information not found"
                ]);
            }

            // Get input data for update
            $mName = $this->request->getVar("m_name") ?? $existingData['m_name'];
            $mComapny = $this->request->getVar("m_company_name") ?? $existingData['m_company_name'];

            $mScore = $this->request->getVar("m_score") ?? $existingData['m_score'];

            $mMob = $this->request->getVar("m_company_mob") ?? $existingData['m_company_mob'];

            $mLocation = $this->request->getVar("m_location") ?? $existingData['m_location'];

            $mTags = $this->request->getVar("m_tags") ?? $existingData['m_tags'];


            $updateData = [
                "m_name" => $mName,
                "m_company_name" => $mComapny,
                "m_score" => $mScore,
                "m_company_mob" => $mMob,
                "m_location" => $mLocation,
                "m_tags" => $mTags
            ];



            // Handle image file if provided
            $imageFile = $this->request->getFile('m_image');
            if (isset($imageFile) && !empty($imageFile)) {
                $uploadPath = FCPATH . 'public/assets/img/uploads/MachineImage/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, TRUE);
                }

                if (!is_dir($uploadPath) || !is_writable($uploadPath)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Upload directory is not writable',
                    ]);
                }

                $imageFile->move($uploadPath);

                if ($imageFile->hasMoved()) {
                    $updateData['m_image'] = 'https://api.advanceengineerings.com/public/assets/img/uploads/MachineImage/' . $imageFile->getName();
                } else {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Failed to upload machine image Image.'
                    ]);
                }
            }

            $MachineModel->update($m_Id, $updateData);

            return $this->response->setJSON([
                "status" => "success",
                "message" => "Machine data updated successfully",
                "data" => $updateData
            ]);
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

    public function deleteMachineData()
    {
    $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }
    

        $mId = $this->request->getVar("m_id");
    
        if (!$mId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "ID  is required"
            ]);
        }
    
        try {
            $machineModel = new MachineModel();
            $existingData = $machineModel->find($mId);
    
            if (!$existingData) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Information not found"
                ]);
            }
    

            $deleteData = $machineModel ->delete($mId);
    
            if ($deleteData) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Data  removed successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to remove data"
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