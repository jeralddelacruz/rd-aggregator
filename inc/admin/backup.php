<?php 
    $path = __DIR__ . '/../../tmp';
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));
    arsort($files);

    $initialFile = '';

    if(isset($_POST['backup'])){
        $backup_file = dirname(__FILE__) .'/../../tmp/' . $dbname . date("Y-m-d") . '.sql';
        $command = "mysqldump --opt -h $dbhost -u$dbuser -p$dbpass ". "$dbname > $backup_file";
        exec($command);

        // zip project //
        $rootPath = realpath(dirname(__FILE__) . '/../../');
        // Initialize archive object
        $zipName = dirname(__FILE__) . '/../../tmp/csbk_'. date('Y_m_d') . '.zip';
        $zip = new ZipArchive();
        $zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
        exec('zip -r '. $zipName . ' ' . $rootPath .' -x *.zip');
        
        redirect('index.php?cmd=backup');
    }
?>

<style>
    a.button{
        display: inline-block;
    }

    a.button:hover{
        color: #fff;
        text-decoration: none;
    }
</style>

<form method="post">
    <table class="tbl_form" style="width: 30%;">
        <thead>
            <tr>
                <th>File</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $key => $file) :
                if(strpos($file, '.zip') !== false) :
                    $initialFile = empty($initialFile) ? '/../../tmp/' . $file : $initialFile; ?>
                <tr>
                    <td><label for="pass"><?php echo $file; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', filemtime(__DIR__ . '/../../tmp/' . $file)); ?></td>
                </tr>
                <?php endif; ?> 	
            <?php endforeach; ?>

            <?php if(empty($initialFile)) : ?>
                <tr>
                    <td align="center" colspan=2>No Backup Found</td>
                </tr>
            <?php endif ?>
            <tr>
                <td><a href="<?php echo empty($initialFile) ? '#' : ''; ?>" class="button"> <?php echo empty($initialFile) ? 'No backup found' : 'Download Latest Backup'; ?></a></td>
                <td><button type="submit" name="backup" class="button">Create backup now</button></td>
            </tr>
        </tbody>
    </table>
</form>


<script>
    $('button[name=backup]').on('click', function(){
        $(this).text('Creating backup... Please Wait...');
    })
</script>