<?php $bgExists = File::exists(public_path() . '/uploads/pdf/bg_v.png'); ?>
@if($bgExists)
    <htmlpageheader name="pageHeader" class="header">
        @if($documentTitle)
            <h1 class="text-uppercase text-right document-title lh-1-3" style="margin-right: 8mm; margin-top: {{ $documentSubtitle ? '-5mm' : '-3mm' }}">
                <b style="color: #000;  font-size: 18px">{{ $documentTitle }}</b><br/>
                <small style="font-size: 13px">{!! @$documentSubtitle !!}</small>
            </h1>
        @endif
    </htmlpageheader>
@else
    @if(@$customPageHeader)
        <header name="pageHeader" class="header" style="padding-top:1px">
            <div style="background: {{ env('APP_MAIL_COLOR_PRIMARY') }}; height: 5mm; margin-top:-7.8em"></div>
            @if(File::exists(public_path() . '/assets/img/logo/logo_sm.png'))
                <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
            @else
                <img src="{{ asset('assets/img/default/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
            @endif
            @if($documentTitle)
                <h1 class="text-uppercase text-right document-title lh-1-3" style="margin-right: 8mm; margin-top: {{ $documentSubtitle ? '-5mm' : '-3mm' }}">
                    <b style="color: #000;  font-size: 18px">{{ $documentTitle }}</b><br/>
                    <small style="font-size: 13px">{!! @$documentSubtitle !!}</small>
                </h1>
            @endif
        </header>
    @else
    <htmlpageheader name="pageHeader" class="header">
        <div style="background: {{ $invoice->customer->agency->color ?? env('APP_MAIL_COLOR_PRIMARY') }}; height: 5mm;"></div>
        @if (!empty($invoice->provider->agency) && !empty($invoice->provider->agency->filepath) && File::exists(public_path() . '/' . $invoice->provider->agency->filepath))
            <img src="{{ asset($invoice->provider->agency->filepath) }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
        @elseif (!empty($invoice->customer->agency) && !empty($invoice->customer->agency->filepath) && File::exists(public_path() . '/' . $invoice->customer->agency->filepath))
            <img src="{{ asset($invoice->customer->agency->filepath) }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
        @elseif(File::exists(public_path() . '/assets/img/logo/logo_sm.png'))
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
        @else
            <img src="{{ asset('/assets/img/default/logo/logo_sm.png') }}" style="float: left; margin: 15px 0 0 30px; max-width: 220px; max-height: 40px"/>
        @endif
        @if($documentTitle)
            <h1 class="text-uppercase text-right document-title lh-1-3" style="margin-right: 8mm; margin-top: {{ $documentSubtitle ? '-5mm' : '-3mm' }}">
                <b style="color: #000;  font-size: 18px">{{ $documentTitle }}</b><br/>
                <small style="font-size: 13px">{!! @$documentSubtitle !!}</small>
            </h1>
        @endif
    </htmlpageheader>
    @endif
@endif
