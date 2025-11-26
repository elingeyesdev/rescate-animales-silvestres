<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">{{ __('Confirmar eliminación') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('¿Estás seguro de querer eliminar este registro? Esta acción no se puede deshacer.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{{ __('Eliminar') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let formToSubmit = null;
    document.querySelectorAll('.js-confirm-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            formToSubmit = this.closest('form');
            if (window.$ && $('#confirmDeleteModal').modal) {
                $('#confirmDeleteModal').modal('show');
            } else {
                if (confirm('{{ __('¿Estás seguro de querer eliminar?') }}')) {
                    formToSubmit && formToSubmit.submit();
                }
            }
        });
    });
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (window.$ && $('#confirmDeleteModal').modal) {
                $('#confirmDeleteModal').modal('hide');
            }
            formToSubmit && formToSubmit.submit();
        });
    }
});
</script>







