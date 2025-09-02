@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-name', 'Dashboard')

@section('content')
<div class="p-4">
    <h3 class="mb-3">Dashboard</h3>
    <div class="card">
        <div class="card-body">
            <p>Selamat datang di dashboard, {{ auth()->user()->username ?? 'Guest' }}!</p>
        </div>
    </div>
</div>
@endsection
