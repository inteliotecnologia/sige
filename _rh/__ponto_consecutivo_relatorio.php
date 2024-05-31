<? if (pode("r", $_SESSION["permissao"])) { ?>

<h2>Batidas de ponto consecutiva</h2>

<?
$j= 0;
for ($i=1; $i<=date("m"); $i++) {
?>
	<h2><?= traduz_mes($i); ?></h2>

    <table cellspacing="0" width="100%">
        <tr>
            <th width="7%">Cód.</th>
            <th width="19%" align="left">Empresa</th>
            <th width="32%" align="left">Nome</th>
            <th width="28%" align="left">Horário</th>
            <th width="14%" align="left">Diferen&ccedil;a</th>
      </tr>
        <?
		//pegar as saídas
		$result_saida = mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2, DATE_FORMAT(vale_dia, '%d/%m/%Y') as vale_dia2
															from rh_ponto
															where DATE_FORMAT(data_batida, '%m') = '". formata_saida($i, 2) ."'
															and   DATE_FORMAT(data_batida, '%Y') = '". date("Y") ."'
															and   tipo = '0'
															order by id_funcionario asc, data_batida asc, hora asc
                                                        ") or die(mysql_error());
		
		/*echo "select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2
															from rh_ponto
															where DATE_FORMAT(data_batida, '%m') = '". formata_saida($i, 2) ."'
															and   DATE_FORMAT(data_batida, '%Y') = '". date("Y") ."'
															and   tipo = '0'
															order by vale_dia asc
                                                        ";*/
        while ($rs= mysql_fetch_object($result_saida)) {
            if (($j%2)==0) $classe= "cor_sim";
            else $classe= "cor_nao";
    
			//mk da saída
            $entrada_possivel= faz_mk_data_completa($rs->data_batida2 ." ". $rs->hora)+38400;
			
			$vale_dia= explode("-", $rs->vale_dia);
			//1 dia depois da batida de saída
			$vale_dia_atual= date("Y-m-d", mktime(0, 0, 0, $vale_dia[1], $vale_dia[2]+1, $vale_dia[0]));
			
			/*echo "<br><br>select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2
															from rh_ponto
															where vale_dia = '". $vale_dia_atual ."'
															and   id_funcionario = '". $rs->id_funcionario ."'
															and   data_batida = '". date("Y-m-d", $entrada_possivel) ."'
															and   hora < '". date("H:i:s", $entrada_possivel) ."'
															and   tipo = '1'
                                                        ";*/
			
			$result_entrada= mysql_query("select *, DATE_FORMAT(data_batida, '%d/%m/%Y') as data_batida2, DATE_FORMAT(vale_dia, '%d/%m/%Y') as vale_dia2
															from rh_ponto
															where vale_dia = '". $vale_dia_atual ."'
															and   id_funcionario = '". $rs->id_funcionario ."'
															and   data_batida = '". date("Y-m-d", $entrada_possivel) ."'
															and   hora < '". date("H:i:s", $entrada_possivel) ."'
															and   tipo = '1'
                                                        ") or die(mysql_error());
			$rs_entrada= mysql_fetch_object($result_entrada);
			
			if (mysql_num_rows($result_entrada)>0)
				$diferenca= retorna_intervalo($rs->data_batida ." ". $rs->hora, $rs_entrada->data_batida ." ". $rs_entrada->hora);
			
			//echo $rs_entrada->data_batida ." ". $rs_entrada->hora; die();
			
			if ((mysql_num_rows($result_entrada)>0) && ($diferenca>3600)) {
        ?>
        <tr class="<?= $classe; ?> corzinha">
            <td align="center"><?= $rs->id_horario; ?></td>
            <td><?= pega_empresa($_SESSION["id_empresa"]); ?></td>
            <td><?= pega_funcionario($rs->id_funcionario); ?></td>
            <td>
            <strong>Saída:</strong> <?= $rs->data_batida2 ." ". $rs->hora; ?> (<?= $rs->vale_dia2; ?>) <br />
            <strong>Entrada:</strong> <?= $rs_entrada->data_batida2 ." ". $rs_entrada->hora; ?> (<?= $rs_entrada->vale_dia2; ?>)</td>
            <td><?= date("H:i:s", mktime(0, 0, $diferenca, 0, 0, 0)); ?></td>
        </tr>
        <? $j++; } } ?>
    </table>
<? } ?>
<br /><br /><br />
<? } ?>