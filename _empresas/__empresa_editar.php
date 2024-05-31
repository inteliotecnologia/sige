<?
if ($_SESSION["tipo_empresa"]=="a") {
	$result= mysql_query("select empresas.*, ufs.* from empresas, cidades, ufs
							where empresas.id_empresa= '". $_GET["id_empresa"] ."'
							and   empresas.id_cidade = cidades.id_cidade
							and   cidades.id_uf = ufs.id_uf
							");

	if (mysql_num_rows($result)>0) {
		$rs= mysql_fetch_object($result);
?>
<h2 class="titulos">Cadastro de empresa</h2>
<div id="setinha2"></div>

<p>Preencha corretamente os campos marcados com * abaixo para cadastrar uma nova empresa:</p>

<form action="<?= AJAX_FORM; ?>formEmpresaEditar" method="post" name="formEmpresaEditar" id="formEmpresaEditar" onsubmit="return ajaxForm('conteudo', 'formEmpresaEditar');">
	<input name="acao" type="hidden" id="acao" value="1" class="escondido" />
	<input name="id_empresa" type="hidden" id="id_empresa" value="<?= $rs->id_empresa; ?>" class="escondido" />
	
    <div class="parte50">
        <label for="nome_fantasia">Nome fantasia:</label>
        <input value="<?= $rs->nome_fantasia; ?>" name="nome_fantasia" id="nome_fantasia" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="razao_social">Razão social:</label>
        <input value="<?= $rs->razao_social; ?>" name="razao_social" id="razao_social" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="cnpj">CNPJ:</label>
        <input value="<?= formata_cnpj($rs->cnpj); ?>" name="cnpj" id="cnpj" onblur="setaClasse(this, 'campo_normal'); verificaCnpj(1);" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaEditar, 'cnpj', '99.999.999/9999-99', event);" maxlength="18" />
        <div id="cnpj_testa" class="lado_campo">
            <input name="passa_cnpj" id="passa_cnpj" type="hidden" class="escondido" value="1" />
            <span id="span_cnpj_testa" class="verde">&nbsp;</span>
        </div>
        <br class="limpa" />
    
        <label for="ie">IE:</label>
        <input value="<?= $rs->ie; ?>" name="ie" id="ie" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="endereco">Endereço:</label>
        <input value="<?= $rs->endereco; ?>" name="endereco" id="endereco" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="bairro">Bairro:</label>
        <input value="<?= $rs->bairro; ?>" name="bairro" id="bairro" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="complemento">Complemento:</label>
        <textarea name="complemento" id="complemento" onfocus="setaClasse(this, 'campo_hover');" onblur="setaClasse(this, 'campo_normal');"><?= $rs->complemento; ?></textarea>
        <br class="limpa" />
        
        <label for="cep">CEP:</label>
        <input value="<?= $rs->cep; ?>" name="cep" id="cep" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaEditar, 'cep', '99999-999', event);" maxlength="9" />
        <div class="lado_campo">
            <span class="vermelho">"99999-999"</span>
        </div>
        <br class="limpa" />
    </div>
    <div class="parte50">
        <label for="id_uf">Estado:</label>
        <select name="id_uf" id="id_uf" onChange="alteraCidade();">
            <?
            $result_uf= mysql_query("select * from ufs order by uf asc ");
            $i=0;
            while($rs_uf= mysql_fetch_object($result_uf)) {
            ?>
            <option <? if ($i==0) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_uf->id_uf; ?>" <? if ($rs_uf->id_uf == $rs->id_uf) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
            <?
            if ($i==2)
                $i=0;
            }
            ?>
        </select>
        <br class="limpa" />
        
        <label for="id_cidade">Cidade:</label>
        <div id="id_cidade_atualiza">
            <select name="id_cidade" id="id_cidade" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');">
                <option value="" selected="selected">--- SELECIONE ---</option>
                <?
                $result_cid= mysql_query("select * from cidades where id_uf = '$rs->id_uf' ");
                $i=0;
                while($rs_cid= mysql_fetch_object($result_cid)) {
                ?>
                <option <? if ($i==0) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs_cid->id_cidade; ?>" <? if ($rs_cid->id_cidade == $rs->id_cidade) echo "selected=\"selected\""; ?>><?= $rs_cid->cidade; ?></option>
                <?
                if ($i==2)
                    $i=0;
                }
                ?>
            </select>
        </div>
        <br class="limpa" />
        
        <label for="contato">Contato:</label>
        <input value="<?= $rs->contato; ?>" name="contato" id="contato" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="tel_com">Tel. comercial:</label>
        <input value="<?= $rs->tel_com; ?>" name="tel_com" id="tel_com" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaEditar, 'tel_com', '(99) 9999-9999', event);" maxlength="14" />
        <div class="lado_campo">
            <span class="vermelho">"(99) 9999-999"</span>
        </div>
        <br class="limpa" />
        
        <label for="tel_com">Ramal:</label>
        <input value="<?= $rs->tel_com; ?>" name="ramal" id="ramal" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="tel_cel">Tel. celular:</label>
        <input value="<?= $rs->tel_cel; ?>" name="tel_cel" id="tel_cel" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaEditar, 'tel_cel', '(99) 9999-9999', event);" maxlength="14" />
        <div class="lado_campo">
            <span class="vermelho">"(99) 9999-999"</span>
        </div>
        <br class="limpa" />
            
        <label for="email">E-mail:</label>
        <input value="<?= $rs->email; ?>" name="email" id="email" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="site">Site:</label>
        <input value="<?= $rs->site; ?>" name="site" id="site" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="agua">Permissão:</label>
        <input type="checkbox" name="os" id="os" <? if (pode('o', $rs->permissao)) echo "checked=\"checked\""; ?> value="o" class="tamanho30" /> <label for="os" class="tamanho30 nao_negrito alinhar_esquerda">O.S.</label>
        <input type="checkbox" name="agua" id="agua" <? if (pode('a', $rs->permissao)) echo "checked=\"checked\""; ?> value="a" class="tamanho30" /> <label for="agua" class="tamanho30 nao_negrito alinhar_esquerda">Água</label>
        <input type="checkbox" name="fluxo" id="fluxo" <? if (pode('f', $rs->permissao)) echo "checked=\"checked\""; ?> value="f" class="tamanho30" /> <label for="fluxo" class=" tamanho80 nao_negrito alinhar_esquerda">Fluxo Laminar</label>
        <br class="limpa" />
	</div>
    <br class="limpa" /><br class="limpa" />
    
	<center>
        <label for="enviar">&nbsp;</label>
        <button type="submit" id="enviar">enviar &gt;&gt;</button>
    </center>
</form>
<? } } ?>