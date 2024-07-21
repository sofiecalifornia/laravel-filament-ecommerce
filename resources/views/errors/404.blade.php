@extends('errors.layout')

@section('title', __('Not found'))
@section('code', '404')
@section('message', __('The server did not find the requested address'))

@section('link')
    <a href="{{ url('/') }}" class="btn btn-link">{{ __('Back to homepage') }}</a>
@endsection
