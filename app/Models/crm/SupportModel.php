<?php

namespace App\Models\Crm;

use CodeIgniter\Model;

class SupportModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'support';
    protected $primaryKey       = 's_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "s_order_no",
        "s_contact",
        "s_poc",
        "s_item",
        "s_due_date",
        "s_qty",
        "s_pndg",
        "s_done",
        "s_unit",
        "s_status",
        "s_total"
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
