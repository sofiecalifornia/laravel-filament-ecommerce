@extends('errors.layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('The client request has not been completed because it lacks valid authentication credentials for the requested resource.'))

@section('link')
    <a href="{{ url('/') }}" class="btn btn-link">{{ __('Back to homepage') }}</a>
@endsection
