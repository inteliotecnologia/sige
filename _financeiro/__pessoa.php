<?
require_once("conexao.php");
if (pode_algum("iaprz", $_SESSION["permissao"])) {
	if ($acao=="") $acao= $_GET["acao"];
	
	if ($_GET["tipo_pessoa"]!="") $tipo_pessoa= $_GET["tipo_pessoa"];
	if ($_POST["tipo_pessoa"]!="") $tipo_pessoa= $_POST["tipo_pessoa"];
	
	if ($_GET["status_pessoa"]!="") $status_pessoa= $_GET["status_pessoa"];
	if ($_POST["status_pessoa"]!="") $status_pessoa= $_POST["status_pessoa"];
	if ($status_pessoa=="") $status_pessoa= 1;
	
	if ($_GET["id_pessoa"]!="") $id_pessoa= $_GET["id_pessoa"];
	if ($_POST["id_pessoa"]!="") $id_pessoa= $_POST["id_pessoa"];
	
	if ($_GET["esquema"]!="") $esquema= $_GET["esquema"];
	if ($_POST["esquema"]!="") $esquema= $_POST["esquema"];
	
	if ($_GET["id_cliente"]!="") $id_pessoa= $_GET["id_cliente"];
	
	if ($acao=='e') {
		$result= mysql_query("select *, DATE_FORMAT(data, '%d/%m/%Y') as data2
								from  pessoas, pessoas_tipos, rh_enderecos, cidades, ufs
								where pessoas.id_pessoa = rh_enderecos.id_pessoa
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas.id_pessoa = '". $id_pessoa ."'
								and   pessoas_tipos.tipo_pessoa = '". $tipo_pessoa ."'
								and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   rh_enderecos.id_cidade = cidades.id_cidade
								and   cidades.id_uf = ufs.id_uf
								limit 1
								") or die(mysql_error());
		
		$rs= mysql_fetch_object($result);
		$tipo_pessoa= $rs->tipo_pessoa;
		$status_pessoa= $rs->status_pessoa;
		
		$tit= "Edição de ";
	} else $tit= "Cadastro de ";
	
	if ( (pode("pi", $_SESSION["permissao"])) && ($tipo_pessoa=="c") )
		$tit.= "cliente";
	elseif ( (pode("i", $_SESSION["permissao"])) && ($tipo_pessoa=="f") )
		$tit.= "fornecedor";
	elseif (pode("a", $_SESSION["permissao"])) {
		$tipo_pessoa= "a";
		$tit.= "empresa com acesso ao sistema";
	}
	
	if ($status_pessoa==3) $tit .= " (em vista)";
?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<? if ($esquema=="") { $div_alvo= "conteudo"; ?>
<h2><?= $tit; ?></h2>
<? } else $div_alvo= "conteudo_interno"; ?>

<form action="<?= AJAX_FORM; ?>formPessoa&amp;acao=<?= $acao; ?>" enctype="multipart/form-data" method="post" name="formPessoa" id="formPessoa" onsubmit="return ajaxForm('<?=$div_alvo;?>', 'formPessoa', 'validacoes', 1);">
    
    <input class="escondido" type="hidden" id="validacoes" value="nome_rz@vazio|id_cidade@vazio" />
    
    <? if ($acao=='e') { ?>
    <input name="id_pessoa" class="escondido" type="hidden" id="id_pessoa" value="<?= $rs->id_pessoa; ?>" />
    <? } ?>
    <input name="tipo_pessoa" class="escondido" type="hidden" id="tipo_pessoa" value="<?= $tipo_pessoa; ?>" />    
    
	<? if ($status_pessoa!=3) { ?>
    <input name="status_pessoa" class="escondido" type="hidden" id="status_pessoa" value="<?= $status_pessoa; ?>" />
    <? } ?>
    
    <input class="escondido" type="hidden" name="esquema" value="<?=$esquema;?>" />
    
    <? if ($tipo_pessoa!='a') { ?>
    <fieldset>
        <legend>Seleção de tipo</legend>
        
        <div class="parte50">
            <label for="tipo">* Tipo:</label>
            <? if ($acao=='i') { ?>
            <select name="tipo" id="tipo" onchange="alteraTipoPessoa(this.value, '<?= $acao; ?>');">
                <option selected="selected" value="j">Jurídica</option>
                <option value="f">Física</option>
            </select>
            <?
            }
			else {
				echo pega_tipo($rs->tipo);
			?>
            <input name="tipo" class="escondido" type="hidden" id="tipo" value="<?= $rs->tipo; ?>" />
			<? } ?>
            <br />
        </div>
        <div class="parte50">
        	<? if ($acao=='e') { ?>
            <label for="num_pessoa">Matrícula:</label>
            <input name="num_pessoa" id="num_pessoa" class="tamanho15p" value="<?= $rs->num_pessoa; ?>" />
            <br />
            <? } ?>
            
            <? if ($status_pessoa==3) { ?>
            <label for="status_pessoa">Situação:</label>
            <select name="status_pessoa" id="status_pessoa">
                <option value="1">Ativo</option>
                <option value="3" selected="selected">Em vista</option>
            </select>
            
            <!--
            <label for="primeiro_contato">Primeiro contato:</label>
            <input name="primeiro_contato" id="primeiro_contato" class="tamanho25p espaco_dir" onkeyup="formataData(this);" value="<?=desformata_data($rs->primeiro_contato);?>" title="Primeiro contato" />
            <br />
            -->
            <? } ?>
        </div>
    </fieldset>
	<? } else { ?>
    <input name="tipo" class="escondido" type="hidden" id="tipo" value="j" />
    <? } ?>
    
    <div id="tipo_pessoa_atualiza">
        <?
        if (($acao=='i') || ($rs->tipo=='j')) require_once("_financeiro/__pessoaj.php");
		else require_once("_financeiro/__pessoaf.php");
		?>
    </div>
    
    <? if ($tipo_pessoa=='f') { ?>
    <fieldset>
        <legend>Centro de custos</legend>
    	
        <div class="parte50">
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
        </div>
        <div class="parte50">
        	<em>* Segure Ctrl enquanto clica para selecionar mais de um tipo de centro de custo.</em>
        </div>
        
    </fieldset>
    <? } ?>
    
    <? if (($tipo_pessoa=="c") && ($status_pessoa!=3)) { ?>
    <fieldset>
    	<legend>Dados adicionais</legend>
        
        <div class="parte50">
            <label for="id_empresa_atendente">* Atendente:</label>
            <select name="id_empresa_atendente" id="id_empresa_atendente" title="Empresa atendente">
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
            
            <label for="id_cliente_tipo">Tipo de cliente:</label>
            <select name="id_cliente_tipo" id="id_cliente_tipo" title="Tipo de cliente" onchange="alteraClienteTipo(this.value);">
				<? if ($acao=='i') { ?>
                <option value="">- SELECIONE -</option>
                <? } ?>
                
				<?
				$result_cliente_tipo= mysql_query("select * from fi_clientes_tipos
												where id_empresa = '". $_SESSION["id_empresa"] ."' 
												order by cliente_tipo asc ");
				$i=0;
				while ($rs_cliente_tipo = mysql_fetch_object($result_cliente_tipo)) {
				?>
				<option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cliente_tipo->id_cliente_tipo; ?>"<? if ($rs_cliente_tipo->id_cliente_tipo==$rs->id_cliente_tipo) echo "selected=\"selected\""; ?>><?= $rs_cliente_tipo->cliente_tipo; ?></option>
				<? $i++; } ?>
			</select>
            <br />
            
            
        </div>
        <div class="parte50">
            <?
			if (($acao=='i') || ($acao=='e') && ($rs->id_cliente_tipo==1)) $classe_1= "mostra";
			else $classe_1= "nao_mostra";
			?>
            
            <div id="div_lavanderia" class="<?= $classe_1; ?>">
                <label for="id_contrato">Contrato:</label>
                <select name="id_contrato" id="id_contrato" title="Contrato">
                    <?
                    $result_contrato= mysql_query("select * from fi_contratos
                                                    where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                    order by id_contrato asc ");
                    $i=0;
                    while ($rs_contrato = mysql_fetch_object($result_contrato)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_contrato->id_contrato; ?>"<? if ($rs_contrato->id_contrato==$rs->id_contrato) echo "selected=\"selected\""; ?>><?= $rs_contrato->contrato; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
                
                <label for="tipo_pedido">* Pedido:</label>
                <select name="tipo_pedido" id="tipo_pedido" title="Pedido">
                    <option value="1" <? if ($rs->tipo_pedido==1) echo "selected=\"selected\""; ?>>Roupa limpa</option>
                    <option value="2" class="cor_sim" <? if ($rs->tipo_pedido==2) echo "selected=\"selected\""; ?>>Roupa suja</option>
                </select>
                <br />
                
                <label for="basear_peso">* Basear peso:</label>
                <select name="basear_peso" id="basear_peso" title="Basear peso">
                    <option value="1" <? if ($rs->basear_peso==1) echo "selected=\"selected\""; ?>>Pesagem da empresa</option>
                    <option value="2" class="cor_sim" <? if ($rs->basear_peso==2) echo "selected=\"selected\""; ?>>Pesagem do cliente</option>
                </select>
                <br />
                
                <label for="basear_nota_data">* Basear nota:</label>
                <select name="basear_nota_data" id="basear_nota_data" title="Basear nota">
                    <option value="1" <? if ($rs->basear_nota_data==1) echo "selected=\"selected\""; ?>>Data de coleta</option>
                    <option value="2" class="cor_sim" <? if ($rs->basear_nota_data==2) echo "selected=\"selected\""; ?>>Data de entrega</option>
                </select>
                <br />
                
                <label for="balanca_coleta">* Balança na coleta:</label>
                <select name="balanca_coleta" id="balanca_coleta" title="Balança na coleta">
                    <option value="1" <? if ($rs->balanca_coleta==1) echo "selected=\"selected\""; ?>>Sim</option>
                    <option value="0" class="cor_sim" <? if ($rs->balanca_coleta==0) echo "selected=\"selected\""; ?>>Não</option>
                </select>
                <br />
                
                <label for="balanca_entrega">* Balança na entrega:</label>
                <select name="balanca_entrega" id="balanca_entrega" title="Balança na entrega">
                    <option value="1" <? if ($rs->balanca_entrega==1) echo "selected=\"selected\""; ?>>Sim</option>
                    <option value="0" class="cor_sim" <? if ($rs->balanca_entrega==0) echo "selected=\"selected\""; ?>>Não</option>
                </select>
                <br />
                
                <label for="id_regiao">Região de entrega:</label>
                <select name="id_regiao" id="id_regiao" title="Região">
                    <option value="">- REGIÃO -</option>
                    <?
                    $result_reg= mysql_query("select * from fi_clientes_regioes
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   status_regiao = '1'
                                                order by regiao asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_reg = mysql_fetch_object($result_reg)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_reg->id_regiao; ?>" <? if ($rs_reg->id_regiao==$rs->id_regiao) echo "selected=\"selected\""; ?>><?= $rs_reg->regiao; ?></option>
                    <? $i++; } ?>
                </select>
                <br />
            </div>
        </div>
        
    </fieldset>
    <? } ?>
    
    <fieldset>
        <legend>Dados de Endereçamento</legend>
        
        <div class="parte50">
            <label for="id_uf">* UF:</label>
            <select name="id_uf" id="id_uf" onchange="alteraCidade('id_cidade_atualiza', 'id_uf', 'id_cidade');">
            <option selected="selected" value="">- UF -</option>
            <?
            $result_uf= mysql_query("select * from ufs order by uf ");
            $i=0;
            while ($rs_uf = mysql_fetch_object($result_uf)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_uf->id_uf; ?>"<? if ($rs_uf->id_uf==$rs->id_uf) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
            <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_cidade">* Cidade:</label>
            <div id="id_cidade_atualiza">
              <select  title="Cidade" id="id_cidade" name="id_cidade">
              <option value="" selected="selected">- CIDADE -</option>
              <?
                $result_cid= mysql_query("select * from cidades where id_uf = '". $rs->id_uf ."' order by id_cidade");
				$i=0;
                while($rs_cid= mysql_fetch_object($result_cid)) {
                ?>
              <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?>  value="<?= $rs_cid->id_cidade; ?>"<? if ($rs_cid->id_cidade==$rs->id_cidade) echo "selected=\"selected\""; ?>><?= $rs_cid->cidade; ?></option>
              <? $i++; } ?>
            </select>
            </div>
            <br />
            
            <label for="rua">Rua:</label>
            <input title="Rua" name="rua" id="rua" value="<?= $rs->rua; ?>" />
            <br />
            
            <label for="numero">Número:</label>
            <input name="numero" id="numero" value="<?= $rs->numero; ?>" />
            <br />
            
            <label for="complemento">Complemento:</label>
            <input name="complemento" id="complemento" value="<?= $rs->complemento; ?>" />
            <br />
            
            <label for="bairro">Bairro:</label>
            <input title="Bairro" name="bairro" id="bairro" value="<?= $rs->bairro; ?>" />
            <br />
        </div>
        <div class="parte50">
            <label for="cep">CEP:</label>
            <input title="CEP" name="cep" id="cep" value="<?= $rs->cep; ?>" onkeypress="return formataCampo(form, this.name, '99999-999', event);" maxlength="9" />
            <br />
            
            <? if ($acao=='i') { ?>
            <label for="tel_com">Tel. comercial:</label>
            <input title="Telefone comercial" name="tel_com" id="tel_com" value="<?= $rs->tel_com; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="tel_fax">Fax:</label>
            <input title="Fax" name="tel_fax" id="tel_fax" value="<?= $rs->tel_fax; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="tel_cel">Celular:</label>
            <input name="tel_cel" id="tel_cel" value="<?= $rs->tel_cel; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            <? } else { ?>
            <br />
            <div onmouseover="Tip('Para editar estes dados, vá até o menu Contatos!');">
            <?
				$result_tel= mysql_query("select tel_contatos_telefones.* from tel_contatos, tel_contatos_telefones
										 	where tel_contatos.id_pessoa = '". $rs->id_pessoa ."'
											and   tel_contatos.id_contato = tel_contatos_telefones.id_contato
											");
				while ($rs_tel= mysql_fetch_object($result_tel)) {
			?>
            
            <label>Tel. <?= pega_tipo_telefone($rs_tel->tipo); ?>:</label>
            <?= $rs_tel->telefone; ?>
            <br />
            
            <? } ?>
            </div>
            <br />
            <? } ?>
            
            <label for="email">E-mail:</label>
            <input title="E-mail" name="email" id="email" value="<?= $rs->email; ?>" />
            <br />
            
            <label for="site">Site:</label>
            <input name="site" id="site" value="<?= $rs->site; ?>" />
            <br />
        </div>
    </fieldset>
    
    <? if ($rs->tipo_pessoa=="a") { ?>
    <fieldset>
	    <legend>Logo</legend>
        
        <div class="parte50 screen">
            <label for="foto">Arquivo:</label>
            <input type="file" name="foto" id="foto" />
            <br />
        </div>
        <div class="parte50" id="empresa_logo">
        	<?
			$id_empresa_aqui= pega_id_empresa_da_pessoa($rs->id_pessoa);
			if (file_exists(CAMINHO . "empresa_". $id_empresa_aqui .".jpg")) {
			?>
            <center>
	            <img src="<?= CAMINHO; ?>empresa_<?= $id_empresa_aqui; ?>.jpg" alt="<?= $rs->nome;?>" />
                <br />
                <a href="javascript:ajaxLink('empresa_logo', 'arquivoExcluir&amp;arquivo=empresa_<?= $id_empresa_aqui; ?>.jpg');" onclick="return confirm('Tem certeza que deseja excluir o logo?');">excluir</a>
            </center>
            <? } ?>
            <br /><br />
        </div>
    </fieldset>
    <? } ?>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>