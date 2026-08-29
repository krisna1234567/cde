<div class="admin-upload-field h-100">
    <div class="admin-upload-preview admin-upload-preview-og">
        @if($image)
            <img src="{{ $image }}" alt="" data-preview-image="{{ $previewKey }}">
        @else
            <img src="" class="d-none" alt="" data-preview-image="{{ $previewKey }}"><i class="bi bi-image" data-preview-placeholder="{{ $previewKey }}"></i>
        @endif
    </div>
    <div class="flex-grow-1">
        <label class="form-label">{{ $label }}</label>
        <input type="file" name="{{ $input }}" accept="image/*" class="form-control" data-image-input data-preview-target="{{ $previewKey }}" data-remove-target="{{ $removeInput }}">
        <input name="{{ $altInput }}" class="form-control mt-2" value="{{ $altValue }}" placeholder="Alternative text">
        <div class="form-check mt-2">
            <input id="{{ $removeInput }}" type="checkbox" class="form-check-input" name="{{ $removeInput }}" value="1">
            <label for="{{ $removeInput }}" class="form-check-label">Remove current image</label>
        </div>
    </div>
</div>
