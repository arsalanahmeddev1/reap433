{{--
    $readonly bool
    $galleryImages optional collection of ProductImage (main gallery: product_attribute_item_id null)
    Uses ProductImage::publicUrl() for src (legacy uploads/… or storage products/…).
--}}
@php
    $ro = ! empty($readonly);
    $galleryCollection = isset($galleryImages) ? $galleryImages : collect();
@endphp
<div class="col-12">
    <div class="mb-3">
        <label class="form-label">Gallery images (optional)</label>
        @if ($ro)
            @if ($galleryCollection->isNotEmpty())
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach ($galleryCollection as $img)
                        @php $src = $img->publicUrl(); @endphp
                        @if ($src !== '')
                            <a href="{{ $src }}" target="_blank" rel="noopener">
                                <img src="{{ $src }}" alt="" class="img-thumbnail rounded" style="max-height: 120px; width: auto;">
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No gallery images.</p>
            @endif
        @else
            <div id="gallery_images" class="dropzone"></div>
            <input type="file" name="images[]" id="galleryInput" multiple hidden>
            <div class="d-flex flex-wrap gap-2 mt-2" id="galleryPreview">
                @foreach ($galleryCollection as $img)
                    @php $src = $img->publicUrl(); @endphp
                    @if ($src !== '')
                        <div class="gallery-item image-preview-wrapper position-relative" data-id="{{ $img->id }}">
                            <img src="{{ $src }}" alt=""
                                style="width: 100px; height: 100px; object-fit: cover; display: block;">
                            <button type="button" class="btn btn-danger btn-sm delete-gallery-image"
                                style="position: absolute; top: 2px; right: 2px; padding: 2px 6px; line-height: 1;"
                                title="Remove">&times;</button>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
