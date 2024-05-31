<?
require_once("conexao.php");
if (pode("t", $_SESSION["permissao"])) {
	
	if ($_GET["status_funcionario"]!="") $status_funcionario= $_GET["status_funcionario"];
	if ($_POST["status_funcionario"]!="") $status_funcionario= $_POST["status_funcionario"];
	
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select * from tel_contatos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_contato = '". $_GET["id_contato"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Contatos telefônicos</h2>

<form action="<?= AJAX_FORM; ?>formContato&amp;acao=<?= $acao; ?>" method="post" name="formContato" id="formContato" onsubmit="return ajaxForm('conteudo', 'formContato', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="nome@vazio|tipo_contato@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_contato" class="escondido" type="hidden" id="id_contato" value="<?= $rs->id_contato; ?>" />
    <? } ?>
    
    <input name="status_funcionario" class="escondido" type="hidden" id="status_funcionario" value="<?= $status_funcionario; ?>" />
    
    
    <fieldset>
        <legend>Dados do funcionário</legend>
        
        <div class="parte50">
            <br />
            
            <label for="nome">* Nome:</label>
            <input title="Nome" name="nome" id="nome" value="<?= $rs->nome; ?>" />
            <br />
            
            <label for="tipo_contato">* Tipo:</label>
            <select name="tipo_contato" id="tipo_contato" title="Tipo de contato" onchange="alteraTipoContatoFuncionario(this.value);">
                <option value="">---</option>
                <?
                $vetor= pega_tipo_contato('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option value="<?=$i;?>" <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> <? if (($i==$rs->tipo_contato) || ($i==$_GET["tipo_contato"])) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <? if (($acao=="e") && ($rs->tipo_contato==2)) { ?>
            <div id="funcionario_identificacao">
                <label for="id_pessoa">Funcionário:</label>
                    <select name="id_pessoa" id="id_pessoa" title="Funcionário">
                    <option value="0">- NENHUM -</option>
					<?
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_funcionarios.status_funcionario <> '2'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_pessoa; ?>" <? if ($rs_fun->id_pessoa==$rs->id_pessoa) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
            </div>
            <? } ?>
            
            <label for="email">E-mail:</label>
            <input title="E-mail" name="email" id="email" value="<?= $rs->email; ?>" />
            <br />
            
            <label for="obs1">Observações:</label>
            <textarea title="Observações" name="obs1" id="obs1"><?= $rs->obs; ?></textarea>
            <br />
            
        </div>
        <div class="parte50">
			<fieldset>
            	<legend>Telefones</legend>
                
                <div id="telefones">
                	<?
					if ($acao=='e') {
						$result_tel= mysql_query("select * from tel_contatos_telefones
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_contato = '". $rs->id_contato ."'
													order by tipo asc
													");
						$k=1;
						while ($rs_tel= mysql_fetch_object($result_tel)) {
						?>
                        <div id="div_telefone_<?=$k;?>">
                            <code class="escondido"></code>
                            <label for="telefone_<?=$k;?>">Telefone <?=$k;?>:</label>
                            <input class="tamanho25p" title="Telefone" name="telefone[]" id="telefone_<?=$k;?>" value="<?=$rs_tel->telefone;?>" onkeyup="ajeitaMascaraTelefone(this);" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
                            
                            <select class="tamanho25p" name="tipo[]" id="tipo_<?=$k;?>">
                                <option <? if ($rs_tel->tipo=="1") echo "selected=\"selected\""; ?> value="1">Residencial</option>
                                <option <? if ($rs_tel->tipo=="2") echo "selected=\"selected\""; ?> value="2" class="cor_sim">Comercial</option>
                                <option <? if ($rs_tel->tipo=="3") echo "selected=\"selected\""; ?> value="3">Celular</option>
                                <option <? if ($rs_tel->tipo=="4") echo "selected=\"selected\""; ?> value="4" class="cor_sim">Fax</option>
                                <option <? if ($rs_tel->tipo=="5") echo "selected=\"selected\""; ?> value="5">Outros</option>
                            </select>
                            <br />
                            
                            <label for="obs_<?=$k;?>">OBS <?=$k;?>:</label>
                            <input class="tamanho25p" title="Observação" name="obs[]" id="obs_<?=$k;?>" value="<?=$rs_tel->obs;?>" />
                            
                            <a href="javascript:void(0);" onclick="removeDiv('telefones', 'div_telefone_<?=$k;?>');">remover</a><br />
                        </div>
                        <?
						$k++;
						}
					}
					?>
                </div>
                
                <br /><br />
                <a href="javascript:void(0);" onclick="criaEspacoTelefone();">novo telefone</a>
            </fieldset>
            
            <fieldset>
            	<legend>Aparecer em relatório</legend>
                
                <input <? if (pode('s', $rs->rel)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="rel[]" id="rel_s" value="s" />
                <label for="rel_s" class="alinhar_esquerda nao_negrito">Supervisor</label>
                <br />
            </fieldset>
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>