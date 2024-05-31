<?
$marcador_inicial = microtime(1);

if (pode("jk", $_SESSION["permissao"])) {
	
	if ($_POST["tipo_rm"]!="") $tipo_rm= $_POST["tipo_rm"];
	if ($_GET["tipo_rm"]!="") $tipo_rm= $_GET["tipo_rm"];
	if ($tipo_rm!="") $str .= " and   tipo_rm = '". $tipo_rm ."' ";
	
	if ($_POST["id_rm"]!="") $id_rm= $_POST["id_rm"];
	if ($_GET["id_rm"]!="") $id_rm= $_GET["id_rm"];
	if ($id_rm!="") $str .= " and   man_rms.num_rm = '". $id_rm ."' ";
	
	
	if ($_POST["id_tecnico_preferencial"]!="") $id_tecnico_preferencial= $_POST["id_tecnico_preferencial"];
	if ($_GET["id_tecnico_preferencial"]!="") $id_tecnico_preferencial= $_GET["id_tecnico_preferencial"];
	if ($id_tecnico_preferencial!="") $str .= " and   id_tecnico_preferencial = '". $id_tecnico_preferencial ."' ";
	
	$id_funcionario_usuario= pega_id_funcionario_do_usuario($_SESSION[id_usuario]);
	
	if ($_POST["id_usuario"]!="") $id_usuario_post= $_POST["id_usuario"];
	if ($_GET["id_usuario"]!="") $id_usuario_post= $_GET["id_usuario"];
	if ($id_usuario_post!="") $str .= " and   man_rms.id_usuario = '". $id_usuario_post ."' ";
	
	if ($tipo_rm=="e") {
		if ($_POST["id_equipamento"]!="") $id_equipamento= $_POST["id_equipamento"];
		if ($_GET["id_equipamento"]!="") $id_equipamento= $_GET["id_equipamento"];
		if ($tipo_rm=="p") $id_equipamento="";
		if ($id_equipamento!="") $str .= " and   id_equipamento = '". $id_equipamento ."' ";
	}
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	if ($_GET["data"]!="") $data= $_GET["data"];
	if ($data!="") $str .= " and   man_rms_andamento.data_rm_andamento = '". formata_data($data) ."' ";
	
	if ($_POST["buscando"]!="") $buscando= $_POST["buscando"];
	if ($_GET["buscando"]!="") $buscando= $_GET["buscando"];
	
	if ($_POST["id_situacao"]!="") $id_situacao= $_POST["id_situacao"];
	if ($_GET["id_situacao"]!="") $id_situacao= $_GET["id_situacao"];
	
	$result= mysql_query("select * from  man_rms, man_rms_andamento
							where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_rms.id_rm = man_rms_andamento.id_rm
							and   man_rms_andamento.id_situacao = '1'
							and   man_rms.status_rm = '1'
							$str
							order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
							") or die(mysql_error());
	
	if ($buscando=="1") $num= 9999;
	else $num= 25;
	
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;

/*echo "select * from  man_rms, man_rms_andamento
							where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_rms.id_rm = man_rms_andamento.id_rm
							and   man_rms_andamento.id_situacao = '1'
							and   man_rms.status_rm = '1'
							$str
							order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
							limit $inicio, $num
							";*/

	$result= mysql_query("select * from  man_rms, man_rms_andamento
							where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   man_rms.id_rm = man_rms_andamento.id_rm
							and   man_rms_andamento.id_situacao = '1'
							and   man_rms.status_rm = '1'
							$str
							order by man_rms_andamento.data_rm_andamento desc, man_rms_andamento.hora_rm_andamento desc
							limit $inicio, $num
							") or die(mysql_error());
	$linhas= mysql_num_rows($result);
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
    <li><a href="./?pagina=manutencao/rm_listar&amp;tipo_rm=e&amp;id_equipamento=<?= $rs_equi->id_equipamento; ?>"><?= $rs_equi->equipamento; ?></a></li>
    <? $i++; } ?>
</ul>
<? } else { ?>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Requisições de manutenção <? if ($id_equipamento!="") echo " - ". pega_equipamento($id_equipamento); ?></h2>

<ul class="recuo1">
    <? if ($id_equipamento!="") { ?>
    <li class="tamanho200 flutuar_esquerda"><a href="./?pagina=manutencao/rm&amp;acao=i&amp;id_equipamento=<?=$id_equipamento;?>">inserir nova RM para este equipamento</a></li>
    <? } else { ?>
    <li class="tamanho160 flutuar_esquerda"><a href="./?pagina=manutencao/rm&amp;acao=i">inserir nova RM</a></li>
    <? } ?>
    <li class="tamanho200 flutuar_esquerda"><a href="./?pagina=manutencao/rm_listar&amp;id_tecnico_preferencial=<?=$id_funcionario_usuario;?>">listar RMs designadas a mim</a></li>
    <li class="tamanho120 flutuar_esquerda"><a href="./?pagina=manutencao/rm_listar">listar todas</a></li>
    
    <li class="tamanho120 flutuar_esquerda"><a href="./?pagina=manutencao/rm_listar&id_situacao=1&buscando=1">listar abertas</a></li>
    
    <li class="tamanho120 flutuar_esquerda"><a href="./?pagina=manutencao/rm_listar&id_situacao=2&buscando=1">listar fechadas</a></li>
    
    <li class="tamanho80 flutuar_esquerda"><a href="./?pagina=manutencao/rm_busca">buscar RM</a></li>
</ul>
<br /><br />

<? if ($linhas==0) echo "Nada encontrado."; else { ?>

<? if ($buscando!="1") { ?>
<p><?=$total;?> registros.</p>
<? } ?>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
	  <th width="7%">Núm.</th>
	  <th width="18%" align="left">Data/hora</th>
      <th width="10%" align="left">&nbsp;</th>
      <th width="12%">Solicitante</th>
      <th width="12%">Prioridade</th>
      <th width="21%">Situa&ccedil;&atilde;o atual</th>
	  <th width="10%" class="unsortable">Ações</th>
  </tr>
	<?
	$i=0;
	$mostradas=0;
	while ($rs= mysql_fetch_object($result)) {
		
		$result_andamento_atual= mysql_query("select * from man_rms_andamento
												where id_rm= '". $rs->id_rm ."'
												order by id_rm_andamento
												desc limit 1");
		$rs_andamento_atual= mysql_fetch_object($result_andamento_atual);
		
		$situacao_atual= $rs_andamento_atual->id_situacao;
		
		if (($id_situacao=="") || (($id_situacao=="1") && ($situacao_atual!="5")) || (($id_situacao=="2") && ($situacao_atual=="5")) ) {
		
			$j= $i+1;
			
			$mostradas++;
			
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
		
		//if ($rs->tipo_rm=="p") $detalhes.= "<strong>Item:</strong> ". $rs->item ."<br />";
		//else $detalhes.= "<strong>Equipamento:</strong> ". pega_equipamento($rs->id_equipamento) ."<br />";
		
		$detalhes.= "<strong>Tipo de serviço:</strong> ". pega_tipo_servico($rs->id_servico_tipo) ."<br />";
		$detalhes.= "<strong>Setor:</strong> ". pega_departamento($rs->id_departamento) ."<br />";
		$detalhes.= "<strong>Problema:</strong> ". nl2br($rs->problema) ."<br />";
		?>
        <td><a class="contexto" href="javascript:void(0);">Ver detalhes<span><?= $detalhes; ?></span></a></td>
        <td align="center"><?= primeira_palavra(pega_nome_pelo_id_usuario($rs->id_usuario)); ?></td>
        <? /*
        <td align="center">
        <?
        if ( ($rs->id_tecnico_preferencial!="") && ($rs->id_tecnico_preferencial!="0") )
	        echo primeira_palavra(pega_funcionario($rs->id_tecnico_preferencial));
	    else echo "-";
        ?>
        </td>
        */ ?>
        <td align="center"><?= pega_prioridade_rm($rs->prioridade_dias); ?></td>
        <td align="center">
		<?
			
			
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
					
					//if ($dias>$rs->prioridade_dias) echo " <span class=\"vermelho menor\"><strong>ATRASADO</strong></span>";
				break;
			}
			
			
			
			if ($rs_andamento_atual->nota!="") {
				$descricao_nota= pega_descricao_contento($rs_andamento_atual->nota);
				$descricao_nota= explode("@", $descricao_nota);
							
				echo " | <span class=\"menor ". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>";
			}
		?>
        </td>
        <td align="center">
            <? if ( (pode("kj", $_SESSION["permissao"])) || ($rs->id_usuario==$_SESSION["id_usuario"])) { ?>
            <!--
            <a href="index2.php?pagina=manutencao/rm_relatorio&amp;id_rm=<?= $rs->id_rm; ?>" target="_blank">
                <img border="0" src="images/ico_pdf.png" alt="Relatório" />
            </a>
            |
            -->
			<a href="./?pagina=manutencao/rm&amp;acao=e&amp;id_rm=<?= $rs->id_rm; ?>">
				<img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
			<? //if ($_SESSION["tipo_usuario"]=="a") { ?>
            |
			<a href="javascript:ajaxLink('conteudo', 'rmExcluir&amp;id_rm=<?= $rs->id_rm; ?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Excluir" /></a>
            <? } else echo "-"; ?>
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
			echo " <a href=\"./?pagina=manutencao/rm_listar&amp;tipo_rm=". $tipo_rm ."&amp;id_situacao=". $id_situacao ."&amp;id_equipamento=". $id_equipamento ."&amp;id_usuario=". $id_usuario_post ."&amp;data=". $data ."&amp;num_pagina=". $i. "\">". $link ."</a>";
	}
}
?>

<? } } } ?>