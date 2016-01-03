<style>
    .cwd-tr-bg-color-1{
        background-color: #F9F9F9;
    }

    #box-table th {
        color: #21759B;
        font-size: 13px;
        font-weight: bold;
        padding: 8px;
    }
</style>
<div class="wrap">
    <h2>Support Contacts</h2>

    <div class="tablenav">
        <div class='tablenav-pages'>
            <?php if(isset($p)): ?>
            <?php echo $p->show();  // Echo out the list of paging. ?>
            <?php endif; ?>
        </div>
    </div>

    <table class="widefat" id="box-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>          
                <th>School</th>
                <th>Preferred Time</th>
                <th>Question</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $contacts = $wpdb->get_results("SELECT * FROM mirrormx_customer_contact order by id DESC $limit");

            if (count($contacts) > 0) {
                $count = 1;
                foreach ($contacts as $value) {
                    ?>
                    <tr  class="cwd-tr-bg-color-<?php echo (int) ($count % 2);
            $count++ ?>">
                        <td><?php echo $value->name; ?></td>
                        <td><?php echo $value->email; ?></td>
                        <td><?php echo $value->school; ?></td> 
                        <td><?php echo $value->start_time; ?> - <?php echo $value->end_time; ?></td>
                        <td><?php echo $value->question; ?></td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td colspan="5">No Record Found!</td>
                <tr>  
                <?php } ?>
        </tbody>
    </table>
</div>