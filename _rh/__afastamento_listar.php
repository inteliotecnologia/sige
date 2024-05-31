<?
if (pode("rhv4", $_SESSION["permissao"])) {
	if ($_GET["tipo_afastamento"]!="") $tipo_afastamento= $_GET["tipo_afastamento"];
	if ($_POST["tipo_afastamento"]!="") $tipo_afastamento= $_POST["tipo_afastamento"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($_POST["data1"]!="") $data1= $_POST["data1"];
	if ($_GET["data1"]!="") $data1= $_GET["data1"];
	
	if ($_POST["data2"]!="") $data2= $_POST["data2"];
	if ($_GET["data2"]!="") $data2= $_GET["data2"];
	
	if ($data1!="")
		$str .= "and   rh_afastamentos.data_emissao >= '". formata_data_hifen($data1) ."'
				and   rh_afastamentos.data_emissao <= '". formata_data_hifen($data2) ."'
				";
	
	if ($id_funcionario!="")
		$str .= "and   rh_afastamentos.id_funcionario = '". $id_funcionario ."'
				";
	
	if (($tipo_afastamento=='s') || ($tipo_afastamento=='d'))
		$str .= "and   ( rh_afastamentos.tipo_afastamento = 's'
						or
						rh_afastamentos.tipo_afastamento = 'd'
						)
				
				";
	elseif (($tipo_afastamento=='p') || ($tipo_afastamento=='a'))
		$str .= "and   ( rh_afastamentos.tipo_afastamento = 'p'
						or
						rh_afastamentos.tipo_afastamento = 'a'
						)
				
				";
	else
		$str .= "and   rh_afastamentos.tipo_afastamento = '". $tipo_afastamento ."'
				";
	
	$result= mysql_query("select *, DATE_FORMAT(data_emissao, '%d/%m/%Y') as data_emissao2
								from  pessoas, rh_funcionarios, rh_afastamentos, rh_departamentos, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   pessoas.tipo = 'f'
								and   rh_funcionarios.id_funcionario = rh_afastamentos.id_funcionario
								and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
								and   rh_carreiras.atual = '1'
								and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
								and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
								". $str ."
								order by rh_afastamentos.data_emissao desc
								") or die(mysql_error());
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select *, DATE_FORMAT(data_emissao, '%d/%m/%Y') as data_emissao2
							from  pessoas, rh_funcionarios, rh_afastamentos, rh_departamentos, rh_carreiras
							where pessoas.id_pessoa = rh_funcionarios.id_pessoa
							and   pessoas.tipo = 'f'
							and   rh_funcionarios.id_funcionario = rh_afastamentos.id_funcionario
							and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
							and   rh_carreiras.atual = '1'
							and   rh_carreiras.id_departamento = rh_departamentos.id_departamento
							and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
							". $str ."
							order by rh_afastamentos.data_emissao desc
							limit $inicio, $num
							") or die(mysql_error());
	
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Afastamentos <? if ($tipo_afastamento!="") echo " - ". pega_tipo_afastamento($tipo_afastamento); ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=rh/afastamento&amp;tipo_afastamento=<?= $tipo_afastamento; ?>&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%">
	<tr>
	  <th width="3%">Cód.</th>
	  <th width="25%" align="left">Nome</th>
	  <th width="13%" align="left">Tipo</th>
      <th width="9%" align="left">Data de emiss&atilde;o</th>
	  <th width="8%">Qtde dias</th>
	  <th width="15%">Per&iacute;odo</th>
      <? if ($tipo_afastamento=='f') { ?>
      <th width="17%">Aquisitivo</th>
      <? } ?>
	  <th width="10%">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_afastamento; ?></td>
		<td>
        <a href="./?pagina=rh/funcionario_esquema&amp;acao=e&amp;id_funcionario=<?= $rs->id_funcionario; ?>"><?= $rs->nome_rz; ?></a>
        <? if ($rs->modo_afastamento=='2') echo "<strong>AC</strong> "; ?>
        </td>
        <td>
		<?
		if ($rs->tipo_afastamento=='o')
			echo pega_motivo($rs->id_motivo);
		else
			echo pega_tipo_afastamento($rs->tipo_afastamento);
		?>
        </td>
        <td><?= $rs->data_emissao2; ?></td>
		<td align="center">
		<?
        if ($rs->tipo_afastamento!='d') echo $rs->qtde_dias;
		else echo "-";
		?>
        </td>
	  <td align="center" class="menor">
      <?
	  	$data_inicial= pega_data_inicial_afastamento($rs->tipo_afastamento, $rs->id_afastamento);
		$data= explode('/', $data_inicial);
		
	  	if ($rs->tipo_afastamento!='d') {
			if ($rs->tipo_afastamento=='b') echo $data_inicial ." [abandono]";
			else echo $data_inicial ." à ". date("d/m/Y", mktime(0, 0, 0, $data[1], $data[0]+($rs->qtde_dias-1), $data[2]));
		}
		else echo "-";
	  ?>
      </td>
      <? if ($tipo_afastamento=='f') { ?>
      <td align="center" class="menor">
      <?= desformata_data($rs->data_inicial_aquisitivo) ." à ". desformata_data($rs->data_final_aquisitivo); ?>
      </td>
      <? } ?>
		<td align="center">
			<a href="javascript:void(0);" onclick="ajaxLink('conteudo', 'carregaPagina&amp;pagina=rh/afastamento&amp;acao=e&amp;id_afastamento=<?= $rs->id_afastamento; ?>');">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
            |
			<a href="javascript:ajaxLink('conteudo', 'afastamentoExcluir&amp;id_afastamento=<?= $rs->id_afastamento; ?>&amp;tipo_afastamento=<?= $tipo_afastamento; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" /></a>

        	<? if ($rs->tipo_afastamento=='d') { ?>
            |
                <a onmouseover="Tip('Advertência');" href="index2.php?pagina=rh/documento&amp;tipo=4&amp;id_afastamento=<?= $rs->id_afastamento; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
                    
                    |
                <a onmouseover="Tip('Justificativa de falta');" href="index2.php?pagina=rh/documento&amp;tipo=19&amp;id_afastamento=<?= $rs->id_afastamento; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            <? } ?>
            
            <? if ($rs->tipo_afastamento=='s') { ?>
            |
                <a onmouseover="Tip('Advertência');" href="index2.php?pagina=rh/documento&amp;tipo=4&amp;id_afastamento=<?= $rs->id_afastamento; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            |
                <a onmouseover="Tip('Aviso de suspensão');" href="index2.php?pagina=rh/documento&amp;tipo=5&amp;id_afastamento=<?= $rs->id_afastamento; ?>" target="_blank">
                    <img border="0" src="images/ico_pdf.png" alt="Relatório" /></a>
            <? } ?>
        </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	echo "<br /><strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=rh/afastamento_listar&amp;id_funcionario=". $id_funcionario ."&amp;tipo_afastamento=". $tipo_afastamento ."&amp;data1=". $data1 ."&amp;data2=". $data2 ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } ?>