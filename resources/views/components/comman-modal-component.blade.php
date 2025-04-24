<div class="modal fade custom_Modal" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
     aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-white" id="{{ $modalId }}Label">{{ $modalTitle }}</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-togleid="{{ $togleId }}" data-modalid="{{ $modalId }}" onclick="uncheckToggle(this)"></button>
            </div>
            <div class="modal-body" id="modal-content-body">
                <!-- Modal Content Goes Here -->
            </div>
        </div>
    </div>
</div>
