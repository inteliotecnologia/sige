<?
if (pode_algum("pue", $_SESSION["permissao"])) {
	
	$result= mysql_query("select * from tr_vistorias_itens
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							and   status_item <> '2'
							order by ordem asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Veículos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=transporte/vistoria_item&&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%">Cód.</th>
		<th width="19%" align="left">Item</th>
		<th width="22%" align="left">Ordem</th>
		<th width="17%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_item; ?></td>
		<td><?= $rs->item; ?></td>
		<td><?= $rs->ordem; ?></td>
		<td align="center">
			<a href="./?pagina=transporte/vistoria_item&amp;acao=e&amp;id_item=<?= $rs->id_item; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'vistoriaItemExcluir&amp;id_item=<?= $rs->id_item; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>