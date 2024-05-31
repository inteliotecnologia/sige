<?
if (pode("ey", $_SESSION["permissao"])) {
	
	if ($_POST["id_veiculo"]!="") $str .= " and   id_veiculo = '". $_POST["id_veiculo"] ."' ";
	if ($_POST["periodo"]!="") $str .= " and   DATE_FORMAT(data_vistoria, '%m/%Y') = '". $_POST["periodo"] ."' ";
	
	$result= mysql_query("select *
							from  tr_vistorias
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							$str
							order by data_vistoria desc
							") or die(mysql_error());
	
	$num= 30;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select *
							from  tr_vistorias
							where id_empresa = '". $_SESSION["id_empresa"] ."'
							$str
							order by data_vistoria desc
							limit $inicio, $num
							") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Vistorias</h2>

<ul class="recuo1">
	<li><a href="./?pagina=transporte/vistoria&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="11%">Cód.</th>
	  <th width="38%" align="left">Veículo</th>
      <th width="29%">Data/hora</th>
	  <th width="22%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_vistoria; ?></td>
        <td><?= pega_veiculo($rs->id_veiculo); ?></td>
        <td align="center"><?= desformata_data($rs->data_vistoria) ." ". $rs->hora_vistoria; ?></td>
        <td align="center">
            <a href="index2.php?pagina=transporte/vistoria_relatorio&amp;id_vistoria=<?= $rs->id_vistoria; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
			<a href="./?pagina=transporte/vistoria&amp;acao=e&amp;id_vistoria=<?= $rs->id_vistoria; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'vistoriaExcluir&amp;id_vistoria=<?= $rs->id_vistoria; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            </td>
	</tr>
	<? $i++; } ?>
</table>

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=transporte/vistoria_listar&amp;id_veiculo=". $id_veiculo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>