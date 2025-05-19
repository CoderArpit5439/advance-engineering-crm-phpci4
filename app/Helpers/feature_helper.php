<?php

use App\Models\Crm\FeatureModel;
use App\Models\Crm\PermissionModel;


function fetch_all_features_for_employees($employee_id) {
    
        $featureModel = new FeatureModel();
        $permissionModel = new PermissionModel();
        
        $allPermissions = $permissionModel->where('p_user_id', $employee_id)->findAll();

        $showFeatures = [];

        foreach ($allPermissions as $permission) {
            
            $permittedFeature = $featureModel->where('f_status', 1)->where('f_id', $permission['p_feature_id'])->first();
            $showFeatures[] = $permittedFeature;


        }
        
        return $showFeatures;

}