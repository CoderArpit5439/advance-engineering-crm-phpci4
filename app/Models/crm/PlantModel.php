<?php

namespace App\Models\Crm;

use CodeIgniter\Model;

class PlantModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'plant';
    protected $primaryKey       = 'p_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "company_name",
        "p_state",
        "p_city",
        "p_area_working",
        "p_tax_type",
        "p_pincode",
        "p_address",
        "p_gst",
        "p_security_contact",
        "p_account_contact",
        "p_store_contact",
        "p_other_contact",
        "p_security_email",
        "p_account_email",
        "p_store_email",
        "p_other_email",
        "p_international_domestic",
        "c_id"
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
