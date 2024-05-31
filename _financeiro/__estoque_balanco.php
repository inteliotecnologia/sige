<?
if (pode("iq|", $_SESSION["permissao"])) {
	if ($_POST["periodo"]!="") {
		$periodo2= explode("/", $_POST["periodo"]);
		$titulo= traduz_mes($periodo2[0]) ."/". $periodo2[1];
	}
	else {
		switch($_POST["periodicidade"]) {
			case 1: $titulo= "1º trimestre de ";
					break;
			case 2: $titulo= "2º trimestre de ";
					break;
			case 3: $titulo= "3º trimestre de ";
					break;
			case 4: $titulo= "4º trimestre de ";
					break;
			case "a": $titulo= "anual de ";
						break;
		}
		
		$titulo .= $_POST["ano"];
	}
	
	$sql= "select fi_itens.* from fi_estoque_mov, fi_itens
							where fi_estoque_mov.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   fi_estoque_mov.id_item = fi_itens.id_item
							". $astr_periodo ."
							group by fi_estoque_mov.id_item
							order by fi_itens.item asc
							";
		
	$result= mysql_query($sql) or die(mysql_error());

	/*$num= 200;
	$total_linhas = mysql_num_rows($result);
	$num_paginas = ceil($total_linhas/$num);
	if (!isset($num_pagina))
		$num_pagina = 0;
	$comeco = $num_pagina*$num;
	
	$result= mysql_query($sql ." limit $comeco, $num") or die(mysql_error());
*/

?>

<h2 class="titulos">Balan&ccedil;o completo do estoque (<?= $titulo; ?>)</h2>

	<table cellspacing="0">
    	<tr>
        	<th width="40%" align="left">Produto</th>
            <th width="15%">Estoque inicial</th>
            <th width="15%">Entradas</th>
            <th width="15%">Sa&iacute;das</th>
            <th width="15%">Estoque final</th>
    	</tr>
		<?
		while($rs= mysql_fetch_object($result)) {
			$estoque_inicial= pega_estoque_inicial($_POST["periodo"], $_POST["ano"], $_POST["periodicidade"], $_SESSION["id_empresa"], $rs->id_item);
			$entradas= pega_entradas($_POST["periodo"], $_POST["ano"], $_POST["periodicidade"], $_SESSION["id_empresa"], $rs->id_item);
			$saidas= pega_saidas($_POST["periodo"], $_POST["ano"], $_POST["periodicidade"], $_SESSION["id_empresa"], $rs->id_item);
			
			//if ($estoque_inicial=="") $estoque_inicial=0;
			//if ($entradas=="") $entradas=0;
			//if ($saidas=="") $saidas=0;
			
			
		?>
        <tr class="corzinha">
        	<td><?= $rs->item; ?></td>
            <td align="center"><?= fnum2($estoque_inicial) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
            <td align="center"><?= fnum2($entradas) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
            <td align="center"><?= fnum2($saidas) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
            <td align="center"><?= fnum2(($estoque_inicial+$entradas)-$saidas) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
        </tr>
        <? } ?>
    </table>
    
    <?
	/*if ($total_linhas>0) {
		if ($num_paginas > 1) {
			$texto_url= "carregaPagina&amp;pagina=_farmacia/balanco_farmacia&amp;ano=". $ano ."&amp;periodicidade=". $periodicidade ."&amp;num_pagina=";
			
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
	*/
	?>

<? } ?>