@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Page Not Found, please recheck URL
    @endisset

@endsection
