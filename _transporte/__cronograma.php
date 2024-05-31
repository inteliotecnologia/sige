<?
require_once("conexao.php");
if (pode("e", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from tr_cronograma
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_cronograma = '". $_GET["id_cronograma"] ."'
								");
							
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Cronograma de coleta/entrega</h2>

<form action="<?= AJAX_FORM; ?>formTranspCronograma&amp;acao=<?= $acao; ?>" method="post" name="formTranspCronograma" id="formTranspCronograma" onsubmit="return validaFormNormal('validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_cliente@vazio|tipo@vazio|id_dia@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_cronograma" class="escondido" type="hidden" id="id_cronograma" value="<?= $rs->id_cronograma; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="id_cliente">* Cliente:</label>
            <select name="id_cliente" id="id_cliente" title="Cliente">
            	<? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                
                <?
                $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'c'
											and   pessoas.id_cliente_tipo = '1'
                                            order by 
                                            pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_ced = mysql_fetch_object($result_ced)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs->id_cedente) echo "selected=\"selected\""; ?>><?= $rs_ced->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="tipo">* Tipo:</label>
            <select name="tipo" id="tipo" title="Tipo">
                <? if ($acao=='i') { ?>
                <option value="">-</option>
                <? } ?>
                <option value="1" <? if ($rs->tipo=="1") echo "selected=\"selected\""; ?>>Coleta</option>
                <option value="2" <? if ($rs->tipo=="2") echo "selected=\"selected\""; ?> class="cor_sim">Entrega</option>
            </select>
            <br />
            
            <label for="id_dia">* Dia:</label>
            <select name="id_dia" id="id_dia" title="id_dia">
                <? if ($acao=='i') { ?>
                <option value="">-</option>
                <option class="cor_sim" value="0">Todos</option>
                <? } ?>
				<?
                for ($i=0; $i<7; $i++) {
                ?>
                <option <? if ($i%2!=0) echo "class=\"cor_sim\""; ?> value="<?= $i; ?>" <? if (($acao=='e') && ($i==$rs->id_dia)) echo "selected=\"selected\""; ?>><?= traduz_dia($i); ?></option>
                <? } ?>
            </select>
            <br />
            
        </div>
        <div class="parte50">
        	<?
			if ($acao=='i') {
				for ($i=0; $i<5; $i++) {
				?>
				<label for="hora_cronograma_<?=$i;?>">Hora:</label>
				<input class="tamanho25p" title="Hora" name="hora_cronograma[]" onkeyup="formataHora(this);" maxlength="5" value="<?= $rs->hora_cronograma; ?>" id="hora_cronograma_<?=$i;?>" />
				<br />
				<? } ?>
            <? } else { ?>
            <label for="hora_cronograma">Hora:</label>
            <input class="tamanho25p" title="Hora" name="hora_cronograma" onkeyup="formataHora(this);" maxlength="5" value="<?= substr($rs->hora_cronograma, 0, 5); ?>" id="hora_cronograma" />
            <br />
            <? } ?>
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript">
	daFoco("cronograma");
</script>
<? } ?>