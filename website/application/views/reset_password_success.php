<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
                <div style="margin:30px 100px; float:left; width:80%;background: white; ">

                    <div class="col-md-12"  style="padding:0px; ">
                        <?php if ($this->session->flashdata('success')) { ?>
                            <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                                <?php echo $this->session->flashdata('success'); ?>
                            </h2>
                        <?php } ?>
                    </div>

                    <div class="clearfix"></div>


                </div>

            </div> 
        </div>      

    </div><!-- /.container -->

</div><!-- /.wrapper -->


<style type="text/css">
    .alert-danger {
        margin-top: 5px;
    }
</style>