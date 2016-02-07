<html>
    <head>
        <title>School created successfully</title>        
    </head>
    <body>
        <div>
            <div style="overflow: hidden;">
                <div style="width:100%!important;background:#f2f2f2;margin:0;padding:0" bgcolor="#f2f2f2">
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
                                                                            <a href="<?php echo base_url(); ?>" target="_blank">
                                                                                <img src="<?php echo base_url('/images/classtune_email_logo.png'); ?>" alt="Classtune" width="225" border="0" style="display:block;outline:none;text-decoration:none;border:none">
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
                                                                                                        <span>Dear <?php echo $user_data['first_name'] . ' ' . $user_data['last_name'] . ','; ?>,</span><br />
                                                                                                        <span>Greeting from ClassTune Team. Thanks for signing up.</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td width="100%" height="40" style="border-collapse:collapse"></td>
                                                                                                </tr>
                                                                                                <tr>                                                                                    
                                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:20px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
                                                                                                        <strong>"<?php echo $school_created_data['returned_school_info']['school']['name']; ?>"</strong> has been successfully created.
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>                                                                                    
                                                                                                    <td valign="top" style="vertical-align:top;font-family:Helvetica,arial,sans-serif;font-size:15px;color:#666666;text-align:left;line-height:20px;border-collapse:collapse" align="left">
                                                                                                        <p>
                                                                                                            <span style="width: 100%; float: left;">Username: <?php echo $school_created_data['paid_user_data']['paid_username']; ?></span>
                                                                                                            <span style="width: 100%; float: left;">School Code: <?php echo $school_created_data['returned_school_info']['school']['activation_code']; ?> (Use this code to create user for your school)</span>
                                                                                                        </p>
                                                                                                        <p>
                                                                                                            <span style="width: 100%; float: left;">User Type: Admin</span>
                                                                                                            <span style="line-height: 40px;">Please <a style="color: #2CABE1;" href="<?php echo $activation_url; ?>">click here</a> to activate your account or 
                                                                                                            go to the following url:</span>
                                                                                                        </p>
                                                                                                        <a style="color: #2CABE1;" href="<?php echo $activation_url; ?>"><?php echo $activation_url; ?></a>

                                                                                                        <p><span>Please <a style="color: #2CABE1;" href="<?php echo $login_url; ?>">click here</a> to login into your account or 
                                                                                                            go to the following url:</span></p>
                                                                                                            <a style="color: #2CABE1;" href="<?php echo $login_url; ?>"><?php echo $login_url; ?></a>

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
                                                        House 54, Road 10, Block E, Banani,Dhaka 1213,Bangladesh © 2015 Classtune
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