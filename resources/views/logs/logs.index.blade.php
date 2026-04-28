@extends('layouts.app')

@section('title', 'Logs do sistema')

@section('content')
    <div id="logs-app" data-endpoint="{{ route('logs.feed') }}"></div>

    @vite('resources/js/app.js')
@endsection