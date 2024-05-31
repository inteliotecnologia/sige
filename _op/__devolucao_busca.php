<?
require_once("conexao.php");
if (pode("psl", $_SESSION["permissao"])) {
?>

<h2>Área suja - Devoluções de resíduos</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
        	
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/devolucao_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/devolucao_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <label for="id_regiao">Região:</label>
                <select name="id_regiao" id="id_regiao" title="Região" onchange="alteraRegiao(this.value, '', 'i');">
                    <option value="">- TODAS -</option>
                    <?
                    $result_reg= mysql_query("select * from fi_clientes_regioes
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   status_regiao = '1'
                                                order by regiao asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_reg = mysql_fetch_object($result_reg)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_reg->id_regiao; ?>" <? if ($rs_reg->id_regiao==$rs->id_regiao) echo "selected=\"selected\""; ?>><?= $rs_reg->regiao; ?></option>
                    <? $i++; } ?>
                </select>
                <br /><br />
                
                <div id="percurso">
                
                </div>
                
                <? /*
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" title="Cliente">
                    <option value="">- TODOS -</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_cliente_tipo = '1'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cli = mysql_fetch_object($result_cli)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                */ ?>
                
                
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
        	
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/devolucao_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/devolucao_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período" class="tamanho25p">
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_remessa, '%m/%Y')) as data_remessa2 from op_suja_remessas order by data_remessa desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_remessa= explode('/', $rs_per->data_remessa2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_remessa2; ?>"><?= traduz_mes($data_remessa[0]) .'/'. $data_remessa[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="lista">Lista:</label>
                <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza1');">
                    <option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                </select>
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <div id="id_cliente_atualiza1">
                    <select name="id_cliente" id="id_cliente" title="Cliente">
                        <option value="">- TODOS -</option>
                        <?
                        $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                    and   pessoas_tipos.tipo_pessoa = 'c'
                                                    and   pessoas.id_cliente_tipo = '1'
													and   pessoas.status_pessoa = '1'
                                                    order by 
                                                    pessoas.apelido_fantasia asc
                                                    ") or die(mysql_error());
                        $i=0;
                        while ($rs_cli = mysql_fetch_object($result_cli)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<script language="javascript" type="text/javascript">
	daFoco("data");
	
	alteraRegiao('', '', 'i');
</script>

<? } ?>