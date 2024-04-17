@section('title')
    {{ ucwords($level) }}
@stop

@section('content')
    @include('api.docs.partials.header')

    <div class="docs-wrapper">
        <div id="docs-sidebar" class="docs-sidebar">
            {{--<div class="top-search-box d-lg-none p-3">
                <form class="search-form">
                    <input type="text" placeholder="Search the docs..." name="search" class="form-control search-input">
                    <button type="submit" class="btn search-btn" value="Search"><i class="fas fa-search"></i></button>
                </form>
            </div>--}}
            <nav id="docs-nav" class="docs-nav navbar">
                <ul class="section-items list-unstyled nav flex-column pb-3">
                    @foreach($allSections as $section)
                        <li class="nav-item section-title">
                            <a class="nav-link scrollto active mt-3" href="#section-{{ $section->slug }}">
                                <span class="theme-icon-holder me-2"><i class="fas {{ $section->icon ? $section->icon : 'fa-angle-right' }}"></i></span> {{ $section->name }}
                            </a>
                        </li>
                        @if(!$section->methods->isEmpty())
                            @foreach($section->methods as $method)
                            <li class="nav-item">
                                <a class="nav-link scrollto" href="#item-{{ $method->id }}">{{ $method->name }}</a>
                            </li>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            </nav>
        </div>
        <div class="docs-content">
            <div class="container">
                @foreach($allSections as $section)
                    <article class="docs-article" id="section-{{ @$section->slug }}">
                        <header class="docs-header">
                            <h1 class="docs-heading text-primary">
                                {{ $section->name }}
                            </h1>
                            <section class="docs-intro">
                                <p>{!! @$section->description !!}</p>
                            </section>
                        </header>
                        @if(!$section->methods->isEmpty())
                            @foreach($section->methods as $method)
                            <section class="docs-section" id="item-{{ $method->id }}">
                                <h2 class="section-heading">
                                    {{ $method->name }}
                                    {{--@if(Auth::check() && Auth::user()->isAdmin())
                                    <a href="{{ route('admin.api.docs.methods.edit', $method->id) }}"
                                        data-toggle="modal"
                                        data-target="#modal-remote-xl">
                                        <i class="fas fa-pen-square"></i>
                                    </a>
                                    @endif--}}
                                </h2>
                                <?php $description = str_replace('__ENDPOINT__', $endpoint, $method->description); ?>
                                <p>{!! $description !!}</p>

                                @if($method->url)
                                <div class="card card-method-{{ $method->method }}">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">
                                            <span class="badge text-uppercase">{{ $method->method }}</span>
                                            {{ $method->url }}
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        @if($method->params)
                                        <h6 class="text-uppercase text-blue">URL Parameters</h6>
                                        <div class="table-responsive mb-3">
                                            <table class="table m-0">
                                                <tbody>
                                                    @foreach($method->params as $param)
                                                    <tr>
                                                        <td style="width: 1%; font-weight: bold">{{ @$param['param'] }}</td>
                                                        <td>
                                                            <code class="variable">{{ @$param['type'] }}</code>
                                                            @if(@$param['required'])
                                                                <code class="badge-required">required</code>
                                                            @else
                                                                <span class="badge-optional">Optional</span>
                                                            @endif
                                                            <small class="text-muted">{{ @$param['description'] }}</small>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif

                                        @if($method->headers)
                                        <div>
                                            <h6 class="text-uppercase text-blue" style="margin-bottom: -8px">Headers</h6>
                                            <div class="docs-code-block pb-0">
                                                <pre class="rounded"><code class="json hljs">{{ $method->headers }}</code></pre>
                                            </div>
                                        </div>
                                        @endif

                                        @if($method->body)
                                            <?php $body = str_replace('__ENDPOINT__', $endpoint, $method->body); ?>
                                            <h6 class="text-uppercase text-blue" style="margin-bottom: -8px">Body</h6>
                                            <div class="docs-code-block pb-0">
                                                <pre class="rounded"><code class="json hljs">{{ $body }}</code></pre>
                                            </div>
                                            <hr/>
                                        @endif


                                        @if($method->response_ok)
                                        <div class="card-response" id="response-ok-{{ $method->id }}" style="display: none">
                                            <h6 class="text-uppercase text-blue" style="margin-bottom: -8px">Response Success</h6>
                                            <div class="docs-code-block pb-0">
                                                <pre class="rounded"><code class="json hljs">{{ $method->response_ok }}</code></pre>
                                            </div>
                                        </div>
                                        @endif

                                        @if($method->response_error)
                                        <div class="card-response" id="response-error-{{ $method->id }}" style="display: none">
                                            <h6 class="text-uppercase text-blue" style="margin-bottom: -8px">Response Error</h6>
                                            <div class="docs-code-block pb-0">
                                                <pre class="rounded"><code class="json hljs">{{ $method->response_error }}</code></pre>
                                            </div>
                                        </div>
                                        @endif

                                        @if($method->fields1)
                                            <div class="card-response" id="response-fields-{{ $method->id }}" style="display: none">
                                                @if($method->fields1)
                                                <h6 class="text-uppercase text-blue">
                                                    {{ $method->fields1_title }}
                                                </h6>
                                                <table class="table table-condensed" style="font-size: 13px">
                                                    <tr>
                                                        <th style="width: 1%">Parameter</th>
                                                        <th style="width: 1%">Required</th>
                                                        <th style="width: 150px">Type</th>
                                                        <th>Description</th>
                                                    </tr>
                                                    @foreach($method->fields1 as $field)
                                                        <tr>
                                                            <td>{{ $field['field'] }}</td>
                                                            <td class="text-center">
                                                                @if($field['required'])
                                                                    <i class="fas fa-check-circle"></i>
                                                                @endif
                                                            </td>
                                                            <td>{{ $field['type'] }} <small>{{ $field['length'] }}</small></td>
                                                            <td>{{ $field['description'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                                @endif

                                                @if($method->fields2)
                                                    <h6 class="text-uppercase text-blue">
                                                        {{ $method->fields2_title }}
                                                    </h6>
                                                        <table class="table table-condensed" style="font-size: 13px">
                                                        <tr>
                                                            <th style="width: 1%">Parameter</th>
                                                            <th style="width: 1%">Required</th>
                                                            <th style="width: 150px">Type</th>
                                                            <th>Description</th>
                                                        </tr>
                                                        @foreach($method->fields2 as $field)
                                                            <tr>
                                                                <td>{{ $field['field'] }}</td>
                                                                <td class="text-center">
                                                                    @if($field['required'])
                                                                        <i class="fas fa-check-circle"></i>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $field['type'] }} <small>{{ $field['length'] }}</small></td>
                                                                <td>{{ $field['description'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                @endif

                                                @if($method->fields3)
                                                    <h6 class="text-uppercase text-blue">
                                                        {{ $method->fields3_title }}
                                                    </h6>
                                                        <table class="table table-condensed" style="font-size: 13px">
                                                        <tr>
                                                            <th style="width: 1%">Parameter</th>
                                                            <th style="width: 1%">Required</th>
                                                            <th style="width: 150px">Type</th>
                                                            <th>Description</th>
                                                        </tr>
                                                        @foreach($method->fields3 as $field)
                                                            <tr>
                                                                <td>{{ $field['field'] }}</td>
                                                                <td class="text-center">
                                                                    @if($field['required'])
                                                                        <i class="fas fa-check-circle"></i>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $field['type'] }} <small>{{ $field['length'] }}</small></td>
                                                                <td>{{ $field['description'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                @endif

                                                @if($method->fields4)
                                                    <h6 class="text-uppercase text-blue">
                                                        {{ $method->fields4_title }}
                                                    </h6>
                                                    <table class="table table-condensed" style="font-size: 13px">
                                                        <tr>
                                                            <th style="width: 1%">Parameter</th>
                                                            <th style="width: 1%">Required</th>
                                                            <th style="width: 150px">Type</th>
                                                            <th>Description</th>
                                                        </tr>
                                                        @foreach($method->fields4 as $field)
                                                            <tr>
                                                                <td>{{ $field['field'] }}</td>
                                                                <td class="text-center">
                                                                    @if($field['required'])
                                                                        <i class="fas fa-check-circle"></i>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $field['type'] }} <small>{{ $field['length'] }}</small></td>
                                                                <td>{{ $field['description'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                @endif

                                            </div>
                                        @endif
                                       {{-- @if($method->headers)
                                            <button class="btn btn-sm btn-default" data-toggle="collapse" data-target="#headers-{{ $method->id }}">
                                                Show Headers <i class="fas fa-angle-down"></i>
                                            </button>
                                        @endif--}}
                                        @if($method->fields1)
                                            <button class="btn btn-sm btn-info"  data-toggle="collapse" data-target="#response-fields-{{ $method->id }}">
                                                Show Available Fields <i class="fas fa-angle-down"></i>
                                            </button>
                                        @endif
                                        @if($method->response_ok)
                                            <button class="btn btn-sm btn-default" data-toggle="collapse" data-target="#response-ok-{{ $method->id }}">
                                                Show Response Success<i class="fas fa-angle-down"></i>
                                            </button>
                                        @endif
                                        @if($method->response_error)
                                            <button class="btn btn-sm btn-default"  data-toggle="collapse" data-target="#response-error-{{ $method->id }}">
                                                Show Response Error<i class="fas fa-angle-down"></i>
                                            </button>
                                        @endif

                                    </div>
                                </div>
                                @endif
                            </section>
                            @endforeach
                        @endif
                    </article>

                @endforeach

            </div>
        </div>
    </div>
@stop




