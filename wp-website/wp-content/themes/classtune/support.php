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
Template Name: supports
*/
get_header(); ?>

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
				<div class="item pricing" id="supportItems" style="margin-top:0px;">					
					<div class="row" style="margin:0px;">
						
						<div class="col-md-12">
							<h2 style="margin-top: 40px;" class="f2"><i>Browse Our Knowledge Libreries</i></h2>
						</div>
						<div class="col-md-6 col-sm-offset-3"  style="margin-top: 40px;">					
							<a href="<?php echo get_site_url(); ?>/faq">
								<button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-question"></i>FAQ</button>
							</a>
							<a href="<?php echo get_site_url(); ?>/user-manual">
								<button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-book"></i>User Manual</button>
							</a>
							<!--button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-play-circle-o"></i>Tutorial Video</button-->
						</div>
						<!--div class="col-md-6 col-sm-offset-3"  style="">					
							
							<button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-lightbulb-o "></i>Traning Module</button>
						</div-->
						<div class="col-md-6 col-sm-offset-3"  style="">	
							<a href="<?php echo get_site_url(); ?>/faq-mobile">
								<button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-mobile"></i>Mobile App</button>
							</a>
							<a href="<?php echo get_site_url(); ?>/troubleshooting">
								<button type="button" class="btn btn-default btn-lg"><i style="color:#000;" class="fa fa-cogs"></i>Troubleshooting</button>
							</a>
						</div>
					</div>			
				</div><!-- /.item -->  
				<div class="item pricing" id="supportMore" style="margin-top:0px;">					
					<div class="row" style="margin:0px;">
						
						<div class="col-md-12">
							<h2 style="margin-top: 40px;margin-bottom: 80px;" class="f2"><i>Need More Help?</i></h2>
						</div>
						<div class="col-md-8 col-sm-offset-2">
							<div class="col-md-4 col-sm-offset-0"  style="">
								<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-weixin"></i><br>Live Chat</button>
							</div>
							<div class="col-md-4 col-sm-offset-0"  style="">
								<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-headphones"></i><br>Hotline</button>
							</div>
							<div class="col-md-4 col-sm-offset-0"  style="">
								<a href="<?php echo get_site_url()."?locale=contact"; ?>">
									<button type="button" class="btn btn-default fa-a btn-lg"><i style="color:#000;" class="fa fa-paper-plane-o"></i><br>Contact Us</button>
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
