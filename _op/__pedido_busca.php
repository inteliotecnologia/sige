<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
	if ($_GET["tipo"]=="1") $tit= "Pedido de lavanderia";
	else $tit= "Relatório de entrega";
?>

<h2><?=$tit;?></h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="index2.php?pagina=op/pedido_relatorio" target="_blank" method="post" name="formPedido" id="formPedido" onsubmit="return validaFormNormal('validacoes');">

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="data@vazio|id_cliente@vazio|entrega@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                
                <label for="data">Data:</label>
                <input name="data" id="data" class="tamanho25p espaco_dir" <? /*onblur="procuraPedidos();"*/ ?> onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
                <br />
                
                <label for="id_cliente">Cliente:</label>
                <select name="id_cliente" id="id_cliente" title="Cliente" <? /*onchange="procuraPedidos();" */ ?>>
                    <option value="">- SELECIONE -</option>
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
                
                <div id="pedidos">
                </div>
                
                <? //if ($_GET["tipo"]==2) { ?>
                <label for="entrega">Entrega:</label>
                <select name="entrega" id="entrega" title="Entrega">
                    <option value="">-</option>
                    <option value="1">1</option>
					<option value="2" class="cor_sim">2</option>
                    <? /*<option value="3">3</option>
                    <option value="4" class="cor_sim">4</option>*/ ?>
                </select>
                <br />
                <? //} ?>
                
                <? /*
                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo" title="tipo">
                    <option value="1">Roupa limpa</option>
					<option value="2" class="cor_sim">Roupa suja</option>
                </select>
                <br />
				*/ ?>
            </div>
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<? } ?>