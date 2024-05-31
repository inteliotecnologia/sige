<?
require_once("conexao.php");
if (pode_algum("rhv4", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	$origem= 'b';
	
	$acao='i';
	if ($_GET["acao"]!="") $acao= $_GET["acao"];
	if ($_POST["acao"]!="") $acao= $_POST["acao"];
	
	if ($acao=='e') {
		
		$result= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2,
								DATE_FORMAT(vale_dia, '%d/%m/%Y') as vale_dia2
								from rh_ponto
								where id_horario = '". $id_horario ."'
								") or die(mysql_error());
								
		if (mysql_num_rows($result)==1) {
			$rs= mysql_fetch_object($result);
			$id_funcionario= $rs->id_funcionario;
		} else die();
			
	}
?>

<? if ($_GET["data"]!="") { ?>
<a href="javascript:void(0);" onclick="fechaDiv('tela_banco_horas');" class="fechar">x</a>                
<h2>Banco de horas</h2>
<? } ?>

<div id="conteudo_flutuante">
    
    <form action="<?= AJAX_FORM; ?>formBancoHoras&amp;acao=<?=$acao;?>" method="post" name="formBancoHoras" id="formBancoHoras" onsubmit="return ajaxForm('conteudo_flutuante', 'formBancoHoras', 'validacoes_banco', true);">
        
        <input class="escondido" type="hidden" id="validacoes_banco" value="id_funcionario@vazio|data_he@data|he@vazio" />
        
        <input type="hidden" title="Funcionário" class="escondido" id="id_funcionario" name="id_funcionario" value="<?= $id_funcionario; ?>" />
        <input type="hidden" class="escondido" id="data1f_banco" name="data1f_banco" value="<?= $_GET["data1f"]; ?>" />
        <input type="hidden" class="escondido" id="data2f_banco" name="data2f_banco" value="<?= $_GET["data2f"]; ?>" />
        
      <input type="hidden" class="escondido" id="origem" name="origem" value="<?= $origem; ?>" />
        
        <label>Funcionário:</label>
        <? if ($id_funcionario=="") { ?>
        <select name="id_funcionario" id="id_funcionario" title="Funcionário">
			<?
            $result_fun= mysql_query("select *
                                        from  pessoas, rh_funcionarios, rh_carreiras
                                        where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                        and   pessoas.tipo = 'f'
                                        and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                        and   rh_carreiras.atual = '1'
                                        and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                        order by pessoas.nome_rz asc
                                        ") or die(mysql_error());
            $i=0;
            while ($rs_fun= mysql_fetch_object($result_fun)) {
            ?>
            <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$_GET["id_funcionario"]) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
            <? $i++; } ?>
        </select>
        <?
        }
		else
			echo pega_funcionario($id_funcionario);
		?>
        <br />
        
        <label>Operação:</label>
        <input onclick="abreDiv('operacoes_debito');" type="radio" class="tamanho30" name="operacao" id="operacao_0" value="0" <? if ($_GET["operacao"]==0) echo "checked=\"checked\""; ?> />
        <label for="operacao_0" class="tamanho50 nao_negrito alinhar_esquerda">Débito</label>
        
        <input onclick="fechaDiv('operacoes_debito');" type="radio" class="tamanho30" name="operacao" id="operacao_1" value="1" <? if ($_GET["operacao"]==1) echo "checked=\"checked\""; ?> />
        <label for="operacao_1" class="tamanho50 nao_negrito alinhar_esquerda">Crédito</label>
        <br />
        
        <div id="operacoes_debito" <? if ($_GET["operacao"]==1) { ?> class="nao_mostra" <? } ?>>
        <label>&nbsp;</label>
        <input type="radio" class="tamanho30" name="operacao_debito" id="operacao_debito_0" value="0" <? if ($_GET["operacao_debito"]==0) echo "checked=\"checked\""; ?> />
        <label for="operacao_debito_0" class="tamanho50 nao_negrito alinhar_esquerda">Mudança de horário/folga</label>
        
        <input type="radio" class="tamanho30" name="operacao_debito" id="operacao_debito_1" value="1" <? if ($_GET["operacao_debito"]==1) echo "checked=\"checked\""; ?> />
        <label for="operacao_debito_1" class="tamanho50 nao_negrito alinhar_esquerda">Pagamento de hora extra</label>
        <br />
        </div>
        
        <label for="data_he">Data:</label>
        <input name="data_he" id="data_he"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?= desformata_data($_GET["data"]); ?>" title="Referente à" />
        <br />
        
        <? /*
        <label>Tipo:</label>
        <input type="radio" class="tamanho30" name="tipo_he" id="tipo_he_0" value="0" <? if ($_GET["tipo_he"]==0) echo "checked=\"checked\""; ?> />
        <label for="tipo_he_0" class="tamanho50 nao_negrito alinhar_esquerda">Diurna</label>
        
        <input type="radio" class="tamanho30" name="tipo_he" id="tipo_he_1" value="1" <? if ($_GET["tipo_he"]==1) echo "checked=\"checked\""; ?> />
        <label for="tipo_he_1" class="tamanho50 nao_negrito alinhar_esquerda">Noturna</label>
        <br />
        */ ?>
        
        <label for="he">Horas:</label>
        <input name="he" id="he" onkeyup="formataHora(this);" value="<?= calcula_total_horas($_GET["he"]); ?>" maxlength="8" title="Horas" />
        <br />
        
        <label for="obs">Observação:</label>
        <textarea name="obs" id="obs"></textarea>
        <br />
        
    <br /><br />
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </form>
</div>
<? } ?>