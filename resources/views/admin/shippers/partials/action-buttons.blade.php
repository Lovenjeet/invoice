<div class="d-flex justify-content-end">
    <div class="dropdown">
        <a class="custom-dropdown-toggle dropdown-toggle" type="button" id="actionsDropdown{{ $shipper->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $shipper->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('shippers.show', $shipper->id) }}">
                    <i class="bi bi-eye me-2"></i>View
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('shippers.edit', $shipper->id) }}">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger delete-shipper" href="#" data-id="{{ $shipper->id }}" data-name="{{ $shipper->name }}">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
        </ul>
    </div>
</div>

