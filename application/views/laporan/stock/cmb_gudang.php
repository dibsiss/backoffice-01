<script>
        var config = {
            '.chosen-select-deselect': {
                allow_single_deselect: true
            }, '.chosen-select-deselect2': {
                allow_single_deselect: true
            }
        }
for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
		
</script>
<div class="form-group">
                        <label>Gudang:</label>
                        <select name="id_gudang" id="id_gudang" class="chosen-select-deselect" onchange="getToko($(this).val())" data-placeholder='Pilih Gudang' style='width:100%'>
						<option value=""></option>
                            <?php foreach($gudang as $g){
								echo "<option value='$g->id_gudang'>$g->nama</option>";
							} ?>
                        </select>
                    </div>