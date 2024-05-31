<?
if (pode("jk", $_SESSION["permissao"])) {
	
	if ($_POST["id_equipamento"]!="") $id_equipamento= $_POST["id_equipamento"];
	if ($_GET["id_equipamento"]!="") $id_equipamento= $_GET["id_equipamento"];
	if ($id_equipamento!="") $str .= " and   tipo_rm = 'e' and   id_equipamento = '". $id_equipamento ."' ";
	
	$result= mysql_query("select *
							from  man_rms, man_rms_andamento
							where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_rms.id_rm = man_rms_andamento.id_rm
							and   man_rms_andamento.id_situacao = '1'
							$str
							order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
							") or die(mysql_error());
	
	$num= 50;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select *
							from  man_rms, man_rms_andamento
							where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_rms.id_rm = man_rms_andamento.id_rm
							and   man_rms_andamento.id_situacao = '1'
							$str
							order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
							limit $inicio, $num
							") or die(mysql_error());
?>

<? if ($_GET["seleciona"]==1) { ?>

<h2>Selecione o equipamento:</h2>

<ul class="recuo1">
    <?
    $result_equi= mysql_query("select * from op_equipamentos
                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                order by equipamento asc
                                 ");
    $i=0;
    while ($rs_equi = mysql_fetch_object($result_equi)) {
    ?>
    <li><a href="./?pagina=manutencao/rm_listar_equipamento&amp;id_equipamento=<?= $rs_equi->id_equipamento; ?>"><?= $rs_equi->equipamento; ?></a></li>
    <? $i++; } ?>
</ul>
<? } else { ?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Requisições de manutenção - <?= pega_equipamento($id_equipamento); ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=manutencao/rm&amp;acao=i&amp;id_equipamento=<?=$id_equipamento;?>">inserir nova RM para este equipamento</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="7%">Núm.</th>
	  <th width="18%" align="left">Data/hora</th>
      <th width="12%" align="left">Tipo</th>
      <th width="16%">Solicitante</th>
      <th width="12%">Prioridade</th>
      <th width="22%">Situa&ccedil;&atilde;o atual</th>
	  <th width="13%" class="unsortable">Ações</th>
  </tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		$j= $i+1;
		
		/*$result_at= mysql_query("update man_rms
									set num_rm= '$j'
									where id_rm= '". $rs->id_rm ."'
									");*/
		
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		if ( ($rs->id_usuario!=$_SESSION["id_usuario"]) && ($_SESSION["tipo_usuario"]!="a") ) {
			//$classe2= "maozinha";
			$link=1;
		}
		else $link= 0;
		
		$result_lida= mysql_query("select * from man_rms_lidas
								  	where id_rm = '". $rs->id_rm ."'
									and   id_usuario = '". $_SESSION["id_usuario"] ."'
									");
		if (mysql_num_rows($result_lida)==0) $novo="<span class=\"vermelho menor\"><strong>NOVO</strong></span>";
		else $novo= "";
	?>
	<tr class="<?= $classe; ?> corzinha <?=$classe2;?>" <? /*if ($link) echo "onclick=\"window.top.location.href='./?pagina=manutencao/rm&amp;acao=e&amp;id_rm=". $rs->id_rm ."';\"";*/ ?>>
		<td align="center"><?= $rs->num_rm; ?></td>
        <td><?= desformata_data($rs->data_rm_andamento) ." ". $rs->hora_rm_andamento; ?> <? if (pode("j", $_SESSION["permissao"])) echo $novo; ?></td>
        <?
		$detalhes= "<strong>Finalidade:</strong> ". pega_finalidade_rm($rs->finalidade_rm) ."<br />";
		
		if ($rs->tipo_rm=="p") $detalhes.= "<strong>Item:</strong> ". $rs->item ."<br />";
		else $detalhes.= "<strong>Equipamento:</strong> ". pega_equipamento($rs->id_equipamento) ."<br />";
		
		$detalhes.= "<strong>Área:</strong> ". $rs->area ."<br />";
		$detalhes.= "<strong>Problema:</strong> ". nl2br($rs->problema) ."<br />";
		?>
        <td><a class="contexto" href="javascript:void(0);"><?= pega_tipo_rm($rs->tipo_rm); ?><span><?= $detalhes; ?></span></a></td>
        <td align="center"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs->id_usuario)); ?></td>
        <td align="center"><?= pega_prioridade_rm($rs->prioridade_dias); ?></td>
        <td align="center">
		<?
			$result_andamento_atual= mysql_query("select * from man_rms_andamento
													where id_rm= '". $rs->id_rm ."'
													order by id_rm_andamento
													desc limit 1");
			$rs_andamento_atual= mysql_fetch_object($result_andamento_atual);
			
			$situacao_atual= $rs_andamento_atual->id_situacao;
			
			if ($situacao_atual==5) {
				if ((pega_nota_situacao_atual_rm($rs->id_rm)==0) || (pega_nota_situacao_atual_rm($rs->id_rm)=="")) echo "Aguardando avaliação";
				else echo pega_situacao_rm($situacao_atual);
			}
			else echo pega_situacao_rm($situacao_atual);
			
			switch ($situacao_atual) {
				case 1:
				case 2:
				case 3:
				case 6:
					//$data_abertura_rm= pega_data_abertura_rm($rs->id_rm);
					
					$data_mk_abertura= faz_mk_data($rs->data_rm_andamento);
					$data_mk_atual= faz_mk_data(date("Y-m-d"));
					$diferenca= $data_mk_atual-$data_mk_abertura;
					$dias= round(($diferenca/60/60/24));
					
					if ($dias>$rs->prioridade_dias) echo " <span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
				break;
			}
			
			
			
			if ($rs_andamento_atual->nota!="") {
				$descricao_nota= pega_descricao_nota($rs_andamento_atual->nota);
				$descricao_nota= explode("@", $descricao_nota);
							
				echo " | Nota ". $rs_andamento_atual->nota ." <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>";
			}
		?>
        </td>
        <td align="center">
            <? if ( (pode("j", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"])) { ?>
            <!--
            <a href="index2.php?pagina=manutencao/rm_relatorio&amp;id_rm=<?= $rs->id_rm; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
            -->
			<a href="./?pagina=manutencao/rm&amp;acao=e&amp;id_rm=<?= $rs->id_rm; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			<? if ($_SESSION["tipo_usuario"]=="a") { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'rmExcluir&amp;id_rm=<?= $rs->id_rm; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            <? } } else echo "-"; ?>
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
			echo " <a href=\"./?pagina=manutencao/rm_listar&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } } ?>