<?php
    function add_css($file){
        echo "<link rel='stylesheet' href='${file}?i=".filemtime($file)."'>";
    }

    function add_script($file){
        echo "<script src='${file}?i=".filemtime($file)."'></script>";
    }

    function add_all_scripts($folder){
        $files = glob($folder.'/*.{js}', GLOB_BRACE);
        foreach($files as $file) {
            add_script($file);
        }
    }
?>