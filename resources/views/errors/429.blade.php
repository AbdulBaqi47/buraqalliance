@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Too Many Requests
    @endisset

@endsection
