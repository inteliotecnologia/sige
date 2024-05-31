<?
require_once("conexao.php");
if (pode_algum("ey", $_SESSION["permissao"])) {
?>

<h2>Percursos</h2>

<div id="conteudo_interno">
    <div class="parte50">
        <fieldset>
            <legend>Diário</legend>
            
            <? if ($_GET["geral"]==1) { ?>
            <form action="index2.php?pagina=transporte/percurso_relatorio" target="_blank" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=transporte/percurso_listar" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" id="id_veiculo" title="Veículo">
                    <option value="">- TODOS -</option>
                    
                    <?
                    $result_vei= mysql_query("select * from op_veiculos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by veiculo asc,
                                                placa asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="data_percurso">Data:</label>
                <input name="data_percurso" id="data_percurso" class="tamanho25p" onkeyup="formataData(this);" maxlength="10" value="<?= date("d/m/Y"); ?>" title="Data" />
                <br />
                
                <label for="tipo">Percurso:</label>
                <select class="tamanho25p" name="tipo" id="tipo" title="Tipo">
                    <option value="">- TODOS -</option>
                    <?
					$vetor= pega_coleta_entrega('l');
					$i=1;
					while ($vetor[$i]) {
					?>
					<option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tipo==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
					<? $i++; } ?>
                    
                    <? /*<option value="1" <? if ($rs->tipo=="1") echo "selected=\"selected\""; ?>>Coleta</option>
                    <option value="2" <? if ($rs->tipo=="2") echo "selected=\"selected\""; ?> class="cor_sim">Entrega</option>
                    <option value="3" <? if ($rs->tipo=="3") echo "selected=\"selected\""; ?>>Outro</option>*/ ?>
                </select>
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
                
                <? if ($_GET["geral"]==1) { ?>
                <label>Tipo:</label>
                <select class="tamanho25p" name="tipo_relatorio" id="tipo_relatorio" title="Tipo de relatório">	  		
                    <option class="cor_sim" value="1">Sintético</option>
                    <option value="2">Analítico</option>
                </select>
                <br />
                <? } ?>
                    
                <br /><br /><br />
                
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
            <form action="index2.php?pagina=transporte/percurso_relatorio" target="_blank" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } else { ?>
            <form action="./?pagina=transporte/percurso_listar" method="post" name="formPercursoBuscar" id="formPercursoBuscar" onsubmit="return validaFormNormal('validacoes');">
            <? } ?>
    
                <input class="escondido" type="hidden" id="validacoes" value="" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="id_veiculo">Veículo:</label>
                <select name="id_veiculo" id="id_veiculo" title="Veículo">
                    <option value="">- TODOS -</option>
                    
                    <?
                    $result_vei= mysql_query("select * from op_veiculos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                order by veiculo asc,
                                                placa asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_vei = mysql_fetch_object($result_vei)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_vei->id_veiculo; ?>" <? if ($rs_vei->id_veiculo==$rs->id_veiculo) echo "selected=\"selected\""; ?>><?= $rs_vei->veiculo ." ". $rs_vei->placa; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="periodo">Período:</label>
                <select class="tamanho25p" name="periodo" id="periodo" title="Período">	  		
                    <?
                    $i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                                from tr_percursos_passos
												where data_percurso <> '0000-00-00'
												order by data_percurso desc ");
                    while ($rs_per= mysql_fetch_object($result_per)) {
                        $data_percurso= explode('/', $rs_per->data_percurso2);
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="tipo">Percurso:</label>
                <select class="tamanho25p" name="tipo" id="tipo" title="Tipo">
                    <option value="">- TODOS -</option>
                    <?
					$vetor= pega_coleta_entrega('l');
					$i=1;
					while ($vetor[$i]) {
					?>
					<option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tipo==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
					<? $i++; } ?>
                    
                    <? /*
					<option value="1" <? if ($rs->tipo=="1") echo "selected=\"selected\""; ?>>Coleta</option>
                    <option value="2" <? if ($rs->tipo=="2") echo "selected=\"selected\""; ?> class="cor_sim">Entrega</option>
                    <option value="3" <? if ($rs->tipo=="3") echo "selected=\"selected\""; ?>>Outro</option>
					*/ ?>
                </select>
                <br /><br />
                
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
                
                <? if ($_GET["geral"]==1) { ?>
                <label>Tipo:</label>
                <select class="tamanho25p" name="tipo_relatorio" id="tipo_relatorio" title="Tipo de relatório">	  		
                    <option class="cor_sim" value="1">Sintético</option>
                    <option value="2">Analítico</option>
                </select>
                <br />
                <? } ?>
                    
                <br /><br /><br />
                
                <center>
                    <button type="submit" id="enviar">Enviar &raquo;</button>
                </center>
            </form>
            
        </fieldset>
    </div>
</div>

<? } ?>