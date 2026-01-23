<div class="d-flex justify-content-end">
    <div class="dropdown">
        <a class="custom-dropdown-toggle dropdown-toggle" type="button" id="actionsDropdown{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown{{ $user->id }}">
            <li>
                <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                    <i class="bi bi-eye me-2"></i>View
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
            @if($user->id !== auth()->id())
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger delete-user" href="#" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>

