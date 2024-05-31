<?
if ($_POST["id_empresa"]!="") $id_empresa= $_POST["id_empresa"];
else $id_empresa= $_GET["id_empresa"];

if ($_SESSION["tipo_empresa"]=="a") {
	$result= mysql_query("select * from equipamentos
							where id_empresa = '". $id_empresa ."'
							order by id_eq asc
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Equipamentos cadastrados de <?= pega_empresa($id_empresa); ?></h2>

<h3 class="screen"><a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_empresas/eq_inserir&amp;id_empresa=<?= $id_empresa; ?>');">adicionar equipamento</a></h3>

<p>Na lista abaixo estão os equipamentos cadastradas no sistema para esta empresa:</p>

<table cellspacing="0">
	<tr>
		<th width="10%">Cód.</th>
		<th width="40%" align="left">Identificação</th>
		<th width="15%">Modelo</th>
		<th width="20%">Nº de série</th>
		<th width="15%">Ações</th>
	</tr>
	<?
	$i= 0;
	while ($rs= mysql_fetch_object($result)) {
		
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_eq==1) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_eq; ?></td>
		<td><?= pega_ident_equipamento($rs->ident); ?></td>
		<td align="center"><?= pega_modelo_equipamento($rs->modelo); ?></td>
		<td align="center"><?= $rs->n_serie; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=_empresas/eq_editar&amp;id_eq=<?= $rs->id_eq; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
		</td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>