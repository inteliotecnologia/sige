<?
require_once("conexao.php");
if (pode_algum("r", $_SESSION["permissao"])) {
?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Treinamentos por funcionário</h2>

<fieldset>
    <legend>Formulário de busca</legend>
    	
        <div class="parte50">
        	<form action="index2.php?pagina=rh/treinamento_relatorio&amp;tipo_relatorio=2" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
                
                <input class="escondido" type="hidden" name="geral" id="geral" value="<?=$_GET["geral"];?>" />
                
                <label for="id_funcionario">Funcionário:</label>
                <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                    <option value="">- SELECIONE -</option>
					<?
                    $result_fun= mysql_query("select * from pessoas, rh_funcionarios, rh_carreiras, rh_treinamentos_funcionarios
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
												and   rh_funcionarios.id_funcionario = rh_treinamentos_funcionarios.id_funcionario
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
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$_GET["id_funcionario"]) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
                
            </form>
        </div>
	
</fieldset>

<? } ?>