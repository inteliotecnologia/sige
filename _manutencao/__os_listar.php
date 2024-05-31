<?
if (pode("jk", $_SESSION["permissao"])) {
	
	if ($_POST["tipo_os"]!="") $tipo_os= $_POST["tipo_os"];
	if ($_GET["tipo_os"]!="") $tipo_os= $_GET["tipo_os"];
	if ($tipo_os!="") $str .= " and   tipo_os = '". $tipo_os ."' ";
	
	if ($_POST["id_os"]!="") $id_os= $_POST["id_os"];
	if ($_GET["id_os"]!="") $id_os= $_GET["id_os"];
	if ($id_os!="") $str .= " and   man_oss.num_os = '". $id_os ."' ";
	
	if ($tipo_os=="e") {
		if ($_POST["id_equipamento"]!="") $id_equipamento= $_POST["id_equipamento"];
		if ($_GET["id_equipamento"]!="") $id_equipamento= $_GET["id_equipamento"];
		
		if ($tipo_os=="p") $id_equipamento="";
		if ($id_equipamento!="") $str .= " and   id_equipamento = '". $id_equipamento ."' ";
	}
	
	if ($_POST["id_usuario"]!="") $id_usuario_post= $_POST["id_usuario"];
	if ($_GET["id_usuario"]!="") $id_usuario_post= $_GET["id_usuario"];
	if ($id_usuario_post!="") $str .= " and   man_oss.id_usuario = '". $id_usuario_post ."' ";
	
	if ($_POST["data"]!="") $id_equipamento= $_POST["data"];
	if ($_GET["data"]!="") $id_equipamento= $_GET["data"];
	if ($data!="") $str .= " and   man_oss_andamento.data_os_andamento = '". formata_data($data) ."' ";
	
	if ($_POST["id_situacao"]!="") $id_situacao= $_POST["id_situacao"];
	if ($_GET["id_situacao"]!="") $id_situacao= $_GET["id_situacao"];
	
	if ($_POST["buscando"]!="") $buscando= $_POST["buscando"];
	if ($_GET["buscando"]!="") $buscando= $_GET["buscando"];
	
	$result= mysql_query("select * from man_oss, man_oss_andamento
							where man_oss.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_oss.id_os = man_oss_andamento.id_os
							and   man_oss_andamento.id_situacao = '1'
							and   man_oss.status_os = '1'
							$str
							order by man_oss_andamento.data_os_andamento desc, man_oss_andamento.hora_os_andamento desc
							") or die(mysql_error());
	
	if ($buscando=="1") $num= 9999;
	else $num= 25;
	
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

	$result= mysql_query("select * from  man_oss, man_oss_andamento
							where man_oss.id_empresa = '". $_SESSION["id_empresa"] ."'
							$str
							and   man_oss.status_os = '1'
							and   man_oss.id_os = man_oss_andamento.id_os
							and   man_oss_andamento.id_situacao = '1'
							order by man_oss_andamento.data_os_andamento desc, man_oss_andamento.hora_os_andamento desc
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
    <li><a href="./?pagina=manutencao/os_listar&amp;tipo_os=e&amp;id_equipamento=<?= $rs_equi->id_equipamento; ?>"><?= $rs_equi->equipamento; ?></a></li>
    <? $i++; } ?>
</ul>
<? } else { ?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Ordens de serviço <? if ($id_equipamento!="") echo " - ". pega_equipamento($id_equipamento); ?></h2>

<ul class="recuo1">
    <li class="flutuar_esquerda tamanho180"><a href="./?pagina=manutencao/os&amp;acao=i">inserir nova OS</a></li>
    <li class="tamanho120 flutuar_esquerda"><a href="./?pagina=manutencao/os_listar&id_situacao=1&buscando=1">listar abertas</a></li>
    
    <li class="tamanho120 flutuar_esquerda"><a href="./?pagina=manutencao/os_listar&id_situacao=2&buscando=1">listar fechadas</a></li>
    
    <li><a href="./?pagina=manutencao/os_busca">buscar</a></li>
</ul>
<br />

<? if ($buscando!="1") { ?>
<p><?=$total;?> registros.</p>
<? } ?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="7%">Núm.</th>
	  <th width="18%" align="left">Data/hora</th>
      <th width="22%" align="left">Detalhes</th>
      <th width="16%" align="left">T&eacute;cnico</th>
      <th width="21%" align="left">Situa&ccedil;&atilde;o atual</th>
      <th width="13%" class="unsortable">Ações</th>
  </tr>
	<?
	$mostradas=0;
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$result_andamento_atual= mysql_query("select * from man_oss_andamento
												where id_os= '". $rs->id_os ."'
												order by id_os_andamento
												desc limit 1");
		$rs_andamento_atual= mysql_fetch_object($result_andamento_atual);
		
		$situacao_atual= $rs_andamento_atual->id_situacao;
		
		if (($id_situacao=="") || (($id_situacao==1) && ($situacao_atual!=5)) || (($id_situacao==2) && ($situacao_atual==5)) ) {
			
			$j= $i+1;
			
			$mostradas++;
			
			/*$result_at= mysql_query("update man_oss
										set num_os= '$j'
										where id_os= '". $rs->id_os ."'
										");*/
			
			if (($i%2)==0) $classe= "odd";
			else $classe= "even";
			
			if ( ($rs->id_usuario!=$_SESSION["id_usuario"]) && ($_SESSION["tipo_usuario"]!="a") ) {
				//$classe2= "maozinha";
				$link=1;
			}
			else $link= 0;
			
			$result_lida= mysql_query("select * from man_oss_lidas
										where id_os = '". $rs->id_os ."'
										and   id_usuario = '". $_SESSION["id_usuario"] ."'
										");
			if (mysql_num_rows($result_lida)==0) $novo="<span class=\"vermelho menor\"><strong>NOVO</strong></span>";
			else $novo= "";
	?>
	<tr id="linha_<?=$i;?>" class="<?= $classe; ?> corzinha <?=$classe2;?>" <? /*if ($link) echo "onclick=\"window.top.location.href='./?pagina=manutencao/rm&amp;acao=e&amp;id_os=". $rs->id_os ."';\"";*/ ?>>
		<td align="center"><?= $rs->num_os; ?></td>
        <td><?= desformata_data($rs->data_os) ." ". $rs->hora_os; ?> <? if (pode("j", $_SESSION["permissao"])) echo $novo; ?></td>
        <?
		$detalhes="";
		
		if ($rs->local_os==2) $detalhes.= "<strong>Cliente:</strong> ". pega_pessoa($rs->id_cliente) ."<br />";
		//else $detalhes.= "<strong>Equipamento:</strong> ". pega_equipamento($rs->id_equipamento) ."<br />";
		
		//if ($rs->tipo_os=="p") $detalhes.= "<strong>Item:</strong> ". $rs->item ."<br />";
		//else 
		
		$detalhes.= "<strong>Setor:</strong> ". pega_departamento($rs->id_departamento) ."<br />";
		$detalhes.= "<strong>Tipo de serviço:</strong> ". pega_tipo_servico($rs->id_servico_tipo) ."<br />";
		
		$detalhes.= "<strong>Área:</strong> ". $rs->area ."<br />";
		$detalhes.= "<strong>Descrição do serviço:</strong> ". nl2br($rs->descricao) ."<br />";
		?>
        <td><a class="contexto" href="javascript:void(0);">Ver detalhes<span><?= $detalhes; ?></span></a></td>
        
        <td><?= pega_manutencao_tecnico($rs->id_tecnico); ?></td>
        <td>
        	<?
			
			
			if ($situacao_atual==5) {
				if ((pega_nota_situacao_atual_os($rs->id_os)==0) || (pega_nota_situacao_atual_os($rs->id_os)=="")) echo "Aguardando avaliação";
				else echo pega_situacao_os($situacao_atual);
			}
			else echo pega_situacao_os($situacao_atual);
			
			switch ($situacao_atual) {
				case 1:
				case 2:
				case 3:
				case 6:
					//$data_abertura_rm= pega_data_abertura_rm($rs->id_rm);
					
					$data_mk_abertura= faz_mk_data($rs->data_os_andamento);
					$data_mk_atual= faz_mk_data(date("Y-m-d"));
					$diferenca= $data_mk_atual-$data_mk_abertura;
					$dias= round(($diferenca/60/60/24));
					
					//if ($dias>$rs->prioridade_dias) echo " <span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
				break;
			}
			
			
			
			if ($rs_andamento_atual->nota!="") {
				$descricao_nota= pega_descricao_contento($rs_andamento_atual->nota);
				$descricao_nota= explode("@", $descricao_nota);
							
				echo " |  <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>";
			}
		?>
        </td>
        <td align="center">
            <? //if ( (pode("j", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"])) { ?>
            <!--
            <a href="index2.php?pagina=manutencao/rm_relatorio&amp;id_os=<?= $rs->id_os; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
            -->
			<a href="./?pagina=manutencao/os&amp;acao=e&amp;id_os=<?= $rs->id_os; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			
            |
			<a href="javascript:ajaxLink('linha_<?=$i;?>', 'osExcluir&amp;id_os=<?= $rs->id_os; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            <? //} ?>
        </td>
	</tr>
	<? $i++; } } ?>
</table>
<br /><br />

<? if ($buscando=="1") { ?>
<p><?=$mostradas;?> registros.</p>
<? } ?>

<?
if ($num_paginas > 1) {
	echo "<strong>Páginas:</strong>"; 
	for ($i=0; $i<$num_paginas; $i++) {
		$link = $i + 1;
		if ($num_pagina==$i)
			echo " <b>". $link ."</b>";
		else
			echo " <a href=\"./?pagina=manutencao/os_listar&amp;tipo_os=". $tipo_os ."&amp;id_situacao=". $id_situacao ."&amp;id_equipamento=". $id_equipamento ."&amp;id_usuario=". $id_usuario_post ."&amp;data=". $data ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } } ?>