<!-- Button trigger modal -->
<button style="display:none" id="launchModal" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalContainer">
  Launch modal
</button>

<!-- Modal -->
<div class="modal fade" id="modalContainer" tabindex="-1" role="dialog" aria-labelledby="modalContainerLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalContainerLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" id="dismissModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="saveChanges" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>