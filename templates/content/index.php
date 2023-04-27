<?php 
    script('advanceddashboard', 'Chart.min');
    style('advanceddashboard', 'jquery.dataTables');
    script('advanceddashboard', 'jquery.dataTables');
?>
<div class="content-container ">
    <div class="totalSahredFiles">
    <div class="col col-6 col-m-12 files-count-card">
            <h2>
            <svg xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="1.5" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 links-shared-icon">
                <path fill-rule="evenodd" d="M19.902 4.098a3.75 3.75 0 00-5.304 0l-4.5 4.5a3.75 3.75 0 001.035 6.037.75.75 0 01-.646 1.353 5.25 5.25 0 01-1.449-8.45l4.5-4.5a5.25 5.25 0 117.424 7.424l-1.757 1.757a.75.75 0 11-1.06-1.06l1.757-1.757a3.75 3.75 0 000-5.304zm-7.389 4.267a.75.75 0 011-.353 5.25 5.25 0 011.449 8.45l-4.5 4.5a5.25 5.25 0 11-7.424-7.424l1.757-1.757a.75.75 0 111.06 1.06l-1.757 1.757a3.75 3.75 0 105.304 5.304l4.5-4.5a3.75 3.75 0 00-1.035-6.037.75.75 0 01-.354-1z" clip-rule="evenodd" />
            </svg>

                <?php p($l->t('Total Shared Links')); ?>
            </h2>
            <div class="infobox">
                <?=$totalSahredFiles ?> Links Shared
            </div>
    </div>
    <div class="col col-6 col-m-12 files-count-card">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" version="1.1" viewBox="0 0 16 16"><circle cx="3.5" cy="8" r="2.5"/><circle cy="12.5" cx="12.5" r="2.5"/><circle cx="12.5" cy="3.5" r="2.5"/><path d="m3.5 8 9 4.5m-9-4.5 9-4.5" stroke="#000" stroke-width="2" fill="none"/></svg>
                <?php p($l->t('Total Internal Shares')); ?>
            </h2>
            <div class="infobox">
                <?=$totalInternalShares ?> Internal Shares
            </div>
    </div>
    </div>
    
    <div class="col col-6 col-m-12 files-count-card">
        <h2>
            <img class="infoicon" src="<?php p(image_path('core', 'places/files.svg')); ?>">
            <?php p($l->t('Top Users have a large number of files')); ?>
        </h2>
        <div class="infobox">
            <div class="shares-wrapper">
            <div class="chart-container">
                <canvas data-files-count="<?php p(json_encode($topUsersByFilesCount)) ?>"
                        class="barchart"
                        id="files-count"
                        style="width:100%; max-height:300px"
                        width="300" height="300"
                ></canvas>
            </div>
        </div>
    </div>
</div>
<div class="content-container ">
    <div class="col col-6 col-m-12 files-count-card">
        <h2>
            <img class="infoicon" src="<?php p(image_path('core', 'actions/quota.svg')); ?>">
            <?php p($l->t('Top Users have a lot of space used')); ?>
        </h2>
        <div class="infobox">
            <div class="shares-wrapper">
            <div class="chart-container">
                <canvas data-used-space="<?php p(json_encode($topUsersByUsedSpace)) ?>"
                        class="barchart"
                        id="used-space"
                        style="width:100%; max-height:300px"
                        width="300" height="300"
                ></canvas>
            </div>
        </div>
    </div>
</div>
<div class="table_container">
    <table class="table" id="dataTable">
        <thead>
            <tr>
                <td>display  Name</td>
                <td>Used Space</td>
                <td>Files </td>
                <td>Links Shared </td>
                <td>Internal Shares</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row): ?>
            <tr>
                <td scope="row"><?= $row['displayName'] ?></td>
                <?php if($row['usedSpace'] < 1024){ ?>
                    <td><?=$row['usedSpace'] . " B";?></td>
                <?php } elseif($row['usedSpace'] < 1048576){?>
                    <td><?= round($row['usedSpace'] / 1024, 2). " KB";?></td>
                <?php } elseif($row['usedSpace'] < 1073741824){?>
                    <td><?= round($row['usedSpace'] / 1048576, 2). " MB";?></td>
                <?php } elseif($row['usedSpace'] < 1099511627776){?>
                    <td><?= round($row['usedSpace'] / 1073741824, 2). " GB";?></td>
                <?php } else{?>
                    <td><?= round($row['usedSpace'] / 1099511627776, 2). " TB";?></td>
                <?php } ?>
            <td><?=$row['filecount']?></td>
            <td><?=$row['linkscount']?></td>
            <td><?=$row['linkscountbayemail']?></td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>