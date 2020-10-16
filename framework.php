<?php
    function add_css($file){
        echo "<link rel='stylesheet' href='${file}?i=".filemtime($file)."'>";
    }

    function add_script($file){
        echo "<script src='${file}?i=".filemtime($file)."'></script>";
    }
?>