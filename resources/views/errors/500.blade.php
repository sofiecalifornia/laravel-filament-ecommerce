@extends('errors.layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('The server encountered an unexpected condition that prevented it from fulfilling the request'))

@section('link')
    <a href="{{ url('/') }}" class="btn btn-link">{{ __('Back to homepage') }}</a>
@endsection
