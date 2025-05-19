<?php

namespace App\Controllers\Feature;

use App\Controllers\BaseController;
use App\Models\Crm\FeatureModel;

helper('feature');
helper('token');
class FeatureController extends BaseController
{
    public function index()
    {
        //
    }

    // THIS API IS FOR ADMIN ONLY
    public function fetchAllFeatures()
    {
        $featureModel = new FeatureModel();

        $allFeatures = $featureModel->findAll();

        return $this->response->setJSON([
            "status" => true,
            "message" => "Fetch All Features",
            "features" => $allFeatures
        ]);

    }

    public function fetchAllMenusForUsers()
    {

        $isToken = check_jwt_authentication();
        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => false
            ]);
        }

        $allPermittedFeatures = fetch_all_features_for_employees($isToken->emp_id);

        return $this->response->setJSON([
            "status" => true,
            "message" => "Feature fetched",
            "features" => $allPermittedFeatures
        ]);

    }


}
