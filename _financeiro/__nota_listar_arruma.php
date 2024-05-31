<?
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["status_nota"]!="") $status_nota= $_GET["status_nota"];
	if ($_POST["status_nota"]!="") $status_nota= $_POST["status_nota"];
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	if ($_GET["notas_itens"]!="") $notas_itens= $_GET["notas_itens"];
	if ($_POST["notas_itens"]!="") $notas_itens= $_POST["notas_itens"];
	
	if ($_GET["tudo"]!="") $tudo= $_GET["tudo"];
	if ($_POST["tudo"]!="") $tudo= $_POST["tudo"];
	
	if ( ($_POST["data1"]!="") || ($_POST["data2"]!="") ) {
		$data1= $_POST["data1"];
		$data2= $_POST["data2"];
	}
	else {
		if ( ($_GET["data1"]!="") || ($_GET["data2"]!="") ) {
			$data1= $_GET["data1"];
			$data2= $_GET["data2"];
		}
	}
	
	if ( ($data1!="") || ($data2!="") ) {
		if ($data1!="") {
			$data1f= $data1;
			$data1= formata_data_hifen($data1);
		}
		if ($data2!="") {
			$data2f= $data2;
			$data2= formata_data_hifen($data2);
		}
		
		if ($data1!="") $data1_mk= faz_mk_data($data1);
		if ($data2!="") $data2_mk= faz_mk_data($data2);
	}
	elseif ($_POST["periodo"]!="") {
		$periodo= explode('/', $_POST["periodo"]);
		
		$data1_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
		$data2_mk= mktime(0, 0, 0, $periodo[0], 31, $periodo[1]);
		
		$data1= date("Y-m-d", $data1_mk);
		$data2= date("Y-m-d", $data2_mk);
		
		$data1f= desformata_data($data1);
		$data2f= desformata_data($data2);
	}
	if ($data1!="") $str .= " and   fi_notas.data_vencimento >= '$data1' ";
	if ($data2!="") $str .= " and   fi_notas.data_vencimento <= '$data2' ";
	
	
	
	if ( ($_POST["data1_emissao"]!="") || ($_POST["data2_emissao"]!="") ) {
		$data1_emissao= $_POST["data1_emissao"];
		$data2_emissao= $_POST["data2_emissao"];
	}
	else {
		if ( ($_GET["data1_emissao"]!="") || ($_GET["data2_emissao"]!="") ) {
			$data1_emissao= $_GET["data1_emissao"];
			$data2_emissao= $_GET["data2_emissao"];
		}
	}
	
	if ( ($data1_emissao!="") || ($data2_emissao!="") ) {
		if ($data1_emissao!="") {
			$data1f_emissao= $data1_emissao;
			$data1_emissao= formata_data_hifen($data1_emissao);
		}
		if ($data2_emissao!="") {
			$data2f_emissao= $data2_emissao;
			$data2_emissao= formata_data_hifen($data2_emissao);
		}
		
		if ($data1_emissao!="") $data1_mk_emissao= faz_mk_data($data1_emissao);
		if ($data2_emissao!="") $data2_mk_emissao= faz_mk_data($data2_emissao);
	}
	elseif ($_POST["periodo_emissao"]!="") {
		$periodo_emissao= explode('/', $_POST["periodo_emissao"]);
		
		$data1_mk_emissao= mktime(0, 0, 0, $periodo_emissao[0], 1, $periodo_emissao[1]);
		$dias_mes_emissao= date("t", $data1_mk_emissao);
		
		$data2_mk_emissao= mktime(0, 0, 0, $periodo_emissao[0], $dias_mes_emissao, $periodo_emissao[1]);
		
		$data1_emissao= date("Y-m-d", $data1_mk_emissao);
		$data2_emissao= date("Y-m-d", $data2_mk_emissao);
		
		$data1f_emissao= desformata_data($data1_emissao);
		$data2f_emissao= desformata_data($data2_emissao);
	}
	if ($data1_emissao!="") $str .= " and   fi_notas.data_emissao >= '$data1_emissao' ";
	if ($data2_emissao!="") $str .= " and   fi_notas.data_emissao <= '$data2_emissao' ";
	
	if ($notas_itens!="") $str .= "
									and   fi_notas.id_nota IN
									(
									select id_nota from fi_notas_itens
									)
									";
	
	if (isset($_POST["geral"])) {
		if ($_POST["num_nota"]!="") $str .= " and fi_notas.num_nota like '%". $_POST["num_nota"] ."%' ";
		if ($_POST["id_cedente"]!="") $str .= " and fi_notas.id_cedente = '". $_POST["id_cedente"] ."' ";
		
		if ($_POST["id_empresa_atendente"]!="") $str .= " and pessoas.id_empresa_atendente = '". $_POST["id_empresa_atendente"] ."' ";
		
		$i=0;
		while ($_POST["id_centro_custo_tipo"][$i]!="") {
			if ($i==0) {
				$str_cc_1= ", fi_pessoas_cc_tipos";
				$str_cc_2= " and   pessoas.id_pessoa = fi_pessoas_cc_tipos.id_pessoa ";
				$str_cc_p= " and ( ";
				$str_cc_u= " ) ";
			}
			
			if ($i>0) $str_or= " or ";
			else $str_or= "";
			
			$str_cc.= $str_or ." fi_pessoas_cc_tipos.id_centro_custo_tipo = '". $_POST["id_centro_custo_tipo"][$i] ."' ";
			
			$i++;	
		}
	}
	
	$sql= "select * from fi_notas
							
							order by fi_notas.data_emissao asc
							";
	
	$result= mysql_query($sql) or die(mysql_error());
	
	if ($tipo_nota=='p') {
		if ($status_nota==1) $tit2= "pagas";
		else $tit2= "à pagar";
		$tit5= "pago";
		$txt_cedente= "Fornecedor";
		$tipo_cedente= "f";
	}
	else {
		if ($status_nota==1) $tit2= "recebidas";
		else $tit2= "à receber";
		$tit5= "recebido";
		$txt_cedente= "Cliente";
		$tipo_cedente= "c";
	}
	
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Lista de duplicatas <?= $tit2; ?></h2>

<ul class="recuo1">
	<li><a href="./?pagina=financeiro/nota&amp;tipo_nota=<?=$tipo_nota;?>&amp;acao=i">inserir</a></li>
</ul>

<table cellspacing="0" width="100%" id="tabela" class="sortable">
	<tr>
		<th width="5%">Cód.</th>
		<th width="9%" align="left">Número</th>
        <th width="27%" align="left"><?= $txt_cedente; ?></th>
		<th width="10%" align="left">Emissão</th>
		<th width="8%" align="left">Parcelas</th>
        <th width="12%" align="left">Vencimento</th>
        <th width="8%">Valor</th>
        <th width="12%">Valor <?=$tit5;?></th>
		<th width="9%" class="unsortable">Ações</th>
	</tr>
	<?
	$i=0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		$valor_total_nota= pega_valor_total_nota($rs->id_nota);
		$valor_pago= pega_valor_total_pagamento_nota($rs->id_nota);
		
		$primeiro_vencimento= pega_primeiro_vencimento_nota($rs->id_nota);
		
		$num_parcelas= pega_num_parcelas_nota($rs->id_nota);
		
		$result_atualiza= mysql_query(" update fi_notas
										set valor_total = '". $valor_total_nota ."'
										where id_nota = '". $rs->id_nota ."'
										") or die(mysql_error());
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td align="center"><?= $rs->id_nota; ?></td>
		<td><a href="./?pagina=financeiro/nota_esquema&amp;id_nota=<?= $rs->id_nota; ?>&amp;acao=e"><?= $rs->num_nota; ?></a></td>
        <td><?= pega_pessoa($rs->id_cedente); ?></td>
        <td><?= desformata_data($rs->data_emissao); ?></td>
        <td><?= $num_parcelas; ?></td>
        <td><span class="escondido"><?=$primeiro_vencimento;?></span><?= desformata_data($primeiro_vencimento); ?></td>
        <td align="center">R$ <?= fnum($valor_total_nota); ?></td>
        <td align="center">R$ <?= fnum($valor_pago); ?></td>
		<td align="center">
			<a href="javascript:ajaxLink('conteudo', 'notaExcluir&amp;id_nota=<?= $rs->id_nota; ?>&amp;tipo_nota=<?=$tipo_nota;?>&amp;status_nota=<?=$status_nota;?>');" onclick="return confirm('Tem certeza que deseja excluir?');">
				<img border="0" src="images/ico_lixeira.png" alt="Status" />
            </a>
        </td>
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if (!isset($_POST["geral"])) {
	if ($num_paginas > 1) {
		/*echo "<br /><strong>Páginas:</strong>"; 
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i)
				echo " <b>". $link ."</b>";
			else
				echo " <a href=\"./?pagina=transporte/percurso_listar&amp;id_veiculo=". $id_veiculo ."&amp;tipo=". $tipo ."&amp;num_pagina=". $i. "\">". $link ."</a>";
		}
		*/
		$num_pagina_real= $num_pagina+1;
	?>
	<fieldset class="tamanho200">
		<legend>Páginas:</legend>
		
		<div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=financeiro/nota_listar&amp;tipo_nota=<?=$_GET["tipo_nota"];?>&amp;status_nota=<?=$_GET["status_nota"];?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
	</fieldset>
<? } } ?>

<? } ?>