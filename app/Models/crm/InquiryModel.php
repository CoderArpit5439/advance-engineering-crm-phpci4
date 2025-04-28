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
        "inq_name",
        "inq_contact",
        "inq_message",
        "inq_email",
        "inq_status",
        "p_name",
        "p_size",
        "p_moc",
        "p_thickness",
        "p_drg",
        "p_product",
        "p_code",
        "p_info",
         "p_id"

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
