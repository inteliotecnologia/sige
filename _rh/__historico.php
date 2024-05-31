<?
require_once("conexao.php");
if (pode_algum("rv", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	
	if ($acao=='e') {
		$result= mysql_query("select *
								from  rh_historico
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_historico = '". $_GET["id_historico"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<h2>Histórico interno</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formHistorico&amp;acao=<?= $acao; ?>" method="post" name="formHistorico" id="formHistorico" onsubmit="return ajaxForm('conteudo', 'formHistorico', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" name="validacoes" value="data_historico@data|id_funcionario@vazio|historico@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_historico" class="escondido" type="hidden" id="id_historico" value="<?= $rs->id_historico; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="razao_social">Empresa:</label>
            <?= pega_empresa($_SESSION["id_empresa"]); ?>
            <br />
            
            <?
			if ($acao=='i') $data_historico= date("d/m/Y");
			else $data_historico= desformata_data($rs->data_historico);
			?>
            <label for="data_historico">* Data:</label>
            <input id="data_historico" name="data_historico" class="tamanho25p" value="<?= $data_historico; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
            <br />
            
            <label for="id_funcionario">* Funcionário:</label>
            <select name="id_funcionario" id="id_funcionario" title="Funcinário">
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
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="historico">* Histórico:</label>
            <textarea title="Histórico" name="historico" id="historico"><?= $rs->historico; ?></textarea>
            <br />
            
        </div>
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>