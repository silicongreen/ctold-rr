<html>
    <head>
        <title>Select School</title>        
    </head>
    <body>
        <div id=":15p" class="ii gt m150fa30681456eab adP adO">
            <div id=":14v" class="a3s" style="overflow: hidden;">
                <div style="width:100%!important;background:#f2f2f2;margin:0;padding:0" bgcolor="#f2f2f2">
                    <div class="adM">
                    </div>
                    <div>
                        <table width="100%" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" border="0" style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin:0;padding:0">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse">
                                    <table width="542" bgcolor="#6AB03E" cellpadding="0" cellspacing="0" border="0" align="center" style="display:block;border-collapse:collapse">
                                        <tbody style="display:table;width:100%">
                                        <tr>
                                            <td style="border-collapse:collapse">

                                                <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse">
                                                    <tbody>
                                                    <tr>
                                                        <td valign="middle" width="100%" align="center" style="border-collapse:collapse;padding:10px 0">
                                                            <div>
                                                                <a href="" target="_blank">
                                                                    <img src="<?php echo base_url("styles/layouts/tdsfront/class_tune/logo/classtune_email_logo.png");?>" alt="Upwork" width="225" border="0" style="display:block;outline:none;text-decoration:none;border:none" class="CToWUd">
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>    
                    </div>
                    <div>    
                        <table width="100%" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" border="0" style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin:0;padding:0">
                            <tbody>
                            <tr>
                                <td style="border-collapse:collapse">
                                    <table bgcolor="#ffffff" width="542" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <table width="502" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
                                                    <tbody style="border-collapse:collapse">
                                                    <tr>
                                                        <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse">
                                                            <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="border-collapse:collapse">
                                                                            <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse">
                                                                                <tbody>
                                                                                <tr>
                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
                                                                                        <span>Congratulations!</span>
                                                                                        <span>Dear <?php echo ($guardian['fulname'] != "")?$guardian['fulname']:(($student['fulname'] != "")?$student['fulname']:(($teacher['fulname'] != "")?$teacher['fulname']:"User")); ?>,</span><br />
                                                                                        <span>Thank you for signing up in ClassTune.Here is your Sign up information.User password is not send here for security reason.</span>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                                                </tr>
                                                                                <tr>                                                                                    
                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
                                                                                        
                                                                                        <?php if($guardian['fulname'] != ""):?>
                                                                                            <span>Your Name :</span>  
                                                                                            <span><strong>
                                                                                                <?php echo $guardian['fulname'];?>
                                                                                            </strong></span>  <br />                                                                                          
                                                                                        <?php endif; ?>
                                                                                        <?php if($guardian['username'] != ""):?>
                                                                                            <span>UserName :</span>  
                                                                                            <span><strong>
                                                                                                <?php echo $guardian['username'];?>
                                                                                            </strong></span>     <br />                                                                                        
                                                                                        <?php endif; ?>
                                                                                        <legend style="border-bottom: 1px dotted;margin: 10px 0px;"></legend>
                                                                                        <?php foreach($students as $row):?>
                                                                                            <?php if($row['fulname'] != ""):?>
                                                                                                <span>Student Name :</span>  
                                                                                                <span><strong>
                                                                                                        <?php echo $row['fulname'];?>
                                                                                                </strong></span> <br />
                                                                                            <?php endif; ?>
                                                                                            <?php if($row['username'] != ""):?>
                                                                                                <span>Student UserName :</span> 
                                                                                                <span><strong>
                                                                                                        <?php echo $row['username'];?>
                                                                                                </strong></span> <br />
                                                                                            <?php endif; ?>

                                                                                            <?php if($row['admission_no'] != ""):?>
                                                                                                <span>Admission No :</span>  
                                                                                                <span><strong>
                                                                                                        <?php echo $row['admission_no'];?>
                                                                                                </strong></span> <br />
                                                                                            <?php endif; ?>
                                                                                            <legend style="border-bottom: 1px dotted;margin: 10px 0px;"></legend>
                                                                                        <?php endforeach; ?>
                                                                                            
                                                                                            
                                                                                        <?php if($teacher['fulname'] != ""):?>
                                                                                            <span>Your Name :</span>  
                                                                                            <span><strong>
                                                                                                    <?php echo $teacher['fulname'];?>
                                                                                            </strong></span> <br />
                                                                                        <?php endif; ?>
                                                                                        <?php if($teacher['username'] != ""):?>
                                                                                            <span>UserName :</span>  
                                                                                                <span><strong>
                                                                                                        <?php echo $teacher['username'];?>
                                                                                            </strong></span> <br />
                                                                                        <?php endif; ?>
                                                                                        <?php if($teacher['admission_no'] != ""):?>
                                                                                            <span>Employee No :</span>  
                                                                                            <span><strong>
                                                                                                <?php echo $teacher['admission_no'];?>
                                                                                            </strong></span> <br />
                                                                                        <?php endif; ?>
                                                                                            
                                                                                            
                                                                                            
                                                                                            
                                                                                            
                                                                                            
                                                                                        <?php if($student['fulname'] != ""):?>
                                                                                            <span>Your Name :</span> 
                                                                                            <span><strong>
                                                                                                    <?php echo $student['fulname'];?>
                                                                                            </strong></span> <br />
                                                                                        <?php endif; ?>
                                                                                        <?php if($student['username'] != ""):?>
                                                                                            <span>UserName :</span>  
                                                                                            <span><strong>
                                                                                                    <?php echo $student['username'];?>
                                                                                            </strong></span> <br />
                                                                                        <?php endif; ?>
                                                                                        <?php if($student['admission_no'] != ""):?>
                                                                                            <span>Admission No :</span>  
                                                                                            <span><strong>
                                                                                                    <?php echo $student['admission_no'];?>
                                                                                            </strong></span> <br />
                                                                                            <legend style="border-bottom: 1px dotted;margin: 10px 0px;"></legend>
                                                                                        <?php endif; ?>
                                                                                        
                                                                                        <?php foreach($guardians as $row):?>
                                                                                            <?php if($row['username'] != ""):?>
                                                                                                <span>Guardian UserName :</span>  
                                                                                                <span><strong>
                                                                                                        <?php echo $row['username'];?>
                                                                                                </strong></span> <br />
                                                                                            <?php endif; ?>
                                                                                            <?php if($row['fulname'] != ""):?>
                                                                                                <span>Guardian Name :</span>  
                                                                                                <span><strong>
                                                                                                        <?php echo $row['fulname'];?>
                                                                                                </strong></span> <br />
                                                                                            <?php endif; ?>
                                                                                            <legend style="border-bottom: 1px dotted;margin: 10px 0px;"></legend>
                                                                                        <?php endforeach; ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">                                                                                        
                                                                                        <p><span>Please <a style="color: #2CABE1;" href="http://www.classtune.com/login">Click here</a> to login into your account or go to the following url:</span></p>
                                                                                        <a style="color: #2CABE1;" href="http://www.classtune.com">http://www.classtune.com/login</a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:14.5px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
                                                                                        <span>Best Regards,</span><br />
                                                                                        <span>ClassTune Team</span>
                                                                                    </td>
                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                                    </tr>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>      
                    <div>
                        <table width="100%" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" border="0" style="width:100%!important;line-height:100%!important;border-collapse:collapse;margin:0;padding:0">
                            <tbody>
                            <tr>
                                <td width="100%" style="border-collapse:collapse">
                                    <table width="502" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse">
                                        <tbody>
                                        <tr>
                                            <td width="100%" height="40" style="border-collapse:collapse"></td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle" style="font-family:Helvetica,arial,sans-serif;font-size:10px;color:#666666;border-collapse:collapse">
                                                House 54, Road 10, Block E, Banani,Dhaka 1213,Bangladesh © 2016 ClassTune
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="40" style="border-collapse:collapse"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>