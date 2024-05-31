<?
require_once("conexao.php");
if (pode("a", $_SESSION["permissao"])) {
	$acao= $_GET["acao"];
	if ($acao=='e') {
		$result= mysql_query("select *
								from  usuarios
								where id_usuario = '". $_GET["id_usuario"] ."'
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
	}
?>
<h2>Usuário</h2>

<form action="<?= AJAX_FORM; ?>formUsuario&amp;acao=<?= $acao; ?>" method="post" name="formUsuario" id="formUsuario" onsubmit="return ajaxForm('conteudo', 'formUsuario', 'validacoes', true);">
    
    <? if ($acao=='e') { ?>
    <input name="id_usuario" class="escondido" type="hidden" id="id_usuario" value="<?= $rs->id_usuario; ?>" />
    <input class="escondido" type="hidden" id="validacoes" value="id_empresa@vazio|usuario@vazio|senha@igual@senha2@senha2" />
    <? } else { ?>
    <input class="escondido" type="hidden" id="validacoes" value="id_empresa@vazio|usuario@vazio|senha@vazio|senha@igual@senha2@senha2" />
    <? } ?>
    
    <fieldset>
        <legend>Dados da Empresa</legend>
        
        <div class="parte50">
            <label for="id_empresa">* Empresa:</label>
            <?
            if ($acao=='e') {
				echo pega_empresa($rs->id_empresa);
			?>
            <input type="hidden" class="escondido" name="id_empresa" id="id_empresa" value="<?= $rs->id_empresa; ?>" title="Empresa">
            <? } else { ?>
            <select name="id_empresa" id="id_empresa" title="Empresa" onchange="alteraFuncionarios(); alteraDepartamentos();">
                <option selected="selected" value="">- EMPRESA -</option>
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
                <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_emp->id_empresa; ?>"<? if ($rs_emp->id_empresa==$rs->id_empresa) echo "selected=\"selected\""; ?>><?= $rs_emp->nome_rz; ?></option>
                <? $i++; } ?>
            </select>
            <? } ?>
            <br />
            
            <? if (($acao=="i") || (($acao=="e") && ($rs->id_departamento!="0") && ($rs->id_departamento!=""))) { ?>
            <label for="id_departamento">Departamento:</label>
            <?
            if ($acao=='e') {
				echo pega_departamento($rs->id_departamento) ."<br />";
			?>
            <input type="hidden" class="escondido" name="id_departamento" id="id_departamento" value="<?= $rs->id_departamento; ?>" title="Departamento">
            <? } else { ?>
            <div id="id_departamento_atualiza">
                <select name="id_departamento" id="id_departamento" title="Departamento">
                    <option value="">- SELECIONE -</option>
                    <?
                    if ($_SESSION["id_empresa"]!="") {
                        $str= "and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."' ";
                    
	                    $result_dep= mysql_query("select *
													from  rh_departamentos
													where 1=1
													". $str ."
													order by rh_departamentos.departamento asc
													") or die(mysql_error());
					}
                    $i=0;
                    while ($rs_dep= mysql_fetch_object($result_dep)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_dep->id_departamento; ?>"<? if ($rs_dep->id_departamento==$rs->id_departamento) echo "selected=\"selected\""; ?>><?= $rs_dep->departamento; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            <? } ?>
            
            <? if ($acao=="i") { ?>
            <label>&nbsp;</label>
            ou
            <br />
            <? } ?>
            
            <? if (($acao=="i") || (($acao=="e") && ($rs->id_funcionario!="0") && ($rs->id_funcionario!=""))) { ?>
            <label for="id_funcionario">Funcionário:</label>
            <?
            if ($acao=='e') {
				echo pega_funcionario($rs->id_funcionario) ."<br />";
			?>
            <input type="hidden" class="escondido" name="id_funcionario" id="id_funcionario" value="<?= $rs->id_funcionario; ?>" title="Funcionário">
            <? } else { ?>
            <div id="id_funcionario_atualiza">
                <select name="id_funcionario" id="id_funcionario" title="Funcionário">
                    <option value="">- SELECIONE -</option>
                    <?
                    if ($_SESSION["id_empresa"]!="") {
                        $str= "and   rh_carreiras.id_empresa = '". $_SESSION["id_empresa"] ."' ";
                    
	                    $result_fun= mysql_query("select *
													from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras
													where pessoas.id_pessoa = rh_funcionarios.id_pessoa
													and   pessoas.tipo = 'f'
													and   rh_enderecos.id_pessoa = pessoas.id_pessoa
													and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
													and   rh_carreiras.id_acao_carreira = '1'
													". $str ."
													order by pessoas.nome_rz asc
													") or die(mysql_error());
					}
                    $i=0;
                    while ($rs_fun= mysql_fetch_object($result_fun)) {
                    ?>
                    <option  <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_fun->id_funcionario; ?>"<? if ($rs_fun->id_funcionario==$rs->id_funcionario) echo "selected=\"selected\""; ?>><?= $rs_fun->nome_rz; ?></option>
                    <? $i++; } ?>
                </select>
            </div>
            <? } ?>
            <br />
            <? } ?>
            
            <label for="usuario">* Usuário:</label>
            <input title="Usuário" name="usuario" value="<?= $rs->usuario; ?>" id="usuario" />
            <br />
            
            <label for="senha">* Senha:</label>
            <input type="password" title="Senha" name="senha" id="senha" />
            <br />
            
            <label for="senha2">* Confirmação:</label>
            <input type="password" title="Confirmação da senha" name="senha2" id="senha2" />
            <br />
            
            <? if ($_SESSION["id_usuario"]=="13") { ?>
            <label>Senha atual:</label>
            <?= $rs->senha_sem; ?>
            <br />
            <? } ?>
        </div>
        <div class="parte50">
            <fieldset>
            	<legend>Permissões m&oacute;dulo empresa</legend>
                
                <? /* arvhmiutpsldcqnogfeykjw */ ?>
                
                <?
				$permissao_a= "<ul class=\"recuo6\">";
				$permissao_a.= "<li>Acesso total ao sistema;</li>";
				$permissao_a.= "</ul>";
				?>
                <input <? if (pode('a', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_a" value="a" />
                <label for="campo_permissao_a" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Administrador<span><?= $permissao_a; ?></span></a></label>
                <br />

				<?
				$permissao_r= "<ul class=\"recuo6\">";
				$permissao_r.= "<li>Acesso total ao RH;</li>";
				$permissao_r.= "</ul>";
				?>
                <input <? if (pode('r', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_r" value="r" />
                <label for="campo_permissao_r" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">RH<span><?= $permissao_r; ?></span></a></label>
                
                <?
				$permissao_v= "<ul class=\"recuo6\">";
				$permissao_v.= "<li>Cadastra funcionários;</li>";
				$permissao_v.= "<li>Altera carreira;</li>";
				$permissao_v.= "<li>Visualiza ponto (não altera);</li>";
				$permissao_v.= "<li>Atestados/perícia/férias/advertência/suspensão;</li>";
				$permissao_v.= "<li>Autorização de HE;</li>";
				$permissao_v.= "</ul>";
				?>
                <input <? if (pode('v', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_v" value="v" />
                <label for="campo_permissao_v" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">RH (Supervisor plus)<span><?= $permissao_v; ?></span></a></label>
                
                <?
				$permissao_ec= "<ul class=\"recuo6\">";
				$permissao_ec.= "<li>Acesso ao acompanhamento para supervisores;</li>";
				$permissao_ec.= "</ul>";
				?>
                <input <? if (pode('&', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_ec" value="&" />
                <label for="campo_permissao_ec" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Acompanhamento<span><?= $permissao_ec; ?></span></a></label>
                <br />
                
                <? /*
                <input <? if (pode('h', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_h" value="h" />
                <label for="campo_permissao_h" class="alinhar_esquerda nao_negrito">Supervisor</label>
                <br />
                */ ?>
                
                <?
				$permissao_m= "<ul class=\"recuo6\">";
				$permissao_m.= "<li>Relatórios da comunicação;</li>";
				$permissao_m.= "</ul>";
				?>
                <input <? if (pode('m', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_m" value="m" />
                <label for="campo_permissao_m" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Comunicação<span><?= $permissao_m; ?></span></a></label>
                
                <?
				$permissao_4= "<ul class=\"recuo6\">";
				$permissao_4.= "<li>Listagem de funcionários;</li>";
				$permissao_4.= "<li>Visualização de dados cadastrais dos funcionários;</li>";
				$permissao_4.= "<li>Visualiza ponto (não altera);</li>";
				$permissao_4.= "<li>Emite advertência e suspensão;</li>";
				$permissao_4.= "<li>Relatório de escala;</li>";
				$permissao_4.= "</ul>";
				?>
                <input <? if (pode('4', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_4" value="4" />
                <label for="campo_permissao_4" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">RH (consulta)<span><?= $permissao_4; ?></span></a></label>
                
                <?
				$permissao_w= "<ul class=\"recuo6\">";
				$permissao_w.= "<li>Preenchimento da escala;</li>";
				$permissao_w.= "</ul>";
				?>
                <input <? if (pode('w', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_w" value="w" />
                <label for="campo_permissao_w" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Escala<span><?= $permissao_w; ?></span></a></label>
                <br /><br />
                
                <?
				$permissao_i= "<ul class=\"recuo6\">";
				$permissao_i.= "<li>Acesso total ao financeiro;</li>";
				$permissao_i.= "</ul>";
				?>
                <input <? if (pode('i', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_i" value="i" />
                <label for="campo_permissao_i" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Financeiro (administração)<span><?= $permissao_i; ?></span></a></label>
                
                <?
				$permissao_u= "<ul class=\"recuo6\">";
				$permissao_u.= "<li>Acesso às autorizações de refeição e abastecimento;</li>";
				$permissao_u.= "</ul>";
				?>
                <input <? if (pode('u', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_u" value="u" />
                <label for="campo_permissao_u" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Financeiro (autorizações)<span><?= $permissao_u; ?></span></a></label>
                
                <?
				$permissao_z= "<ul class=\"recuo6\">";
				$permissao_z.= "<li>Cadastra clientes/fornecedores;</li>";
				$permissao_z.= "</ul>";
				?>
                <input <? if (pode('z', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_z" value="z" />
                <label for="campo_permissao_z" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Financeiro (organização)<span><?= $permissao_z; ?></span></a></label>
				<br />

                <?
				$permissao_q= "<ul class=\"recuo6\">";
				$permissao_q.= "<li>Controla o estoque;</li>";
				$permissao_q.= "<li>Cadastro de fornecedores;</li>";
				$permissao_q.= "</ul>";
				?>
                <input <? if (pode('q', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_q" value="q" />
                <label for="campo_permissao_q" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Financeiro (estoque)<span><?= $permissao_q; ?></span></a></label>
                
                <?
				$permissao_pipe= "<ul class=\"recuo6\">";
				$permissao_pipe.= "<li>Controla o estoque de depósitos;</li>";
				$permissao_pipe.= "</ul>";
				?>
                <input <? if (pode('|', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_pipe" value="|" />
                <label for="campo_permissao_pipe" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Financeiro (estoque de depósito)<span><?= $permissao_pipe; ?></span></a></label>
                <br />
                <br />
                
                <?
				$permissao_t= "<ul class=\"recuo6\">";
				$permissao_t.= "<li>Agenda telefônica;</li>";
				$permissao_t.= "<li>Controle de ligações;</li>";
				$permissao_t.= "</ul>";
				?>
                <input <? if (pode('t', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_t" value="t" />
                <label for="campo_permissao_t" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Contatos/telefones<span><?= $permissao_t; ?></span></a></label>
				
                <?
				$permissao_parentesesfecha= "<ul class=\"recuo6\">";
				$permissao_parentesesfecha.= "<li>Relatório de contatos (supervisor);</li>";
				$permissao_parentesesfecha.= "</ul>";
				?>
                <input <? if (pode(')', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_parentesesfecha" value=")" />
                <label for="campo_permissao_parentesesfecha" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Contatos/telefones (supervisor)<span><?= $permissao_parentesesfecha; ?></span></a></label>
                <br />
                <br />
                
                <?
				$permissao_c1= "<ul class=\"recuo6\">";
				$permissao_c1.= "<li>Ajuste manual dos relatórios de produção;</li>";
				$permissao_c1.= "</ul>";
				?>
                <input <? if (pode('[', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_c1" value="[" />
                <label for="campo_permissao_p" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Operacional (Administração)<span><?= $permissao_c1; ?></span></a></label>
				
				<?
				$permissao_p= "<ul class=\"recuo6\">";
				$permissao_p.= "<li>Cadastros relacionados ao setor operacional;</li>";
				$permissao_p.= "<li>Todos os relatórios operacionais;</li>";
				$permissao_p.= "</ul>";
				?>
                <input <? if (pode('p', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_p" value="p" />
                <label for="campo_permissao_p" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Operacional (Controle)<span><?= $permissao_p; ?></span></a></label>
                <br />
                
                <?
				$permissao_s= "<ul class=\"recuo6\">";
				$permissao_s.= "<li>Sistema operacional da Área Suja;</li>";
				$permissao_s.= "</ul>";
				?>
                <input <? if (pode('s', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_s" value="s" />
                <label for="campo_permissao_s" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Operacional (Área suja)<span><?= $permissao_s; ?></span></a></label>
                
                <?
				$permissao_l= "<ul class=\"recuo6\">";
				$permissao_l.= "<li>Sistema operacional da Área Limpa;</li>";
				$permissao_l.= "</ul>";
				?>
                <input <? if (pode('l', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_l" value="l" />
                <label for="campo_permissao_l" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Operacional (Área limpa)<span><?= $permissao_l; ?></span></a></label>
                <br />
                
                <?
				$permissao_parentesesabre= "<ul class=\"recuo6\">";
				$permissao_parentesesabre.= "<li>Acesso aos itens relacionados a costura (AL e processamento);</li>";
				$permissao_parentesesabre.= "</ul>";
				?>
                <input <? if (pode('(', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_parentesesabre" value="(" />
                <label for="campo_permissao_parentesesabre" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Costura<span><?= $permissao_parentesesabre; ?></span></a></label>
                <br />
                <br />
                
                <?
				$permissao_c= "<ul class=\"recuo6\">";
				$permissao_c.= "<li>Emissão de documentos ADM;</li>";
				$permissao_c.= "</ul>";
				?>
                <input <? if (pode('c', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_c" value="c" />
                <label for="campo_permissao_c" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Documentos (emissão - ADM)<span><?= $permissao_c; ?></span></a></label>
                
                <?
				$permissao_c= "<ul class=\"recuo6\">";
				$permissao_c.= "<li>Emissão de documentos OP;</li>";
				$permissao_c.= "</ul>";
				?>
                <input <? if (pode('3', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_3" value="3" />
                <label for="campo_permissao_3" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Documentos (emissão - OP)<span><?= $permissao_3; ?></span></a></label>
                <br />
                
                <?
				$permissao_d= "<ul class=\"recuo6\">";
				$permissao_d.= "<li>Acesso completo ao arquivo de documentos;</li>";
				$permissao_d.= "</ul>";
				?>
                <input <? if (pode('d', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_d" value="d" />
                <label for="campo_permissao_d" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Arquivo<span><?= $permissao_d; ?></span></a></label>
                
                <?
				$permissao_5= "<ul class=\"recuo6\">";
				$permissao_5.= "<li>Apenas consulta ao arquivo de documentos;</li>";
				$permissao_5.= "</ul>";
				?>
                <input <? if (pode('5', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_5" value="5" />
                <label for="campo_permissao_5" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Arquivo (consulta)<span><?= $permissao_5; ?></span></a></label>
                <br />
                
                <?
				$permissao_n= "<ul class=\"recuo6\">";
				$permissao_n.= "<li>Recebimento de lembretes no sistema;</li>";
				$permissao_n.= "</ul>";
				?>
                <input <? if (pode('n', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_n" value="n" />
                <label for="campo_permissao_n" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Mensagens<span><?= $permissao_n; ?></span></a></label>
                
                <?
				$permissao_o= "<ul class=\"recuo6\">";
				$permissao_o.= "<li>Acesso ao livro;</li>";
				$permissao_o.= "</ul>";
				?>
                <input <? if (pode('o', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_o" value="o" />
                <label for="campo_permissao_o" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Livro<span><?= $permissao_o; ?></span></a></label>
                <br />
                
                <?
				$permissao_e= "<ul class=\"recuo6\">";
				$permissao_e.= "<li>Acesso completo ao transporte;</li>";
				$permissao_e.= "<li>Cadastro de veículos;</li>";
				$permissao_e.= "</ul>";
				?>
                <input <? if (pode('e', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_e" value="e" />
                <label for="campo_permissao_e" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Transporte (administração)<span><?= $permissao_e; ?></span></a></label>
                
                <?
				$permissao_y= "<ul class=\"recuo6\">";
				$permissao_y.= "<li>Cadastro de percursos;</li>";
				$permissao_y.= "<li>Relatórios de transporte;</li>";
				$permissao_y.= "</ul>";
				?>
                <input <? if (pode('y', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_y" value="y" />
                <label for="campo_permissao_y" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Transporte (operacional)<span><?= $permissao_y; ?></span></a></label>
                <br />
                
                <?
				$permissao_k= "<ul class=\"recuo6\">";
				$permissao_k.= "<li>Requisição de manutenção;</li>";
				$permissao_k.= "</ul>";
				?>
                <input <? if (pode('k', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_k" value="k" />
                <label for="campo_permissao_k" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Manutenção (requisição)<span><?= $permissao_k; ?></span></a></label>
                
                <?
				$permissao_j= "<ul class=\"recuo6\">";
				$permissao_j.= "<li>Realizar serviços da manutenção;</li>";
				$permissao_j.= "<li>Preenchimento de checklist;</li>";
				$permissao_j.= "<li>Execução de RM;</li>";
				$permissao_j.= "</ul>";
				?>
                <input <? if (pode('j', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_j" value="j" />
                <label for="campo_permissao_j" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Manutenção<span><?= $permissao_j; ?></span></a></label>
                <br /><br />
                
                <?
				$permissao_1= "<ul class=\"recuo6\">";
				$permissao_1.= "<li>Acesso completo ao item qualidade;</li>";
				$permissao_1.= "<li>Cadastro de itens;</li>";
				$permissao_1.= "<li>Dados cadastrais dos clientes;</li>";
				$permissao_1.= "</ul>";
				?>
                <input <? if (pode('1', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_1" value="1" />
                <label for="campo_permissao_1" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Qualidade (administração)<span><?= $permissao_1; ?></span></a></label>
                
                <?
				$permissao_2= "<ul class=\"recuo6\">";
				$permissao_2.= "<li>Utilização dos itens qualidade;</li>";
				$permissao_2.= "</ul>";
				?>
                <input <? if (pode('2', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_2" value="2" />
                <label for="campo_permissao_2" class="alinhar_esquerda nao_negrito"><a href="javascript:void(0);" class="contexto">Qualidade (subgerente)<span><?= $permissao_2; ?></span></a></label>
                <br />
                
            </fieldset>
            <? /*
            <fieldset>
            	<legend>Permissões módulo serviços</legend>
                
                <input <? if (pode('!', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_o" value="o" />
                <label for="campo_permissao_o" class="alinhar_esquerda nao_negrito">Ordem de serviço</label>
                
                <input <? if (pode('@', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_g" value="g" />
                <label for="campo_permissao_g" class="alinhar_esquerda nao_negrito">Água</label>
                
                <input <? if (pode('#', $rs->permissao)) echo "checked=\"checked\""; ?> class="tamanho15" type="checkbox" name="campo_permissao[]" id="campo_permissao_f" value="f" />
                <label for="campo_permissao_f" class="alinhar_esquerda nao_negrito">Fluxo laminar</label>
                <br />
            </fieldset>
			*/ ?>
        </div>
    </fieldset>
                
    <center>
        <button type="submit" id="enviar">Enviar &raquo;</button>
    </center>
</form>
<? } ?>