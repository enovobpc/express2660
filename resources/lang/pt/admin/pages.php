<?php

    return [

        'layouts' => [
            'layout-01' =>  'Layout 1',
            'layout-02' =>  'Layout 2',
            'layout-03' =>  'Layout 3',
            'layout-04' =>  'Layout 4',
            'layout-05' =>  'Layout 5',
            'layout-06' =>  'Layout 6',
            'layout-07' =>  'Layout 07',
            'layout-08' =>  'Layout 08',
            'layout-09' =>  'Layout 09',
            'layout-10' =>  'Layout 10',
            'layout-11' =>  'Layout 11',
            'layout-12' =>  'Layout 12',
        ],

        'layouts-templates' => [
            'layout-01' =>  [
                'blocks' => '1',
                'html'   => '<div class="row {ROW_SPACER}"><div class="col-xs-12">{BLOCK_1}</div></div>',
            ],
            'layout-02' =>  [
                'blocks' => '2',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-6">{BLOCK_1}</div>
                                <div class="col-sm-6">{BLOCK_2}</div>
                             </div>',
            ],
            'layout-03' => [
                'blocks' => '3',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">{BLOCK_1}</div>
                                <div class="col-sm-4">{BLOCK_2}</div>
                                <div class="col-sm-4">{BLOCK_3}</div>
                            </div>',
            ],
            'layout-04' => [
                'blocks' => '4',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-3">{BLOCK_1}</div>
                                <div class="col-sm-3">{BLOCK_2}</div>
                                <div class="col-sm-3">{BLOCK_3}</div>
                                <div class="col-sm-3">{BLOCK_4}</div>
                            </div>',
            ],
            'layout-05' => [
                'blocks' => '4',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">{BLOCK_1}</div>
                                <div class="col-sm-4">{BLOCK_2}</div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_3}</div>
                                        <div class="col-sm-12">{BLOCK_4}</div>
                                    </div>
                                </div>',
            ],
            'layout-06' => [
                'blocks' => '5',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">{BLOCK_1}</div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_2}</div>
                                        <div class="col-sm-12">{BLOCK_3}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_4}</div>
                                        <div class="col-sm-12">{BLOCK_5}</div>
                                    </div>
                                </div>
                             </div>',
            ],
            'layout-07' => [
                'blocks' => '5',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_1}</div>
                                        <div class="col-sm-12">{BLOCK_2}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_3}</div>
                                        <div class="col-sm-12">{BLOCK_4}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">{BLOCK_5}</div>
                             </div>',
            ],
            'layout-08' => [
                'blocks' => '4',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">{BLOCK_1}</div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_2}</div>
                                        <div class="col-sm-12">{BLOCK_3}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">{BLOCK_4}</div>
                             </div>',
            ],
            'layout-09' => [
                'blocks' => '5',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_1}</div>
                                        <div class="col-sm-12">{BLOCK_2}</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">{BLOCK_3}</div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12 margin-bottom-25">{BLOCK_4}</div>
                                        <div class="col-sm-12">{BLOCK_5}</div>
                                    </div>
                                </div>
                             </div>',
            ],
            'layout-10' =>  [
                'blocks' => '2',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-8">{BLOCK_1}</div>
                                <div class="col-sm-4">{BLOCK_2}</div>
                             </div>',
            ],
            'layout-11' =>  [
                'blocks' => '2',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-4">{BLOCK_1}</div>
                                <div class="col-sm-8">{BLOCK_2}</div>
                             </div>',
            ],
            'layout-12' => [
                'blocks' => '3',
                'html'   => '<div class="row {ROW_SPACER}">
                                <div class="col-sm-8 fa">{BLOCK_1}</div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-12">{BLOCK_2}</div>
                                        <div class="col-sm-12">{BLOCK_3}</div>
                                    </div>
                                </div>
                             </div>',
            ],
        ],

        'content-types' => [
            'image' =>  [
                'icon' => 'fa-image',
                'text' => 'Imagem'
            ],
            'thumbnail' =>  [
                'icon' => 'fa-address-card',
                'text' => 'Thumbnail'
            ],
            'text' =>  [
                'icon' => 'fa-font',
                'text' => 'Texto livre'
            ],
            'video' =>  [
                'icon' => 'fa-play',
                'text' => 'Vídeo'
            ],
            'html' =>  [
                'icon' => 'fa-code',
                'text' => 'HTML'
            ],
            'slider_products' =>  [
                'icon' => 'fa-tags',
                'text' => 'Slider Produtos'
            ],
        ],
    ];