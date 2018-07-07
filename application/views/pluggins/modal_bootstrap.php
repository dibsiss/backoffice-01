<div id="modal-bootstrap" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Nota</h4>
      </div>
      <div class="modal-body">
        <p>Ingin Print Nota</p>
      </div>
      <div class="modal-footer">
        <a id="urlNota" target="_blank" onclick="hideModal()" class="btn btn-primary" >Print</a>
        <button type="button" onclick="hideModal()" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
function hideModal(){
	location.reload();
}
</script>