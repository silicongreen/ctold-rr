<div class="block">
    <h2 class="section">Paid Statistics</h2>

    <table   class="table table-bordered" style="width: 100%;">
        <thead>
            <tr class="even"><th>User Type</th><th>User</th><th>Session</th><th>Session Time</th></tr>
        </thead>
        <?php
        $total = 0;
        $totalsession = 0;
        $totaltime = 0;
        foreach ($stat as $value):
            ?>

            <tr class="even"><td><?php echo $user_type[$value->user_type_paid]; ?></td><td> <a href="javascript:void(0)" class="user_full_stat" id="<?php echo $value->user_type_paid; ?>_full_stat"><?php echo $value->countUsers ?></td>
                <td> <?php echo $value->snumber ?></td><td><?php echo $value->stime ?></td></tr>
            <?php
            $total = $total + $value->countUsers;
            $totalsession = $totalsession + $value->snumber;
            $totaltime = $totaltime + $value->stime;
            unset($user_type[$value->user_type_paid]);
            ?>
        <?php endforeach; ?>
        <?php foreach ($user_type as $value): ?>
            <tr class="even"><td>0</td><td>0</td>
                <td>0</td><td>0</td></tr>
        <?php endforeach; ?>
        <tr class="even"><td>All</td><td> <?php echo $total ?></td>
            <td> <?php echo $totalsession ?></td><td><?php echo $totaltime ?></td></tr>

    </table>


</div>>