<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
	if ($_GET["id_substituicao_funcao"]!="") $id_substituicao_funcao= $_GET["id_substituicao_funcao"];
	if ($_POST["id_substituicao_funcao"]!="") $id_substituicao_funcao= $_POST["id_substituicao_funcao"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_substituicao_funcao
							 	where id_substituicao_funcao = '". $id_substituicao_funcao ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<form action="<?= AJAX_FORM; ?>formSubstituicaoFuncao&amp;acao=<?= $acao; ?>" method="post" name="formSubstituicaoFuncao" id="formSubstituicaoFuncao" onsubmit="return ajaxForm('conteudo_interno', 'formSubstituicaoFuncao', 'validacoes', true);">

    <input class="escondido" type="hidden" id="validacoes" value="id_funcionario@vazio|data_substituicao@data" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_substituicao_funcao" class="escondido" type="hidden" id="id_substituicao_funcao" value="<?= $rs->id_substituicao_funcao; ?>" />
    <? } ?>
       
	<label for="id_funcionario">Funcionário:</label>
    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
        <?
        $result_fun= mysql_query("select * from  pessoas, rh_funcionarios, rh_carreiras
                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                    and   pessoas.tipo = 'f'
                                    and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                    and   rh_carreiras.atual = '1'
                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by pessoas.nome_rz asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_fun= mysql_fetch_object($result_fun)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="data_substituicao">Data:</label>
    <? if ($acao=='i') $data_substituicao= date("d/m/Y"); else $data_substituicao= desformata_data($rs->data_substituicao); ?>
    <input name="data_substituicao" id="data_substituicao" class="tamanho15p" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?=$data_substituicao;?>" />
    <br />
    
    <? if ($acao=='e') { ?>
    <label>Função atual:</label>
    <?= pega_cargo(pega_id_cargo_atual($rs->id_funcionario)); ?>
    <br /><br />
    <? } ?>
    
    <label for="funcao_substituicao">Nova função:</label>
    <input name="funcao_substituicao" id="funcao_substituicao" value="<?=$rs->funcao_substituicao;?>" />
    <br />
    
    <label for="periodo_substituicao">Período:</label>
    <input name="periodo_substituicao" id="periodo_substituicao" value="<?=$rs->periodo_substituicao;?>" />
    <br />
    
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>