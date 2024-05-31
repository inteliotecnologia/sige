<?
require_once("conexao.php");
if (pode_algum("pl", $_SESSION["permissao"])) {
	if ($_GET["tipo"]=="1") $tit= "Pedido de lavanderia";
	else $tit= "Relatório de entrega";
?>

<div id="div_nada" style="display:none;">
</div>

<h2><?=$tit;?></h2>

<ul class="recuo1">
	<li class="flutuar_esquerda tamanho200"><a href="./?pagina=op/entrega_busca">gerar nota de entrega</a></li>
    <li class="flutuar_esquerda"><a href="./?pagina=op/entrega_busca2">buscar nota gerada</a></li>
</ul>
<br /><br />

<div class="parte50">
    <fieldset>
        <legend>Formulário de busca <span class="vermelho">(pela data de coleta)</span></legend>
        
        <form action="index2.php?pagina=op/entrega_relatorio5" target="_blank" method="post" name="formEntrega" id="formEntrega" onsubmit="return validaFormNormal('validacoes1');">

            <input class="escondido" type="hidden" id="validacoes1" value="data@vazio|id_cliente@vazio|entrega@vazio" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            <input class="escondido" type="hidden" id="data_tipo" name="data_tipo" value="c" />
            
            <!--
            <label for="opcao_relatorio">Opção:</label>
            <select name="opcao_relatorio" id="opcao_relatorio">
                <option value="1" selected="selected">Gerar relatório</option>
                <option value="2">Identificar pesagens</option>
            </select>
            <br />
            -->
            
            <label for="data">Data:</label>
            <input name="data" id="data1" class="tamanho25p espaco_dir" onblur="procuraPercursos('1');" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
            <br />
            
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente1" title="Cliente" onchange="procuraPercursos('1'); checaDeschecaPeso(this.value, '1'); ">
                <option value="">- SELECIONE -</option>
                <?
                $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'c'
                                            and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
											and   pessoas.basear_nota_data= '1'
                                            order by pessoas.apelido_fantasia asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_cli = mysql_fetch_object($result_cli)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <div id="entregas_1">
            	<label for="entrega">Entrega:</label>
            	---
            	<br /><br />
            </div>
            
            <div id="denominacao_1" class="escondido">
	            <label for="denominacao">Denominação:</label>
	            <select name="denominacao" id="denominacao">
	                <option value="0">-</option>
	                <option value="1">Entrega extra</option>
	                <option value="2" class="cor_sim">Entrega complementar</option>
	            </select>
	            <br />
            </div>
            
            <? /*if ($_SESSION["tipo_usuario"]=="a") { ?>
            <label for="mostrar">Mostrar:</label>
            <input type="checkbox"  class="tamanho15" name="mostrar" id="mostrar" value="1" />
            <br />
            <? }*/ ?>
            
            <!--<label for="extra">Tipo de entrega:</label>
            <select name="extra" id="extra" onchange="processaEntrega(this.value);">
                <option value="0">Entrega normal</option>
                <option value="1" class="cor_sim">Entrega extra/complementar</option>
            </select>
            <br />
            
            <div id="div_denominacao" style="display:none;">
                <label for="denominacao">Denominação:</label>
                <select name="denominacao" id="denominacao">
                    <option value="0">-</option>
                    <option value="1">Entrega extra</option>
                    <option value="2" class="cor_sim">Entrega complementar</option>
                </select>
                <br />
            </div>-->
            
            <label for="mostrar_peso">Mostrar peso:</label>
            <input type="checkbox"  class="tamanho15" name="mostrar_peso" id="mostrar_peso1" value="1" />
            <br /><br />
            
            <label for="obs">OBS:</label>
            <input name="obs" id="obs" />
            <br />
            
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
<div class="parte50">
    <fieldset>
        <legend>Formulário de busca <span class="vermelho">(pela data de entrega)</span></legend>
        
        <form action="index2.php?pagina=op/entrega_relatorio5" target="_blank" method="post" name="formEntrega" id="formEntrega" onsubmit="return validaFormNormal('validacoes2');">

            <input class="escondido" type="hidden" id="validacoes2" value="data2@vazio|id_cliente2@vazio|entrega2@vazio" />
            <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
            <input class="escondido" type="hidden" id="data_tipo2" name="data_tipo" value="e" />
            
            <!--
            <label for="opcao_relatorio2">Opção:</label>
            <select name="opcao_relatorio" id="opcao_relatorio2">
                <option value="1" selected="selected">Gerar relatório</option>
                <option value="2">Identificar pesagens</option>
            </select>
            <br />
            -->
            
            <label for="data2">Data:</label>
            <input name="data" id="data2" class="tamanho25p espaco_dir" onblur="procuraPercursos('2');" onkeyup="formataData(this);" value="<?=date("d/m/Y");?>" title="Data 1" />
            <br />
            
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente2" title="Cliente" onchange="procuraPercursos('2');checaDeschecaPeso(this.value, '2');">
                <option value="">- SELECIONE -</option>
                <?
                $result_cli= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
                                            where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                            and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                            and   pessoas_tipos.tipo_pessoa = 'c'
                                            and   pessoas.status_pessoa = '1'
											and   pessoas.id_cliente_tipo = '1'
											and   pessoas.basear_nota_data= '2'
                                            order by pessoas.apelido_fantasia asc
                                            ") or die(mysql_error());
                $i=0;
                while ($rs_cli = mysql_fetch_object($result_cli)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cli->id_cliente; ?>"><?= $rs_cli->apelido_fantasia; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <div id="entregas_2">
            	<label for="entrega">Entrega:</label>
            	---
            	<br /><br />
            </div>
            
            <div id="denominacao_2" class="escondido">
	            <label for="denominacao">Denominação:</label>
	            <select name="denominacao" id="denominacao">
	                <option value="0">-</option>
	                <option value="1">Entrega extra</option>
	                <option value="2" class="cor_sim">Entrega complementar</option>
	            </select>
	            <br />
            </div>
            
            <label for="mostrar_peso2">Mostrar peso:</label>
            <input type="checkbox"  class="tamanho15" name="mostrar_peso" id="mostrar_peso2" value="1" />
            <br /><br />
            
            <label for="obs2">OBS:</label>
            <input name="obs" id="obs2" />
            <br />
            
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