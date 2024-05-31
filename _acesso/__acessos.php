<?
if ($_SESSION["tipo_usuario"]=="a") {
	$sql= "select *, DATE_FORMAT(data_acesso, '%d/%m/%Y %H:%i:%s') as data_acesso
							from acessos order by id_acesso desc";
	
	$result= mysql_query($sql) or die(mysql_error());
	
	$total_antes= mysql_num_rows($result);
	
	if (!isset($tudo)) {
		$num= 50;
		$total_linhas = mysql_num_rows($result);
		$num_paginas = ceil($total_linhas/$num);
		if (!isset($num_pagina))
			$num_pagina = 0;
		$inicio = $num_pagina*$num;
		
		$result= mysql_query($sql ." limit $inicio, $num") or die(mysql_error());
	}
?>
<h2>Acessos</h2>

<p>Foram encontrados <strong><?= $total_antes; ?></strong> registro(s), mostrando <strong><?= $num; ?></strong> registros, de <strong><?= $inicio; ?></strong> até <strong><?= ($inicio+$num); ?></strong>. </p>

<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<tr>
		<th width="5%">Cód.</th>
		<th align="left" width="20%">Usuário</th>
		<th width="34%">IP</th>
		<th width="12%">Data/Hora</th>
		<th width="8%" class="unsortable">Ação</th>
	</tr>
	<?
	$i= 0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ($rs->tipo=="e") {
			$tipo= "entrada";
			$cor= "azul";
		}
		else {
			$tipo= "saída";
			$cor= "vermelho";
		}
		
	?>
	<tr class="corzinha <?= $cor; ?> <?= $classe; ?>">
		<td align="center"><?= $rs->id_acesso; ?></td>
		<td><?= pega_nome_pelo_id_usuario($rs->id_usuario); ?></a></td>
		<td align="center">
		<?
        if ($rs->ip!="") {
			echo $rs->ip;
			if ($rs->ip!=$rs->ip_reverso)
				echo " (". $rs->ip_reverso .")";
		}
		else
			echo "anônimo";
		?>
        </td>
		<td align="center"><?= $rs->data_acesso ?></td>
		<td align="center"><?= $tipo; ?></td>
	</tr>
	<? $i++; } ?>
</table>
<br />
<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		echo "<br /><strong>Páginas:</strong> "; 
		
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i) $texto_paginacao .= "<strong>". $link ."</strong> ";
			else $texto_paginacao .=  "<a href=\"./?pagina=acesso/acessos&num_pagina=". $i ."\">". $link ."</a> ";
		}

		echo $texto_paginacao;
	}
}
?>
<br /><br /><br /><br /><br />
<? } ?>