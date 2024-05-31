<?
if (pode("r", $_SESSION["permissao"])) {
	
	$result= mysql_query("select *
								from  rh_vt_linhas
								where rh_vt_linhas.id_empresa = '". $_SESSION["id_empresa"] ."'
								order by rh_vt_linhas.linha asc
								") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Linhas de ônibus</h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/vt_linha&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="10%">Cód.</th>
	  <th width="45%" align="left">Linha</th>
	  <th width="25%">Valor</th>
	  <th width="20%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_linha; ?></td>
		<td><?= $rs->linha; ?></td>
		<td align="center">R$ <?= fnum($rs->valor); ?></td>
        <td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/vt_linha&amp;acao=e&amp;id_linha=<?= $rs->id_linha; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'VTLinhaExcluir&amp;id_linha=<?= $rs->id_linha; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<? } ?>