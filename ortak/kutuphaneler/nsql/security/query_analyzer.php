<?php

namespace nsql\database\security;

/**
 * Query Analyzer sınıfı
 * Sorgu analizi ve güvenlik kontrolü
 */
class query_analyzer
{
    /**
     * Sorguyu analiz et
     */
    public static function analyze(string $query): array
    {
        $analysis = [
            'type' => self::get_query_type($query),
            'tables' => self::extract_tables($query),
            'columns' => self::extract_columns($query),
            'has_where' => self::has_where_clause($query),
            'has_joins' => self::has_joins($query),
            'is_safe' => self::is_safe_query($query),
            'risk_level' => self::get_risk_level($query)
        ];

        return $analysis;
    }

    /**
     * Sorgu tipini belirle
     */
    private static function get_query_type(string $query): string
    {
        $query = trim(strtoupper($query));
        
        if (strpos($query, 'SELECT') === 0) return 'SELECT';
        if (strpos($query, 'INSERT') === 0) return 'INSERT';
        if (strpos($query, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($query, 'DELETE') === 0) return 'DELETE';
        if (strpos($query, 'CREATE') === 0) return 'CREATE';
        if (strpos($query, 'DROP') === 0) return 'DROP';
        if (strpos($query, 'ALTER') === 0) return 'ALTER';
        
        return 'UNKNOWN';
    }

    /**
     * Tablo isimlerini çıkar
     */
    private static function extract_tables(string $query): array
    {
        $tables = [];
        $query = strtoupper($query);
        
        // FROM clause
        if (preg_match('/FROM\s+(\w+)/i', $query, $matches)) {
            $tables[] = $matches[1];
        }
        
        // JOIN clauses
        if (preg_match_all('/JOIN\s+(\w+)/i', $query, $matches)) {
            $tables = array_merge($tables, $matches[1]);
        }
        
        return array_unique($tables);
    }

    /**
     * Sütun isimlerini çıkar
     */
    private static function extract_columns(string $query): array
    {
        $columns = [];
        
        // SELECT clause
        if (preg_match('/SELECT\s+(.*?)\s+FROM/i', $query, $matches)) {
            $select_part = $matches[1];
            if (preg_match_all('/(\w+)/', $select_part, $col_matches)) {
                $columns = $col_matches[1];
            }
        }
        
        return array_unique($columns);
    }

    /**
     * WHERE clause var mı?
     */
    private static function has_where_clause(string $query): bool
    {
        return stripos($query, 'WHERE') !== false;
    }

    /**
     * JOIN var mı?
     */
    private static function has_joins(string $query): bool
    {
        return stripos($query, 'JOIN') !== false;
    }

    /**
     * Güvenli sorgu mu?
     */
    private static function is_safe_query(string $query): bool
    {
        $dangerous_patterns = [
            '/DROP\s+TABLE/i',
            '/TRUNCATE\s+TABLE/i',
            '/DELETE\s+FROM\s+\w+\s*$/i',
            '/UPDATE\s+\w+\s+SET\s+\w+\s*=/i'
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $query)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Risk seviyesini belirle
     */
    private static function get_risk_level(string $query): string
    {
        $query = strtoupper($query);
        
        if (strpos($query, 'DROP') !== false || strpos($query, 'TRUNCATE') !== false) {
            return 'HIGH';
        }
        
        if (strpos($query, 'DELETE') !== false || strpos($query, 'UPDATE') !== false) {
            return 'MEDIUM';
        }
        
        if (strpos($query, 'SELECT') !== false || strpos($query, 'INSERT') !== false) {
            return 'LOW';
        }
        
        return 'UNKNOWN';
    }
}
?>
