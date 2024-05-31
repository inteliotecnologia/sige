<?
require_once("conexao.php");
if (pode("ps", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_suja_remessas
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_remessa = '". $_GET["id_remessa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Área suja - Remessa</h2>

<form action="<?= AJAX_FORM; ?>formRemessa&amp;acao=<?= $acao; ?>" method="post" name="formRemessa" id="formRemessa" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="data_remessa@data|<? if ($acao=="e") { ?>num_remessa@vazio|<? } ?>id_veiculo@vazio|id_percurso@vazio|hora_chegada@vazio|hora_inicio_descarga@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_remessa" class="escondido" type="hidden" id="id_remessa" value="<?= $rs->id_remessa; ?>" />
    <input name="origem" class="escondido" type="hidden" id="origem" value="<?= $_GET["origem"]; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50 gigante">
        	<?
			if ($acao=='i') $data_remessa= date("d/m/Y");
			else $data_remessa= desformata_data($rs->data_remessa);
			?>
            <label for="data_remessa">* Data:</label>
            <input id="data_remessa" name="data_remessa" class="tamanho25p" value="<?= $data_remessa; ?>" title="Data da remessa" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <? if ($acao=="e") { ?>
            <label for="num_remessa">* Número:</label>
            <input id="num_remessa" name="num_remessa" value="<?= $rs->num_remessa; ?>" class="espaco_dir tamanho25p" title="Número" />
            <br />
            <? } ?>
            
            <? /*
            <label for="relave">Relave:</label>
            <input id="relave" name="relave" value="<?= fnum($rs->relave); ?>" class="espaco_dir tamanho25p" onkeydown="formataValor(this,event);" title="Relave" /> kg
            <br />*/ ?>
            
            <label for="veiculo">Veículo:</label>
            <input id="veiculo" name="veiculo" value="<?= pega_codigo_do_veiculo($rs->id_veiculo); ?>" class="tamanho25p espaco_dir" onblur="pegaVeiculo(this.value); pegaPercursosVeiculo(this.value, '<?=$acao;?>');" />
            <div id="veiculo_atualiza">
            	<?= pega_veiculo($rs->id_veiculo); ?>
                <input id="id_veiculo" name="id_veiculo" value="<?= $rs->id_veiculo; ?>" title="Veículo" class="escondido" />
            </div>
            <br />
            
            <label for="id_percurso">Percurso:</label>
            <div id="id_percurso_atualiza">
            	<select id="id_percurso" name="id_percurso">
                    <option value="">Identifique o veículo</option>
                </select>
            </div>
            <br />
            
        </div>
        <div class="parte50 gigante">
        	<label for="hora_chegada">* Hora da chegada:</label>
            <input id="hora_chegada" name="hora_chegada" class="tamanho25p" value="<?= $rs->hora_chegada; ?>" title="Hora da chegada"  onkeyup="formataHora(this);" maxlength="8" />
            <br />
            
            <label for="hora_inicio_descarga">* Hora de inicío da descarga:</label>
            <input id="hora_inicio_descarga" name="hora_inicio_descarga" class="tamanho25p" value="<?= $rs->hora_inicio_descarga; ?>" title="Hora de inicío da descarga"  onkeyup="formataHora(this);" maxlength="8" />
            <br />
            
            <?
			if ($acao=="e") {
				if ($acao=='i') $hora_fim_descarga= date("H:i:s");
				else $hora_fim_descarga= $rs->hora_fim_descarga;
			?>
            <label for="hora_fim_descarga">* Hora de fim da descarga:</label>
            <input id="hora_fim_descarga" name="hora_fim_descarga" class="tamanho25p" value="<?= $hora_fim_descarga; ?>" title="Hora de fim da descarga"  onkeyup="formataHora(this);" maxlength="8" />
            <br />
			<? } ?>
            
            <br />
            
            <? /* if ($acao=="e") { ?>
            	<fieldset>
                	<legend>Separação</legend>
					
                    <label for="data_inicio_separacao">* Início (data/hora):</label>
                    <input id="data_inicio_separacao" name="data_inicio_separacao" class="tamanho25p espaco_dir" value="<?= desformata_data($rs->data_inicio_separacao); ?>" title="Data de início da separação"  onkeyup="formataData(this);" maxlength="10" />
                    <input id="hora_inicio_separacao" name="hora_inicio_separacao" class="tamanho25p" value="<?= $rs->hora_inicio_separacao; ?>" title="Hora de início da separação"  onkeyup="formataHora(this);" maxlength="8" />
                    <br />
                    
                    <label for="data_fim_separacao">* Fim (data/hora):</label>
                    <input id="data_fim_separacao" name="data_fim_separacao" class="tamanho25p espaco_dir" value="<?= desformata_data($rs->data_fim_separacao); ?>" title="Data de fim da separação"  onkeyup="formataData(this);" maxlength="10" />
                    <input id="hora_fim_separacao" name="hora_fim_separacao" class="tamanho25p" value="<?= $rs->hora_fim_separacao; ?>" title="Hora de fim da separação"  onkeyup="formataHora(this);" maxlength="8" />
                    <br />
                </fieldset>
			<? } */ ?>
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	
	<? if ($acao=="i") { ?>
	daFoco("veiculo");
	<? } else { ?>
	pegaPercursosVeiculo("<?= pega_codigo_do_veiculo($rs->id_veiculo); ?>", "e");
	daFoco("data_remessa");
	<? } ?>
</script>
<? } ?>