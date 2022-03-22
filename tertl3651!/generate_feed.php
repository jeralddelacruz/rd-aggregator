<?php
    function testMsg() {
      echo "test";
    }
    echo '<script type="text/javascript">
        getFeed('.$content_data['user_id'].','.$content_data['content_id'].',"'.$content_data['news_link'].'","'.$dbprefix.'");
     </script>';
?>