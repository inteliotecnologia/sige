<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
	if ($_GET["tipo"]=="1") $tit= "Pedido de lavanderia";
	else $tit= "Relatório de entrega";
?>

<h2><?=$tit;?></h2>

<div class="parte50">
    <fieldset>
        <legend>Entregas</legend>
        
        <form action="index2.php?pagina=op/entrega_coleta_relatorio" target="_blank" method="post" name="formEntrega" id="formEntrega" onsubmit="return validaFormNormal('validacoes');">
                
            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            
            <input class="escondido" type="hidden" name="tipo_relatorio" value="e" />
            
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                            from  tr_percursos_passos
                                            where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                            order by data_percurso desc ");
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_percurso= explode('/', $rs_per->data_percurso2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
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
                <select name="id_cliente" id="id_cliente" title="Cliente" <? /*onchange="procuraPedidos();" */ ?>>
                    <option value="">- TODOS -</option>
                    <?
                    $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
                                                and   pessoas.status_pessoa = '1'
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
            </div>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<div class="parte50">
    <fieldset>
        <legend>Coletas</legend>
        
        <form action="index2.php?pagina=op/entrega_coleta_relatorio" target="_blank" method="post" name="formEntrega" id="formEntrega" onsubmit="return validaFormNormal('validacoes');">

            <input class="escondido" type="hidden" id="validacoes" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            
            <input class="escondido" type="hidden" name="tipo_relatorio" value="c" />
            
            <label for="periodo">Período:</label>
            <select name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_percurso, '%m/%Y')) as data_percurso2
                                            from  tr_percursos_passos
                                            where DATE_FORMAT(data_percurso, '%m/%Y') <> '00/0000'
                                            order by data_percurso desc ");
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_percurso= explode('/', $rs_per->data_percurso2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_percurso2; ?>"><?= traduz_mes($data_percurso[0]) .'/'. $data_percurso[1]; ?></option>
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
            <select name="lista" id="lista" onchange="alteraClientesAtivosInativos(this.value, 'id_cliente_atualiza2');">
                <option value="1">Ativos</option>
                <option value="0" class="cor_sim">Inativos</option>
            </select>
            <br />
            
            <label for="id_cliente">Cliente:</label>
            <div id="id_cliente_atualiza2">
                <select name="id_cliente" id="id_cliente" title="Cliente" <? /*onchange="procuraPedidos();" */ ?>>
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
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>
<? } ?>