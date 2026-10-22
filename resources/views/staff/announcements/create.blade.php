@extends('layouts.dashboard')

@section('title', 'Create Announcement')
@section('page-title', 'Create Announcement')

@section('content')
    <div class="dashboard-card admin-form-card announcement-form-page">
        <div class="card-header">
            <div>
                <h3>Create Health Center Announcement</h3>
                <p>Share updates for your assigned health center.</p>
            </div>
            <a href="{{ route('staff.announcements.index') }}" class="secondary-button">Back</a>
        </div>

        <form method="POST" action="{{ route('staff.announcements.store') }}" enctype="multipart/form-data" class="stacked-form">
            @csrf

            @if($errors->any())
                <div class="form-error-box">
                    <strong>Please correct the following:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-grid">
                <div class="form-field full-width">
                    <label for="title">Announcement title <span aria-hidden="true">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" placeholder="e.g. Clinic schedule update" maxlength="255" autocomplete="off" required>
                </div>

                <div class="form-field">
                    <label for="image">Announcement image <span class="text-muted">(optional)</span></label>
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                    <small>Use a JPG, PNG, or WebP image up to 2 MB.</small>
                    <div class="announcement-upload-preview" data-upload-preview hidden>
                        <button type="button" class="announcement-image-preview-trigger" data-image-preview="" data-image-alt="" aria-label="Preview selected image">
                            <img alt="">
                        </button>
                        <span>Image preview</span>
                    </div>
                </div>

                <div class="form-field full-width">
                    <label for="content">Announcement details <span aria-hidden="true">*</span></label>
                    <textarea id="content" name="content" rows="8" placeholder="Share the details patients and staff need to know." required>{{ old('content') }}</textarea>
                    <small>Include dates, locations, and any instructions patients should follow.</small>
                </div>

                <div class="form-field">
                    <label for="event_start_date">Event start date</label>
                    <input id="event_start_date" name="event_start_date" type="date" value="{{ old('event_start_date') }}">
                </div>

                <div class="form-field">
                    <label for="event_end_date">Event end date</label>
                    <input id="event_end_date" name="event_end_date" type="date" value="{{ old('event_end_date') }}">
                    <small>The announcement will no longer appear after this date.</small>
                </div>

                <div class="form-field checkbox-field">
                    <label>
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                        Publish immediately
                    </label>
                    <small>Published announcements are visible to patients at your health center and may trigger notifications.</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('staff.announcements.index') }}" class="secondary-button">Cancel</a>
                <button type="submit" class="primary-button">Save Announcement</button>
            </div>
        </form>
    </div>
@endsection
