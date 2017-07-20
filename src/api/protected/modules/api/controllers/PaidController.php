<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class PaidController extends Controller
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
                'actions' => array('usercheck','student','getbatch','parent','checkstudent','teacher','getteacherinfo','getcategoryposition'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    
    public function actionTeacher()
    {
        $first_name = Yii::app()->request->getPost('first_name');
        $last_name = Yii::app()->request->getPost('last_name');
        $email = Yii::app()->request->getPost('email');
        //$user_id = Yii::app()->request->getPost('user_id');
        $password = Yii::app()->request->getPost('password');
        $school_code = Yii::app()->request->getPost('school_code');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $date_of_birth = Yii::app()->request->getPost('date_of_birth');
        $joining_date = Yii::app()->request->getPost('joining_date');
        $gender = Yii::app()->request->getPost('gender');
        $contact_no = Yii::app()->request->getPost('contact_no');
        $photo = Yii::app()->request->getPost('photo');
        $employee_number = Yii::app()->request->getPost('employee_number');
        
        $job_title = Yii::app()->request->getPost('job_title');
        
        
        
        $employee_category_id = Yii::app()->request->getPost('employee_category_id');
        $employee_position_id = Yii::app()->request->getPost('employee_position_id');
        $employee_department_id = Yii::app()->request->getPost('employee_department_id');
        $employee_grade_id = Yii::app()->request->getPost('employee_grade_id');
        
        $batch_id = Yii::app()->request->getPost('batch_id');
        $response = array();
        if($joining_date && $job_title && $employee_category_id && $employee_position_id && $employee_department_id && $employee_number && $first_name && $last_name && $email && $password && $school_code  && $date_of_birth && $gender)
        {
            
            $file = array();
            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $file = $_FILES;
            }
            $school = new Schools();
            if($selected_school = $school->getschoolbycode($school_code))
            {
                if($selected_school->id < 10 )
                {
                    $user_id = "0".$selected_school->id."-".$employee_number;
                }
                else
                {
                    $user_id = $selected_school->id."-".$employee_number;
                }    
               
                $user = new Users();
                if($user->checkUserExists($user_id))
                {
                    
                    $free_user_id = $this->createfreeuser($first_name,$last_name,$email,$user_id,$gender,$password,$date_of_birth,$contact_no,$selected_school,$file);
                    if($free_user_id)
                    {
                        $paid_user_id = $this->createPaidUser($first_name,$last_name,$user_id,$password,$selected_school,$email,3);
                        if($paid_user_id)
                        {
                            $emp_id = $this->createTeacher($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,
                                    $employee_number,$job_title,$employee_category_id,$employee_position_id,$employee_department_id,$joining_date,$employee_grade_id);
                            if($emp_id)
                            {
                                $this->updateFreeUser($paid_user_id,$user_id,$password,$selected_school,$free_user_id);
                                if($batch_id)
                                {
                                    $embatch = new BatchTutors();
                                    $embatch->batch_id = $batch_id;
                                    $embatch->employee_id = $emp_id;
                                    $embatch->save();
                                }
                                
                                
                                //making data for send mail
                                $data['teacher']['fulname'] = $first_name." ".$last_name;
                                $data['teacher']['username'] = $user_id;
                                $data['teacher']['admission_no'] = $employee_number;
                                $data['email'] = $email;
                                $data['email_name'] = $first_name." ".$last_name;
                                //making data for sending mail
                                
                                Settings::sendCurlMail($data);
                                
                                $response = $this->create_return_value($free_user_id,$user_id,$password);
                            }
                            else 
                            {
                                $this->delete_user($free_user_id,$paid_user_id);
                                $response = $this->create_return_value();
                            }                                
                        }
                        else 
                        {
                            $this->delete_user($free_user_id);
                            $response = $this->create_return_value();
                        }
                    }
                        
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "Employee Number already used";
                }
            }
        }
        
        if(!$response)
        {
            $response = $this->create_return_value();
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionGetCategoryPosition()
    {
        $school_code = Yii::app()->request->getPost('school_code');
        $category_id = Yii::app()->request->getPost('category_id');
        if($school_code && $category_id)
        {
            $school = new Schools();
            $selected_school = $school->getschoolbycode($school_code);
            if($selected_school)
            {
                $position = new EmployeePositions();
                $all_position = $position->getForSchool($selected_school->id,$category_id);
                if($all_position)
                {
                    $response['data']['position'] = $all_position;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "SCHOOL DONT CREATE EMPLOYEE POSITION FOR THIS YET";
                }    
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            } 
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    public function actionGetTeacherInfo()
    {
        $school_code = Yii::app()->request->getPost('school_code');
        if($school_code)
        {
            $school = new Schools();
            $selected_school = $school->getschoolbycode($school_code);
            if($selected_school)
            {
                $category = new EmployeeCategories();
                $all_categories = $category->getForSchool($selected_school->id);
                
                $departments = new EmployeeDepartments();
                $all_departments = $departments->getForSchool($selected_school->id);
                
                $grades = new EmployeeGrades();
                $all_grades = $grades->getForSchool($selected_school->id);
                if($all_categories && $all_grades && $all_departments)
                {
                    $response['data']['grades'] = $all_grades;
                    $response['data']['departments'] = $all_departments;
                    $response['data']['categories'] = $all_categories;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "SCHOOL DONT CREATE EMPLOYEE NECESSARY INFORMATION YET";
                }    
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            } 
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    
    
    public function actionGetBatch()
    {
        $school_code = Yii::app()->request->getPost('school_code');
        if($school_code)
        {
            $school = new Schools();
            $selected_school = $school->getschoolbycode($school_code);
            if($selected_school)
            {
                $batch = new Batches();
                $all_batches = $batch->getSchoolBatches($selected_school->id);
                if($all_batches)
                {
                    $response['data']['batches'] = $all_batches;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "SCHOOL DONT CREATE ANY CLASS YET";
                }    
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            } 
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    
    
    
    private function createParent($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,$std_data,$user_id)
    {
        
        $guardian = new Guardians();
        $guardian->ward_id = $std_data[0]['student_id'];
        $guardian->relation = $std_data[0]['relation'];
        $guardian->first_name = $first_name;
        $guardian->last_name = $last_name;
//        $student->gender = ($gender==1)?"m":"f";
//        $guardian->batch_id = $batch_id;
        $guardian->country_id = 14;
        $guardian->school_id = $selected_school->id;
        $guardian->created_at = date("Y-m-d H:i:s");
        $guardian->updated_at = date("Y-m-d H:i:s");
        $guardian->user_id = $paid_user_id;
        $guardian->email = $email;
        
        $guardian->mobile_phone = $contact_no;
        
        $guardian->dob = $date_of_birth;
       
        if($guardian->save())
        {
            foreach($std_data as $value)
            {
                $studentobj = new Students();
                $std = $studentobj->findByPk($value['student_id']);
                if(!$std->immediate_contact_id)
                {
                    $std->immediate_contact_id = $guardian->id;
                    $std->save();
                }
                $stdgu = new GuardianStudent();
                if(!$stdgu->data_exists($value['student_id'],$guardian->id))
                {
                    $stdgu->student_id = $value['student_id'];
                    $stdgu->guardian_id = $guardian->id;
                    
                    $stdgu->save();
                }
            }    
            return $guardian->id;
        }
        else
        {
            return FALSE;
        } 
                
        
        
    } 
    
    private function createTeacher($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,
                                        $employee_number,$job_title,$employee_category_id,$employee_position_id,$employee_department_id,$joining_date,$employee_grade_id="")
    {
        
        $employee = new Employees();
        $employee->employee_number = $employee_number;
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->gender = ($gender==1)?"m":"f";
        $employee->nationality_id = 14;
        $employee->school_id = $selected_school->id;
        $employee->joining_date = $joining_date;
        
        $employee->job_title = $job_title;
        $employee->employee_category_id = $employee_category_id;
        $employee->employee_position_id = $employee_position_id;
        $employee->employee_department_id = $employee_department_id;
        if($employee_grade_id)
        {
            $employee->employee_grade_id = $employee_grade_id;
        }
        
        
        $employee->created_at = date("Y-m-d H:i:s");
        $employee->updated_at = date("Y-m-d H:i:s");
        $employee->user_id = $paid_user_id;
        $employee->email = $email;
        
        $employee->mobile_phone = $contact_no;
        
        $employee->date_of_birth = $date_of_birth;
       
        if($employee->save())
        {
            return $employee->id;
        }
        else
        {
            return FALSE;
        } 
                
        
        
    } 
    
    private function createStudent($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,$batch_id,$user_id)
    {
        
        $student = new Students();
        $student->admission_no = $user_id;
        $student->first_name = $first_name;
        $student->last_name = $last_name;
        $student->gender = ($gender==1)?"m":"f";
        $student->batch_id = $batch_id;
        $student->country_id = 14;
        $student->school_id = $selected_school->id;
        $student->admission_date = date("Y-m-d");
        $student->created_at = date("Y-m-d H:i:s");
        $student->updated_at = date("Y-m-d H:i:s");
        $student->user_id = $paid_user_id;
        $student->email = $email;
        
        $student->phone1 = $contact_no;
        
        $student->date_of_birth = $date_of_birth;
       
        if($student->save())
        {
            $student->sibling_id = $student->id;
            $student->save();
            return $student->id;
        }
        else
        {
            return FALSE;
        } 
                
        
        
    } 
    
    public function actionCheckStudent()
    {
        $school_code = Yii::app()->request->getPost('school_code');
        $student_id = Yii::app()->request->getPost('student_id');
        if($school_code)
        {
            $school = new Schools();
            $selected_school = $school->getschoolbycode($school_code);
            if($selected_school)
            {
                $user = new Users();
                if($student = $user->checkStudentExists($student_id,$selected_school->id))
                {
                    $middle_name = (!empty($student['studentDetails']->middle_name)) ? $student['studentDetails']->middle_name . ' ' : '';
                    $response['data']['full_name'] = rtrim($student['studentDetails']->first_name . ' ' . $middle_name . $student['studentDetails']->last_name);
                    $response['data']['std_id'] = $student->id;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "Student not exists";
                }    
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            } 
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
        
    }
    
    public function actionparent()
    {
        $first_name = Yii::app()->request->getPost('first_name');
        $last_name = Yii::app()->request->getPost('last_name');
        $email = Yii::app()->request->getPost('email');
        $user_id = Yii::app()->request->getPost('user_id');
        $password = Yii::app()->request->getPost('password');
        $school_code = Yii::app()->request->getPost('school_code');
        $date_of_birth = Yii::app()->request->getPost('date_of_birth');
        $gender = Yii::app()->request->getPost('gender');
        $childrens = Yii::app()->request->getPost('childrens');
        $contact_no = Yii::app()->request->getPost('contact_no');
        $response = array();
        if($first_name && $last_name && $email && $user_id && $password && $school_code && $date_of_birth && $gender && $childrens)
        {
            $file = array();
            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $file = $_FILES;
            }
            $school = new Schools();
            if($selected_school = $school->getschoolbycode($school_code))
            {
                $user = new Users();
                if($user->checkUserExists($user_id))
                {
                    $all_student_exits = true;
                    $user = new Users();
                    
                    $students = explode("|", $childrens);
                    
                    $std_data = array();
                    foreach($students as $key=>$value)
                    {
                        $std_ids = explode(",", $value);
                        $user = new Users();
                        
                        if(!$user->checkStudentExists($std_ids[0],$selected_school->id) || count($std_ids)<2  )
                        {
                           
                            $all_student_exits = false;
                        }
                        else 
                        {
                         
                            $user_data = $user->checkStudentExists($std_ids[0],$selected_school->id);
                            
                            $std_obj = new Students();
                            $students_id = $std_obj->getStudentByUserId($user_data->id);
                            if(!$students_id)
                            {
                                $all_student_exits = false;
                            }
                            else
                            {
                                $std_data[$key]['student_id'] = $students_id->id;
                                $std_data[$key]['relation'] = $std_ids[1];
                            }    
                        }
                        
                    }    
                    
                    
                    if($all_student_exits)
                    {
                        $free_user_id = $this->createfreeuser($first_name,$last_name,$email,$user_id,$gender,$password,$date_of_birth,$contact_no,$selected_school,$file);
                        if($free_user_id)
                        {
                            $paid_user_id = $this->createPaidUser($first_name,$last_name,$user_id,$password,$selected_school,$email,4);
                            if($paid_user_id)
                            {
                                
                                
                                $parent_id = $this->createParent($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,$std_data,$user_id);
                                if($parent_id)
                                {
                                    $this->updateFreeUser($paid_user_id,$user_id,$password,$selected_school,$free_user_id);
                                    
                                    //making data for sending mail
                                    $gstd = new GuardianStudent();
                                    $students = $gstd->getChildren($parent_id);
                                    
                                    if($students)
                                    {
                                        foreach($students as $key=>$value)
                                        {
                                            $data['students'][$key]['fulname'] = $value['students']->first_name." ".$value['students']->last_name;
                                            $data['students'][$key]['username'] = "";
                                            $usersmodel = new Users();
                                            $udata = $usersmodel->findByPk($value['students']->user_id);
                                            if($udata)
                                            {
                                                $data['students'][$key]['username'] = $udata->username;
                                            }
                                            $data['students'][$key]['admission_no'] = $value['students']->admission_no; 
                                        }    
                                    }
                                    
                                    
                                    
                                    $data['guardian']['fulname'] = $first_name." ".$last_name;
                                    $data['guardian']['username'] = $user_id;
                                    $data['email'] = $email;
                                    $data['email_name'] = $first_name." ".$last_name;
                                    // end sending mail
                                    
                                    Settings::sendCurlMail($data);
                                    
                                    
                                    $response = $this->create_return_value($free_user_id,$user_id,$password);
                                }
                                else 
                                {
                                    $this->delete_user($free_user_id,$paid_user_id);
                                    $response = $this->create_return_value();
                                }                                
                            }
                            else 
                            {
                                $this->delete_user($free_user_id);
                                $response = $this->create_return_value();
                            }
                        }
                    } 
                    else
                    {
                        $response['status']['code'] = 402;
                        $response['status']['msg'] = "Invalid Student Id";
                    }    
                } 
                else 
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "username already exists";
                }
            }
            
        }
        
        if (!$response) {
            $response = $this->create_return_value();
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
         
           
    public function actionstudent()
    {
        $first_name = Yii::app()->request->getPost('first_name');
        $last_name = Yii::app()->request->getPost('last_name');
        $email = Yii::app()->request->getPost('email');
        //$user_id = Yii::app()->request->getPost('user_id');
        $password = Yii::app()->request->getPost('password');
        $school_code = Yii::app()->request->getPost('school_code');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $date_of_birth = Yii::app()->request->getPost('date_of_birth');
        $gender = Yii::app()->request->getPost('gender');
        $contact_no = Yii::app()->request->getPost('contact_no');
        $admission = Yii::app()->request->getPost('admission');
        $response = array();
        if($admission && $first_name && $last_name && $email && $password && $school_code && $batch_id && $date_of_birth && $gender)
        {
            $file = array();
            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $file = $_FILES;
            }
            $school = new Schools();
            if($selected_school = $school->getschoolbycode($school_code))
            {
                if($selected_school->id < 10 )
                {
                    $user_id = "0".$selected_school->id."-".$admission;
                }
                else
                {
                    $user_id = $selected_school->id."-".$admission;
                }    
               
                $user = new Users();
                if($user->checkUserExists($user_id))
                {
                    $batch = new Batches();
                    if($batch->checkSchoolBatch($selected_school->id,$batch_id))
                    {
                        $free_user_id = $this->createfreeuser($first_name,$last_name,$email,$user_id,$gender,$password,$date_of_birth,$contact_no,$selected_school,$file);
                        if($free_user_id)
                        {
                            $paid_user_id = $this->createPaidUser($first_name,$last_name,$user_id,$password,$selected_school,$email);
                            if($paid_user_id)
                            {
                                $student_id = $this->createStudent($first_name,$last_name,$email,$gender,$date_of_birth,$contact_no,$selected_school,$paid_user_id,$batch_id,$admission);
                                if($student_id)
                                {
                                    $this->updateFreeUser($paid_user_id,$user_id,$password,$selected_school,$free_user_id);
                                    
                                    //create data for sending mail
                                    $data['student']['fulname'] = $first_name." ".$last_name;
                                    $data['student']['username'] = $user_id;
                                    $data['student']['admission_no'] = $admission;
                                    $data['email'] = $email;
                                    $data['email_name'] = $first_name." ".$last_name;
                                    
                                    //create data for sending mail
                                    
                                    Settings::sendCurlMail($data);
                                    
                                    
                                    $response = $this->create_return_value($free_user_id,$user_id,$password);
                                }
                                else 
                                {
                                    $this->delete_user($free_user_id,$paid_user_id);
                                    $response = $this->create_return_value();
                                }                                
                            }
                            else 
                            {
                                $this->delete_user($free_user_id);
                                $response = $this->create_return_value();
                            }
                        }
                    }    
                }
                else
                {
                    $response['status']['code'] = 401;
                    $response['status']['msg'] = "Student admission no exists";
                }
            }
        }
        
        if(!$response)
        {
            $response = $this->create_return_value();
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionuserCheck()
    {
       
         $first_name = Yii::app()->request->getPost('first_name');
         $last_name = Yii::app()->request->getPost('last_name');
         $email = Yii::app()->request->getPost('email');
         //$user_id = Yii::app()->request->getPost('user_id');
         $password = Yii::app()->request->getPost('password');
         $school_code = Yii::app()->request->getPost('school_code');
         //&& $user_id
         if($first_name && $last_name && $email  && $password && $school_code)
         {
              $school = new Schools();
              $selected_school = $school->getschoolbycode($school_code);
              if($selected_school)
              {
//                  $user = new Users();
//                  if($user->checkUserExists($user_id))
//                  {
                      $response['data']['school_id'] = $selected_school->id;
                      $response['status']['code'] = 200;
                      $response['status']['msg'] = "Success";
//                  }
//                  else
//                  {
//                     $response['status']['code'] = 402;
//                     $response['status']['msg'] = "User Id Exists";
//                  }    
              }
              else
              {
                 $response['status']['code'] = 401;
                 $response['status']['msg'] = "BAD SCHOOL CODE";
              }    
         }
         else
         {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
         }
         
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    
    
    
    //GLOBAL PRIVATE FUNCTION
    
    private function encrypt($field, $salt)
    {
        return hash('sha512', $salt . $field);
    }
    private function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function delete_user( $free_user_id=0, $paid_user_id=0 )
    {
        if($free_user_id)
        {
            $freeuserObj = new Freeusers();
            $freeuser_data = $freeuserObj->findByPk($free_user_id);
            $freeuser_data->delete();
        }
        if($paid_user_id)
        {
            $user = new Users();
            $user_data = $user->findByPk($paid_user_id);
            $user_data->delete();
        }
    }         
    private function create_return_value($free_user_id=0,$username="",$password="",$type=1)
    {
        if($free_user_id)
        {
            $user = new Users;
            $user->username = $username;
            $user->hashed_password = $password;
            $user->login();
            
            $freeuserObj = new Freeusers();
            $freeuser_data = $freeuserObj->findByPk($free_user_id);
            $response['data'] = $freeuser_data->getPaidUserInfo($freeuser_data);
            $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freeuser_data);
            $response['data']['free_id'] = $freeuser_data->id;
            $response['data']['user'] = $freeuser_data->getUserInfo($freeuser_data->id,$response['data']['paid_user']['school_id'],$type);
            $response['data']['is_register'] = true;
            $response['data']['is_login'] = true;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }
        else 
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        
        return $response;
    }   
    private function updateFreeUser($paid_id,$paid_username,$paid_password,$selected_school,$free_user_id)
    {
        $freeuserObj = new Freeusers();
        $free_data = $freeuserObj->findByPk($free_user_id);
        
        $free_data->paid_id = $paid_id;
        $free_data->paid_username = $paid_username;
        $free_data->paid_password = $paid_password;
        
        $free_data->paid_school_id = $selected_school->id;
        $free_data->paid_school_code = $selected_school->code;
        $free_data->save();
        
    }
    private function createfreeuser($first_name,$last_name,$email,$user_id,$gender,$password,$date_of_birth,$contact_no,$selected_school,$file=array())
    {
        $freeuserObj = new Freeusers();
        $freeuserObj->salt = md5(uniqid(rand(), true));
        $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
        $freeuserObj->email = $user_id;
        $freeuserObj->user_type = 1;
        $freeuserObj->first_name = $first_name;
        $freeuserObj->last_name = $last_name;
        
        $freeuserObj->gender = $gender;
        $freeuserObj->nick_name = 1;
        $freeuserObj->tds_country_id = 14;
        $freeuserObj->mobile_no = $contact_no;
        $freeuserObj->dob = $date_of_birth;
        $freeuserObj->school_name = $selected_school->name;
        $freeuserObj->is_joined_spellbee = 1;
        
        if (isset($file['profile_image']['name']) && !empty($file['profile_image']['name']))
        {
            $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
            $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
            $tmp_name = $file["profile_image"]["tmp_name"];
            $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $file["profile_image"]["name"]);

            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            $freeuserObj->profile_image = $main_dir . $name;
        }
        
        
        
        if($freeuserObj->save())
        {
            return $freeuserObj->id;
        }
        else
        {
            return FALSE;
        }    
    }  
    private function createPaidUser($first_name,$last_name,$user_id,$password,$selected_school,$email='',$user_type=2)
    {
        $user = new Users();
        $user->username = $user_id;
        $user->salt = $this->generateRandomString();
        $user->hashed_password = sha1($user->salt .$password);
        if($user_type==2)
        {
            $user->student = 1;
        }
        if($user_type==3)
        {
            $user->employee = 1;
        }
        if($user_type==4)
        {
            $user->parent = 1;
        }
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        
        $user->school_id = $selected_school->id;
        $user->email = $email;
        $user->created_at = date("Y-m-d H:i:s");
        
        $user->updated_at = date("Y-m-d H:i:s");
        if($user->save())
        {
            return $user->id;
        }
        else
        {
            return FALSE;
        } 
                
        
        
    } 
    
    
}

