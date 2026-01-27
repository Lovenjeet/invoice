<div class="d-flex justify-content-end">
    <div class="dropdown">
        <a class="custom-dropdown-toggle dropdown-toggle" type="button" id="actionsDropdown{{ $invoice->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $invoice->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('invoices.show', $invoice->id) }}">
                    <i class="bi bi-eye me-2"></i>View
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('invoices.edit', $invoice->id) }}">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
        </ul>
    </div>
</div>

