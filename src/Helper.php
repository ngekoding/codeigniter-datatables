<?php

namespace Ngekoding\CodeIgniterDataTables;

use PHPSQLParser\PHPSQLParser;

class Helper
{
    public static function getColumnAliases($queryBuilder, $config)
    {
        $queryBuilderClone = clone $queryBuilder;
        $compiledSelect = $queryBuilderClone->{$config->get('getCompiledSelect')}();

        $parser = new PHPSQLParser();
        $parsed = $parser->parse($compiledSelect);

        $tableAliases = [];
        foreach ($parsed['FROM'] as $from) {
            if ($from['expr_type'] === 'table') {
                $name = $from['no_quotes']['parts'][0];
                $alias = isset($from['alias']['name']) ? $from['alias']['no_quotes']['parts'][0] : $name;
                $tableAliases[$alias] = $name;
            }
        }

        $columnAliases = [];
        foreach ($parsed['SELECT'] as $select) {
            $expr_type = $select['expr_type'];
            $base_expr = $select['base_expr'];

            if (isset($select['alias']['name'])) {
                $alias = $select['alias']['no_quotes']['parts'][0];

                if ($expr_type === 'colref') {
                    $key = implode('.', $select['no_quotes']['parts']);
                } elseif ($expr_type === 'expression') {
                    $key = trim(str_replace($alias, '', $base_expr));
                } elseif (str_contains($expr_type, 'function')) {
                    $parts = [];
                    foreach ($select['sub_tree'] as $part) {
                        $parts[] = $part['base_expr'];
                    }
                    $key =  $base_expr . '(' . implode(', ', $parts) . ')';
                }
                $columnAliases[$alias] = $key;
            } elseif ($expr_type === 'colref') {
                if (str_contains($base_expr, '*')) {
                    if (str_contains($base_expr, '.')) {
                        $tableAlias = $select['no_quotes']['parts'][0];
                    } else {
                        $tableAlias = array_key_first($tableAliases);
                    }

                    $tableName = $tableAliases[$tableAlias];
                    $fields = $queryBuilder->{$config->get('getFieldNames')}($tableName);
                    foreach ($fields as $field) {
                        $key = "{$tableAlias}.{$field}";
                        $columnAliases[$field] = $key;
                    } 
                } elseif (str_contains($base_expr, '.')) {
                    $field = $select['no_quotes']['parts'][1];
                    $key = implode('.', $select['no_quotes']['parts']);
                    $columnAliases[$field] = $key;
                } 
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

    /**
     * Resolve CodeIgniter version
     * 
     * @param string|int|null $ciVersion
     * @return int
     */
    public static function resolveCodeIgniterVersion($ciVersion)
    {
        if ( ! in_array($ciVersion, [3, 4])) {
            if (class_exists(\CodeIgniter\Database\BaseBuilder::class)) {
                return 4;
            }
            return 3;
        }
        return (int) $ciVersion;
    }
}
