<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  fi_centro_custos_tipos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_centro_custo_tipo = '". $_GET["id_centro_custo_tipo"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Centro de custo</h2>

<form action="<?= AJAX_FORM; ?>formTipoCentroCusto&amp;acao=<?= $acao; ?>" method="post" name="formTipoCentroCusto" id="formTipoCentroCusto" onsubmit="return ajaxForm('conteudo', 'formTipoCentroCusto', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="id_centro_custo@vazio|centro_custo_tipo@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_centro_custo_tipo" class="escondido" type="hidden" id="id_centro_custo_tipo" value="<?= $rs->id_centro_custo_tipo; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
        	
            <label for="id_centro_custo">* Centro de custo:</label>
			<?
            $result_cc= mysql_query("select *
                                        from  fi_centro_custos
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                        order by centro_custo asc
                                        ") or die(mysql_error());
            $i=1;
            while ($rs_cc= mysql_fetch_object($result_cc)) {
				$result3= mysql_query("select * from fi_cc_ct
										where id_centro_custo_tipo = '". $rs->id_centro_custo_tipo ."'
										and   id_centro_custo = '". $rs_cc->id_centro_custo ."'
										");
				$linhas3= mysql_num_rows($result3);
				
            ?>
            <input type="checkbox" name="id_centro_custo[]" id="id_centro_custo_<?=$i;?>" class="tamanho30" value="<?= $rs_cc->id_centro_custo; ?>" <? if ($linhas3>0) echo "checked=\"checked\""; ?> />
            <label for="id_centro_custo_<?=$i;?>" class="alinhar_esquerda nao_negrito"><?= $rs_cc->centro_custo; ?></label>
            <? if (($i%2)==0) { ?><br /><label>&nbsp;</label><? } ?>
            
            <? $i++; } ?>
            <br />
            
            <label for="centro_custo_tipo">* Tipo:</label>
            <input title="Tipo/Centro de custo" name="centro_custo_tipo" value="<?= $rs->centro_custo_tipo; ?>" id="centro_custo_tipo" />
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>