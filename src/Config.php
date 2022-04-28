<?php

namespace Ngekoding\CodeIgniterDataTables;

class Config
{
    protected $ciVersion;

    /**
     * Map the method to call base on CodeIgniter version
     * We use version 4 as the references name
     */
    protected $methodsMapping = [
        '3' => [
            'countAllResults' => 'count_all_results',
            'orderBy' => 'order_by',
            'where' => 'where',
            'limit' => 'limit',
            'get' => 'get',
            'QBSelect' => 'qb_select',
            'getFieldNames' => 'list_fields',
            'getResult' => 'result',
            'getResultArray' => 'result_array',
        ]
    ];

    public function __construct($ciVersion = '4')
    {
        $this->ciVersion = $ciVersion;
    }

    public function get($name)
    {
        if (isset($this->methodsMapping[$this->ciVersion])) {
            return $this->methodsMapping[$this->ciVersion][$name];
        }
        return $name;
    }
}
