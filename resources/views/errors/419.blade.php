@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Page Expired - Reload page
    @endisset

@endsection
