<?
require_once("conexao.php");
if (pode("r", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  rh_motivos
								where id_motivo = '". $_GET["id_motivo"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
	if ($_GET["tipo_motivo"]!="") $tipo_motivo= $_GET["tipo_motivo"];
	else $tipo_motivo= $rs->tipo_motivo;
?>
<h2>Motivo</h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formMotivo&amp;acao=<?= $acao; ?>" method="post" name="formMotivo" id="formMotivo" onsubmit="return ajaxForm('conteudo', 'formMotivo', 'validacoes');">
    
    <?
	if ($tipo_motivo=='o')
		$validacoes_adicionais= "|qtde_dias@numeros";
	?>
    
    <input class="escondido" type="hidden" id="validacoes" value="motivo@vazio|tipo_motivo@vazio<?=$validacoes_adicionais;?>" />
    <? if ($acao=='e') { ?>
    <input name="id_motivo" class="escondido" type="hidden" id="id_motivo" value="<?= $rs->id_motivo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="razao_social">Empresa:</label>
            <?= pega_empresa($_SESSION["id_empresa"]); ?>
            <br />		
            
            <label>Tipo:</label>
            <?= pega_tipo_motivo($tipo_motivo); ?>
            <input class="escondido" type="hidden" name="tipo_motivo" value="<?= $tipo_motivo; ?>" id="tipo_motivo" />
            <br />
            
            <label for="motivo">* Motivo:</label>
            <input title="Motivo" name="motivo" value="<?= $rs->motivo; ?>" id="motivo" />
            <br />
            
            <? if ($tipo_motivo=='o') { ?>
            <label for="qtde_dias">* Quantidade de dias:</label>
            <input title="Quantidade de dias" name="qtde_dias" value="<?= $rs->qtde_dias; ?>" id="qtde_dias" />
            <br />
            <? } ?>
            
            <? if ($tipo_motivo=='t') { ?>
            <label for="qtde_dias">* Tipo:</label>
            <select title="Tipo de dados" name="qtde_dias" id="qtde_dias">
            	<option value="0" <? if ($rs->qtde_dias==0) echo "selected=\"selected\""; ?>>Inteiro</option>
                <option value="1" <? if ($rs->qtde_dias==1) echo "selected=\"selected\""; ?> class="cor_sim">Monetário</option>
            </select>
            <br />
            <? } ?>
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>