@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Unauthorized, please login to access this page
    @endisset

@endsection
