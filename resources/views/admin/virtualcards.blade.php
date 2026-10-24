<?php
$style = Auth('admin')->User()->dashboard_style;
$text = $style == "light" ? "dark" : "light";
?>
@extends('layouts.app')
    @section('content')
        @include('admin.topmenu')
        @include('admin.sidebar')
        <div class="main-panel bg-{{$style}}">
            <div class="content bg-{{$style}}">
                <div class="page-inner">
                    <div class="mt-2 mb-4">
                        <h1 class="title1 text-{{$text}}">{{$settings->site_name}} virtual cards</h1>
                        <small class="text-{{$text}}">Enter only the last 4 digits and expiry from your card issuer. Never enter a full card number or CVV here.</small>
                    </div>
                    @if(Session::has('message'))
                        <div class="alert alert-info alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-info-circle"></i> {{Session::get('message')}}
                        </div>
                    @endif
                    @if(count($errors) > 0)
                        <div class="alert alert-danger alert-dismissable" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            @foreach ($errors->all() as $error)
                                <i class="fa fa-warning"></i> {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <div class="card p-4 mb-4 bg-{{$style}} shadow">
                        <h4 class="text-{{$text}}">Issue a card to a user</h4>
                        <form method="POST" action="{{ route('virtualcards.issue') }}" class="form-row align-items-end">
                            @csrf
                            <div class="col-md-5 mb-2">
                                <label class="text-{{$text}}">User</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">Select user</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->id}}">{{$u->name}} {{$u->l_name}} ({{$u->email}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="text-{{$text}}">Last 4 digits</label>
                                <input type="text" name="last4" class="form-control" maxlength="4" pattern="\d{4}" inputmode="numeric" autocomplete="off" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="text-{{$text}}">Expiry (MM/YY)</label>
                                <input type="text" name="expiry" class="form-control" maxlength="5" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}" autocomplete="off" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="submit" class="btn btn-primary btn-block">Issue card</button>
                            </div>
                        </form>
                    </div>

                    <div class="card p-4 bg-{{$style}} shadow">
                        <div class="table-responsive">
                            <table class="table table-hover text-{{$text}}">
                                <thead>
                                    <tr><th>User</th><th>Status</th><th>Card</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($cards as $c)
                                        <tr>
                                            <td>{{ optional($c->user)->name }} {{ optional($c->user)->l_name }}<br><small>{{ optional($c->user)->email }}</small></td>
                                            <td>{{ ucfirst($c->status) }}</td>
                                            <td>@if($c->status === 'approved') •••• {{ $c->last4 }} &nbsp; {{ $c->expiry }} @else - @endif</td>
                                            <td>
                                                @if($c->status !== 'approved')
                                                    <form method="POST" action="{{ route('virtualcards.approve', $c->id) }}" class="form-inline mb-1">
                                                        @csrf
                                                        <input type="text" name="last4" class="form-control form-control-sm mr-1" style="width:80px" maxlength="4" placeholder="Last 4" inputmode="numeric" autocomplete="off" required>
                                                        <input type="text" name="expiry" class="form-control form-control-sm mr-1" style="width:80px" maxlength="5" placeholder="MM/YY" autocomplete="off" required>
                                                        <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                                    </form>
                                                @endif
                                                @if($c->status !== 'rejected')
                                                    <form method="POST" action="{{ route('virtualcards.reject', $c->id) }}" onsubmit="return confirm('Reject / revoke this card?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">{{ $c->status === 'approved' ? 'Revoke' : 'Reject' }}</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No card requests yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
