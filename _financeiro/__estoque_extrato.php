<? if (pode("iq|", $_SESSION["permissao"])) { ?>

<?

if ($_GET["id_item"]) $id_item= $_GET["id_item"];
if ($_POST["id_item"]) $id_item= $_POST["id_item"];

if ($_GET["tipo_trans"]) $tipo_trans= $_GET["tipo_trans"];
if ($_POST["tipo_trans"]) $tipo_trans= $_POST["tipo_trans"];

if (!isset($tipo_trans)) $tipo_trans= "todos";
	
$result= mysql_query("select *, fi_estoque_mov.id_usuario as id_usuario2
					 	from fi_estoque_mov, fi_itens
						where fi_estoque_mov.id_item = '". $id_item ."'
						and   fi_estoque_mov.id_item = fi_itens.id_item
						and   fi_estoque_mov.id_empresa = '". $_SESSION["id_empresa"] ."'
						". $str ."
						order by fi_estoque_mov.id_mov asc
						") or die(mysql_error());

//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "tira extrato do remédio ". $_GET["id_remedio"] ." | ". pega_remedio($_GET["id_remedio"]), $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
?>

<h2>Extrato de movimentação de produto - <?= pega_item($_GET["id_item"]); ?></h2>

<? if ( (isset($_POST["inicio"])) && ($_POST["inicio"]!="") && (isset($_POST["fim"])) && ($_POST["fim"]!="") ) { ?>
	<h3>Entre <?= $_POST["inicio"] . " e ". $_POST["fim"]; ?></h3>
<? } ?>

<p>Foram encontrados <strong><?= mysql_num_rows($result); ?></strong> registro(s) para sua solicitação.</p>
<br />

<?
if ( (!isset($tipo_trans)) || ($tipo_trans=="todos") ) {
    $th_str= "Tipo de transação";
}

switch ($tipo_trans) {
    case 'm': $th_str= "Centro de custo";
                break;
    case 'd': $th_str= "Para";
                break;
    case 's': $th_str= "Tipo saída";
                break;
}
if (mysql_num_rows($result)>0) {
?>

<table cellspacing="0" width="100%">
    <tr>
        <th width="8%">Cód.</th>
        <th width="16%">Data</th>
        <th width="19%">Tipo de transação</th>
        <th width="12%" align="right">Quantidade</th>
        <th width="10%" align="right">Saldo atual </th>
        <th width="18%">&nbsp;</th>
        <th width="17%" align="left">Autorizado por</th>
    </tr>
    <?
    $i= 0;

    if ( (isset($_POST["inicio"])) && ($_POST["inicio"]!="") && (isset($_POST["fim"])) && ($_POST["fim"]!="") ) {
        $datai= desformata_data($_POST["inicio"]);
        $dataf= desformata_data($_POST["fim"]);
        
        $data_inicial= date("Ymd", mktime(0, 0, 0, $datai[1], $datai[0], $datai[2]));
        $data_final= date("Ymd", mktime(0, 0, 0, $dataf[1], $dataf[0], $dataf[2]));
    }
    
    while ($rs= mysql_fetch_object($result)) {
		
		if ($rs->tipo_trans=="e") {
			$saldo += $rs->qtde;
			$sinal= "+";
		}
		else {
			$saldo -= $rs->qtde;
			$sinal= "-";
		}
		
		if ($rs->tipo_trans=="e") $cor= "azul";
		else $cor= "vermelho";
        
        if ( (isset($_POST["inicio"])) && ($_POST["inicio"]!="") && (isset($_POST["fim"])) && ($_POST["fim"]!="") ) {
            $registro[$i]= date("Ymd", mktime(0, 0, 0, $rs->mes, $rs->dia, $rs->ano));
        }

        if ( (($registro[$i] >= $data_inicial) && ($registro[$i] <= $data_final) ) || !((isset($_POST["inicio"])) && ($_POST["inicio"]!="") && (isset($_POST["fim"])) && ($_POST["fim"]!="")) ) {
    ?>
    <tr class="corzinha">
        <td align="center">
            <?= $rs->id_mov; ?></td>
        <td align="center"><?= desformata_data($rs->data_trans) ." ". $rs->hora_trans; ?></td>
        <td align="center">
		<?
		if ($rs->subtipo_trans=="m") echo "Movimentação";
		else echo pega_tipo_transacao($rs->tipo_trans);
		
		if ($rs->subtipo_trans=="n") echo "(RM)";
		?>
        </td>
        <td align="right" class="<?= $cor; ?>"><?= $sinal . fnumf($rs->qtde) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
        <td align="right" class="azul"><?= fnumf($saldo) ." ". pega_tipo_apres($rs->tipo_apres); ?></td>
        <td align="center">
        <?= $destino; ?>
        </td>
        <td><?= pega_nome_pelo_id_usuario($rs->id_usuario2); ?></td>
    </tr>
    <?
        }
        $i++;
    }
    ?>
</table>

<?
}
?>
<br /><br /><br /><br /><br />
<?
}
?>