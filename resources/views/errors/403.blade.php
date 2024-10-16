@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Access denied
    @endisset

@endsection
