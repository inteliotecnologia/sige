<?
require_once("conexao.php");
if (pode("u", $_SESSION["permissao"])) {
	if ($acao=="") $acao= $_GET["acao"];
	if ($acao=='e') {
		if ($_GET["id_refeicao"]!="") $id_refeicao= $_GET["id_refeicao"];
		if ($_POST["id_refeicao"]!="") $id_refeicao= $_POST["id_refeicao"];
		
		$result= mysql_query("select * from fi_refeicoes
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_refeicao = '". $id_refeicao ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
	}
?>
<h2>Controle de refeicões</h2>

<?
if (($acao=='e') && (mysql_num_rows($result)==0)) {
	echo "Requisição não encontrada!";
	die();
}
?>

<form action="<?= AJAX_FORM; ?>formRefeicao&amp;acao=<?= $acao; ?>" method="post" name="formRefeicao" id="formRefeicao" onsubmit="return ajaxForm('conteudo', 'formRefeicao', 'validacoes', true);">
    
    <?
    if ($acao=='e') {
		$validacoes_adicional= "valor@vazio|"; 
	?>
    <input name="id_refeicao" class="escondido" type="hidden" id="id_refeicao" value="<?= $rs->id_refeicao; ?>" />
    <? } ?>
    <input class="escondido" type="hidden" id="validacoes" value="data@data|id_motivo@vazio|id_departamento@vazio|id_turno@vazio|num_almocos@vazio|tipo_almoco@vazio|opcao_almoco@vazio|<?=$validacoes_adicionais;?>id_usuario_at@vazio" />
    
    <fieldset>
        <legend>Formulário</legend>
        
        <div class="parte50">
            
            <?
			if ($acao=='i') $data= date("d/m/Y");
			else $data= desformata_data($rs->data);
			?>
            <label for="data">* Data:</label>
            <input id="data" name="data" class="tamanho25p" value="<?= $data; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
            
			<?
			/*
			if ($acao=='i') $hora= date("H:i:s");
			else $hora= $rs->hora;
			?>
            <input id="hora" name="hora" <? if ($acao=='i') echo "disabled=\"disabled\""; ?> class="tamanho25p" value="<?= $hora; ?>" title="Hora da lavagem"  onkeyup="formataHora(this);" maxlength="8" />
            */ ?>
            <br />
            
            <label for="id_funcionario">Entregue para:</label>
            <select name="id_funcionario" id="id_funcionario" title="Funcionário">
            	<? if ($acao=='i') { ?>
                <option value="">- SELECIONE -</option>
                <? } ?>
                
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
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_departamento">Departamento:</label>
            <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnosSohMultiplo(this.value, '1');">
                <option value="">- TODOS -</option>
                <?
                $result_dep= mysql_query("select * from rh_departamentos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                             ");
                $i=0;
                while ($rs_dep = mysql_fetch_object($result_dep)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_turno">Turno:</label>
            <div id="id_turno_atualiza_1">
                <select name="id_turno" id="id_turno" title="Turno">	  		
                    <option value="">- TURNO -</option>
                    <?
                    $result_tur= mysql_query("select * from rh_turnos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_tur = mysql_fetch_object($result_tur)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <br /><br />
            
            <label for="id_motivo">Motivo:</label>
            <select name="id_motivo" id="id_motivo" title="Motivo">
                <option value="">---</option>
                <?
				$result_mot= mysql_query("select * from  rh_motivos
											where rh_motivos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_motivos.tipo_motivo = 'r'
											order by rh_motivos.motivo asc
											") or die(mysql_error());
                $i=0;
                while ($rs_mot= mysql_fetch_object($result_mot)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mot->id_motivo; ?>" <? if ($rs_mot->id_motivo==$rs->id_motivo) echo "selected=\"selected\""; ?>><?= $rs_mot->motivo; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="num_almocos">* Nº almoços:</label>
            <input name="num_almocos" id="num_almocos" class="tamanho25p espaco_dir" value="<?= $rs->num_almocos;?>" title="Nº almoços" />
            <br />
            
            <label for="tipo_almoco">* Tipo de almoço:</label>
            <select name="tipo_almoco" id="tipo_almoco" title="Tipo de almoço">
                <option value="">---</option>
                <?
                $vetor= pega_tipo_almoco('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($i==$rs->tipo_almoco) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="opcao_almoco">* Bebida:</label>
            <select name="opcao_almoco" id="opcao_almoco" title="Bebida">
                <option value="">---</option>
                <?
                $vetor= pega_opcao_almoco('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if ($i==$rs->opcao_almoco) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
        </div>
        <div class="parte50">
			<? //if ($acao=="e") { ?>
            <label for="valor_total">* Valor total:</label>
            <? if ($rs->valor_total=="0") $valor_total= ""; else $valor_total= fnum($rs->valor_total); ?>
            <input id="valor_total" name="valor_total" class="espaco_dir tamanho25p" value="<?= $valor_total; ?>" onkeydown="formataValor(this,event);" title="Valor por litro" />
            <br />
            <? //} ?>
            
            <label for="id_usuario_at">* Autorizado por:</label>
            <select name="id_usuario_at" id="id_usuario_at" title="Autorizado por">
                <option value="">---</option>
				<?
                $result_fun= mysql_query("select *
                                            from  pessoas, rh_funcionarios, rh_carreiras, rh_departamentos
                                            where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                            and   pessoas.tipo = 'f'
                                            and   rh_funcionarios.status_funcionario = '1'
                                            and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                            and   rh_carreiras.atual = '1'
											and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
											and   rh_departamentos.pode_autorizar = '1'
                                            and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by pessoas.nome_rz asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_fun= mysql_fetch_object($result_fun)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$rs->id_usuario_at) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="obs1">Observações:</label>
            <textarea title="Observações" name="obs1" id="obs1"><?= $rs->obs; ?></textarea>
            <br />
            
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>