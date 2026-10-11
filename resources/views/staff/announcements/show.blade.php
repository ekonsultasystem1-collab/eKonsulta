@extends('layouts.dashboard')

@section('title', 'Announcement Details')
@section('page-title', 'Announcement Details')

@section('content')
    <x-announcement-detail
        :announcement="$announcement"
        back-route="staff.announcements.index"
        edit-route="staff.announcements.edit"
    />
@endsection
