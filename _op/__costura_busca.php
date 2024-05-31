<?
require_once("conexao.php");
if (pode("pl(", $_SESSION["permissao"])) {
?>

<h2>Área limpa - Costura</h2>

<div id="conteudo_interno">
	<? if ($_GET["geral"]!=1) { ?>
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/costura_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/costura_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
                    
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
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
                
                <label for="id_peca">Roupa:</label>
                <select id="id_peca" name="id_peca">
                	<option value="">- TODAS -</option>
                    <?
					$result_pecas= mysql_query("select * from op_limpa_pecas
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												order by peca asc
												");
					$j=1;
					while ($rs_peca= mysql_fetch_object($result_pecas)) {
					?>
					<option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_peca) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
					<? $j++; } ?>
                </select>
                <br />
                
                <br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
	</div>
    <? } ?>
    
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/costura_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/costura_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
                    
                <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" title="Período">	  		
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
                <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza2');">
                    <option value="1">Ativos</option>
                    <option value="0" class="cor_sim">Inativos</option>
                </select>
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <div id="id_cliente_atualiza2">
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
                
                <? if ($_GET["geral"]!=1) { ?>
                <label for="id_peca">Roupa:</label>
                <select id="id_peca" name="id_peca">
                	<option value="">- TODAS -</option>
                    <?
					$result_pecas= mysql_query("select * from op_limpa_pecas
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												order by peca asc
												");
					$j=1;
					while ($rs_peca= mysql_fetch_object($result_pecas)) {
					?>
					<option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_peca) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
					<? $j++; } ?>
                </select>
                <br />
                <? } ?>
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
</script>
<? } ?>