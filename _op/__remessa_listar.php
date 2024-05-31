<?
if (pode_algum("ps", $_SESSION["permissao"])) {

	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str= " and   DATE_FORMAT(data_remessa, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	if ($_GET["data"]!="") $data= $_GET["data"];
	if ($data!="") $str.= " and   data_remessa= '". formata_data($data) ."' ";
	
	if ($_POST["id_veiculo"]!="") $id_veiculo= $_POST["id_veiculo"];
	if ($_GET["id_veiculo"]!="") $id_veiculo= $_GET["id_veiculo"];
	if ($id_veiculo!="") $str.= " and   id_veiculo= '". $id_veiculo ."' ";
	
	$result= mysql_query("select * from op_suja_remessas
							where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_remessa desc, num_remessa desc
							");
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select * from op_suja_remessas
							where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_remessa desc, num_remessa desc
							limit $inicio, $num
							");
	
	$periodo2= explode("/", $periodo);
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área suja - Remessas</h2>

<ul class="recuo1">
	<li><a href="./?pagina=op/remessa&amp;acao=i">inserir</a></li>
</ul>
<br />

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/remessa_listar&amp;periodo=". $periodo ."&amp;data=". $data ."&amp;id_veiculo=". $id_veiculo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/remessa_listar&amp;periodo=<?=$periodo;?>&amp;data=<?=$data;?>&amp;id_veiculo=<?=$id_veiculo;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<table cellspacing="0" width="100%">
	<tr>
		<th width="3%">Cód.</th>
	  <th width="9%" align="left">Remessa</th>
	  <th width="9%">Chegada</th>
		<th width="37%" align="left">Ve&iacute;culo</th>
		<th width="13%" align="left">Percurso</th>
		<th width="10%">N&ordm; pesagens</th>
		<th width="9%">N&ordm; lavagens</th>
	  <th width="10%">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_remessa; ?></td>
		<td><?= desformata_data($rs->data_remessa) ." <strong>nº ". pega_dado_remessa("num_remessa", $rs->id_remessa) ."</strong>"; ?></td>
		<td align="center"><?= $rs->hora_chegada; ?></td>
		<td><?= pega_veiculo($rs->id_veiculo); ?></td>
		<td><?= pega_percurso($rs->id_percurso); ?></td>
		<td align="center">
        	<?
			$result_pesagens= mysql_query("select count(id_pesagem) as total_pesagens from op_suja_pesagem
											where id_remessa = '". $rs->id_remessa ."'
											");
			$rs_pesagens= mysql_fetch_object($result_pesagens);
			
			echo $rs_pesagens->total_pesagens;
			?>
        </td>
		<td align="center">
			<?
			$result_lavagens= mysql_query("select count(id_lavagem) as total_lavagens from op_suja_lavagem
											where id_remessa = '". $rs->id_remessa ."'
											");
			$rs_lavagens= mysql_fetch_object($result_lavagens);
			
			echo $rs_lavagens->total_lavagens;
			?>
        </td>
		<td align="center">
			<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
            <button class="tamanho50 espaco_dir" onclick="window.top.location.href='./?pagina=op/remessa&amp;acao=e&amp;id_remessa=<?= $rs->id_remessa; ?>&amp;origem=r';" id="link_edita<?=$i;?>">edita</button>
            <button class="tamanho50" onclick="var confirma= confirm('Tem certeza que deseja excluir?'); if (confirma) ajaxLink('conteudo', 'remessaExcluir&amp;id_remessa=<?= $rs->id_remessa; ?>&amp;tipo_remessa=<?= $rs->tipo_remessa; ?>');" id="link_exclui<?=$i;?>">exclui</button>
            <? } else { ?>
            <a href="./?pagina=op/remessa&amp;acao=e&amp;id_remessa=<?= $rs->id_remessa; ?>&amp;origem=r">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
	        <? //if ($_SESSION["tipo_usuario"]=='a') { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'remessaExcluir&amp;id_remessa=<?= $rs->id_remessa; ?>&amp;tipo_remessa=<?= $rs->tipo_remessa; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>
            <? //} ?>
            <? } ?>
            </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	/*echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=op/remessa_listar&amp;periodo=". $periodo ."&amp;data=". $data ."&amp;id_veiculo=". $id_veiculo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
	*/
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/remessa_listar&amp;periodo=<?=$periodo;?>&amp;data=<?=$data;?>&amp;id_veiculo=<?=$id_veiculo;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_edita0");
</script>

<? } ?>