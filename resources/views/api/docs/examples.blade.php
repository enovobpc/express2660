@section('title')
    {{ ucwords($level) }}
@stop

@section('content')
    @include('api.docs.partials.header')

    <div class="docs-wrapper">
        <div id="docs-sidebar" class="docs-sidebar">
            <nav id="docs-nav" class="docs-nav navbar">
                <ul class="section-items list-unstyled nav flex-column pb-3">
                    <li class="nav-item section-title">
                        <a class="nav-link scrollto active mt-3" href="#section-php">
                            <span class="theme-icon-holder me-2"><i class="fab fa-php"></i></span> PHP Examples
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-php-post">POST Request</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-php-get">GET Request</a>
                    </li>

                    <li class="nav-item section-title">
                        <a class="nav-link scrollto active mt-3" href="#section-docs">
                            <span class="theme-icon-holder me-2"><i class="fas fa-book"></i></span> Data Listing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-list-services">List of Services Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-list-status">List of Status Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-list-incidences">List of incidence Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scrollto" href="#item-list-resolution-types">List of resolution Types</a>
                    </li>
                    @if($level == 'partners')
                        <li class="nav-item">
                            <a class="nav-link scrollto" href="#item-list-agencies">List of Agencies</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link scrollto" href="#item-list-customer-types">List of customer Types</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link scrollto" href="#item-list-payment-conditions">List Payment Conditions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link scrollto" href="#item-list-routes">List Routes</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <div class="docs-content">
            <div class="container">
                <article class="docs-article" id="section-php">
                    <header class="docs-header">
                        <h1 class="docs-heading text-primary">
                            PHP Examples
                        </h1>
<!--                        <section class="docs-intro">
                            <p>DFSFDSF</p>
                        </section>-->
                    </header>
                    <section class="docs-section" id="item-php-post">
                        <h2 class="section-heading">POST Request</h2>
                        <p>
                            The following example allows you to make a call to our API using the POST method.<br/>
                            The example below shows the call to the "Request Token" method
                        </p>
                        <div class="docs-code-block pb-0">
                            <pre class="rounded"><code class="json hljs">
    //request variables
    $data = http_build_query($data);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL             => "https://api.XXXXXXXXXXXX.com/oauth/token",
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => "",
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 30,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => "POST",
        CURLOPT_POSTFIELDS      => $data,
        CURLOPT_HTTPHEADER      => array(
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        throw new Exception($err);
    }

    echo json_decode($response);
                                </code></pre>
                        </div>
                    </section>
                    <section class="docs-section" id="item-php-get">
                        <h2 class="section-heading">GET Request</h2>
                        <p>The following example allows you to make a call to our API using the GET method to get a list of all shipments.</p>
                        <div class="docs-code-block pb-0">
                            <pre class="rounded"><code class="json hljs">

    $url = "https://api.XXXXXXXXXXXX.com/v1/shipments/list?date=2018-10-05"

    $headers = [
        "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjQwNTQzNzMx...",
    ];

    $data = http_build_query($data);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    if ($err) {
        throw new Exception($err);
    }

    echo json_decode($response);
                                </code></pre>
                        </div>
                    </section>
                </article>
                <article class="docs-article" id="section-docs">
                    <header class="docs-header">
                        <h1 class="docs-heading text-primary">
                            Auxiliar Tables
                        </h1>
                    </header>
                    <section class="docs-section" id="item-list-services">
                        <h2 class="section-heading">List of Services Codes</h2>
                        <p>
                            List of service codes and details by service.<br/>
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> This listing is subject to change without prior notice. You can use the corresponding API method to get the updated list at any time.
                            </small>
                        </p>
                        <table class="table table-condensed" style="font-size: 14px">
                            <tr>
                                <th style="width: 1%">Code</th>
                                <th>Service</th>
                                <th>Tempo Transito</th>
                                <th style="width: 90px">Max. Kg</th>
                                <th style="width: 90px">Max. Vols</th>
                            </tr>
                            @foreach($servicesList as $item)
                                <tr>
                                    <td>{{ $item->display_code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->transit_time }}</td>
                                    <td>{{ $item->max_kg }}</td>
                                    <td>{{ $item->max_vol }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </section>
                    <section class="docs-section" id="item-list-status">
                        <h2 class="section-heading">List of Status Codes</h2>
                        <p>
                            List of status codes and details by status.<br/>
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> This listing is subject to change without prior notice. You can use the corresponding API method to get the updated list at any time.
                            </small>
                        </p>
                        <table class="table table-condensed" style="font-size: 14px">
                            <tr>
                                <th style="width: 1%">Code</th>
                                <th>Status</th>
                                <th style="width: 60px">Final Status</th>
                            </tr>
                            @foreach($statusList as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if($item->is_final)
                                            <i class="fas fa-check-circle"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </section>
                    <section class="docs-section" id="item-list-incidences">
                        <h2 class="section-heading">List of Incidences Codes</h2>
                        <p>
                            List of incidences codes and details by status.<br/>
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i> This listing is subject to change without prior notice. You can use the corresponding API method to get the updated list at any time.
                            </small>
                        </p>
                        <table class="table table-condensed" style="font-size: 14px">
                            <tr>
                                <th style="width: 1%">Code</th>
                                <th>Status</th>
                            </tr>
                            @foreach($incidencesList as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </section>
                    <section class="docs-section" id="item-list-resolution-types">
                        <h2 class="section-heading">List of Resolutions Actions</h2>
                        <p>
                            List of incidence resolution actions.
                        </p>
                        <table class="table table-condensed" style="font-size: 14px">
                            <tr>
                                <th style="width: 1%">Code</th>
                                <th>Action</th>
                            </tr>
                            @foreach($resolutionsTypes as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </section>
                    @if($level == 'partners')
                        <section class="docs-section" id="item-list-agencies">
                            <h2 class="section-heading">List of agencies</h2>
                            <p>
                                List of agencies
                            </p>
                            <table class="table table-condensed" style="font-size: 14px">
                                <tr>
                                    <th style="width: 1%">ID</th>
                                    <th style="width: 1%">Code</th>
                                    <th>Name</th>
                                </tr>
                                @foreach($agencies as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->print_name }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </section>
                        <section class="docs-section" id="item-list-customer-types">
                            <h2 class="section-heading">List of Customer Types</h2>
                            <p>
                                List of customer types.
                            </p>
                            <table class="table table-condensed" style="font-size: 14px">
                                <tr>
                                    <th style="width: 1%">ID</th>
                                    <th>Name</th>
                                </tr>
                                @foreach($customerTypes as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </section>
                        <section class="docs-section" id="item-list-payment-conditions">
                            <h2 class="section-heading">List Payment Conditions</h2>
                            <p>
                                List of payment conditions
                            </p>
                            <table class="table table-condensed" style="font-size: 14px">
                                <tr>
                                    <th style="width: 1%">Code</th>
                                    <th>Name</th>
                                </tr>
                                @foreach($paymentConditions as $item)
                                    <tr>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </section>
                        <section class="docs-section" id="item-list-routes">
                            <h2 class="section-heading">List of Routes</h2>
                            <p>
                                List of routes
                            </p>
                            <table class="table table-condensed" style="font-size: 14px">
                                <tr>
                                    <th style="width: 1%">ID</th>
                                    <th style="width: 1%">Code</th>
                                    <th>Name</th>
                                </tr>
                                @foreach($routes as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->name }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </section>
                    @endif
                </article>
            </div>
        </div>
    </div>
@stop


