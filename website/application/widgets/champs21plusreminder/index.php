<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Widget Plugin 
 * 
 * Install this file as application/plugins/widget_pi.php
 * 
 * @version:     0.1
 * $copyright     Copyright (c) Wiredesignz 2009-03-24
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class champs21plusreminder extends widget {

    function run() {
        $reminder = plus_api2();

        $parsed_data = array();
        $cars = array('homework' => "New Homework",
            'event' => "New Event",
            'fee' => "Fees submission date",
            'result' => "Result Published",
            'exam' => "Exam Scheduled"
        );
        $reminder = json_decode(json_encode($reminder), 1);
        
        $ar_data = array('homework' => array(),
            'event' => array(),
            'fee' => array(),
            'result' => array(),
            'exam' => array(),
            'others' => array()
        );

        $i = 1;
        foreach ($reminder['reminder'] as $employee) {
            foreach ($cars as $k => $head) {
                if (strpos($employee['subject'], $head) !== false) {
                    $ar_data[$k][] = $employee;
                }
            }

            $i++;
        }

        $data['data'] = $ar_data;

        //echo "<pre>";
        //print_r($res_me);
        $this->render($data);
    }

}
