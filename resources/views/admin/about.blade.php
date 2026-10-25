<?php
if (Auth('admin')->User()->dashboard_style == "light") {
    $text = "dark";
} else {
    $text = "light";
}
$brand = $settings->site_name ?? config('app.name');
?>
@extends('layouts.app')
    @section('content')
        @include('admin.topmenu')
        @include('admin.sidebar')
        <div class="main-panel bg-{{Auth('admin')->User()->dashboard_style}}">
            <div class="content bg-{{Auth('admin')->User()->dashboard_style}}">
                <div class="page-inner">
                    <div class="mt-2 mb-4">
                        <h1 class="title1 text-{{$text}}">{{$title}}</h1>
                    </div>

                    <div class="mb-5 row">
                        <div class="col-md-7">
                            <div class="card p-4 shadow bg-{{Auth('admin')->User()->dashboard_style}} text-{{$text}}">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ asset('dash/images/mitrax-mark.png') }}" alt="{{ $brand }}"
                                         style="width:44px;height:44px;object-fit:contain;" class="mr-3">
                                    <div>
                                        <h3 class="mb-0 text-{{$text}}">{{ $brand }}</h3>
                                        <small class="text-muted">{{ $settings->site_title ?? '' }}</small>
                                    </div>
                                </div>

                                <p class="mb-4">
                                    {{ $settings->description ?: 'Crypto investment and portfolio management platform.' }}
                                </p>

                                <table class="table table-sm table-borderless text-{{$text}} mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="pl-0" style="width:40%;">Site address</th>
                                            <td>{{ $settings->site_address ?: url('/') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">Support email</th>
                                            <td>
                                                @if($settings->contact_email)
                                                    <a href="mailto:{{ $settings->contact_email }}">{{ $settings->contact_email }}</a>
                                                @else
                                                    <span class="text-muted">Not configured</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">Support channel</th>
                                            <td><a href="https://t.me/+VRumJJSKKGdjM2I0" target="_blank" rel="noopener">Telegram</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card p-4 shadow bg-{{Auth('admin')->User()->dashboard_style}} text-{{$text}}">
                                <h4 class="mb-3 text-{{$text}}">System information</h4>
                                <table class="table table-sm table-borderless text-{{$text}} mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="pl-0" style="width:50%;">Application</th>
                                            <td>{{ config('app.name') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">Environment</th>
                                            <td>{{ app()->environment() }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">Laravel</th>
                                            <td>{{ app()->version() }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">PHP</th>
                                            <td>{{ PHP_VERSION }}</td>
                                        </tr>
                                        <tr>
                                            <th class="pl-0">Timezone</th>
                                            <td>{{ config('app.timezone') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
