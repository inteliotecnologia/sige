<?
require_once("conexao.php");
if (pode_algum("rw", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  rh_escala_troca
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_escala_troca = '". $_GET["id_escala_troca"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Troca de escala</h2>

<form action="<?= AJAX_FORM; ?>formEscalaTroca&amp;acao=<?= $acao; ?>" method="post" name="formEscalaTroca" id="formEscalaTroca" onsubmit="return ajaxForm('conteudo', 'formEscalaTroca', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" name="validacoes" value="data_escala_troca@data|id_funcionario_solicitante@vazio|id_funcionario_assume@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_escala_troca" class="escondido" type="hidden" id="id_escala_troca" value="<?= $rs->id_escala_troca; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            
            <?
			if ($acao=='i') {
					
				if ($acao=='i') $data_escala_troca= date("d/m/Y");
				else $data_escala_troca= desformata_data($rs->data_escala_troca);
				?>
				<label for="data_escala_troca">* Data:</label>
				<input id="data_escala_troca" name="data_escala_troca" class="tamanho25p" value="<?= $data_escala_troca; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
				<br />
				
				<label for="id_funcionario_solicitante">* Funcionário solicitante:</label>
				<select class="espaco_dir" name="id_funcionario_solicitante" id="id_funcionario_solicitante" title="Funcinário solicitante" onchange="pegaDadosTurnodoFuncionario();">
					<option value="">---</option>
					<?
					$result_fun= mysql_query("select *
												from  pessoas, rh_funcionarios, rh_carreiras
												where pessoas.id_pessoa = rh_funcionarios.id_pessoa
												and   pessoas.tipo = 'f'
												and   rh_funcionarios.status_funcionario = '1'
												and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
												order by pessoas.nome_rz asc
												") or die(mysql_error());
					$i=0;
					while ($rs_fun= mysql_fetch_object($result_fun)) {
					?>
					<option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario_solicitante) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
					<? $i++; } ?>
				</select>
				
				<div id="turno_atualiza">
					
				</div>
				
				<br />
				
				<label for="id_funcionario_assume">* Funcionário que assume:</label>
				<select name="id_funcionario_assume" id="id_funcionario_assume" title="Funcinário que assume">
					<option value="">---</option>
					<?
					$result_fun= mysql_query("select *
												from  pessoas, rh_funcionarios, rh_carreiras
												where pessoas.id_pessoa = rh_funcionarios.id_pessoa
												and   pessoas.tipo = 'f'
												and   rh_funcionarios.status_funcionario = '1'
												and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
												and   rh_carreiras.atual = '1'
												and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
												order by pessoas.nome_rz asc
												") or die(mysql_error());
					$i=0;
					while ($rs_fun= mysql_fetch_object($result_fun)) {
					?>
					<option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario_assume) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
					<? $i++; } ?>
				</select>
				<br />
            <? }  else { ?>
            <label>Data:</label>
            <?= desformata_data($rs->data_escala_troca); ?>
            <br /><br />
            
            <label>Funcionário solicitante:</label>
            <?= pega_funcionario($rs->id_funcionario_solicitante); ?>
            <br /><br />
            
            <label>Funcionário que assume:</label>
            <?= pega_funcionario($rs->id_funcionario_assume); ?>
            <br /><br />
            
            <? } ?>
            
            <label for="justificativa">Justificativa:</label>
            <input title="justificativa" name="justificativa" id="Justificativa" value="<?= $rs->justificativa; ?>" />
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>