<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;width: 96%;">
        <a href="<?php echo base_url('spellingbee'); ?>">
            <h1 style="color:#93989C;float: left;" class="title noPrint f2">
                Spelling Bee&nbsp;
            </h1>
        </a>                
        <h1 style="color:#93989C;float: left;" class="title noPrint f2">
            > Game Rules
        </h1>        
    </div>
    <div style="clear:both;"></div>
    <div class="spellingbee">
        <div style="float: left;width: 100%;padding: 10px 50px;margin-top:35px;">
            <a href="<?php echo base_url('gamerules'); ?>">
                <h1 style="float: left;color:#000;font-size:40px;margin-top: 20px;" class="title noPrint f2">
                    Game Rules
                </h1>
            </a>
            <a href="<?php echo base_url('spellingbee'); ?>">
                <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sp-logo.png'); ?>" style="float: right;width:15%;">
            </a>
            
        </div>
        <div style="clear:both;"></div>    
        <div class="col-lg-12 row-fluid row">

            <ol>
                <li>
                    <p>
                        Any student studying in Class VI-X is eligible to participate in Spelling Bee 2015.
                    </p>
                </li>
                <li>
                    <p>
                        Students from both English and Bangla mediums are eligible to participate.
                    </p>
                </li>
                <li>
                    <p>
                        The candidates of SSC and O-Level examinations are NOT eligible to participate in Spelling Bee 2015.
                    </p>
                </li>
                <li>
                    <p>
                        The participant must be Bangladeshi.
                    </p>
                </li>
                <li>
                    <p>
                        Any Non-Resident Bangladeshi student can participate in Spelling Bee provided (s)he is a student of Class VI- X.
                    </p>
                </li>
                <li>
                    <p>
                       The speller must not bypass or circumvent normal school activity to study for Spelling Bee.
                    </p>
                </li>
                <li>
                    <p>
                        Upon successfully being selected for the divisional round, the speller must fill up the divisional round accessform (to be obtained from the Champs21.com website) and bring it along with him/her to the divisional round venue.
                    </p>
                </li>
                <li>
                    <div>
                        <p>
                            Champs21.com reserves all rights to alter/change the competition format at any point of time
                        </p>
                        <p>
                            <strong>Note : </strong>For Non Resident Bangladeshi (N.R.B) students who are willing to participate in the competition, the following apply: 
                        </p>
                        <ul>
                            <li>
                                <p>
                                    N.R.B students from student from class VI-X can participate.
                                </p>
                            </li>
                            <li>
                                <p>
                                    In the event of qualifying to the divisional round, the student must bring <b>a letter from the school</b> confirming student status, detailing the <b>class</b> and 
                                </p>
                            </li>
                            <li>
                                <p>
                                    Please note that upon qualifying to the divisional round, you will have to travel to Bangladesh to participate in the divisional round. 
                                </p>
                            </li>
                        </ul>
                        <p>
                            Are you already a registered user of www.champs21.com? If yes, just log in to your account and start playing ‘Spelling Bee Online Round’. If you are not registered yet, just click here to register and start playing. 
                        </p>
                        <p>
                            As per game rules, if you make one mistake, you're out of the game! The good news is that you can play the game as many times as you want until the 1st Round ends on July 15, 2015. Your best score will be considered and only top spellers of your division will be selected for the 2nd Round. 
                        </p>
                    </div>
                </li>
            </ol>

        </div>


              
        <div style="height:100px;"></div>    
        
    </div>
</div>

<style>
.spellingbee {
  background: none repeat scroll 0 0 #ffffff;
  margin: 10px 20px;
  min-height: 600px;
  background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/bee.png'); ?>");
  background-position: right bottom;
  background-repeat: no-repeat;
  background-size: 13% auto;
}
nav {
    display: block;
}
.tabcontainer {
    float: left;
    width: 100%;
    text-align:center;
    
}
.tabcontainer ul
{
    margin:0px;
}
.tabheading li.active {
    background-color: #FDF8CE;
    border-bottom: 0;
    margin-bottom: -1px;
}
.tabheading li {
    display: inline-block;
    border: 1px solid #ddd;
    background-color: #F2FAFD;
    margin: 0;
    padding: 10px 0px;
}
.tabheading li a { 
    padding: 10px 33px;
    color:#88A5B3;
}
.tabbody.active {
    display: block;
}
.tabbody {    
    margin: 0px auto;
    min-height: 10px;
    width: 94%;
    padding: 0;
    display: none;
}

</style>
<script>
 $('.tabheading li').click(function () {
        var tabid = $(this).attr("rel");
        $(this).parents('.tabcontainer').find('.active').removeClass('active');
        $('.tabbody').hide();
        $('#' + tabid).show();
        $(this).addClass('active');

        return false;
    });
</script>
<style>
    .row{
        padding: 0px 30px 30px;
    }
    .row ol li
    {
        font-family: Arial !important;
    }
    .row p
    {
        font-family: Arial;
        letter-spacing:0px;
    }
</style>