<?
if (pode_algum("p", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from op_acompanhamento_itens
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							and   status_item <> '0'
							order by id_acompanhamento_item asc
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Checklist</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/acompanhamento_item&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="13%">ID</th>
		<th width="67%" align="left">Item</th>
		<th width="20%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_acompanhamento_item; ?></td>
		<td><?= $rs->acompanhamento_item; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=op/acompanhamento_item&amp;acao=e&amp;id_acompanhamento_item=<?= $rs->id_acompanhamento_item; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'acompanhamentoItemExcluir&amp;id_acompanhamento_item=<?= $rs->id_acompanhamento_item; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>