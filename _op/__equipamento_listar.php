<?
if (pode_algum("pkj", $_SESSION["permissao"])) {
	if ($_GET["tipo_equipamento"]!="") $tipo_equipamento= $_GET["tipo_equipamento"];
	//if ($_POST["tipo_equipamento"]!="") $tipo_equipamento= $_GET["tipo_equipamento"];

	if ($_GET["id_cliente_equipamento"]!="") $id_cliente_equipamento= $_GET["id_cliente_equipamento"];
	if ($_POST["id_cliente_equipamento"]!="") $id_cliente_equipamento= $_POST["id_cliente_equipamento"];
	if ($id_cliente_equipamento!="") $str.= " and   id_cliente_equipamento = '". $id_cliente_equipamento ."' ";
	
	if ($_GET["id_cliente"]!="") $id_cliente= $_GET["id_cliente"];
	if ($_POST["id_cliente"]!="") $id_cliente= $_POST["id_cliente"];
	if ($id_cliente!="") $str.= " and   id_cliente_equipamento = '". $id_cliente ."' ";
	
	$result= mysql_query("select * from op_equipamentos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by tipo_equipamento asc, equipamento asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<div id="conteudo_interno">
	<? if ($esquema==1) { $div_alvo= "conteudo_interno"; ?>
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
    
    <? } else { $div_alvo= "conteudo";  ?>
    <h2>Equipamentos</h2>
    
    <ul class="recuo1">
        <li><a href="./?pagina=op/equipamento&amp;acao=i">inserir</a></li>
    </ul>
    <? } ?>
    
    <table cellspacing="0" width="100%">
        <tr>
            <th width="7%">ID</th>
            <th width="5%" align="left">C&oacute;d.</th>
            <th width="15%" align="left">C&oacute;d. patrimonial</th>
            <th width="16%" align="left">Tipo</th>
            <th width="32%" align="left">Equipamento</th>
            <th width="12%" align="left">Cliente</th>
            <th width="13%">Ações</th>
        </tr>
        <?
        $i=0;
        while ($rs= mysql_fetch_object($result)) {
            if (($i%2)==0) $classe= "cor_sim";
            else $classe= "cor_nao";
        ?>
        <tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
            <td align="center"><?= $rs->id_equipamento; ?></td>
            <td><?= $rs->codigo; ?></td>
            <td><?= $rs->codigo_patrimonial; ?></td>
            <td><?= pega_tipo_equipamento2($rs->tipo_equipamento); ?></td>
            <td><?= $rs->equipamento; ?></td>
            <td><? if (($rs->id_cliente!="") && ($rs->id_cliente!="0")) echo pega_sigla_pessoa($rs->id_cliente); else echo "-"; ?></td>
            <td align="center">
                <a href="javascript:void(0);" onclick="ajaxLink('<?=$div_alvo;?>', 'carregaPagina&amp;pagina=op/equipamento&amp;acao=e&amp;id_equipamento=<?= $rs->id_equipamento; ?>&amp;esquema=<?= $esquema; ?>&amp;id_cliente=<?= $id_cliente; ?>');">
                    <img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                
                <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
                |
                <a href="javascript:ajaxLink('linha_<?=$i;?>', 'equipamentoExcluir&amp;id_equipamento=<?= $rs->id_equipamento; ?>&amp;tipo_equipamento=<?= $rs->tipo_equipamento; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
                    <img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
                <? //} ?>
            </td>
        </tr>
        <? $i++; } ?>
    </table>
</div>
<? } ?>