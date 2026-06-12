@php
    $statusBadge = $category->status === 'active' ? 'badge-light-success' : 'badge-light-secondary';
@endphp
<tr class="product-removes inbox-data" data-category-id="{{ $category->id }}">
    <td class="category-name">
        <template class="category-description-template">{!! $category->description !!}</template>
        {{ $category->name }}
    </td>
    <td class="category-slug">
        <code class="text-reset">{{ $category->slug }}</code>
    </td>
    <td class="category-status">
        <span class="badge {{ $statusBadge }}">{{ ucfirst($category->status) }}</span>
    </td>
    <td>
        <div class="common-align gap-2 justify-content-start">
            <a
                class="square-white"
                href="{{ route('artifacts.index', ['category_id' => $category->id]) }}"
                target="_blank"
                rel="noopener noreferrer"
                title="{{ __('View on storefront') }}"
            >
                <span><i class="fa-solid fa-eye"></i></span>
            </a>
            <button
                type="button"
                class="square-white js-category-edit border-0 p-0"
                title="{{ __('Edit') }}"
                data-update-url="{{ route('product-categories.update', $category) }}"
                data-name="{{ $category->name }}"
                data-slug="{{ $category->slug }}"
                data-status="{{ $category->status }}"
                data-parent-id="{{ $category->parent_id }}"
                data-image-url="{{ $category->imageUrl() }}"
            >
                <span><i class="fa-solid fa-pen"></i></span>
            </button>
            <form action="{{ route('product-categories.destroy', $category) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="square-white border-0 js-category-delete" title="{{ __('Delete') }}">
                    <span><i class="fa-solid fa-trash"></i></span>
                </button>
            </form>
        </div>
    </td>
</tr>
