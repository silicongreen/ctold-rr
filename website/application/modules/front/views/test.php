<body data-spy="scroll" data-target=".navbar">
 

    <nav style="top: 0px;" id="topnav" class="navbar navbar-inverse navbar-fixed-top navbar-default" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                <a href="http://www.thedailystar.net/" target="_blank"><img src="<?php echo base_url(); ?>rana_plaza_files/st.png" alt="Back to thedailystar.net" style="margin:17px 15px 17px 0px; float:left"></a><a class="navbar-brand" href="#">Rana Plaza</a> </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class=""><a href="#top-section">Home</a></li>
                    <?php
                    $i=1;
                    if(count($news)>0):
                    foreach($news as $value):
                    ?>
                    <li <?php if($i==1): ?>class="active"<?php else: ?> class="" <?php endif; ?>><a href="#Chapter-<?php echo $i; ?>"><?php echo $i; ?></a></li>
                    
                    <?php
                    $i++;
                    endforeach;
                    endif;
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
    </nav>
    <!-- HEADER -->
    <header id="top-section">
        <div style="top: 100px;" class="jumbotron">
            <div class="container">
                <div class="row">
                    <div class="text-left col-xs-8 col-sm-6 col-md-6 col-lg-4">
                        <h1 class="">Rana Plaza</h1>
                        <hr>
                        <p>Untold stories of<br>
                            Malaysia Airlines MH370</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="inner-top-bg"></div>
        <!-- OVERLAY BG-->
    </header>
    <!-- / HEADER -->
    <div class="responsive-frame video-container">
        <iframe src="<?php echo base_url(); ?>rana_plaza_files/playerwidget.htm" frameborder="0" scrolling="no"></iframe>
    </div>
    <!--  Chapter-1 -->
    <?php
        $i=1;
        if(count($news)>0):
        foreach($news as $value):
     ?>
    
    <section id="Chapter-<?php echo $i;?>" style="background: url(<?php echo $value->lead_material;?>) no-repeat local center top;
                    background-size:100% 100%;" class="unicorn">
        <div class="container">
            <!-- Example row of columns -->
            <div class="row">
                <div class="page-header color-white col-xs-8 col-sm-6 col-md-6 col-lg-4">
                    <h3 style="padding-top:50px; text-shadow: 0 1px 3px rgba(0,0,0,.4), 0 0 0;"><?php echo $value->shoulder;?></h3>
                    <hr>
                    <h1 style="text-shadow: 0 1px 3px rgba(0,0,0,.4), 0 0 0;"><?php echo $value->headline;?></h1>
                </div>
            </div>
        </div>
        <!-- / CONTAINER-->
    </section>
    <section id="chapters">
        <div class="chaptercontainer">
            <!-- Example row of columns -->
            <div class="row">
                <div class="chapter">
                    <!-- Content here -->     
                    <?php echo $value->content;?>

                </div>
                <!-- /.chapter -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    
    <?php
        $i++;
        endforeach;
        endif;
     ?>
    

