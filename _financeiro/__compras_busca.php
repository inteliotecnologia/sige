<?
require_once("conexao.php");
if (pode("iq|", $_SESSION["permissao"])) {
	if ($_GET["id_funcionario"]!="") $id_funcionario= $_GET["id_funcionario"];
	else $id_funcionario= $_POST["id_funcionario"];
?>

<h2>Relatório de compras</h2>

<div class="parte50">
    <fieldset>
        <legend>Discriminado por preço</legend>
    
        <form action="index2.php?pagina=financeiro/compras_relatorio" target="_blank" method="post">
            
            <input class="escondido" type="hidden" id="validacoes" value="periodo@vazio" />
            <input class="escondido" type="hidden" name="tipo_relatorio" value="p" />
            
            <label for="id_centro_custo_tipo">* Tipo:</label>
            <select name="id_centro_custo_tipo" id="id_centro_custo_tipo" title="Centro de custo">
                <option value="">- TODOS -</option>
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
            
            <!--
            <label>&nbsp;</label>
            ou
            <br />
            
            <label for="data1">Datas:</label>
            <input name="data1" id="data1" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
            <div class="flutuar_esquerda espaco_dir">à</div>
            <input name="data2" id="data2" class="tamanho25p espaco_dir" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="" title="Data 1" />
            <br />
            -->
            
            <br /><br />
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
            
        </form>
        
    </fieldset>
</div>
<br />

<? } ?>