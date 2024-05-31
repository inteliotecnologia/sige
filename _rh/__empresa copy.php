<?
require_once("conexao.php");
if (pode_algum("ap", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *, DATE_FORMAT(data, '%d/%m/%Y') as data2
								from  pessoas, empresas, rh_enderecos, cidades, ufs
								where pessoas.tipo = 'j'
								and   pessoas.id_pessoa = empresas.id_pessoa
								and   pessoas.id_pessoa = rh_enderecos.id_pessoa
								and   empresas.id_empresa = '". $_GET["id_empresa"] ."'
								and   rh_enderecos.id_cidade = cidades.id_cidade
								and   cidades.id_uf = ufs.id_uf
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		$tipo_empresa= $rs->tipo_empresa;
		$tit= "Edição de ";
	} else $tit= "Cadastro de ";
	
	
	
	if ( (pode("pi", $_SESSION["permissao"])) && ($_GET["tipo_empresa"]=="c") ) {
		$tipo_empresa= "c";
		$tit.= "cliente";
	}
	elseif ( (pode("i", $_SESSION["permissao"])) && ($_GET["tipo_empresa"]=="f") ) {
		$tipo_empresa= "f";
		$tit.= "fornecedor";
	}
	elseif (pode("a", $_SESSION["permissao"])) {
		$tipo_empresa= "a";
		$tit.= "empresa com acesso ao sistema";
	}
	
?>
<h2><?= $tit; ?></h2>

<p>* Campos de preenchimento obrigatório.</p>

<form action="<?= AJAX_FORM; ?>formEmpresa&amp;acao=<?= $acao; ?>" method="post" name="formEmpresa" id="formEmpresa" onsubmit="return ajaxForm('conteudo', 'formEmpresa', 'validacoes', true);">
    
    <input class="escondido" type="hidden" id="validacoes" value="tipo_empresa@vazio|razao_social@vazio|nome_fantasia@vazio|passa_cnpj@vazio@cnpj|ie@vazio|contato@vazio|id_cidade@vazio|rua@vazio|bairro@vazio|cep@vazio|tel_com@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_empresa" class="escondido" type="hidden" id="id_empresa" value="<?= $rs->id_empresa; ?>" />
    <? } ?>
    <input name="tipo_empresa" class="escondido" type="hidden" id="tipo_empresa" value="<?= $tipo_empresa; ?>" />
    
    <fieldset>
        <legend>Seleção de tipo</legend>
        
        <label for="tipo">* Tipo:</label>
        <select name="tipo" id="tipo" onchange="alteraTipoPessoa('id_cidade_atualiza', 'id_uf', 'id_cidade');">
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
        
    </fieldset>
    
    <fieldset>
        <legend>Dados da Empresa</legend>
        
        <div class="parte50">
            <label for="razao_social">* Razão Social:</label>
            <input title="Razão Social" name="razao_social" id="razao_social" value="<?= $rs->nome_rz; ?>" />
            <br />		
            
            <label for="nome_fantasia">* Nome Fantasia:</label>
            <input title="Nome Fantasia" name="nome_fantasia" value="<?= $rs->apelido_fantasia; ?>" id="nome_fantasia" />
            <br />
            
            <label for="cnpj">* CNPJ:</label>
            <input title="CNPJ" name="cnpj" id="cnpj" onblur="verificaCnpj('<?=$acao;?>');" value="<?= $rs->cpf_cnpj; ?>" onkeypress="return formataCampo(form, this.name, '99.999.999/9999-99', event);" maxlength="18" />
			<br />
            
            <label>&nbsp;</label>
            <div id="cnpj_testa" class="lado_campo">
                <input title="CNPJ" name="passa_cnpj" id="passa_cnpj" type="hidden" class="escondido" value="" />
                <span id="span_cnpj_testa" class="vermelho">Não testado!</span>
            </div>
            <br />
            
            <script language="javascript">
				verificaCnpj('<?=$acao;?>');
			</script>
            
            <? if ($tipo_empresa=="c") { ?>
            <label for="codigo">Código de identificação:</label>
            <input title="Código de identificação" name="codigo" id="codigo" value="<?= $rs->codigo; ?>" />
            <br />
            <? } ?>
            
            <label for="sigla">Sigla:</label>
            <input title="Sigla" name="sigla" id="sigla" value="<?= $rs->sigla; ?>" />
            <br />
        </div>
        <div class="parte50">
            <label for="ie">* Inscr. Estadual:</label>
            <input title="Inscrição Estadual" name="ie" id="ie" value="<?= $rs->rg_ie; ?>" />
            <br />
            
            <label for="contato">* Contato:</label>
            <input title="Contato" name="contato" value="<?= $rs->contato; ?>" id="contato" />
            <br />
            
            <? /*
            <label for="data">Data de fundação:</label>
            <input title="Data de fundação" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" name="data" id="data" onkeyup="formataData(this);" value="<?= $rs->data2; ?>" maxlength="10" /> 
            <br /> 
			*/ ?>
        </div>
    </fieldset>
    
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
            
            <label for="id_cidade">Cidade:</label>
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
            
            <label for="rua">* Rua:</label>
            <input title="Rua" name="rua" id="rua" value="<?= $rs->rua; ?>" />
            <br />
            
            <label for="numero">Numero:</label>
            <input name="numero" id="numero" value="<?= $rs->numero; ?>" />
            <br />
            
            <label for="complemento">Complemento:</label>
            <input name="complemento" id="complemento" value="<?= $rs->complemento; ?>" />
            <br />
            
            <label for="bairro">* Bairro:</label>
            <input title="Bairro" name="bairro" id="bairro" value="<?= $rs->bairro; ?>" />
            <br />
        </div>
        <div class="parte50">
            <label for="cep">* CEP:</label>
            <input title="CEP" name="cep" id="cep" value="<?= $rs->cep; ?>" onkeypress="return formataCampo(form, this.name, '99999-999', event);" maxlength="9" />
            <br />
            
            <label for="tel_com">* Tel. comercial:</label>
            <input title="Telefone comercial" name="tel_com" id="tel_com" value="<?= $rs->tel_com; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="tel_fax">Fax:</label>
            <input title="Fax" name="tel_fax" id="tel_fax" value="<?= $rs->tel_fax; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="tel_cel">Celular:</label>
            <input name="tel_cel" id="tel_cel" value="<?= $rs->tel_cel; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="email">E-mail:</label>
            <input title="E-mail" name="email" id="email" value="<?= $rs->email; ?>" />
            <br />
            
            <label for="site">Site:</label>
            <input name="site" id="site" value="<?= $rs->site; ?>" />
            <br />
        </div>
    </fieldset>
            
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>