<?
require_once("conexao.php");
if (pode("t", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *, DATE_FORMAT(data_ligacao, '%d/%m/%Y') as data_ligacao2 from tel_contatos_ligacoes
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_ligacao = '". $_GET["id_ligacao"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Controle de ligações</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formLigacao&amp;acao=<?= $acao; ?>" method="post" name="formLigacao" id="formLigacao" onsubmit="return ajaxForm('conteudo', 'formLigacao', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="telefone@vazio|para@vazio|data_ligacao@data|hora_ligacao@vazio|id_funcionario@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_ligacao" class="escondido" type="hidden" id="id_ligacao" value="<?= $rs->id_ligacao; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados do ligação</legend>
        
        <div class="parte50">
            
            <label for="telefone">* Telefone:</label>
		    <input title="Telefone" name="telefone" id="telefone" <? if ($acao=='i') { ?> onblur="verificaTelefone(this.value);" <? } ?> <? if ($_GET["telefone"]!='') { ?> onfocus="daFoco('id_funcionario');" value="<?=$_GET["telefone"];?>" <? } else { ?>  value="<?=$rs->telefone;?>" <? } ?> onkeyup="ajeitaMascaraTelefone(this);" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="para">* Para:</label>
            <div id="para_area">
	            <input title="Para" name="para" id="para" value="<?= $rs->para; ?>" />
            </div>
            <br />
            
            <label for="data_ligacao">* Data da ligação:</label>
            <?
			if ($acao=='i') $data_ligacao= date("d/m/Y");
			else $data_ligacao= $rs->data_ligacao2;
			?>
            <input name="data_ligacao" id="data_ligacao" title="Data da ligação" value="<?= $data_ligacao; ?>" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <label for="hora_ligacao">* Hora da ligação:</label>
            <?
			if ($acao=='i') $hora_ligacao= date("H:i:s");
			else $hora_ligacao= $rs->hora_ligacao;
			?>
            <input name="hora_ligacao" id="hora_ligacao" title="Hora da ligação" value="<?= $hora_ligacao; ?>" onkeyup="formataHora(this);" maxlength="8" />
            <br />
            
        </div>
        <div class="parte50">
			<label for="id_funcionario">* Solicitante:</label>
            <select name="id_funcionario" id="id_funcionario" title="Solicitante">
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
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
                <option value="0" <? if ("0"==$rs->id_funcionario) echo "selected=\"selected\""; ?>>--- OUTRO FUNCIONÁRIO</option>
            </select>
            <br />
            
            <label for="obs">Observações:</label>
            <textarea name="obs" id="obs" title="Observa&ccedil;&otilde;es"><?= $rs->obs; ?></textarea>
            <br />
    </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
    <script language="javascript" type="text/javascript">
		daFoco('telefone');
	</script>
</form>
<? } ?>