
                </div>
            </div>
            <!-- END MAIN CONTENT-->
            <footer class="footer">
                <div class="container-fluid">
                <?php
                    $res=$DB->query("select * from $dbprefix"."page where page_bmenu='1' and page_pack like '%;".$_SESSION["PackID"].";%' order by page_order");
                    if(sizeof($res)){
                ?>
                    <nav class="pull-left">
                        <ul>
                    <?php
                        foreach($res as $row){
                    ?>
                            <li><a href="index.php?cmd=page&id=<?php echo $row["page_id"];?>"><?php echo $row["page_title"];?></a></li>
                    <?php
                        }
                    ?>
                        </ul>
                    </nav>
                <?php
                    }
                ?>
                    <p class="copyright pull-right">&copy; <?php echo date("Y")." ".$WEBSITE["sitename"];?>. All rights reserved.</p>
                </div>
            </footer>
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <?php include("scripts.php"); ?>
    
</body>

</html>
<!-- end document-->
