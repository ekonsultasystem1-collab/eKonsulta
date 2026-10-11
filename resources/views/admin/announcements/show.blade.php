@extends('layouts.dashboard')

@section('title', 'Announcement Details')
@section('page-title', 'Announcement Details')

@section('content')
    <x-announcement-detail
        :announcement="$announcement"
        back-route="admin.announcements.index"
        edit-route="admin.announcements.edit"
    />
@endsection
