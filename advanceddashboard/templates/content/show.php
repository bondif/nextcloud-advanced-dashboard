<?php
    style('advanceddashboard', 'style');
?>
<div class="active-users-container">
    <h1>Active Users Here :</h1>
    <p>There are <b><?= $active_users ?></b> active users between  <b><?= date('Y-m-d H:i', $start_time) ?></b> and  <b><?= date('Y-m-d H:i', $end_time) ?></b> </p>
</div>