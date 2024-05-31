<?
if (pode("t", $_SESSION["permissao"])) {
	
	$sql .= "select *, DATE_FORMAT(data_ligacao, '%d/%m/%Y') as data_ligacao2 from tel_contatos_ligacoes
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									order by data_ligacao desc, hora_ligacao desc
									";
	//echo $sql;
	
	$result= mysql_query($sql) or die(mysql_error());
	
	$total_antes= mysql_num_rows($result);
		
	if ($tudo!=1) {
		$num= 30;
		$total_linhas = mysql_num_rows($result);
		$num_paginas = ceil($total_linhas/$num);
		if (!isset($num_pagina))
			$num_pagina = 0;
		$comeco = $num_pagina*$num;
		
		$result= mysql_query($sql ." limit $comeco, $num") or die(mysql_error());
	}

?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Controle de ligações</h2>

<ul class="recuo1">
	<li><a href="./?pagina=contatos/ligacao&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="7%">Cód.</th>
	  <th width="24%" align="left">Para</th>
      <th width="19%" align="left">Telefone</th>
	  <th width="21%" align="left">Solicitante</th>
      <th width="17%" align="left">Data/hora</th>
	  <th width="12%">Ações</th>
  </tr>
	<?
	$j=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($j%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_ligacao; ?></td>
		<td><?= $rs->para; ?></td>
        <td><?= $rs->telefone; ?></td>
        <td><?= pega_funcionario($rs->id_funcionario); ?></td>
        <td><?= $rs->data_ligacao2 ." ". $rs->hora_ligacao; ?></td>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=contatos/ligacao&amp;acao=e&amp;id_ligacao=<?= $rs->id_ligacao; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
                |
			<a href="javascript:ajaxLink('conteudo', 'ligacaoExcluir&amp;id_ligacao=<?= $rs->id_ligacao; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
                </a>
                </td>
	</tr>
	<? $j++; } ?>
</table>

<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		$texto_url= "carregaPagina&amp;pagina=contatos/ligacao_listar&amp;num_pagina=";
		
		$texto_paginacao .= "<div id=\"paginacao\">
				<ul>";
		if ($num_pagina > 0) {
			$menos = $num_pagina - 1;
			$texto_paginacao .=  "<li><a class=\"maior\" href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $menos ."')\">&laquo; Anterior</a></li>";
		}

		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i)
				$texto_paginacao .= "<li class=\"paginacao_atual\">". $link ."</li>";
			else
				$texto_paginacao .=  "<li><a href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $i ."')\">". $link ."</a></li>";
		}
	
		if ($num_pagina < ($num_paginas - 1)) {
			$mais = $num_pagina + 1;
			$texto_paginacao .=  "<li><a class=\"maior\" href=\"javascript:void(0);\" onclick=\"ajaxLink('conteudo', '". $texto_url . $mais ."')\">Pr&oacute;xima &raquo;</a></li>";
		}
		$texto_paginacao .=  "</ul>
			</div>";

		echo $texto_paginacao;
	}
}
?>

<? } ?>