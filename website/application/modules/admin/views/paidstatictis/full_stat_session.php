
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                       
                        <table   class="table table-bordered" style="width: 100%;">
                           <tr class="even">
                                <th>
                                    Name
                                </th>
                                
                                <th>
                                    User Type
                                </th>
                                <th>
                                    School
                                </th>
                                <th>
                                    Number Of Session
                                </th>
                                <th>
                                    Session Time
                                </th>
                            </tr>
                            <?php foreach($stat as $value): ?>
                            
                            <tr class="even">
                                <td>
                                    <?php echo $value->username ?>
                                </td>
                                <td>
                                    <?php echo $user_type_array[$value->user_type_paid] ?>
                                </td>
                                <td>
                                    <?php echo $value->name ?>
                                </td>
                                <td>
                                    <?php echo $value->snumber; ?>
                                </td>
                                <td>
                                    <?php echo round($value->session_time/60); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>


            </div>

        </div>

