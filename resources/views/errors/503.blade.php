@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Service Unavailable
    @endisset

@endsection
