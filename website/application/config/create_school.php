<?php

$config['create_school'] = array(
    'main_domain' => 'champs21.com',
    'default_import' => TRUE,
    'import' => 'default_class_seeds, default_emp_category_seeds, default_emp_dept_seeds, default_emp_grade_seeds, default_exam_grade_seeds',
);

$config['plus_api'] = array(
    'url' => "http://plus.champs21.com/",
);

$config['custom_urls'] = array(
    'activation' => "http://classtune.champs21.com/activation",
    'login' => "http://classtune.champs21.com/login",
);

$config['setup_forms'] = array(
    1 => 'shift',
//    2 => 'course',
    2 => 'subject',
    3 => 'employee_category',
    4 => 'employee_position',
    5 => 'employee_grade',
    6 => 'employee_department',
);

