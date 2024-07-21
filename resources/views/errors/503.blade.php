@extends('errors.layout')

@section('title', __('Service unvailable'))
@section('code', '503')
@section('message', __('Certainly a maintenance, please try again in a few minutes'))

@section('link')
    <a href="{{ Request::url() }}" class="btn btn-link">{{ __('Refresh') }}</a>
@endsection
