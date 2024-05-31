<?
require_once("conexao.php");
if (pode("iq|", $_SESSION["permissao"])) {
	
	if ($_GET["status_nota"]!="") $status_nota= $_GET["status_nota"];
	if ($_POST["status_nota"]!="") $status_nota= $_POST["status_nota"];
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	if ($tipo_nota=='p') {
		if ($status_nota==1) $tit2= "pagas";
		else $tit2= "à pagar";
		$txt_cedente= "Fornecedor";
		$tipo_cedente= "f";
	}
	else {
		if ($status_nota==1) $tit2= "recebidas";
		else $tit2= "à receber";
		$txt_cedente= "Cliente";
		$tipo_cedente= "c";
	}
?>

<h2>Busca de duplicata <?= $tit2; ?></h2>

<div class="parte50">
    <fieldset>
        <legend>Anual/trimestral</legend>
        
        <form action="<?= AJAX_FORM; ?>formEstoqueBalanco" method="post" name="formEstoqueBalanco" id="formEstoqueBalanco" onsubmit="return ajaxForm('conteudo', 'formEstoqueBalanco', 'validacoes');">

                
                <label for="ano">Ano:</label>
                <select name="ano" id="ano">
                    <?
                    for ($i=date("Y"); $i>=2009; $i--) {
                    ?>
                    <option class="<? if (($i%2)==0) echo "cor_sim"; ?>" value="<?= $i; ?>"><?= $i; ?></option>
                    <? } ?>
                </select>
                <br />
    
                <label for="periodo">Periodicidade:</label>
                <input type="radio" name="periodicidade" id="periodicidade_1" value="1" class="tamanho20" checked="checked" /> <label class="tamanho20 nao_negrito" for="periodicidade_1">1ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_2" value="2" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_2">2ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_3" value="3" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_3">3ºtri</label>
                <input type="radio" name="periodicidade" id="periodicidade_4" value="4" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_4">4ºtri</label>	
                <input type="radio" name="periodicidade" id="periodicidade_a" value="a" class="tamanho20" /> <label class="tamanho20 nao_negrito" for="periodicidade_a">anual</label>	
			<br /><br />
                
            
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<div class="parte50">
    <fieldset>
        <legend>Mensal</legend>
        
        <form action="<?= AJAX_FORM; ?>formEstoqueBalanco2" method="post" name="formEstoqueBalanco2" id="formEstoqueBalanco2" onsubmit="return ajaxForm('conteudo', 'formEstoqueBalanco2');">
                
            <label for="periodo">Período:</label>
            <select class="tamanho25p" name="periodo" id="periodo" title="Período">	  		
                <?
                $i=0;
                $result_per= mysql_query("select distinct(DATE_FORMAT(data_trans, '%m/%Y')) as data_batida2
                                              from fi_estoque_mov order by data_trans desc ");
                
                while ($rs_per= mysql_fetch_object($result_per)) {
                    $data_batida= explode('/', $rs_per->data_batida2);
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data_batida2; ?>" <? if ($_POST["periodo"]==$rs_per->data_batida2) echo "selected=\"selected\""; ?>><?= traduz_mes($data_batida[0]) .'/'. $data_batida[1]; ?></option>
                <? $i++; } ?>
            </select>
                <br />
			<br /><br />
                
            
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	daFoco("id_abastecimento");
</script>

<? } ?>