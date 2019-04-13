<?php

/**
 * Query File
 *
 * Query File is basicaly a lightweight PHP class for queries (retrieve/select/where/order) custom data from a flat file.
 * File is pretty to learn, not like JSON, or ARRAY, or SQL, all level of developers can learn easy that typo of files.
 *  *
 * Homepage: -
 * GitHub: https://github.com/dev.fjonathan
 * README: -
 * CONTRIBUTING: -
 * KNOWN LIMITATIONS: -
 * EXAMPLES: -
 *
 * @license - MIT License
 * @author  Jonathan Franco <dev.fjonathan@gmail.com>
 *
 * @version 0.1
 */

namespace File;

class Q
{

    const FILETYPE = 'ay';
    private $stored;
    
    /** @var Database */

    public static $data;

    /**
     * Load database created by user manually.
     * Check some parameters if database is nulled or corrupted.
     *
     * @file_extist - if file exist in user folder
     * @file_extension - if filetype is not .ay
     *
     * @return static
     */

    public function table($database){

        // [check] if file exist

        if(!file_exists($database)){
            exit ("#1 : Error! No file founded on path: " . $database);
        }

        // [check] file extension

        $filename = substr($database, strrpos( $database, '/' )+1);
        $filetype = explode('.', $filename)[1];

        if($filetype != self::FILETYPE){
            exit("#2 : Error! File type doesn't match to database declared");
        }

        // [retrieve] whith new format

        $filecontent = file_get_contents($database);
        $file = $this->formating_data($filecontent);
        $q = $this->fetch_data($file);

        static::$data = $q;
        return new static;

    }

    /**
     * Formating data to remove characters not defined to load results
     * Like: spaces, or indents
     *
     * @return array
     */

    private function formating_data($filecontent){

        // [remove] indents

        $content = preg_replace('/[ \t]+/', ' ', preg_replace('/[\r\n]+/', "", $filecontent));

        // [remove] spaces

        $content = str_replace('] (','](', $content);
        $content = str_replace(' (','(', $content);
        $content = str_replace(') [',')[', $content);
        $content = str_replace('] [','][', $content);
        $content = str_replace(': ',':', $content);

        return $content;

    }

    /**
     * Group all data without spaces or idents.
     * Build an array to user output (unique/multidimension)
     *
     * @array - it's a minimal array without filter
     *
     * @return array
     */

    private function fetch_data($file){

        $_array = $file;

        $regexp = '/new /';
        $globalize_array = preg_split($regexp, $_array, -1, PREG_SPLIT_NO_EMPTY);

        $_a = [];
        foreach ($globalize_array as $a_key => $a){

            // [creating array of]

            $keyable = [];

            // [settings of obj (1: name; 2: values)]

            $group_name = str_replace('"', '', explode('"(', $a)[0]);
            $group_values = "(".explode('"(', $a)[1];

            $group_delimit = explode("](", $group_values);

            foreach ($group_delimit as $g_key => $g){

                $_content = explode(")[", $g);
                $g_name = str_replace(array("(", ")"), "", $_content[0]);
                $g_values = $_content[1];

                $keyable['name'] = $g_name;

                // [if multidimensional]

                if (strpos($a, '][') !== false) {

                    $_reset = explode('][', $g_values);

                    foreach ($_reset as $r){

                        $_unique_key = explode(":", $r)[0];
                        $_unique_value = explode(":", $r)[1];
                        $keyable[$_unique_key] = $_unique_value;

                    }

                }

                // [if unique]

                if (!strpos($a, '][') !== false) {

                    $_unique_key = explode(":", $g_values)[0];
                    $_unique_value = explode(":", $g_values)[1];
                    $keyable[$_unique_key] = $_unique_value;

                }

                $_a[$group_name][] = $keyable;

            }

        }

        return $_a;

    }

    /**
     * Return all data from and group
     *
     * @return static
     */

    function searchAll($content){

        // [check] if content exist

        if(!isset(static::$data[$content])){
            exit("#3 : Error! Doesn't exist any content called: " . $content);
        }

        $all = static::$data[$content];

        static::$data = $all;
        return new static;

    }

    /**
     * Return all data filtering by key
     * Search in file (group), and retrieve all.
     *
     * Only possible to filter 1 time in a row.
     *
     * @return static
     */

    public function searchBy($content, $where, $element){

        // [check] if content exist

        if(!isset(static::$data[$content])){
            exit("#4 : Error! Doesn't exist any content called: " . $content);
        }

        // [build] array

        $where = $this->where(static::$data[$content], $where, $element);
        
        static::$data = $where;
        return new static;
        
    }

    /**
     * Search by group where one key it's equal to anything
     * For example: groups, id, 1
     *
     * @return array
     */

    function where($array, $key, $value) {

        $results = array();
        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, $this->where($subarray, $key, $value));
            }
        }

        return $results;

    }

    /**
     * Ordering the result by two options
     * Normal options like mysql
     *
     * @ASC - Ordering ascending
     * @DESC - Ordering descening
     *
     * @return array
     */

    function orderBy($field, $type = NULL){

        $array = static::$data;

        $order_settings = array(
            'field' => $field,
            'type' => $type
        );

        $this->stored = $order_settings;

        usort($array, function($asc, $desc) {

            // [order] asc

            $a = $asc;
            $b = $desc;

            // [order] desc

            if($this->stored['type'] == 'DESC'){
                $a = $desc;
                $b = $asc;
            }

            if (!isset($a[$this->stored['field']])){
                exit("#5 : Error! The argument called for order is not valid or SearchBy/SearchAll is not used.");
            }

            $retval = $a[$this->stored['field']] <=> $b[$this->stored['field']];

            if ($retval == 0) {

                $a['suborder'] = [];
                $b['suborder'] = [];

                $retval = $a['suborder'] <=> $b['suborder'];

                if ($retval == 0) {

                    $a['details']['subsuborder'] = [];
                    $b['details']['subsuborder'] = [];
                    $retval = $a['details']['subsuborder'] <=> $b['details']['subsuborder'];

                }

            }

            return $retval;

        });

        static::$data = $array;
        return new static;

    }

    /**
     * Returning all data for user management
     *
     * @return static
     */

    public function record(){

        return static::$data;

    }

}