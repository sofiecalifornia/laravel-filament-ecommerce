@extends('errors.layout')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Sorry, your session has expired, please refresh and try again'))

@section('link')
    <a href="{{ url('/') }}" class="btn btn-link">{{ __('Back to homepage') }}</a>
@endsection
