<div class="block">
    <h2 class="section">Paid Statistics &nbsp;&nbsp;<span class="loading-msg">(Loading Data This will take some time...)</span></h2>

    <div class="CSSTableGenerator" >
        <table   style="width: 100%;">

            <tr class="even"><td>User Type</td><td>User</td><td>Session</td><td>Session Time (Min)</td></tr>

            <?php
            $total = 0;
            $totalsession = 0;
            $totaltime = 0;
            foreach ($stat as $value):
                ?>

                <tr class="even"><td><?php echo $user_type[$value->user_type_paid]; ?></td><td> <a href="javascript:void(0)" class="user_full_stat" id="<?php echo $value->user_type_paid; ?>_full_stat"><?php echo $value->countUsers ?></td>
                    <td> <?php echo $value->snumber ?></td><td><?php echo round($value->stime / 60); ?></td></tr>
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
                <td> <?php echo $totalsession ?></td><td><?php echo round($totaltime/60); ?></td></tr>

        </table>
    </div> 


</div>