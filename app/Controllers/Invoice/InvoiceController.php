<?php

namespace App\Controllers\Invoice;

use App\Models\crm\InvoiceModel;
use CodeIgniter\RESTful\ResourceController;
// use Mpdf\Mpdf;

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

    public function generateInvoice()
    {
        $invoiceModel = new InvoiceModel();

        $invoiceId = $this->request->getGet('invoice_id');
        // Fetch data from the database
        $invoiceData = $invoiceModel->find($invoiceId);

        if (!$invoiceData) {
            return $this->response->setStatusCode(404)->setBody('Invoice not found');
        }

        // Prepare dynamic data
        $company = "Your Company Name";
        $items = $invoiceData['inv_items']; // Assuming this is an array of items
        $totals = $invoiceData['inv_totals'];
        $bankDetails = $invoiceData['inv_bank'];

        // HTML Content
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .invoice-header { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                table, th, td { border: 1px solid black; }
                th, td { padding: 8px; text-align: left; }
                .total { font-weight: bold; }
                .bank-details { margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="invoice-header">
                <h1>Invoice</h1>
                <p><strong>' . $company . '</strong></p>
            </div>

            <h3>Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>HSN Code</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Price Per Unit</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
                
                foreach ($items as $item) {
                    $html .= '
                    <tr>
                        <td>' . $item['inv_item_name'] . '</td>
                        <td>' . $item['inv_hsn_code'] . '</td>
                        <td>' . $item['inv_quantity'] . '</td>
                        <td>' . $item['inv_unit'] . '</td>
                        <td>₹ ' . number_format($item['inv_price_per_unit'], 2) . '</td>
                        <td>₹ ' . number_format($item['inv_total'], 2) . '</td>
                    </tr>';
                }

                $html .= '
                </tbody>
            </table>

            <h3>Totals</h3>
            <table>
                <tbody>
                    <tr>
                        <td>Total Quantity</td>
                        <td>' . $totals['inv_total_quantity'] . '</td>
                    </tr>
                    <tr>
                        <td>Total Package</td>
                        <td>' . $totals['inv_total_package'] . '</td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td>₹ ' . number_format($totals['inv_subtotal'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>Transport</td>
                        <td>₹ ' . number_format($totals['inv_transport'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>GST</td>
                        <td>₹ ' . number_format($totals['inv_gst'], 2) . '</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>₹ ' . number_format($totals['inv_discount'], 2) . '</td>
                    </tr>
                    <tr class="total">
                        <td>Total</td>
                        <td>₹ ' . number_format($totals['inv_total'], 2) . '</td>
                    </tr>
                </tbody>
            </table>

            <h3>Bank Details</h3>
            <table class="bank-details">
                <tbody>
                    <tr>
                        <td>Bank Name</td>
                        <td>' . $bankDetails['inv_bank_name'] . '</td>
                    </tr>
                    <tr>
                        <td>IFSC Code</td>
                        <td>' . $bankDetails['inv_ifsc_code'] . '</td>
                    </tr>
                    <tr>
                        <td>Account Holder Name</td>
                        <td>' . $bankDetails['inv_account_holder_name'] . '</td>
                    </tr>
                    <tr>
                        <td>Account Number</td>
                        <td>' . $bankDetails['inv_account_number'] . '</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';

        // Generate PDF
        $mpdf = new \Mpdf\Mpdf();
        // ([
        //     'margin_left' => 20,
        //     'margin_right' => 15,
        //     'margin_top' => 48,
        //     'margin_bottom' => 25,
        //     'margin_header' => 10,
        //     'margin_footer' => 10
        // ]);
        $mpdf->WriteHTML($html);
        $pdfOutput = $mpdf->Output('', 'S'); // Save as string for response

        // Send PDF as response
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="invoice.pdf"')
            ->setBody($pdfOutput);
    }

}
