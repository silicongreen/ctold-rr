<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/tabs_old.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.css'); ?>">
<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;">
        <a href="http://www.champs21.dev/spellingbee/">
            <h1 style="color:#93989C;" class="title noPrint f2">
                Spelling Bee
            </h1>
        </a>
    </div>
    <nav>
        <div class='ribbon'>
            <a id="tabHeader_1" href='#'><span>Home</span></a>
            <a id="tabHeader_2" href='#'><span>About</span></a>
            <a id="tabHeader_3" href='#'><span>Registration</span></a>
            <a id="tabHeader_4" href='#'><span>Spellato</span></a>
            <a id="tabHeader_5" href='#'><span>Terms & Condition</span></a>
            <a id="tabHeader_6" href='#'><span>Gallery</span></a>
            <a id="tabHeader_7" href='#'><span>Profile</span></a>
        </div>
    </nav>
    <div class="spellingbee">
        <div id="tabscontent">
            <section class="tabpage" id="tabpage_1">
                <?php $this->load->view('spellingbee/home'); ?>              
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_2">
               <?php $this->load->view('spellingbee/about'); ?>  
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_3">
               <?php $this->load->view('spellingbee/registration'); ?>  
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_4">
               <?php $this->load->view('spellingbee/spellato'); ?>  
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_5">
               <?php $this->load->view('spellingbee/term_n_condition'); ?>  
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_6">
               <?php $this->load->view('spellingbee/gallery'); ?>  
            </section>
            <section class="tabpage content mCustomScrollbar" id="tabpage_7">
               <?php $this->load->view('spellingbee/profile'); ?>  
            </section>
        </div>
    </div>
</div>

<style>
    .spellingbee
    {
        
        margin: 70px 20px;
        
    }
    nav {
    display: block;
}
/*Forked ends*/
.ribbon:after, .ribbon:before {
    margin-top:0.5em;
    content: "";
    float:left;
    border:1.5em solid #fff;
}

.ribbon:after {
    border-right-color:transparent;
}

.ribbon:before {
    border-left-color:transparent;
}

/*Links*/
 .ribbon a:link { 
    color:#093651;
    text-decoration:none;
    float:left;
    height:3.5em;
    overflow:hidden;
}
/*Animated Folds*/
.ribbon span {
    background:#fff;
    display:inline-block;
    line-height:3em;
    padding:0 1em;
    margin-top:0.5em;
    position:relative;

    -webkit-transition: background-color 0.2s, margin-top 0.2s;  /* Saf3.2+, Chrome */
    -moz-transition: background-color 0.2s, margin-top 0.2s;  /* FF4+ */
    -ms-transition: background-color 0.2s, margin-top 0.2s;  /* IE10 */
    -o-transition: background-color 0.2s, margin-top 0.2s;  /* Opera 10.5+ */
    transition: background-color 0.2s, margin-top 0.2s;
}

.ribbon a:hover span,.ribbon a:focus span {
    background:#FFD204;
    margin-top:0;
}

.ribbon span:before {
    content: "";
    position:absolute;
    top:3em;
    left:0;
    border-right:0.5em solid #9B8651;
    border-bottom:0.5em solid #fff;
}

.ribbon span:after {
    content: "";
    position:absolute;
    top:3em;
    right:0;
    border-left:0.5em solid #9B8651;
    border-bottom:0.5em solid #fff;
}

.content {
  background: none repeat scroll 0 0 #fff;
  box-sizing: border-box;
  height: 400px;
  margin: 10px;
  max-width: 98%;
  overflow: auto;
  padding: 20px;
  position: relative;
  width: 100%;
}
#tabscontent {
	-moz-border-radius-topleft: 0px;
	-moz-border-radius-topright: 4px;
	-moz-border-radius-bottomright: 4px;
	-moz-border-radius-bottomleft: 4px;
	border-top-left-radius: 0px;
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
	border-bottom-left-radius: 4px; 
	height: 410px;
        background-color: #fff;
        overflow: hidden;
	/*background: #FFFFFF; /* old browsers */
	/*background: -moz-linear-gradient(top, #FFFFFF 0%, #FFFFFF 90%, #e4e9ed 100%); /* firefox */
	/*background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FFFFFF), color-stop(90%,#FFFFFF), color-stop(100%,#e4e9ed));  webkit */
	margin:0;
	color:#333;
}
.tabActiveHeader{
	cursor:pointer;
	color: #333;
}
#tabscontent .tabpage:not(:first-child) 
{
    display: none;
}
</style>
