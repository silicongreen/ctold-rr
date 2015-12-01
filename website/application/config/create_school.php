<?php

$config['create_school'] = array(
    'main_domain' => 'classtune.com',
    'default_import' => TRUE,
    'mode' => 'live', // dev and live (dev will not send email or create subdomain)
    'import' => 'default_class_seeds, default_emp_category_seeds, default_emp_dept_seeds, default_emp_grade_seeds, default_exam_grade_seeds',
);

$config['plus_api'] = array(
    'url' => "http://plus.classtune.com/",
);

$config['custom_urls'] = array(
    'activation' => "http://classtune.classtune.com/activation",
    'login' => "http://classtune.classtune.com/login",
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

