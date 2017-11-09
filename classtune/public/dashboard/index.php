<?php 
    include 'scripts/modules/config.php';
    
    if ( !isset($_GET['dom']) && empty($_GET['dom'])) 
    {
        header("Location: http://www.classtune.com");
    }
    
    $server_name = $_SERVER['SERVER_NAME'];
    $current_server = "http://" . $server_name;
    $server_name = "http://" . str_replace("dashboard", $_GET['dom'], $server_name);
    $thai_school = 0;
    //Temporary Code, When Done please remove
    //Indent to pass the token using $_GET parameter
    if ( isset($_GET['modified_id']) && !empty ($_GET['modified_id']) )
    {
        $thai_school = 1;
        $school_modified_id = $_GET['modified_id'];
        $url = $server_name . '/oauth/token';

        $fields = array(
                'client_id' => 'b2a74741527577417766c57ee66b998f03f8666c',
                'client_secret' => '3955b8d770dbb0e2a5900e744d84c2f60e96a621',
                'grant_type' => 'password',
                'username' => 'cbis-admin',
                'password' => 'cbis81',
                'redirect_uri' => $server_name . '/authenticate'
        );
//         $fields = array(
//                'client_id' => '900dbcba0d3320a2fd3ded6f0fe93b68e41e87ce',
//                'client_secret' => 'f943d664fbbb19778c63d059d5d7d35a98f72102',
//                'grant_type' => 'password',
//                'username' => 'chs-admin',
//                'password' => '123456',
//                'redirect_uri' => $server_name . '/authenticate'
//        );
        $fields_string = '';
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        $fields_string = substr($fields_string, 0, -1);

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);
        print_r($result);
        exit;
        //close connection
        curl_close($ch);
        $token = $res->access_token;
        
        $conn = new mysqli($db_m['host'],$db_m['username'], $db_m['password'], $db_m['dbname']);
        $conn->set_charset("utf8");
        
        $result = $conn->query("SELECT * FROM schools WHERE id = " . $_GET['modified_id']);
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $school_name = $rs['institute_name'];
        
    }
    else if ( isset($target) && $target == "local" )
    {
        $url = $server_name . '/oauth/token';

        $fields = array(
                'client_id' => '900dbcba0d3320a2fd3ded6f0fe93b68e41e87ce',
                'client_secret' => 'f943d664fbbb19778c63d059d5d7d35a98f72102',
                'grant_type' => 'password',
                'username' => 'chs-admin',
                'password' => '123456',
                'redirect_uri' => $server_name . '/authenticate'
        );
        $fields_string = '';
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        $fields_string = substr($fields_string, 0, -1);

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $res = json_decode($result);

        //close connection
        curl_close($ch);
        $token = $res->access_token;
    }
    else
    {
        $token = $_GET['access_token'];
    }
    $username = $_GET['username'];
?>
<!doctype html>
<html ng-app="minovateApp" ng-controller="MainCtrl" class="no-js {{containerClass}}">
  <head>
    <meta charset="utf-8">
    <title>Classtune  - Admin Dashboard</title>
    <meta name="description" content="Classtune School Management System">
    <meta name="viewport" content="width=device-width">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="icon" type="image/ico" href="favicon.ico" />
    <link rel="stylesheet" href="styles/vendor.0cc3d200.css">
    <link rel="stylesheet" href="styles/main.1be6a35c.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script>
        var school_domain = 'chs';
        var school_name = '';
        var school_id = 0;
        var admin_name = '';
        var admin_username = '';
        var dashboard_link = '<?php echo $current_server; ?>';
        var thai_school = <?php echo $thai_school; ?>;
    </script>    
    <script>
        var classtune_server = '<?php echo $server_name; ?>';
        var token = '<?php echo $token; ?>';
        var username = '<?php echo $username; ?>';
        show_user(classtune_server, token, username);
        function show_user(classtune_server, token, username)
        {
            try
            {
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function(evt)
                {
                   if (xhr.readyState==4)
                    {
                        return show_response(evt.target.responseText, classtune_server, token);
                    }
                }
                
                xhr.open('GET', classtune_server+"/api/users/"+username);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('Authorization', 'Token token="'+token+'"');
                xhr.send();
            }
            catch(err)
            {
                alert(err.message);
            }
        }
        
        function reminder_counts(classtune_server, token, username)
        {
            
        }

        function show_response(xml, classtune_server, token)
        {
            
            var parser = new DOMParser();
            var xmlDoc = parser.parseFromString(xml,"text/xml");
            admin_name = xmlDoc.getElementsByTagName("first_name")[0].childNodes[0].nodeValue + " " + xmlDoc.getElementsByTagName("last_name")[0].childNodes[0].nodeValue;
            admin_username = xmlDoc.getElementsByTagName("username")[0].childNodes[0].nodeValue;
            
            if ( xmlDoc.getElementsByTagName("user_type")[0].childNodes[0].nodeValue != "Admin" && xmlDoc.getElementsByTagName("user_type")[0].childNodes[0].nodeValue != "Principle" )
            {
                location.href = classtune_server;
            }
            else
            {
                try
                {
                    var xhr = new XMLHttpRequest();

                    xhr.onreadystatechange = function(evt)
                    {
                       if (xhr.readyState==4)
                        {
                            var parser = new DOMParser();
                            var xmlDoc = parser.parseFromString(evt.target.responseText,"text/xml");
                            if ( thai_school == 1 )
                            {
                                school_name = '<?php echo $school_name; ?>';
                            }
                            else
                            {
                                school_name = xmlDoc.getElementsByTagName("institute_name")[0].childNodes[0].nodeValue;
                            }
                            school_id = xmlDoc.getElementsByTagName("institute_id")[0].childNodes[0].nodeValue;
                        }
                    }

                    xhr.open('GET', classtune_server+"/api/schools");
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.setRequestHeader('Authorization', 'Token token="'+token+'"');
                    xhr.send();
                }
                catch(err)
                {
                    alert(err.message);
                }
            }
        }
        
        

    </script>
  </head>
  <body id="minovate" class="{{main.settings.navbarHeaderColor}} {{main.settings.activeColor}} {{containerClass}} header-fixed aside-fixed rightbar-hidden appWrapper" ng-class="{'header-fixed': main.settings.headerFixed, 'header-static': !main.settings.headerFixed, 'aside-fixed': main.settings.asideFixed, 'aside-static': !main.settings.asideFixed, 'rightbar-show': main.settings.rightbarShow, 'rightbar-hidden': !main.settings.rightbarShow}">

    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Application content -->
    <div id="wrap" ui-view autoscroll="false"></div>

    <!-- Page Loader -->
    <div id="pageloader" page-loader></div>


    <!-- Google Analytics: change UA-XXXXX-X to be your site's ID -->
     <script>
       (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
       (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
       m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
       })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

       ga('create', 'UA-XXXXX-X');
       ga('send', 'pageview');
    </script>
    <!--script src='//maps.googleapis.com/maps/api/js?libraries=weather,geometry,visualization,places,drawing&sensor=false&language=en&v=3.17'></script-->

    <!--[if lt IE 9]>
    <script src="scripts/oldieshim.f2dbeece.js"></script>
    <![endif]-->

    <script src="scripts/vendor.342123aasw3.js"></script>

    <script src="scripts/app.f2dbeece.js"></script>
</body>
</html>
