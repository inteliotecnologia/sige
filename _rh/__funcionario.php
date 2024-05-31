<?
require_once("conexao.php");
if (pode("rv4", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *, DATE_FORMAT(pessoas.data, '%d/%m/%Y') as data_nasc2,
								DATE_FORMAT(rh_funcionarios.data_exp_rg, '%d/%m/%Y') as data_exp_rg2,
								DATE_FORMAT(rh_funcionarios.data_pis, '%d/%m/%Y') as data_pis2,
								DATE_FORMAT(rh_funcionarios.data_exp_ctps, '%d/%m/%Y') as data_exp_ctps2,
								DATE_FORMAT(rh_funcionarios.validade_cnh, '%d/%m/%Y') as validade_cnh2,
								DATE_FORMAT(rh_carreiras.data, '%d/%m/%Y') as data_carreira
								
								from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras
								where pessoas.id_pessoa = rh_funcionarios.id_pessoa
								and   rh_funcionarios.id_funcionario =  '". $_GET["id_funcionario"] ."'
								and   rh_enderecos.id_pessoa = pessoas.id_pessoa
								and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
								and   rh_carreiras.atual = '1'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		if (mysql_num_rows($result)==0) {
			echo "Complete os dados da carreira do funcionário para visualizar os dados!";
			die();
		}
	}
?>

<? if ($acao=='i') { ?>
<h2>Inserção de funcionário</h2>
<? } ?>

<form action="<?= AJAX_FORM; ?>formFuncionario&amp;acao=<?= $acao; ?>" enctype="multipart/form-data" method="post" name="formFuncionario" id="formFuncionario" onsubmit="return validaFormNormal('validacoes', true);">
    
	<input class="escondido" type="hidden" id="validacoes" name="validacoes" value="id_departamento@vazio|id_turno@vazio@cnpj|id_cargo@vazio|data_admissao@data_passada|nome@vazio|data_nasc@data_passada|passa_cpf@vazio|id_cidade@vazio" />
    <? if ($acao=='e') { ?>
    <input name="id_funcionario" class="escondido" type="hidden" id="id_funcionario" value="<?= $rs->id_funcionario; ?>" />
    <input name="id_pessoa" class="escondido" type="hidden" id="id_pessoa" value="<?= $rs->id_pessoa; ?>" />
    <? } ?>
    <input name="tipo_pessoa" class="escondido" type="hidden" id="tipo_pessoa" value="u" />
		
    <fieldset>
        <legend>Empresa</legend>
        
        <div class="parte50">
        
        	<label for="id_empresa_rel">Empresa padrão:</label>
            <select name="id_empresa_rel" id="id_empresa_rel" title="Empresa">
                <option selected="selected" value="">- NENHUMA -</option>
                <?
                $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
											where pessoas.id_pessoa = empresas.id_pessoa
											and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
											and   pessoas_tipos.tipo_pessoa = 'a'
											order by 
											pessoas.nome_rz asc");
                $i=0;
                while ($rs_emp = mysql_fetch_object($result_emp)) {
                ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"<? if ($rs_emp->id_empresa==$rs->id_empresa_rel) echo "selected=\"selected\""; ?>><?= $rs_emp->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        	
            <label for="id_departamento">* Departamento:</label>
            <? if ($acao=='e') { ?>
            <?= pega_departamento($rs->id_departamento); ?>
            <input type="hidden" class="escondido" name="id_departamento" id="id_departamento" title="Departamento" value="<?= $rs->id_departamento; ?>" />
            <? } else { ?>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento" onchange="alteraTurnos(); alteraCargos();">
                    <option value="">- DEPARTAMENTO -</option>
                    <?
                    $result_dep= mysql_query("select * from rh_departamentos
                                                where id_empresa = '". $_SESSION["id_empresa"] ."'
                                                 ");
                    $i=0;
                    while ($rs_dep = mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>" <? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            
            <label for="id_cargo">* Cargo:</label>
            <? if ($acao=='e') { ?>
            <?= pega_cargo($rs->id_cargo); ?>
            <input type="hidden" class="escondido" name="id_cargo" id="id_cargo" title="Cargo" value="<?= $rs->id_cargo; ?>" />
            <? } else { ?>
            <div id="id_cargo_atualiza">
                <select name="id_cargo" id="id_cargo" title="Cargo" <? if ($acao=='e') echo "disabled=\"disabled\"" ?>>	  		
                    <option value="">- CARGO -</option>
                    <?
                    $result_car= mysql_query("select * from rh_cargos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_car = mysql_fetch_object($result_car)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_car->id_cargo; ?>" <? if ($rs_car->id_cargo==$rs->id_cargo) echo "selected=\"selected\""; ?>><?= $rs_car->cargo; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            
            <label for="id_turno">* Turno:</label>
            <? if ($acao=='e') { ?>
            <?= pega_turno($rs->id_turno); ?>
            <input type="hidden" class="escondido" name="id_turno" id="id_turno" title="Turno" value="<?= $rs->id_turno; ?>" />
            <? } else { ?>
            <div id="id_turno_atualiza">
                <select name="id_turno" id="id_turno" title="Turno" onchange="alteraIntervalos();">	  		
                    <option value="">- TURNO -</option>
                    <?
                    $result_tur= mysql_query("select * from rh_turnos
                                                where id_departamento = '". $rs->id_departamento ."'
                                                 ");
                    $i=0;
                    while ($rs_tur = mysql_fetch_object($result_tur)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_tur->id_turno; ?>" <? if ($rs_tur->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_tur->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            
            <label for="id_intervalo">* Intervalo:</label>
            <? if ($acao=='e') { ?>
            <?= pega_intervalo($rs->id_intervalo); ?>
            <input type="hidden" class="escondido" name="id_intervalo" id="id_intervalo" title="Intervalo" value="<?= $rs->id_intervalo; ?>" />
            <? } else { ?>
            <div id="id_intervalo_atualiza">
                <select name="id_intervalo" id="id_intervalo" title="Intervalo">	  		
                    <option value="">- INTERVALO -</option>
                    <?
                    $result_int= mysql_query("select * from rh_turnos_intervalos
                                                where id_turno = '". $rs->id_turno ."'
                                                 ");
                    $i=0;
                    while ($rs_int = mysql_fetch_object($result_int)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_int->id_turno; ?>" <? if ($rs_int->id_turno==$rs->id_turno) echo "selected=\"selected\""; ?>><?= $rs_int->turno; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            
        </div>
        <div class="parte50">
            <label for="turnante">* Turnante:</label>
			<? if ($acao=='e') { ?>
            <?= sim_nao($rs->turnante); ?>
            <? } else { ?>
            <input type="radio" class="tamanho15" name="turnante" id="turnante_1" value="1" /> <label class="tamanho50 nao_negrito alinhar_esquerda" for="turnante_1">Sim</label>
            <input type="radio" class="tamanho15" name="turnante" id="turnante_2" value="2" <? if ($acao=='i') echo "checked=\"checked\""; ?> /> <label class="tamanho50 nao_negrito alinhar_esquerda" for="turnante_2">Não</label>
            <? } ?>
            <br />
            
            <? if ($acao=='e') { ?>
            <label>Cartão:</label>
            <?= pega_cartao_do_funcionario($rs->id_funcionario); ?>
            <br />
            <? } ?>
            
            <label for="data_admissao">* Data de admissão:</label>
            <?
            if ($acao=='e') {
				$data_admissao= pega_data_admissao($rs->id_funcionario);
				echo $data_admissao;
			?>
            <input type="hidden" class="escondido" name="data_admissao" id="data_admissao" value="<?= $data_admissao; ?>" />
            <? } else { ?>
            <input name="data_admissao" id="data_admissao" onfocus="displayCalendar(this, 'dd/mm/yyyy', this);" onkeyup="formataData(this);" value="<?= $rs->data_admissao2; ?>" maxlength="10" />
            <? } ?>
            <br />
            
            <? if ($acao=='e') { ?>
            <label>Matrícula (empresa):</label>
            <input name="num_func" id="num_func" value="<?=$rs->num_func;?>" class="tamanho25p" />
            <br />
            <? } ?>
            
            <label for="matr_cont">Matrícula (contabilidade):</label>
            <input name="matr_cont" id="matr_cont" value="<?=$rs->matr_cont;?>" class="tamanho25p" />
            <br />
            
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Dados Pessoais</legend>
        
        <div class="parte50">
            <label for="nome">* Nome:</label>
            <input name="nome" id="nome" title="Nome" value="<?= $rs->nome_rz; ?>" />
            <br />		
            
            <label for="apelido">Apelido:</label>
            <input name="apelido" id="apelido" value="<?= $rs->apelido_fantasia; ?>" />
            <br />
            
            <label for="estado_civil">Estado Civil:</label>
            <select name="estado_civil" id="estado_civil" title="Estado civil">
                <option value="">- SELECIONE -</option> 
                <?
                $vetor= pega_estado_civil('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->estado_civil==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_uf_naturalidade">Naturalidade:</label>
            <select name="id_uf_naturalidade" id="id_uf_naturalidade" onchange="alteraCidade('id_cidade_naturalidade_atualiza', 'id_uf_naturalidade', 'id_cidade_naturalidade');">
            <option selected="selected">- UF -</option>
            <?
            $result_uf= mysql_query("select * from ufs order by id_uf");
			$i=0;
            while ($rs_uf = mysql_fetch_object($result_uf)) {
            ?>
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?>  value="<?= $rs_uf->id_uf; ?>"<? if ($rs_uf->id_uf==pega_id_uf($rs->naturalidade_id_cid)) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
            <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_cidade_naturalidade">* Cidade:</label>
            <div id="id_cidade_naturalidade_atualiza">
              <select id="id_cidade_naturalidade" name="id_cidade_naturalidade" title="Naturalidade">
              <option value="" selected>- CIDADE -</option>
              <?
                $result_mo= mysql_query("select * from cidades where id_uf = '". pega_id_uf($rs->naturalidade_id_cid) ."' order by cidade asc ");
				$i= 0;
                while($rs_mo= mysql_fetch_object($result_mo)) {
                ?>
              <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?>   value="<?= $rs_mo->id_cidade; ?>"<? if ($rs_mo->id_cidade==$rs->naturalidade_id_cid) echo "selected"; ?>><?= $rs_mo->cidade; ?></option>
              <? $i++; } ?>
            </select>
            </div>
            <br />
        </div>
        <div class="parte50">
            <label for="sexo">Sexo:</label> 
            <select name="sexo" id="sexo" title="Sexo"> 
                <option selected="selected" value="">- SELECIONE -</option> 
                <option value="m" <? if ($rs->sexo=='m') echo "selected=\"selected\""; ?> class="cor_sim">Masculino</option>
                <option value="f" <? if ($rs->sexo=='f') echo "selected=\"selected\""; ?>>Feminino</option>
            </select>
            <br />
            
            <label for="data_nasc">* Data de nasc.:</label>
            <input title="Data de nascimento" name="data_nasc" id="data_nasc" onkeyup="formataData(this);" value="<?= $rs->data_nasc2; ?>" maxlength="10" /> 
            <br />
            
            <label for="nomepai">Nome do pai:</label> 
            <input name="nomepai" id="nomepai" value="<?= $rs->nomepai; ?>" /> 
            <br />
            
            <label for="nomemae">Nome da mãe:</label>  
            <input name="nomemae" id="nomemae" value="<?= $rs->nomemae; ?>" /> 
            <br />
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Documentos</legend>
    	
        <div class="parte50">
        
            <label for="rg">RG:</label>
            <input title="RG" name="rg" id="rg" value="<?= $rs->rg_ie; ?>" />
            <br />
            
            <label for="orgrg">Órg. expeditor:</label>  
            <input name="orgrg" id="orgrg" title="Órgão expeditor" value="<?= $rs->org_exp_rg; ?>" />
            <br />
            
            <label for="rg_id_uf">UF expeditor:</label>  
            <select name="rg_id_uf" id="rg_id_uf" title="UF (órgão expeditor do RG)">
                <option selected="selected">- UF -</option>
                <?
                $result_uf= mysql_query("select * from ufs order by id_uf");
				$i=0;
                while ($rs_uf = mysql_fetch_object($result_uf)) {
                ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_uf->id_uf; ?>"<? if ($rs_uf->id_uf==$rs->uf_rg) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <?
			if ($rs->data_exp_rg2=="00/00/0000") $data_exp_rg2="";
			else  $data_exp_rg2= $rs->data_exp_rg2;
			?>
            <label for="data_exp_rg">Data exp. RG:</label>
            <input title="Data de expedição do RG" name="data_exp_rg" id="data_exp_rg" onkeyup="formataData(this);" value="<?= $data_exp_rg2; ?>" maxlength="10" /> 
            <br />
                
            <label for="cpf">* CPF:</label>
            <input title="CPF" name="cpf" id="cpf" onblur="verificaCpf('<?=$acao;?>');" value="<?= $rs->cpf_cnpj; ?>" onkeypress="return formataCampo(form, this.name, '999.999.999-99', event);" maxlength="14" />
            <br />
            
            <label>&nbsp;</label>
            <div id="cpf_testa" class="lado_campo">
                <input title="CPF" name="passa_cpf" id="passa_cpf" type="hidden" class="escondido" value="" />
                <span id="span_cpf_testa" class="vermelho">Não testado!</span>
            </div>
            <br />
            
            <script language="javascript">
				verificaCpf('<?=$acao;?>');
			</script>
            
            <label for="pis">PIS/PASEP:</label>
            <input title="PIS/PASEP" name="pis" id="pis" value="<?= $rs->pis; ?>" />
            <br />
            
            <?
			if ($rs->data_pis2=="00/00/0000") $data_cad_pis2="";
			else  $data_cad_pis2= $rs->data_pis2;
			?>
            <label for="data_cad_pis">Data Cad. PIS:</label>
            <input title="Data de cadastro no PIS" name="data_cad_pis" id="data_cad_pis" onkeyup="formataData(this);" value="<?= $data_cad_pis2; ?>" maxlength="10" /> 
            <br />
        </div>
        <div class="parte50">
            <label for="ctps">CTPS:</label>
            <input title="CTPS" name="ctps" id="ctps" value="<?= $rs->ctps; ?>" />
            <br />
            
            <label for="serie_ctps">Série:</label>
            <input title="Série (CTPS)" name="serie_ctps" id="serie_ctps" value="<?= $rs->serie_ctps; ?>" />
            <br />
            
            <label for="ctps_id_uf">UF:</label>
            <select title="UF (CTPS)" name="ctps_id_uf" id="ctps_id_uf">
                <option selected="selected" value="">- UF -</option>
                <?
                $result_uf= mysql_query("select * from ufs order by id_uf");
				$i=0;
                while ($rs_uf = mysql_fetch_object($result_uf)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_uf->id_uf; ?>"<? if ($rs_uf->id_uf==$rs->id_uf_ctps) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
                <? $i++;} ?>
            </select>
            <br />
            
            <?
			if ($rs->data_exp_ctps2=="00/00/0000") $data_exp_ctps2="";
			else  $data_exp_ctps2= $rs->data_exp_ctps2;
			?>
            <label for="data_exp_ctps">Data Exp. CTPS:</label>
            <input title="Data de expedição (CTPS)" name="data_exp_ctps" id="data_exp_ctps" onkeyup="formataData(this);" value="<?= $data_exp_ctps2; ?>" maxlength="10" /> 
            <br />
            
            <label for="cnh">CNH:</label>
            <input name="cnh" id="cnh" value="<?= $rs->cnh; ?>" />
            <br />
            
            <?
			if ($rs->validade_cnh2=="00/00/0000") $validade_cnh2="";
			else  $validade_cnh2= $rs->validade_cnh2;
			?>
            <label for="cnh_validade">Validade CNH:</label>
            <input title="Data de validade da CNH" name="cnh_validade" id="cnh_validade" onkeyup="formataData(this);" value="<?= $validade_cnh2; ?>" maxlength="10" /> 
            <br /><br />

			<label for="escolaridade">Escolaridade:</label>
            <select name="escolaridade" id="escolaridade" title="Escolaridade">
                <option value="">- SELECIONE -</option> 
                <?
                $vetor= pega_escolaridade('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->escolaridade==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />

        </div>
    </fieldset>
    
    <fieldset>
        <legend>Endereçamento</legend>
        
        <div class="parte50">        
            <label for="id_uf">* UF:</label>
            <select name="id_uf" id="id_uf" onchange="alteraCidade('id_cidade_atualiza', 'id_uf', 'id_cidade');">
            <option value="">- UF -</option>
            <?
                $result_uf= mysql_query("select * from ufs order by id_uf");
				$i=0;
                while ($rs_uf = mysql_fetch_object($result_uf)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_uf->id_uf; ?>"<? if ($rs_uf->id_uf==pega_id_uf($rs->id_cidade)) echo "selected=\"selected\""; ?>><?= $rs_uf->uf; ?></option>
            <? $i++; } ?>
            </select>
            <br />
            
            <label for="id_cidade">* Cidade:</label>
            <div id="id_cidade_atualiza">
            <select id="id_cidade" name="id_cidade" title="Cidade">
                <option value="">- CIDADE -</option>
                <?
                $result_mo= mysql_query("select * from cidades where id_uf = '". pega_id_uf($rs->id_cidade) ."' order by id_cidade");
				$i=0;
                while($rs_mo= mysql_fetch_object($result_mo)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_mo->id_cidade; ?>"<? if ($rs_mo->id_cidade==$rs->id_cidade) echo "selected=\"selected\""; ?>><?= $rs_mo->cidade; ?></option>
                <? $i++; } ?>
            </select>
            </div>
            <br />
            
            <label for="rua">Rua:</label>
            <input title="Rua" name="rua" id="rua" value="<?= $rs->rua; ?>" />
            <br />
            
            <label for="numero">Numero:</label>
            <input class="tamanho15p" name="numero" id="numero" value="<?= $rs->numero; ?>" />
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
            <input class="tamanho15p" title="CEP" name="cep" id="cep" value="<?= $rs->cep; ?>" onkeypress="return formataCampo(form, this.name, '99999-999', event);" maxlength="9" />
            <br />
            
            <? if ($acao=='i') { ?>
            <label for="tel_res">Tel. residencial:</label>
            <input name="tel_res" id="tel_res" value="<?= $rs->tel_res; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            
            <label for="tel_cel">Tel. celular:</label>
            <input name="tel_cel" id="tel_cel" value="<?= $rs->tel_cel; ?>" onkeypress="return formataCampo(form, this.name, '(99) 9999-9999', event);" maxlength="14" />
            <br />
            <? } else { ?>
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
            <? } ?>
            
            <label for="email">E-mail:</label>
            <input name="email" id="email" value="<?= $rs->email; ?>" />
            <br />
            
            <label for="site">Site:</label>
            <input name="site" id="site" value="<?= $rs->site; ?>" />
            <br />
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Dados bancários</legend>
        
        <div class="parte50">        
        	
            <label for="id_banco">Banco:</label>
            <select id="id_banco" name="id_banco" title="Banco">
                <option value="">- SELECIONE -</option>
                <?
                $result_bc= mysql_query("select * from rh_bancos order by banco asc ");
				$i=0;
                while($rs_bc= mysql_fetch_object($result_bc)) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_bc->id_banco; ?>"<? if ($rs_bc->id_banco==$rs->id_banco) echo "selected=\"selected\""; ?>><?= $rs_bc->id_banco .". ". $rs_bc->banco; ?></option>
                <? $i++; } ?>
            </select>
            <br />
            
            <label for="agencia">Agência:</label>
            <input title="Agência" class="tamanho15p" name="agencia" id="agencia" value="<?= $rs->agencia; ?>" />
            <br />
            
        </div>
        <div class="parte50">
            <label for="conta">Conta (c/ DIV):</label>
            <input title="Conta" class="tamanho15p" name="conta" id="conta" value="<?= $rs->conta; ?>" />
            <br />
            
        	<label for="operacao">Operação:</label>
            <input title="Operação" class="tamanho15p" name="operacao" id="operacao" value="<?= $rs->operacao; ?>" />
            <br />
            
        </div>
        
    </fieldset>
    
    <fieldset>
        <legend>Filhos</legend>
        
        <div id="filhos">
            
			<?
			$result_filhos= mysql_query("select * from rh_funcionarios_filhos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_funcionario = '". $rs->id_funcionario ."'
											order by id_filho asc
											");
            while ($rs_filhos= mysql_fetch_object($result_filhos)) {
			?>
            <div id="div_filho_<?=$i;?>">
                <div class="parte33">
                    <code class="escondido"></code>
                    <label for="nome_filho_<?=$i;?>">Nome:</label>
                    <input class="" title="Filho" name="nome_filho[]" id="nome_filho_<?=$i;?>" value="<?=$rs_filhos->nome_filho;?>" /><br />
                    
                    <label for="sexo_filho_<?=$i;?>">Sexo:</label>
                    <select class="" name="sexo_filho[]" id="sexo_filho_<?=$i;?>">
                        <option value="m" <? if ($rs_filhos->sexo_filho=="m") echo "selected=\"selected\""; ?>>Masculino</option>
                        <option value="f" <? if ($rs_filhos->sexo_filho=="f") echo "selected=\"selected\""; ?> class="cor_sim">Feminino</option>
                    </select>
                    <br />
                    
                    <label for="data_nasc_filho_<?=$i;?>">Data de nascimento:</label>
                    <input class="" value="<?= desformata_data($rs_filhos->data_nasc_filho); ?>" title="Data de nascimento" onkeyup="formataData(this);" maxlenght="10" name="data_nasc_filho[]" id="data_nasc_filho_<?=$i;?>" />
                    <br />
                    
                    <label>&nbsp;</label>
                    <a href="javascript:void(0);" onclick="removeDiv('filhos', 'div_filho_<?=$i;?>');">remover</a>
                    <br /><br />
                </div>
            </div>
            <? } ?>
        </div>
        <br />
        
        <label>&nbsp;</label>
        <a href="javascript:void(0);" onclick="criaEspacoFilho();">novo filho</a>
        <br />
        
    </fieldset>
    <br />
    
    <fieldset>
        <legend>Outros</legend>
        
        <div class="parte50">
            <label for="tamanho_uniforme">Uniforme:</label>
            <select title="Uniforme" name="tamanho_uniforme" id="tamanho_uniforme">
            <option value="">- SELECIONE -</option> 
                <?
                $vetor= pega_tamanho_uniforme('l');
                $i=1;
                while ($vetor[$i]) {
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?=$i;?>" <? if ($rs->tamanho_uniforme==$i) echo "selected=\"selected\""; ?>><?= $vetor[$i]; ?></option>
                <? $i++; } ?>
            </select>
            <br />
        </div>
        <div class="parte50">
            <label for="numero_calcado">Nº calçado:</label>
            <input title="Nº calçado" class="tamanho15p" name="numero_calcado" id="numero_calcado" value="<?= $rs->numero_calcado; ?>" />
            <br />
        </div>
        
    </fieldset>
    
    <fieldset>
	    <legend>Foto</legend>
        
        <div class="parte50 screen">
            <label for="foto">Arquivo da foto:</label>
            <input type="file" name="foto" id="foto" />
            <br />
        </div>
        <div class="parte50" id="funcionario_foto">
        	<?
			if (file_exists(CAMINHO . "pessoa_". $rs->id_pessoa .".jpg")) {
			?>
            <center>
	            <img src="includes/phpthumb/phpThumb.php?src=pessoa_<?= $rs->id_pessoa; ?>.jpg&amp;w=300&amp;zc=1&amp;far=T" alt="<?= $rs->nome;?>" width="300" />
                <br />
                <? if (pode("rv", $_SESSION["permissao"])) { ?>
                <a href="javascript:ajaxLink('funcionario_foto', 'arquivoExcluir&amp;arquivo=pessoa_<?= $rs->id_pessoa; ?>.jpg&amp;id_funcionario=<?=$rs->id_funcionario;?>');" onclick="return confirm('Tem certeza que deseja excluir a foto deste(a) funcionário(a)?');">excluir</a>
                <? } ?>
            </center>
            <? } ?>
            <br /><br />
        </div>
    </fieldset>
    
    <? if (pode("rv", $_SESSION["permissao"])) { ?>
    <center>
	    <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
    <? } ?>
</form>

<? if (!pode("rv", $_SESSION["permissao"])) { ?>
<script language="javascript">
	//desabilitaTudo();
</script>
<? } ?>

<? } ?>