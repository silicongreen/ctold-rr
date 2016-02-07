<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
/*
Template Name: faq-bn
*/
get_header(); ?>
<input type="hidden" value="<?php echo get_site_url(); ?>" id="base_url">
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">		
		<div class="container-fluid" style="background:#fff;padding:0px;">
			<div class="wrapper">
				<div class="item pricing" id="supportWrap" style="margin-top:80px;">					
					<div class="row" style="margin:0px;">
						<div class="col-md-6 col-sm-offset-3"  style="">					
							<div id="custom-search-input">
								<div class="input-group col-md-12">
									<input type="text" class="form-control input-lg" placeholder="Search Keyword" />
									<span class="input-group-btn">
										<button class="btn btn-info btn-lg" type="button">
											<i class="glyphicon glyphicon-search"></i>
										</button>
									</span>
								</div>
							</div>                     
						</div>
					</div>			
				</div><!-- /.item -->  
				<div class="item pricing" id="supportFaq" style="margin-top:0px;">					
					<div class="row" style="margin:0px;">
						
						<div class="col-md-12">
							<h2 style="margin-top: 40px;" class="f2"><i>এফএকিউ</i></h2>
						</div>
						<div class="col-md-8 col-sm-offset-2" id="faq-data" style="margin-top: 40px;">					
							<?php 
								//$var = do_shortcode( '[faq cat_id="3"] ' );
								//echo $var;
							?>
							<div class="tabbable">
								<ul class="nav nav-tabs" id="myTabs">    
									<li class="active"><a href="#admin" data-toggle="tab">অ্যাডমিন</a></li>
									<li><a href="#student" data-toggle="tab">শিক্ষার্থী</a></li>
									<li><a href="#parent" data-toggle="tab">অভিভাবক</a></li>
									<li><a href="#teacher" data-toggle="tab">শিক্ষক</a></li>
								</ul>
							 
								<div class="tab-content">
									<div class="tab-pane active" faq-index="2" id="admin"></div>
									<div class="tab-pane" faq-index="3" id="student"></div>
									<div class="tab-pane" faq-index="4" id="parent"></div>
									<div class="tab-pane" faq-index="5" id="teacher"></div>
								</div>
							</div>
						</div>						
					</div>			
				</div><!-- /.item -->  
				<div class="item pricing" id="supportMore" style="margin-top:0px;">					
					<div class="row" style="margin:0px;">
						
						<div class="col-md-12">
							<h2 style="margin-top: 40px;margin-bottom: 80px;" class="f2"><i>আরো সাহায্যের প্রয়োজন?</i></h2>
						</div>
						<div class="col-md-8 col-sm-offset-2">
							<div class="col-md-4 col-sm-offset-0"  style="">
								<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-weixin"></i><br>কথোপকথন</button>
							</div>
							<div class="col-md-4 col-sm-offset-0"  style="">
								<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-headphones"></i><br>হটলাইন</button>
							</div>
							<div class="col-md-4 col-sm-offset-0"  style="">
								<a href="<?php echo get_site_url().'/'.$lang."?locale=contact"; ?>" >
									<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-paper-plane-o"></i><br>যোগাযোগ</button>
								</a>
							</div>
						</div>
						
					</div>			
				</div><!-- /.item -->  
			</div>
		</div>

	</main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_footer('extra'); ?>
<script>
jQuery(document).ready( function($) {

	//$.ajax({
	//	url: "http://www.ctune.dev/?shortcode=faq",
	//	type: "post",
	//	success: function( data ) {
			//$('#home').html( data );
	//	}
	//})

})
</script>
<script>
    $(function() {
        var baseURL = $("#base_url").val();
        //load content for first tab and initialize faq cat_id="3"
        $('#admin').load(baseURL+'?shortcode=faq&cat_id=2', function() {
            $('#myTabs').tab(); //initialize tabs
        });    
        $('#myTabs').on('show.bs.tab', function (e) {    
			//e.preventDefault();	
            var pattern=/#.+/gi //use regex to get anchor(==selector)
            var contentID = e.target.toString().match(pattern)[0]; //get anchor         
		    var id = contentID.replace('#','');
		    var cat_id = $('#'+id).attr('faq-index');

            //load content for selected tab  
            $(contentID).load(baseURL+'?shortcode=faq&cat_id='+cat_id, function(){
                $('#myTabs').tab(); //reinitialize tabs
            });
        });
    });
</script>
<style>
    #supportFaq #faq-data .nav-tabs
	{
		border-color:transparent;
		margin-bottom:20px;
		width:100%;
	}
	#supportFaq #faq-data .nav-tabs li
	{
		width:25%;
	}
	#supportFaq #myTabs a
	{
		border-radius:0px;
		border: 0px solid transparent;		
		line-height: 1.42857;
		margin-right: 0px;
		padding:10px 38.1%;
		font-size:16px;
		color: #000;
		background-color: #f9f9f9;
	}
	#supportFaq #myTabs a:hover
	{
		background-color: #CFEDC5;
	}
    #supportFaq #myTabs .active a
	{
		border-radius:0px;
		background-color:#7EC247;
		-moz-border-bottom-colors: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		color: #fff;
		font-size:16px;
		border-color: #ddd #ddd transparent;
		border-image: none;
		border-style: solid;
		border-width: 1px;		
		cursor: default;
		padding:9px 38.1%;
	}
	.panel-heading
	{
		text-align:left;
		border-bottom: 0px solid transparent;
		border-top-left-radius: 0px;
		border-top-right-radius: 0px;
		padding: 6px 15px;
	}
	.panel-body
	{
		text-align:left;
	}
	.faqHeader {
        font-size: 27px;
        margin: 20px;
    }
	.panel
	{
		background-color: #fff;
		border: 1px solid #F1F1F1;
		border-radius: 0px;
		box-shadow: 0 0px 0px rgba(0, 0, 0, 0.05);
		margin-bottom: 20px;
	}
    .panel-heading [data-toggle="collapse"]:after {
        font-family: 'Glyphicons Halflings';
        content: "\e072"; /* "play" icon */
        float: right;
        color: #6BB454;
        font-size: 18px;
        line-height: 45px;
        /* rotate "play" icon from > (right arrow) to down arrow */
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transform: rotate(-90deg);
    }

    .panel-heading [data-toggle="collapse"].collapsed:after {
        /* rotate "play" icon from > (right arrow) to ^ (up arrow) */
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg);
        color: #CFEDC5;
    }
</style>