@extends('errors::minimal')

@section('title', __('Payment Required'))
@section('code', '402')

@section('message')

    @isset($exception)
        {{ $exception->getMessage() }}
    @else
        Payment Required
    @endisset

@endsection
