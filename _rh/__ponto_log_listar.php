<?
require_once("conexao.php");
if (pode("rhv", $_SESSION["permissao"])) {
	
	if ($_POST["data_log"]!="") $data_log= $_POST["data_log"];
	if ($_GET["data_log"]!="") $data_log= $_GET["data_log"];
	if ($data_log!="") $str .= " and   data_log = '". formata_data($data_log) ."' ";
	
	if ($_POST["hora_log"]!="") $hora_log= $_POST["hora_log"];
	if ($_GET["hora_log"]!="") $hora_log= $_GET["hora_log"];
	if ($hora_log!="") $str .= " and   LEFT(hora_log, 2) = '". $hora_log ."' ";
	
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($id_funcionario!="") $str .= " and   id_funcionario = '". $id_funcionario ."' ";
	
	if ($_POST["tipo"]!="") $tipo= $_POST["tipo"];
	if ($_GET["tipo"]!="") $tipo= $_GET["tipo"];
	if ($tipo!="") $str .= " and   tipo = '". $tipo ."' ";
	
	$sql= "select * from rh_ponto_logs
						 where 1=1
						 $str
						 order by id_log desc ";
	
	$result= mysql_query($sql) or die(mysql_error());
	
	$total_antes= mysql_num_rows($result);
	
	if (!isset($tudo)) {
		$num= 50;
		$total_linhas = mysql_num_rows($result);
		$num_paginas = ceil($total_linhas/$num);
		if (!isset($num_pagina))
			$num_pagina = 0;
		$inicio = $num_pagina*$num;
		
		$result= mysql_query($sql ." limit $inicio, $num") or die(mysql_error());
	}
?>

<h2>Registro do ponto</h2>

<ul class="recuo1 screen">
	<li><a href="./?pagina=rh/ponto_log_busca">buscar</a></li>
</ul>
<br />

<p>Foram encontrados <strong><?= $total_antes; ?></strong> registro(s), mostrando <strong><?= $num; ?></strong> registros, de <strong><?= $inicio; ?></strong> até <strong><?= ($inicio+$num); ?></strong>. </p>
<br />



<table cellspacing="0" width="100%" class="sortable" id="tabela">
	<tr>
		<th width="3%">Cód.</th>
		<th align="left" width="6%">Cart&atilde;o</th>
		<th width="12%" align="left">Funcion&aacute;rio</th>
		<th width="12%">Data/hora</th>
		<th width="8%">Turnante</th>
		<th width="12%" align="left">Supervisor</th>
		<th width="20%" align="left">Mensagem</th>
		<th width="9%" align="left">Opera&ccedil;&atilde;o</th>
		<th width="12%">IP</th>
		<th width="6%" align="left">?</th>
	</tr>
	<?
	$i= 0;
	while ($rs= mysql_fetch_object($result)) {
		if (($i%2)==0) $classe= "odd";
		else $classe= "even";
		
		/*if ($rs->tipo==1) {
			$cor= "azul";
		}
		elseif ($rs->tipo==0) {
			$cor= "vermelho";
		}
		else {
			$cor= "amarelo";
		}*/
	?>
	<tr class="corzinha <?= $cor; ?> <?= $classe; ?>">
		<td align="center"><?= $rs->id_log; ?></td>
		<td><?= $rs->num_cartao; ?></a></td>
		<td class="menor"><?= pega_funcionario($rs->id_funcionario); ?></td>
		<td align="center" class="menor"><?= desformata_data($rs->data_log) ." ". $rs->hora_log; ?></td>
		<td align="center"><?= strip_tags(sim_nao($rs->turnante)); ?></td>
		<td class="menor"><?= pega_funcionario($rs->id_supervisor); ?></td>
		<td class="menor">
		<?
        if (($rs->msg!="SUPERVISOR AUTORIZANDO") && ($rs->msg!="Entrada") && ($rs->msg!="Saída")) echo "<span class=\"vermelho\">". $rs->msg ."</span>";
		else echo $rs->msg;
		?>
        </td>
		<td class="menor"><?= entrada_saida_erro($rs->tipo); ?></td>
		<td align="center">
			<?
            if ($rs->ip!="") echo $rs->ip;
            ?>
        </td>
		<td><?= $rs->id_horario. " (". $rs->parte_script .")"; ?></td>
	</tr>
	<? $i++; } ?>
</table>
<br />
<?
if ($total_linhas>0) {
	if ($num_paginas > 1) {
		echo "<br /><strong>Páginas:</strong> "; 
		
		for ($i=0; $i<$num_paginas; $i++) {
			$link = $i + 1;
			if ($num_pagina==$i) $texto_paginacao .= "<strong>". $link ."</strong> ";
			else $texto_paginacao .=  "<a href=\"./?pagina=rh/ponto_log_listar&amp;data_log=". $data_log ."&amp;hora_log=". $hora_log ."&amp;id_funcionario=". $id_funcionario ."&amp;tipo=". $tipo ."&amp;num_pagina=". $i ."\">". $link ."</a> ";
		}

		echo $texto_paginacao;
	}
}
?>
<br /><br /><br /><br /><br />
<? } ?>