<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	if ($_GET["id_vt_desconto"]!="") $id_vt_desconto= $_GET["id_vt_desconto"];
	if ($_POST["id_vt_desconto"]!="") $id_vt_desconto= $_POST["id_vt_desconto"];
	
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	if ($_POST["id_funcionario"]!="") $id_funcionario= $_POST["id_funcionario"];
	
	if ($acao=="") $acao= $_GET["acao"];

	if ($acao=="e") {
		$result= mysql_query("select * from rh_vt_descontos
							 	where id_vt_desconto = '". $id_vt_desconto ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$id_funcionario= $rs->id_funcionario;
	}
?>
<form action="<?= AJAX_FORM; ?>formVTDesconto&amp;acao=<?= $acao; ?>" method="post" name="formVTDesconto" id="formVTDesconto" onsubmit="return ajaxForm('conteudo_interno', 'formVTDesconto', 'validacoes_desconto', true);">

    <input class="escondido" type="hidden" id="validacoes_desconto" value="id_funcionario@vazio|periodo@vazio|qtde@vazio" />
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $id_funcionario; ?>" />
    
    <? if ($acao=="e") { ?>
    <input name="id_vt_desconto" class="escondido" type="hidden" id="id_vt_desconto" value="<?= $rs->id_vt_desconto; ?>" />
    <? } ?>
       
	<label for="id_funcionario">Funcionário:</label>
    <select name="id_funcionario" id="id_funcionario" title="Funcionário">
        <?
        $result_fun= mysql_query("select *
                                    from  pessoas, rh_funcionarios, rh_carreiras
                                    where pessoas.id_pessoa = rh_funcionarios.id_pessoa
                                    and   pessoas.tipo = 'f'
                                    and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
                                    and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
                                    and   rh_carreiras.atual = '1'
                                    and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    order by pessoas.nome_rz asc
                                    ") or die(mysql_error());
        $i=0;
        while ($rs_fun= mysql_fetch_object($result_fun)) {
        ?>
        <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>" <? if ($rs_fun->id_funcionario==$id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
        <? $i++; } ?>
    </select>
    <br />
    
    <label for="periodo">Mês/ano:</label>
    <select name="periodo" id="periodo" title="Mês/ano">
    	<?
		$i=0;
		$result_per= mysql_query("select distinct(DATE_FORMAT(data_batida, '%m/%Y')) as data_batida2 from rh_ponto order by data_batida desc ");
		
		while ($rs_per= mysql_fetch_object($result_per)) {
			$data_batida= explode('/', $rs_per->data_batida2);
			if ((date("d")>=25) && (date("m")==$data_batida[0]) && (date("Y")==$data_batida[1]) ) {
				$proximo_mes= date("m/Y", mktime(0, 0, 0, $data_batida[0]+1, 1, $data_batida[1]));
				$data_batida2= explode('/', $proximo_mes);
		?>
		<option <? if ($i%2==1) echo "class=\"cor_sim\""; ?> <? if ($rs->mes."/".$rs->ano==$proximo_mes) echo "selected=\"selected\""; ?> value="<?= $proximo_mes; ?>"><?= traduz_mes($data_batida2[0]) .'/'. $data_batida2[1]; ?></option>
		<? } ?>
		<option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> <? if ($rs->mes."/".$rs->ano==$rs_per->data_batida2) echo "selected=\"selected\""; ?> value="<?= $rs_per->data_batida2; ?>"><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
		<? $i++; } ?>
    </select>
    <br />
    
    <label for="data_entrega">Data de entrega:</label>
    <? if ($acao=='i') $data_entrega= date("d/m/Y"); else $data_entrega= desformata_data($rs->data_entrega); ?>
    <input name="data_entrega" id="data_entrega" class="tamanho15p" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" maxlength="10" value="<?=$data_entrega;?>" />
    <br />
    
    <label for="qtde">Desconto:</label>
    <input name="qtde" id="qtde" class="tamanho15p" value="<?=$rs->qtde;?>" />
    <br /><br />
    
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    
</form>
<? } ?>