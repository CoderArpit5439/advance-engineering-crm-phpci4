<?php

namespace App\Controllers\Invoice;

use App\Models\crm\InvoiceModel;
use CodeIgniter\RESTful\ResourceController;

class InvoiceController extends ResourceController
{



    public function creatInvoice()
    {
        $invoiceModel = new InvoiceModel();
        $inv_name = $this->request->getVar("inv_name") ?? "";
        $inv_date = $this->request->getVar("inv_date") ?? "";
        $inv_po_no = $this->request->getVar("inv_po_no") ?? "";
        $inv_number = $this->request->getVar("inv_number") ?? "";
        $inv_challan = $this->request->getVar("inv_challan") ?? "";
        $inv_description = $this->request->getVar("inv_description") ?? "";
        $inv_package = $this->request->getVar("inv_package") ?? "";
        $inv_quantity = $this->request->getVar("inv_quantity") ?? "";
       $inv_subtotal = $this->request->getVar("inv_subtotal") ?? "";
        $inv_transport = $this->request->getVar("inv_transport") ?? "";
        $inv_gst = $this->request->getVar("inv_gst") ?? "";
        $inv_discount = $this->request->getVar("inv_discount") ?? "";
        $inv_bank = $this->request->getVar("inv_bank") ?? "";
        $inv_total = $this->request->getVar("inv_total") ?? "";
        $randomNumber = rand(0,1000);

        $invoiceSlug = url_title($inv_name . ' ' . $randomNumber, '-', true);

        $data = [
            'inv_name' => $inv_name,
            'inv_date' => $inv_date,
            'inv_po_no' => $inv_po_no,
            'inv_number' => $inv_number,
            'inv_challan' => $inv_challan,
            'inv_description' => $inv_description,
            'inv_package' => $inv_package,
            'inv_quantity' => $inv_quantity,
            'inv_subtotal' => $inv_subtotal,
            'inv_transport' => $inv_transport,
            'inv_gst' => $inv_gst,
            'inv_discount' => $inv_discount,
            'inv_bank' => $inv_bank,
            'inv_total' => $inv_total,
            'inv_slug' => $invoiceSlug,
        ];

        try {
            $invoiceAdd = $invoiceModel->insert($data);
            if ($invoiceAdd) {
                return $this->response->setJSON([
                    "status" => true,
                    "message" => "Invoice uploaded successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => false,
                    "message" => "Invoice not uploaded"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => false,
                "message" => $th->getMessage()
            ]);
        }
    }


}
