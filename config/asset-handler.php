<?php
return [

    /*
    |----------------------------------------------------------------
    | Asset containers.
    |----------------------------------------------------------------
    |
    | Asset containers to be loaded.
    |
    */
    "containers" => array(
        "images" => [
            "print_pattern" => '<img src="{{URL}}">',
            "file_regex"    => "/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i",
            "path"          => "/public/assets/images",
            "url"           => "/assets/images",
            "versioned"     => false
        ],
        "scripts" => [
            "print_pattern" => '<script src="{{URL}}" type="application/javascript"></script>',
            "file_regex"    => "/\\.js$/i",
            "path"          => "/public/assets/scripts",
            "url"           => "/assets/scripts",
            "versioned"     => false
        ],
        "styles" => [
            "print_pattern" => '<link rel="stylesheet" type="text/css" href="{{URL}}" title="{{NAME}}">',
            "file_regex"    => "/\\.css$/i",
            "path"          => "/public/assets/styles",
            "url"           => "/assets/styles",
            "versioned"     => false
        ],
        //
        // Add your own containers here.
        //
    )
];
