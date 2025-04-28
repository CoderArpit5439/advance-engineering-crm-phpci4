<?php

namespace App\Models\crm;

use CodeIgniter\Model;

class QuotationProductionModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'quotationProduction';
    protected $primaryKey = 'qp_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        "p_category",
        "p_unique_id",
        "p_name",
        "p_slug",
        "p_price",
        "p_material",
        "p_moc",
        "p_dimension",
        "p_brand",
        "p_color",
        "p_weight",
        "p_description",
        "p_manufacturer",
        "p_country",
        "p_code",
        "p_drawing_no",
        "p_finish_type",
        "customerId",
        "customerName",
        "inq_id",
         "q_id"
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
