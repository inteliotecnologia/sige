<?
require_once("conexao.php");
if (pode("qit", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  fi_itens
								where id_item = '". $_GET["id_item"] ."'
								/* and   id_empresa = '". $_SESSION["id_empresa"] ."' */
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Produtos</h2>

<form action="<?= AJAX_FORM; ?>formItem&amp;acao=<?= $acao; ?>" method="post" name="formItem" id="formItem" onsubmit="return ajaxForm('conteudo', 'formItem', 'validacoes');">
    
    <input class="escondido" type="hidden" id="validacoes" value="item@vazio|tipo_apres@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_item" class="escondido" type="hidden" id="id_item" value="<?= $rs->id_item; ?>" />
    <? } ?>
    
    <fieldset>
        <legend>Dados</legend>
        
        <div class="parte50">
            <label for="item">* Produto:</label>
            <input title="Produto" name="item" value="<?= $rs->item; ?>" id="item" />
            <br />
            
            <label for="tipo_apres">Apresentação:</label>
            <select name="tipo_apres" id="tipo_apres">
                <?
                $vetor= pega_tipo_apres('l');
                
                $i=0; $j=0;
                while ($vetor[$i][$j]) {
                ?>
                <option <? if (($i%2)==0) echo "class=\"cor_sim\""; ?> value="<?= $vetor[$i][0]; ?>" <? if ($rs->tipo_apres==$vetor[$i][0]) echo "selected=\"selected\""; ?>><?= $vetor[$i][1]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_centro_custo_tipo">* Tipo (Centro de Custo):</label>
            <select name="id_centro_custo_tipo" id="id_centro_custo_tipo" title="Centro de custo">
                <option value="">- NENHUM -</option>
				<?
                $result_cc= mysql_query("select *
                                            from  fi_centro_custos
                                            where id_empresa = '". $_SESSION["id_empresa"] ."'
                                            order by centro_custo asc
                                            ") or die(mysql_error());
                while ($rs_cc= mysql_fetch_object($result_cc)) {
                ?>
                <optgroup label="<?= $rs_cc->centro_custo; ?>">
					<?
                    $result_cc2= mysql_query("select *
                                                from  fi_centro_custos_tipos, fi_cc_ct
                                                where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
												and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
												and   fi_cc_ct.id_centro_custo = '". $rs_cc->id_centro_custo ."'
                                                order by fi_centro_custos_tipos.centro_custo_tipo asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cc2= mysql_fetch_object($result_cc2)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc2->id_centro_custo_tipo; ?>"<? if ($rs->id_centro_custo_tipo==$rs_cc2->id_centro_custo_tipo) echo "selected=\"selected\""; ?>><?= $rs_cc2->centro_custo_tipo; ?></option>
                    <? $i++; } ?>
				</optgroup>
                <? } ?>
            </select>
            <br />
        </div>
        <br /><br /><br />
        
        <center>
            <button type="submit" id="enviar">Enviar &raquo;</button>
        </center>
        
    </fieldset>
            
</form>

<? if ($acao=='e') { ?>

<form action="<?= AJAX_FORM; ?>formItemExcluirNovo&amp;acao=<?= $acao; ?>" method="post" name="formItemExcluirNovo" id="formItemExcluirNovo" onsubmit="return ajaxForm('conteudo', 'formItemExcluirNovo', 'validacoes', 1);">

    <fieldset>
        <legend>Excluir este item e associar as ações a um novo:</legend>
    
        <input class="escondido" type="hidden" id="validacoes" value="item@vazio|tipo_apres@vazio" />
        <input name="id_item" class="escondido" type="hidden" id="id_item" value="<?= $rs->id_item; ?>" />
        <input name="item" class="escondido" type="hidden" id="item" value="<?= $rs->item; ?>" />
        
        <div class="parte50">
            <label for="id_item_novo">* Produto:</label>
            <select name="id_item_novo" id="id_item_novo" title="Item novo">
                <option value="">- NENHUM -</option>
                <?
                $result_item= mysql_query("select * from fi_itens
											where id_item <> '". $_GET["id_item"] ."'
											order by item asc
											") or die(mysql_error());
				$i=0;
                while ($rs_item= mysql_fetch_object($result_item)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_item->id_item; ?>"><?= $rs_item->item; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        </div>
        
        <center>
            <button type="submit" id="enviar">Excluir este item &raquo;</button>
        </center>
	</fieldset>

 </form>
<? } ?>

<? } ?>