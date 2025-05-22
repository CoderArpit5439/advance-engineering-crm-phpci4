<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class InquiryModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'inquiry';
    protected $primaryKey = 'inq_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        
        // OLD FIELDS MADE BY AAKASH
        // "inq_name",
        // "inq_customer_id",
        // "inq_contact",
        // "inq_message",
        // "inq_email",
        // "inq_status",

        // NEW FIELDS MADE BY ARPIT
        "inq_company_id",
        "inq_quotation_id",
        "inq_item_code",
        "inq_drg_no",
        "inq_dimension",
        "inq_material",
        "inq_weight",
        "inq_qty",
        "inq_price",
        "inq_customer_id",
        "inq_description",
        "inq_status",
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
