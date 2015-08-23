<div class="block">
    <h2 class="section">Paid Statistics</h2>

    <?php
    $total = 0;
    foreach ($stat as $value):
        ?>
        <fieldset class="top">
            <label style="font-size: 20px; font-weight: bold;" ><?php echo $user_type[$value->user_type_paid]; ?></label>
            <div style="margin-top:10px; font-size: 20px; font-weight: bold; cursor:pointer"  class="user_full_stat" id="<?php echo $user_type[$value->user_type_paid]; ?>_full">
                <?php echo $value->countUsers ?>
            </div>
        </fieldset>
        <?php
        $total = $total + $value->countUsers;
        unset($user_type[$value->user_type_paid]);
        ?>
    <?php endforeach; ?>
    <?php foreach ($user_type as $value): ?>
        <fieldset class="top">
            <label  style="font-size: 20px; font-weight: bold;" for="required_field"><?php echo $value; ?></label>
            <div style="margin-top:10px; font-size: 20px; font-weight: bold;">
                0
            </div>
        </fieldset>
    <?php endforeach; ?>
    <fieldset class="top">
        <label  style="font-size: 20px; font-weight: bold;" for="required_field">Total</label>
        <div style="margin-top:10px; font-size: 20px; font-weight: bold;">
            <?php echo $total ?>
        </div>
    </fieldset>
</div>