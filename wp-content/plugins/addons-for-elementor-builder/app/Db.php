<?php

namespace AFEB;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DataBase Class
 * 
 * @class Db
 * @version 1.0.0
 */
class Db extends Base
{
    /**
     * Current DataBase Version
     */
    const DB_VERSION = '4';

    /**
     * Running any query
     * 
     * @since 1.0.0
     * 
     * @param string $query
     * @param string $type
     * 
     * @return mixed
     */
    public function query($query, $type = 'SELECT')
    {
        $type = strtoupper($type);
        $database = $this->get_dbo();

        if (trim($type) != 'SELECT') {
            $query = $this->prefix($query);
        }
        if (trim($type) == 'SELECT') {
            return $this->slct($query);
        }
        if ($type == 'INSERT') {
            $database->query($query);
            return $database->insert_id;
        }

        return $database->query($query);
    }

    /**
     * Returns records count of a query
     * 
     * @since 1.0.0
     * 
     * @param string $query
     * @param string $table
     * 
     * @return int
     */
    public function num($query, $table = '')
    {

        if (trim($table)) {
            $query = "SELECT COUNT(*) FROM `#__$table`";
        }

        $query = $this->prefix($query);
        $dbo = $this->get_dbo();

        return $dbo->get_var($query);
    }

    /**
     * Selects records from Db
     * 
     * @since 1.0.0
     * 
     * @param string $query
     * @param string $result
     * 
     * @return mixed
     */
    public function slct($query, $result = 'OBJECTLIST')
    {
        $query = $this->prefix($query);
        $database = $this->get_dbo();

        $result = strtoupper(trim($result));
        if ($result == 'OBJECTLIST') {
            return $database->get_results($query, OBJECT_K);
        } elseif ($result == 'OBJECT') {
            return $database->get_row($query, OBJECT);
        } elseif ($result == 'ASSOCLIST') {
            return $database->get_results($query, ARRAY_A);
        } elseif ($result == 'ASSOC') {
            return $database->get_row($query, ARRAY_A);
        } elseif ($result == 'RESULT') {
            return $database->get_var($query);
        } elseif ($result == 'COLUMN') {
            return $database->get_col($query);
        } else {
            return $database->get_results($query, OBJECT_K);
        }
    }

    /**
     * Apply WordPress table prefix on queries
     * 
     * @since 1.0.0
     * 
     * @param string $query
     * 
     * @return string
     */
    public function prefix($query)
    {
        $wpdb = $this->get_dbo();
        $query = str_replace('#__blogs', $wpdb->base_prefix . 'blogs', $query);
        $query = str_replace('#__', $wpdb->prefix, $query);

        return $query;
    }

    /**
     * Prepares a SQL query for safe execution
     * 
     * @since 1.0.0
     * 
     * @param string $query
     * 
     * @return string
     */
    public function prepare($query)
    {
        return $query;
    }

    /**
     * Clear Sql Result Cache
     */
    public function flush()
    {
        $database = $this->get_dbo();
        $database->flush();
    }

    /**
     * Returns wordPress Db object
     * 
     * @since 1.0.0
     * 
     * @global object $wpdb
     * 
     * @return object
     */
    public function get_dbo()
    {
        global $wpdb;
        return $wpdb;
    }
}
