

<div class="form-group">		
    <h4 class="col-lg-12 no-padding">Setup Classes</h4>		
</div>		

<div class="clearfix"></div>		

<div class="col-lg-5 panel panel-default">		
    <?php for ($i = 1; $i <= 10; $i++) { ?>		

        <div class="form-group class_wrapper">		
            <label class="col-lg-12">		
                <input name="checkbox" class="class_checkbox checkbox-inline right-margin-10" type="checkbox" value="Class <?php echo $i; ?>">Class <?php echo $i; ?>		
            </label>		
        </div>		

    <?php } ?>		
</div>		

<div class="col-lg-5 panel panel-default" id="extra_vaules_classes">		
    <table class="table table-hover table-condensed table-bordered table-responsive">		
        <thead>		
            <tr>		
                <th>Class Name</th>		
                <th></th>		
            </tr>		
        </thead>		
        <tbody>		

        </tbody>		
    </table>		
</div>