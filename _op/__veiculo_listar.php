<?
if (pode_algum("pue", $_SESSION["permissao"])) {
	if ($_GET["tipo_veiculo"]!="") $tipo_veiculo= $_GET["tipo_veiculo"];
	
	$result= mysql_query("select * from op_veiculos
							where id_empresa = '". $_SESSION["id_empresa"] ."' 
							");
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Veículos</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/veiculo&amp;tipo_veiculo=<?= $tipo_veiculo; ?>&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
		<th width="8%">Cód.</th>
		<th width="19%" align="left">Identifica&ccedil;&atilde;o</th>
		<th width="22%" align="left">Ve&iacute;culo</th>
		<th width="16%">Placa</th>
		<th width="18%">Tipo padr&atilde;o</th>
		<th width="17%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		if ($rs->status_veiculo) $status= 0;
		else $status= 1;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_veiculo; ?></td>
		<td><?= $rs->codigo; ?></td>
		<td><?= $rs->veiculo; ?></td>
		<td align="center"><?= $rs->placa; ?></td>
		<td align="center"><?= pega_coleta_entrega($rs->tipo_padrao); ?></td>
		<td align="center">
			<a href="./?pagina=op/veiculo&amp;acao=e&amp;id_veiculo=<?= $rs->id_veiculo; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			|
            <a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'veiculoStatus&amp;id_veiculo=<?= $rs->id_veiculo; ?>&amp;status=<?= $status; ?>');">
				<img border="0" src="images/ico_<?= $status; ?>.png" alt="Status" /></a>
            
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'veiculoExcluir&amp;id_veiculo=<?= $rs->id_veiculo; ?>&amp;tipo_veiculo=<?= $rs->tipo_veiculo; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>