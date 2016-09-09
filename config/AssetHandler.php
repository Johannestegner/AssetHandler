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
            "print_pattern" => '<img src="{{PATH}}">',
            "file_regex"    => "/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i",
            "path"          => "/public/assets/images",
            "url"           => "/assets/images",
            "versioned"     => false
        ],
        "scripts" => [
            "print_pattern" => '<script src="{{PATH}}" type="application/javascript"></script>',
            "file_regex"    => "/\\.js$/i",
            "path"          => "/public/assets/scripts",
            "url"           => "/assets/scripts",
            "versioned"     => true
        ],
        "styles" => [
            "print_pattern" => '<link rel="stylesheet" type="text/css" href="{{PATH}}" title="{{NAME}}">',
            "file_regex"    => "/\\.css$/i",
            "path"          => "/public/assets/styles",
            "url"           => "/assets/styles",
            "versioned"     => true
        ],
        //
        // Add your own custom containers here.
        //
    )
];
