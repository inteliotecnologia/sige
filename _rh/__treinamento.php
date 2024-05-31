<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	
	if ($_GET["tipo_treinamento"]!="") $tipo_treinamento= $_GET["tipo_treinamento"];
	if ($_POST["tipo_treinamento"]!="") $tipo_treinamento= $_POST["tipo_treinamento"];
	
	if ($tipo_treinamento=='') $tipo_treinamento=1;
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_SESSION["id_empresa"]!="")
			$str= "and   id_empresa = '". $_SESSION["id_empresa"] ."' ";
	
		$result= mysql_query("select *, DATE_FORMAT(data_treinamento, '%d/%m/%Y') as data_treinamento2 from rh_treinamentos
								where rh_treinamentos.id_treinamento = '". $_GET["id_treinamento"] ."'
								$str
								");
							
		$rs= mysql_fetch_object($result);
		
		$tipo_treinamento= $rs->tipo_treinamento;
	}
?>

<h2>Treinamentos <?= pega_tipo_treinamento($tipo_treinamento); ?>s</h2>

<form action="<?= AJAX_FORM; ?>formTreinamento&amp;acao=<?= $acao; ?>" method="post" name="formTreinamento" id="formTreinamento" onsubmit="return validaFormNormal('validacoes', 0, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="treinamento@vazio|data_treinamento@data" />
    <input class="escondido" type="hidden" name="tipo_treinamento" value="<?= $tipo_treinamento; ?>" />
    
    <? if ($acao=='e') { ?>
    <input name="id_treinamento" class="escondido" type="hidden" id="id_treinamento" value="<?= $rs->id_treinamento; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">		
            
            <label for="treinamento">* Treinamento:</label>
            <input title="Treinamento" name="treinamento" value="<?= $rs->treinamento; ?>" id="treinamento" />
            <br />
            
            <label for="data_treinamento">* Data treinamento:</label>
            <input name="data_treinamento" id="data_treinamento" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= $rs->data_treinamento2; ?>" title="Data do treinamento" />
            <br />
            
            <label for="carga_horaria">Carga horária:</label>
            <input title="Carga horária" name="carga_horaria" class="tamanho25p" value="<?= $rs->carga_horaria; ?>" id="carga_horaria" /> horas
            <br />
            
        </div>
        <div class="parte50">
        	<label for="monitor">Monitor:</label>
            <input title="Monitor" name="monitor" value="<?= $rs->monitor; ?>" id="monitor" />
            <br />
            
            <label for="instituicao">Instituição:</label>
            <input title="Instituição" name="instituicao" value="<?= $rs->instituicao; ?>" id="instituicao" />
            <br />
        </div>
    </fieldset>
    
    <fieldset>
    	<legend>Participantes</legend>
        
        <?
		if ($tipo_treinamento==1) {
			$i=0;
			
			$result_dep= mysql_query("select distinct(rh_departamentos.id_departamento), rh_departamentos.departamento from rh_departamentos, rh_carreiras, rh_funcionarios
										where rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.status_funcionario <> '0'
										and   rh_funcionarios.status_funcionario <> '2'
										order by rh_departamentos.departamento asc
										") or die(mysql_error());
			while ($rs_dep = mysql_fetch_object($result_dep)) {
			?>
			<fieldset class="fescala">
				<legend><?= $rs_dep->departamento; ?></legend>
					
				<?
				$result_fun= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   pessoas.tipo = 'f'
											and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_departamento = '". $rs_dep->id_departamento ."'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_funcionarios.status_funcionario <> '0'
											and   rh_funcionarios.status_funcionario <> '2'
											order by pessoas.nome_rz asc
											") or die(mysql_error());
				
				while ($rs_fun= mysql_fetch_object($result_fun)) {
					if ($acao=='e') {
						$result_participante= mysql_query("select id_funcionario from rh_treinamentos_funcionarios
															where id_funcionario = '". $rs_fun->id_funcionario ."'
															and   id_treinamento = '". $rs->id_treinamento ."'
															");
						$linhas_participante= mysql_num_rows($result_participante);
					}
				?>
					<div class="parte33">
						<input class="escondido" type="hidden" name="nada[<?=$i;?>]" value="1" />
						<input class="tamanho30" <? if ($linhas_participante>0) echo "checked=\"checked\""; ?> type="checkbox" name="id_funcionario[<?=$i;?>]" id="id_funcionario_<?=$i;?>" value="<?= $rs_fun->id_funcionario; ?>" />
						<label for="id_funcionario_<?=$i;?>" class="menor nao_negrito alinhar_esquerda tamanho200"><?=$rs_fun->nome_rz;?></label>
					</div>
				</tr>
				<?
					$i++;
				}
				?>
			</fieldset>
    <? } } else { ?>
    
    <label class="tamanho100" for="participantes">Participantes:</label>
    <textarea title="Participantes" name="participantes"><?= $rs->participantes; ?></textarea>
    <br />
    
    <? } ?>
        
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>