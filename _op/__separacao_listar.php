<?
if (pode_algum("ps", $_SESSION["permissao"])) {

	if ($_POST["periodo"]!="") $periodo= $_POST["periodo"];
	if ($_GET["periodo"]!="") $periodo= $_GET["periodo"];
	if ($periodo!="") $str= " and   DATE_FORMAT(data_remessa, '%m/%Y') = '". $periodo ."' ";
	
	if ($_POST["data"]!="") $data= $_POST["data"];
	if ($_GET["data"]!="") $data= $_GET["data"];
	if ($data!="") $str.= " and   data_remessa= '". formata_data_hifen($data) ."' ";
	
	if ($_POST["id_veiculo"]!="") $id_veiculo= $_POST["id_veiculo"];
	if ($_GET["id_veiculo"]!="") $id_veiculo= $_GET["id_veiculo"];
	if ($id_veiculo!="") $str.= " and   id_veiculo= '". $id_veiculo ."' ";
	
	$result= mysql_query("select * from op_suja_remessas
							where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_remessa desc, num_remessa desc
							");
	$num= 15;
	$total = mysql_num_rows($result);
	$num_paginas = ceil($total/$num);
	if ($_GET["num_pagina"]=="") $num_pagina= 0;
	else $num_pagina= $_GET["num_pagina"];
	$inicio = $num_pagina*$num;
	
	$result= mysql_query("select *, 			DATE_FORMAT(data_inicio_separacao, '%d') as dia_inicio_separacao2,
												DATE_FORMAT(data_inicio_separacao, '%m') as mes_inicio_separacao2,
												DATE_FORMAT(data_inicio_separacao, '%Y') as ano_inicio_separacao2,
												
												DATE_FORMAT(hora_inicio_separacao, '%H') as hora_inicio_separacao2,
												DATE_FORMAT(hora_inicio_separacao, '%i') as minuto_inicio_separacao2,
												DATE_FORMAT(hora_inicio_separacao, '%s') as segundo_inicio_separacao2,
												
												DATE_FORMAT(data_fim_separacao, '%d') as dia_fim_separacao2,
												DATE_FORMAT(data_fim_separacao, '%m') as mes_fim_separacao2,
												DATE_FORMAT(data_fim_separacao, '%Y') as ano_fim_separacao2,
												
												DATE_FORMAT(hora_fim_separacao, '%H') as hora_fim_separacao2,
												DATE_FORMAT(hora_fim_separacao, '%i') as minuto_fim_separacao2,
												DATE_FORMAT(hora_fim_separacao, '%s') as segundo_fim_separacao2
												
												from op_suja_remessas
							where op_suja_remessas.id_empresa = '". $_SESSION["id_empresa"] ."' 
							". $str ."
							order by data_remessa desc, num_remessa desc
							limit $inicio, $num
							");
	
	$periodo2= explode("/", $periodo);
?>

<div id="tela_aux" class="telinha1 screen">
</div>

<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2>Área suja - Remessas (Separação)</h2>

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
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/separacao_listar&amp;data=<?=$data;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<table cellspacing="0" width="100%">
	<tr>
		<th width="10%">Cód.</th>
	    <th width="12%" align="left">Remessa</th>
	    <th width="12%">Chegada</th>
		<th width="12%">Primeira pesagem</th>
  	    <th width="12%">Peso</th>
		<th colspan="5">Separações</th>
	</tr>
    <tr>
    	<th>&nbsp;</th>
	    <th align="left">&nbsp;</th>
	    <th>&nbsp;</th>
		<th>&nbsp;</th>
        <th>&nbsp;</th>
        <th width="4%" align="left">&nbsp;</th>
		<th width="12%" align="left">In&iacute;cio</th>
		<th width="12%" align="left">Fim</th>
		<th width="17%" align="left">Dura&ccedil;&atilde;o</th>
        <th width="12%" align="left">&nbsp;</th>
    </tr>
	<?
	$i=0;
	$primeiro= -1;
	
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "cor_sim";
		else $classe= "cor_nao";
		
		$data1_producao_mk= mktime($rs->hora_inicio_separacao2, $rs->minuto_inicio_separacao2, $rs->segundo_inicio_separacao2, $rs->mes_inicio_separacao2, $rs->dia_inicio_separacao2, $rs->ano_inicio_separacao2);
		$data2_producao_mk= mktime($rs->hora_fim_separacao2, $rs->minuto_fim_separacao2, $rs->segundo_fim_separacao2, $rs->mes_fim_separacao2, $rs->dia_fim_separacao2, $rs->ano_fim_separacao2);
		
		$diferenca= $data2_producao_mk-$data1_producao_mk;
		
		if (($diferenca<=0) || ($diferenca>36000)) $erro=1;
		else $erro=0;
	?>
	<tr class="<?= $classe; ?> corzinha">
		<td valign="top" align="center"><?= $rs->id_remessa; ?></td>
		<td valign="top"><?= desformata_data($rs->data_remessa) ." <strong>nº ". pega_dado_remessa("num_remessa", $rs->id_remessa) ."</strong>"; ?></td>
		<td valign="top" align="center"><?= substr(desformata_data($rs->data_remessa), 0, 5) ." ". $rs->hora_chegada; ?></td>
		<td valign="top" align="center">
        <?
		$result_pesagem_primeira= mysql_query("select * from op_suja_pesagem
												where id_remessa = '". $rs->id_remessa ."'
												order by data_pesagem asc, hora_pesagem asc
												limit 1
												");
		$rs_pesagem_primeira= mysql_fetch_object($result_pesagem_primeira);
		
		echo substr(desformata_data($rs_pesagem_primeira->data_pesagem), 0, 5) ." ". $rs_pesagem_primeira->hora_pesagem;
		?>
        </td>
        <td valign="top" align="center">
        	<?
            $result_soma= mysql_query("select sum(peso) as soma from op_suja_pesagem
										where op_suja_pesagem.id_empresa = '". $_SESSION["id_empresa"] ."' 
										/* and   ( op_suja_pesagem.data_pesagem = '". $data ."' or op_suja_pesagem.data_pesagem = '". $amanha ."' ) */
										and   op_suja_pesagem.id_remessa = '". $rs->id_remessa ."'
										") or die(mysql_error());
			$rs_soma= mysql_fetch_object($result_soma);
			
			echo fnum($rs_soma->soma) ." kg";
			?>
        </td>
        
		<td valign="top" colspan="5">
			<?
            
            /*if (($rs->hora_inicio_separacao!="") && ($rs->hora_inicio_separacao!="00:00:00")) echo substr(desformata_data($rs->data_inicio_separacao), 0, 5) ." ". $rs->hora_inicio_separacao;
            else {
                if ($primeiro==-1) $primeiro= $i;
                echo "<button id=\"link_inicia". $i ."\" onclick=\"var confirma= confirm('Tem certeza que deseja iniciar a separação?'); if (confirma) ajaxLink('conteudo', 'iniciaSeparacaoRemessa&id_remessa=". $rs->id_remessa ."');\">Iniciar separação</button>";
            }
            ?>
            
            
            <?
            if (($rs->hora_fim_separacao!="") && ($rs->hora_fim_separacao!="00:00:00")) echo substr(desformata_data($rs->data_fim_separacao), 0, 5) ." ". $rs->hora_fim_separacao;
            else {
                if ($primeiro==-1) $primeiro= $i;
                echo "<button id=\"link_finaliza". $i ."\" onclick=\"var confirma= confirm('Tem certeza que deseja finalizar a separação?'); if (confirma) ajaxLink('conteudo', 'finalizaSeparacaoRemessa&id_remessa=". $rs->id_remessa ."');\">Em andamento</button>";
            }*/
            ?>
				
			<?
			$tempo_separacao=0;
			
            $result_separacoes= mysql_query("select *, DATE_FORMAT(data_separacao, '%d') as dia_separacao2,
                                                DATE_FORMAT(data_separacao, '%m') as mes_separacao2,
                                                DATE_FORMAT(data_separacao, '%Y') as ano_separacao2,
                                                DATE_FORMAT(hora_separacao, '%H') as hora_separacao2,
                                                DATE_FORMAT(hora_separacao, '%i') as minuto_separacao2,
                                                DATE_FORMAT(hora_separacao, '%s') as segundo_separacao2
                                                
                                                from op_suja_remessas_separacoes
                                                where id_remessa = '". $rs->id_remessa ."'
                                                and   tipo_separacao = '1'
                                                order by data_separacao asc, hora_separacao asc
                                                ") or die(mysql_error());
            
            $linhas_separacoes= mysql_num_rows($result_separacoes);
			
			if ($linhas_separacoes>0) {
            ?>
            
            <table width="100%" cellpadding="0" cellspacing="0">
            <?
                $e=1;
                while ($rs_separacoes= mysql_fetch_object($result_separacoes)) {
                    
                    $result_separacao_encerra= mysql_query("select *,
                                                            DATE_FORMAT(data_separacao, '%d') as dia_separacao2,
                                                            DATE_FORMAT(data_separacao, '%m') as mes_separacao2,
                                                            DATE_FORMAT(data_separacao, '%Y') as ano_separacao2,
                                                            DATE_FORMAT(hora_separacao, '%H') as hora_separacao2,
                                                            DATE_FORMAT(hora_separacao, '%i') as minuto_separacao2,
                                                            DATE_FORMAT(hora_separacao, '%s') as segundo_separacao2
                                                            
                                                            from op_suja_remessas_separacoes
                                                            where id_remessa = '". $rs->id_remessa ."'
                                                            and   tipo_separacao = '0'
                                                            /*and   data_separacao >= '". $rs_separacoes->data_separacao ."'
                                                            and   hora_separacao >= '". $rs_separacoes->hora_separacao ."' */
															and   id_separacao_fecha = '". $rs_separacoes->id_separacao ."'
                                                            order by data_separacao asc, hora_separacao asc limit 1
                                                            ") or die(mysql_error());
                    $linhas_separacao_encerra= mysql_num_rows($result_separacao_encerra);
                    
					$rs_separacao_encerra= mysql_fetch_object($result_separacao_encerra);
					
					$data1_producao_mk= mktime($rs_separacoes->hora_separacao2, $rs_separacoes->minuto_separacao2, $rs_separacoes->segundo_separacao2, $rs_separacoes->mes_separacao2, $rs_separacoes->dia_separacao2, $rs_separacoes->ano_separacao2);
					$data2_producao_mk= mktime($rs_separacao_encerra->hora_separacao2, $rs_separacao_encerra->minuto_separacao2, $rs_separacao_encerra->segundo_separacao2, $rs_separacao_encerra->mes_separacao2, $rs_separacao_encerra->dia_separacao2, $rs_separacao_encerra->ano_separacao2);
					
					$diferenca= $data2_producao_mk-$data1_producao_mk;
					
					if (($diferenca<=0) || ($diferenca>36000)) $erro=1;
					else $erro=0;		
					?>
					<tr>
						<td width="8%">
                        	<strong><?=$e;?></strong>
                        </td>
                        <td width="26%">
							<?=substr(desformata_data($rs_separacoes->data_separacao), 0, 5) ." ". substr($rs_separacoes->hora_separacao, 0, 5);?>
							<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
							<button class="tamanho50 espaco_dir" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/separacao_horario&amp;id_separacao=<?= $rs_separacoes->id_separacao; ?>&amp;id_remessa=<?= $rs_separacoes->id_remessa; ?>&amp;tipo_separacao=1');" id="link_edita_separacao<?=$rs_separacoes->id_separacao;?>">edita</button>
							<? } else { ?>
							<a href="javascript:void(0);" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/separacao_horario&amp;id_separacao=<?= $rs_separacoes->id_separacao; ?>&amp;id_remessa=<?= $rs_separacoes->id_remessa; ?>&amp;tipo_separacao=1');" id="link_edita_separacao<?=$rs_separacoes->id_separacao;?>"><img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
							<? } ?>
							
						</td>
						<td width="26%">
							<? if ($linhas_separacao_encerra==0) { ?>
							<button id="link_finaliza_separacao_<?= $rs_separacao->id_separacao;?>" onclick="var confirma= confirm('Tem certeza que deseja finalizar a separação?'); if (confirma) ajaxLink('conteudo', 'finalizaSeparacaoRemessa&id_separacao=<?= $rs_separacoes->id_separacao; ?>&id_remessa=<?= $rs_separacoes->id_remessa; ?>');">Finaliza/pausa</button>
							<? } else { ?>
								<?=substr(desformata_data($rs_separacao_encerra->data_separacao), 0, 5) ." ". substr($rs_separacao_encerra->hora_separacao, 0, 5);?>
								<? if ($_SESSION["id_departamento_sessao"]!="") { ?>
								<button class="tamanho50 espaco_dir" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/separacao_horario&amp;id_separacao=<?= $rs_separacao_encerra->id_separacao; ?>&amp;id_remessa=<?= $rs_separacao_encerra->id_remessa; ?>&amp;tipo_separacao=0');" id="link_edita_separacao<?=$rs_separacao_encerra->id_separacao;?>">edita</button>
								<? } else { ?>
								<a href="javascript:void(0);" onclick="abreDiv('tela_aux'); ajaxLink('tela_aux', 'carregaPagina&amp;pagina=op/separacao_horario&amp;id_separacao=<?= $rs_separacao_encerra->id_separacao; ?>&amp;id_remessa=<?= $rs_separacao_encerra->id_remessa; ?>&amp;tipo_separacao=0');"><img border="0" src="images/ico_lapis.png" alt="Edita" /></a>
								<? } ?>
							<? } ?>
						</td>
						<td width="27%">
							<?
                            if ($linhas_separacao_encerra>0) {
								if ($erro) echo " <strong><span class='vermelho'>ERRO</span></strong> ";
								else {
									echo calcula_total_horas($diferenca);
									$tempo_separacao+=$diferenca;
								}
								
							}
							else echo "Aguardando...";
							?>
                        </td>
                        <td width="7%">
                        	<a onclick="return confirm('Tem certeza que deseja excluir esta faixa de horários?');" href="link.php?apagaSeparacaoRemessa&id_separacao=<?=$rs_separacoes->id_separacao;?>&amp;id_remessa=<?=$rs_separacoes->id_remessa;?>"><img src="images/ico_lixeira.png" alt="Excluir" /></a>
                        </td>
					</tr>
					<?
						$e++;
					}
					
					if ($linhas_separacoes>1) {
	            ?>
                	<tr>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><strong><?= calcula_total_horas($tempo_separacao); ?></strong></td>
                        <td>&nbsp;</td>
                    </tr>
                	<? } ?>
  	        </table>
            <br />
            <? } ?>
            
            <button id="link_inicia<?=$i;?>" onclick="var confirma= confirm('Tem certeza que deseja iniciar a separação?'); if (confirma) ajaxLink('conteudo', 'iniciaSeparacaoRemessa&id_remessa=<?= $rs->id_remessa; ?>');">Inicia</button>
            
        </td>
		
	</tr>
	<? $i++; } ?>
</table>
<br /><br />

<?
if ($num_paginas > 1) {
	$num_pagina_real= $num_pagina+1;
?>
<fieldset class="tamanho200">
	<legend>Páginas:</legend>
    
    <div class="flutuar_esquerda espaco_dir"><strong>Página:</strong></div> <input onkeyup="if (event.keyCode==13) pulaParaPagina('./?pagina=op/separacao_listar&amp;data=<?=$data;?>&amp;num_pagina=', this);" class="tamanho40 flutuar_esquerda" name="num_pagina" id="num_pagina" value="<?= $num_pagina_real; ?>" /> de <b><?= fnumf($num_paginas); ?></b>.
</fieldset>
<? } ?>

<script type="text/javascript">
	daFoco("link_inicia0");
</script>

<? } ?>