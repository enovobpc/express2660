<?php

    $layout = 'layouts.master';

    if(!hasModule('website')){
        $layout = 'layouts.default';
    }

    $hideSidebar = false;
    /*if(config('app.source') == 'utiltrans' && in_array($auth->id, [6,10])) {
        $hideSidebar = true;
    }*/
?>
@extends($layout)

@section('content')
    @if(hasModule('website'))
        @include('website.partials.account_header')
    @endif
    <section class="account-section">
        <div class="container account-container">
            <div class="row">
                @if(!$hideSidebar)
                <div class="col-xs-12 col-md-2 account-left-panel">
                    <div class="account-sidebar">
                        @include('account.partials.sidebar')
                    </div>
                    @if(Setting::get('customers_show_support_phone') && (@$auth->agency->mobile || @$auth->agency->phone))
                    <div class="customer-support hidden-xs">
                        <span class="agency">{{ trans('account/global.word.customer-support') }}</span>
                        <div class="contact">
                            <span class="icon"><i class="fas fa-phone"></i></span>
                            <?php $auth->agency->phone ?? $auth->agency->mobile ?>
                            @if(strlen(str_replace(' ', '', $auth->agency->phone)) == '9')
                                <span class="number">{{ phone_format(str_replace(' ', '', $auth->agency->phone)) }}</span>
                            @else
                                <span class="number extense">{{ $auth->agency->phone }}</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!env('APP_HIDE_CREDITS'))
                        <p class="account-credit">
                            {!! app_brand() !!}
                        </p>
                        <div class="spacer-50"></div>
                    @endif
                </div>
                <div class="col-xs-12 col-md-10 account-right-panel">
                    <div class="account-main-panel">
                        @yield('account-content')
                    </div>
                </div>
                @else
                    <div class="col-xs-12 account-right-panel" style="width: 100%">
                        <div class="account-main-panel">
                            @yield('account-content')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <div class="account-container">
        @include('account.partials.modals.remote_md')
        @include('account.partials.modals.remote_lg')
        @include('account.partials.modals.remote_xl')
        @include('account.partials.modals.remote_xs')
    </div>
    <div class="spacer-50"></div>
    @if($hideSidebar)
    @endif

    @if($hideSidebar)
        <style>
            .modal-xl {
                position: fixed;
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
                margin: 0;
                width: 100% !important;
                background: #fff;
            }

            .modal-footer {
                position: fixed;
                bottom: 0;
                right: 0;
                left: 0;
            }

            .modal-content {
                box-shadow: none;
                border: none;
            }

            .modal-shipment .panel-heading h4 {
                background: transparent;
                border: none;
            }

            .modal-shipment .panel-heading h4:before,
            .modal-shipment .panel-heading h4:after {
                display: none;
            }


            .modal-shipment .panel-default > .panel-heading {
                background: #999;
            }
            .extra-options {
                display: none;
            }
        </style>
    @endif
@stop