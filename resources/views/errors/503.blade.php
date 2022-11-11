@extends('errors::minimal')

@section('title', __('Syngenta TYMIRIUM Manual | Temporarily Offline'))
@section('code', '503')
@section('message', __($exception->getmessage() ?: 'service unavailable'))
