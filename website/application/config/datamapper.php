<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Data Mapper Configuration
 *
 * Global configuration settings that apply to all DataMapped models.
 */

$config['prefix'] = 'tds_';
$config['join_prefix'] = '';
$config['error_prefix'] = '';
$config['error_suffix'] = '';
$config['created_field'] = 'created';
$config['updated_field'] = 'updated';
$config['local_time'] = TRUE;
$config['unix_timestamp'] = FALSE;
$config['timestamp_format'] = 'Y-m-d H:i:s';
$config['lang_file_format'] = 'model_${model}';
$config['field_label_lang_format'] = '${model}_${field}';
$config['auto_transaction'] = FALSE;
$config['auto_populate_has_many'] = FALSE;
$config['auto_populate_has_one'] = FALSE;
$config['all_array_uses_ids'] = FALSE;
// set to FALSE to use the same DB instance across the board (breaks subqueries)
// Set to any acceptable parameters to $CI->database() to override the default.
$config['db_params'] = '';
// Uncomment to enable the production cache
// $config['production_cache'] = 'datamapper/cache';
//$config['production_cache'] = FCPATH.'upload/cache/datamapper';
$config['extensions_path'] = 'datamapper';
$config['extensions'] = array();

/* End of file datamapper.php */
/* Location: ./application/config/datamapper.php */
