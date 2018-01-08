<style type="text/css">
    .morebtn{
        cursor: pointer;
        font-size: 12px;
        position: absolute;
        right: 0;
        margin-right:120px;  
        color: white;
        background: #000;
        border: 1px solid #ccc;
        padding: 5px 7px;
        margin-top: -30px;
    }
    .left_container{
        margin-right: 50px;
    }
    .story-feature h2 {
        border-bottom: 1px solid #D8D8D8;
        border-top: 1px solid #D8D8D8;
        color: #505050;
        font-size: 1.1em;
        font-weight: bold;
        line-height: 16px;
        margin: 0 0 8px;
        padding: 11px 0 12px;
        text-rendering: optimizelegibility;
    }.story-feature ul li {
        font-size: 1em;
        line-height: 16px;
        background-position: -1200px 5px;
        background-repeat: no-repeat;
        margin: 0 0 8px;
        padding: 0 0 0 16px;
        background-image: url("http://upload.news.bbc.cs.streamuk.com/images/story_sprite.gif");
        list-style-type: none;
    }
    .red{ color: red;}
    #text p, #text h3,#text h1 { padding: 10px 0 !important;}
    .notify{ font-size: 12px; font-style: italic;}
    .sign-up {
        position: relative; 
        width: 280px;
        padding: 33px 25px 29px;
        background: white;
        border-bottom: 1px solid #c4c4c4;
        border-radius: 5px;
        @include box-shadow(0 1px 5px rgba(black, .25));

        &:before, &:after {
            content: '';
            position: absolute;
            bottom: 1px;
            left: 0;
            right: 0;
            height: 10px;
            background: inherit;
            border-bottom: 1px solid #d2d2d2;
            border-radius: 4px;
        }

        &:after {
            bottom: 3px;
            border-color: #dcdcdc;
        }
    }

    .sign-up-title {
        margin: -25px -25px 25px;
        padding: 15px 25px;
        line-height: 35px;
        font-size: 26px;
        font-weight: 300;
        color: #aaa;
        text-align: center;
        text-shadow: 0 1px rgba(white, .75);
        background: #f7f7f7;

        &:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: #c4e17f;
            border-radius: 5px 5px 0 0;
            @include linear-gradient(left,
            #c4e17f, #c4e17f 12.5%,
            #f7fdca 12.5%, #f7fdca 25%,
            #fecf71 25%, #fecf71 37.5%,
            #f0776c 37.5%, #f0776c 50%,
            #db9dbe 50%, #db9dbe 62.5%,
            #c49cde 62.5%, #c49cde 75%,
            #669ae1 75%, #669ae1 87.5%,
            #62c2e4 87.5%, #62c2e4);
        }
    }

    input {
        font-family: inherit;
        color: inherit;
        @include box-sizing(border-box);
    }

    .sign-up-input {
        width: 100%;  
        font-size: 12px;
        background: white;
        border: 1px solid #ebebeb;
        border-radius: 2px;
        @include box-shadow(inset 0 -2px #ebebeb);
        &:focus { 
            border-color: #62c2e4;
            outline: none;
            @include box-shadow(inset 0 -2px #62c2e4);
        }

        .lt-ie9 & { line-height: 48px; }
    }

    .sign-up-button {
        position: relative;
        vertical-align: top;
        width: 100px; 
        font-size: 22px;
        color: white;
        padding: 10px 0 !important;
        text-align: center;
        text-shadow: 0 1px 2px rgba(black, .25);
        background: #f0776c; 
        border-bottom: 1px solid #d76b60;
        border-radius: 2px;
        cursor: pointer;
        @include box-shadow(inset 0 -2px #d76b60);

        &:active {
            top: 1px;
            outline: none;
            @include box-shadow(none);
        }
    }

    :-moz-placeholder { color: #ccc; font-weight: 300; }
    ::-moz-placeholder { color: #ccc; opacity: 1; font-weight: 300; }
    ::-webkit-input-placeholder { color: #ccc; font-weight: 300; }
    :-ms-input-placeholder { color: #ccc; font-weight: 300; }

    ::-moz-focus-inner { border: 0; padding: 0; }

    .htmlForm td{
        font-family:georgia, Arial, Helvetica, sans-serif;
        font-size:12px;
        color:#000;
        border-bottom:1px #EAE3C8 solid;
        padding: 5px 0;
    }
    .htmlForm input,select{
        border:1px #BDB597 solid;
        font-family:georgia;
        font-size:12px;
        padding:4px;
    }.htmlForm textarea{ 
        width:180%;
        height:100px;
    }
    .points{
        font-family:tahoma;
        font-size:11px;
        color:#CC3300;
        padding-left:50px;
        padding-top:20px;
    }
    .image-req{

        padding: 0px !important;
    }
    .points li{
        padding-top:5px;
    }
    .lbltext{ 
        text-align: right;
        padding-right: 10px !important;

    }
</style> 

<div class="ym-gbox sports-inner-news">   

    <div class="ym-wbox container">

        <div class="ym-grid ym-column">
            <div class="ym-col1 left ">
                <form action='' method='POST' enctype='multipart/form-data'>
                    <div class="left_container">

                        <div id="text">
                            <h1>Your news, your pictures</h1>

                            <h3>If you have a news photograph or video that you have taken that we may be interested in, you can send it to the BBC News website using the form below.</h3>

                            <p>Don't forget to include your name, e-mail address and some background information about the image. You should also include your telephone number just in case we need to get back to you.</p>
                            <p class="notify">Fill up all the mandatory fields (marked with <span class="red">*</span> to ensure that your photos stand a good chance to be selected.</p>
                        </div>

                        <div   class="container_16 clearfix popup">

                            <table   border="0" cellpadding="5px" cellspacing="5" style="width:95%;" bgcolor="#FFCC66" class="sign-up">
                                <tr bgcolor="#FFFFDD">
                                    <td width="2px" bgcolor="#FFCC66"></td>
                                    <td width="400px">

                                        <!-- The inner table below is a container for form -->              
                                        <table width="100%" border="0" cellpadding="3px" class="htmlForm" cellspacing="0">
                                            <tr>
                                                <td colspan="2">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="left" class="lbltext">Name<span class="red">*</span></td>
                                                <td width="220px"><input name="name" type="text" class="sign-up-input" placeholder="What's your name?" autofocus></td>
                                            </tr>
                                            <tr>
                                                <td align="left"  class="lbltext">Email<span class="red">*</span></td>
                                                <td><input name="email" type="text" class="sign-up-input" placeholder="What's your email address?" autofocus></td>
                                            </tr>
                                            <tr>
                                                <td align="left"  class="lbltext">Phone/Mobile<span class="red">*</span></td>
                                                <td><input name="phone" type="text" class="sign-up-input" placeholder="What's your Phone/Mobile?" autofocus></td>
                                            </tr>
                                            <tr>
                                                <td align="left"  class="lbltext">Subject<span class="red">*</span></td>
                                                <td><input name="subject" type="text" class="sign-up-input" placeholder="What's your Phone/Mobile?" autofocus></td>
                                            </tr>                   
                                            <tr>
                                                <td align="left"  class="lbltext">Date taken<span class="red">*</span></td>
                                                <td><input id="subject"  name="subject" type="text" class="sign-up-input" placeholder="yyyy-mm-dd" autofocus></td>
                                            </tr>
                                            <tr>
                                                <td align="left"  class="lbltext">Description<span class="red">*</span> </td>
                                                <td>
                                                    <textarea id="description"  name="description" type="text" class="sign-up-input" placeholder="Tell about your photo" autofocus></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="left"  class="lbltext">&nbsp;</td>
                                                <td>
                                                    <ul class="points image-req">
                                                        <li>Maximum file size: 5 MB</li>
                                                        <li>File Type: jpg, gif, png</li>
                                                        <li>Image dimension: 1000x550 (WidthxHeight)</li> 
                                                        <li>You can choose maximum 3 images</li>
                                                    </ul>

                                                </td>
                                            </tr> 


                                            <tr>
                                                <td align="left"  class="lbltext">Choose Image(s)<span class="red">*</span></td>
                                                <td>

                                                    <input id="choose_images1" name="choose_images[]" type="file"  class="sign-up-input"  autofocus>
                                                    <a onclick="addNewFile();" class="morebtn">Add More + </a>
                                                    <input type="hidden" value="1" name="countImg" id="countImg">
                                                    <div id="MoreImgDiv">&nbsp;</div> 
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>&nbsp;</td>
                                                <td><input type="submit" value="Send" class="sign-up-button"></td>
                                            </tr>


                                        </table>
                                    </td>
                                    <td valign="top">
                                        <ul class="points">
                                            <li>Name is required</li>
                                            <li>Your email will be used as your identity</li>
                                            <li>Phone will be used for contacts</li>
                                            <li>Subject is required</li>
                                            <li>Date Taken is required</li>
                                            <li>Description will be used as caption of your photo</li>
                                        </ul>
                                    </td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </form>

            </div>
            <div class="ym-col3 right ym-gr">

                <div class="story-feature left">
                    <h2>SEND US YOUR PICTURES</h2>
                    <p><b>Other ways of sending us your pictures.</b></p>  <br/>
                    <ul>
                        <li>Email: <a style="color:#000;" href="mailto:photocompetition@dailystar.net">photocompetition@dailystar.net</a></li>
                        <li>Maximum file size: 5 MB</li>
                        <li>Image dimension: 1000x550 (WidthxHeight)</li>
                        <li>You can choose maximum 3 images</li>
                    </ul> 
                    <p>When taking photos or filming please do not endanger yourself or others, take unnecessary risks or infringe any laws. Please read our <a href="http://www.thedailystar.net">Terms and Conditions</a> for the full terms of our rights.</p>
                </div> 
            </div> 
        </div> 
    </div>
  
</div> 
</div>

<script>
    function addNewFile() {

        var inc = document.getElementById("countImg");
        if ((parseInt(inc.value)) > 2) {
            alert("You can choose Maximum 3 images.");
            return false;
        }
        var inputFile = '<input id="choose_images' + (inc + 1) + '" name="choose_images[]" type="file"  class="sign-up-input"  autofocus style="margin-top:10px;">';
        var myDiv = document.getElementById("MoreImgDiv");
        myDiv.innerHTML += inputFile;
        inc.value = (parseInt(inc.value) + 1);
    }

</script>