<?
require_once("conexao.php");
if (pode("rv", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de histórico interno</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="<?= AJAX_FORM; ?>formHistoricoBuscar" method="post" name="formHistoricoBuscar" id="formHistoricoBuscar" onsubmit="return ajaxForm('conteudo', 'formHistoricoBuscar');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="" />
                    
                    <label>Empresa:</label>
                    <?= pega_empresa($_SESSION["id_empresa"]); ?>
                    <br />
                    
                    <label for="id_funcionario">* Funcionário:</label>
                    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                        <option value="">- SELECIONE -</option>
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
                    <br />
                    
                    <label for="data_historico">* Data:</label>
                    <input id="data_historico" name="data_historico" class="tamanho25p" value="<?= $data_historico; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
                    <br /><br />
                    
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>