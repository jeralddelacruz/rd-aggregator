<?php
    $id = $_GET['id'];

    if($id&&(!$row=$DB->info("prb","prb_id='$id'"))){
        redirect("index.php?cmd=cbpr");
    }

    if(isset($_POST['btn_eswipe'])){
        if(isset($_POST["subject"]) && isset($_POST["body"])){
            $subject = strip($_POST["subject"]);
            $body = strip($_POST["body"], 0);
            $DB->query("UPDATE $dbprefix"."prlist SET prlist_esubj='$subject', prlist_ebody='$body' WHERE user_id='$UserID' AND pr_id='$id'");

            $_SESSION['msg'] = 'Email swipe successfully save.';
            redirect("index.php?cmd=cbpr");
        }else{
            $error = "Subject and Content fields are required.";
        }
    }elseif (isset($_POST['btn_afflink'])) {
        if(isset($_POST["affiliate_link"])){
            $link = strip($_POST["affiliate_link"]);
            $DB->query("UPDATE $dbprefix"."prlist SET prlist_affiliate_link='$link' WHERE user_id='$UserID' AND pr_id='$id'");
            $_SESSION['msg'] = 'Affiliate Link successfully save.';
            redirect("index.php?cmd=cbpr");
        }else{
            $error = "Affiliate Link field is required.";
        }
    }

    $pr_str="";
    $prb_arr=$DB->get_pack();

    $prbRes=$DB->query("SELECT * from $dbprefix"."prb WHERE prb_id='$id' LIMIT 1");
    $prlistRes=$DB->query("SELECT * from $dbprefix"."prlist WHERE user_id='$UserID' AND pr_id='$id' LIMIT 1");
    $affiliate_link = isset($prlistRes[0]['prlist_affiliate_link']) ? $prlistRes[0]['prlist_affiliate_link'] : '';
    $subject = isset($prlistRes[0]['prlist_esubj']) ? $prlistRes[0]['prlist_esubj'] : '';
    $body = isset($prlistRes[0]['prlist_ebody']) ? $prlistRes[0]['prlist_ebody'] : '';
?>

<h2><?php echo $index_title;?>- <em><?php echo $prbRes[0]['prb_title']; ?></em><a href="index.php?cmd=cbpr" class="btn btn-default" style="float:right"> Back to CB Products</a></h2>

<?php if($error){ ?> <div class="alert alert-danger"><?php echo $error;?></div> <?php $error = '';} ?>
    <div class="row">
        <div class="col-md-12">
            <form method="post">
                <div class="content">
                    <div class="card" style="padding: 15px;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><h4 class="title">Email Swipe</h4></label>
                                </div>

                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" name="subject" value="<?php echo $subject; ?>" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="body">Content</label>
                                    <textarea name="body"class="tinymce"><?php echo $body; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for=""><h4 class="title">Affiliate</h4></label>
                                </div>

                                <div class="form-group">
                                    <label for="affiliate_link">Affiliate Link</label>
                                    <input type="text" id="affiliate_link" value="<?php echo $affiliate_link; ?>"  name="affiliate_link" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button name="btn_eswipe" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill"type="submit">Save Email Swipe Changes</button>
                                <button name="btn_afflink" class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill"type="submit">Save Affiliate Link Changes</button>
                                <a class="btn btn-<?php echo $WEBSITE["theme_btn"];?> btn-fill" href="index.php?cmd=cbpr">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="../tinymce/tinymce.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            tinymce.init({
                selector:".tinymce",
                height:400,
                theme:"modern",

                plugins:["advlist autolink lists link image media emoticons charmap preview hr anchor searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking save table contextmenu directionality paste textcolor colorpicker fullpage"],

                toolbar1:"fontselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | removeformat",
                toolbar2:"alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | code fullpage preview",

                fontsize_formats:"8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px 36px 48px 72px",

                relative_urls:false,
                remove_script_host:false,

                external_filemanager_path:"../tinymce_fm/",
                external_plugins:{"filemanager":"../tinymce_fm/plugin.min.js"},
                filemanager_title:"File Manager",
                filemanager_sort_by:"name",
                filemanager_descending:true,
                valid_elements:"*[*]"
            });
        });
    </script>