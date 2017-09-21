<?php
    if ( !isset($_GET['dom']) && empty($_GET['dom'])) 
    {
        header("Location: http://www.classtune.com");
    }
    
    $server_name = $_SERVER['SERVER_NAME'];
    $server_name = "http://" . str_replace("dashboard", $_GET['dom'], $server_name);
    
    //Temporary Code, When Done please remove
    //Indent to pass the token using $_GET parameter
    $url = $server_name . '/oauth/token';
    
    $fields = array(
            'client_id' => '900dbcba0d3320a2fd3ded6f0fe93b68e41e87ce',
            'client_secret' => 'f943d664fbbb19778c63d059d5d7d35a98f72102',
            'grant_type' => 'password',
            'username' => 'chs-admin',
            'password' => '123456',
            'redirect_uri' => 'http://chs.classtune.dev/authenticate'
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
    <script>
        var school_domain = 'chs';
        var school_name = '';
        var school_id = 0;
    </script>    
    <script>
        var fedena_server = '<?php echo $server_name; ?>';
        var token = '<?php echo $token; ?>';
        var username = '<?php echo $username; ?>';
        show_user(fedena_server, token, username);
        function show_user(fedena_server, token, username)
        {
            try
            {
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function(evt)
                {
                   if (xhr.readyState==4)
                    {
                        return show_response(evt.target.responseText, fedena_server, token);
                    }
                }
                
                xhr.open('GET', fedena_server+"/api/users/"+username);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('Authorization', 'Token token="'+token+'"');
                xhr.send();
            }
            catch(err)
            {
                alert(err.message);
            }
        }

        function show_response(xml, fedena_server, token)
        {
            var parser = new DOMParser();
            var xmlDoc = parser.parseFromString(xml,"text/xml");
            if ( xmlDoc.getElementsByTagName("user_type")[0].childNodes[0].nodeValue != "Admin" && xmlDoc.getElementsByTagName("user_type")[0].childNodes[0].nodeValue != "Principle" )
            {
                location.href = fedena_server;
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
                            school_name = xmlDoc.getElementsByTagName("institute_name")[0].childNodes[0].nodeValue;
                            school_id = xmlDoc.getElementsByTagName("institute_id")[0].childNodes[0].nodeValue;
                        }
                    }

                    xhr.open('GET', fedena_server+"/api/schools");
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

    <script src="scripts/vendor.4c173da6.js"></script>

    <script src="scripts/app.f2dbeece.js"></script>
</body>
</html>
