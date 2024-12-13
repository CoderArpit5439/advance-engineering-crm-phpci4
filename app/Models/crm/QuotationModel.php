<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class QuotationModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'quotation';
    protected $primaryKey = 'quo_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "quo_name",
        "quo_pdf",
        "quo_slug",
        "quo_date",
        "quo_subject",
        "quo_number",
        "quo_description",
        "quo_quantity",
        "quo_kg",
        "quo_subtotal",
        "quo_discount",
        "quo_total",
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
