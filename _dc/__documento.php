<?
require_once("conexao.php");
if (pode("d", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  dc_documentos
								where id_documento = '". $_GET["id_documento"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Documento</h2>

<form action="<?= AJAX_FORM; ?>formDocumento&amp;acao=<?= $acao; ?>" method="post" name="formDocumento" id="formDocumento" onsubmit="return validaFormNormal('validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_pasta@vazio|documento@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_documento" class="escondido" type="hidden" id="id_documento" value="<?= $rs->id_documento; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
        	<label for="id_departamento">* Departamento:</label>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="carregaPastas(this.value);">
                    <option value="">- TODOS -</option>
                    
					<?
					if ($acao=='i') $id_departamento_aqui= $_GET["id_departamento"];
					else $id_departamento_aqui= pega_departamento_pasta($rs->id_pasta);
					
					$result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
												where pessoas.id_pessoa = empresas.id_pessoa
												and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
												and   pessoas_tipos.tipo_pessoa = 'a'
												order by 
												pessoas.nome_rz asc");
					while ($rs_emp= mysql_fetch_object($result_emp)) {
					?>
					<optgroup class="opt1" label="<?= $rs_emp->apelido_fantasia; ?>">
						<?
						$result_cc= mysql_query("select *
													from  rh_departamentos
													where id_empresa = '". $rs_emp->id_empresa ."'
													order by departamento asc
													") or die(mysql_error());
						$i=0;
						while ($rs_cc= mysql_fetch_object($result_cc)) {
						?>
						<option <? if ($i%2==0) echo "class=\"cor_sim\""; else echo "class=\"cor_nao\""; ?> <? if ($id_departamento_aqui==$rs_cc->id_departamento) echo "selected=\"selected\""; ?> value="<?= $rs_cc->id_departamento; ?>"><?= $rs_cc->departamento; ?></option>
                        <? $i++; } ?>
                    <? } ?>
                </select>
            </div>
            <br /><br />
            
            <label for="num_pasta">Número:</label>
            <input title="Pasta" name="pasta" value="<?=$rs->pasta;?>" id="pasta" class="tamanho15p" onblur="selecionaPasta(this.value);" />
            <br />
            
            <label>ou</label>
            <br /><br />
            
        	<label for="id_pasta">* Pasta:</label>
            <div id="div_pastas">
                <select name="id_pasta" id="id_pasta" title="Pasta">
                    <option value="">-</option>
                    <?
                    $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                                where pessoas.id_pessoa = empresas.id_pessoa
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'a'
                                                order by 
                                                pessoas.nome_rz asc");
                    while ($rs_emp= mysql_fetch_object($result_emp)) {
                    ?>
                    <optgroup class="opt1" label="<?= $rs_emp->apelido_fantasia; ?>">
                        <?
                        $result_cc= mysql_query("select *
                                                    from  rh_departamentos
                                                    where id_empresa = '". $rs_emp->id_empresa ."'
                                                    order by departamento asc
                                                    ") or die(mysql_error());
                        while ($rs_cc= mysql_fetch_object($result_cc)) {
                        ?>
                        <optgroup class="opt2" label="<?= $rs_cc->departamento; ?>">
                            <?
                            $result_cc2= mysql_query("select *
                                                        from  dc_documentos_pastas
                                                        where /* id_empresa = '". $_SESSION["id_empresa"] ."'
                                                        and   */ id_departamento = '". $rs_cc->id_departamento ."'
                                                        order by pasta asc
                                                        ") or die(mysql_error());
                            $i=0;
                            while ($rs_cc2= mysql_fetch_object($result_cc2)) {
                            ?>
                            <option <? if ($i%2==0) echo "class=\"cor_sim\""; else echo "class=\"cor_nao\""; ?> value="<?= $rs_cc2->id_pasta; ?>" <? if (($rs_cc2->id_pasta==$rs->id_pasta) || ($rs_cc2->id_pasta==$_GET["id_pasta"])) echo "selected=\"selected\""; ?>><?= $rs_cc2->pasta ." - ". $rs_cc2->nome_pasta ." (". ativo_inativo($rs_cc2->status_pasta) .")"; ?></option>
                            <? $i++; } ?>
                        </optgroup>
                        <? } ?>
                    </optgroup>
                    <? } ?>
                </select>
            </div>
            <br />
            
            <label for="documento">* Documento:</label>
            <input title="Documento" name="documento" value="<?= $rs->documento; ?>" id="documento" />
            <br />
        </div>
        <div class="parte50">
        	
            <label for="data_emissao">Data de emissão:</label>
            <input id="data_emissao" name="data_emissao" class="tamanho25p" value="<?= desformata_data($rs->data_emissao); ?>" title="Data da emissão" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <label for="data_vencimento">Data de vencimento:</label>
            <input id="data_vencimento" name="data_vencimento" class="tamanho25p" value="<?= desformata_data($rs->data_vencimento); ?>" title="Data da vencimento" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <label for="alerta_dias">Alertar ? dias antes:</label>
            <input title="Alerta" name="alerta_dias" class="tamanho15p" value="<?= $rs->alerta_dias; ?>" id="alerta_dias" />
            <br />
            
            <label for="obs">Observações:</label>
            <textarea title="Observações" name="obs" id="obs"><?= $rs->obs; ?></textarea>
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>

<script language="javascript">
	daFoco("documento");
</script>
<? } ?>