<div class="d-flex justify-content-end">
    <div class="dropdown">
        <a class="custom-dropdown-toggle dropdown-toggle" type="button" id="actionsDropdown{{ $hsCode->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $hsCode->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('hs-codes.edit', $hsCode->id) }}">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger delete-hs-code" href="#" data-id="{{ $hsCode->id }}" data-name="{{ $hsCode->sku }}">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
        </ul>
    </div>
</div>

