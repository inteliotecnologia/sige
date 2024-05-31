<?
require_once("conexao.php");
if (pode_algum("pue", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  op_veiculos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_veiculo = '". $_GET["id_veiculo"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Veículos</h2>

<form action="<?= AJAX_FORM; ?>formVeiculo&amp;acao=<?= $acao; ?>" method="post" name="formVeiculo" id="formVeiculo" onsubmit="return validaFormNormal('validacoes', false, 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="codigo@vazio|veiculo@vazio|placa@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_veiculo" class="escondido" type="hidden" id="id_veiculo" value="<?= $rs->id_veiculo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="codigo">* Código:</label>
            <input title="Código" name="codigo" value="<?= $rs->codigo; ?>" id="codigo" />
            <br />
            
            <label for="veiculo">* Veículo:</label>
            <input title="Veículo" name="veiculo" value="<?= $rs->veiculo; ?>" id="veiculo" />
            <br />
            
            <label for="placa">* Placa:</label>
            <input title="Placa" name="placa" value="<?= $rs->placa; ?>" id="placa" />
            <br />
            
            <label for="tipo_padrao">Tipo (padrão):</label>
            <select name="tipo_padrao" id="tipo_padrao" title="Tipo">
                <option value="">-</option>
                <?
                $vetor= pega_coleta_entrega('l');
                $i=1;
                while ($i<3) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tipo_padrao==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="chassi">Chassi:</label>
            <input title="Chassi" name="chassi" value="<?= $rs->chassi; ?>" id="chassi" />
            <br />
            
            <label for="cod_cor">Cód. cor:</label>
            <input title="Cód. cor" name="cod_cor" value="<?= $rs->cod_cor; ?>" id="cod_cor" />
            <br />
            
            <label for="motor">Motor:</label>
            <input title="Motor" name="motor" value="<?= $rs->motor; ?>" id="motor" />
            <br />
            
            <label for="peso_bruto">Peso bruto:</label>
            <input title="Peso bruto" onkeydown="formataValor(this,event);" name="peso_bruto" value="<?= fnum($rs->peso_bruto); ?>" id="peso_bruto" />
            <br />
            
            <label for="entre_eixos">Entre eixos:</label>
            <input title="Entre eixos" name="entre_eixos" value="<?= $rs->entre_eixos; ?>" id="entre_eixos" />
            <br />
        </div>
        
    	<br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
    </fieldset>
</form>
<? } ?>