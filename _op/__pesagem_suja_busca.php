<?
require_once("conexao.php");
if (pode_algum("psl", $_SESSION["permissao"])) {
?>

<h2>Área suja - Pesagem</h2>

<div id="conteudo_interno">
    <? if ($_GET["geral"]!=2) { ?>
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/pesagem_suja_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/pesagem_suja_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="d" />
                    
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <? if ($_GET["geral"]=="") { ?>
                
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
                
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TODOS -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '2'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="goma">Goma:</label>
                <select name="goma" id="goma" title="Goma">	  		
                    <option value="">- TODOS -</option>
                    <option value="1" class="cor_sim">Sim</option>
                    <option value="0">Não</option>
                </select>
                <br />
                
                <? } else { ?>
                <label>Tipo:</label>
                <select class="tamanho25p" name="tipo_detalhamento_relatorio" id="tipo_detalhamento_relatorio" title="Tipo de relatório">	  		
                    <option class="cor_sim" value="1">Sintético</option>
                    <option value="2">Analítico</option>
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
    <? } ?>
    <div class="parte50">
        <fieldset>
            <legend>Mensal</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=op/pesagem_suja_relatorio" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } elseif ($_GET["geral"]==2) { ?>
            <form action="index2.php?pagina=op/pesagem_suja_relatorio_xls" target="_blank" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=op/pesagem_suja_listar" method="post" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_relatorio" value="m" />
                
                <label for="periodo">Período:</label>
                <select class="tamanho25p" name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_pesagem, '%m/%Y')) as data_batida2
                                                  from op_suja_pesagem order by data_pesagem desc ");
                    
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_batida= explode('/', $rs_per->data_batida2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>" <? if ($_POST["periodo"]==$rs_per->data_batida2) echo "selected=\"selected\""; ?>><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Entre datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
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
                
                <? if ($_GET["geral"]=="") { ?>
                <label for="id_turno">Turno:</label>
                <div id="id_turno_atualiza">
                    <select name="id_turno" id="id_turno" title="Turno">	  		
                        <option value="">- TODOS -</option>
                        <?
                        $result_tur= mysql_query("select * from rh_turnos
                                                    where id_departamento = '2'
                                                     ");
                        $i=0;
                        while ($rs_tur = mysql_fetch_object($result_tur)) {
                        ?>
                        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                        <? $i++; } ?>
                    </select>
                </div>
                <br />
                
                <label for="goma">Goma:</label>
                <select name="goma" id="goma" title="Goma">	  		
                    <option value="">- TODOS -</option>
                    <option value="1" class="cor_sim">Sim</option>
                    <option value="0">Não</option>
                </select>
                <br />
                
                <? } else { ?>
                <br /><br />
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