<?php

namespace App\Controllers\Order;

use App\Models\crm\OrderModel;
use CodeIgniter\RESTful\ResourceController;
helper('token');
class OrderController extends ResourceController
{
    public function fetchOrders()
    {

        try {

            $orderModel = new OrderModel();
            $allOrder = $orderModel->orderBy("or_id", "DESC")->findAll();
            $countOrder = $orderModel->countAllResults();

            if ($allOrder) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allOrder) . ' Ordres found',
                    "data" => $allOrder,
                    "count" => $countOrder
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Orders not found',
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

    public function createOrder()
    {
        $orderModel = new OrderModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "or_customer" => $this->request->getVar("or_customer") ?? "",
            "or_contact" => $this->request->getVar("or_contact") ?? "",
            "or_order_no" => $this->request->getVar("or_order_no") ?? "",
            "or_cstr_p_o" => $this->request->getVar("or_cstr_p_o") ?? "",
            "or_item" => $this->request->getVar("or_item") ?? "",
            "or_due_date" => $this->request->getVar("or_due_date") ?? "",
            "or_qty" => $this->request->getVar("or_qty") ?? "",
            "or_pndg" => $this->request->getVar("or_pndg") ?? "",
            "or_done" => $this->request->getVar("or_done") ?? "",
            "or_unit" => $this->request->getVar("or_unit") ?? "",
            "or_total" => $this->request->getVar("or_total") ?? "",
            "or_status" => $this->request->getVar("or_status") ?? "",
        ];

        try {
            $orderAdded = $orderModel->insert($data);
            if ($orderAdded) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Order added successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Order not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }

     // Update Order
     public function updateOrder()
     {
         $orderModel = new OrderModel();
         $isToken = check_jwt_authentication();
 
         if (!$isToken) {
             return $this->respond([
                 "status" => "error",
                 "message" => "Authentication failed"
             ], 401);
         }
          $id = $this->request->getVar("or_id");
         $order = $orderModel->find($id);
         if (!$order) {
             return $this->respond([
                 "status" => "error",
                 "message" => "Order not found"
             ], 404);
         }
 
         $data = [
             "or_customer" => $this->request->getVar("or_customer") ?? $order['or_customer'],
             "or_contact" => $this->request->getVar("or_contact") ?? $order['or_contact'],
             "or_order_no" => $this->request->getVar("or_order_no") ?? $order['or_order_no'],
             "or_cstr_p_o" => $this->request->getVar("or_cstr_p_o") ?? $order['or_cstr_p_o'],
             "or_item" => $this->request->getVar("or_item") ?? $order['or_item'],
             "or_due_date" => $this->request->getVar("or_due_date") ?? $order['or_due_date'],
             "or_qty" => $this->request->getVar("or_qty") ?? $order['or_qty'],
             "or_pndg" => $this->request->getVar("or_pndg") ?? $order['or_pndg'],
             "or_done" => $this->request->getVar("or_done") ?? $order['or_done'],
             "or_unit" => $this->request->getVar("or_unit") ?? $order['or_unit'],
             "or_total" => $this->request->getVar("or_total") ?? $order['or_total'],
             "or_status" => $this->request->getVar("or_status") ?? $order['or_status'],
         ];
 
         try {
             $orderModel->update($id, $data);
             return $this->respond([
                 "status" => "success",
                 "message" => "Order updated successfully",
                 "data" => $data
             ]);
         } catch (\Throwable $th) {
             return $this->respond([
                 "status" => "error",
                 "message" => $th->getMessage()
             ], 500);
         }
     }
}
