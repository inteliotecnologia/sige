<?
if ($_SESSION["tipo_empresa"]=="a") {
?>
<h2 class="titulos">Cadastro de empresa</h2>

<p>Preencha corretamente os campos marcados com * abaixo para cadastrar uma nova empresa:</p>

<center>
	<div id="form_erro" class="vermelho">
	<?
	if ((!isset($ok0)) && (!isset($ok1)))
		echo "<br />";
	else {
		if (isset($ok0))
			echo "Não foi possível cadastrar, tente novamente ou entre em contato com o suporte!";
		else
			echo "Mensagem enviada com sucesso! Obrigado por entrar em contato!";
	} ?>
	</div>
</center>

<br />

	
<form action="<?= AJAX_FORM; ?>formEmpresaInserir" method="post" name="formEmpresaInserir" id="formEmpresaInserir" onsubmit="return ajaxForm('conteudo', 'formEmpresaInserir');">
	<input name="acao" type="hidden" id="acao" value="1" class="escondido" />

	<div class="parte50">
        <label for="nome_fantasia">Nome fantasia:</label>
        <input name="nome_fantasia" id="nome_fantasia" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="razao_social">Razão social:</label>
        <input name="razao_social" id="razao_social" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="cnpj">CNPJ:</label>
        <input name="cnpj" id="cnpj" onblur="setaClasse(this, 'campo_normal'); verificaCnpj(0);" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaInserir, 'cnpj', '99.999.999/9999-99', event);" maxlength="18" />
        <div id="cnpj_testa" class="lado_campo">
            <input name="passa_cnpj" id="passa_cnpj" type="hidden" class="escondido" value="" />
            <span id="span_cnpj_testa" class="vermelho">Não testado!</span>
        </div>
        <br class="limpa" />
    
        <label for="ie">IE:</label>
        <input name="ie" id="ie" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="endereco">Endereço:</label>
        <input name="endereco" id="endereco" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="bairro">Bairro:</label>
        <input name="bairro" id="bairro" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="complemento">Complemento:</label>
        <textarea name="complemento" id="complemento" onfocus="setaClasse(this, 'campo_hover');" onblur="setaClasse(this, 'campo_normal');"></textarea>
        <br class="limpa" />
        
        <label for="cep">CEP:</label>
        <input name="cep" id="cep" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaInserir, 'cep', '99999-999', event);" maxlength="9" />
        <div class="lado_campo">
            <span class="vermelho">"99999-999"</span>
        </div>
        <br class="limpa" />
    </div>
    <div class="parte50">
        <label for="id_uf">Estado:</label>
        <select name="id_uf" id="id_uf" onChange="alteraCidade();">
            <?
            $id_uf_padrao= 24;
            $result= mysql_query("select * from ufs order by uf asc ");
            $i=0;
            while($rs= mysql_fetch_object($result)) {
            ?>
            <option <? if ($i==0) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs->id_uf; ?>" <? if ($rs->id_uf == $id_uf_padrao) echo "selected"; ?>><?= $rs->uf; ?></option>
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
                $result= mysql_query("select * from cidades where id_uf = '$id_uf_padrao' ");
                $i=0;
                while($rs= mysql_fetch_object($result)) {
                ?>
                <option <? if ($i==0) echo "class=\"cor_sim\""; $i++; ?> value="<?= $rs->id_cidade; ?>" <? //if ($rs->id_area == $id_area) echo "selected"; ?>><?= $rs->cidade; ?></option>
                <?
                if ($i==2)
                    $i=0;
                }
                ?>
            </select>
        </div>
        <br class="limpa" />
        
        <label for="contato">Contato:</label>
        <input name="contato" id="contato" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="tel_com">Tel. comercial:</label>
        <input name="tel_com" id="tel_com" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaInserir, 'tel_com', '(99) 9999-9999', event);" maxlength="14" />
        <div class="lado_campo">
            <span class="vermelho">"(99) 9999-999"</span>
        </div>
        <br class="limpa" />
        
        <label for="tel_com">Ramal:</label>
        <input name="ramal" id="ramal" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
    
        <label for="tel_cel">Tel. celular:</label>
        <input name="tel_cel" id="tel_cel" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" onkeypress="return formataCampo(formEmpresaInserir, 'tel_cel', '(99) 9999-9999', event);" maxlength="14" />
        <div class="lado_campo">
            <span class="vermelho">"(99) 9999-999"</span>
        </div>
        <br class="limpa" />
            
        <label for="email">E-mail:</label>
        <input name="email" id="email" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="site">Site:</label>
        <input name="site" id="site" onblur="setaClasse(this, 'campo_normal');" onfocus="setaClasse(this, 'campo_hover');" />
        <br class="limpa" />
        
        <label for="agua">Permissão:</label>
        <input type="checkbox" name="os" id="os" value="o" class="tamanho30" /> <label for="os" class=" tamanho80 nao_negrito alinhar_esquerda">O.S.</label>
        <input type="checkbox" name="agua" id="agua" value="a" class="tamanho30" /> <label for="agua" class="tamanho30 nao_negrito alinhar_esquerda">Água</label>
        <input type="checkbox" name="fluxo" id="fluxo" value="f" class="tamanho30" /> <label for="fluxo" class=" tamanho80 nao_negrito alinhar_esquerda">Fluxo Laminar</label>
        
        <br class="limpa" />
        
	</div>
    <br class="limpa" /><br class="limpa" />
    
    <center>
        <label for="enviar">&nbsp;</label>
        <button type="submit" id="enviar">enviar &gt;&gt;</button>
    </center>
    
</form>
<? } ?>