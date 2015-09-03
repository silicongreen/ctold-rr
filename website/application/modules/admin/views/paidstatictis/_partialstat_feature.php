<div class="block">
                            <h2 class="section"><span class="loading-msg">Loading Data This will take some time...</span></h2>
                            <div class="CSSTableGenerator" >
                                <table   style="width: 100%;">

                                    <tr class="even">
                                        <td>User Type</td>
                                        <td>Homework</td>
                                        <td>Attendance/report</td>
                                        <td>Exam Reports</td>
                                        <td>Class Routines</td>
                                        <td>Exam Routines</td>
                                        <td>Events</td>
                                        <td>Leave</td>
                                        <td>Quiz</td>
                                        <td>lesson_plan</td>
                                        <td>Syllabus</td>
                                    </tr>
                                    <?php foreach ($user_type as $key=>$value): ?>
                                    <?php $index = $key-1; ?>
                                    <tr class="even">
                                        <td><?php echo $value; ?></td>
                                        <?php echo create_html_td($stat_homework,$index); ?>
                                        <?php echo create_html_td($stat_attendence,$index); ?>
                                        <?php echo create_html_td($stat_exam_reports,$index); ?>
                                        <?php echo create_html_td($stat_class_routines,$index); ?>
                                        <?php echo create_html_td($stat_exam_routines,$index); ?>
                                        <?php echo create_html_td($stat_events,$index); ?>
                                        <?php echo create_html_td($stat_leave,$index); ?> 
                                        <?php echo create_html_td($stat_quize,$index); ?> 
                                        <?php echo create_html_td($stat_lesson_plan,$index); ?>
                                        <?php echo create_html_td($stat_syllabus,$index); ?>
                                    </tr>  
                                    <?php endforeach; ?>

                                    

                                </table>
                            </div>     


                        </div>