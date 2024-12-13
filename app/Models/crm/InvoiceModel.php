<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'invoice';
    protected $primaryKey = 'inv_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "inv_name",
        "inv_po_no",
        "inv_number",
        "inv_challan",
        "inv_description",
        "inv_package",
        "inv_quantity",
        "inv_subtotal",
        "inv_transport",
        "inv_gst",
        "inv_discount",
        "inv_bank",
        "inv_date",
        "inv_slug",
        "inv_total",
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}
