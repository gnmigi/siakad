<div class="btn-group" role="group">
    <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" title="Edit Mata Kuliah">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route(auth()->user()->prefix . 'akademik.mata-kuliah-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-danger" title="Hapus Mata Kuliah" onclick="confirmDelete('{{ $item->code }}')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
