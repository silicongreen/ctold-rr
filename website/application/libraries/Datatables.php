<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
  /**
  * Ignited Datatables
  *
  * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
  * found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
  *
  * @package    CodeIgniter
  * @subpackage libraries
  * @category   library
  * @version    0.7
  * @author     Vincent Bambico <metal.conspiracy@gmail.com>
  *             Yusuf Ozdemir <yusuf@ozdemir.be>
  * @link       http://codeigniter.com/forums/viewthread/160896/
  */
  class Datatables
  {
    /**
    * Global container variables for chained argument results
    *
    */
    protected $ci;
    protected $table;
    protected $distinct;
    protected $group_by;
    protected $controller_name;
    protected $use_found_rows = false;
    protected $select         = array();
    protected $joins          = array();
    protected $columns        = array();
    protected $where          = array();
    protected $filter         = array();
    protected $add_columns    = array();
    protected $edit_columns   = array();
    protected $unset_columns  = array();
    protected $custom_string_array  = array();
    protected $date_string_field_position  = array();
    protected $custom_string_field_position;
    protected $primary_key;
    protected $image_field_position;
    
    protected $buttons  = array();

    /**
    * Copies an instance of CI
    */
    public function __construct()
    {
      $this->ci =& get_instance();
    }

    /**
    * If you establish multiple databases in config/database.php this will allow you to
    * set the database (other than $active_group) - more info: http://codeigniter.com/forums/viewthread/145901/#712942
    */
    public function set_database($db_name)
    {
	$db_data = $this->ci->load->database($db_name, TRUE);
	$this->ci->db = $db_data;
    }
    
    public function set_image_field_position($field_position)
    {
      $this->image_field_position[]=$field_position;
    } 
    
    public function set_use_found_rows($b_found_rows)
    {
       $this->use_found_rows = $b_found_rows; 
    }
    /**
     * set buttons for datatable
     * @param string $function_name
     */
    public function set_buttons($function_name,$popup="model",$new_window=false,$dependancy=null)
    {
         $this->buttons[]=array($function_name,$popup,$new_window,$dependancy);
        
    } 
    /**
     * set Controller name for datatable
     * @param string $controller_name
     */
    public function set_controller_name($controller_name)
    {
         $this->controller_name=$controller_name;
        
    }
    /**
     * set Primary key for datatable (table)
     * @param string $primary_key 
     */
    public function set_primary_key($primary_key)
    {
         $this->primary_key = $primary_key;
        
    }
    
    public function set_date_string($field_position)
    {
        $this->date_string_field_position[]=$field_position;
        
    } 
    /**
     * set custom string for datatable row
     * @param int $field_position
     * @param array $custom_string_array
     */
    public function set_custom_string($field_position,$custom_string_array = array(1=>"Yes",0=>"No"))
    {
        $this->custom_string_array[]=$custom_string_array;
        
        $this->custom_string_field_position[]=$field_position;
    } 
    /**
     * generate button for datatable
     * @return string
     */
    
    private function generate_button($row_val)
    {
        $button_string = "";
        if(!empty($this->buttons) && $this->controller_name!="")
        {
            foreach($this->buttons as $value)
            {
               if(isset($value[3]) && $value[3])
               {
                  
                   if($row_val[$value[3]['field']]!=$value[3]['value'])
                   {
                       continue;
                   }    
               }    
               
               $popup = ($value[1])?$value[1]:"noModel";
               
               $popup = ($value[2] && $popup=="noModel")?"new_window":$popup;
            
               
               if(($value[0]=="edit" && access_check($this->controller_name,'edit')) || ($value[0]=="update" && access_check($this->controller_name,'update'))  || ($value[0]=="update_pdf" && access_check($this->controller_name,'update_pdf')))
               {
                  $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button" class="light icon_only div_icon '.$popup.'"  id="'.base_url().'admin/'.$this->controller_name.'/'.$value[0].'/pk"><div class="ui-icon ui-icon-pencil" ></div></div></button>';
               }
               else if(($value[0]=="delete" && access_check($this->controller_name,'delete')) || ($value[0]=="del" && access_check($this->controller_name,'del')) || ($value[0]=="remove" && access_check($this->controller_name,'remove')))      
               {
                    $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button"  class="light icon_only div_icon ajax" executeFunction="'.$value[0].'"  id="pk"><div class="ui-icon ui-icon-closethick" ></div></div></button>';
               }
               else if($value[0]=="restore" && access_check($this->controller_name,'restore'))
               {
                  $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button"  class="green icon_only div_icon ajax_restore" executeFunction="'.$value[0].'"  id="pk">'.ucfirst($value[0]).'</div></button>'; 
               }    
              else if(($value[0]=="settings" && access_check($this->controller_name,'settings')) || 
                 ($value[0]=="configuration" && access_check($this->controller_name,'configuration')) 
                 || ($value[0]=="setting" && access_check($this->controller_name,'setting')) 
                 || ($value[0]=="config" && access_check($this->controller_name,'config')) 
                 || ($value[0]=="option" && access_check($this->controller_name,'option')) 
                 || ($value[0]=="options" && access_check($this->controller_name,'options'))
                 || ($value[0]=="opt" && access_check($this->controller_name,'opt'))) 
                {
                  $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button"  class="light icon_only div_icon '.$popup.' "  id="'.base_url().'admin/'.$this->controller_name.'/'.$value[0].'/pk"><div class="ui-icon ui-icon-gear" ></div></div></button>'; 
                }
               else if(access_check($this->controller_name,$value[0]))
               {
                   
                   
                   
                   if($popup == 'ajax')
                       $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button"  class="green icon_only div_icon '.$popup.'" executeFunction="'.$value[0].'" id="pk">'.ucfirst(str_replace("_"," ",$value[0])).'</div></button>';
                   else  
                       $button_string.='<button style="float:left; margin:5px 5px 0px 0px" type="button"  class="green icon_only div_icon '.$popup.'" id="'.base_url().'admin/'.$this->controller_name.'/'.$value[0].'/pk">'.ucfirst(str_replace("_"," ",$value[0])).'</div></button>'; 
        
               }
            }   
            
        }
        return $button_string;
        
    }        

    /**
    * Generates the SELECT portion of the query
    *
    * @param string $columns
    * @param bool $backtick_protect
    * @return mixed
    */
    public function select($columns, $backtick_protect = TRUE)
    {
      foreach($this->explode(',', $columns) as $val)
      {
        $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
        $this->columns[] =  $column;
        $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
      }

      $this->ci->db->select($columns, $backtick_protect);
      return $this;
    }

    /**
    * Generates the DISTINCT portion of the query
    *
    * @param string $column
    * @return mixed
    */
    public function distinct($column)
    {
      $this->distinct = $column;
      $this->ci->db->distinct($column);
      return $this;
    }

    /**
    * Generates the GROUP_BY portion of the query
    *
    * @param string $column
    * @return mixed
    */
    public function group_by($column)
    {
      $this->group_by = $column;
      $this->ci->db->group_by($column);
      return $this;
    }

    /**
    * Generates the FROM portion of the query
    *
    * @param string $table
    * @return mixed
    */
    public function from($table)
    {
      $this->table = $table;
      $this->ci->db->from($table);
      return $this;
    }

    /**
    * Generates the JOIN portion of the query
    *
    * @param string $table
    * @param string $fk
    * @param string $type
    * @return mixed
    */
    public function join($table, $fk, $type = NULL)
    {
      $this->joins[] = array($table, $fk, $type);
      $this->ci->db->join($table, $fk, $type);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->where($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function or_where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->or_where($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function like($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->where[] = array($key_condition, $val, $backtick_protect);
      $this->ci->db->like($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
      $this->filter[] = array($key_condition, $val, $backtick_protect);
      return $this;
    }

    /**
    * Sets additional column variables for adding custom columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function add_column($column, $content, $match_replacement = NULL)
    {
      $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    /**
    * Sets additional column variables for editing columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function edit_column($column, $content, $match_replacement)
    {
      $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
      return $this;
    }

    /**
    * Unset column
    *
    * @param string $column
    * @return mixed
    */
    public function unset_column($column)
    {
      $this->unset_columns[] = $column;
      return $this;
    }

    /**
    * Builds all the necessary query segments and performs the main query based on results set from chained statements
    *
    * @param string charset
    * @return string
    */
    public function generate($charset = 'UTF-8')
    {
      $this->get_paging();
      $this->get_ordering();
      $this->get_filtering();
      return $this->produce_output($charset);
    }

    /**
    * Generates the LIMIT portion of the query
    *
    * @return mixed
    */
    protected function get_paging()
    {
      $iStart = $this->ci->input->post('iDisplayStart');
      $iLength = $this->ci->input->post('iDisplayLength');
      $this->ci->db->limit(($iLength != '' && $iLength != '-1')? $iLength : 100, ($iStart)? $iStart : 0);
    }

    /**
    * Generates the ORDER BY portion of the query
    *
    * @return mixed
    */
    protected function get_ordering()
    {
      if($this->check_mDataprop())
        $mColArray = $this->get_mDataprop();
      elseif($this->ci->input->post('sColumns'))
        $mColArray = explode(',', $this->ci->input->post('sColumns'));
      else
        $mColArray = $this->columns;

      $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
      $columns = array_values(array_diff($this->columns, $this->unset_columns));
 
      for($i = 0; $i < intval($this->ci->input->post('iSortingCols')); $i++)
        if(isset($mColArray[intval($this->ci->input->post('iSortCol_' . $i))]) && in_array($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $columns) && $this->ci->input->post('bSortable_'.intval($this->ci->input->post('iSortCol_' . $i))) == 'true')
          $this->ci->db->order_by($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $this->ci->input->post('sSortDir_' . $i));
        
        
    
    }

    /**
    * Generates the LIKE portion of the query
    *
    * @return mixed
    */
    protected function get_filtering()
    {
      if($this->check_mDataprop())
        $mColArray = $this->get_mDataprop();
      elseif($this->ci->input->post('sColumns'))
        $mColArray = explode(',', $this->ci->input->post('sColumns'));
      else
        $mColArray = $this->columns;
      
      //print_r($mColArray);
      
      $sWhere = '';
      $sSearch = $this->ci->input->post('sSearch');
      $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
      $columns = array_values(array_diff($this->columns, $this->unset_columns));

      if($sSearch != '')
        for($i = 0; $i < count($mColArray); $i++)
          if($this->ci->input->post('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns))
            $sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";

      $sWhere = substr_replace($sWhere, '', -3);

      if($sWhere != '')
        $this->ci->db->where('(' . $sWhere . ')');
      
      
      for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
      {
        if(isset($_POST['sSearch_' . $i]) && $this->ci->input->post('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns))
        {
          $miSearch = explode(',', $this->ci->input->post('sSearch_' . $i));

          foreach($miSearch as $val)
          {
            if(preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches))
              $this->ci->db->where($this->select[$mColArray[$i]].' '.$matches[1], $matches[3]);
            else if(isset($_POST['sType_' . $i]) && $this->ci->input->post('sType_' . $i) == 'eq')
            {
              $this->ci->db->where($this->select[$mColArray[$i]],$val);
            }
            else if(isset($_POST['sType_' . $i]) && $this->ci->input->post('sType_' . $i) == 'group_concate')
            {
             
              $this->ci->db->having($this->select[$mColArray[$i]]." LIKE '%".$val."%'");
            }
            else if(isset($_POST['sType_' . $i]) && $this->ci->input->post('sType_' . $i) == 'between')
            {
                list($minvalue,$maxvalue) = explode(" - ",$val);
                $this->ci->db->where($this->select[$mColArray[$i]].' >=', $minvalue);
                $this->ci->db->where($this->select[$mColArray[$i]].' <=', $maxvalue);
          
            }
            else
              $this->ci->db->where($this->select[$mColArray[$i]].' LIKE', '%'.$val.'%');
          }
        }
      }
      
      foreach($this->filter as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);
    }

    /**
    * Compiles the select statement based on the other functions called and runs the query
    *
    * @return mixed
    */
    protected function get_display_result()
    {
      $data = $this->ci->db->get();
      return $data;
    }
    
    protected function get_total_results_found_rows()
    {
        $query = $this->ci->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $query;
    }        

    /**
    * Builds a JSON encoded string data
    *
    * @param string charset
    * @return string
    */
    protected function produce_output($charset)
    {
      $aaData = array();
      $rResult = $this->get_display_result();
      if($this->use_found_rows)
      $iFilteredTotal = $this->get_total_results_found_rows();
      else
      $iFilteredTotal = $this->get_total_results(true);
      
      $iTotal = $this->get_total_results();
      

      foreach($rResult->result_array() as $row_key => $row_val)
      {
        $aaData[$row_key] = ($this->check_mDataprop()) ? $row_val : array_values($row_val);
        //$aaData[$row_key] = $row_val;
        
        if($this->image_field_position)
        {
            foreach($this->image_field_position as $key=>$a_custom_string_field_position)
            {
                if(in_array($a_custom_string_field_position,array_keys($aaData[$row_key])))
                {
                    if(isset($aaData[$row_key][$a_custom_string_field_position]) && $aaData[$row_key][$a_custom_string_field_position])
                    $aaData[$row_key][$a_custom_string_field_position] = "<img src='".base_url().$aaData[$row_key][$a_custom_string_field_position]."' width='100' />";
                }
            }
                
        } 
        
        if($this->custom_string_field_position)
        {
            foreach($this->custom_string_field_position as $key=>$a_custom_string_field_position)
            {
                if(in_array($a_custom_string_field_position,array_keys($aaData[$row_key])))
                {
                    $aaData[$row_key][$a_custom_string_field_position] = $this->custom_string_array[$key][$aaData[$row_key][$a_custom_string_field_position]];
                }
            }
        } 
        
        if($this->date_string_field_position)
        {
            foreach($this->date_string_field_position as $key=>$a_date_string_field_position)
            {
                if(in_array($a_date_string_field_position,array_keys($aaData[$row_key])))
                {
                    $aaData[$row_key][$a_date_string_field_position] = date("Y-m-d",  strtotime($aaData[$row_key][$a_date_string_field_position]));
                }
            }
        }
        
        
        
        
        if(isset($row_val[$this->primary_key]))
        {
            
            $aaData[$row_key][] = str_replace("pk", $row_val[$this->primary_key], $this->generate_button($row_val));
            
        }    

        foreach($this->add_columns as $field => $val)
          if($this->check_mDataprop())
            $aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
          else
            $aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);

        foreach($this->edit_columns as $modkey => $modval)
          foreach($modval as $val)
            $aaData[$row_key][($this->check_mDataprop())? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);
            
        $aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_mDataprop())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));
        if(!$this->check_mDataprop())
          $aaData[$row_key] = array_values($aaData[$row_key]);
        
        foreach($aaData[$row_key] as $key=>$value)
        {
          if(!$value)
          {
             $aaData[$row_key][$key]="&nbsp;&nbsp;"; 
          }    
        }
      }
          

      $sColumns = array_diff($this->columns, $this->unset_columns);
      $sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));

      $sOutput = array
      (
        'sEcho'                => intval($this->ci->input->post('sEcho')),
        'iTotalRecords'        => $iTotal,
        'iTotalDisplayRecords' => $iFilteredTotal,
        'aaData'               => $aaData,
        'sColumns'             => implode(',', $sColumns)
      );

      if(strtolower($charset) == 'utf-8')
        return json_encode($sOutput, JSON_UNESCAPED_UNICODE| JSON_HEX_QUOT | JSON_HEX_TAG );
      else
        return $this->jsonify($sOutput);
    }

    /**
    * Get result count
    *
    * @return integer
    */
    protected function get_total_results($filtering = FALSE)
    {
      if($filtering)
        $this->get_filtering();

      foreach($this->joins as $val)
        $this->ci->db->join($val[0], $val[1], $val[2]);
      
      
      foreach($this->where as $val)
        $this->ci->db->where($val[0], $val[1], $val[2]);

      return $this->ci->db->count_all_results($this->table);
    }

    /**
    * Runs callback functions and makes replacements
    *
    * @param mixed $custom_val
    * @param mixed $row_data
    * @return string $custom_val['content']
    */
    protected function exec_replace($custom_val, $row_data)
    {
      $replace_string = '';

      if(isset($custom_val['replacement']) && is_array($custom_val['replacement']))
      {
        foreach($custom_val['replacement'] as $key => $val)
        {
          $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

          if(preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1]))
          {
            $func = $matches[1];
            $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            foreach($args as $args_key => $args_val)
            {
              $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
              $args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->check_mDataprop())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
            }

            $replace_string = call_user_func_array($func, $args);
          }
          elseif(in_array($sval, $this->columns))
            $replace_string = $row_data[($this->check_mDataprop())? $sval : array_search($sval, $this->columns)];
          else
            $replace_string = $sval;

          $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
        }
      }

      return $custom_val['content'];
    }

    /**
    * Check mDataprop
    *
    * @return bool
    */
    protected function check_mDataprop()
    {
      if(!$this->ci->input->post('mDataProp_0'))
        return FALSE;
      
      for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
        if(!is_numeric($this->ci->input->post('mDataProp_' . $i)))
          return TRUE;

      return FALSE;
    }

    /**
    * Get mDataprop order
    *
    * @return mixed
    */
    protected function get_mDataprop()
    {
      $mDataProp = array();

      for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
        $mDataProp[] = $this->ci->input->post('mDataProp_' . $i);

      return $mDataProp;
    }

    /**
    * Return the difference of open and close characters
    *
    * @param string $str
    * @param string $open
    * @param string $close
    * @return string $retval
    */
    protected function balanceChars($str, $open, $close)
    {
      $openCount = substr_count($str, $open);
      $closeCount = substr_count($str, $close);
      $retval = $openCount - $closeCount;
      return $retval;
    }

    /**
    * Explode, but ignore delimiter until closing characters are found
    *
    * @param string $delimiter
    * @param string $str
    * @param string $open
    * @param string $close
    * @return mixed $retval
    */
    protected function explode($delimiter, $str, $open = '(', $close=')')
    {
      $retval = array();
      $hold = array();
      $balance = 0;
      $parts = explode($delimiter, $str);

      foreach($parts as $part)
      {
        $hold[] = $part;
        $balance += $this->balanceChars($part, $open, $close);

        if($balance < 1)
        {
          $retval[] = implode($delimiter, $hold);
          $hold = array();
          $balance = 0;
        }
      }

      if(count($hold) > 0)
        $retval[] = implode($delimiter, $hold);

      return $retval;
    }

    /**
    * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
    *
    * @param mixed result
    * @return string
    */
    protected function jsonify($result = FALSE)
    {
      if(is_null($result))
        return 'null';

      if($result === FALSE)
        return 'false';

      if($result === TRUE)
        return 'true';

      if(is_scalar($result))
      {
        if(is_float($result))
          return floatval(str_replace(',', '.', strval($result)));

        if(is_string($result))
        {
          static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
          return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
        }
        else
          return $result;
      }

      $isList = TRUE;

      for($i = 0, reset($result); $i < count($result); $i++, next($result))
      {
        if(key($result) !== $i)
        {
          $isList = FALSE;
          break;
        }
      }

      $json = array();

      if($isList)
      {
        foreach($result as $value)
          $json[] = $this->jsonify($value);

        return '[' . join(',', $json) . ']';
      }
      else
      {
        foreach($result as $key => $value)
          $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);

        return '{' . join(',', $json) . '}';
      }
    }
  }
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */