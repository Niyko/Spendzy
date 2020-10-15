<?php
    function add_css($file){
        echo "<link rel='stylesheet' href='${file}?i=".filemtime($file)."'>";
    }
?>