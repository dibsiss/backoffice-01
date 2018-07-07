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
                        <label>Toko:</label>
                        <select name="id_toko" id="id_toko" class="chosen-select-deselect" data-placeholder='Pilih Toko' style='width:100%'>
						<option value=""></option>
                            <?php foreach($toko as $g){
								echo "<option value='$g->id_toko'>$g->nama</option>";
							} ?>
                        </select>
                    </div>