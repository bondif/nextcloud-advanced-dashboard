<?php 
    style('advanceddashboard', 'jquery.dataTables');
    script('advanceddashboard', 'jquery.dataTables');
?>
<div class="content-container">
<br>
<div class="daterangeinputs">
    <form method="POST" action="/apps/advanceddashboard/filesbytime/post">
        <span>
            <label for="start_date">Start date:</label>
            <input type="datetime-local" id="start_date" name="start_date">
        </span>
        <span>
            <label for="end_date">End date:</label>
            <input type="datetime-local" id="end_date" name="end_date">
        </span>
        <button type="submit">Submit</button>
    </form>
</div>
    
<br>
    <p><?php echo $params['message']; ?></p>


<?php 
    if(!empty($users)){
?>
<div class="table_container">
    <table class="table"  id="dataTable">
        <thead>
            <tr>
                <td>display  Name</td>
                <td>Number of Created Files</td>
                <td>Number of Uploaded Files</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
            <td scope="row">
                <?= $user['displayName'] ?>
            </td>
            <td><?=$user['createdfilescount']?></td>
            <td><?=$user['uploadedfilescount']?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
		<th></th>
                <th>Total:</th>
                <th>Total: </th>
            </tr>
        </tfoot>
    </table>
</div>
<?php 
    }else{
?>
<div class="errore-please-date">
    <!-- <h2>Please Enter The Date Range</h2> -->
    <div>
        <span style="--i:1">Please&nbsp;</span>
        <span style="--i:2">Enter &nbsp;</span>
        <span style="--i:3">The&nbsp;</span>
        <span style="--i:4">Date &nbsp;</span>
        <span style="--i:5">Range </span>
    </div>  
</div>
<?php 
    }
?>
</div>
