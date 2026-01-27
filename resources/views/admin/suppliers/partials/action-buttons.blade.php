<div class="d-flex justify-content-end">
    <div class="dropdown">
        <a class="custom-dropdown-toggle dropdown-toggle" type="button" id="actionsDropdown{{ $supplier->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $supplier->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('suppliers.show', $supplier->id) }}">
                    <i class="bi bi-eye me-2"></i>View
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('suppliers.edit', $supplier->id) }}">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger delete-supplier" href="#" data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
        </ul>
    </div>
</div>

