@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Something went wrong!
    @endisset

@endsection

