<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'orders';
    protected $primaryKey = 'or_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        // OLD FIELDS MADE BY AAKASH
        // "or_customer" ,
        // "or_contact" ,
        // "or_order_" ,
        // "or_cstr_p_o" ,
        // "or_item" ,
        // "or_due_date" ,
        // "or_qty" ,
        // "or_pndg" ,
        // "or_done" ,
        // "or_unit" ,
        // "or_total" ,
        // "or_status" ,

        // NEW FIELDS MADE BY ARPIT

        "or_company_id",
		"or_customer_id",
		"or_quotation_id",	
		"or_po_number",		
		"or_item_code",			
		"or_drg_no",
		"or_po_description",		
		"or_description",		
		"or_dimension",			
		"or_material",			
		"or_weight",			
		"or_qty",			
		"or_price",			
		"or_total_amt",			
		"or_thikness"
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
