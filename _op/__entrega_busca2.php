<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
?>

<h2>Buscar nota de entrega</h2>

<ul class="recuo1">
	<li class="flutuar_esquerda tamanho200"><a href="./?pagina=op/entrega_busca">gerar nota de entrega</a></li>
    <li class="flutuar_esquerda"><a href="./?pagina=op/entrega_busca2">buscar nota gerada</a></li>
</ul>
<br /><br />

<div class="parte50">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="./?pagina=op/entrega_listar" method="post" name="formEntrega" id="formEntrega" onsubmit="return validaFormNormal('validacoes1');">

            <input class="escondido" type="hidden" id="validacoes1" value="" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            <input class="escondido" type="hidden" id="data_tipo" name="data_tipo" value="c" />
            
            <label for="num_pedido">Número da nota:</label>
            <input name="num_pedido" id="num_pedido" class="tamanho25p espaco_dir" value="" title="" />
            <br />
            
            <label for="data">Data:</label>
            <input name="data" id="data" class="tamanho25p espaco_dir" <? /*onblur="procuraPedidos();"*/ ?> onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
            <br />
            
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" title="Cliente" <? /*onchange="procuraPedidos();" */ ?>>
                <option value="">- SELECIONE -</option>
                <?
                $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'c'
                                            and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
                                            order by pessoas.apelido_fantasia asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_cli = mysql_fetch_object($result_cli)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <? /*
            
            <label for="extra">Tipo de entrega:</label>
            <select name="extra" id="extra" onchange="processaEntrega(this.value);">
                <option value="0">Entrega normal</option>
                <option value="1" class="cor_sim">Entrega extra</option>
            </select>
            <br />
            */ ?>
            
            <? /*
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo" title="tipo">
                <option value="1">Roupa limpa</option>
                <option value="2" class="cor_sim">Roupa suja</option>
            </select>
            <br />
            */ ?>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<? } ?>