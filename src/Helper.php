<?php

namespace Ngekoding\CodeIgniterDataTables;

use PHPSQLParser\PHPSQLParser;

class Helper
{
    public static function getColumnAliases($qbSelect)
    {
        if (empty($qbSelect)) return [];

        $sql = 'SELECT '.implode(', ', $qbSelect);
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sql);

        $columnAliases = [];
        foreach ($parsed['SELECT'] as $select) {
            if ($select['alias']) {
                $alias = $select['alias']['name'];
                if ($select['expr_type'] == 'colref') {
                    $key = $select['base_expr'];
                } elseif (strpos($select['expr_type'], 'function') !== FALSE) {
                    $parts = [];
                    foreach ($select['sub_tree'] as $part) {
                        $parts[] = $part['base_expr'];
                    }
                    $key =  $select['base_expr'].'('.implode(', ', $parts).')';
                }
                $columnAliases[$alias] = $key;
            }
        }

        return $columnAliases;
    }

    /**
     * Get all select fields result
     * Used when we use the arrays data source for ordering
     * @param $queryBuilder
     * @param Config $config
     * 
     * @return array
     */
    public static function getFieldNames($queryBuilder, $config)
    {
        return $queryBuilder->where('0=1') // We don't need any data
                            ->{$config->get('get')}()
                            ->{$config->get('getFieldNames')}();
    }
}
