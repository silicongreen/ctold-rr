<div class="modal fade" id="classModal" tabindex="-1" role="dialog" aria-labelledby="ClassModalLable" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="ClassModalLable">Add New</h4>
            </div> <!-- /.modal-header -->

            <div class="modal-body">
                <form role="form">
                    <div class="form-group class_name_wrapper"></div>
                    <div class="form-group class_name_check_box_id" id=""></div>

                    <div class="form-group">
                        <div class="input-group col-lg-12">
                            <input type="text" class="form-control" id="class_name_txt_box" placeholder="Class Name" value="" />
                            <label for="class_name_txt_box" class="input-group-addon glyphicon glyphicon-plus"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group col-lg-12">
                            <input type="text" class="form-control" id="section_name_txt_box" placeholder="Section Names" value="" />
                            <label for="section_name_txt_box" class="input-group-addon glyphicon glyphicon-plus"></label>
                        </div>
                        <p>Comma separated value</p>
                    </div>
                </form>
            </div> <!-- /.modal-body -->

            <div class="modal-footer">
                <button class="form-control btn btn-primary" id="add_new_class">OK</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.default modal -->