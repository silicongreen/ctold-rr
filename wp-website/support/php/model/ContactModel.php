<?php

class ContactModel extends Model
{
    // Static methods
    
    public function __construct($data = null, $rawData = null)
    {
        parent::__construct($data, $rawData);
    }
    public static function repo()
    {
        return new ContactModel;
    }
    
    
    
    public function getTableName()
    {
        return 'mirrormx_customer_contact';
    }
    
    public function getColumns()
    {
        return array('name', 'email', 'school','start_time','end_time','question','user_info');
    }
    public function preSave()
    {
        $result = parent::preSave();
        return $result;
    }
    
   
}

?>
