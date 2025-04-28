<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\crm\InquiryModel;
use App\Models\Customer\CartModel;
use App\Models\Customer\CustomerModel;
use App\Models\Product\ProductModel;
use CodeIgniter\RESTful\ResourceController;

helper("token");

class DashboardController extends ResourceController
{
    public function fetchDashboardDetails()
    {
        $customerModel = new CustomerModel();
        $productModel = new ProductModel();
    
        try {
          
            $customerList = $customerModel->orderBy('c_created_at', 'DESC')->findAll(5, 0);
            $productList = $productModel->orderBy('p_created_at', 'DESC')->findAll(5,0);
    
            
            $activeCustomers = $customerModel->where('c_status', 'active')->countAllResults();
            $inactiveCustomers = $customerModel->where('c_status', 'inactive')->countAllResults();
    
           
            $activeProducts = $productModel->where('p_status', 'active')->countAllResults();
            $inactiveProducts = $productModel->where('p_status', 'inactive')->countAllResults();
    
        
            $response = [
                'status' => 'success',
                'message' => 'Data successfully fetched.',
                'customer' => $customerList ?? [],  
                'product' => $productList ?? [],    
                'customer_count' => [
                    'active' => $activeCustomers,
                    'inactive' => $inactiveCustomers
                ],
                'product_count' => [
                    'active' => $activeProducts,
                    'inactive' => $inactiveProducts
                ]
            ];

            if (empty($customerList)) {
                $response['message'] = 'No customers found.';
                $response['customer'] = [];
            }
    
            if (empty($productList)) {
                $response['message'] = 'No products found.';
                $response['product'] = [];
            }
    
            
            return $this->response->setJSON($response);
    
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'data' => null
            ]);
        }
    }
    

}
