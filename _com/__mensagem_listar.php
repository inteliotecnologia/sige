<?
if (pode("n", $_SESSION["permissao"])) {
	if ($_GET["tipo"]=="") $tipo="r";
	else $tipo= $_GET["tipo"];
	
	$id_pessoa= pega_id_pessoa_do_usuario($_SESSION["id_usuario"]);
	
	if (($tipo=="r") || ($tipo=="e")) {
		if ($tipo=="r")
			$result= mysql_query("select id_mensagem from com_mensagens
								 	where situacao_para='1'
									and para= '". $id_pessoa ."'
									order by id_mensagem desc") or die(mysql_error());
		if ($tipo=="e")
			$result= mysql_query("select id_mensagem from com_mensagens
								 	where situacao_de= '1'
									and de= '". $id_pessoa ."'
									order by id_mensagem desc") or die(mysql_error());
		
		$num= 15;
		$total = mysql_num_rows($result);
		$num_paginas = ceil($total/$num);
		if ($_GET["num_pagina"]=="") $num_pagina= 0;
		else $num_pagina= $_GET["num_pagina"];
		$inicio = $num_pagina*$num;
		
		if ($tipo=="r")
			$result= mysql_query("select *, DATE_FORMAT(data_mensagem, '%d/%m/%Y') as data_mensagem
									from com_mensagens
									where situacao_para='1'
									and para= '". $id_pessoa ."'
									order by id_mensagem desc limit $inicio, $num") or die(mysql_error());
		if ($tipo=="e")
			$result= mysql_query("select *, DATE_FORMAT(data_mensagem, '%d/%m/%Y') as data_mensagem
									from com_mensagens
									where situacao_de='1'
									and   de= '". $id_pessoa ."'
									order by id_mensagem desc limit $inicio, $num") or die(mysql_error());
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Lembretes <? if ($tipo=="r") echo "recebidos"; else echo "enviados"; ?></h2>

<? /*
<ul class="recuo1">
	<li><a href="./?pagina=com/mensagem&amp;acao=i">enviar nova mensagem</a></li>
    <li><a href="./?pagina=com/mensagem_listar&amp;tipo=r">mensagens recebidas</a></li>
    <li><a href="./?pagina=com/mensagem_listar&amp;tipo=e">mensagens enviadas</a></li>
</ul>
<br />
*/ ?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="8%">Cód.</th>
		<th width="30%" align="left"><? if ($tipo=="e") echo "Para"; else echo "De"; ?></th>
		<th width="37%" align="left">Assunto</th>
        <th width="15%" align="left">Data/hora</th>
		<th width="10%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if (($tipo=="r") && ($rs->lida==0)) $classe2= "cor_destaque";
		else $classe2= "";
	?>
	<tr class="<?= $classe ." ". $classe2; ?> corzinha">
		<td align="center"><?= $rs->id_mensagem; ?></td>
		<td><? if ($tipo=="e") echo pega_pessoa($rs->para); else echo pega_pessoa($rs->de); ?></td>
        <td><a class="linkao" href="./?pagina=com/mensagem_ver&amp;id_mensagem=<?= $rs->id_mensagem; ?>&amp;tipo=<?= $tipo; ?>"><?= $rs->titulo; ?></a></td>
		<td><?= $rs->data_mensagem ." ". $rs->hora_mensagem; ?></td>
		<td align="center">
			<a href="link.php?mensagemExcluir&amp;id_mensagem=<?= $rs->id_mensagem; ?>&amp;tipo=<?= $tipo; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	if ($num_pagina > 0) {
		$menos = $num_pagina - 1;
		echo "<a href=\"./?pagina=com/mensagem_listar&amp;tipo=". $tipo ."&amp;num_pagina=". $menos. "\">&laquo; Anterior</a>";
	}

	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=com/mensagem_listar&amp;tipo=". $tipo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}

	if ($num_pagina < ($num_paginas - 1)) {
		$mais = $num_pagina + 1;
		echo " <a href=\"./?pagina=com/mensagem_listar&amp;tipo=". $tipo ."&amp;num_pagina=". $mais ."\">Pr&oacute;xima &raquo;</a>";
	}
}
?>

<? } } ?>