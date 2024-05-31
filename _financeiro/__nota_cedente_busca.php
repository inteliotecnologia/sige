<?
require_once("conexao.php");
if (pode("i", $_SESSION["permissao"])) {
	
	if ($_GET["status_nota"]!="") $status_nota= $_GET["status_nota"];
	if ($_POST["status_nota"]!="") $status_nota= $_POST["status_nota"];
	
	if ($_GET["tipo_nota"]!="") $tipo_nota= $_GET["tipo_nota"];
	if ($_POST["tipo_nota"]!="") $tipo_nota= $_POST["tipo_nota"];
	
	if ($tipo_nota=='p') {
		if ($status_nota==1) $tit2= "pagas";
		else $tit2= "à pagar";
		$txt_cedente= "Fornecedor";
		$txt_cedente_plural= "Fornecedores";
		$tipo_cedente= "f";
	}
	else {
		if ($status_nota==1) $tit2= "recebidas";
		else $tit2= "à receber";
		$txt_cedente= "Cliente";
		$txt_cedente_plural= "Clientes";
		$tipo_cedente= "c";
	}
?>

<h2>Relatório de duplicatas - <?= $txt_cedente_plural; ?></h2>

<div id="conteudo_interno">
    <fieldset>
        <legend>Formulário de busca</legend>
        
        <form action="index2.php?pagina=financeiro/nota_cedente_relatorio" target="_blank" method="post" name="formNotaBuscar" id="formNotaBuscar">

            <div class="parte50">
                
                <input class="escondido" type="hidden" id="validacoes" value="id_cedente@vazio" />
                <input class="escondido" type="hidden" name="geral" value="<?= $_GET["geral"]; ?>" />
                <input class="escondido" type="hidden" name="tipo_nota" value="<?= $tipo_nota; ?>" />
                
                <label for="periodo">Vencimento:</label>
                <select name="periodo" id="periodo" title="Período">	  		
					<option value="">--- TODOS ---</option>
					<?
					$i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_vencimento, '%m/%Y')) as data2
												from fi_notas order by data_vencimento desc ");
					
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data= explode('/', $rs_per->data2);
					?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1">Datas:</label>
                <input name="data1" id="data1" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2" id="data2" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                <label for="periodo_emissao">Emissão:</label>
                <select name="periodo_emissao" id="periodo_emissao" title="Período">	  		
					<option value="">--- TODOS ---</option>
					<?
					$i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_emissao, '%m/%Y')) as data2
												from fi_notas order by data_emissao desc ");
					
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data= explode('/', $rs_per->data2);
					?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1_emissao">Datas:</label>
                <input name="data1_emissao" id="data1_emissao" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2_emissao" id="data2_emissao" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                
                
                <? if ($tipo_nota=="p") { ?>
                <label for="periodo_pagamento">Pagamento:</label>
                <select name="periodo_pagamento" id="periodo_pagamento" title="Período">	  		
					<option value="">--- TODOS ---</option>
					<?
					$i=0;
                    $result_per= mysql_query("select distinct(DATE_FORMAT(data_pagamento, '%m/%Y')) as data2
												from fi_notas_parcelas_pagamentos order by data_pagamento desc ");
					
                    while ($rs_per= mysql_fetch_object($result_per)) {
						$data= explode('/', $rs_per->data2);
					?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label>&nbsp;</label>
                ou
                <br />
                
                <label for="data1_pagamento">Datas:</label>
                <input name="data1_pagamento" id="data1_pagamento" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 1" />
                <div class="flutuar_esquerda espaco_dir">à</div>
                <input name="data2_pagamento" id="data2_pagamento" class="tamanho25p espaco_dir" onkeyup="formataData(this);" maxlength="10" value="" title="Data 2" />
                <br /><br />
                <? } ?>
                
            </div>
            <div class="parte50">
            	
                <? /* ?>
                <label for="id_empresa_atendente">Atendente:</label>
                <select name="id_empresa_atendente" id="id_empresa_atendente" title="Empresa atendente">
                    <option value="">- TODOS -</option>
                    <?
                    if ($acao=='i') $id_empresa_aqui= $_SESSION["id_empresa"];
                    else $id_empresa_aqui= $rs->id_empresa_atendente;
                    
                    $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                                where pessoas.id_pessoa = empresas.id_pessoa
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = 'a'
                                                order by 
                                                pessoas.apelido_fantasia asc");
                    $i=0;
                    while ($rs_emp = mysql_fetch_object($result_emp)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"<? if ($rs_emp->id_empresa==$id_empresa_aqui) echo "selected=\"selected\""; ?>><?= $rs_emp->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                <? //} */ ?>
                
                <label for="id_cedente"><?= $txt_cedente; ?>:</label>
                <select name="id_cedente" id="id_cedente" title="<?= $txt_cedente; ?>">
                    <option value="">- TODOS -</option>
                    <?
                    $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos
                                                where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                                and   pessoas_tipos.tipo_pessoa = '$tipo_cedente'
                                                order by 
                                                pessoas.apelido_fantasia asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_ced = mysql_fetch_object($result_ced)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs->id_cedente) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <? /*<label for="ordenacao">Ordenação:</label>
                <select name="ordenacao" id="ordenacao" title="Ordenação">	  		
					<option value="">--- TODOS ---</option>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->data2; ?>"><?= traduz_mes($data[0]) .'/'. $data[1]; ?></option>
                </select>
                <br />
                
                
            	<label for="id_centro_custo_tipo">* Tipo:</label>
                <select size="10" multiple="multiple" name="id_centro_custo_tipo[]" id="id_centro_custo_tipo" title="Centro de custo">
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
                            if ($acao=='e') {
                                $result_cc3= mysql_query("select * from fi_pessoas_cc_tipos
                                                            where id_pessoa = '". $rs->id_pessoa ."'
                                                            and   id_centro_custo_tipo = '". $rs_cc2->id_centro_custo_tipo ."'
                                                            ");
                                $linhas_cc3= mysql_num_rows($result_cc3);
                            }
                        ?>
                        <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc2->id_centro_custo_tipo; ?>"<? if ($linhas_cc3>0) echo "selected=\"selected\""; ?>><?= $rs_cc2->centro_custo_tipo; ?></option>
                        <? $i++; } ?>
                    </optgroup>
                    <? } ?>
                </select>
                <br />
				
				*/ ?>
            </div>
            
            <br /><br /><br />
            
            <center>
                <button type="submit" id="enviar">Enviar &raquo;</button>
            </center>
        </form>
        
    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	daFoco("periodo");
</script>

<? } ?>