<?
if ($_SESSION["tipo_empresa"]=="a") {
?>
<h2 class="titulos">Cadastro de equipamento para <?= pega_empresa($_GET["id_empresa"]); ?></h2>

<p>Preencha corretamente os campos marcados com * abaixo para cadastrar uma nova empresa:</p>

<br />

	<form action="<?= AJAX_FORM; ?>formEqInserir" method="post" name="formEqInserir" id="formEqInserir" onsubmit="return ajaxForm('conteudo', 'formEqInserir');">
	<input name="acao" type="hidden" id="acao" value="1" class="escondido" />
    <input name="id_empresa" type="hidden" id="id_empresa" value="<?= $_GET["id_empresa"]; ?>" class="escondido" />

	<div class="parte50">
    
        <label for="ident">Identificação:</label>
        <select name="ident" id="ident">
        	<?
			$j=1;
			$vetor= pega_ident_equipamento("l");
			while ($vetor[$j]) {
			?>
			<option <? if (($j%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->ident==$j) echo "selected=\"selected\""; ?> value="<?=$j;?>"><?= $vetor[$j]; ?></option>
			<?
				$j++;
			}
			?>
        </select>
        <br class="limpa" />
    
        <label for="modelo">Modelo:</label>
		<select name="modelo" id="modelo">
        	<?
			$j=1;
			$vetor= pega_modelo_equipamento("l");
			while ($vetor[$j]) {
			?>
			<option <? if (($j%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->modelo==$j) echo "selected=\"selected\""; ?> value="<?=$j;?>"><?= $vetor[$j]; ?></option>
			<?
				$j++;
			}
			?>
        </select>
        <br class="limpa" />
    
        <label for="n_serie">Nº série:</label>
        <input name="n_serie" id="n_serie" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" value="<?= $rs->n_serie; ?>" />
        <br class="limpa" />
        
        <label for="voltagem">Voltagem:</label>
        <input name="voltagem" id="voltagem" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" value="<?= $rs->voltagem; ?>" />
        <br class="limpa" />
    </div>
    <div class="parte50">
		<label for="classe">Classe:</label>
		<select name="classe" id="classe">
        	<?
			$j=1;
			$vetor= pega_classe_equipamento("l");
			while ($vetor[$j]) {
			?>
			<option <? if (($j%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->modelo==$j) echo "selected=\"selected\""; ?> value="<?=$j;?>"><?= $vetor[$j]; ?></option>
			<?
				$j++;
			}
			?>
        </select>
        <br class="limpa" />
        
        <label for="tipo">Tipo:</label>
		<select name="tipo" id="tipo">
        	<?
			$j=1;
			$vetor= pega_tipo_equipamento("l");
			while ($vetor[$j]) {
			?>
			<option <? if (($j%2)==0) echo "class=\"cor_sim\""; ?> <? if ($rs->tipo==$j) echo "selected=\"selected\""; ?> value="<?=$j;?>"><?= $vetor[$j]; ?></option>
			<?
				$j++;
			}
			?>
        </select>
        <br class="limpa" />
        
        <label for="dimensoes">Dimensões:</label>
        <input name="dimensoes" id="dimensoes" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" value="<?= $rs->dimensoes; ?>" />
        <br class="limpa" />
	</div>
    <br class="limpa" /><br class="limpa" />
    
    <center>
        <label for="enviar">&nbsp;</label>
        <button type="submit" id="enviar">enviar &gt;&gt;</button>
    </center>
    
</form>
<? } ?>