<?
require_once("conexao.php");
if (pode_algum("rhv", $_SESSION["permissao"])) {
?>

<h2>Registro do ponto</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Formulário de busca</legend>
            
            <form action="./?pagina=rh/ponto_log_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="data_log">Data:</label>
                <input name="data_log" id="data_log"  onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <label for="hora_log">Hora:</label>
                <input name="hora_log" id="hora_log" class="tamanho25p espaco_dir" onkeyup="formataHora(this);" maxlength="5" value="" title="Data 1" />
                <br />
                
                <label for="id_funcionario">Funcionário:</label>
                <select id="id_funcionario" name="id_funcionario">
                    <option value="">- TODOS -</option>
                    <?
                    $j=0;
                    $result_fun= mysql_query("select *
                                                from  pessoas, rh_funcionarios, rh_carreiras
                                                where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                                and   pessoas.tipo = 'f'
                                                and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                                and   rh_carreiras.atual = '1'
                                                and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   rh_funcionarios.status_funcionario <> '2'
                                                order by pessoas.nome_rz asc
                                                ") or die(mysql_error());
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <label for="tipo">Operação:</label>
                <select id="tipo" name="tipo">
                    <option value="">- TODOS -</option>
                    <option value="-1">Erro</option>
                    <option class="cor_sim" value="1">Entrada</option>
                    <option value="0">Saída</option>
                </select>
                <br />
                
                <br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<script language="javascript" type="text/javascript">
	daFoco("data");
</script>

<? } ?>