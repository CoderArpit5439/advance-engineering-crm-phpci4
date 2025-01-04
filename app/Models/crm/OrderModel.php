<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'order';
    protected $primaryKey = 'or_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "or_customer" ,
        "or_contact" ,
        "or_order_no" ,
        "or_cstr_p_o" ,
        "or_item" ,
        "or_due_date" ,
        "or_qty" ,
        "or_pndg" ,
        "or_done" ,
        "or_unit" ,
        "or_total" ,
        "or_status" ,
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
