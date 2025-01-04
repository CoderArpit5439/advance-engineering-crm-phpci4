<?php

namespace App\Controllers\Order;

use App\Models\crm\OrderModel;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends ResourceController
{
    public function fetchOrders()
    {
        $orderModel = new OrderModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "message" => "Authentication failed",
                "status" => "error"
            ]);
        }

        try {
            $page = $this->request->getGet("page") ?? 1;
            $page = ($page * 20) - 20;
            $search = $this->request->getGet("search") ?? "";

            $allOrders = $orderModel->orderBy("or_order_no", "DESC")
                ->like("or_customer", $search)
                ->orLike("or_contact", $search)
                ->findAll(20, $page);

            $countOrders = $orderModel->like("or_customer", $search)
                ->orLike("or_contact", $search)
                ->countAllResults();

            if ($allOrders) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allOrders) . ' Orders found',
                    "data" => $allOrders,
                    "count" => $countOrders
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No orders found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage(),
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

    public function updateOrder()
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

        $orderId = $this->request->getGet("id");

        try {
            $orderModel->update($orderId, $data);
            $updatedOrder = $orderModel->where("or_order_no", $orderId)->first();
            if ($updatedOrder) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Order updated successfully",
                    "data" => $updatedOrder
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Order not updated"
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
