@extends('layouts.dashboard')

@section('title', 'Edit Announcement')
@section('page-title', 'Edit Announcement')

@section('content')
    <div class="dashboard-card admin-form-card announcement-form-page">
        <div class="card-header">
            <div>
                <h3>Edit Announcement</h3>
                <p>Update the content and visibility of this announcement.</p>
            </div>
            <a href="{{ route('admin.announcements.index') }}" class="secondary-button">Back</a>
        </div>

        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" class="stacked-form">
            @csrf
            @method('PUT')

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
                    <input id="title" name="title" type="text" value="{{ old('title', $announcement->title) }}" maxlength="255" required>
                </div>

                <div class="form-field">
                    <label for="health_center_id">Audience</label>
                    <select id="health_center_id" name="health_center_id">
                        <option value="">Global announcement</option>
                        @foreach($healthCenters as $healthCenter)
                            <option value="{{ $healthCenter->id }}" {{ old('health_center_id', $announcement->health_center_id) == $healthCenter->id ? 'selected' : '' }}>
                                {{ $healthCenter->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="event_start_date">Event start date</label>
                    <input id="event_start_date" name="event_start_date" type="date" value="{{ old('event_start_date', $announcement->event_start_date?->format('Y-m-d')) }}">
                </div>

                <div class="form-field">
                    <label for="event_end_date">Event end date</label>
                    <input id="event_end_date" name="event_end_date" type="date" value="{{ old('event_end_date', $announcement->event_end_date?->format('Y-m-d')) }}">
                    <small>The announcement will no longer appear after this date.</small>
                </div>

                <div class="form-field">
                    <label for="image">Replace image <span class="text-muted">(optional)</span></label>
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
                    <small>Use a JPG, PNG, or WebP image up to 2 MB.</small>
                    <div class="announcement-upload-preview" data-upload-preview hidden>
                        <button type="button" class="announcement-image-preview-trigger" data-image-preview="" data-image-alt="" aria-label="Preview selected image">
                            <img alt="">
                        </button>
                        <span>New image preview</span>
                    </div>
                </div>

                @if($announcement->image_path)
                    <div class="form-field full-width">
                        <label>Current Image</label>
                        <button
                            type="button"
                            class="announcement-current-image announcement-image-preview-trigger"
                            data-image-preview="{{ asset('storage/' . $announcement->image_path) }}"
                            data-image-alt="{{ $announcement->title }}"
                            aria-label="Preview current announcement image"
                        >
                            <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="">
                            <span class="announcement-image-preview-hint">Click to enlarge</span>
                        </button>
                        <label class="checkbox-field" style="margin-top: 12px;">
                            <input type="checkbox" name="remove_image" value="1">
                            Remove current image
                        </label>
                    </div>
                @endif

                <div class="form-field full-width">
                    <label for="content">Announcement details <span aria-hidden="true">*</span></label>
                    <textarea id="content" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
                    <small>Include relevant dates, locations, and instructions for the selected audience.</small>
                </div>

                <div class="form-field checkbox-field">
                    <label>
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }}>
                        Publish this announcement
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.announcements.index') }}" class="secondary-button">Cancel</a>
                <button type="submit" class="primary-button">Update Announcement</button>
            </div>
        </form>
    </div>
@endsection
