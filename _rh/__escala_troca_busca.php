<?
require_once("conexao.php");
if (pode("rw", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de troca de escala</h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca do período</legend>
            
            <div class="parte50">
                <form action="<?= AJAX_FORM; ?>formEscalaTrocaBuscar" method="post" name="formEscalaTrocaBuscar" id="formEscalaTrocaBuscar" onsubmit="return ajaxForm('conteudo', 'formEscalaTrocaBuscar');">
                    
                    <input class="escondido" type="hidden" id="validacoes" value="" />
                    
                    <label for="id_funcionario_solicitante">Funcionário (solicitante):</label>
                    <select name="id_funcionario_solicitante" id="id_funcionario_solicitante" title="Funcinário solicitante">
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
                    <br />
                    
                    <label for="id_funcionario_assume">Funcionário (assume):</label>
                    <select name="id_funcionario_assume" id="id_funcionario_assume" title="Funcinário solicitante">
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
                    <br />
                    
                    <label for="data_escala_troca">Data:</label>
                    <input id="data_escala_troca" name="data_escala_troca" class="tamanho25p" value="<?= $data_escala_troca; ?>" title="Data" onkeyup="formataData(this);" maxlength="10" />
                    <br />
                   
                    <label>&nbsp;</label>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                    <br />
                
                </form>
            </div>
        
    </fieldset>
</div>

<? } ?>