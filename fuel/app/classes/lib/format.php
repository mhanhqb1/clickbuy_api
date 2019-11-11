<?php

/**
 * Part of the Fuel framework.
 *
 * @package    Lib
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Lib;

/**
 * Format class
 *
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @package    Fuel
 * @category   Core
 * @author     Fuel Development Team
 * @copyright  2010 - 2012 Fuel Development Team
 * @link       http://docs.fuelphp.com/classes/format.html
 */
class Format extends \Fuel\Core\Format {

    public function __construct($data = null, $from_type = null) {
        parent::__construct($data, $from_type);
    }

    /**
     * and convert PFP array to HTML ul li
     *
     * @author      thailh 
     * @param array $data Input array
     * @return string HTML ul li string
     * 
     */
    public function to_html($data = null) {
        if ($data == null) {
            $data = $this->_data;
        }
        $return = '<ul>';
        foreach ($data as $item) {
            $return .= '<li>' . (is_array($item) ? $this->to_html($item) : $item) . '</li>';
        }
        $return .= '</ul>';
        return $return;
    }

    /**
     * To XML conversion
     *
     * @param   mixed $data
     * @param   null $structure
     * @param   null|string $basenode
     * @param   type $use_cdata
     * @param   type $default_item
     * @return  string XML format
     */
    public function to_custom_xml($data = null, $structure = null, $basenode = null, $use_cdata = null, $default_item = 'ITEM') {
        if ($data == null) {
            $data = $this->_data;
        }

        is_null($basenode) and $basenode = \Config::get('format.xml.basenode', 'xml');
        is_null($use_cdata) and $use_cdata = \Config::get('format.xml.use_cdata', false);

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($structure == null) {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }

        // Force it to be something useful
        if (!is_array($data) and ! is_object($data)) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z_\-0-9]/i', '', $key);
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                // make string key...
                $key = (\Inflector::singularize($basenode) != $basenode) ? \Inflector::singularize($basenode) : $default_item;
            }

            // if there is another array found recrusively call this function
            if (is_array($value) or is_object($value)) {
                $node = $structure->addChild($key);

                // recursive call if value is not empty
                if (!empty($value)) {
                    $this->to_custom_xml($value, $node, $key, $use_cdata, $default_item);
                }
            } else {
                // add single node.
                $encoded = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

                if ($use_cdata and ( $encoded !== (string) $value)) {
                    $dom = dom_import_simplexml($structure->addChild($key));
                    $owner = $dom->ownerDocument;
                    $dom->appendChild($owner->createCDATASection($value));
                } else {
                    $structure->addChild($key, $encoded);
                }
            }
        }

        // pass back as string. or simple xml object if you want!
        return $structure->asXML();
    }

    /**
     * Import Serialized data
     *
     * @param   string  $string
     * @return  mixed
     */
    private function _from_serialize($string) {
        return unserialize(trim($string));
    }

    /**
     * Import JSON data
     *
     * @param   string  $string
     * @return  mixed
     */
    private function _from_json($string) {
        return json_decode(trim($string), true);
    }

    /**
     * Loads Format config.
     */
    public static function _init() {
        parent::_init();
    }

    /**
     * To CSV conversion, without header
     *
     * @param   mixed   $data
     * @param   mixed   $delimiter
     * @return  string Csv format
     */
    public function to_custom_csv($data = null, $delimiter = null) {
        // csv format settings
        $newline = \Config::get('format.csv.export.newline', \Config::get('format.csv.newline', "\n"));
        $delimiter or $delimiter = \Config::get('format.csv.export.delimiter', \Config::get('format.csv.delimiter', ','));
        $enclosure = \Config::get('format.csv.export.enclosure', \Config::get('format.csv.enclosure', '"'));
        $escape = \Config::get('format.csv.export.escape', \Config::get('format.csv.escape', '"'));

        // escape function
        $escaper = function($items) use($enclosure, $escape) {
            return array_map(function($item) use($enclosure, $escape) {
                return str_replace($enclosure, $escape . $enclosure, $item);
            }, $items);
        };

        if ($data === null) {
            $data = $this->_data;
        }

        if (is_object($data) and ! $data instanceof \Iterator) {
            $data = $this->to_array($data);
        }

        $output = '';

        foreach ($data as $row) {
            $output .= $enclosure . implode($enclosure . $delimiter . $enclosure, $escaper((array) $row)) . $enclosure . $newline;
        }

        return rtrim($output, $newline);
    }

}
