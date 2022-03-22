<div class="popup-container hide">
    <div class="close-icon"> x </div>
        
        <!--For first page-->
        <div class="first-page">
            <div class="avatar-container">
                <img src="../../upload/<?php echo $campaign['user_id']; ?>/popup/<?php echo $popup_data[0]["avatar_url"]; ?>" />
            </div>
            
            <div class="name-container">
                <?php echo $popup_data[0]["name"]; ?>
            </div>
            
            <div class="question-container">
                <?php echo $popup_data[0]["question"]; ?>
            </div>
            
            <div class="btn-container">
                <a href="#yes" id="btn-yes" class="btn-success">YES</a>
                <a href="#no" id="btn-no" class="btn-danger">NO</a>
            </div>
        </div>
        
        <!--For Second page-->
        <div class="second-page hide">
            <div class="popup-description">
                <?php echo $popup_data[0]["description"]; ?>
            </div>
            
            <div class="popup-sub-description">
                <?php echo $popup_data[0]["sub_description"]; ?>
            </div>
            
            <div class="second-image-container">
                <img src="../../upload/<?php echo $campaign['user_id']; ?>/popup/<?php echo $popup_data[0]["second_image_url"]; ?>" />
            </div>
            
            <div class="second-btn-container">
                <a href="<?php echo $popup_data[0]["button_link"]; ?>" class="btn-success">Start Now!</a>
            </div>
        </div>
</div>