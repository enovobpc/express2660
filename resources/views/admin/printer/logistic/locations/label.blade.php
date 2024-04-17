<div class="adhesive-label">
    <div style="height: 10mm"></div>
    <div style="margin: 0 12mm">
        <div style="float: left; width: 50%">
            <img src="{{ asset('assets/img/logo/logo_sm.png') }}" style="height: 45px;" class="m-t-10"/>
        </div>
        <div style="float: right; width: 49%; margin-top: -7px">
            <div style="text-align: right">
                <img src="{{ $qrCode }}" style="height: 70px"/>
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
    @if(0)
        <?php $down = 0; ?>
        <div class="text-center" style="width: 110mm; float: left">
            <div style="height: 8mm"></div>
            <div style="display: inline-block;">
                <barcode code="{{ $location->barcode }}" type="C128A" size="1.9" height="1"/>
            </div>
            <div class="fs-75 bold text-center m-t-40 text-uppercase">
                {{ $location->code }}
            </div>
        </div>
        <div style="width: 30mm; float: left; margin-top: 10mm; text-align: left; margin-left: -5mm">
            @if(@$down)
                <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik00NDYuOTA2LDI2MS45NjljLTEuNzkyLTMuNjU2LTUuNS01Ljk2OS05LjU3My01Ljk2OWgtOTZWMTAuNjY3QzM0MS4zMzMsNC43NzEsMzM2LjU2MywwLDMzMC42NjcsMEgxODEuMzMzDQoJCQljLTUuODk2LDAtMTAuNjY3LDQuNzcxLTEwLjY2NywxMC42NjdWMjU2aC05NmMtNC4wNzMsMC03Ljc4MSwyLjMxMy05LjU3Myw1Ljk2OWMtMS43OTIsMy42NDYtMS4zNTQsOCwxLjEzNSwxMS4yMTkNCgkJCWwxODEuMzMzLDIzNC42NjdjMi4wMjEsMi42MTUsNS4xMzUsNC4xNDYsOC40MzgsNC4xNDZzNi40MTctMS41MzEsOC40MzgtNC4xNDZsMTgxLjMzMy0yMzQuNjY3DQoJCQlDNDQ4LjI2LDI2OS45NjksNDQ4LjY5OCwyNjUuNjE1LDQ0Ni45MDYsMjYxLjk2OXoiLz4NCgk8L2c+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8L3N2Zz4NCg==" />
            @else
                <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE5LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPg0KPHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJDYXBhXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMjsiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGc+DQoJPGc+DQoJCTxwYXRoIGQ9Ik00NDUuNzcxLDIzOC44MTNMMjY0LjQzOCw0LjE0NmMtMi4wMTktMi42MTItNS4yMjMtNC4xNDMtOC40MjgtNC4xNDZjLTMuMjExLTAuMDAzLTYuNDI0LDEuNTI5LTguNDQ3LDQuMTQ2DQoJCQlMNjYuMjI5LDIzOC44MTNjLTIuNDksMy4yMTktMi45MjcsNy41NzMtMS4xMzUsMTEuMjE5YzEuNzkyLDMuNjU2LDUuNSw1Ljk2OSw5LjU3Myw1Ljk2OWg5NnYyNDUuMzMzDQoJCQljMCw1Ljg5Niw0Ljc3MSwxMC42NjcsMTAuNjY3LDEwLjY2N2gxNDkuMzMzYzUuODk2LDAsMTAuNjY3LTQuNzcxLDEwLjY2Ny0xMC42NjdWMjU2aDk2YzQuMDczLDAsNy43ODEtMi4zMTMsOS41NzMtNS45NjkNCgkJCUM0NDguNjk4LDI0Ni4zODUsNDQ4LjI2LDI0Mi4wMzEsNDQ1Ljc3MSwyMzguODEzeiIvPg0KCTwvZz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjxnPg0KPC9nPg0KPGc+DQo8L2c+DQo8Zz4NCjwvZz4NCjwvc3ZnPg0K" />
            @endif
        </div>
    @else
    <div class="text-center">
        <div style="height: 8mm"></div>
        <div style="display: inline-block;">
            <barcode code="{{ $location->barcode }}" type="C128A" size="1.9" height="1"/>
        </div>
        <div class="fs-75 bold text-center m-t-40 text-uppercase">
            {{ $location->code }}
        </div>
    </div>
    @endif
</div>