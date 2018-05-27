<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class SyncController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('campus','department','designation','employee'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionEmployee()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $insert['employee_number'] = explode("||",Yii::app()->request->getPost('employee_number'));
        $insert['campus'] = explode("||",Yii::app()->request->getPost('campus'));
        $insert['first_name'] = explode("||",Yii::app()->request->getPost('first_name'));
        $insert['employee_position_id'] = explode("||",Yii::app()->request->getPost('employee_position_id'));
        $insert['employee_department_id'] = explode("||",Yii::app()->request->getPost('employee_department_id'));
        $insert['grade'] = explode("||",Yii::app()->request->getPost('grade'));
        $insert['section_name'] = explode("||",Yii::app()->request->getPost('section_name'));
        $insert['emp_status_to'] = explode("||",Yii::app()->request->getPost('emp_status_to'));
        $insert['shift'] = explode("||",Yii::app()->request->getPost('shift'));
        $insert['joining_date'] = explode("||",Yii::app()->request->getPost('joining_date'));
        $insert['blood_group'] = explode("||",Yii::app()->request->getPost('blood_group'));
        $insert['date_of_birth'] = explode("||",Yii::app()->request->getPost('date_of_birth'));
        $insert['nationality_id'] = explode("||",Yii::app()->request->getPost('nationality_id'));
        $insert['father_name'] = explode("||",Yii::app()->request->getPost('father_name'));
        $insert['mother_name'] = explode("||",Yii::app()->request->getPost('mother_name'));
        
        $insert['home_address_line1'] = explode("||",Yii::app()->request->getPost('home_address_line1'));
        $insert['home_address_line2'] = explode("||",Yii::app()->request->getPost('home_address_line2'));
        $insert['home_city'] = explode("||",Yii::app()->request->getPost('home_city'));
        $insert['home_pin_code'] = explode("||",Yii::app()->request->getPost('home_pin_code'));
        $insert['office_address_line1'] = explode("||",Yii::app()->request->getPost('office_address_line1'));
        $insert['office_address_line2'] = explode("||",Yii::app()->request->getPost('office_address_line2'));
        $insert['office_city'] = explode("||",Yii::app()->request->getPost('office_city'));
        
        $insert['office_pin_code'] = explode("||",Yii::app()->request->getPost('office_pin_code'));
        $insert['mobile_phone'] = explode("||",Yii::app()->request->getPost('mobile_phone'));
        $insert['home_phone'] = explode("||",Yii::app()->request->getPost('home_phone'));
        $insert['email'] = explode("||",Yii::app()->request->getPost('email'));
        
        
        $insert['reference'] = explode("||",Yii::app()->request->getPost('reference'));
        $insert['education'] = explode("||",Yii::app()->request->getPost('education'));
        $insert['salary'] = explode("||",Yii::app()->request->getPost('salary'));
        
        $empObj = new Employees();
        foreach($insert['employee_number'] as $key=>$value)
        {
            
            $data = $empObj->getEmpByAdmission($school_id, $value);
            if($data)
            {
                $empObjNew = $empObj->findByPk($data->id);
                $empObjNew->updated_at = date("Y-m-d H:i:s");
            }
            else 
            {
                $empObjNew = new Employees();
                $empObjNew->created_at = date("Y-m-d H:i:s");
                $empObjNew->updated_at = date("Y-m-d H:i:s");
            }
            foreach($insert as $tname=>$empvalue)
            {
                if($tname == "reference" || $tname == "education" || $tname=="salary")
                {
                    continue;
                }        
                if($tname == "employee_position_id")
                {
                    $posObj = new EmployeePositions();
                    $pos = $posObj->getPositionByName($empvalue[$key],$school_id);
                    if($pos)
                    {
                        $empvalue[$key] = $pos->id;
                        $empObjNew->employee_category_id = $pos->employee_category_id;
                        
                    }
                } 
                if($tname == "employee_department_id")
                {
                    $Obj = new EmployeeDepartments();
                    $data = $Obj->getByName($empvalue[$key],$school_id);
                    if($data)
                    {
                        $empvalue[$key] = $data->id;
                        
                    }
                } 
                if($tname == "grade")
                {
                    $Obj = new EmployeeGrades();
                    $data = $Obj->getByName($empvalue[$key],$school_id);
                    if($data)
                    {
                        $empvalue[$key] = $data->id;
                        
                    }
                } 
                if($tname == "nationality_id")
                {
                    $empvalue[$key] = 14;
                } 
                $empObjNew->$tname = $empvalue[$key];
            } 
            $empObjNew->save();
            foreach($insert as $tname=>$empvalue)
            {
                if($tname == "reference" || $tname == "education" || $tname=="salary")
                {
                   
                    if($tname == "reference")
                    {
                        EmployeeReferences::model()->deleteAll(
                                "`employee_id` = (:employee_id)", array(':employee_id' => $empObjNew->id)
                        );
                        $emp_ref_obj = new EmployeeReferences();
                        $all_emp_ref = explode("----", $empvalue[$key]);
                        if($all_emp_ref)
                        {
                            foreach($all_emp_ref as $emp_ref_value)
                            {
                                $ref_array = $all_emp_ref = explode("--", $emp_ref_value);
                                $emp_ref_obj->employee_id = $empObjNew->id;
                                $emp_ref_obj->name = $ref_array[0];
                                $emp_ref_obj->mailing_address = $ref_array[1];
                                $emp_ref_obj->land_phone = $ref_array[2];
                                $emp_ref_obj->mobile = $ref_array[3];
                                $emp_ref_obj->email = $ref_array[4];
                                $emp_ref_obj->save();
                            }    
                        }
                        
                    }
                    if($tname == "education")
                    {
                        EmployeeEducations::model()->deleteAll(
                                "`employee_id` = (:employee_id)", array(':employee_id' => $empObjNew->id)
                        );
                        $emp_edu_obj = new EmployeeEducations();
                        $all_edu = explode("----", $empvalue[$key]);
                        if($all_edu)
                        {
                            foreach($all_edu as $emp_edu_value)
                            {
                                $edu_array = explode("--", $emp_edu_value);
                                $emp_edu_obj->employee_id = $empObjNew->id;
                                $emp_edu_obj->degree = $edu_array[0];
                                $emp_edu_obj->year = $edu_array[1];
                                $emp_edu_obj->insttute = $edu_array[2];
                                $emp_edu_obj->result = $edu_array[3];
                                $emp_edu_obj->save();
                            }    
                        }
                    }
                    if($tname=="salary")
                    {
                       
                        EmployeeSalaryStructures::model()->deleteAll(
                                "`employee_id` = (:employee_id)", array(':employee_id' => $empObjNew->id)
                        );
                        $all_sal = explode("----", $empvalue[$key]);
                        $pay_roll_array = array("Basic","House Rent","Medical","Transport","Holiday","Tiffin");
                        $payrollcatObj = new PayrollCategories();
                        $all_pay_cat = $payrollcatObj->getPayRollCategory($school_id);
                        if($all_pay_cat)
                        foreach($all_pay_cat as $value)
                        {
                            if(in_array($value->name, $pay_roll_array))
                            {
                                $key = array_search($value->name, $pay_roll_array);
                                if(isset($all_sal[$key]) && $all_sal[$key] > 0)
                                {
                                    $emSalStObj = new EmployeeSalaryStructures();
                                    $emSalStObj->employee_id = $empObjNew->id;
                                    $emSalStObj->payroll_category_id = $value->id;
                                    $emSalStObj->amount = $all_sal[$key];
                                    $emSalStObj->school_id = $school_id;
                                    $emSalStObj->created_at = date("Y-m-d H:i:s");
                                    $emSalStObj->updated_at = date("Y-m-d H:i:s");
                                    $emSalStObj->save();
                                    
                                }    
                            }
                        }    
                       
                       
                    }
                }  
            }
        }
        $response['data'] = $insert;
        $response['success'] = true;
        $response['msg'] = "SAVED";
        echo CJSON::encode($response);
        Yii::app()->end();
        
        
    }
    
    public function actionDesignation()
    {
        $cat_array = array(1=>844,2=>856,3=>850,4=>863,5=>857);
        $names = explode("||",Yii::app()->request->getPost('deg_string'));
        $postion_strings = explode("||",Yii::app()->request->getPost('postion_strings'));
        $ids = explode("||",Yii::app()->request->getPost('id_string'));
        $school_id = Yii::app()->request->getPost('school_id');
        $posObj = new EmployeePositions();
        foreach($names as $key=>$value)
        {
            $sync_id = $ids[$key];
            $employee_category_id = 857;
            if(isset($cat_array[$postion_strings[$key]]))
            {
                 $employee_category_id = $cat_array[$postion_strings[$key]];
            }
            $data = $posObj->getPositionBySyncId($sync_id,$value, $school_id);
            if($data)
            {
                $pos = $posObj->findByPk($data->id);
                $pos->name = $value;
                $pos->sync_id = $sync_id;
                $pos->employee_category_id = $employee_category_id;
                $pos->updated_at = date("Y-m-d H:i:s");
                $pos->save();
            }   
            else
            {
                
                $pos = new EmployeePositions();
                $pos->name = $value;
                $pos->status = 1;
                $pos->employee_category_id = $employee_category_id;
                $pos->created_at = date("Y-m-d H:i:s");
                $pos->updated_at = date("Y-m-d H:i:s");
                
                $pos->school_id = $school_id;
                $pos->sync_id = $sync_id;
                $pos->save();
            }    
        }    
        
        
        $response['success'] = true;
        $response['msg'] = "SAVED";
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    public function actionDepartment()
    {
        $names = explode("||",Yii::app()->request->getPost('dep_string'));
        $ids = explode("||",Yii::app()->request->getPost('id_string'));
        $school_id = Yii::app()->request->getPost('school_id');
        $is_section = Yii::app()->request->getPost('is_section');
        $depObj = new EmployeeDepartments();
        foreach($names as $key=>$value)
        {
            $sync_id = $ids[$key];
            $data = $depObj->getDepartmentBySyncId($sync_id,$value, $school_id);
            if($data)
            {
                $dep = $depObj->findByPk($data->id);
                $dep->name = $value;
                $dep->sync_id = $sync_id;
                $dep->updated_at = date("Y-m-d H:i:s");
                $dep->save();
            }   
            else
            {
                
                $dep = new EmployeeDepartments();
                $dep->name = $value;
                $dep->status = 1;
                $dep->created_at = date("Y-m-d H:i:s");
                $dep->updated_at = date("Y-m-d H:i:s");
                if($is_section)
                {
                    $dep->is_section = 1;
                }
                $dep->school_id = $school_id;
                $dep->sync_id = $sync_id;
                $dep->save();
            }    
        }    
        
        
        $response['success'] = true;
        $response['msg'] = "SAVED";
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    public function actionCampus()
    {
        $campus_names = explode("||",Yii::app()->request->getPost('campus_string'));
        $ids = explode("||",Yii::app()->request->getPost('id_string'));
        $school_id = Yii::app()->request->getPost('school_id');
        $campusObj = new Campus();
        foreach($campus_names as $key=>$value)
        {
            $sync_id = $ids[$key];
            $campus_data = $campusObj->getCampusBySyncId($sync_id,$value, $school_id);
            if($campus_data)
            {
                $campus = $campusObj->findByPk($campus_data->id);
                $campus->name = $value;
                $campus->sync_id = $sync_id;
                $campus->updated_at = date("Y-m-d H:i:s");
                $campus->save();
            }   
            else
            {
                
                $campus = new Campus();
                $campus->name = $value;
                $campus->created_at = date("Y-m-d H:i:s");
                $campus->updated_at = date("Y-m-d H:i:s");
                $campus->school_id = $school_id;
                $campus->sync_id = $sync_id;
                $campus->save();
            }    
        }    
        
        
        $response['success'] = true;
        $response['msg'] = "SAVED";
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    
   
}

