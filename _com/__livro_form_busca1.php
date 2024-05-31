<?
require_once("conexao.php");
if (pode("o", $_SESSION["permissao"])) {
?>
<div class="parte50">
    <input type="hidden" class="escondido" name="origem" value="1" />
    <input type="hidden" class="escondido" name="minhas" value="<?=$_GET["minhas"];?>" />
    
    <label class="tamanho100" for="data">Data:</label>
    <input class="tamanho100 espaco_dir" name="data" id="data" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $data; ?>" title="Data" />
</div>
<? } ?>