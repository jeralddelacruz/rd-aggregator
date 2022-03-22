    
                            </div>
                            <!-- END PAGE CONTENT-->

                            <!-- FOOTER CONTENT-->
                            <footer id="theme-3-footer">
                                <?php
                                    $res=$DB->query("select * from $dbprefix"."page where page_bmenu='1' and page_pack like '%;".$_SESSION["PackID"].";%' order by page_order");
                                ?>
                                <?php if(sizeof($res)): ?>
                                    <nav>
                                        <ul>
                                            <?php foreach($res as $row):?>
                                                <li>
                                                    <a href="index.php?cmd=page&id=<?= $row["page_id"];?>">
                                                        <?= $row["page_title"];?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                                <p class="copyright">&copy; <?= date("Y")." ".$WEBSITE["sitename"];?>. All rights reserved.</p>
                            </footer>
                            <!-- END FOOTER-->

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- END PAGE CONTENT  -->
    </div>

    <?php include('scripts.php'); ?>
</body>

</html>
<!-- end document-->