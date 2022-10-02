<?php

return
[
	'views_style_directory'=> 'default-theme',
	'separate_style_according_to_actions' =>
    [
        'index'=>
        [
            'extends'=>'default',
            'section'=>'content'
        ],
        'create'=>
        [
            'extends'=>'default',
            'section'=>'content'
        ],
        'edit'=>
        [
            'extends'=>'default',
            'section'=>'content'
        ],
        'show'=>
        [
            'extends'=>'default',
            'section'=>'content'
        ],
    ],

];
