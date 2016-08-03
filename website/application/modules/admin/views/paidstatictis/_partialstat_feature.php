<div class="block">
                            <h2 class="section"><span class="loading-msg">Loading Data This will take some time...</span></h2>
                            <div class="CSSTableGenerator" >
                                <table   style="width: 100%;">

                                    <tr class="even">
                                        <td>User Type</td>
                                        <td>Homework</td>
                                        <td>Attendance/report</td>
                                        <td>Exam</td>
                                        <td>Class Routines</td>
                                        <td>Events</td>
                                        <td>Notice</td>
                                        <td>Leave</td>
                                        <td>Quiz</td>
                                        <td>lesson_plan</td>
                                        <td>Syllabus</td>
                                        <td>Meetings</td>
                                    </tr>
                                    
                                    <?php foreach ($user_type as $key=>$value): ?>
                                    <?php $index = $key; ?>
                                    
                                    <tr class="even">
                                        <td><?php echo $value; ?></td>
                                        <?php echo create_html_td($stat_homework,$index); ?>
                                        <?php echo create_html_td($stat_attendence,$index,"at"); ?>
                                        <?php echo create_html_td($stat_exams,$index,'ex'); ?>
                                        <?php echo create_html_td($stat_class_routines,$index,'cr'); ?>
                                        <?php echo create_html_td($stat_events,$index,'ev'); ?>
                                        <?php echo create_html_td($stat_notice,$index,'no'); ?>
                                        <?php echo create_html_td($stat_leave,$index,'le'); ?> 
                                        <?php echo create_html_td($stat_quize,$index,'qu'); ?> 
                                        <?php echo create_html_td($stat_lesson_plan,$index,'lp'); ?>
                                        <?php echo create_html_td($stat_syllabus,$index,'sy'); ?>
                                        <?php echo create_html_td($stat_mettings,$index,'me'); ?>
                                    </tr>  
                                    <?php endforeach; ?>

                                    

                                </table>
                            </div>     


                        </div>