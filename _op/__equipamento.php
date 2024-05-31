<?
require_once("conexao.php");
if (pode_algum("pkj", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_equipamentos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_equipamento = '". $_GET["id_equipamento"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<div id="conteudo_interno">
    
    <? if ($esquema=="") { $div_alvo= "conteudo"; ?>
    <h2>Equipamentos</h2>
    <? } else { $div_alvo= "conteudo_interno"; ?>
    <ul class="recuo1">
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_geral&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">lavação/transporte</a></li>
        <li class="flutuar_esquerda tamanho200"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_servicos_pecas&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">passadoria/costura/enxoval</a></li>
        <li class="flutuar_esquerda tamanho100"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=1&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">hampers</a></li>
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=financeiro/cliente_item_cedido&amp;tipo_item_cedido=2&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">formulário de coleta</a></li>
        <li class="flutuar_esquerda tamanho80"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento_listar&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">equipamentos</a></li>
    </ul>
    <br /><br />
    
    <ul class="recuo1">
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento&amp;acao=i&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">inserir equipamento</a></li>
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento_listar&amp;esquema=1&id_cliente=<?=$id_cliente;?>&id_cliente_equipamento=<?=$id_cliente;?>');">listar deste cliente</a></li>
        <li class="flutuar_esquerda tamanho160"><a href="javascript:void(0);" onclick="ajaxLink('conteudo_interno', 'carregaPagina&amp;pagina=op/equipamento_listar&amp;esquema=1&id_cliente=<?=$id_cliente;?>');">listar todos</a></li>
    </ul>
    <br /><br />
    <? } ?>
    
    <form action="<?= AJAX_FORM; ?>formEquipamento&amp;acao=<?= $acao; ?>" method="post" name="formEquipamento" id="formEquipamento" onsubmit="return ajaxForm('<?=$div_alvo;?>', 'formEquipamento', 'validacoes');">
        
        <input class="escondido" type="hidden" id="validacoes" value="codigo@vazio|tipo_equipamento@vazio|equipamento@vazio" />
        <? if ($acao=='e') { ?>
        <input name="id_equipamento" class="escondido" type="hidden" id="id_equipamento" value="<?= $rs->id_equipamento; ?>" />
        <? } ?>
        
        <input name="esquema" class="escondido" type="hidden" value="<?= $esquema; ?>" />
        <input name="id_cliente" class="escondido" type="hidden" value="<?= $id_cliente; ?>" />
        
        <fieldset>
            <legend>Dados do equipamento</legend>
            
            <div class="parte50">
                <label for="codigo">* Cód.:</label>
                <input title="Código" name="codigo" class="tamanho15p" value="<?= $rs->codigo; ?>" id="codigo" />
                <br />
                
                <label for="codigo_patrimonial">* Cód. patrimonial:</label>
                <input title="Código" name="codigo_patrimonial" class="tamanho15p" value="<?= $rs->codigo_patrimonial; ?>" id="codigo" />
                <br />
                
                <label for="id_cliente_equipamento">Cliente:</label>
                <select name="id_cliente_equipamento" id="id_cliente_equipamento" title="Cliente">
                    <option value="">- NENHUM -</option>
                    <?
                    $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'c'
												and   pessoas.id_cliente_tipo = '1'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $k=0;
                    while ($rs_ced = mysql_fetch_object($result_ced)) {
                    ?>
                    <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if (($rs_ced->id_cedente==$rs->id_cliente) || ($rs_ced->id_cedente==$id_cliente)) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
                    <? $k++; } ?>
                </select>
                <br />
                
                <label for="tipo_equipamento">* Tipo:</label>
                <select id="tipo_equipamento" name="tipo_equipamento" title="Tipo de equipamento">
                    <option value="">---</option>
                    <?
                    $result_eq= mysql_query("select * from op_equipamentos_tipos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by equipamento_tipo asc
                                            ") or die(mysql_error());
                    
                    while ($rs_eq= mysql_fetch_object($result_eq)) {
                    ?>
                    <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_eq->id_equipamento_tipo; ?>" <? if ($rs_eq->id_equipamento_tipo==$rs->tipo_equipamento) echo "selected=\"selected\""; ?>><?= $rs_eq->equipamento_tipo; ?></option>
                    <? $j++; } ?>
                </select>
                <br />
                
                <label for="equipamento">* Equipamento:</label>
                <input title="Equipamento" name="equipamento" value="<?= $rs->equipamento; ?>" id="equipamento" />
                <br />
            </div>
            <br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </fieldset>
    </form>
</div>
<? } ?>