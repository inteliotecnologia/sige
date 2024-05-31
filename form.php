<?
require_once("funcoes.php");
if (!$conexao) require_once("conexao.php");

header("Content-type: text/html; charset=iso-8859-1", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<body>'; 
*/

if ($_SESSION["tipo_usuario"]=="a") {
	if (isset($_GET["formEmpresaEmular"])) {
		$_SESSION["id_empresa"]= $_POST["id_empresa_emula"];
		header("location: ./");
	}
}//fim soh adm do sistema

if (pode("aipuslerz(", $_SESSION["permissao"])) {	
	if (isset($_GET["formPessoa"])) {
		if (($_POST["tipo"]!="") && ($_POST["id_cidade"]!="") ) {
			//inserir empresa
			if ($_GET["acao"]=="i") {	
				$var=0;
				inicia_transacao();
				
				if ($_POST["tipo"]=='f') $cpf_cnpj= $_POST["cpf"];
				else $cpf_cnpj= $_POST["cnpj"];
				
				$auth= gera_auth();
				$num_pessoa= (pega_num_ultima_pessoa($_SESSION["id_empresa"], $_POST["tipo_pessoa"])+1);
				
				$result1= mysql_query("insert into pessoas (nome_rz, apelido_fantasia, rg_ie, cpf_cnpj, data, contato, tipo, codigo,
															sigla, auth, id_empresa, id_empresa_atendente, tipo_pedido, basear_peso, basear_nota_data,
															balanca_coleta, balanca_entrega, id_regiao, status_pessoa, id_contrato, primeiro_contato, id_cliente_tipo) values
										('". strtoupper($_POST["nome_rz"]) ."', '". strtoupper($_POST["apelido_fantasia"]) ."', '". $_POST["rg_ie"] ."',
										'". $cpf_cnpj ."', '". formata_data($_POST["data_nasc"]) ."', '". strtoupper($_POST["contato"]) ."','". $_POST["tipo"] ."',
										'". $_POST["codigo"] ."', '". $_POST["sigla"] ."',
										
										'". $auth ."', '". $_SESSION["id_empresa"] ."', '". $_POST["id_empresa_atendente"] ."', '". $_POST["tipo_pedido"] ."', '". $_POST["basear_peso"] ."',
										'". $_POST["basear_nota_data"] ."', '". $_POST["balanca_coleta"] ."', '". $_POST["balanca_entrega"] ."',
										'". $_POST["id_regiao"] ."', '". $_POST["status_pessoa"] ."', '". $_POST["id_contrato"] ."', '". formata_data($_POST["primeiro_contato"]) ."', '". $_POST["id_cliente_tipo"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_pessoa= mysql_insert_id();
				
				$result1b= mysql_query("insert into pessoas_tipos (id_pessoa, num_pessoa, tipo_pessoa, id_empresa) values
										('". $id_pessoa ."', '". $num_pessoa ."', '". $_POST["tipo_pessoa"] ."', '". $_SESSION["id_empresa"] ."') ") or die(mysql_error());
				if (!$result1b) $var++;
				
				$result2= mysql_query("insert into rh_enderecos (id_pessoa, id_cidade, rua, numero, complemento, bairro,
																 cep, tel_com, tel_res, tel_cel, email, site)
										values
										('$id_pessoa', '". $_POST["id_cidade"] ."', '". strtoupper($_POST["rua"]) ."', '". $_POST["numero"] ."',
										'". strtoupper($_POST["complemento"]) ."', '". strtoupper($_POST["bairro"]) ."', '". $_POST["cep"] ."', '". $_POST["tel_com"] ."',
										'". $_POST["tel_res"] ."', '". $_POST["tel_cel"] ."', '". $_POST["email"] ."', '". $_POST["site"] ."' )") or die(mysql_error());
				if (!$result2) $var++;
				
				//só se for uma empresa admin
				if ($_POST["tipo_pessoa"]=='a') {
					$result3 = mysql_query("insert into empresas (id_pessoa, status_empresa)
											values ('$id_pessoa', '1') ") or die(mysql_error());
					if (!$result3) $var++;
					
					$id_empresa_aqui= mysql_insert_id();
					
					if ($_FILES["foto"]["name"]!="") {
						$caminho= CAMINHO . "empresa_". $id_empresa_aqui .".jpg";
						@move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
						@otimiza_foto($caminho, 195);
					}
				}
				
				//fornecedor
				if ($_POST["tipo_pessoa"]=='f') {
					$result_cc_pre= mysql_query("delete from fi_pessoas_cc_tipos
													where id_pessoa = '". $id_pessoa ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
				
					$i=0;
					while ($_POST["id_centro_custo_tipo"][$i]!="") {
						$result_cc1[$i]= mysql_query("insert into fi_pessoas_cc_tipos
													(id_pessoa, id_centro_custo_tipo, id_empresa) values
													('". $id_pessoa ."', '". $_POST["id_centro_custo_tipo"][$i] ."',
													'". $_SESSION["id_empresa"] ."')
													") or die(mysql_error());
						if (!$result_cc1[$i]) $var++;
						
						$i++;
					}
				}
				
				$passa_contato=0;
				switch ($_POST["tipo_pessoa"]) {
					case 'f': $tipo_contato= 1; $passa_contato=1; break;
					case 'c': $tipo_contato= 4; $passa_contato=1; break;
				}
				
				if ($passa_contato) {
					$result4= mysql_query("insert into tel_contatos (id_empresa, tipo_contato, nome, email,
													id_pessoa, id_usuario)
													values (
													'". $_SESSION["id_empresa"] ."', '$tipo_contato',
													'". strtoupper($_POST["nome_rz"]) ."', '". $_POST["email"] ."',
													'$id_pessoa', '". $_SESSION["id_usuario"] ."' ) ") or die("5: ". mysql_error());
					if (!$result4) $var++;
					$id_contato= mysql_insert_id();
					
					if ($_POST["tel_res"]!="") {
						$result5= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
											values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_res"] ."', '1' ) ") or die("6: ". mysql_error());
						if (!$result5) $var++;
					}
					if ($_POST["tel_cel"]!="") {
						$result6= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
											values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_cel"] ."', '3' ) ") or die("7: ". mysql_error());
						if (!$result6) $var++;
					}
					if ($_POST["tel_com"]!="") {
						$result7= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
												values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_com"] ."', '2' ) ") or die("8: ". mysql_error());
						if (!$result7) $var++;
					}
					if ($_POST["tel_fax"]!="") {
						$result5= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
											values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_fax"] ."', '4' ) ") or die("6: ". mysql_error());
						if (!$result5) $var++;
					}
				}
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/pessoa_listar";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/pessoa_listar&tipo_pessoa=". $_POST["tipo_pessoa"] ."&msg=". $msg);
			}
				
			//editar pessoa
			if ($_GET["acao"]=="e") {
				$var=0;
				/*$result_pre= mysql_query("select pessoas.id_pessoa from pessoas
											where pessoas.id_pessoa <> '". $_POST["id_pessoa"] ."'
											and   pessoas.cpf_cnpj = '". $_POST["cnpj"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					if ($_POST["tipo"]=='f') $cpf_cnpj= $_POST["cpf"];
					else $cpf_cnpj= $_POST["cnpj"];
					
					$result1= mysql_query("update pessoas set
											pessoas.nome_rz= '". strtoupper($_POST["nome_rz"]) ."',
											pessoas.apelido_fantasia= '". strtoupper($_POST["apelido_fantasia"]) ."',
											pessoas.rg_ie= '". $_POST["rg_ie"] ."',
											pessoas.cpf_cnpj= '". $cpf_cnpj ."',
											pessoas.data= '". formata_data($_POST["data_nasc"]) ."',
											pessoas.contato= '". strtoupper($_POST["contato"]) ."',
											pessoas.sigla= '". strtoupper($_POST["sigla"]) ."',
											pessoas.codigo= '". strtoupper($_POST["codigo"]) ."',
											pessoas.id_empresa_atendente= '". $_POST["id_empresa_atendente"] ."',
											pessoas.tipo_pedido= '". $_POST["tipo_pedido"] ."',
											pessoas.basear_peso= '". $_POST["basear_peso"] ."',
											pessoas.basear_nota_data= '". $_POST["basear_nota_data"] ."',
											pessoas.balanca_coleta= '". $_POST["balanca_coleta"] ."',
											pessoas.balanca_entrega= '". $_POST["balanca_entrega"] ."',
											pessoas.id_regiao= '". $_POST["id_regiao"] ."',
											pessoas.status_pessoa= '". $_POST["status_pessoa"] ."',
											pessoas.id_contrato= '". $_POST["id_contrato"] ."',
											pessoas.primeiro_contato= '". formata_data($_POST["primeiro_contato"]) ."',
											pessoas.id_cliente_tipo= '". $_POST["id_cliente_tipo"] ."'
											where pessoas.id_pessoa = '". $_POST["id_pessoa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$result2= mysql_query("update pessoas, rh_enderecos set
											rh_enderecos.id_cidade= '". $_POST["id_cidade"] ."',
											rh_enderecos.rua= '". strtoupper($_POST["rua"]) ."',
											rh_enderecos.numero= '". $_POST["numero"] ."',
											rh_enderecos.complemento= '". strtoupper($_POST["complemento"]) ."',
											rh_enderecos.bairro= '". strtoupper($_POST["bairro"]) ."',
											rh_enderecos.cep= '". $_POST["cep"] ."',
											rh_enderecos.tel_res= '". $_POST["tel_res"] ."',
											rh_enderecos.tel_com= '". $_POST["tel_com"] ."',
											rh_enderecos.tel_cel= '". $_POST["tel_cel"] ."',
											rh_enderecos.tel_fax= '". $_POST["tel_fax"] ."',
											rh_enderecos.email= '". $_POST["email"] ."',
											rh_enderecos.site= '". $_POST["site"] ."'
											where pessoas.id_pessoa = '". $_POST["id_pessoa"] ."'
											and   pessoas.id_pessoa = rh_enderecos.id_pessoa
											") or die(mysql_error());
					if (!$result2) $var++;
					
					$result3= mysql_query("update pessoas_tipos set
											num_pessoa= '". $_POST["num_pessoa"] ."'
											where id_pessoa = '". $_POST["id_pessoa"] ."'
											and   tipo_pessoa = '". $_POST["tipo_pessoa"] ."'
											") or die(mysql_error());
					if (!$result3) $var++;
					
					if ($_POST["tipo_pessoa"]=='f') {
						$result_cc_pre= mysql_query("delete from fi_pessoas_cc_tipos
														where id_pessoa = '". $_POST["id_pessoa"] ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														") or die(mysql_error());
					
						$i=0;
						while ($_POST["id_centro_custo_tipo"][$i]!="") {
							$result_cc1[$i]= mysql_query("insert into fi_pessoas_cc_tipos
														(id_pessoa, id_centro_custo_tipo, id_empresa) values
														('". $_POST["id_pessoa"] ."', '". $_POST["id_centro_custo_tipo"][$i] ."',
														'". $_SESSION["id_empresa"] ."')
														") or die(mysql_error());
							if (!$result_cc1[$i]) $var++;
							
							$i++;
						}
					}
					elseif ($_POST["tipo_pessoa"]=="a") {
						if ($_FILES["foto"]["name"]!="") {
							$id_empresa_aqui= pega_id_empresa_da_pessoa($_POST["id_pessoa"]);
							$caminho= CAMINHO . "empresa_". $id_empresa_aqui .".jpg";
							@move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
							@otimiza_foto($caminho, 195);
						}
					}
					
					finaliza_transacao($var);
				//} else { echo 4; $var++; }
				
				$msg= $var;
				
				if ($_POST["esquema"]=="") {
					$pagina= "financeiro/pessoa_listar";
					require_once("index2.php");
				}
				else {
					$pagina= "financeiro/pessoa";
					require_once("index2.php");
				}
				
				//header("location: ./?pagina=financeiro/pessoa_listar&tipo_pessoa=". $_POST["tipo_pessoa"] ."&msg=". $msg);
			}//e
		}//fim teste variáveis
		else echo "Faltando dados.";
	}//formEmpresa
	
	if (isset($_GET["formClientePecas"])) {
		$var=0;
		inicia_transacao();
		
		$result1_pre= mysql_query("update pessoas set
									pessoas.obs_gerais= '". $_POST["obs_gerais"] ."'
									where pessoas.id_pessoa = '". $_POST["id_cliente"] ."'
									") or die(mysql_error());
		if (!$result1_pre) $var++;
		
		$result_pre= mysql_query("delete from fi_clientes_pecas
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_cliente = '". $_POST["id_cliente"] ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		$i=0;
		while ($_POST["nada"][$i]!="") {
			if ($_POST["id_peca"][$i]!="") {
				$result1[$i]= mysql_query("insert into fi_clientes_pecas
										(id_empresa, id_cliente, id_peca, acabamento_orientacoes, status_cliente_peca, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
										'". $_POST["id_peca"][$i]  ."', '". $_POST["acabamento_orientacoes"][$i]  ."', '1', '". $_SESSION["id_usuario"] ."')
										") or die(mysql_error());
				if (!$result1[$i]) $var++;
			}
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "financeiro/cliente_pecas";
		require_once("index2.php");
	}
	
	if (isset($_GET["formClientePecasSelecao"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("update fi_clientes_pecas
										set status_cliente_peca = '0'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '". $_POST["id_cliente"] ."'
										") or die(mysql_error());
		
		$i=0;
		while ($_POST["nada"][$i]!="") {
			if ($_POST["id_peca"][$i]!="") {
				
				$result_pre_peca[$i]= mysql_query("select * from fi_clientes_pecas
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_cliente = '". $_POST["id_cliente"] ."'
													and   id_peca = '". $_POST["id_peca"][$i] ."'
													") or die(mysql_error());
				
				$linhas_pre_peca[$i]= mysql_num_rows($result_pre_peca[$i]);
				
				if ($linhas_pre_peca[$i]==0)
					$result1[$i]= mysql_query("insert into fi_clientes_pecas
											(id_empresa, id_cliente, id_peca, acabamento_orientacoes, status_cliente_peca, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
											'". $_POST["id_peca"][$i]  ."', '', '1', '". $_SESSION["id_usuario"] ."')
											") or die(mysql_error());

				else
					$result1[$i]= mysql_query("update fi_clientes_pecas set status_cliente_peca = '1'
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_cliente = '". $_POST["id_cliente"] ."'
												and   id_peca = '". $_POST["id_peca"][$i] ."'
											") or die(mysql_error());
				
				if (!$result1[$i]) $var++;
			}
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "financeiro/cliente_pecas_selecao";
		require_once("index2.php");
	}
	
	if (isset($_GET["formClienteServicosPecas"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("delete from fi_clientes_servicos_pecas
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_cliente = '". $_POST["id_cliente"] ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		$i=0;
		while ($_POST["nada"][$i]!="") {
			if ($_POST["id_peca"][$i]!="") {
				$result1[$i]= mysql_query("insert into fi_clientes_servicos_pecas
										(id_empresa, id_cliente, id_peca, passadoria_preco, costura_preco, enxoval_preco, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
										'". $_POST["id_peca"][$i]  ."', '". formata_valor($_POST["passadoria_preco"][$i])  ."',
										'". formata_valor($_POST["costura_preco"][$i])  ."', '". formata_valor($_POST["enxoval_preco"][$i])  ."',
										'". $_SESSION["id_usuario"] ."')
										") or die(mysql_error());
				if (!$result1[$i]) $var++;
			}
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "financeiro/cliente_servicos_pecas";
		require_once("index2.php");
	}
	
	if (isset($_GET["formClienteHistorico"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["reclamacao_id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into com_livro (id_empresa, mensagem, data_livro, hora_livro, reclamacao_id_cliente, restrito, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["mensagem"] ."',
												'". formata_data($_POST["data_livro"]) ."', '". $_POST["hora_livro"] ."',
												'". $_POST["reclamacao_id_cliente"] ."', '1',
												'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_historico";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["reclamacao_id_cliente"]!="") && ($_POST["id_livro"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update com_livro set reclamacao_id_cliente= '". $_POST["reclamacao_id_cliente"] ."',
										mensagem= '". $_POST["mensagem"] ."',
										data_livro= '". formata_data($_POST["data_livro"]) ."',
										hora_livro= '". $_POST["hora_livro"] ."'
										where id_livro = '". $_POST["id_livro"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "financeiro/cliente_historico";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formVisitaPesquisa"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into qual_pesquisa (id_empresa, id_cliente, responsavel, id_cliente_setor,
																  data_pesquisa, duracao, obs, pontos_positivos, pontos_negativos, criticas, status_pesquisa, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["responsavel"] ."', '". $_POST["id_cliente_setor"] ."',
												'". formata_data($_POST["data_pesquisa"]) ."', '". $_POST["duracao"] ."',
												'". $_POST["obs"] ."', '". $_POST["pontos_positivos"] ."', '". $_POST["pontos_negativos"] ."', '". $_POST["criticas"] ."', '1',
												'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_pesquisa";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_pesquisa"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update qual_pesquisa set id_cliente= '". $_POST["id_cliente"] ."',
										responsavel= '". $_POST["responsavel"] ."',
										id_cliente_setor= '". $_POST["id_cliente_setor"] ."',
										data_pesquisa= '". formata_data($_POST["data_pesquisa"]) ."',
										duracao= '". $_POST["duracao"] ."',
										obs= '". $_POST["obs"] ."',
										pontos_positivos= '". $_POST["pontos_positivos"] ."',
										pontos_negativos= '". $_POST["pontos_negativos"] ."',
										criticas= '". $_POST["criticas"] ."'
										where id_pesquisa = '". $_POST["id_pesquisa"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "financeiro/cliente_pesquisa";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formAcompanhamentoItem"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["acompanhamento_item"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into op_acompanhamento_itens (id_empresa, acompanhamento_item, status_item, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["acompanhamento_item"] ."', '1', '". $_SESSION["id_usuario"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
					
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "op/acompanhamento_item_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update op_acompanhamento_itens set
										acompanhamento_item= '". $_POST["acompanhamento_item"] ."'
										where id_acompanhamento_item= '". $_POST["id_acompanhamento_item"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "op/acompanhamento_item_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formReclamacaoCausa"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["causa"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into qual_reclamacoes_causas (id_empresa, causa, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["causa"] ."', '". $_SESSION["id_usuario"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
					
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "qualidade/causa_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update qual_reclamacoes_causas set
										causa= '". $_POST["causa"] ."'
										where id_causa= '". $_POST["id_causa"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "qualidade/causa_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formClienteSetor"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into fi_clientes_setores (id_empresa, id_cliente, setor, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
												'". $_POST["setor"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_setor";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_cliente_setor"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update fi_clientes_setores set id_cliente= '". $_POST["id_cliente"] ."',
										setor= '". $_POST["setor"] ."'
										where id_cliente_setor = '". $_POST["id_cliente_setor"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "financeiro/cliente_setor";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formPesquisaCategoria"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["pesquisa_categoria"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into qual_pesquisa_categorias (id_empresa, pesquisa_categoria, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["pesquisa_categoria"] ."',
												'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "qualidade/pesquisa_categoria_listar";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["pesquisa_categoria"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update qual_pesquisa_categorias set pesquisa_categoria= '". $_POST["pesquisa_categoria"] ."'
										where id_pesquisa_categoria = '". $_POST["id_pesquisa_categoria"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "qualidade/pesquisa_categoria_listar";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formPesquisaItem"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["pesquisa_item"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into qual_pesquisa_itens (id_empresa, id_pesquisa_categoria, pesquisa_item, status_item, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_pesquisa_categoria"] ."', '". $_POST["pesquisa_item"] ."',
												'1', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "qualidade/pesquisa_item_listar";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_pesquisa_item"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update qual_pesquisa_itens set id_pesquisa_categoria= '". $_POST["id_pesquisa_categoria"] ."',
										pesquisa_item= '". $_POST["pesquisa_item"] ."'
										where id_pesquisa_item = '". $_POST["id_pesquisa_item"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "qualidade/pesquisa_item_listar";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formClienteServicosGeral"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into fi_clientes_servicos_geral
									  (id_empresa, id_cliente, lavacao_peso_minimo_mes,
										lavacao_preco_kg, extra_mes, extra_adicional_preco,
										costura_recebimento_id_dia, costura_entrega_id_dia, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
										'". formata_valor($_POST["lavacao_peso_minimo_mes"]) ."', '". formata_valor($_POST["lavacao_preco_kg"]) ."',
										'". $_POST["extra_mes"] ."', '". formata_valor($_POST["extra_adicional_preco"]) ."',
										'". $_POST["costura_recebimento_id_dia"] ."', '". $_POST["costura_entrega_id_dia"] ."',
										'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$result_limpa= mysql_query("delete from tr_cronograma
										   		where id_cliente= '". $_POST["id_cliente"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
				
				$i=0;
				while ($_POST["tipo"][$i]!="") {
					$result1= mysql_query("insert into tr_cronograma (id_empresa, id_cliente, tipo, id_dia, hora_cronograma, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["tipo"][$i] ."',
											'". $_POST["id_dia"][$i] ."', '". $_POST["hora_cronograma"][$i] ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_servicos_geral";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/cliente_esquema&msg=". $msg ."&pagina_inclui=_financeiro/__cliente_pecas_dobra&acao=i&aba_atual=parametros&id_peca=". $_POST["id_peca"] ."&id_cliente=". $_POST["id_cliente"]);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update fi_clientes_servicos_geral set
									  	lavacao_peso_minimo_mes= '". formata_valor($_POST["lavacao_peso_minimo_mes"]) ."',
										lavacao_preco_kg= '". formata_valor($_POST["lavacao_preco_kg"]) ."',
										extra_mes= '". $_POST["extra_mes"] ."',
										extra_adicional_preco= '". formata_valor($_POST["extra_adicional_preco"]) ."',
										costura_recebimento_id_dia= '". $_POST["costura_recebimento_id_dia"] ."',
										costura_entrega_id_dia= '". $_POST["costura_entrega_id_dia"] ."'
										where id_cliente_servico_geral = '". $_POST["id_cliente_servico_geral"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '". $_POST["id_cliente"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				$result_limpa= mysql_query("delete from tr_cronograma
										   		where id_cliente= '". $_POST["id_cliente"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
				
				$i=0;
				while ($_POST["nada"][$i]!="") {
					if ($_POST["hora_cronograma"][$i]!="") {
						$result1= mysql_query("insert into tr_cronograma (id_empresa, id_cliente, tipo, id_dia, hora_cronograma, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["tipo"][$i] ."',
												'". $_POST["id_dia"][$i] ."', '". $_POST["hora_cronograma"][$i] ."', '". $_SESSION["id_usuario"] ."'
												) ") or die(mysql_error());
						if (!$result1) $var++;
					}
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "financeiro/cliente_servicos_geral";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/cliente_esquema&msg=". $msg ."&pagina_inclui=_financeiro/__cliente_pecas_dobra&acao=i&aba_atual=parametros&id_peca=". $_POST["id_peca"] ."&id_cliente=". $_POST["id_cliente"]);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formPesquisaNotas"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result_limpa= mysql_query("delete from qual_pesquisa_notas
										   		where id_cliente= '". $_POST["id_cliente"] ."'
												and   id_pesquisa= '". $_POST["id_pesquisa"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
				if (!$result_limpa) $var++;
				
				$i=0;
				while ($_POST["nada"][$i]!="") {
					$result1= mysql_query("insert into qual_pesquisa_notas (id_empresa, id_pesquisa, id_pesquisa_item, id_cliente, nota, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_pesquisa"] ."', '". $_POST["id_pesquisa_item"][$i] ."',
											'". $_POST["id_cliente"] ."', '". $_POST["nota"][$i] ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_pesquisa_notas";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/cliente_esquema&msg=". $msg ."&pagina_inclui=_financeiro/__cliente_pecas_dobra&acao=i&aba_atual=parametros&id_peca=". $_POST["id_peca"] ."&id_cliente=". $_POST["id_cliente"]);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formClientePecasDobra"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_peca"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into fi_clientes_pecas_dobra (id_empresa, id_cliente, id_peca, legenda_foto, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
												'". $_POST["id_peca"] ."', '". $_POST["legenda_foto"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$id_cliente_peca_dobra= mysql_insert_id();
				
				if ($_FILES["foto"]["name"]!="") {
					$caminho= CAMINHO . "cliente_peca_dobra_". $id_cliente_peca_dobra .".jpg";
					@move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
					//otimiza_foto($caminho, 200);
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				//$pagina= "financeiro/cliente_setor";
				//require_once("index2.php");
				
				header("location: ./?pagina=financeiro/cliente_esquema&msg=". $msg ."&pagina_inclui=_financeiro/__cliente_pecas_dobra&acao=i&aba_atual=parametros&id_peca=". $_POST["id_peca"] ."&id_cliente=". $_POST["id_cliente"]);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_peca"]!="") && ($_POST["id_cliente_peca_dobra"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update fi_clientes_pecas_dobra set id_cliente= '". $_POST["id_cliente"] ."',
										id_peca= '". $_POST["id_peca"] ."',
										legenda_foto= '". $_POST["legenda_foto"] ."'
										where id_cliente_peca_dobra = '". $_POST["id_cliente_peca_dobra"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				if ($_FILES["foto"]["name"]!="") {
					$caminho= CAMINHO . "cliente_peca_dobra_". $_POST["id_cliente_peca_dobra"] .".jpg";
					@move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
					//otimiza_foto($caminho, 200);
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				//$pagina= "financeiro/cliente_setor";
				//require_once("index2.php");
				
				header("location: ./?pagina=financeiro/cliente_esquema&msg=". $msg ."&pagina_inclui=_financeiro/__cliente_pecas_dobra&acao=i&aba_atual=parametros&id_peca=". $_POST["id_peca"] ."&id_cliente=". $_POST["id_cliente"]);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formClienteItemCedido"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("insert into fi_clientes_itens_cedidos (id_empresa, id_cliente, tipo_item_cedido,
																			  	data_entrega, data_valida, qtde, qtde_padrao, qtde_debito, recebido_por, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
												'". $_POST["tipo_item_cedido"] ."', '". formata_data($_POST["data_entrega"]) ."', '". formata_data($_POST["data_valida"]) ."',
												'". $_POST["qtde"] ."', '". $_POST["qtde_padrao"] ."', '". $_POST["qtde_debito"] ."', '". $_POST["recebido_por"] ."',
												'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
			
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/cliente_item_cedido";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update fi_clientes_itens_cedidos set id_cliente= '". $_POST["id_cliente"] ."',
										data_entrega= '". formata_data($_POST["data_entrega"]) ."',
										data_valida= '". formata_data($_POST["data_valida"]) ."',
										qtde= '". $_POST["qtde"] ."',
										qtde_padrao= '". $_POST["qtde_padrao"] ."',
										qtde_debito= '". $_POST["qtde_debito"] ."',
										recebido_por= '". $_POST["recebido_por"] ."'
										where id_item_cedido = '". $_POST["id_item_cedido"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "financeiro/cliente_item_cedido";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formClienteItemCedidoPadrao"])) {
	
		$var=0;

		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["qtde_padrao"]!="")) {
			inicia_transacao();
			
			/*$result_apaga= mysql_query("delete from fi_clientes_itens_cedidos_padrao
										where id_empresa= '". $_SESSION["id_empresa"] ."'
										and   id_cliente = '". $_POST["id_cliente"] ."'
										");
			if (!$result_apaga) $var++;
			*/
			
			$result1= mysql_query("insert into fi_clientes_itens_cedidos_padrao (id_empresa, id_cliente, tipo_item_cedido,
																				qtde_padrao, data_qtde_padrao, id_usuario)
									values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
											'". $_POST["tipo_item_cedido"] ."', '". $_POST["qtde_padrao"] ."', '". formata_data($_POST["data_qtde_padrao"]) ."',
											'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
		
			finaliza_transacao($var);
			$msg= $var;
				
			$pagina= "financeiro/cliente_item_cedido";
			require_once("index2.php");
			//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
		}
		else echo "Faltam dados!";
	
	}//form
	
	if (isset($_GET["formContrato"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["contrato"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into fi_contratos (id_empresa, contrato, data_contrato, pesagem_cliente, tipo_contrato, status_contrato, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["contrato"] ."',
										'". formata_data($_POST["data_contrato"]) ."', '". $_POST["pesagem_cliente"] ."',
										'". $_POST["tipo_contrato"] ."', '1', '". $_SESSION["id_usuario"] ."'
										) ") or die(mysql_error());
				if (!$result1) $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "financeiro/contrato_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update fi_contratos set
										contrato= '". ($_POST["contrato"]) ."',
										data_contrato= '". formata_data($_POST["data_contrato"]) ."',
										pesagem_cliente= '". $_POST["pesagem_cliente"] ."',
										tipo_contrato= '". $_POST["tipo_contrato"] ."'
										where id_contrato = '". $_POST["id_contrato"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/contrato_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formEquipamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["equipamento"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select * from op_equipamentos
											where codigo = '". strtoupper($_POST["codigo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {
					
					$result1= mysql_query("insert into op_equipamentos (id_empresa, codigo, codigo_patrimonial, equipamento, id_cliente_equipamento, tipo_equipamento, ocupado) values
											('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["codigo"]) ."', '". strtoupper($_POST["codigo_patrimonial"]) ."',
											'". strtoupper($_POST["equipamento"]) ."', '". $_POST["id_cliente_equipamento"] ."', '". $_POST["tipo_equipamento"] ."', '0') ") or die(mysql_error());
					if (!$result1) $var++;
					
				} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "op/equipamento_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				$result_pre= mysql_query("select * from op_equipamentos
											where codigo = '". strtoupper($_POST["codigo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_equipamento <> '". $_POST["id_equipamento"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update op_equipamentos set
											codigo= '". strtoupper($_POST["codigo"]) ."',
											codigo_patrimonial= '". strtoupper($_POST["codigo_patrimonial"]) ."',
											equipamento= '". strtoupper($_POST["equipamento"]) ."',
											id_cliente_equipamento= '". $_POST["id_cliente_equipamento"] ."',
											tipo_equipamento= '". $_POST["tipo_equipamento"] ."'
											where id_equipamento = '". $_POST["id_equipamento"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "op/equipamento_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formEquipamentoTipo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["equipamento_tipo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into op_equipamentos_tipos (id_empresa, equipamento_tipo) values
										('". $_SESSION["id_empresa"] ."',
										'". $_POST["equipamento_tipo"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
					
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "op/equipamento_tipo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update op_equipamentos_tipos set
										equipamento_tipo= '". $_POST["equipamento_tipo"] ."'
										where id_equipamento_tipo = '". $_POST["id_equipamento_tipo"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "op/equipamento_tipo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formVeiculo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["veiculo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into op_veiculos (id_empresa, veiculo, placa, chassi, cod_cor,
																	motor, peso_bruto, entre_eixos, codigo, tipo_padrao, status_veiculo) values
											('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["veiculo"]) ."',
											 '". strtoupper($_POST["placa"]) ."', '". strtoupper($_POST["chassi"]) ."',
											 '". strtoupper($_POST["cod_cor"]) ."', '". strtoupper($_POST["motor"]) ."',
											 '". formata_valor($_POST["peso_bruto"]) ."', '". strtoupper($_POST["entre_eixos"]) ."',
											 '". $_POST["codigo"] ."', '". $_POST["tipo_padrao"] ."', '1' ) ") or die(mysql_error());
					if (!$result1) $var++;
				} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/veiculo_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update op_veiculos set
											veiculo= '". strtoupper($_POST["veiculo"]) ."',
											placa = '". strtoupper($_POST["placa"]) ."',
											chassi = '". strtoupper($_POST["chassi"]) ."',
											cod_cor = '". strtoupper($_POST["cod_cor"]) ."',
											peso_bruto = '". formata_valor($_POST["peso_bruto"]) ."',
											entre_eixos = '". strtoupper($_POST["entre_eixos"]) ."',
											motor = '". strtoupper($_POST["motor"]) ."',
											codigo= '". $_POST["codigo"] ."',
											tipo_padrao= '". $_POST["tipo_padrao"] ."'
											where id_veiculo = '". $_POST["id_veiculo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/veiculo_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formVistoriaItem"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["item"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				
				$result1= mysql_query("insert into tr_vistorias_itens (id_empresa, item, ordem, status_item, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". ($_POST["item"]) ."',
										 '". ($_POST["ordem"]) ."', '1', '". $_SESSION["id_usuario"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
			
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=transporte/vistoria_item_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
			
				inicia_transacao();
				
				$result1= mysql_query("update tr_vistorias_itens set
										item= '". ($_POST["item"]) ."',
										ordem = '". ($_POST["ordem"]) ."'
										where id_item = '". $_POST["id_item"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				header("location: ./?pagina=transporte/vistoria_item_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formProducaoAjusteFuncionarios"])) {
		if ($_SESSION["id_empresa"]!="") {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["indexador"][$i]!="") {
				$result_pre[$i]= mysql_query("select * from op_limpa_producao_funcionarios
												where id_departamento = '". $_POST["id_departamento"] ."'
												and   data = '". $_POST["data"][$i] ."'
												and   turno = '". $_POST["turno"][$i] ."'
												and   qtde_horas = '". $_POST["qtde_horas"][$i] ."'
												") or die(mysql_error());
				
				$linhas_pre[$i]= mysql_num_rows($result_pre[$i]);
				
				if ($linhas_pre[$i]==0) {
					
					//if (($_POST["qtde_funcionarios"][$i]!="") && ($_POST["qtde_funcionarios"][$i]!="0")) {
						$result1[$i]= mysql_query("insert into op_limpa_producao_funcionarios
													(id_empresa, id_departamento, data, turno, qtde_horas, qtde_funcionarios, id_usuario) values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_departamento"] ."', '". $_POST["data"][$i] ."',
													'". $_POST["turno"][$i] ."', '". $_POST["qtde_horas"][$i]  ."',
													'". $_POST["qtde_funcionarios"][$i] ."', '". $_SESSION["id_usuario"] ."')
													") or die(mysql_error());
						if (!$result1[$i]) $var++;
					//}
				
				}
				else {
					
					$result1[$i]= mysql_query("update op_limpa_producao_funcionarios set
												qtde_funcionarios= '". ($_POST["qtde_funcionarios"][$i]) ."'
												where id_departamento = '". $_POST["id_departamento"] ."'
												and   data = '". $_POST["data"][$i] ."'
												and   turno = '". $_POST["turno"][$i] ."'
												and   qtde_horas = '". $_POST["qtde_horas"][$i] ."'
												") or die(mysql_error());
					if (!$result1[$i]) $var++;
				}
				
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			header("location: ./?pagina=op/producao_quantitativo_ajuste&data1=". $_POST["data1"] ."&data2=". $_POST["data2"] ."&id_departamento=". $_POST["id_departamento"] ."&msg=". $msg);
			
			// fazer a média --------------------------------------------------------------------------
		}//fim teste variáveis
	}
	
	if (isset($_GET["formProducaoAjuste"])) {
		if ($_SESSION["id_empresa"]!="") {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["id_funcionario"][$i]!="") {
				$result_pre[$i]= mysql_query("select * from rh_ponto_producao_funcionarios
												where id_funcionario = '". $_POST["id_funcionario"][$i] ."'
												and   vale_dia = '". $_POST["vale_dia"][$i] ."'
												and   hora_base = '". $_POST["h"][$i] ."'
												and   id_departamento = '". $_POST["id_departamento"] ."'
												") or die(mysql_error());
				
				$linhas_pre[$i]= mysql_num_rows($result_pre[$i]);
				
				if ($linhas_pre[$i]==0) {
					if ($_POST["trabalhou"][$i]=="1") {
						$result1[$i]= mysql_query("insert into rh_ponto_producao_funcionarios
													(id_empresa, id_departamento, vale_dia, id_funcionario, id_turno_index, hora_base, trabalhou, original) values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_departamento"] ."', '". $_POST["vale_dia"][$i] ."', '". $_POST["id_funcionario"][$i] ."', '". $_POST["id_turno_index"][$i]  ."',
													'". $_POST["h"][$i] ."', '". $_POST["trabalhou"][$i] ."', '0')
													") or die(mysql_error());
						if (!$result1[$i]) $var++;
					}
				}
				else {
					$rs_pre[$i]= mysql_fetch_object($result_pre[$i]);
					
					if (($rs_pre[$i]->original=="1") && ($rs_pre[$i]->trabalhou!=$_POST["trabalhou"][$i])) $str_add= ", original = '0' ";
					else $str_add="";
					
					$result1[$i]= mysql_query("update rh_ponto_producao_funcionarios set
												trabalhou= '". ($_POST["trabalhou"][$i]) ."'
												$str_add
												where vale_dia = '". $_POST["vale_dia"][$i] ."'
												and   id_funcionario = '". $_POST["id_funcionario"][$i] ."'
												/* and   id_turno_index = '". $_POST["id_turno_index"][$i] ."' */
												and   hora_base = '". $_POST["h"][$i] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_departamento = '". $_POST["id_departamento"] ."'
												") or die(mysql_error());
					if (!$result1[$i]) $var++;
				}
				
				if ($_POST["trabalha"][$i]!="") {
					
				}
			
				$i++;
			}
			
			// fazer a média --------------------------------------------------------------------------
			
			if ( ($_POST["data1"]!="") && ($_POST["data2"]!="") ) {
				$data1= $_POST["data1"];
				$data2= $_POST["data2"];
			}
			else {
				if ( ($_GET["data1"]!="") && ($_GET["data2"]!="") ) {
					$data1= $_GET["data1"];
					$data2= $_GET["data2"];
				}
			}
			
			if ( ($data1!="") && ($data2!="") ) {
				$data1f= $data1; $data1= formata_data_hifen($data1);
				$data2f= $data2; $data2= formata_data_hifen($data2);
				
				$data1= soma_data($data1, -1, 0, 0);
				$data2= soma_data($data2, -1, 0, 0);
				
				$data1_mk= faz_mk_data($data1);
				$data2_mk= faz_mk_data($data2);
				
				//$primeiro_dia_mes_mk= mktime(0, 0, 0, $periodo[0], 1, $periodo[1]);
				$primeiro_dia_mes_mk= $data1_mk;
				
				$id_dia_primeiro_dia= date("w", $primeiro_dia_mes_mk);
				
				$i_inicio= 1;
				
				// 10/10/2010
				
				$primeiro_dia_periodo_mk= $primeiro_dia_mes_mk;
				
				//echo substr($data1f, 6, 4);//date("d/m/Y", $primeiro_dia_periodo_mk); die();
				
				$ultimo_dia_periodo_mk= $data2_mk+(86400*2);
			}
			
			$diferenca = ceil(($ultimo_dia_periodo_mk-$primeiro_dia_periodo_mk)/86400);
		
			for ($i=$i_inicio; $i<$diferenca; $i++) {
				$calculo_data= $data1_mk+(86400*$i);
				$amanha_mk= mktime(0, 0, 0, $periodo[0], $i+1, $periodo[1]);
				$id_dia= date("w", $calculo_data);
				$data_formatada= date("d/m/Y", $calculo_data);
				$data= date("Y-m-d", $calculo_data);
				$amanha= soma_data($data, 1, 0, 0);
				
				if (($id_dia==0) || ($id_dia==6)) {
					
					$total_plantao= 0;
					
					for ($hi=6; $hi<30; $hi++) {
						
						if ($hi<24) $h=$hi;
						else $h= $hi-24;
						
						$result_ajuste= mysql_query("select count(id_funcionario) as total from rh_ponto_producao_funcionarios
															where vale_dia = '". $data ."'
															and   id_turno_index = '0'
															and   hora_base = '". $h ."'
															and   trabalhou = '1'
															and   id_departamento = '". $_POST["id_departamento"] ."'
															");				
						$rs_ajuste= mysql_fetch_object($result_ajuste);
						
						//$total[$h]= $rs_ajuste->total;
						
						$total_plantao+=$rs_ajuste->total;
					}
					
					$media= $total_plantao/24;
					
					$result_atualiza= mysql_query("update rh_ponto_producao set media = '". $media."'
													where vale_dia= '". $data ."'
													and   id_turno_index = '0'
													and   id_departamento = '". $_POST["id_departamento"] ."'
													");
				}
				else {
					
					$total_normal= 0;
					
					for ($hi=6; $hi<30; $hi++) {
						
						if ($hi<24) $h=$hi;
						else $h= $hi-24;
						
						switch ($h) {
							case 0:
							case 1:
							case 2:
							case 3:
							case 4:
							case 5:
							$id_turno_index=4;
							break;
							case 6:
							case 7:
							case 8:
							case 9:
							case 10:
							case 11:
							$id_turno_index=1;
							break;
							case 12:
							case 13:
							case 14:
							case 15:
							case 16:
							case 17:
							$id_turno_index=2;
							break;
							case 18:
							case 19:
							case 20:
							case 21:
							case 22:
							case 23:
							$id_turno_index=3;
							break;	
						}
						
						$result_ajuste= mysql_query("select count(id_funcionario) as total from rh_ponto_producao_funcionarios
															where vale_dia = '". $data ."'
															and   id_turno_index = '". $id_turno_index ."'
															and   hora_base = '". $h ."'
															and   trabalhou = '1'
															and   id_departamento = '". $_POST["id_departamento"] ."'
															");				
						$rs_ajuste= mysql_fetch_object($result_ajuste);
						
						//$total[$h]= $rs_ajuste->total;
						
						$total_normal+=$rs_ajuste->total;
						
						switch($h) {
							case 5:
							case 11:
							case 17:
							case 23:
							
							$media= $total_normal/6;
					
							$result_atualiza= mysql_query("update rh_ponto_producao set media = '". $media."'
															where vale_dia= '". $data ."'
															and   id_turno_index = '". $id_turno_index ."'
															and   id_departamento = '". $_POST["id_departamento"] ."'
															");
							
							$total_normal= 0;
							
							break;
						}
					}
				}
				
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			header("location: ./?pagina=op/producao_ajuste&data1=". $_POST["data1"] ."&data2=". $_POST["data2"] ."&id_departamento=". $_POST["id_departamento"] ."&msg=". $msg);
			
			//$pagina= "rh/escala";
			//require_once("index2.php");
		}//fim teste variáveis
	}
	
	if (isset($_GET["formPeca"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["peca"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select * from op_limpa_pecas
											where peca = '". $_POST["peca"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into op_limpa_pecas (id_empresa, peca, qtde_padrao_pacote, id_grupo, status_peca, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["peca"] ."', '". $_POST["qtde_padrao_pacote"] ."', '". $_POST["id_grupo"] ."',
											 '1', '". $_SESSION["id_usuario"] ."') ") or die(mysql_error());
					if (!$result1) $var++;
				} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "op/peca_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				$result_pre= mysql_query("select * from op_limpa_pecas
											where peca = '". $_POST["peca"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_peca <> '". $_POST["id_peca"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update op_limpa_pecas set
											peca= '". $_POST["peca"] ."',
											id_grupo= '". $_POST["id_grupo"] ."',
											qtde_padrao_pacote= '". $_POST["qtde_padrao_pacote"] ."'
											where id_peca = '". $_POST["id_peca"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "op/peca_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRelatorioColeta"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["qtde"]!="")) {
				
			inicia_transacao();
			$var=0;
			
			$result_pre= mysql_query("select * from op_coletas
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										order by num_coleta desc limit 1
										") or die(mysql_error());

			$rs_pre= mysql_fetch_object($result_pre);
			
			$proximo= $rs_pre->num_coleta+1;
			
			for ($i=$proximo; $i<($proximo+$_POST["qtde"]); $i++) {
				
				$result1= mysql_query("insert into op_coletas (id_empresa, num_coleta,
															   id_cliente, data_coleta, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $i ."',
										 '0', '0', '". $_SESSION["id_usuario"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
			}
			
			finaliza_transacao($var);
			$msg= $var;
			
			$pagina= "op/coleta_busca";
			require_once("index2.php");
		
		}//fim teste variáveis
	}
	
	if (isset($_GET["formProcesso"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["processo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select * from op_equipamentos_processos
											where codigo = '". strtoupper($_POST["codigo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   status_processo = '1'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into op_equipamentos_processos (id_empresa, codigo, processo, tempo, relave, carga_maxima, status_processo) values
											('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["codigo"]) ."',
											'". strtoupper($_POST["processo"]) ."', '". $_POST["tempo"] ."', '". $_POST["relave"] ."',
											'". formata_valor($_POST["carga_maxima"]) ."', '1') ") or die(mysql_error());
					if (!$result1) $var++;
				} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "op/processo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				$result_pre= mysql_query("select * from op_equipamentos_processos
											where codigo = '". strtoupper($_POST["codigo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_processo <> '". $_POST["id_processo"] ."'
											and   status_processo = '1'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update op_equipamentos_processos set
											codigo= '". strtoupper($_POST["codigo"]) ."',
											processo= '". strtoupper($_POST["processo"]) ."',
											tempo = '". $_POST["tempo"] ."',
											carga_maxima = '". formata_valor($_POST["carga_maxima"]) ."',
											relave = '". strtoupper($_POST["relave"]) ."'
											where id_processo = '". $_POST["id_processo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "op/processo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRemessa"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["data_remessa"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
					$num_remessa= pega_num_remessa(formata_data($_POST["data_remessa"]), $_SESSION["id_empresa"]);
					
					$result1= mysql_query("insert into op_suja_remessas (id_empresa, data_remessa, num_remessa, relave, hora_chegada, hora_inicio_descarga, hora_fim_descarga, id_usuario, id_veiculo, id_percurso) values
											('". $_SESSION["id_empresa"] ."', '". formata_data($_POST["data_remessa"]) ."', '". $num_remessa ."', '". formata_valor($_POST["relave"]) ."',
											'". formata_data($_POST["hora_chegada"]) ."', '". formata_data($_POST["hora_inicio_descarga"]) ."', '". formata_data($_POST["hora_fim_descarga"]) ."',
											'". $_SESSION["id_usuario"] ."', '". $_POST["id_veiculo"] ."', '". $_POST["id_percurso"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
				//} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/pesagem_suja&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update op_suja_remessas set
										data_remessa= '". formata_data($_POST["data_remessa"]) ."',
										num_remessa= '". $_POST["num_remessa"] ."',
										relave= '". formata_valor($_POST["relave"]) ."',
										hora_chegada= '". $_POST["hora_chegada"] ."',
										hora_inicio_descarga= '". $_POST["hora_inicio_descarga"] ."',
										hora_fim_descarga= '". $_POST["hora_fim_descarga"] ."',
										
										data_inicio_separacao= '". formata_data($_POST["data_inicio_separacao"]) ."',
										hora_inicio_separacao= '". $_POST["hora_inicio_separacao"] ."',
										
										data_fim_separacao= '". formata_data($_POST["data_fim_separacao"]) ."',
										hora_fim_separacao= '". $_POST["hora_fim_separacao"] ."',
										id_veiculo= '". $_POST["id_veiculo"] ."',
										id_percurso= '". $_POST["id_percurso"] ."'
										where id_remessa = '". $_POST["id_remessa"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				if ($_POST["origem"]=="r")
					header("location: ./?pagina=op/remessa_listar&msg=". $msg);
				else
					header("location: ./?pagina=op/separacao_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formSeparacaoHorario"])) {
		
		$sql= "update op_suja_remessas_separacoes set
					data_separacao = '". formata_data($_POST["data_separacao"]) ."',
					hora_separacao = '". $_POST["hora_separacao"] ."'
					where id_empresa = '". $_SESSION["id_empresa"] ."'
					and   id_separacao = '". $_POST["id_separacao"] ."'
					";
		
		$result= mysql_query($sql) or die(mysql_error());
		
		$pagina="op/separacao_listar";
		require_once("index2.php");
		
		/*echo "<script language='javascript' type='text/javascript'>;";
		if ($result) {
			echo "alert('Dados armazenados com sucesso!');";
			echo "fechaDiv('tela_aux');";
		}
		else {
			echo "alert('Não foi possível atualizar, tente novamente!');";
		}
		echo "</script>";*/
	}
	
	if (isset($_GET["formPesagemSuja"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["peso"]!="") && ($_POST["id_remessa"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$i=0;
				while ($_POST["peso"][$i]!="") {
					$result1= mysql_query("insert into op_suja_pesagem (id_empresa, id_remessa, id_cliente, data_pesagem, hora_pesagem, peso, goma, hampers, id_turno, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_cliente"] ."',
											'". formata_data($_POST["data_pesagem"]) ."', '". $_POST["hora_pesagem"] ."',
											'". formata_valor($_POST["peso"][$i]) ."', '". $_POST["goma"] ."', '". $_POST["hampers"][$i] ."', '". $_SESSION["id_turno_sessao"] ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$i++;
				}
				//} else $var++;
				
				$result_remessa= mysql_query("update  op_suja_remessas
											 	set   hora_fim_descarga= '". date("H:i:s") ."'
												where id_remessa= '". $_POST["id_remessa"] ."'
												");
				if (!$result_remessa) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
								
				header("location: ./?pagina=op/pesagem_suja&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update op_suja_pesagem set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											data_pesagem= '". formata_data($_POST["data_pesagem"]) ."',
											hora_pesagem = '". $_POST["hora_pesagem"] ."',
											peso = '". formata_valor($_POST["peso"]) ."',
											goma= '". $_POST["goma"] ."',
											hampers= '". $_POST["hampers"] ."'
											where id_pesagem = '". $_POST["id_pesagem"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/pesagem_suja_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formLavagem"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["peso_total"]!="") && ($_POST["id_remessa"]!="") && ($_POST["id_processo"]!="") && ($_POST["id_equipamento"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				
					$tempo_processo= pega_processo_tempo($_POST["id_processo"]);
				
					$result1= mysql_query("insert into op_suja_lavagem
											(id_empresa, id_remessa, id_peca, data_lavagem, hora_lavagem,
											id_equipamento, id_processo, tempo_processo, peso_total,
											id_funcionario, id_turno, id_usuario, obs) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_peca"] ."',
											'". formata_data($_POST["data_lavagem"]) ."', '". $_POST["hora_lavagem"] ."',
											'". $_POST["id_equipamento"] ."', '". $_POST["id_processo"] ."', '". $tempo_processo ."',
											'". formata_valor($_POST["peso_total"]) ."',
											'". $_POST["id_funcionario"] ."', '". $_SESSION["id_turno_sessao"] ."', '". $_SESSION["id_usuario"] ."', '". $_POST["obs"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$id_lavagem= mysql_insert_id();
					
					if (($_POST["data_fim_lavagem"]=="") && ($_POST["hora_fim_lavagem"]=="")) {
						$result2= mysql_query("update op_equipamentos
												set   ocupado= '1'
												where id_equipamento = '". $_POST["id_equipamento"] ."'
												") or die(mysql_error());
						if (!$result2) $var++;
					}
					
					$i=0;
					while ($_POST["id_tipo_roupa"][$i]) {
						$result4[$i]= mysql_query("insert into op_suja_lavagem_pecas (id_empresa, id_lavagem,
																					   id_tipo_roupa, qtde_pecas, id_cliente, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $id_lavagem ."',
												 '". $_POST["id_tipo_roupa"][$i] ."', '". $_POST["qtde_pecas"][$i] ."',
												 '". $_POST["id_cliente_peca"][$i] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
						if (!$result4[$i]) $var++;
						
						$i++;
					}
					
					$i= 0;
					while ($_POST["id_cesto"][$i]) {
						$result3= mysql_query("insert into op_suja_lavagem_cestos
											  	(id_empresa, id_lavagem, id_cesto, id_cliente, peso, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $id_lavagem ."', '". $_POST["id_cesto"][$i] ."',
												'". $_POST["id_cliente"][$i] ."', '". formata_valor($_POST["peso"][$i]) ."',
												'". $_SESSION["id_usuario"] ."'
												) ") or die(mysql_error());
						if (!$result3) $var++;
						
						$i++;
					}
				//} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/lavagem&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$tempo_processo= pega_processo_tempo($_POST["id_processo"]);
					
					$result1= mysql_query("update op_suja_lavagem set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											id_peca = '". $_POST["id_peca"] ."',
											data_lavagem= '". formata_data($_POST["data_lavagem"]) ."',
											hora_lavagem = '". $_POST["hora_lavagem"] ."',
											
											data_fim_lavagem= '". formata_data($_POST["data_fim_lavagem"]) ."',
											hora_fim_lavagem = '". $_POST["hora_fim_lavagem"] ."',
											
											id_funcionario = '". $_POST["id_funcionario"] ."',
											id_equipamento = '". $_POST["id_equipamento"] ."',
											id_processo = '". $_POST["id_processo"] ."',
											tempo_processo= '". $tempo_processo ."',
											peso1 = '". formata_valor($_POST["peso1"]) ."',
											peso2 = '". formata_valor($_POST["peso2"]) ."',
											peso_total = '". formata_valor($_POST["peso_total"]) ."'
											where id_lavagem = '". $_POST["id_lavagem"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					if (($_POST["data_fim_lavagem"]!="") && ($_POST["hora_fim_lavagem"]!="")) {
						$result2= mysql_query("update op_equipamentos
												set   ocupado= '0'
												where id_equipamento = '". $_POST["id_equipamento"] ."'
												") or die(mysql_error());
						if (!$result2) $var++;
					}
					
					$result_limpa1= mysql_query("delete from op_suja_lavagem_pecas
											   	where id_lavagem= '". $_POST["id_lavagem"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					
					$i=0;
					while ($_POST["id_tipo_roupa"][$i]) {
						$result4[$i]= mysql_query("insert into op_suja_lavagem_pecas (id_empresa, id_lavagem,
																					   id_tipo_roupa, qtde_pecas, id_cliente, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_lavagem"] ."',
												 '". $_POST["id_tipo_roupa"][$i] ."', '". $_POST["qtde_pecas"][$i] ."',
												 '". $_POST["id_cliente_peca"][$i] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
						if (!$result4[$i]) $var++;
						
						$i++;
					}
					
					$result_limpa= mysql_query("delete from op_suja_lavagem_cestos
											   	where id_lavagem= '". $_POST["id_lavagem"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					
					
					
					$i= 0;
					while ($_POST["id_cesto"][$i]) {
						$result3= mysql_query("insert into op_suja_lavagem_cestos
											  	(id_empresa, id_lavagem, id_cesto, id_cliente, peso, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $id_lavagem ."', '". $_POST["id_cesto"][$i] ."',
												'". $_POST["id_cliente"][$i] ."', '". formata_valor($_POST["peso"][$i]) ."',
												'". $_SESSION["id_usuario"] ."'
												) ") or die(mysql_error());
						if (!$result3) $var++;
						
						$i++;
					}
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				if ($_SESSION["id_usuario"]==13) header("location: ./?pagina=op/lavagem&acao=e&msg=". $msg ."&id_lavagem=". $_POST["id_lavagem"]);
				else header("location: ./?pagina=op/lavagem_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
		else echo "Faltam dados!";
	}
	
	if (isset($_GET["formTrocaQuimico"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["data_troca"]!="") && ($_POST["num_galao"]!="") && ($_POST["id_quimico"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
					
					$qtde_funcionarios= pega_funcionarios_trabalhando(1);
				
					$result1= mysql_query("insert into op_suja_quimicos_trocas (id_empresa, data_troca, hora_troca, num_galao, id_quimico, qtde, id_funcionario, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". formata_data($_POST["data_troca"]) ."', '". $_POST["hora_troca"] ."',
											'". $_POST["num_galao"] ."', '". $_POST["id_quimico"] ."',
											'". formata_valor($_POST["qtde"]) ."', '". $_POST["id_funcionario"] ."',
											'". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
				//} else $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/quimico_listar&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update op_suja_quimicos_trocas set
											data_troca= '". formata_data($_POST["data_troca"]) ."',
											hora_troca = '". $_POST["hora_troca"] ."',
											num_galao = '". $_POST["num_galao"] ."',
											id_quimico = '". $_POST["id_quimico"] ."',
											qtde = '". formata_valor($_POST["qtde"]) ."',
											id_funcionario= '". $_POST["id_funcionario"] ."'
											where id_troca_quimico = '". $_POST["id_troca_quimico"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/quimico_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formPesagemLimpaExtraRotina"])) {
		
		inicia_transacao();
		$var=0;
		
		$result= mysql_query("select * from op_limpa_pesagem_pecas, op_limpa_pesagem
								where op_limpa_pesagem_pecas.id_pesagem_peca = '". $_POST["id_pesagem_peca"] ."'
								and   op_limpa_pesagem.id_pesagem = op_limpa_pesagem_pecas.id_pesagem
								") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$result_pesagem_pecas= mysql_query("select count(*) as total from op_limpa_pesagem_pecas
											where id_pesagem = '". $rs->id_pesagem ."'
											") or die(mysql_error());
		$rs_pesagem_pecas= mysql_fetch_object($result_pesagem_pecas);
		
		if ( ((int)$_POST[num_pacotes]==0) && ((int)$_POST[pacotes_sobra]==0) && ((int)$_POST[qtde_pecas_sobra]==0) )
			die ("Você precisa dizer a quantidade que está movendo. Tudo zero não pode.<br><br> Pressione ESC para voltar.");
			
		if ( ((int)$_POST[num_pacotes]<0) || ((int)$_POST[pacotes_sobra]<0) || ((int)$_POST[qtde_pecas_sobra]<0) )
			die ("Valor negativo não pode.<br><br> Pressione ESC para voltar.");
		if ((int)$_POST[num_pacotes]>$rs->num_pacotes)
			die ("Número de pacotes maior que a pesagem original.<br><br> Pressione ESC para voltar.");
		if ((int)$_POST[pacotes_sobra]>$rs->pacotes_sobra)
			die ("Número de pacotes c/ sobra maior que a pesagem original.<br><br> Pressione ESC para voltar.");
		if ((int)$_POST[qtde_pecas_sobra]>$rs->qtde_pecas_sobra)
			die ("Número de peças com sobra maior que a pesagem original.<br><br> Pressione ESC para voltar.");
		if ((float)formata_valor($_POST[peso])>$rs->peso)
			die ("Peso maior que a pesagem original.<br><br> Pressione ESC para voltar.");
		if ((float)formata_valor($_POST[peso])==0)
			die ("Peso não pode ser zero.<br><br> Pressione ESC para voltar.");
						
		//se tem só uma peça na pesagem, só manda de um lugar para o outro
		if ($rs_pesagem_pecas->total==1) {
			
			//se só estiver movendo de um lado para outro
			if ( 
				((int)$_POST[num_pacotes]==$rs->num_pacotes) &&
				((int)$_POST[pacotes_sobra]==$rs->pacotes_sobra) &&
				((int)$_POST[qtde_pecas_sobra]==$rs->qtde_pecas_sobra) &&
				((float)formata_valor($_POST[peso])==$rs->peso)
				)
				{
					$result_atualiza= mysql_query("update op_limpa_pesagem
													set extra = '". $_POST["extra"] ."',
													peso = '". formata_valor($_POST["peso"]) ."',
													data_hora_pesagem = '". $rs->data_pesagem ." ". $rs->hora_pesagem ."'
													where id_pesagem ='". $rs->id_pesagem ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
					if (!$result_atualiza) $var++;
				}
				//mexeu no número de peças e/ou peso
				else {
					
					//calcula a diferença
					$dif_num_pacotes= $rs->num_pacotes-(int)$_POST[num_pacotes];
					$dif_pacotes_sobra= $rs->pacotes_sobra-(int)$_POST[pacotes_sobra];
					$dif_qtde_pecas_sobra= $rs->qtde_pecas_sobra-(int)$_POST[qtde_pecas_sobra];
					$dif_peso= $rs->peso-(float)formata_valor($_POST[peso]);
					
					//atualiza a pesagem atual com a diferença do peso
					$result_atualiza1= mysql_query("update op_limpa_pesagem
													set peso = '". $dif_peso ."',
													data_hora_pesagem = '". $rs->data_pesagem ." ". $rs->hora_pesagem ."'
													where id_pesagem ='". $rs->id_pesagem ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
					if (!$result_atualiza1) $var++;
					
					$result_atualiza2= mysql_query("update op_limpa_pesagem_pecas
													set num_pacotes = '". $dif_num_pacotes ."',
													pacotes_sobra = '". $dif_pacotes_sobra ."',
													qtde_pecas_sobra = '". $dif_qtde_pecas_sobra ."'
													where id_pesagem = '". $rs->id_pesagem ."'
													and   id_pesagem_peca = '". $rs->id_pesagem_peca ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
					if (!$result_atualiza2) $var++;
					
					//insere novo registro de pesagem com os dados digitados no ato
					
					$result_insere1= mysql_query("insert into op_limpa_pesagem
													(id_empresa, id_cliente, data_pesagem, hora_pesagem, data_hora_pesagem, id_grupo, peso, costura, goma, roupa_alheia, id_usuario, id_turno, extra)
													values
													('". $_SESSION["id_empresa"] ."', '". $rs->id_cliente ."',
													'". $rs->data_pesagem ."', '". $rs->hora_pesagem ."', '". $rs->data_hora_pesagem ."', '". $rs->id_grupo ."', '". formata_valor($_POST["peso"]) ."',
													'". $rs->costura ."', '". $rs->goma ."', '". $rs->roupa_alheia ."', '". $_SESSION["id_usuario"] ."', '". $rs->id_turno ."', '". $_POST["extra"] ."' 
													)
													") or die(mysql_error());
					if (!$result_insere1) $var++;
					
					$id_pesagem= mysql_insert_id();
					
					$result_insere2= mysql_query("insert into op_limpa_pesagem_pecas
													(id_empresa, id_pesagem, id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_pesagem ."',
													'". $rs->id_tipo_roupa ."', '". $_POST[num_pacotes] ."', '". $rs->qtde_pacote ."', '". $_POST[qtde_pecas_sobra] ."', 
													'". $_POST[pacotes_sobra] ."', '". $_SESSION["id_usuario"] ."' 
													)
													") or die(mysql_error());
					if (!$result_insere2) $var++;
					
				}
		}
		else {
			
			//atualiza a pesagem atual com a diferença de peso...
			$result_atualiza1= mysql_query("update op_limpa_pesagem
											set peso = peso - ". formata_valor($_POST["peso"]) ."
											where id_pesagem ='". $rs->id_pesagem ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
			if (!$result_atualiza1) $var++;
			
			//aqui insere a pesagem/peça que está manipulando
			$result_insere1= mysql_query("insert into op_limpa_pesagem
											(id_empresa, id_cliente, data_pesagem, hora_pesagem, data_hora_pesagem, id_grupo, peso, costura, goma, roupa_alheia, id_usuario, id_turno, extra)
											values
											('". $_SESSION["id_empresa"] ."', '". $rs->id_cliente ."',
											'". $rs->data_pesagem ."', '". $rs->hora_pesagem ."', '". $rs->data_hora_pesagem ."', '". $rs->id_grupo ."', '". formata_valor($_POST["peso"]) ."',
											'". $rs->costura ."', '". $rs->goma ."', '". $rs->roupa_alheia ."', '". $_SESSION["id_usuario"] ."', '". $rs->id_turno ."', '". $_POST[extra] ."' 
											)
											") or die(mysql_error());
			if (!$result_insere1) $var++;
			
			$id_pesagem= mysql_insert_id();
			
			//se só estiver movendo de um lado para outro, nao mudou valores de pacote e peça…
			if ( 
				((int)$_POST[num_pacotes]==$rs->num_pacotes) &&
				((int)$_POST[pacotes_sobra]==$rs->pacotes_sobra) &&
				((int)$_POST[qtde_pecas_sobra]==$rs->qtde_pecas_sobra)
				)
			{
				
				$result_insere2= mysql_query("insert into op_limpa_pesagem_pecas
												(id_empresa, id_pesagem, id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra, id_usuario)
												values
												('". $_SESSION["id_empresa"] ."', '". $id_pesagem ."',
												'". $rs->id_tipo_roupa ."', '". $rs->num_pacotes ."', '". $rs->qtde_pacote ."', '". $rs->qtde_pecas_sobra ."', 
												'". $rs->pacotes_sobra ."', '". $_SESSION["id_usuario"] ."' 
												)
												") or die(mysql_error());
				if (!$result_insere2) $var++;
				
				//aqui retira a pesagem, pois já inseriu uma nova...
				$result_deleta1= mysql_query("delete from op_limpa_pesagem_pecas
												where id_pesagem = '". $rs->id_pesagem ."'
												and   id_pesagem_peca = '". $rs->id_pesagem_peca ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
				if (!$result_deleta1) $var++;
				
			}
			else {
				
				//calcula a diferença
				$dif_num_pacotes= $rs->num_pacotes-(int)$_POST[num_pacotes];
				$dif_pacotes_sobra= $rs->pacotes_sobra-(int)$_POST[pacotes_sobra];
				$dif_qtde_pecas_sobra= $rs->qtde_pecas_sobra-(int)$_POST[qtde_pecas_sobra];
				$dif_peso= $rs->peso-(float)formata_valor($_POST[peso]);
				
				//atualiza a pesagem/peça atual com o que ficou de diferença
				$result_atualiza2= mysql_query("update op_limpa_pesagem_pecas
												set num_pacotes = '". $dif_num_pacotes ."',
												pacotes_sobra = '". $dif_pacotes_sobra ."',
												qtde_pecas_sobra = '". $dif_qtde_pecas_sobra ."'
												where id_pesagem = '". $rs->id_pesagem ."'
												and   id_pesagem_peca = '". $rs->id_pesagem_peca ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
				if (!$result_atualiza2) $var++;
				
				//insere as peças que horam digitadas pelo usuário
				$result_insere2= mysql_query("insert into op_limpa_pesagem_pecas
												(id_empresa, id_pesagem, id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra, id_usuario)
												values
												('". $_SESSION["id_empresa"] ."', '". $id_pesagem ."',
												'". $rs->id_tipo_roupa ."', '". $_POST[num_pacotes] ."', '". $rs->qtde_pacote ."', '". $_POST[qtde_pecas_sobra] ."', 
												'". $_POST[pacotes_sobra] ."', '". $_SESSION["id_usuario"] ."' 
												)
												") or die(mysql_error());
				if (!$result_insere2) $var++;
				
			}
			
			
		}
		
		/*$result_pesagem= mysql_query("select * from op_limpa_pesagem
									where id_pesagem = '". $rs->id_pesagem ."'
									") or die(mysql_error());
		$rs_pesagem= mysql_fetch_object($result_pesagem);
		*/
		
		finaliza_transacao($var);
		
		if (!$var) {
		?>
		<script language="javascript" type="text/javascript">jQuery("#item_roupa_<?=$_POST["id_pesagem_peca"];?>").css("background-color", "#990000"); fechaDiv('tela_aux');
		
		var resposta= confirm('Atualizar relação de pesagens agora?');
		
		if (resposta) {
			window.top.location.href='./?pagina=op/pesagem_limpa_cliente_listar&id_cliente=<?= $rs->id_cliente ?>';
		}
		</script>
		<?
		}
	}
	
	if (isset($_GET["formPesagemLimpa"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["peso"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$id_grupo= pega_id_grupo_da_peca($_POST["id_tipo_roupa"][0]);
				
				//ainda não foi pesado, insere 2 vezes...
				/*if ($_POST["extra_modo"]==2) $limite_extra=2;
				else */$limite_extra=1;
				
				for ($j=0; $j<$limite_extra; $j++) {
					
					if (($limite_extra==1) && ($_POST["extra"]==1)) $extra_aqui=1;
					else {
						if ($j==0) $extra_aqui=0;
						else $extra_aqui=1;
					}
					
					if ($_POST["data_pesagem"]!="") $data_pesagem= $_POST["data_pesagem"];
					else $data_pesagem= date("d/m/Y");
					
					if ($_POST["hora_pesagem"]!="") $hora_pesagem= $_POST["hora_pesagem"];
					else $hora_pesagem= date("H:i:s");
					
					if ($_SESSION["id_turno_sessao"]!="-3")
						$id_turno_calcula= pega_turno_pelo_horario(formata_data_hifen($data_pesagem) ." ". $hora_pesagem);
					else
						$id_turno_calcula= $_SESSION["id_turno_sessao"];
					
					//faz a busca por percursos, se houver um percurso com data superior à pesagem atual, o sistema joga o horário deste pesagem imediatamente
					//após o horáro do percurso, para erro humano não influenciar nas entregas
					
					$result_percurso= mysql_query("select * from tr_percursos_clientes, tr_percursos
													where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' )
													and   tr_percursos.data_hora_percurso > '". formata_data_hifen($_POST["data_pesagem"]) ." ". $_POST["hora_pesagem"] ."'
													and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente"] ."'
													order by tr_percursos.data_hora_percurso desc limit 1
													") or die(mysql_error());
													
					$linhas_percurso= mysql_num_rows($result_percurso);
					
					//se realmete já existe um percurso cadastrado com horário superior ao da pesagem, ele encerra e coloca as pesagens com data maior
					if ($linhas_percurso>0) {
						$rs_percurso= mysql_fetch_object($result_percurso);
						
						//soma um minuto à hora do percurso, para associar à pesagem
						$data_hora_pesagem= soma_data_hora($rs_percurso->data_hora_percurso, 0, 0, 0, 0, 1, 0);
						
						//echo $data_hora_pesagem; die();
						
						$data_hora_pesagem= explode(" ", $data_hora_pesagem, 2);
						
						$data_pesagem= $data_hora_pesagem[0];
						$hora_pesagem= $data_hora_pesagem[1];
					}
					else {
						$data_pesagem= formata_data_hifen($_POST["data_pesagem"]);
						$hora_pesagem= $_POST["hora_pesagem"];
					}
					
					$result1= mysql_query("insert into op_limpa_pesagem (id_empresa, id_remessa, id_cliente, id_grupo,
																		 data_pesagem, hora_pesagem, data_hora_pesagem, peso,
																		 costura, goma, roupa_alheia, id_turno, extra, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_cliente"] ."', '$id_grupo',
											'". $data_pesagem ."', '". $hora_pesagem ."', '". $data_pesagem ." ". $hora_pesagem ."', '". formata_valor($_POST["peso"]) ."',
											'". $_POST["costura"] ."', '". $_POST["goma"] ."', '". $_POST["roupa_alheia"] ."', '". $id_turno_calcula ."', '". $extra_aqui ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$id_pesagem= mysql_insert_id();
					
					$i=0;
					while ($_POST["id_tipo_roupa"][$i]) {
						$qtde_pacote_aqui= pega_qtde_padrao_pacote($_POST["id_tipo_roupa"][$i]);
						
						$result2[$i]= mysql_query("insert into op_limpa_pesagem_pecas (id_empresa, id_pesagem,
																					   id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra) values
												('". $_SESSION["id_empresa"] ."', '". $id_pesagem ."',
												 '". $_POST["id_tipo_roupa"][$i] ."', '". $_POST["num_pacotes"][$i] ."',
												 '". $qtde_pacote_aqui ."', '". $_POST["qtde_pecas_sobra"][$i] ."', '". $_POST["pacotes_sobra"][$i] ."'
												 ) ") or die(mysql_error());
						if (!$result2[$i]) $var++;
						
						$i++;
					}
					
					if ($i==0) $var++;
				}

				finaliza_transacao($var);
				$msg= $var;
				
				if ($_POST["extra"]==1) $url_redirect= "&extra=1";
				else $url_redirect= "";
				
				header("location: ./?pagina=op/pesagem_limpa&acao=i&msg=". $msg . $url_redirect);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$id_grupo= pega_id_grupo_da_peca($_POST["id_tipo_roupa"][0]);
					
					if ($_SESSION["id_turno_sessao"]=="") {
						$id_turno_calcula= @pega_turno_pelo_horario(formata_data($_POST["data_pesagem"]) ." ". $_POST["hora_pesagem"]);
						
						$str_add= "
									data_pesagem= '". @formata_data($_POST["data_pesagem"]) ."',
									hora_pesagem = '". $_POST["hora_pesagem"] ."',
									id_turno= '". $id_turno_calcula ."',
									";
					}
					else $str_add= "
									data_pesagem= data_pesagem,
									hora_pesagem = hora_pesagem,
									id_turno= id_turno,
									";
					
					
					
					$result1= mysql_query("update op_limpa_pesagem set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											id_grupo = '". $id_grupo ."',
											data_hora_pesagem= '". formata_data_hifen($_POST["data_pesagem"]) ." ". $_POST["hora_pesagem"] ."',
											peso = '". formata_valor($_POST["peso"]) ."',
											goma= '". $_POST["goma"] ."',
											roupa_alheia= '". $_POST["roupa_alheia"] ."',
											$str_add
											costura= '". $_POST["costura"] ."',
											extra= '". $_POST["extra"] ."'
											where id_pesagem = '". $_POST["id_pesagem"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					
					if (!$result1) $var++;
					
					$result_del= mysql_query("delete from op_limpa_pesagem_pecas
												where id_pesagem = '". $_POST["id_pesagem"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					
					$i=0;
					while ($_POST["id_tipo_roupa"][$i]) {
						$qtde_pacote_aqui= pega_qtde_padrao_pacote($_POST["id_tipo_roupa"][$i]);
						
						$result2[$i]= mysql_query("insert into op_limpa_pesagem_pecas (id_empresa, id_pesagem,
																					   id_tipo_roupa, num_pacotes, qtde_pacote, qtde_pecas_sobra, pacotes_sobra) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_pesagem"] ."',
												 '". $_POST["id_tipo_roupa"][$i] ."', '". $_POST["num_pacotes"][$i] ."',
												 '". $qtde_pacote_aqui ."', '". $_POST["qtde_pecas_sobra"][$i] ."', '". $_POST["pacotes_sobra"][$i] ."'
												 ) ") or die(mysql_error());
						if (!$result2[$i]) $var++;
						
						$i++;
					}
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				//se a página foi carregada pela listagem por cliente
				if ($_POST["origem"]==2)
					header("location: ./?pagina=op/pesagem_limpa_cliente_listar&id_cliente=". $_POST["id_cliente"] ."&msg=". $msg);
				else
					header("location: ./?pagina=op/pesagem_limpa_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formDevolucao"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_remessa"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				
					$result1= mysql_query("insert into op_suja_devolucao (id_empresa, id_remessa, id_cliente, data_devolucao, hora_devolucao, peso, pacotes, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_cliente"] ."',
											'". formata_data($_POST["data_devolucao"]) ."', '". $_POST["hora_devolucao"] ."', '". formata_valor($_POST["peso"]) ."', '". $_POST["pacotes"] ."',
											'". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
				//} else $var++;
				
				$id_devolucao= mysql_insert_id();
				
				$i= 0;
				while ($_POST["id_item"][$i]) {
					if ($i==1) $peso_qtde= formata_valor($_POST["peso_qtde"][$i]);
					else $peso_qtde= $_POST["peso_qtde"][$i];
					
					$result2= mysql_query("insert into op_suja_devolucao_itens (id_devolucao, id_empresa, id_item, peso_qtde) values
											('". $id_devolucao ."', '". $_SESSION["id_empresa"] ."', '". $_POST["id_item"][$i] ."', '". $peso_qtde ."' ) ") or die(mysql_error());
					if (!$result2) $var++;
					
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/devolucao_listar&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update op_suja_devolucao set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											peso = '". formata_valor($_POST["peso"]) ."',
											pacotes = '". $_POST["pacotes"] ."',
											data_devolucao= '". formata_data($_POST["data_devolucao"]) ."',
											hora_devolucao = '". $_POST["hora_devolucao"] ."'
											where id_devolucao = '". $_POST["id_devolucao"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$result_del= mysql_query("delete from op_suja_devolucao_itens
												where id_devolucao = '". $_POST["id_devolucao"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					if (!$result_del) $var++;
					
					$i= 0;
					while ($_POST["id_item"][$i]) {
						if ($i==1) $peso_qtde= formata_valor($_POST["peso_qtde"][$i]);
						else $peso_qtde= $_POST["peso_qtde"][$i];
						
						$result2= mysql_query("insert into op_suja_devolucao_itens (id_devolucao, id_empresa, id_item, peso_qtde) values
												('". $_POST["id_devolucao"] ."', '". $_SESSION["id_empresa"] ."', '". $_POST["id_item"][$i] ."', '". $peso_qtde ."' ) ") or die(mysql_error());
						if (!$result2) $var++;
						
						$i++;
					}
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/devolucao_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formCostura"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				$i=0;
				while ($_POST["id_peca"][$i]!="") {
					if ($_POST["qtde"][$i]!="") {
						$result1= mysql_query("insert into op_limpa_costura (id_empresa, id_remessa, id_cliente, id_peca, data_costura, qtde, obs, id_usuario) 
												values
							('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["id_peca"][$i] ."',
							'". formata_data($_POST["data_costura"]) ."', '". $_POST["qtde"][$i] ."', '". $_POST["obs"] ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					}						
					$i++;
				}
				//} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/costura_listar&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update op_limpa_costura set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											id_peca = '". $_POST["id_peca"] ."',
											data_costura= '". formata_data($_POST["data_costura"]) ."',
											obs = '". $_POST["obs"] ."',
											qtde = '". $_POST["qtde"] ."'
											where id_costura = '". $_POST["id_costura"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/costura_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formGoma"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_remessa"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				$i=0;
				while ($_POST["id_peca"][$i]!="") {
					if (($_POST["qtde"][$i]!="") || ($_POST["peso"][$i]!="")) {
						$result1= mysql_query("insert into op_suja_gomas (id_empresa, id_remessa, id_cliente, id_peca, data_goma, qtde, peso, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_remessa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["id_peca"][$i] ."',
											'". formata_data($_POST["data_goma"]) ."', '". $_POST["qtde"][$i] ."', '". formata_valor($_POST["peso"][$i]) ."', '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					}						
					$i++;
				}
				//} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=op/goma_listar&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result_teste= mysql_query("select * from op_suja_gomas
											   	where id_goma = '". $_POST["id_goma"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					$rs_teste= mysql_fetch_object($result_teste);
					
					//abre nao conformidade
					if ($rs_teste->qtde!=$_POST["qtde"]) {
						$result_insere= mysql_query("insert into op_suja_gomas_correcoes
														(id_empresa, id_goma, data, hora, qtde_anterior, qtde, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_goma"] ."', '". date("Ymd") ."', '". date("H:i:s") ."',
														'". $rs_teste->qtde ."', '". $_POST["qtde"] ."',
														
														'". $_SESSION["id_usuario"] ."'
														 )
														") or die(mysql_error());
					}
					
					if ($rs_teste->peso!=formata_valor($_POST["peso"])) {
						$result_insere= mysql_query("insert into op_suja_gomas_correcoes
														(id_empresa, id_goma, data, hora, peso_anterior, peso, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_goma"] ."', '". date("Ymd") ."', '". date("H:i:s") ."',
														'". $rs_teste->peso ."', '". formata_valor($_POST["peso"]) ."',
														
														'". $_SESSION["id_usuario"] ."'
														 )
														") or die(mysql_error());
					}
					
					$result1= mysql_query("update op_suja_gomas set
											id_remessa= '". $_POST["id_remessa"] ."',
											id_cliente = '". $_POST["id_cliente"] ."',
											id_peca = '". $_POST["id_peca"] ."',
											data_goma= '". formata_data($_POST["data_goma"]) ."',
											qtde = '". $_POST["qtde"] ."',
											peso = '". formata_valor($_POST["peso"]) ."'
											where id_goma = '". $_POST["id_goma"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=op/goma_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formTrocaQuimicoBuscar"])) {
		$pagina= "op/quimico_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formRemessaBuscar"])) {
		$pagina= "op/remessa_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formPesagemSujaBuscar"])) {
		$pagina= "op/pesagem_suja_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formLavagemBuscar"])) {
		$pagina= "op/lavagem_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formDevolucaoBuscar"])) {
		$pagina= "op/devolucao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formPesagemLimpaBuscar"])) {
		$pagina= "op/pesagem_limpa_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formCosturaBuscar"])) {
		$pagina= "op/costura_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formGomaBuscar"])) {
		$pagina= "op/goma_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formProducaoLimpaBuscar"])) {
		$pagina= "op/producao_listar";
		require_once("index2.php");
	}
}

// ############################################### MENSAGENS ###############################################

if (pode("n", $_SESSION["permissao"])) {	
	
	if (isset($_GET["formMensagem"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["titulo"]!="")) {
			inicia_transacao();
			$var=0;
	
			$auth= gera_auth();
			
			for ($i=0; $i<3; $i++) {
				$j= $i+1;
				if ($_FILES["anexo"]["name"][$i]!="") {
					$nome_correto= retira_acentos($_FILES["anexo"]["name"][$i]);
					$caminho= CAMINHO ."mensagem_". $auth ."_". $nome_correto;
					$upload= move_uploaded_file($_FILES["anexo"]["tmp_name"][$i], $caminho);
					
					if ($i==0) $anexos= $nome_correto;
					else $anexos.= " |+| ". $nome_correto;
				}
			}
			
			$i=0;
			while ($_POST["para"][$i]) {
				$result[$i]= mysql_query("insert into com_mensagens
										 (id_empresa, de, para, titulo, mensagem,
										 data_mensagem, hora_mensagem, lida, situacao_de, situacao_para, auth, anexos)
										 values 
										('". $_SESSION["id_empresa"] ."', '". pega_id_pessoa_do_usuario($_SESSION["id_usuario"]) ."',
										'". $_POST["para"][$i] ."', '". $_POST["titulo"] ."', '". $_POST["mensagem"] ."',
										'". date("Ymd") ."', '". date("His") ."',  '0', '1', '1', '$auth', '$anexos')") or die(mysql_error());
				
				if (!$result[$i]) $var++;
				
				$i++;
			}
			
			finaliza_transacao($var);
			$msg= $var;
			
			header("location: ./?pagina=com/mensagem_listar&tipo=e&msg=". $msg);
		}
	}
}

// ############################################### LIVRO ###############################################

if (pode("o", $_SESSION["permissao"])) {	
	
	if (isset($_GET["formReclamacaoAndamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_situacao"]!="") && ($_POST["id_livro"]!="")) {
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into qual_reclamacoes_andamento (id_empresa, id_livro, id_situacao, data_andamento, hora_andamento, obs, id_usuario)
													   values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_livro"] ."', '". $_POST["id_situacao"] ."',
										'". date("Ymd") ."', '". date("H:i:s") ."',
										'". $_POST["obs"] ."',
										'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			
			$result_livro= mysql_query("select *, DATE_FORMAT(data_livro, '%Y') as ano from com_livro
									   	where id_livro = '". $_POST["id_livro"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
			$rs_livro= mysql_fetch_object($result_livro);
			
			$mensagem_aqui= "<strong>Atualização em ". pega_motivo($rs_livro->id_motivo) ." Nº ". $rs_livro->num_livro ."/". $rs_livro->ano .":</strong><br /><br />
									
									<strong>ABERTURA POR ". pega_funcionario($rs_livro->de) ." EM ". desformata_data($rs_livro->data_livro) ." ÀS ". $rs_livro->hora_livro .":</strong><br />". $rs_livro->mensagem ." <br />
									
									<strong>ATUALIZAÇÃO POR ". pega_funcionario($_SESSION["id_funcionario_sessao"]) ." EM ". date("d/m/Y") ." ÀS ". date("H:i:s") ." (". pega_situacao_reclamacao($_POST["id_situacao"]) ."):</strong>
									<br />
									". $_POST["obs"] ."
									
									<a class=\"menor vermelho\" href=\"./?pagina=qualidade/reclamacao&amp;acao=e&amp;id_livro=". $_POST["id_livro"] ."\" target=\"_blank\"><strong>ACESSAR</strong></a>
								";
			
			$result_livro_deptos= mysql_query("select id_departamento from com_livro_permissoes
											  	where id_livro = '". $_POST["id_livro"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
			$id_departamentos= array();
			$i=0;
			$nao_inseriu_depto_principal= true;
			
			while ($rs_livro_deptos= mysql_fetch_object($result_livro_deptos)) {
				$id_departamentos[$i]= $rs_livro_deptos->id_departamento;
				
				if ($rs_livro_deptos->id_departamento==$rs_livro->id_departamento_principal) $nao_inseriu_depto_principal= false;
				
				$i++;
			}
			
			//se o depto reclamado não estiver na lista de destinatários, adiciona ele manualmente aqui
			if ($nao_inseriu_depto_principal) $id_departamentos[$i]= $rs_livro->id_departamento_principal;
			
			$id_livro_inserido= insere_recado_livro_sistema($mensagem_aqui, 35, $id_departamentos);
			
			finaliza_transacao($var);
			
			$msg= $var;
			header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_POST["id_livro"] ."&msg=". $msg);
		}//fim teste variáveis
	}
	
	if (isset($_GET["formReclamacaoAlertar"])) {
		
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento_principal"]!="")) {
			inicia_transacao();
			$var=0;
	
			if ($_POST["data_livro"]!="") $data_livro= $_POST["data_livro"];
			else $data_livro= date("d/m/Y");
			
			if ($_POST["hora_livro"]!="") $hora_livro= $_POST["hora_livro"];
			else $hora_livro= date("H:i:s");
			
			$result_pre= mysql_query("select * from com_livro
									 	where id_livro = '". $_POST["id_livro"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
			$rs_pre= mysql_fetch_object($result_pre);
			
			/*$result_num_livro= mysql_query("select * from com_livro
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_motivo <> '34'
											and   id_motivo <> '37'
											and   id_motivo <> '41'
											and   id_motivo <> '42'
											order by num_livro desc limit 1
											");
			$rs_num_livro= mysql_fetch_object($result_num_livro);
			$num_livro= $rs_num_livro->num_livro+1;
			*/
			
			$result= mysql_query("insert into com_livro
										  	(id_empresa, num_livro, tipo_de, de, id_outro_departamento, mensagem, 
											 data_livro, hora_livro, tipo_resposta, resposta, resposta_id_livro, id_motivo,
											 resposta_requerida, id_departamento_principal, prioridade_dias,
											 reclamacao_original, reclamacao_original_id_livro
											 ) values
											('". $_SESSION["id_empresa"] ."', '". $rs_pre->num_livro ."', '". $rs_pre->tipo_de ."', '". $rs_pre->de ."', '". $rs_pre->id_outro_departamento ."',
											 '". $rs_pre->mensagem ."<br /><span class=\"menor\"><strong>(REQUISITANDO ATENÇÃO - ORIGINALMENTE POSTADO EM ". desformata_data($rs_pre->data_livro) ." ÀS ". $rs_pre->hora_livro .")</strong></span>',
											 '". formata_data($data_livro) ."', '". $hora_livro ."',
											 '". $rs_pre->tipo_resposta ."', '". $rs_pre->resposta ."', '". $rs_pre->id_livro ."',
											 
											 '". $rs_pre->id_motivo ."', '". $rs_pre->resposta_requerida ."',
											 '". $_POST["id_departamento_principal"] ."', '". $rs_pre->prioridade_dias ."',
											 '0', '". $_POST["id_livro"] ."'
											 ) ") or die(mysql_error());
			$id_livro_inserido= mysql_insert_id();
			
			$insere_principal= false;
			
			$i=0;
			while ($_POST["para"][$i]!="") {
				
				if ($_POST["id_departamento_principal"]==$_POST["para"][$i]) {
					$insere_principal=true;
				}
				else {
					$str1=", resposta_requerida_depto";
					$str2=", '1'";
				}
				
				$result_permissao2[$i]= mysql_query("insert into com_livro_permissoes
													 (id_empresa, id_livro, id_departamento $str1 )
													 values 
													 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
													 '". $_POST["para"][$i] ."' $str2 )
													 ") or die(mysql_error());
				
				if (!$result_permissao2[$i]) $var++;
				
				$i++;
			}
			
			if (!$insere_principal) {
				$result_permissao= mysql_query("insert into com_livro_permissoes
													 (id_empresa, id_livro, id_departamento, resposta_requerida_depto)
													 values 
													 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
													 '". $_POST["id_departamento_principal"] ."', '1' )
													 ") or die(mysql_error());
				
				if (!$result_permissao) $var++;
			}
			
			finaliza_transacao($var);
			$msg= $var;
			
			header("location: ./?pagina=qualidade/reclamacao&acao=e&msg=". $msg ."&id_livro=". $_POST["id_livro"]);
		}
	}
	
	//só qualidade ou admin
	if (pode("12(", $_SESSION["permissao"])) {
		if (isset($_GET["formReclamacaoDepartamentoAlterar"])) {
			
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento_principal"]!="") && ($_POST["id_livro"]!="")) {
				inicia_transacao();
				$var=0;
				
				$result= mysql_query("update com_livro
										set id_departamento_principal = '". $_POST["id_departamento_principal"] ."'
										where id_livro = '". $_POST["id_livro"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				
				if (!$result) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=qualidade/reclamacao&acao=e&msg=". $msg ."&id_livro=". $_POST["id_livro"]);
			}
		}
		
		if (isset($_GET["formReclamacaoAnular"])) {
			
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_livro"]!="")) {
				inicia_transacao();
				$var=0;
				
				$str_num_livro= " and   id_motivo <> '34'
								  and   id_motivo <> '37'
								  and   id_motivo <> '41'
								  and   id_motivo <> '42'
								  ";
				
				$ano_aqui= substr($_POST["data_livro"], 6, 4);
				
				$result_num_livro= mysql_query("select * from com_livro
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   DATE_FORMAT(data_livro, '%Y') = '". $ano_aqui ."'
												$str_num_livro
												order by num_livro desc limit 1
												");
				$rs_num_livro= mysql_fetch_object($result_num_livro);
				$num_livro= $rs_num_livro->num_livro+1;
				
				$result= mysql_query("update com_livro
										set id_motivo = '',
										num_livro= '$num_livro'
										where id_livro = '". $_POST["id_livro"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				
				if (!$result) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=qualidade/reclamacao&acao=e&msg=". $msg ."&id_livro=". $_POST["id_livro"]);
			}
		}
		
		if (isset($_GET["formRMAnular"])) {
			
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_rm"]!="")) {
				inicia_transacao();
				$var=0;
				
				$result= mysql_query("select * from man_rms, man_rms_andamento
										where man_rms.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   man_rms.id_rm = '". $_POST["id_rm"] ."'
										and   man_rms.id_rm = man_rms_andamento.id_rm
										and   man_rms_andamento.id_situacao = '1'
										");
				$rs= mysql_fetch_object($result);
				
				$id_funcionario_aqui= pega_id_funcionario_do_usuario($rs->id_usuario);
				
				$mensagem_aqui= $rs->problema ."<br /><br /><span class='menor'><strong>RM Nº ". $RS->num_rm ."</strong> ANULADA.</span>";
				
				$id_departamentos[0]= pega_dado_carreira("id_departamento", $id_funcionario_aqui);
				$id_departamentos[1]= 8;
				$id_departamentos[2]= 15;
				$id_departamentos[3]= 20;
				
				$id_livro_inserido= insere_recado_livro_normal($id_funcionario_aqui, $mensagem_aqui, 0, $id_departamentos, 8);
				
				$result_apaga1= mysql_query("delete from man_rms
											where id_rm = '". $_POST["id_rm"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
				
				if (!$result_apaga1) $var++;
				
				$result_apaga2= mysql_query("delete from man_rms_andamento
											where id_rm = '". $_POST["id_rm"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
				
				if (!$result_apaga2) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=com/livro&data=". date("d/m/Y") ."#livro_". $id_livro_inserido);
			}
		}
	}
	
	
	if (isset($_GET["formLivro"])) {
		
		$algum_departamento=1;
		
		/*if ($_POST["id_livro"]!="")	$algum_departamento=1;
		else {
			$i=0;
			while ($_POST["para"][$i]!="") {
				$algum_departamento=1;
				$i++;
			}
		}*/
				
		if (($_SESSION["id_empresa"]!="") && ($_POST["mensagem"]!="") && ($algum_departamento!="")) {
			inicia_transacao();
			$var=0;
	
			$auth= gera_auth();
			
			if ($_SESSION["id_funcionario_sessao"]!="") {
				$tipo_de= "f";
				$de= $_SESSION["id_funcionario_sessao"];
			}
			else {
				$tipo_de= "d";
				$de= $_SESSION["id_departamento_sessao"];
			}
			
			if ($_POST["data_livro"]!="") $data_livro= $_POST["data_livro"];
			else $data_livro= date("d/m/Y");
			
			if ($_POST["hora_livro"]!="") $hora_livro= $_POST["hora_livro"];
			else $hora_livro= date("H:i:s");
			
			if ($_POST["id_livro"]!="") $mensagem_aqui= nl2br(addslashes($_POST["mensagem"]));
			else $mensagem_aqui= addslashes($_POST["mensagem"]);
			
			
			
			switch ($_POST["id_motivo"]) {
				//reclamacao
				case 34:
				case 37:
					$str_num_livro= " and   (id_motivo = '34' or id_motivo = '37') ";
					break;
				//nc
				case 41:
				case 42:
					$str_num_livro= " and   (id_motivo = '41' or id_motivo = '42') ";
					break;
				//demais
				default:
					$str_num_livro= " and   id_motivo <> '34'
									  and   id_motivo <> '37'
									  and   id_motivo <> '41'
									  and   id_motivo <> '42'
									  ";
			}
			
			$ano_aqui= substr($data_livro, 6, 4);
			
			$result_num_livro= mysql_query("select * from com_livro
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   DATE_FORMAT(data_livro, '%Y') = '". $ano_aqui ."'
											$str_num_livro
											order by num_livro desc limit 1
											");
			$rs_num_livro= mysql_fetch_object($result_num_livro);
			$num_livro= $rs_num_livro->num_livro+1;
			
			$result= mysql_query("insert into com_livro
										  	(id_empresa, num_livro, tipo_de, de, id_outro_departamento, mensagem, 
											 data_livro, hora_livro, tipo_resposta, resposta, resposta_id_livro,
											 id_motivo, resposta_requerida, id_departamento_principal, prioridade_dias,
											 reclamacao_original, reclamacao_original_id_livro, reclamacao_id_cliente, id_causa
											 ) values
											('". $_SESSION["id_empresa"] ."', '". $num_livro ."', '". $tipo_de ."', '". $de ."', '". $_POST["id_outro_departamento"] ."',
											 '". $mensagem_aqui ."', '". formata_data($data_livro) ."', '". $hora_livro ."',
											 '". $_POST["tipo_resposta"] ."', '". $_POST["resposta"] ."', '". $_POST["id_livro"] ."',
											 
											 '". $_POST["id_motivo"] ."', '0',
											 '". $_POST["id_departamento_principal"] ."', '". $_POST["prioridade_dias"] ."',
											 '1', '', '". $_POST["reclamacao_id_cliente"] ."', '". $_POST["id_causa"] ."'
											 ) ") or die(mysql_error());
			$id_livro_inserido= mysql_insert_id();
			
			/*if ($_POST["id_livro"]!="") {
				$result_para= mysql_query("select * from com_livro_permissoes
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_livro = '". $_POST["id_livro"] ."'
											");
				$para= "";
				while ($rs_para= mysql_fetch_object($result_para)) {
					$result_permissao[$i]= mysql_query("insert into com_livro_permissoes
														 (id_empresa, id_livro, id_departamento)
														 values 
														 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
														 '". $rs_para->id_departamento ."' )
														 ") or die(mysql_error());
					
					if (!$result_permissao[$i]) $var++;
				}
			}
			else {
				if (($_SESSION["id_funcionario_sessao"]!="") && ($_POST["id_livro"]=="")) {
				}*/
				
				if ($_POST["id_outro_departamento"]!="") $id_departamento_usuario2= $_POST["id_outro_departamento"];
				else $id_departamento_usuario2= pega_dado_carreira("id_departamento", $_SESSION["id_funcionario_sessao"]);
				
				$inseriu_proprio= 0;
				$inseriu_principal= 0;
				
				$i=0;
				while ($_POST["para"][$i]!="") {
					
					switch ($_POST["id_departamento_principal"]) {
						case "":
							if (($_POST["resposta_requerida"]=="1") && ($_POST["para"][$i]!=$id_departamento_usuario2)) $resposta_requerida_depto=1;
							else $resposta_requerida_depto=0;
						break;
						default:
							if (($_POST["resposta_requerida"]=="1") && ($_POST["para"][$i]==$_POST["id_departamento_principal"])) $resposta_requerida_depto=1;
							else $resposta_requerida_depto=0;
						break;
					}
					
					$result_permissao[$i]= mysql_query("insert into com_livro_permissoes
														 (id_empresa, id_livro, id_departamento, resposta_requerida_depto)
														 values 
														 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
														 '". $_POST["para"][$i] ."', '". $resposta_requerida_depto ."' )
														 ") or die(mysql_error());
					
					if (!$result_permissao[$i]) $var++;
					
					if ($_POST["para"][$i]==$id_departamento_usuario2) $inseriu_proprio= 1;
					if (($_POST["id_departamento_principal"]!="") && ($_POST["para"][$i]==$_POST["id_departamento_principal"])) $inseriu_principal= 1;
					
					$i++;
				}
				
				//se nao inseriu o prioprio
				if (!$inseriu_proprio) {
					
					if (($_POST["resposta_requerida"]=="1") && ($id_departamento_usuario2==$_POST["id_departamento_principal"])) $resposta_requerida_depto=1;
					else $resposta_requerida_depto=0;
					
					$result_permissao_proprio= mysql_query("insert into com_livro_permissoes
														 (id_empresa, id_livro, id_departamento, resposta_requerida_depto)
														 values 
														 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
														 '". $id_departamento_usuario2 ."', '". $resposta_requerida_depto ."' )
														 ") or die(mysql_error());
					if (!$result_permissao_proprio) $var++;
				}
				
				//se nao inseriu o principal
				if (($_POST["id_departamento_principal"]!="") && (!$inseriu_principal)) {
					
					if ($_POST["resposta_requerida"]=="1") $resposta_requerida_depto=1;
					else $resposta_requerida_depto=0;
					
					$result_permissao_principal= mysql_query("insert into com_livro_permissoes
														 (id_empresa, id_livro, id_departamento, resposta_requerida_depto)
														 values 
														 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
														 '". $_POST["id_departamento_principal"] ."', '". $resposta_requerida_depto ."' )
														 ") or die(mysql_error());
					if (!$result_permissao_principal) $var++;
				}
			//}
			
			$_SESSION["mensagem_livro"]="";
			
			//se está respondendo
			if ($_POST["id_livro"]!="") {
				/*$result_departamento_principal= mysql_query("select * from com_livro
																where id_empresa = '". $_SESSION["id_empresa"] ."'
																and   id_livro = '". $_POST["id_livro"] ."'
																");
				$rs_departamento_principal= mysql_fetch_object($result_departamento_principal);
				
				$permissao_departamento_auxiliar= 0;
				
				//pegar todos os deptos do qual o usuário é responsável
				$result_departamento_auxiliares= mysql_query("select * from rh_carreiras_departamentos
																where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
																and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
																and   rh_carreiras_departamentos.valido = '1'
															") or die(mysql_error());
				while ($rs_departamento_auxiliares= mysql_fetch_object($result_departamento_auxiliares)) {
					//se o destino da mensagem for algum departamento no qual o usuário é responsável
					if ($rs_departamento_principal->id_departamento_principal==$rs_departamento_auxiliares->id_departamento) {
						$permissao_departamento_auxiliar=1;
						break;
					}
				}
				
				if (($rs_departamento_principal->id_departamento_principal==$id_departamento_usuario2) || ($rs_departamento_principal->id_departamento_principal=="") || ($rs_departamento_principal->id_departamento_principal=="0") || ($permissao_departamento_auxiliar) ) {
					$result_respondido= mysql_query("update com_livro
													 set resposta_requerida = '2'
													 where id_livro = '". $_POST["id_livro"] ."'
													 and   id_empresa = '". $_SESSION["id_empresa"] ."'
													 ") or die(mysql_error());
					if (!$result_respondido) $var++;
				}*/
				
				$str_deptos_auxiliares="";
				
				//pegar todos os deptos do qual o usuário é responsável
				$result_departamento_auxiliares= mysql_query("select * from rh_carreiras_departamentos
																where rh_carreiras_departamentos.id_funcionario = '". $_SESSION["id_funcionario_sessao"] ."'
																and   rh_carreiras_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
																and   rh_carreiras_departamentos.valido = '1'
															") or die(mysql_error());
				while ($rs_departamento_auxiliares= mysql_fetch_object($result_departamento_auxiliares)) {
					$str_deptos_auxiliares.= " or id_departamento = '". $rs_departamento_auxiliares->id_departamento ."' ";
				}
				
				$result_checagem= mysql_query("select * from com_livro_permissoes
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_livro = '". $_POST["id_livro"] ."'
												and   (id_departamento = '". $id_departamento_usuario2 ."'
												$str_deptos_auxiliares
												)
												") or die(mysql_error());
				if (!$result_checagem) $var++;
				
				$linhas_checagem= mysql_num_rows($result_checagem);
				//$rs_checagem= mysql_fetch_object($result_checagem);
				
				if ($linhas_checagem>0) {
					$result_atualiza= mysql_query("update com_livro_permissoes
													set   resposta_requerida_depto= '2'
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_livro = '". $_POST["id_livro"] ."'
													and   (id_departamento = '". $id_departamento_usuario2 ."'
													$str_deptos_auxiliares
													)
													");
					if (!$result_atualiza) $var++;
				}
				
			}
			
			finaliza_transacao($var);
			$msg= $var;
			
			if ($_POST["id_livro"]!="") header("location: ./?pagina=com/livro&msg=". $msg."&minhas=". $_POST["minhas_busca"] ."&id_funcionario=". $_POST["id_funcionario_busca"] ."&parte=". $_POST["parte_busca"] ."&id_motivo=". $_POST["id_motivo_busca"] ."&resposta_requerida=". $_POST["resposta_requerida_busca"] ."&data1=". $_POST["data1_busca"] ."&data2=". $_POST["data2_busca"] ."&data=". $_POST["data_busca"] ."&depto_para=". $_POST["depto_para"] ."&id_departamento_principal=". $_POST["id_departamento_principal_busca"] ."#livro_". $_POST["id_livro"]);
			else header("location: ./?pagina=com/livro&msg=". $msg ."&minhas=". $_POST["minhas_busca"] ."&id_funcionario=". $_POST["id_funcionario_busca"] ."&parte=". $_POST["parte_busca"] ."&id_motivo=". $_POST["id_motivo_busca"] ."&resposta_requerida=". $_POST["resposta_requerida_busca"] ."&data1=". $_POST["data1_busca"] ."&data2=". $_POST["data2_busca"] ."&data=". $_POST["data_busca"] ."&depto_para=". $_POST["depto_para_busca"] ."&id_departamento_principal=". $_POST["id_departamento_principal_busca"]);
		}
		else {
			$_SESSION["mensagem_livro"]= $_POST["mensagem"];
			
			header("location: ./?pagina=com/livro&msg=1&minhas=". $_POST["minhas_busca"] ."&id_funcionario=". $_POST["id_funcionario_busca"] ."&parte=". $_POST["parte_busca"] ."&id_motivo=". $_POST["id_motivo_busca"] ."&resposta_requerida=". $_POST["resposta_requerida_busca"] ."&data1=". $_POST["data1_busca"] ."&data2=". $_POST["data2_busca"] ."&data=". $_POST["data_busca"] ."&depto_para=". $_POST["depto_para_busca"] ."&id_departamento_principal=". $_POST["id_departamento_principal_busca"]);
		}
	}
	
	if (isset($_GET["formLivroBuscar"])) {
		header("location: ./?pagina=com/livro");
	}
}


// ############################################### CONTROLE ###############################################

if (pode("u", $_SESSION["permissao"])) {	
	
	if (isset($_GET["formAbastecimentoBuscar"])) {
		$pagina= "financeiro/abastecimento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formAbastecimento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["data"]!="") && ($_POST["id_veiculo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				
					$result1= mysql_query("insert into fi_abastecimentos
										  	(id_empresa, id_veiculo, id_funcionario, 
											 data, tipo_comb, valor_litro, litros,
											 valor_total, obs, id_usuario_at, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_veiculo"] ."', '". $_POST["id_funcionario"] ."',
											'". formata_data($_POST["data"]) ."',
											'". $_POST["tipo_comb"] ."',
											'". formata_valor($_POST["valor_litro"]) ."', '". formata_valor($_POST["litros"]) ."', '". formata_valor($_POST["valor_total"]) ."',
											'". $_POST["obs1"] ."', '". $_POST["id_usuario_at"] ."',
											'". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
				//} else $var++;
				
				$id_abastecimento= mysql_insert_id();
				
				finaliza_transacao($var);
				$msg= $var;
				
				$acao='i';
				$pagina= "financeiro/abastecimento_listar";
				require_once("index2.php");
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update fi_abastecimentos set
											id_veiculo= '". $_POST["id_veiculo"] ."',
											id_funcionario= '". $_POST["id_funcionario"] ."',
											data= '". formata_data($_POST["data"]) ."',
											tipo_comb= '". $_POST["tipo_comb"] ."',
											valor_litro= '". formata_valor($_POST["valor_litro"]) ."',
											litros= '". formata_valor($_POST["litros"]) ."',
											valor_total= '". formata_valor($_POST["valor_total"]) ."',
											obs= '". $_POST["obs1"] ."',
											id_usuario_at= '". $_POST["id_usuario_at"] ."'
											
											where id_abastecimento = '". $_POST["id_abastecimento"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				$pagina= "financeiro/abastecimento_listar";
				require_once("index2.php");
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRefeicaoBuscar"])) {
		$pagina= "financeiro/refeicao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formRefeicao"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["data"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());

				if (mysql_num_rows($result_pre)==0) {*/
				
					$result1= mysql_query("insert into fi_refeicoes
										  	(id_empresa, id_funcionario, id_departamento, id_turno, id_motivo,
											 data, num_almocos, tipo_almoco, opcao_almoco,
											 valor_total, obs, id_usuario_at, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $_POST["id_departamento"] ."',
											'". $_POST["id_turno"] ."', '". $_POST["id_motivo"] ."',
											'". formata_data($_POST["data"]) ."',
											'". $_POST["num_almocos"] ."', '". $_POST["tipo_almoco"] ."', '". $_POST["opcao_almoco"] ."',
											'". formata_valor($_POST["valor_total"]) ."',
											'". $_POST["obs1"] ."', '". $_POST["id_usuario_at"] ."',
											'". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
				//} else $var++;
				
				$id_refeicao= mysql_insert_id();
				
				finaliza_transacao($var);
				$msg= $var;
				
				$acao='i';
				$pagina= "financeiro/refeicao_listar";
				require_once("index2.php");
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update fi_refeicoes set
											id_funcionario= '". $_POST["id_funcionario"] ."',
											id_departamento= '". $_POST["id_departamento"] ."',
											id_turno= '". $_POST["id_turno"] ."',
											id_motivo= '". $_POST["id_motivo"] ."',
											data= '". formata_data($_POST["data"]) ."',
											num_almocos= '". $_POST["num_almocos"] ."',
											tipo_almoco= '". $_POST["tipo_almoco"] ."',
											opcao_almoco= '". $_POST["opcao_almoco"] ."',
											valor_total= '". formata_valor($_POST["valor_total"]) ."',
											obs= '". $_POST["obs1"] ."',
											id_usuario_at= '". $_POST["id_usuario_at"] ."'
											where id_refeicao = '". $_POST["id_refeicao"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				$msg= $var;
				
				$pagina= "financeiro/refeicao_listar";
				require_once("index2.php");
			}//e
			
		}//fim teste variáveis
	}
	
}

// ############################################### ADM EMPRESA ###############################################
	
if (pode("aiz12", $_SESSION["permissao"])) {
	
	if (isset($_GET["formAd"])) {
		if (($_POST["id_cliente"]!="") && ($_POST["nome"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				$var= 0;
				inicia_transacao();
				
				$result1= mysql_query("insert into tr_clientes_ad (id_empresa, id_cliente, nome, cargo, setor, data_nasc, email, usuario,
																   	auth, status_usuario, situacao) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
											'". $_POST["nome"] ."', '". $_POST["cargo"] ."', '". $_POST["setor"] ."', '". formata_data($_POST["data_nasc"]) ."', '". $_POST["email"] ."', '". $_POST["usuario"] ."', '". gera_auth() ."', '1', '1' ) ") or die("1: ". mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/ad_listar";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/ad_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				if ($_POST["zerar_senha"]==1) $linha_senha= " senha = '', ";
				
				$result1= mysql_query("update tr_clientes_ad set
										id_cliente= '". $_POST["id_cliente"] ."',
										nome= '". $_POST["nome"] ."',
										cargo= '". $_POST["cargo"] ."',
										setor= '". $_POST["setor"] ."',
										email= '". $_POST["email"] ."',
										". $linha_senha ."
										usuario= '". $_POST["usuario"] ."'
										where id_ad = '". $_POST["id_ad"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/ad_listar";
				require_once("index2.php");
				
				//header("location: ./?pagina=financeiro/ad_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formUsuario"])) {
		if (($_POST["id_empresa"]!="") && ($_POST["usuario"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				if ($_POST["senha"]!="") {
					
					if ($_POST["id_departamento"]!="") {
						$id_funcionario_condicao= 0;
						$id_departamento_condicao= $_POST["id_departamento"];
						
						$result_pre= mysql_query("select * from usuarios
													where id_departamento = '". $_POST["id_departamento"] ."'
													and   id_empresa = '". $_POST["id_empresa"] ."'
													and   situacao = '1'
													") or die(mysql_error());
					}
					elseif ($_POST["id_funcionario"]!="") {
						$id_funcionario_condicao= $_POST["id_funcionario"];
						$id_departamento_condicao= 0;
						
						$result_pre= mysql_query("select * from usuarios
													where id_funcionario = '". $_POST["id_funcionario"] ."'
													and   id_empresa = '". $_POST["id_empresa"] ."'
													and   situacao = '1'
													") or die(mysql_error());
					}
					else die("Escolhe um funcionário ou um departamento para criar um usuário!");
					
					$var=0;
					
					
					if (mysql_num_rows($result_pre)==0) {
						inicia_transacao();
						
						$i=0;
						$permissao_insere= '.';
						
						while ($_POST["campo_permissao"][$i]) {
							$permissao_insere.= $_POST["campo_permissao"][$i];
							$i++;
						}
						$permissao_insere.= '.';
						
						$result1= mysql_query("insert into usuarios (id_funcionario, id_departamento, id_empresa, usuario, senha, senha_sem, tipo_usuario, status_usuario, permissao, situacao) values
												('". $id_funcionario_condicao ."', '". $id_departamento_condicao ."', '". $_POST["id_empresa"] ."',
													'". $_POST["usuario"] ."', '". md5($_POST["senha"]) ."', '". $_POST["senha"] ."',
													'e', '1', '$permissao_insere', '1' ) ") or die("1: ". mysql_error());
						if (!$result1) $var++;
						
						finaliza_transacao($var);
					} else $var++;
				}//senha em branco
				else $var++;
				
				$msg= $var;	
				$pagina= "acesso/usuario_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$i=0; $permissao_insere= '.';
				while ($_POST["campo_permissao"][$i]) {
					$permissao_insere.= $_POST["campo_permissao"][$i];
					$i++;
				}
				$permissao_insere.= '.';
				
				if ($_POST["senha"]!="")
					$linha_senha= " senha= '". md5($_POST["senha"]) ."', senha_sem= '". $_POST["senha"] ."', ";
				
				$result1= mysql_query("update usuarios set
										usuario= '". $_POST["usuario"] ."',
										". $linha_senha ."
										permissao= '$permissao_insere'
										where id_usuario = '". $_POST["id_usuario"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "acesso/usuario_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
}// fim pode a
	
// ############################################### TRANSPORTE ADM #############################################
	
if (pode("e", $_SESSION["permissao"])) {
	if (isset($_GET["formTranspCronograma"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["id_dia"]!="")) {
			
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				
				inicia_transacao();
				
				if ($_POST["id_dia"]==0) {
					$limite= 7;
				}
				else {
					$limite= 1;
				}
				
				for ($j=0; $j<$limite; $j++) {
					if ($_POST["id_dia"]!=0) $id_dia_unico= $_POST["id_dia"];
					else $id_dia_unico= $j;
					
					$i= 0;
					while ($_POST["hora_cronograma"][$i]!="") {
						$result1[$i]= mysql_query("insert into tr_cronograma (id_empresa, id_cliente, tipo, id_dia, hora_cronograma, id_usuario) values
												   ('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."', '". $_POST["tipo"] ."',
													'". $id_dia_unico ."', '". $_POST["hora_cronograma"][$i] ."',
													'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
						if (!$result1[$i]) $var++;
						
						$i++;
					}
				}
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=transporte/cronograma_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;

				inicia_transacao();
				
				$result1= mysql_query("update tr_cronograma set
										id_cliente= '". $_POST["id_cliente"] ."',
										tipo= '". $_POST["tipo"] ."',
										id_dia= '". $_POST["id_dia"] ."',
										hora_cronograma= '". $_POST["hora_cronograma"] ."'
										where id_cronograma = '". $_POST["id_cronograma"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=transporte/cronograma_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
}

// ############################################### TRANSPORTE TODOS #############################################

if (pode("ey", $_SESSION["permissao"])) {
	
	if (isset($_GET["formVistoria"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_veiculo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into tr_vistorias (id_empresa, id_veiculo, data_vistoria, hora_vistoria, km, motoristas, obs, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_veiculo"] ."',
										'". formata_data($_POST["data_vistoria"]) ."', '". $_POST["hora_vistoria"] ."', '". formata_valor($_POST["km"]) ."',
										'". $_POST["motoristas"] ."', '". $_POST["obs"] ."', '". $_SESSION["id_usuario"] ."'
										) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$id_vistoria= mysql_insert_id();
				
				$i= 0;
				while ($_POST["id_item"][$i]) {
					$result2= mysql_query("insert into tr_vistorias_itens_checklist (id_empresa, id_vistoria, id_item, valor) values
											('". $_SESSION["id_empresa"] ."', '". $id_vistoria ."', '". $_POST["id_item"][$i] ."', '". $_POST["valor"][$i] ."' ) ") or die(mysql_error());
					if (!$result2) $var++;
					
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=transporte/vistoria_listar&acao=i&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update tr_vistorias set
										id_veiculo= '". $_POST["id_veiculo"] ."',
										data_vistoria= '". formata_data($_POST["data_vistoria"]) ."',
										hora_vistoria = '". $_POST["hora_vistoria"] ."',
										km = '". formata_valor($_POST["km"]) ."',
										motoristas= '". $_POST["motoristas"] ."',
										obs= '". $_POST["obs"] ."'
										where id_vistoria = '". $_POST["id_vistoria"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				$result_del= mysql_query("delete from tr_vistorias_itens_checklist
											where id_vistoria = '". $_POST["id_vistoria"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
				if (!$result_del) $var++;
				
				$i= 0;
				while ($_POST["id_item"][$i]) {
					$result2= mysql_query("insert into tr_vistorias_itens_checklist (id_empresa, id_vistoria, id_item, valor) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_vistoria"] ."', '". $_POST["id_item"][$i] ."', '". $_POST["valor"][$i] ."' ) ") or die(mysql_error());
					if (!$result2) $var++;
					
					$i++;
				}
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				header("location: ./?pagina=transporte/vistoria_listar&acao=i&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
	
	if (isset($_GET["formAdValida"])) {
		if ($_SESSION["id_empresa"]!="") {
			
			$var=0;

			inicia_transacao();
			
			$result_valida= mysql_query("select * from tr_clientes_ad
											where id_cliente = '". $_POST["id_cliente"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   usuario = '". $_POST["usuario"] ."'
											and   senha = '". md5($_POST["senha_ad"]) ."'
											and   status_usuario= '1'
											and   situacao= '1'
											") or die(mysql_error());
			if (!$result_valida) $var++;
			
			if (mysql_num_rows($result_valida)==1) {
				$rs_valida= mysql_fetch_object($result_valida);
				
				echo "
				<script language=\"javascript\">
					preencheDiv('div_id_ad_". $_POST["cont"] ."', '<label class=\"tamanho50\">Ass. por:</label> <span class=\"verde\">". $rs_valida->nome ."</span> <br />');
					atribuiValor('id_ad_". $_POST["cont"] ."', '". $rs_valida->id_ad ."');
					fechaDiv('assinatura_digital');
				</script>
				";
			}
			else {
				$erro_valida=1;
				
				$pagina= "transporte/ad_valida";
				require_once("index2.php");
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
		}//fim teste variáveis
	}
	
	if (isset($_GET["formPercurso"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_veiculo"]!="") && ($_POST["id_motorista"]!="")) {
			
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				
				inicia_transacao();
				
				$data_hora_percurso= formata_data_hifen($_POST["data_percurso"][0]) ." ". $_POST["hora_percurso"][0];
				
				switch($_POST["tipo"]) {
					case "1":
					case "4":
						$str_tipo= " and   ( tr_percursos.tipo = '1' or tr_percursos.tipo = '4' ) ";
					break;
					
					case "2":
					case "5":
						$str_tipo= " and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' ) ";
					break;
					
					case "3":
						$str_tipo= " and   tr_percursos.tipo = '3' ";
					break;
				}
											
				$result1= mysql_query("insert into tr_percursos (id_empresa, id_veiculo, tipo,
																 obs, id_motorista, id_situacao_atual, veiculo_permissao, data_hora_percurso, id_regiao, id_usuario) values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_veiculo"] ."', '". $_POST["tipo"] ."',
													'". $_POST["obs"] ."',
													'". $_POST["id_motorista"] ."', '1', '". $_POST["veiculo_permissao"] ."',
													'". $data_hora_percurso ."', '". $_POST["id_regiao"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_percurso= mysql_insert_id();
				
				
				$i=0;
				while ($_POST["id_cliente_entrega"][$i]!="") {
					
					$result_num[$i]= mysql_query("select count(tr_percursos.id_percurso) as total from tr_percursos, tr_percursos_clientes
												where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente_entrega"][$i] ."'
												
												$str_tipo
												
												and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". formata_data_hifen($_POST["data_percurso"][0]) ."'
												");
					$rs_num[$i]= mysql_fetch_object($result_num[$i]);
					$num_percurso[$i]= $rs_num[$i]->total+1;
					
					$result_num_tipo[$i]= mysql_query("select count(tr_percursos.id_percurso) as total from tr_percursos, tr_percursos_clientes
												where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente_entrega"][$i] ."'
												and   tr_percursos.tipo = '". $_POST["tipo"] ."'
												and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". formata_data_hifen($_POST["data_percurso"][0]) ."'
												");
					$rs_num_tipo[$i]= mysql_fetch_object($result_num_tipo[$i]);
					$num_percurso_tipo[$i]= $rs_num_tipo[$i]->total+1;
					
					$result2[$i]= mysql_query("insert into tr_percursos_clientes
											(id_empresa, id_percurso, id_cliente, num_percurso, num_percurso_tipo, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $id_percurso  ."',
											'". $_POST["id_cliente_entrega"][$i] ."', '". $num_percurso[$i] ."', '". $num_percurso_tipo[$i] ."', '". $_SESSION["id_usuario"] ."' )
											") or die(mysql_error());
					if (!$result2[$i]) $var++;
					
					
					// ----------------------------------------------------------------- aqui faz a jogada para identificar todas as ordens dos todos os percuros
				
					$result_confirmacao[$i]= mysql_query("select * from  tr_percursos, tr_percursos_clientes
															where DATE_FORMAT(data_hora_percurso, '%Y-%m-%d') = '". formata_data_hifen($_POST["data_percurso"][0]) ."'
															and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
															and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente_entrega"][$i] ."'
															
															$str_tipo
															
															order by tr_percursos.data_hora_percurso asc
															") or die("3: ". mysql_error());
					
					
					$j=1;
					$normal=1;
					$extra=1;
					$outros=1;
					
					$linhas_confirmacao[$i]= mysql_num_rows($result_confirmacao[$i]);
					
					if ($linhas_confirmacao[$i]>0) {
					
						//echo $rs_cli->apelido_fantasia ."<br><br>";
						
						while ($rs_confirmacao[$i]= mysql_fetch_object($result_confirmacao[$i])) {
							//echo "Ajustando percurso: ". number_format($rs->id_percurso_cliente, 0, ',', '.') ." ". $rs->data_hora_percurso ." ---> ". $i ."<br>";
							
							if ( ($rs_confirmacao[$i]->tipo=="1") || ($rs_confirmacao[$i]->tipo=="2") ) {
								$num_percurso_tipo= $normal;
							}
							if ( ($rs_confirmacao[$i]->tipo=="4") || ($rs_confirmacao[$i]->tipo=="5") ) {
								$num_percurso_tipo= $extra;
							}
							else {
								$num_percurso_tipo= $outros;
							}
							
							$result_atualiza2[$i]= mysql_query("update tr_percursos_clientes
																set num_percurso = '". $j ."',
																num_percurso_tipo = '". $num_percurso_tipo ."'
																where id_percurso_cliente = '". $rs_confirmacao[$i]->id_percurso_cliente ."'
																") or die("4: ". mysql_error());
							
							if ( ($rs_confirmacao[$i]->tipo=="1") || ($rs_confirmacao[$i]->tipo=="2") ) {
								$normal++;
							}
							if ( ($rs_confirmacao[$i]->tipo=="4") || ($rs_confirmacao[$i]->tipo=="5") ) {
								$extra++;
							}
							else {
								$outros++;
							}
							
							$j++;
						}
						
						//echo "<br><br>";
					}

					
					
					$i++;
				}
				
				/*
				$i=0;
				while ($_POST["id_remessa"][$i]!="") {
					$result2[$i]= mysql_query("insert into tr_percursos_remessas
											(id_empresa, id_percurso, id_remessa, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $id_percurso  ."',
											'". $_POST["id_remessa"][$i] ."', '". $_SESSION["id_usuario"] ."' )
											") or die(mysql_error());
					if (!$result2[$i]) $var++;
					$i++;
				}
				*/
				
				$i=0;
				while ($_POST["passo"][$i]!="") {
					$result3[$i]= mysql_query("insert into tr_percursos_passos
											(id_empresa, id_percurso, passo, id_ad, data_percurso, hora_percurso, km, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $id_percurso  ."', '". $_POST["passo"][$i] ."', '". $_POST["id_ad"][$i] ."',
											'". formata_data($_POST["data_percurso"][$i]) ."', '". $_POST["hora_percurso"][$i] ."',
											'". formata_valor($_POST["km"][$i]) ."', '". $_SESSION["id_usuario"] ."')
											") or die(mysql_error());
					if (!$result3[$i]) $var++;
					$i++;
				}
				
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=transporte/percurso_listar&id_percurso=".$id_percurso."&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;

				inicia_transacao();
				
				switch($_POST["tipo"]) {
					case "1":
					case "4":
						$str_tipo= " and   ( tr_percursos.tipo = '1' or tr_percursos.tipo = '4' ) ";
					break;
					
					case "2":
					case "5":
						$str_tipo= " and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' ) ";
					break;
					
					case "3":
						$str_tipo= " and   tr_percursos.tipo = '3' ";
					break;
				}
				
				$result_pre= mysql_query("select * from tr_percursos, tr_percursos_passos
											where tr_percursos.id_percurso = '". $_POST["id_percurso"] ."'
											and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
											and   tr_percursos_passos.passo = '1'
											") or die(mysql_error());
				$rs_pre= mysql_fetch_object($result_pre);
				
				$result1= mysql_query("update tr_percursos set
										id_veiculo= '". $_POST["id_veiculo"] ."',
										tipo= '". $_POST["tipo"] ."',
										obs= '". $_POST["obs"] ."',
										id_motorista= '". $_POST["id_motorista"] ."',
										id_regiao= '". $_POST["id_regiao"] ."',
										veiculo_permissao= '". $_POST["veiculo_permissao"] ."'
										where id_percurso = '". $_POST["id_percurso"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				//primeiro precisa passar e arrumar para todos os clientes que estavam envolvidos, isso evita que clientes retirados fiquem ser alteração alguma
				
				$result_num_pre_cliente= mysql_query("select tr_percursos_clientes.* from tr_percursos, tr_percursos_clientes
												where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos.id_percurso = '". $_POST["id_percurso"] ."'
												
												$str_tipo
												
												and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $rs_pre->data_percurso ."'
												order by tr_percursos.data_hora_percurso asc
												") or die("2: ". mysql_error());
									//die("sorry");			
				//---------------------------------
				
				$result_del1= mysql_query("delete from tr_percursos_clientes
												where id_percurso = '". $_POST["id_percurso"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
				if (!$result_del1) $var++;
				
				$str_passo_corrige= "";
				
				$i=0;
				while ($_POST["id_cliente_entrega"][$i]!="") {
					
					$result2[$i]= mysql_query("insert into tr_percursos_clientes
											(id_empresa, id_percurso, id_cliente, num_percurso, id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_percurso"] ."',
											'". $_POST["id_cliente_entrega"][$i] ."', '". 0 ."', '". $_SESSION["id_usuario"] ."' )
											") or die("1: ". mysql_error());
					if (!$result2[$i]) $var++;
					
					
					//agora sim, vai corrigir o número dos percursos dos clientes que vieram selecionados
					$result_num_pre[$i]= mysql_query("select * from tr_percursos, tr_percursos_clientes
													where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $_POST["id_cliente_entrega"][$i] ."'
													
													$str_tipo
													
													and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $rs_pre->data_percurso ."'
													order by tr_percursos.data_hora_percurso asc
													") or die("2: ". mysql_error());
					
					$j=1;
					$normal=1;
					$extra=1;
					$outros=1;
					
					while ($rs_num[$i]= mysql_fetch_object($result_num_pre[$i])) {
						
						if ( ($rs_num[$i]->tipo=="1") || ($rs_num[$i]->tipo=="2") ) {
							$num_percurso_tipo= $normal;
						}
						if ( ($rs_num[$i]->tipo=="4") || ($rs_num[$i]->tipo=="5") ) {
							$num_percurso_tipo= $extra;
						}
						else {
							$num_percurso_tipo= $outros;
						}
						
						$result_atualiza2= mysql_query("update tr_percursos_clientes
															set num_percurso = '". $j ."',
															num_percurso_tipo = '". $num_percurso_tipo ."'
															where id_percurso_cliente = '". $rs_num[$i]->id_percurso_cliente ."'
															") or die("4: ". mysql_error());
						if (!$result_atualiza2) $var++;
						
						if ( ($rs_num[$i]->tipo=="1") || ($rs_num[$i]->tipo=="2") ) {
							$normal++;
						}
						if ( ($rs_num[$i]->tipo=="4") || ($rs_num[$i]->tipo=="5") ) {
							$extra++;
						}
						else {
							$outros++;
						}
						
						$j++;
						
						
						$result_num_atualiza[$i]= mysql_query("update tr_percursos_clientes
																set num_percurso= '". $np ."'
																
																where   id_percurso_cliente = '". $rs_num[$i]->id_percurso_cliente ."'
																") or die("np: ". mysql_error());
						$np++;
					}
					
					//-
					
					$result_correcao_teste[$i]= mysql_query("select id_percurso_local from tr_percursos_passos
																where id_percurso = '". $_POST["id_percurso"] ."'
																and   id_cliente = '". $_POST["id_cliente_entrega"][$i] ."'
																");
					$linhas_correcao_teste[$i]= mysql_num_rows($result_correcao_teste[$i]);
					
					if ($linhas_correcao_teste[$i]==0) {
						$result_percurso_dados= mysql_query("select * from tr_percursos_passos
																where id_percurso = '". $_POST["id_percurso"] ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																and   passo = '1'
																");
						$rs_percurso_dados= mysql_fetch_object($result_percurso_dados);
						
						$result_correcao[$i]= mysql_query("insert into tr_percursos_passos
															(id_empresa, id_percurso, passo, id_cliente, data_percurso, id_usuario) values
															('". $_SESSION["id_empresa"] ."', '". $_POST["id_percurso"]  ."', '2', '". $_POST["id_cliente_entrega"][$i] ."',
															'". $rs_percurso_dados->data_percurso ."', '". $_SESSION["id_usuario"] ."')
															") or die(mysql_error());
					}
					
					//--- fim correção
					
					$str_passo_corrige.= " and   id_cliente <> '". $_POST["id_cliente_entrega"][$i] ."' ";
					
					$i++;
				}
				
				//continuando o conserto…
				while ($rs_num_cliente= mysql_fetch_object($result_num_pre_cliente)) {
					
					/*echo "select * from tr_percursos, tr_percursos_clientes
													where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $rs_num_cliente->id_cliente ."'
													
													$str_tipo
													
													and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $rs_pre->data_percurso ."'
													order by tr_percursos.data_hora_percurso asc
													<br><br>
													";
					*/
					
					//agora sim, vai corrigir o número dos percursos dos clientes que vieram selecionados
					$result_num_pre2= mysql_query("select * from tr_percursos, tr_percursos_clientes
													where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													and   tr_percursos_clientes.id_cliente = '". $rs_num_cliente->id_cliente ."'
													
													$str_tipo
													
													and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $rs_pre->data_percurso ."'
													order by tr_percursos.data_hora_percurso asc
													") or die("2: ". mysql_error());
					
					$j=1;
					$normal=1;
					$extra=1;
					$outros=1;
					
					while ($rs_num2= mysql_fetch_object($result_num_pre2)) {
						
						
						if ( ($rs_num2->tipo=="1") || ($rs_num2->tipo=="2") ) {
							$num_percurso_tipo= $normal;
						}
						if ( ($rs_num2->tipo=="4") || ($rs_num2->tipo=="5") ) {
							$num_percurso_tipo= $extra;
						}
						else {
							$num_percurso_tipo= $outros;
						}
						
						$result_atualiza2= mysql_query("update tr_percursos_clientes
															set num_percurso = '". $j ."',
															num_percurso_tipo = '". $num_percurso_tipo ."'
															where id_percurso_cliente = '". $rs_num2->id_percurso_cliente ."'
															") or die("4: ". mysql_error());
						if (!$result_atualiza2) $var++;
						
						if ( ($rs_num2->tipo=="1") || ($rs_num2->tipo=="2") ) {
							$normal++;
						}
						if ( ($rs_num2->tipo=="4") || ($rs_num2->tipo=="5") ) {
							$extra++;
						}
						else {
							$outros++;
						}
						
						$j++;
					}
				}
				
				
				$result_passos_corrige= mysql_query("delete from tr_percursos_passos
														where id_percurso = '". $_POST["id_percurso"] ."'
														and   passo = '2'
														$str_passo_corrige
														");
				if (!$result_passos_corrige) $var++;
								
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=transporte/percurso_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formPercursoDados"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_percurso"]!="")) {
			
			$var=0;
			
			inicia_transacao();
			
			switch($_POST["tipo"]) {
				case "1":
				case "4":
					$str_tipo= " and   ( tr_percursos.tipo = '1' or tr_percursos.tipo = '4' ) ";
				break;
				
				case "2":
				case "5":
					$str_tipo= " and   ( tr_percursos.tipo = '2' or tr_percursos.tipo = '5' ) ";
				break;
				
				case "3":
					$str_tipo= " and   tr_percursos.tipo = '3' ";
				break;
			}
			
			$data_hora_percurso= formata_data_hifen($_POST["data_percurso"][0]) ." ". $_POST["hora_percurso"][0];
			
			$result_atual_um= mysql_query("update tr_percursos
											set   id_situacao_atual = '1',
												  data_hora_percurso= '". $data_hora_percurso ."'
											where id_percurso = '". $_POST["id_percurso"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			if (!$result_atual_um) $var++;
			
			$result_del= mysql_query("delete from tr_percursos_passos
												where id_percurso = '". $_POST["id_percurso"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
			if (!$result_del) $var++;
			
			$i=0;
			while ($_POST["passo"][$i]!="") {
				//se é saida ou passagem de cliente faz... se for volta, só faz se a data e hora tiver preenchida
				if (($_POST["passo"][$i]!=3) || (($_POST["passo"][$i]==3) && ($_POST["data_percurso"][$i]!="") && ($_POST["hora_percurso"][$i]!=""))) {
					$result2[$i]= mysql_query("insert into tr_percursos_passos
											(id_empresa, id_percurso, passo, id_cliente, id_ad, data_percurso, hora_percurso, km,
											 peso, pnr, data_registro, hora_registro,
											 id_usuario) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_percurso"]  ."', '". $_POST["passo"][$i] ."',
											'". $_POST["id_cliente"][$i] ."', '". $_POST["id_ad"][$i] ."',
											'". formata_data($_POST["data_percurso"][$i]) ."', '". $_POST["hora_percurso"][$i] ."',
											'". formata_valor($_POST["km"][$i]) ."',
											
											'". formata_valor($_POST["peso"][$i]) ."', '". $_POST["pnr"][$i] ."',
											'". date("Ymd") ."', '". date("His") ."',
											
											'". $_SESSION["id_usuario"] ."')
											") or die(mysql_error());
					if (!$result2[$i]) $var++;
					
					@logs($_SESSION["id_acesso"], $_SESSION["id_usuario"], $_SESSION["id_empresa"], $var, $_POST["id_percurso"],
						  "Passo: ". $_POST["passo"][$i] ." | Cliente: ". pega_sigla_pessoa($_POST["id_cliente"][$i]) ." | Peso: ". $_POST["peso"][$i],
						  1, $_SERVER["REMOTE_ADDR"]);
					
					//atualizar percurso como concluido
					if ($_POST["passo"][$i]==3) {
						$result_atual= mysql_query("update tr_percursos
												   	set   id_situacao_atual = '3'
													where id_percurso = '". $_POST["id_percurso"] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													");
						if (!$result_atual) $var++;
					}
				}
				
				$i++;
			}
			
			$result_clientes= mysql_query("select * from tr_percursos_clientes
											where tr_percursos_clientes.id_percurso = '". $_POST["id_percurso"] ."'
											") or die(mysql_error());
			
			$linhas_clientes= mysql_num_rows($result_clientes);
			
			$i=1;
			while ($rs_clientes= mysql_fetch_object($result_clientes)) {
			
				// ----------------------------------------------------------------- aqui faz a jogada para identificar todas as ordens dos todos os percuros, caso o horário seja mudado ou não
				
				$result_confirmacao[$i]= mysql_query("select * from  tr_percursos, tr_percursos_clientes
														where DATE_FORMAT(data_hora_percurso, '%Y-%m-%d') = '". formata_data_hifen($_POST["data_percurso"][0]) ."'
														and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
														and   tr_percursos_clientes.id_cliente = '". $rs_clientes->id_cliente ."'
														
														$str_tipo
														
														order by tr_percursos.data_hora_percurso asc
														") or die("3: ". mysql_error());
				
				
				$j=1;
				$normal=1;
				$extra=1;
				$outros=1;
				
				$linhas_confirmacao[$i]= mysql_num_rows($result_confirmacao[$i]);
				
				if ($linhas_confirmacao[$i]>0) {
				
					//echo $rs_cli->apelido_fantasia ."<br><br>";
					
					while ($rs_confirmacao[$i]= mysql_fetch_object($result_confirmacao[$i])) {
						//echo "Ajustando percurso: ". number_format($rs->id_percurso_cliente, 0, ',', '.') ." ". $rs->data_hora_percurso ." ---> ". $i ."<br>";
						
						if ( ($rs_confirmacao[$i]->tipo=="1") || ($rs_confirmacao[$i]->tipo=="2") ) {
							$num_percurso_tipo= $normal;
						}
						if ( ($rs_confirmacao[$i]->tipo=="4") || ($rs_confirmacao[$i]->tipo=="5") ) {
							$num_percurso_tipo= $extra;
						}
						else {
							$num_percurso_tipo= $outros;
						}
						
						$result_atualiza2[$i]= mysql_query("update tr_percursos_clientes
															set num_percurso = '". $j ."',
															num_percurso_tipo = '". $num_percurso_tipo ."'
															where id_percurso_cliente = '". $rs_confirmacao[$i]->id_percurso_cliente ."'
															") or die("4: ". mysql_error());
						if (!$result_atualiza2[$i]) $var++;
						
						if ( ($rs_confirmacao[$i]->tipo=="1") || ($rs_confirmacao[$i]->tipo=="2") ) {
							$normal++;
						}
						if ( ($rs_confirmacao[$i]->tipo=="4") || ($rs_confirmacao[$i]->tipo=="5") ) {
							$extra++;
						}
						else {
							$outros++;
						}
						
						$j++;
					}
					
					//echo "<br><br>";
				}
			
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			header("location: ./?pagina=transporte/percurso_listar&msg=". $msg);

		}//fim teste variáveis
	}
}

// ############################################### QUALIDADE #############################################
if (pode("12(", $_SESSION["permissao"])) {
	
	if (isset($_GET["formCosturaConserto"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cliente"]!="") && ($_POST["data_chegada"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$id_grupo= pega_id_grupo_da_peca($_POST["id_tipo_roupa"][0]);
			
				$result1= mysql_query("insert into op_limpa_costura_consertos (id_empresa, id_cliente, data_chegada,
																	 obs, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_cliente"] ."',
										'". formata_data($_POST["data_chegada"]) ."', '". $_POST["obs"] ."', 
										'". $_SESSION["id_usuario"] ."'
										) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$id_costura_conserto= mysql_insert_id();
				
				$i=0;
				while ($_POST["id_tipo_roupa"][$i]) {
					$result2[$i]= mysql_query("insert into op_limpa_costura_consertos_pecas (id_empresa, id_costura_conserto,
																				   id_tipo_roupa, qtde_recebido, qtde_consertado,
																				   qtde_substituido, qtde_baixa, qtde_devolvido, id_motivo_costura) values
											('". $_SESSION["id_empresa"] ."', '". $id_costura_conserto ."',
											 '". $_POST["id_tipo_roupa"][$i] ."',
											 '". $_POST["qtde_recebido"][$i] ."', '". $_POST["qtde_consertado"][$i] ."',
											 '". $_POST["qtde_substituido"][$i] ."', '". $_POST["qtde_baixa"][$i] ."', '". $_POST["qtde_devolvido"][$i] ."',
											 '". $_POST["id_motivo_costura"][$i] ."'
											 ) ") or die(mysql_error());
					if (!$result2[$i]) $var++;
					
					$i++;
				}
				
				if ($i==0) $var++;

				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=qualidade/costura_conserto_listar&msg=". $msg);
			}
			
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				/*$result_pre= mysql_query("select * from op_veiculos
											where veiculo = '". strtoupper($_POST["veiculo"]) ."'
											and   placa = '". strtoupper($_POST["placa"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_veiculo <> '". $_POST["id_veiculo"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update op_limpa_costura_consertos set
											id_cliente = '". $_POST["id_cliente"] ."',
											data_chegada= '". formata_data($_POST["data_chegada"]) ."',
											obs = '". $_POST["obs"] ."',
											data_entrega= '". formata_data($_POST["data_entrega"]) ."',
											hora_entrega = '". $_POST["hora_entrega"] ."',
											id_veiculo = '". $_POST["id_veiculo"] ."',
											peso_entregue = '". formata_valor($_POST["peso_entregue"]) ."'
											
											where id_costura_conserto = '". $_POST["id_costura_conserto"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					
					if (!$result1) $var++;
					
					$result_del= mysql_query("delete from op_limpa_costura_consertos_pecas
												where id_costura_conserto = '". $_POST["id_costura_conserto"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												");
					
					$i=0;
					while ($_POST["id_tipo_roupa"][$i]) {
						$result2[$i]= mysql_query("insert into op_limpa_costura_consertos_pecas (id_empresa, id_costura_conserto,
																					   id_tipo_roupa, qtde_recebido, qtde_consertado, qtde_substituido, qtde_baixa, qtde_devolvido, id_motivo_costura) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_costura_conserto"] ."',
												 '". $_POST["id_tipo_roupa"][$i] ."', '". $_POST["qtde_recebido"][$i] ."', '". $_POST["qtde_consertado"][$i] ."',
												 '". $_POST["qtde_substituido"][$i] ."', '". $_POST["qtde_baixa"][$i] ."', '". $_POST["qtde_devolvido"][$i] ."', '". $_POST["id_motivo_costura"][$i] ."'
												 ) ") or die(mysql_error());
						if (!$result2[$i]) $var++;
						
						$i++;
					}
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=qualidade/costura_conserto_listar&msg=". $msg);
			}//e
			
		}//fim teste variáveis
	}
}

// ############################################### MANUTENÇÃO #############################################
	
if (pode("pkj", $_SESSION["permissao"])) {
	
	if (isset($_GET["formManutencaoChecklistBuscar"])) {
		$pagina= "manutencao/checklist";
		require_once("index2.php");
	}
	
	if (isset($_GET["formManutencaoChecklist"])) {
		if ($_SESSION["id_empresa"]!="") {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["id_checklist_item"][$i]!="") {
				if ($_POST["data_checklist"][$i]!="") {
					/*
					$result_checa[$i]= mysql_query("select from man_checklist
													where id_checklist_item = '". $_POST["id_checklist_item"][$i] ."'
													and   data_checklist = '". $_POST["data_checklist"][$i] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
					*/
					
					$result_pre[$i]= mysql_query("delete from man_checklist
													where id_checklist_item = '". $_POST["id_checklist_item"][$i] ."'
													and   data_checklist = '". $_POST["data_checklist"][$i] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_tecnico = '". $_POST["id_tecnico"] ."'
													") or die(mysql_error());
					
					if ($_POST["valor"][$i]!="") {
						$result1[$i]= mysql_query("insert into man_checklist
												(id_empresa, id_checklist_item, data_checklist, valor, id_tecnico, id_funcionario, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_checklist_item"][$i]  ."', '". $_POST["data_checklist"][$i]  ."',
												'". $_POST["valor"][$i] ."', '". $_POST["id_tecnico"] ."', '". $_SESSION["id_funcionario_sessao"] ."', '". $_SESSION["id_usuario"] ."')
												") or die(mysql_error());
						if (!$result1[$i]) $var++;
					}
				}						
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "manutencao/checklist";
			require_once("index2.php");
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRMEstoqueSaida"])) {
		if (($_POST["id_item"]!="") && ($_POST["qtde"]!="")) {
			$var= 0;
			inicia_transacao();
			
			$result_pre= mysql_query("select qtde_atual from fi_estoque
										where id_empresa= '". $_SESSION["id_empresa"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										");
			
			$rs_pre= mysql_fetch_object($result_pre);
			
			//se a qtde atual for maior ou igual q a solicitada para saída
			if ($rs_pre->qtde_atual >= formata_valor($_POST["qtde"])) {
				
				$result1= mysql_query("insert into fi_estoque_mov
										(id_empresa, id_item, tipo_trans, subtipo_trans, qtde, data_trans, hora_trans, observacoes, id_rm, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."', 's', 'n',
										'". formata_valor($_POST["qtde"]) ."', '". date("Ymd") ."', '". date("His") ."',
										'". strip_tags($_POST["observacoes"]) ."', '". $_POST["id_rm"] ."', '". $_SESSION["id_usuario"] ."')");
				
				if (!$result1) $var++;
				$id_mov= mysql_insert_id();
				
				$result2= mysql_query("update fi_estoque set
										qtde_atual = qtde_atual - '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										") or die(mysql_error());
				
				if (!$result2) $var++;
				
				//if ($_POST["id_motivo"]=="0") {
					$result_iv= mysql_query("select * from fi_estoque_iv
														where id_item= '". $_POST["id_item"] ."'
														and   id_empresa = '". $_SESSION["id_empresa"] ."'
														and   qtde > '0'
														order by data_trans asc, hora_trans asc
														");
					$saldo= 0;
					$i= 0;
					
					while ($rs_iv= mysql_fetch_object($result_iv)) {
						if ($i==0) {
							$var_qtde= $_POST["qtde"];
							$saldo= $_POST["qtde"];
						}
						else $var_qtde= $saldo;
						
						//se o que está dando saída é maior que a linha atual
						if ($var_qtde>=$rs_iv->qtde) {
							$saldo= $_POST["qtde"]-$rs_iv->qtde;
							
							$valor= $rs_iv->qtde*$rs_iv->valor_unitario;
							
							$result3= mysql_query("insert into fi_custos
													(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '8',
													'". $rs_iv->id_centro_custo_tipo ."', '$id_mov',
													'". date("Ymd") ."', '". date("His") ."', '". $valor ."', '1',
													'". $_SESSION["id_usuario"] ."')");
				
							if (!$result3) $var++;
							
							$result4= mysql_query("update fi_estoque_iv
												  	set qtde= '0'
													where id_iv= '". $rs_iv->id_iv ."'
													limit 1
													");
							if (!$result4) $var++;
							
							//salvando os itens que foram dados saida
							
							$result5= mysql_query("insert into fi_estoque_iv_iteracoes
													(id_empresa, id_mov, id_iv, qtde, qtde_removida)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_mov ."', '". $rs_iv->id_iv ."', '". $saldo ."', '". formata_valor($_POST["qtde"]) ."' )");
				
							if (!$result5) $var++;
						}
						else {
							if ($saldo>0) {
								$qtde_aqui=$saldo;
								
								if ($saldo<$rs_iv->qtde) $saldo= 0;
								else $saldo-= $rs_iv->qtde;
								
								$valor= $qtde_aqui*$rs_iv->valor_unitario;
								
								$result3= mysql_query("insert into fi_custos
														(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '8',
														'". $rs_iv->id_centro_custo_tipo ."', '$id_mov',
														'". date("Ymd") ."', '". date("His") ."', '". $valor ."', '1',
														'". $_SESSION["id_usuario"] ."')");
				
								if (!$result3) $var++;
								
								$result4= mysql_query("update fi_estoque_iv
														set qtde = qtde - '". $qtde_aqui ."'
														where id_iv= '". $rs_iv->id_iv ."'
														limit 1
														");
								if (!$result4) $var++;
								
								//salvando os itens que foram dados saida
								$result5= mysql_query("insert into fi_estoque_iv_iteracoes
														(id_empresa, id_mov, id_iv, qtde, qtde_removida)
														values
														('". $_SESSION["id_empresa"] ."', '". $id_mov ."', '". $rs_iv->id_iv ."', '". $saldo ."', '". formata_valor($_POST["qtde"]) ."' )");
					
								if (!$result5) $var++;
							}
						}
						
						$i++;
					}
				//}
				
			}//fim teste js+
			else {
				$var++;
				
				echo "<script language=\"javascript\">alert('Entre com um valor menor do que o estoque atual!');</script>";
			}
			
			if ($var==0) echo "<script language=\"javascript\">atribuiValor('item_busca', ''); atribuiValor('qtde', ''); document.getElementById('id_item')[0].selected=true; alert('Item retirado do estoque e vinculado à esta RM com sucesso!');</script>";
			
			finaliza_transacao($var);
		}
		
		//if ($var==0)
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá saída de remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]) ." subtipo: ". $_POST["subtipo_trans"] ." para ". pega_nome($_POST["id_pessoa"]) ."", $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			
		$msg= $var;
		
		$pagina= "manutencao/rm_estoque_listar";
		
		require_once("index2.php");
	}
	
	if (isset($_GET["formOSEstoqueSaida"])) {
		if (($_POST["id_item"]!="") && ($_POST["qtde"]!="")) {
			$var= 0;
			inicia_transacao();
			
			$result_pre= mysql_query("select qtde_atual from fi_estoque
										where id_empresa= '". $_SESSION["id_empresa"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										");
			
			$rs_pre= mysql_fetch_object($result_pre);
			
			//se a qtde atual for maior ou igual q a solicitada para saída
			if ($rs_pre->qtde_atual >= formata_valor($_POST["qtde"])) {
				
				$result1= mysql_query("insert into fi_estoque_mov
										(id_empresa, id_item, tipo_trans, subtipo_trans, qtde, data_trans, hora_trans, observacoes, id_os, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."', 's', 'n',
										'". formata_valor($_POST["qtde"]) ."', '". date("Ymd") ."', '". date("His") ."',
										'". strip_tags($_POST["observacoes"]) ."', '". $_POST["id_os"] ."', '". $_SESSION["id_usuario"] ."')");
				
				if (!$result1) $var++;
				$id_mov= mysql_insert_id();
				
				$result2= mysql_query("update fi_estoque set
										qtde_atual = qtde_atual - '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										") or die(mysql_error());
				
				if (!$result2) $var++;
				
				//if ($_POST["id_motivo"]=="0") {
					$result_iv= mysql_query("select * from fi_estoque_iv
												where id_item= '". $_POST["id_item"] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   qtde > '0'
												order by data_trans asc, hora_trans asc
												");
					$saldo= 0;
					$i= 0;
					
					while ($rs_iv= mysql_fetch_object($result_iv)) {
						if ($i==0) {
							$var_qtde= $_POST["qtde"];
							$saldo= $_POST["qtde"];
						}
						else $var_qtde= $saldo;
						
						//se o que está dando saída é maior que a linha atual
						if ($var_qtde>=$rs_iv->qtde) {
							$saldo= $_POST["qtde"]-$rs_iv->qtde;
							
							$valor= $rs_iv->qtde*$rs_iv->valor_unitario;
							
							$result3= mysql_query("insert into fi_custos
													(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '8',
													'". $rs_iv->id_centro_custo_tipo ."', '$id_mov',
													'". date("Ymd") ."', '". date("His") ."', '". $valor ."', '1',
													'". $_SESSION["id_usuario"] ."')");
				
							if (!$result3) $var++;
							
							$result4= mysql_query("update fi_estoque_iv
												  	set qtde= '0'
													where id_iv= '". $rs_iv->id_iv ."'
													limit 1
													");
							if (!$result4) $var++;
							
							//salvando os itens que foram dados saida
							
							$result5= mysql_query("insert into fi_estoque_iv_iteracoes
													(id_empresa, id_mov, id_iv, qtde, qtde_removida)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_mov ."', '". $rs_iv->id_iv ."', '". $saldo ."', '". formata_valor($_POST["qtde"]) ."' )");
				
							if (!$result5) $var++;
						}
						else {
							if ($saldo>0) {
								$qtde_aqui=$saldo;
								
								if ($saldo<$rs_iv->qtde) $saldo= 0;
								else $saldo-= $rs_iv->qtde;
								
								$valor= $qtde_aqui*$rs_iv->valor_unitario;
								
								$result3= mysql_query("insert into fi_custos
														(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '8',
														'". $rs_iv->id_centro_custo_tipo ."', '$id_mov',
														'". date("Ymd") ."', '". date("His") ."', '". $valor ."', '1',
														'". $_SESSION["id_usuario"] ."')");
				
								if (!$result3) $var++;
								
								$result4= mysql_query("update fi_estoque_iv
														set qtde = qtde - '". $qtde_aqui ."'
														where id_iv= '". $rs_iv->id_iv ."'
														limit 1
														");
								if (!$result4) $var++;
								
								//salvando os itens que foram dados saida
								$result5= mysql_query("insert into fi_estoque_iv_iteracoes
														(id_empresa, id_mov, id_iv, qtde, qtde_removida)
														values
														('". $_SESSION["id_empresa"] ."', '". $id_mov ."', '". $rs_iv->id_iv ."', '". $saldo ."', '". formata_valor($_POST["qtde"]) ."' )");
					
								if (!$result5) $var++;
							}
						}
						
						$i++;
					}
				//}
				
			}//fim teste js+
			else {
				$var++;
				
				echo "<script language=\"javascript\">alert('Entre com um valor menor do que o estoque atual!');</script>";
			}
			
			if ($var==0) echo "<script language=\"javascript\">atribuiValor('item_busca', ''); atribuiValor('qtde', ''); document.getElementById('id_item')[0].selected=true; alert('Item retirado do estoque e vinculado à esta OS com sucesso!');</script>";
			
			finaliza_transacao($var);
		}
		
		//if ($var==0)
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá saída de remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]) ." subtipo: ". $_POST["subtipo_trans"] ." para ". pega_nome($_POST["id_pessoa"]) ."", $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			
		$msg= $var;
		
		$pagina= "manutencao/os_estoque_listar";
		
		require_once("index2.php");
	}
	
	if (isset($_GET["formRMTempoTrabalho"])) {
		if ($_POST["id_rm_servico"]!="") {
			//inserir
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$result1= mysql_query("update man_rms_servicos set
										data_inicio = '". formata_data($_POST["data_inicio"]) ."',
										hora_inicio = '". $_POST["hora_inicio"] ."',
										data_fim = '". formata_data($_POST["data_fim"]) ."',
										hora_fim = '". $_POST["hora_fim"] ."'
										where id_rm_servico = '". $_POST["id_rm_servico"] ."'
										and id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				if ($msg==0) {
					echo "<script language=\"javascript\">fechaDiv('tela_tempo_servico'); ajaxLink('tempo_servico', 'carregaPagina&pagina=manutencao/rm_tempo_trabalho_listar&id_rm=". $_POST["id_rm"] ."');</script>";
				}
				else echo "Não foi possível atualizar, tente novamente!";
			}
				
		}//fim teste variáveis
	}
	
	if (isset($_GET["formOSTempoTrabalho"])) {
		if ($_POST["id_os_servico"]!="") {
			//inserir
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$result1= mysql_query("update man_oss_servicos set
										data_inicio = '". formata_data($_POST["data_inicio"]) ."',
										hora_inicio = '". $_POST["hora_inicio"] ."',
										data_fim = '". formata_data($_POST["data_fim"]) ."',
										hora_fim = '". $_POST["hora_fim"] ."'
										where id_os_servico = '". $_POST["id_os_servico"] ."'
										and id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				if ($msg==0) {
					echo "<script language=\"javascript\">fechaDiv('tela_tempo_servico'); ajaxLink('tempo_servico', 'carregaPagina&pagina=manutencao/os_tempo_trabalho_listar&id_os=". $_POST["id_os"] ."');</script>";
				}
				else echo "Não foi possível atualizar, tente novamente!";
			}
				
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRMAndamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_situacao"]!="") && ($_POST["id_rm"]!="")) {
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into man_rms_andamento (id_empresa, id_rm, id_situacao, data_rm_andamento, hora_rm_andamento, obs, id_usuario)
													   values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_rm"] ."', '". $_POST["id_situacao"] ."',
										'". date("Ymd") ."', '". date("H:i:s") ."',
										'". $_POST["obs"] ."',
										'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			
			finaliza_transacao($var);
			
			$msg= $var;
			header("location: ./?pagina=manutencao/rm&acao=e&id_rm=". $_POST["id_rm"] ."&msg=". $msg);
		}//fim teste variáveis
	}
	
	if (isset($_GET["formServicoTipo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["servico_tipo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result1= mysql_query("insert into man_servicos_tipos (id_empresa, servico_tipo) values
										('". $_SESSION["id_empresa"] ."',
										'". $_POST["servico_tipo"] ."') ") or die(mysql_error());
				if (!$result1) $var++;
					
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "manutencao/servico_tipo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				inicia_transacao();
				
				$result1= mysql_query("update man_servicos_tipos set
										servico_tipo= '". $_POST["servico_tipo"] ."'
										where id_servico_tipo = '". $_POST["id_servico_tipo"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "manutencao/servico_tipo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formRM"])) {
		if (($_SESSION["id_empresa"]!="") ) {
			
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				
				inicia_transacao();
				
				$num_rm= (pega_num_ultima_rm($_SESSION["id_empresa"])+1);
				
				$result1= mysql_query("insert into man_rms (id_empresa, id_departamento, id_servico_tipo, num_rm, tipo_rm, finalidade_rm, id_equipamento, item, area,
														   prioridade_dias, problema, id_tecnico_preferencial, id_usuario) values
										    ('". $_SESSION["id_empresa"] ."', '". $_POST["id_departamento"] ."', '". $_POST["id_servico_tipo"] ."', '$num_rm', '". $_POST["tipo_rm"] ."', '". $_POST["finalidade_rm"] ."',
											'". $_POST["id_equipamento"] ."', '". $_POST["item"] ."', '". $_POST["area"] ."',
											'". $_POST["prioridade_dias"] ."', '". $_POST["problema"] ."', '". $_POST["id_tecnico_preferencial"] ."',
											'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_rm= mysql_insert_id();
				
				$result2= mysql_query("insert into man_rms_andamento (id_empresa, id_rm, id_situacao, data_rm_andamento, hora_rm_andamento, obs, id_usuario)
														   values
										    ('". $_SESSION["id_empresa"] ."', '". $id_rm ."', '1',
											'". formata_data($_POST["data_rm"]) ."', '". $_POST["hora_rm"] ."',			   
											'', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result2) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/rm_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;

				inicia_transacao();
				
				$result1= mysql_query("update man_rms set
										tipo_rm= '". $_POST["tipo_rm"] ."',
										finalidade_rm= '". $_POST["finalidade_rm"] ."',
										id_equipamento= '". $_POST["id_equipamento"] ."',
										id_departamento= '". $_POST["id_departamento"] ."',
										id_servico_tipo= '". $_POST["id_servico_tipo"] ."',
										item= '". $_POST["item"] ."',
										area= '". $_POST["area"] ."',
										prioridade_dias= '". $_POST["prioridade_dias"] ."',
										problema= '". $_POST["problema"] ."',
										id_tecnico_preferencial= '". $_POST["id_tecnico_preferencial"] ."'
										where id_rm = '". $_POST["id_rm"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/rm_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formOS"])) {
		if (($_SESSION["id_empresa"]!="")) {
			
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				
				inicia_transacao();
				
				$num_os= (pega_num_ultima_os($_SESSION["id_empresa"])+1);
				
				$result1= mysql_query("insert into man_oss (id_empresa, num_os, id_departamento, id_servico_tipo, prioridade_dias, data_os, hora_os, local_os, tipo_os, id_cliente, id_equipamento, item, area,
														   descricao, id_tecnico, id_usuario) values
										    ('". $_SESSION["id_empresa"] ."', '$num_os',
										    
										    '". $_POST["id_departamento"] ."', '". $_POST["id_servico_tipo"] ."', '". $_POST["prioridade_dias"] ."',
										    
										    '". formata_data($_POST["data_os"]) ."', '". $_POST["hora_os"] ."', '". $_POST["local_os"] ."', '". $_POST["tipo_os"] ."', '". $_POST["id_cliente"] ."',
											'". $_POST["id_equipamento"] ."', '". $_POST["item"] ."', '". $_POST["area"] ."',
											'". $_POST["descricao"] ."', '". $_POST["id_tecnico"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_os= mysql_insert_id();
				
				$result2= mysql_query("insert into man_oss_andamento (id_empresa, id_os, id_situacao, data_os_andamento, hora_os_andamento, obs, id_usuario)
														   values
										    ('". $_SESSION["id_empresa"] ."', '". $id_os ."', '1',
											'". formata_data($_POST["data_os"]) ."', '". $_POST["hora_os"] ."',			   
											'', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result2) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/os_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;

				inicia_transacao();
				
				$result1= mysql_query("update man_oss set
										local_os= '". $_POST["local_os"] ."',
										tipo_os= '". $_POST["tipo_os"] ."',
										
										id_departamento= '". $_POST["tipo_os"] ."',
										id_servico_tipo= '". $_POST["id_servico_tipo"] ."',
										prioridade_dias= '". $_POST["prioridade_dias"] ."',
										
										id_equipamento= '". $_POST["id_equipamento"] ."',
										id_cliente= '". $_POST["id_cliente"] ."',
										item= '". $_POST["item"] ."',
										area= '". $_POST["area"] ."',
										id_tecnico= '". $_POST["id_tecnico"] ."',
										descricao= '". $_POST["descricao"] ."'
										where id_os = '". $_POST["id_os"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/os_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formOSAndamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_situacao"]!="") && ($_POST["id_os"]!="")) {
			$var=0;
			inicia_transacao();
			
			$result1= mysql_query("insert into man_oss_andamento (id_empresa, id_os, id_situacao, data_os_andamento, hora_os_andamento, obs, id_usuario)
													   values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_os"] ."', '". $_POST["id_situacao"] ."',
										'". date("Ymd") ."', '". date("H:i:s") ."',
										'". $_POST["obs"] ."',
										'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			
			finaliza_transacao($var);
			
			$msg= $var;
			header("location: ./?pagina=manutencao/os&acao=e&id_os=". $_POST["id_os"] ."&msg=". $msg);
		}//fim teste variáveis
	}
	
	if (isset($_GET["formManutencaoTecnico"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["tipo_tecnico"]!="")) {
			
			if ($_POST["tipo_tecnico"]==2) $id_funcionario_aqui=0;
			else $id_funcionario_aqui=$_POST["id_funcionario"];
				
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				
				inicia_transacao();	
				
				$result1= mysql_query("insert into man_tecnicos (id_empresa, num_tecnico, tipo_tecnico,
																 id_funcionario, nome_tecnico, status_tecnico, id_usuario) values
										    ('". $_SESSION["id_empresa"] ."', '". $_POST["num_tecnico"] ."', '". $_POST["tipo_tecnico"] ."',
											'". $id_funcionario_aqui ."', '". $_POST["nome_tecnico"] ."', '1',
											'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/tecnico_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;

				inicia_transacao();
				
				$result1= mysql_query("update man_tecnicos set
										num_tecnico= '". $_POST["num_tecnico"] ."',
										tipo_tecnico= '". $_POST["tipo_tecnico"] ."',
										id_funcionario= '". $id_funcionario_aqui ."',
										nome_tecnico= '". $_POST["nome_tecnico"] ."'
										where id_tecnico = '". $_POST["id_tecnico"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=manutencao/tecnico_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
}


if (pode("t", $_SESSION["permissao"])) {
	
	if (isset($_GET["formContatoBuscar"])) {
		$pagina= "contatos/contato_esquema";
		require_once("index2.php");
	}
	
	if (isset($_GET["formContato"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["nome"]!="")) {
			
			$i=0; $rels= '.';
			while ($_POST["rel"][$i]) {
				$rels.= $_POST["rel"][$i];
				$i++;
			}
			$rels.= '.';
			
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				$result_pre= mysql_query("select * from tel_contatos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   nome = '". $_POST["nome"] ."' ") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$id_versao= checa_versao_contatos('n', $_SESSION["id_empresa"], $_SESSION["id_usuario"]);
					
					$result1= mysql_query("insert into tel_contatos (id_empresa, tipo_contato, nome, email, obs, id_usuario, rel) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["tipo_contato"] ."', '". strtoupper($_POST["nome"]) ."',
												'". $_POST["email"] ."', '". $_POST["obs1"] ."',
												'". $_SESSION["id_usuario"] ."', '". $rels ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
					$id_contato= mysql_insert_id();
					
					$result_del= mysql_query("delete from tel_contatos_telefones
													where id_contato = '". $id_contato ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
					if (!$result_del) $var++;
					
					$i=0;
					while ($_POST["telefone"][$i]!="") {
						$result2[$i]= mysql_query("insert into tel_contatos_telefones
												(id_empresa, id_contato, telefone, tipo, obs) values
												('". $_SESSION["id_empresa"] ."', '". $id_contato  ."', '". $_POST["telefone"][$i] ."',
												'". $_POST["tipo"][$i] ."', '". strtoupper($_POST["obs"][$i]) ."')
												") or die(mysql_error());
						if (!$result2[$i]) $var++;
						$i++;
					}
					
					finaliza_transacao($var);
				} else $var++;
				
				$letra= strtolower(substr($_POST["nome"], 0, 1));
				
				$msg= $var;
				$pagina= "contatos/contato_esquema";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select * from tel_contatos_telefones
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   telefone = '". $_POST["telefone"] ."'
											and   id_contato <> '". $_POST["id_contato"] ."'
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$id_versao= checa_versao_contatos('n', $_SESSION["id_empresa"], $_SESSION["id_usuario"]);
					
					if ($_POST["tipo_contato"]==2) $id_pessoa_contato= $_POST["id_pessoa"];
					else $id_pessoa_contato= 0;
					
					$result1= mysql_query("update tel_contatos set
											nome= '". strtoupper($_POST["nome"]) ."',
											tipo_contato= '". $_POST["tipo_contato"] ."',
											email= '". $_POST["email"] ."',
											id_pessoa= '". $id_pessoa_contato ."',
											obs= '". $_POST["obs1"] ."',
											rel= '". $rels ."'
											where id_contato = '". $_POST["id_contato"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$result_del= mysql_query("delete from tel_contatos_telefones
													where id_contato = '". $_POST["id_contato"] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
					
					if (!$result_del) $var++;
					
					$i=0;
					while ($_POST["telefone"][$i]!="") {
						$result2[$i]= mysql_query("insert into tel_contatos_telefones
												(id_empresa, id_contato, telefone, tipo, obs) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_contato"]  ."', '". $_POST["telefone"][$i] ."',
												'". $_POST["tipo"][$i] ."', '". strtoupper($_POST["obs"][$i]) ."')
												") or die(mysql_error());
						if (!$result2[$i]) $var++;
						
						$i++;
					}
					
					finaliza_transacao($var);
				} else $var++;
				
				$letra= strtolower(substr($_POST["nome"], 0, 1));
				
				$msg= $var;
				$pagina= "contatos/contato_esquema";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formLigacao"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["telefone"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
			
				inicia_transacao();
				
				$result1= mysql_query("insert into tel_contatos_ligacoes (id_empresa, id_funcionario, telefone, para, data_ligacao, hora_ligacao, obs, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $_POST["telefone"] ."',
											'". strtoupper($_POST["para"]) ."', '". formata_data_hifen($_POST["data_ligacao"]) ."',
											'". $_POST["hora_ligacao"] ."', '". strtoupper($_POST["obs"]) ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;

				finaliza_transacao($var);

				$msg= $var;
				$pagina= "contatos/ligacao_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
									
				$result1= mysql_query("update tel_contatos_ligacoes set
										id_funcionario= '". $_POST["id_funcionario"] ."',
										telefone= '". $_POST["telefone"] ."',
										para= '". strtoupper($_POST["para"]) ."',
										data_ligacao= '". formata_data_hifen($_POST["data_ligacao"]) ."',
										hora_ligacao= '". $_POST["hora_ligacao"] ."',
										obs= '". strtoupper($_POST["obs"]) ."'
										where id_ligacao = '". $_POST["id_ligacao"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
								
				finaliza_transacao($var);
				
				$msg= $var;
				$pagina= "contatos/ligacao_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
}//pode t

// ####################################### EMISSÃO DE DOCUMENTOS ##########################################

if (pode("c3", $_SESSION["permissao"])) {
	//echo 1;
	if (isset($_GET["formDocumentoEmissaoBuscar"])) {
		$pagina= "dc/documento_emissao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formDocumentoEmissao"])) {
		if ($_SESSION["id_empresa"]!="") {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$ano= substr($_POST["data_emissao"], 6, 4);
				
				$result_pre= mysql_query("select * from dc_documentos_emissoes
										 	where tipo = '". $_POST["tipo"] ."'
											and   tipo_documento = '". $_POST["tipo_documento"] ."'
											and   ano = '". $ano ."'
											and   id_empresa = '". $_POST["id_empresa"] ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				$num= ($linhas_pre+1);
				
				$result1= mysql_query("insert into dc_documentos_emissoes (id_empresa, tipo_documento, num, ano, tipo, data_emissao,
																		   de, para, para2, cc, obs, assunto, mensagem,
																		   fax, telefone, status_documento,
																		   metodo, metodo_num, id_departamento, id_turno, situacao,
																		   hora_inicio, hora_termino, dirigente, participantes, relator, cidade_uf, turno,
																		   id_usuario)
									  									   values
										('". $_POST["id_empresa"] ."', '". $_POST["tipo_documento"] ."', '". $num ."', '". $ano ."', '". $_POST["tipo"] ."', '". formata_data($_POST["data_emissao"]) ."',
										 '". ($_POST["de"]) ."', '". ($_POST["para"]) ."', '". ($_POST["para2"]) ."', '". $_POST["cc"] ."', '". $_POST["obs"] ."', '". $_POST["assunto"] ."', '". $_POST["mensagem"] ."',
										 '". $_POST["fax"] ."', '". $_POST["telefone"] ."', '1',
										 '". $_POST["metodo"] ."', '". $_POST["metodo_num"] ."', '". $_POST["id_departamento"] ."', '". $_POST["id_turno"] ."', '". $_POST["situacao"] ."', 
										 '". $_POST["hora_inicio"] ."', '". $_POST["hora_termino"] ."', '". $_POST["dirigente"] ."', '". $_POST["participantes"] ."', '". $_POST["relator"] ."', '". $_POST["cidade_uf"] ."', 
										 '". $_POST["turno"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=dc/documento_emissao_listar&tipo=". $_POST["tipo"] ."&tipo_documento=". $_POST["tipo_documento"] ."&metodo=". $_POST["metodo"] ."&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$result1= mysql_query("update dc_documentos_emissoes set
										id_empresa = '". $_POST["id_empresa"] ."',
										data_emissao= '". formata_data($_POST["data_emissao"]) ."',
										de= '". ($_POST["de"]) ."',
										para= '". ($_POST["para"]) ."',
										para2= '". ($_POST["para2"]) ."',
										cc= '". $_POST["cc"] ."',
										obs= '". $_POST["obs"] ."',
										assunto= '". $_POST["assunto"] ."',
										mensagem= '". $_POST["mensagem"] ."',
										fax= '". $_POST["fax"] ."',
										telefone= '". $_POST["telefone"] ."',
										num= '". $_POST["num"] ."',
										metodo= '". $_POST["metodo"] ."',
										metodo_num= '". $_POST["metodo_num"] ."',
										id_departamento= '". $_POST["id_departamento"] ."',
										id_turno= '". $_POST["id_turno"] ."',
										situacao= '". $_POST["situacao"] ."',
										
										hora_inicio= '". $_POST["hora_inicio"] ."',
										hora_termino= '". $_POST["hora_termino"] ."',
										dirigente= '". $_POST["dirigente"] ."',
										participantes= '". $_POST["participantes"] ."',
										relator= '". $_POST["relator"] ."',
										turno= '". $_POST["turno"] ."',
										cidade_uf= '". $_POST["cidade_uf"] ."'
										
										where id_documento_emissao = '". $_POST["id_documento_emissao"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				header("location: ./?pagina=dc/documento_emissao_listar&tipo=". $_POST["tipo"] ."&tipo_documento=". $_POST["tipo_documento"] ."&metodo=". $_POST["metodo"] ."&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
}

// ############################################ DOCUMENTOS ###############################################

if (pode("d", $_SESSION["permissao"])) {
	//echo 1;
	if (isset($_GET["formDocumentoBuscar"])) {
		$pagina= "dc/documento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formPasta"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select * from dc_documentos_pastas
										 	where id_empresa= '". $_POST["id_empresa"] ."'
											and   id_departamento = '". $_POST["id_departamento"] ."'
											and   pasta = '". strtoupper($_POST["pasta"]) ."'
											and   status_pasta = '". $_POST["status_pasta"] ."'
											") or die(mysql_error());
				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into dc_documentos_pastas (id_empresa, id_departamento, pasta, nome_pasta, status_pasta, id_usuario) values
											('". $_POST["id_empresa"] ."', '". $_POST["id_departamento"] ."',
												'". strtoupper($_POST["pasta"]) ."', '". strtoupper($_POST["nome_pasta"]) ."',
												'". $_POST["status_pasta"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$id_pasta= mysql_insert_id();
				}
				else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				if ($msg==0) header("location: ./?pagina=dc/documento&acao=i&id_departamento=". $_POST["id_departamento"] ."&id_pasta=". $id_pasta ."&msg=". $msg);
				else header("location: ./?pagina=dc/documento_pasta_listar&id_departamento=". $_POST["id_departamento"] ."&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				/*$result_pre= mysql_query("select centro_custo from fi_centro_custos
											where centro_custo = '". strtoupper($_POST["centro_custo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_centro_custo <> '". $_POST["id_centro_custo"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update dc_documentos_pastas set
											pasta= '". strtoupper($_POST["pasta"]) ."',
											nome_pasta= '". strtoupper($_POST["nome_pasta"]) ."',
											id_empresa= '". $_POST["id_empresa"] ."',
											id_departamento= '". $_POST["id_departamento"] ."',
											status_pasta= '". $_POST["status_pasta"] ."'
											where id_pasta = '". $_POST["id_pasta"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=dc/documento_pasta_listar&id_departamento=". $_POST["id_departamento"] ."&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formDocumento"])) {
		//echo 2;
		if ($_POST["id_pasta"]!="") {
			//echo 3;
			//inserir
			if ($_GET["acao"]=="i") {	
				//echo 4;
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into dc_documentos (id_empresa, id_pasta, documento,
																  data_emissao, data_vencimento, alerta_dias, obs, id_usuario, id_mensagem) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["id_pasta"] ."',
											'". strtoupper($_POST["documento"]) ."',
											'". formata_data($_POST["data_emissao"]) ."', '". formata_data($_POST["data_vencimento"]) ."',
											'". $_POST["alerta_dias"] ."', '". $_POST["obs"] ."',
											'". $_SESSION["id_usuario"] ."', '0' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=dc/documento&acao=i&msg=". $msg ."&id_departamento=". $_POST["id_departamento"] ."&id_pasta=". $_POST["id_pasta"]);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				/*$result_pre= mysql_query("select centro_custo from fi_centro_custos
											where centro_custo = '". strtoupper($_POST["centro_custo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_centro_custo <> '". $_POST["id_centro_custo"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {*/
					inicia_transacao();
					
					$result1= mysql_query("update dc_documentos set
											documento= '". strtoupper($_POST["documento"]) ."',
											id_pasta= '". $_POST["id_pasta"] ."',
											data_emissao= '". formata_data($_POST["data_emissao"]) ."',
											data_vencimento= '". formata_data($_POST["data_vencimento"]) ."',
											alerta_dias= '". $_POST["alerta_dias"] ."',
											obs= '". $_POST["obs"] ."'
											where id_documento = '". $_POST["id_documento"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				//} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=dc/documento_listar&id_pasta=". $_POST[id_pasta] ."&msg=". $msg);
			}//e
		}//fim teste variáveis
		else echo "Faltam dados.";
	}
}

// ########################################### FINANCEIRO ###########################################

if (pode("iq", $_SESSION["permissao"])) {
	
	if (isset($_GET["formNotaBuscar"])) {
		$pagina= "financeiro/nota_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formEstoqueBalanco"])) {
		$pagina= "financeiro/estoque_balanco";
		require_once("index2.php");
	}
	if (isset($_GET["formEstoqueBalanco2"])) {
		$pagina= "financeiro/estoque_balanco";
		require_once("index2.php");
	}
	
	if (isset($_GET["formNotaDetalhamento"])) {
		
		//inserir
		if ($_GET["acao"]=="i") {	
			inicia_transacao();
			$var=0;
		
			$result_pre= mysql_query("select * from fi_notas
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_cedente = '". $_POST["id_cedente"] ."'
										and   num_nota = '". $_POST["num_nota"] ."'
										");
			$linhas_pre= mysql_num_rows($result_pre);
			
			if ($linhas_pre==0) {
				
				if ($_POST["pagar"]==1) {
					$status_parcela_aqui=1;
				}
				else {
					$status_parcela_aqui=0;
				}
				
				$result1_insere= mysql_query("insert into fi_notas (id_empresa, tipo_nota, status_nota, id_cedente, num_nota,
															 data_emissao, data_vencimento, valor_total,
															 id_usuario)
															 values
										('". $_SESSION["id_empresa"] ."', '". $_POST["tipo_nota"] ."', '". $status_parcela_aqui ."',
										 '". $_POST["id_cedente"] ."', '". $_POST["num_nota"] ."',
										 '". formata_data($_POST["data_emissao"]) ."', '". formata_data($_POST["data_vencimento"][0]) ."',
										 '0',
										 '". $_SESSION["id_usuario"] ."'
										) ") or die(mysql_error());
				if (!$result1_insere) $var++;
				
				$id_nota= mysql_insert_id();
				
				//------------------------------------ parcelas
				
				$valor_total_somado=0;
				
				$i=0;
				while ($_POST["data_vencimento"][$i]!="") {
					if (($_POST["data_vencimento"][$i]!="") && ($_POST["valor"][$i]!="") ) {
						
						$result1[$i]= mysql_query("insert into fi_notas_parcelas
														(id_empresa, id_nota, data_vencimento, valor, status_parcela)
														values
														('". $_SESSION["id_empresa"] ."',
														'". $id_nota ."', '". formata_data($_POST["data_vencimento"][$i]) ."',
														'". formata_valor($_POST["valor"][$i]) ."', '". $status_parcela_aqui ."'
														)") or die("2: ". mysql_error());
						if (!$result1[$i]) $var++;
						
						$valor_total_somado+=formata_valor($_POST["valor"][$i]);
						
						$id_parcela= mysql_insert_id();
						
						if ($_POST["pagar"]==1) { 
							$result1_pagamento= mysql_query("insert into fi_notas_parcelas_pagamentos
															(id_parcela, id_empresa, id_nota, data_pagamento, valor_pago, integral)
															values
															('". $id_parcela ."', '". $_SESSION["id_empresa"] ."',
															'". $id_nota ."', '". formata_data($_POST["data_vencimento"][$i]) ."',
															'". formata_valor($_POST["valor"][$i]) ."', '1'
															)") or die("2: ". mysql_error());
							if (!$result1_pagamento) $var++;
							
						}
					}						
					$i++;
				}
				
				$result_atualiza= mysql_query(" update fi_notas
												set valor_total = '". $valor_total_somado ."'
												where id_nota = '". $id_nota ."'
												") or die(mysql_error());
				if (!$result_atualiza) $var++;
				
				//------------------------------------ pagamento
				
				$acao='e';
				
				$pagina_redirecionar= "financeiro/nota_esquema";
				$pagina_inclui="_financeiro/__nota_detalhamento.php";
			}
			else {
				$var++;
				$acao='i';
				
				$msg_str= "Nota já cadastrada!";
				
				$pagina_redirecionar= "financeiro/nota_detalhamento";
			}
			
			finaliza_transacao($var);
			$msg= $var;
				
			$pagina= $pagina_redirecionar;
			require_once("index2.php");
		}
		
		if ($_GET["acao"]=="e") {	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_nota"]!="")) {
				inicia_transacao();
				$var=0;
				
				$result1_at= mysql_query("update fi_notas set
											id_cedente= '". $_POST["id_cedente"] ."',
											num_nota= '". $_POST["num_nota"] ."',
											data_emissao= '". formata_data($_POST["data_emissao"]) ."'
											where id_nota = '". $_POST["id_nota"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
				if (!$result1_at) $var++;
				
				$result_pre= mysql_query("select * from fi_notas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_nota = '". $_POST["id_nota"] ."'
											limit 1
											") or die("2: ". mysql_error());
				$rs_pre= mysql_fetch_object($result_pre);
				if (mysql_num_rows($result_pre)==0) die("Nota inexistente!");
				
				$result_limpa= mysql_query("delete from fi_notas_parcelas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_nota = '". $_POST["id_nota"] ."'
											and   status_parcela = '0'
											") or die("1: ". mysql_error());
				if (!$result_limpa) $var++;
				
				$valor_total_somado=0;
				
				$i=0;
				while ($_POST["data_vencimento"][$i]!="") {
					if (($_POST["data_vencimento"][$i]!="") && ($_POST["valor"][$i]!="") ) {
						$result1[$i]= mysql_query("insert into fi_notas_parcelas
														(id_empresa, id_nota, data_vencimento, valor, status_parcela)
														values
														('". $_SESSION["id_empresa"] ."',
														'". $_POST["id_nota"] ."', '". formata_data($_POST["data_vencimento"][$i]) ."',
														'". formata_valor($_POST["valor"][$i]) ."', '0'
														)") or die("2: ". mysql_error());
						if (!$result1[$i]) $var++;
						
						$valor_total_somado+=formata_valor($_POST["valor"][$i]);
					}						
					$i++;
				}
				
				$result_atualiza= mysql_query(" update fi_notas
												set valor_total = '". $valor_total_somado ."'
												where id_nota = '". $_POST["id_nota"] ."'
												") or die(mysql_error());
				if (!$result_atualiza) $var++;
				
				/*
				//pegando todas os itens atuais da nota e tirando do sistema
				$result_limpa0= mysql_query("select * from fi_notas_itens
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_nota = '". $_POST["id_nota"] ."'
												");
				
				while ($rs_limpa0= mysql_fetch_object($result_limpa0)) {
					$result_pre_limpa4= mysql_query("update fi_estoque_iv set
														qtde = qtde - '". $rs_limpa0->qtde ."'
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_item= '". $rs_limpa0->id_item ."'
														and   valor_unitario = '". $rs_limpa0->valor_unitario ."'
														and   qtde > 0
														and   id_nota_item = '". $rs_limpa0->id_nota_item ."'
														") or die("6: ". mysql_error());
					if (!$result_pre_limpa4) $var++;	
				}
				*/
				
				$result_limpa2= mysql_query("delete from fi_notas_itens
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_nota = '". $_POST["id_nota"] ."'
												") or die("11: ". mysql_error());
				if (!$result_limpa2) $var++;
				
				
				//############################### LIMPA ESTOQUE #############################
				
				$result_pre_limpa3= mysql_query("select * from fi_estoque_mov
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_nota = '". $_POST["id_nota"] ."'
													and   modo= '2'
													") or die("11: ". mysql_error());
				if (!$result_pre_limpa3) $var++;
				
				//se tiver alguma coisa...
				if (mysql_num_rows($result_pre_limpa3)>0) {
					
					while ($rs_pre_limpa3= mysql_fetch_object($result_pre_limpa3)) {
						$result_pre_limpa4= mysql_query("update fi_estoque set
															qtde_atual = qtde_atual - '". $rs_pre_limpa3->qtde ."'
															where id_empresa = '". $_SESSION["id_empresa"] ."'
															and   id_item= '". $rs_pre_limpa3->id_item ."'
															") or die("6: ". mysql_error());
						if (!$result_pre_limpa4) $var++;
						
						$result_limpa1= mysql_query("update fi_estoque_iv
													set qtde = qtde - '". $rs_pre_limpa3->qtde ."'
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_item= '". $rs_pre_limpa3->id_item ."'
													and   valor_unitario = ". $rs_pre_limpa3->valor_unitario ."
													") or die("11: ". mysql_error());
						if (!$result_limpa1) $var++;
					}
				}
				
				$result_limpa4= mysql_query("delete from fi_estoque_mov
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_nota = '". $_POST["id_nota"] ."'
												and   modo= '2'
												") or die("11: ". mysql_error());
				if (!$result_limpa4) $var++;
				
				//############################## /LIMPA ESTOQUE #############################
				
				//############################### LIMPA CC #############################
				
				$result_limpa7= mysql_query("delete from fi_custos
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_nota = '". $_POST["id_nota"] ."'
												and   modo= '2'
												") or die("12: ". mysql_error());
				if (!$result_limpa7) $var++;
				
				//##############################/ LIMPA CC #############################
				
				$i=0;
				while ($_POST["nada"][$i]!="") {
					if ((($_POST["id_item"][$i]!="") || ($_POST["descricao"][$i]!="")) && ($_POST["valor_unitario"][$i]!="") && ($_POST["valor_total"][$i]!="") ) {
						$result2= mysql_query("insert into fi_notas_itens
														(id_empresa, id_nota, id_item, descricao, valor_unitario, qtde, valor_total, destinacao, id_centro_custo, id_centro_custo_tipo, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."',
														'". $_POST["id_nota"] ."', '". $_POST["id_item"][$i] ."',
														'". $_POST["descricao"][$i] ."', '". formata_valor($_POST["valor_unitario"][$i]) ."',
														'". formata_valor($_POST["qtde"][$i]) ."', '". formata_valor($_POST["valor_total"][$i]) ."',
														'". $_POST["destinacao"][$i] ."', '". $_POST["id_centro_custo"][$i] ."', '". $_POST["id_centro_custo_tipo"][$i] ."',
														'". $_SESSION["id_usuario"] ."'
														)") or die("2: ". mysql_error());
						if (!$result2) $var++;
						$id_nota_item= mysql_insert_id();
						
						//se id_item tiver valor e está inserindo no estoque
						if ($_POST["destinacao"][$i]==1) {
							//echo "oiiiiiiiiiii = $i <br />";
							$result1= mysql_query("insert into fi_estoque_mov
													(id_empresa, id_item, tipo_trans, qtde,
													 data_trans, hora_trans, observacoes, valor_unitario, modo, id_centro_custo_tipo, id_nota, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"][$i] ."', 'e',
													'". formata_valor($_POST["qtde"][$i]) ."', '". date("Ymd") ."', '". date("His") ."',
													'". strip_tags($_POST["observacoes"]) ."', '". formata_valor($_POST["valor_unitario"][$i]) ."',
													'2', '". $_POST["id_centro_custo_tipo"][$i] ."', '". $_POST["id_nota"] ."', '". $_SESSION["id_usuario"] ."'
													)") or die("3: ". mysql_error());
					
							if (!$result1) $var++;
							
							$result2= mysql_query("select id from fi_estoque
													where id_empresa= '". $_SESSION["id_empresa"] ."'
													and   id_item = '". $_POST["id_item"][$i] ."'
													") or die("4: ". mysql_error());
							
							if (mysql_num_rows($result2)==0)
								$result3= mysql_query("insert into fi_estoque 
														(id_empresa, id_item, qtde_atual)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"][$i] ."', '". formata_valor($_POST["qtde"][$i]) ."' ) ") or die("5: ". mysql_error());
							else
								$result3= mysql_query("update fi_estoque set
														qtde_atual = qtde_atual + '". formata_valor($_POST["qtde"][$i]) ."'
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_item= '". $_POST["id_item"][$i] ."'
														") or die("6: ". mysql_error());
							if (!$result3) $var++;
							
							$result5_pre= mysql_query("select * from fi_estoque_iv
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_item = '". $_POST["id_item"][$i] ."'
														and   valor_unitario = '". formata_valor($_POST["valor_unitario"][$i]) ."'
														");
							
							$linhas5_pre= mysql_num_rows($result5_pre);
							
							//se nao tem ainda nenhum lançamento deste item, nesta empresa, com este preço unitário
							if ($linhas5_pre==0)
								$result5= mysql_query("insert into fi_estoque_iv
														(id_empresa, id_item, qtde, data_trans, hora_trans, valor_unitario, id_nota, id_centro_custo_tipo, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"][$i] ."',
														'". formata_valor($_POST["qtde"][$i]) ."', '". date("Ymd") ."', '". date("His") ."',
														'". formata_valor($_POST["valor_unitario"][$i]) ."', '". $_POST["id_nota"] ."', '". $_POST["id_centro_custo_tipo"][$i] ."',
														'". $_SESSION["id_usuario"] ."'
														) ");
							else {
								$rs5_pre= mysql_fetch_object($result5_pre);
								
								$result5= mysql_query("update fi_estoque_iv
														set qtde = qtde + '". $_POST["qtde"][$i] ."'
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_item = '". $_POST["id_item"][$i] ."'
														and   valor_unitario = ". formata_valor($_POST["valor_unitario"][$i]) ."
														");
							}
							
							if (!$result5) $var++;
						}
						//se está indo para um centro de custo
						elseif ($_POST["destinacao"][$i]==2) {
							$result7= mysql_query("insert into fi_custos
													(id_empresa, id_centro_custo, id_centro_custo_tipo, id_nota, id_nota_item,
													 data, hora, valor, modo, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_centro_custo"][$i] ."', '". $_POST["id_centro_custo_tipo"][$i] ."',
													'". $_POST["id_nota"] ."', '". $id_nota_item ."',
													'". date("Ymd") ."', '". date("His") ."',
													'". formata_valor($_POST["valor_total"][$i]) ."', '2',
													'". $_SESSION["id_usuario"] ."'
													)") or die("7: ". mysql_error());
					
							if (!$result7) $var++;
						}
					}						
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
				
				//echo $var;
				
				$pagina_inclui= "_financeiro/__nota_detalhamento.php";
				$pagina= "financeiro/nota_esquema";
				require_once("index2.php");
				
			}
		}//fim edição
	}
	
	if (isset($_GET["formNotaPagamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_nota"]!="")) {
			inicia_transacao();
			$var=0;
			
			/*
			$result_pre= mysql_query("select * from fi_notas
									 	where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_nota = '". $_POST["id_nota"] ."'
										limit 1
										");
			$rs_pre= mysql_fetch_object($result_pre);
			*/
			
			$i=0;
			while ($_POST["id_parcela"][$i]!="") {
				if (($_POST["data_pagamento"][$i]!="") && ($_POST["valor_pago"][$i]!="") ) {
					/*$result_parcela_pre[$i]= mysql_query("select * from fi_notas_parcelas
														where id_empresa = '". $_SESSION["id_empresa"] ."'
														and   id_nota = '". $_POST["id_nota"] ."'
														and   id_parcela = '". $_POST["id_parcela"][$i] ."'
														limit 1
														");
					$rs_parcela_pre= mysql_fetch_object($result_parcela_pre[$i]);*/
					
					$result1[$i]= mysql_query("insert into fi_notas_parcelas_pagamentos
													(id_parcela, id_empresa, id_nota, data_pagamento, valor_pago, integral)
													values
													('". $_POST["id_parcela"][$i] ."', '". $_SESSION["id_empresa"] ."',
													'". $_POST["id_nota"] ."', '". formata_data($_POST["data_pagamento"][$i]) ."',
													'". formata_valor($_POST["valor_pago"][$i]) ."', '". $_POST["integral"][$i] ."'
													)") or die("2: ". mysql_error());
					if (!$result1[$i]) $var++;
					
					$valor_total_pago= pega_valor_total_pagamento_nota($_POST["id_nota"]);
					$valor_total_nota= pega_valor_total_nota($_POST["id_nota"]);
					
					//se o campo estiver marcado (pagando a parcela inteira)
					//ou
					//se o valor da soma das parcelas é maior que o total da nota
					if (($_POST["integral"][$i]==1) || ($valor_total_pago>=$valor_total_nota)) {
						$result2[$i]= mysql_query("update fi_notas_parcelas
													set   status_parcela = '1'
													where id_parcela = '". $_POST["id_parcela"][$i] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													limit 1
													") or die("2: ". mysql_error());
						if (!$result2[$i]) $var++;
					}
				}						
				$i++;
			}
			
			//seleciona todas as parcelas em aberto, se todas foram pagas, dá baixa na nota como acertada (paga ou recebida)
			$result_parcela_pos= mysql_query("select * from fi_notas_parcelas
													where id_empresa = '". $_SESSION["id_empresa"] ."'
													and   id_nota = '". $_POST["id_nota"] ."'
													and   status_parcela = '0'
													limit 1
													");
			$linhas_parcela_pos= mysql_num_rows($result_parcela_pos);
			
			if ($linhas_parcela_pos==0) {
				$result_nota= mysql_query("update fi_notas
										  	set   status_nota = '1'
											where id_nota = '". $_POST["id_nota"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											limit 1
											");
			}
			
			finaliza_transacao($var);
			$msg= $var;
			
			$pagina_inclui= "_financeiro/__nota_pagamento.php";
			$pagina= "financeiro/nota_esquema";
			require_once("index2.php");
		}
	}
	
	if (isset($_GET["formNota"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_cedente"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result_pre= mysql_query("select * from fi_notas
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_cedente = '". $_POST["id_cedente"] ."'
											and   num_nota = '". $_POST["num_nota"] ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if ($linhas_pre==0) {
					$result1= mysql_query("insert into fi_notas (id_empresa, tipo_nota, status_nota, id_cedente, num_nota,
																 data_emissao, data_vencimento, valor_total,
																 id_usuario
																 ) values
											('". $_SESSION["id_empresa"] ."', '". $_POST["tipo_nota"] ."', '0',
											 '". $_POST["id_cedente"] ."', '". $_POST["num_nota"] ."',
											 '". formata_data($_POST["data_emissao"]) ."', '". formata_data($_POST["data_vencimento"]) ."',
											 '". formata_valor($_POST["valor_total"]) ."',
											 '". $_SESSION["id_usuario"] ."'
											) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$id_nota= mysql_insert_id();
					
					$result2= mysql_query("insert into fi_notas_parcelas (id_empresa, id_nota, data_vencimento,
																		  valor, status_parcela) values
											('". $_SESSION["id_empresa"] ."', '". $id_nota ."',
											 '". formata_data($_POST["data_vencimento"]) ."',
											 '". formata_valor($_POST["valor_total"]) ."', '0'
											) ") or die(mysql_error());
					if (!$result2) $var++;
					$id_parcela= mysql_insert_id();
					
					if ($_POST["pagar"]==1) { 
	
						$result1_pagamento= mysql_query("insert into fi_notas_parcelas_pagamentos
														(id_parcela, id_empresa, id_nota, data_pagamento, valor_pago, integral)
														values
														('". $id_parcela ."', '". $_SESSION["id_empresa"] ."',
														'". $id_nota ."', '". formata_data($_POST["data_vencimento"]) ."',
														'". formata_valor($_POST["valor_total"]) ."', '1'
														)") or die("2: ". mysql_error());
						if (!$result1_pagamento) $var++;
						
						$result_pagamento_nota= mysql_query("update fi_notas
																set   status_nota = '1'
																where id_nota = '". $id_nota ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																limit 1
																") or die("2.5: ". mysql_error());
						if (!$result_pagamento_nota) $var++;
						
						$result_pagamento_parcela= mysql_query("update fi_notas_parcelas
																set   status_parcela = '1'
																where id_parcela = '". $id_parcela ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																limit 1
																") or die("3: ". mysql_error());
						if (!$result_pagamento_parcela) $var++;
					
					}
					
					$pagina_redirecionar= "financeiro/nota_esquema";
					$pagina_inclui="_financeiro/__nota_detalhamento.php";
				}
				else {
					$var++;
					$acao='i';
					
					$msg_str= "Nota já cadastrada!";
					
					$pagina_redirecionar= "financeiro/nota";
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= $pagina_redirecionar;
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$result1= mysql_query("update fi_notas set
										id_cedente= '". $_POST["id_cedente"] ."',
										num_nota= '". $_POST["num_nota"] ."',
										data_emissao= '". formata_data($_POST["data_emissao"]) ."',
										data_vencimento= '". formata_data($_POST["data_vencimento"]) ."',
										valor_total= '". formata_valor($_POST["valor_total"]) ."'
										where id_nota = '". $_POST["id_nota"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/nota_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formClienteTipo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["cliente_tipo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into fi_clientes_tipos (id_empresa, cliente_tipo, status_cliente_tipo, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["cliente_tipo"]) ."',
											'1', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=financeiro/cliente_tipo_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select cliente_tipo from fi_clientes_tipos
											where cliente_tipo = '". strtoupper($_POST["cliente_tipo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_cliente_tipo <> '". $_POST["id_cliente_tipo"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update fi_clientes_tipos set
											cliente_tipo= '". strtoupper($_POST["cliente_tipo"]) ."'
											where id_cliente_tipo = '". $_POST["id_cliente_tipo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=financeiro/cliente_tipo_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formDeposito"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["deposito"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into fi_depositos (id_empresa, deposito, status_deposito, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["deposito"]) ."', '1', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/deposito_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select deposito from fi_depositos
											where deposito = '". strtoupper($_POST["deposito"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_deposito <> '". $_POST["id_deposito"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update fi_depositos set
											deposito= '". strtoupper($_POST["deposito"]) ."'
											where id_deposito = '". $_POST["id_deposito"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "financeiro/deposito_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formCentroCusto"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["centro_custo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
				
				$result_pre= mysql_query("select centro_custo from fi_centro_custos
											where centro_custo = '". strtoupper($_POST["centro_custo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into fi_centro_custos (id_empresa, centro_custo, status_centro_custo) values
											('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["centro_custo"]) ."', '1' ) ") or die(mysql_error());
					if (!$result1) $var++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/centro_custo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select centro_custo from fi_centro_custos
											where centro_custo = '". strtoupper($_POST["centro_custo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_centro_custo <> '". $_POST["id_centro_custo"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update fi_centro_custos set
											centro_custo= '". strtoupper($_POST["centro_custo"]) ."'
											where id_centro_custo = '". $_POST["id_centro_custo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "financeiro/centro_custo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formTipoCentroCusto"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["centro_custo_tipo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result_pre= mysql_query("select * from fi_centro_custos_tipos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   centro_custo_tipo = '". $_POST["centro_custo_tipo"] ."' ") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into fi_centro_custos_tipos (id_empresa, centro_custo_tipo) values
											('". $_SESSION["id_empresa"] ."',
											'". strtoupper($_POST["centro_custo_tipo"]) ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
					
					$id_centro_custo_tipo= mysql_insert_id();
					
					$i=0;
					while ($_POST["id_centro_custo"][$i]!="") {
						$result2[$i]= mysql_query("insert into fi_cc_ct
												(id_empresa, id_centro_custo, id_centro_custo_tipo) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_centro_custo"][$i]  ."',
												'". $id_centro_custo_tipo ."')
												") or die(mysql_error());
						if (!$result2[$i]) $var++;
						$i++;
					}
				} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "financeiro/centro_custo_tipo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();
				
				$result_pre= mysql_query("select * from fi_centro_custos_tipos
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   centro_custo_tipo = '". $_POST["centro_custo_tipo"] ."'
											and   id_centro_custo_tipo <> '". $_POST["id_centro_custo_tipo"] ."'
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					
					$result1= mysql_query("update fi_centro_custos_tipos set
											centro_custo_tipo= '". strtoupper($_POST["centro_custo_tipo"]) ."'
											where id_centro_custo_tipo = '". $_POST["id_centro_custo_tipo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					
					$result_limpa= mysql_query("delete from fi_cc_ct
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_centro_custo_tipo = '". $_POST["id_centro_custo_tipo"] ."'
												") or die("1: ". mysql_error());
					if (!$result_limpa) $var++;
					
					$i=0;
					while ($_POST["id_centro_custo"][$i]!="") {
						$result2[$i]= mysql_query("insert into fi_cc_ct
												(id_empresa, id_centro_custo, id_centro_custo_tipo) values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_centro_custo"][$i]  ."', '". $_POST["id_centro_custo_tipo"] ."')
												") or die(mysql_error());
						if (!$result2[$i]) $var++;
						$i++;
					}
					
					if (!$result1) $var++;
					
				} else $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "financeiro/centro_custo_tipo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
}//fim pode i

// ############################################### RH ###############################################

if (pode("rhvw", $_SESSION["permissao"])) {
	
	// ------------------ buscas
	
	if (isset($_GET["formEspelhoBuscar"])) {
		$pagina= "rh/espelho";
		require_once("index2.php");
	}
	
	if (isset($_GET["formBancoHorasBuscar"])) {
		$pagina= "rh/banco";
		require_once("index2.php");
	}
	
	if (isset($_GET["formAcompanhamentoBuscar"])) {
		$pagina= "rh/acompanhamento";
		require_once("index2.php");
	}
	
	if (isset($_GET["formEscalaBuscar"])) {
		$pagina= "rh/escala";
		require_once("index2.php");
	}
	
	if (isset($_GET["formDescontoBuscar"])) {
		$pagina= "rh/desconto";
		require_once("index2.php");
	}
	
	if (isset($_GET["formIdadeBuscar"])) {
		$pagina= "rh/idade_relatorio";
		require_once("index2.php");
	}
	
	if (isset($_GET["formAfastamentoBuscar"])) {
		$pagina= "rh/afastamento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formHistoricoBuscar"])) {
		$pagina= "rh/historico_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formEscalaTrocaBuscar"])) {
		$pagina= "rh/escala_troca_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formBancoHoras"])) {
		if (($_POST["id_funcionario"]!="") && ($_POST["he"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				inicia_transacao();
				
				$result_pre= mysql_query("select * from rh_ponto_banco_atual
											where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_funcionario = '". $_POST["id_funcionario"] ."'
											") or die(mysql_error());
				if (!$result_pre) $var++;
				
				//débito
				if ($_POST["operacao"]==0) {
					$sinal="-";
					$operacao_debito= $_POST["operacao_debito"];
				}
				else {
					$sinal="+";
					//anulando a variável
					$operacao_debito= 2;
				}
				
				if (mysql_num_rows($result_pre)==0) {
					$result1= mysql_query("insert into rh_ponto_banco_atual (id_empresa, id_funcionario, he) values 
											( '". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $sinal . formata_hora($_POST["he"]) ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
				}
				else {
					$result1= mysql_query("update rh_ponto_banco_atual set
											he = he ". $sinal ." '". formata_hora($_POST["he"]) ."'
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
				}
				
				$result2= mysql_query("insert into rh_ponto_banco (id_empresa, id_funcionario,
																   data_he, he, operacao, operacao_debito, id_usuario, obs) values 
										( '". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". formata_data_hifen($_POST["data_he"]) ."',
										'". formata_hora($_POST["he"]) ."',
										'". $_POST["operacao"] ."', '". $operacao_debito ."', '". $_SESSION["id_usuario"] ."', '". $_POST["obs"] ."'
										) ") or die(mysql_error());
				if (!$result2) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				if ($msg==0) {
					//echo "Operação realizada com sucesso!";
					if ($_POST["origem"]=='b')
						echo "<script language=\"javascript\">alteraBancoHorasFuncionario('". $_POST["id_funcionario"] ."', '". $_POST["data1f_banco"] ."', '". $_POST["data2f_banco"] ."');</script>";
					else
						echo "<script language=\"javascript\">alteraEspelhoFuncionario('". $_POST["id_funcionario"] ."', '". $_POST["data1f_banco"] ."', '". $_POST["data2f_banco"] ."');</script>";
				}
				else echo "Não foi possível cadastrar, tente novamente!";
			}
				
		}//fim teste variáveis
	}
	
	if (isset($_GET["formAfastamento"])) {
		if (($_POST["id_funcionario"]!="") && ($_POST["data_emissao"]!="") ) {
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				inicia_transacao();
				
				if ($_POST["tipo_afastamento"]=="d") $data_inicial2= formata_data_hifen($_POST["data_emissao"]);
				else $data_inicial2= formata_data_hifen($_POST["data_inicial_abono"]);
				
				$result1= mysql_query("insert into rh_afastamentos (id_empresa, tipo_afastamento, id_funcionario, data_emissao,
																 num_cid10, qtde_dias, nome_medico, crm,
																 num_requerimento, id_motivo, obs, data_inicial, modo_afastamento,
																 data_inicial_aquisitivo, data_final_aquisitivo
																 ) values 
									( '". $_SESSION["id_empresa"] ."', '". $_POST["tipo_afastamento"] ."', '". $_POST["id_funcionario"] ."', '". formata_data_hifen($_POST["data_emissao"]) ."', '". $_POST["cid10"] ."',
									'". $_POST["qtde_dias"] ."', '". strtoupper($_POST["nome_medico"]) ."', '". strtoupper($_POST["crm"]) ."',
									'". $_POST["num_requerimento"] ."', '". $_POST["id_motivo"] ."', '". $_POST["obs"] ."', '". $data_inicial2 ."', '". $_POST["modo_afastamento"] ."',
									'". formata_data_hifen($_POST["data_inicial_aquisitivo"]) ."', '". formata_data_hifen($_POST["data_final_aquisitivo"]) ."'
									) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$id_afastamento= mysql_insert_id();
				$data_inicial_abono= formata_data($_POST["data_inicial_abono"]);
				
				for ($i=0; $i<$_POST["qtde_dias"]; $i++) {
					$data_abono= date("Ymd", mktime(0, 0, 0, pega_mes($data_inicial_abono), pega_dia($data_inicial_abono)+$i, pega_ano($data_inicial_abono)));
					
					$result2= mysql_query("insert into rh_afastamentos_dias (tipo_afastamento, id_afastamento, id_funcionario, data) values
												('". $_POST["tipo_afastamento"] ."', '$id_afastamento', '". $_POST["id_funcionario"] ."', '$data_abono')");
					if (!$result2) $var++;
					
					//se estiver lançando perícia
					if ($_POST["tipo_afastamento"]=="p") {
						ajusta_dados_rh($_SESSION["id_empresa"], $_POST["id_funcionario"], $data_abono, $ht_funcao, $horas_diurnas, $horas_noturnas, $falta_dia, $total_faltas_diurnas_aqui, $total_faltas_noturnas_aqui,
												$he_funcao, $saldo_extras_diurnas, $saldo_extras_noturnas, $total_he_normais_60_dia[0], $total_he_normais_100_dia[0], $total_he_normais_60_dia[1], $total_he_normais_100_dia[1],
												$falta_justificada, $falta_nao_justificada, $suspensao, $_SESSION["id_usuario"]);
					}
				}
				
				finaliza_transacao($var);
				
				$msg= $var;
				$tipo_afastamento= $_POST["tipo_afastamento"];
				$pagina= "rh/afastamento_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				
				if ($_POST["tipo_afastamento"]=="d") $data_inicial2= formata_data_hifen($_POST["data_emissao"]);
				else $data_inicial2= formata_data_hifen($_POST["data_inicial_abono"]);
				
				$result_pre= mysql_query("select * from rh_afastamentos
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   id_afastamento = '". $_POST["id_afastamento"] ."'
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==1) {
					inicia_transacao();
					
					$result1= mysql_query("update rh_afastamentos set
											data_emissao= '". formata_data($_POST["data_emissao"]) ."',
											num_cid10= '". $_POST["cid10"] ."',
											qtde_dias= '". $_POST["qtde_dias"] ."',
											nome_medico= '". strtoupper($_POST["nome_medico"]) ."',
											crm= '". strtoupper($_POST["crm"]) ."',
											num_requerimento= '". $_POST["num_requerimento"] ."',
											obs= '". $_POST["obs"] ."',
											data_inicial= '". $data_inicial2 ."',
											id_motivo= '". $_POST["id_motivo"] ."',
											modo_afastamento = '". $_POST["modo_afastamento"] ."'
											where id_afastamento = '". $_POST["id_afastamento"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$data_inicial_abono= formata_data($_POST["data_inicial_abono"]);
					$result2= mysql_query("delete from rh_afastamentos_dias
												where tipo_afastamento = '". $_POST["tipo_afastamento"] ."'
												and id_afastamento = '". $_POST["id_afastamento"] ."'
												and id_funcionario = '". $_POST["id_funcionario"] ."'
												");
												
					for ($i=0; $i<$_POST["qtde_dias"]; $i++) {
						$data_abono= date("Ymd", mktime(0, 0, 0, pega_mes($data_inicial_abono), pega_dia($data_inicial_abono)+$i, pega_ano($data_inicial_abono)));
						
						$result3= mysql_query("insert into rh_afastamentos_dias (tipo_afastamento, id_afastamento, id_funcionario, data) values
													('". $_POST["tipo_afastamento"] ."', '". $_POST["id_afastamento"] ."', '". $_POST["id_funcionario"] ."', '$data_abono')");
						if (!$result3) $var++;
					}
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$tipo_afastamento= $_POST["tipo_afastamento"];
				$pagina= "rh/afastamento_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formCartao"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["tipo_cartao"]!="") && ($_POST["numero_cartao"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				$var=0;
				$result_pre= mysql_query("select * from rh_cartoes
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   tipo_cartao = '". $_POST["tipo_cartao"] ."' ") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("insert into rh_cartoes (id_funcionario, numero_cartao, tipo_cartao, id_empresa) values
											('". $_POST["id_funcionario"] ."', '". $_POST["numero_cartao"] ."',
												'". $_POST["tipo_cartao"] ."', '". $_SESSION["id_empresa"] ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;	
				$pagina= "rh/cartao_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select * from rh_cartoes
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   tipo_cartao = '". $_POST["tipo_cartao"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_cartao <> '". $_POST["id_cartao"] ."'
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
										
					$result1= mysql_query("update rh_cartoes set
											tipo_cartao= '". $_POST["tipo_cartao"] ."',
											numero_cartao= '". $_POST["numero_cartao"] ."'
											where id_cartao = '". $_POST["id_cartao"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "rh/cartao_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formHorario"])) {
		if (($_POST["id_funcionario"]!="") && ($_POST["data_batida"]!="") && ($_POST["vale_dia"]!="") && ($_POST["hora"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {
				if ($_POST["tipo"]!="") {
					$var=0;
					$permissao_ponto= false;
					
					$result_pre= mysql_query("select * from rh_ponto
												where id_funcionario = '". $_POST["id_funcionario"] ."'
												and   vale_dia = '". formata_data($_POST["vale_dia"]) ."'
												order by data_batida desc, hora desc
												") or die(mysql_error());
					$rs_pre= mysql_fetch_object($result_pre);
					
					inicia_transacao();
					$total_operacoes= mysql_num_rows($result_pre);
					
					//se não entrou ou se entrou e já saiu (pares) e se está vindo uma operação de entrada
					//ou
					//se falta sair e está vindo uma saída
					if ( (($total_operacoes%2==0) && ($_POST["tipo"]=='1')) || (($total_operacoes%2==1) && ($_POST["tipo"]=='0')) ) {
						if ($total_operacoes>0) {
							$data_ultima= explode('-', $rs_pre->data_batida);
							$hora_ultima= explode(':', $rs_pre->hora);
							$data_ultima_mk= mktime($hora_ultima[0], $hora_ultima[1], $hora_ultima[2], $data_ultima[1], $data_ultima[2], $data_ultima[0]);
							
							$data_agora= explode('-', formata_data_hifen($_POST["data_batida"]));
							$hora_agora= explode(':', $_POST["hora"]);
							$data_agora_mk= mktime($hora_agora[0], $hora_agora[1], $hora_agora[2], $data_agora[1], $data_agora[2], $data_agora[0]);
							
							//if ($data_agora_mk>$data_ultima_mk) $permissao_ponto= true;
							//else $msg= "Data informada é menor que a última operação do funcionário!";
							
							//echo $data_agora_mk ." - ". $data_ultima_mk;
							
							$permissao_ponto= true;
						}
						else
							$permissao_ponto= true;
					}
					else
						$msg= "Operação incorreta, verifique se é entrada ou saída!";
										
					if ($permissao_ponto) {
						$result1= mysql_query("insert into rh_ponto (id_funcionario, data_batida, hora, data_hora_batida, tipo, vale_dia, id_usuario) values
												('". $_POST["id_funcionario"] ."', '". formata_data($_POST["data_batida"]) ."',
													'". $_POST["hora"] ."', '". formata_data_hifen($_POST["data_batida"]) ." ". $_POST["hora"] ."', '". $_POST["tipo"] ."', '". formata_data($_POST["vale_dia"]) ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
						if (!$result1) $var++;
					} else $var++;
					
					finaliza_transacao($var);
					
					if ($msg=="") $msg= $var;
					$pagina= "rh/espelho";
					require_once("index2.php");
					
				}//fim tipo
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select * from rh_funcionarios, rh_ponto
											where rh_ponto.id_funcionario = rh_funcionarios.id_funcionario
											and   rh_funcionarios.id_funcionario = '". $_POST["id_funcionario"] ."'
											and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   rh_ponto.id_horario = '". $_POST["id_horario"] ."'
											") or die(mysql_error());
				
				if (mysql_num_rows($result_pre)==1) {
					inicia_transacao();
					
					$rs_pre= mysql_fetch_object($result_pre);
					/*
					$result1= mysql_query("insert into rh_ponto_alteracoes (tipo_alteracao, id_motivo, id_horario, id_funcionario, data_batida, hora, tipo, id_supervisor, hl, vale_dia, id_usuario, turnante)
											values ('e', '". $_POST["id_motivo"] ."', '". $rs_pre->id_horario ."', '". $rs_pre->id_funcionario .". ',
														'". $rs_pre->data_batida ."',
														'". $rs_pre->hora ."', '". $rs_pre->tipo ."',
														'". $rs_pre->id_supervisor ."', '". $rs_pre->hl ."',
														'". $rs_pre->vale_dia ."', '". $rs_pre->id_usuario ."', '". $rs_pre->turnante ."'
														 ) ") or die(mysql_error());
					if (!$result1) $var++;
					*/
					
					$result1= mysql_query("update rh_ponto set
											data_batida= '". formata_data($_POST["data_batida"]) ."',
											vale_dia= '". formata_data($_POST["vale_dia"]) ."',
											data_hora_batida= '". formata_data_hifen($_POST["data_batida"]) ." ". $_POST["hora"] ."',
											hora= '". $_POST["hora"] ."'
											where id_horario = '". $_POST["id_horario"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "rh/espelho";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formHorarioExcluir"])) {
		$var=0;

		if (($_POST["id_horario"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["data1"]!="") && ($_POST["data2"]!="")) {
			inicia_transacao();
			
			$result_pre= mysql_query("select * from rh_funcionarios, rh_ponto
										where rh_ponto.id_funcionario = rh_funcionarios.id_funcionario
										and   rh_funcionarios.id_funcionario = '". $_POST["id_funcionario"] ."'
										and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   rh_ponto.id_horario = '". $_POST["id_horario"] ."'
										") or die(mysql_error());
			if (!$result_pre) $var++;
			
			if (mysql_num_rows($result_pre)==1) {
				$rs_pre= mysql_fetch_object($result_pre);
				/*
				$result1= mysql_query("insert into rh_ponto_alteracoes (tipo_alteracao, id_motivo, id_horario, id_funcionario, data_batida, hora, tipo, id_supervisor, hl, vale_dia, id_usuario, turnante)
										values ('x', '". $_POST["id_motivo"] ."', '". $rs_pre->id_horario ."', '". $rs_pre->id_funcionario .". ',
													'". $rs_pre->data_batida ."',
													'". $rs_pre->hora ."', '". $rs_pre->tipo ."',
													'". $rs_pre->id_supervisor ."', '". $rs_pre->hl ."',
													'". $rs_pre->vale_dia ."', '". $rs_pre->id_usuario ."', '". $rs_pre->turnante ."'
													 ) ") or die(mysql_error());
				if (!$result1) $var++;
				*/
				
				$result2= mysql_query("delete from rh_ponto
										where id_horario= '". $_POST["id_horario"] ."'
										and   id_funcionario= '". $_POST["id_funcionario"] ."'
										") or die(mysql_error());
				if (!$result2) $var++;
			} else $var++;
			
			finaliza_transacao($var);
			$msg= $var;
				
			$pagina= "rh/espelho";
			require_once("index2.php");
			//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
		}
	}//form
	
	if (isset($_GET["formCarreiraChecklist"])) {
		$var= 0;
		inicia_transacao();
		
		$result_del= mysql_query("delete from rh_carreiras_checklist
										where id_acao_carreira = '". $_POST["id_acao_carreira"] ."'
										and   id_funcionario = '". $_POST["id_funcionario"] ."'
										") or die(mysql_error());
		if (!$result_del) $var++;
		
		$tudo= 1;
		$i=1;
		while ($_POST["id_item"][$i]!="") {
			if ($_POST["checado"][$i]!="") {
				$result_insere[$i]= mysql_query("insert into rh_carreiras_checklist
												(id_empresa, id_acao_carreira, id_funcionario, id_item, id_usuario)
												values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_acao_carreira"] ."',
												'". $_POST["id_funcionario"] ."', '". $_POST["id_item"][$i] ."',
												'". $_SESSION["id_usuario"] ."'
												)
												") or die(mysql_error());
				if (!$result_insere[$i]) $var++;
			} else $tudo= 0;
		
			$i++;
		}
		
		if ($tudo) {
			//admissão
			if ($_POST["id_acao_carreira"]==1) $status_funcionario= 1;
			//demissão
			else $status_funcionario= 0;
			
			$result_atualiza= mysql_query("update rh_funcionarios
										  	set status_funcionario = '". $status_funcionario ."'
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											");
		}
		else {
			//admissão
			if ($_POST["id_acao_carreira"]==1) $status_funcionario= -1;
			//demissão
			else $status_funcionario= -1;
			
			$result_atualiza= mysql_query("update rh_funcionarios
										  	set status_funcionario = '". $status_funcionario ."'
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											");
		}
		
		finaliza_transacao($var);
		$msg= $var;
			
		$pagina= "rh/carreira_checklist";
		require_once("index2.php");
		
	}
	
	if (isset($_GET["formCarreira"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["data"]!="") && ($_POST["id_acao_carreira"]!="")) {
				inicia_transacao();
				
				$result_atual= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   atual = '1'
											");
				$rs_atual= mysql_fetch_object($result_atual);
				
				//demissao
				if ($_POST["id_acao_carreira"]==2) {
					$id_departamento_aqui= $rs_atual->id_departamento;
					$id_cargo_aqui= $rs_atual->id_cargo;
					$id_turno_aqui= $rs_atual->id_turno;
					$id_intervalo_aqui= $rs_atual->id_intervalo;
					$turnante_aqui= $rs_atual->turnante;
					$insalubridade_aqui= $rs_atual->insalubridade;
				}
				//restante
				else {
					if (($_POST["id_departamento"]=="") || ($_POST["id_turno"]=="") || ($_POST["id_cargo"]==""))
						die("Preencha todos os campos requeridos!");
						
					$id_departamento_aqui= $_POST["id_departamento"];
					$id_cargo_aqui= $_POST["id_cargo"];
					$id_turno_aqui= $_POST["id_turno"];
					$id_intervalo_aqui= $_POST["id_intervalo"];
					$turnante_aqui= $_POST["turnante"];
					$insalubridade_aqui= $_POST["insalubridade"];
				}
				
				$result_pre1= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											order by data desc limit 1
											");
				if (!$result_pre1) $var++;
				$rs_pre1= mysql_fetch_object($result_pre1);
				
				$ultima_data= str_replace('-', '', $rs_pre1->data);
				$data_atual= formata_data($_POST["data"]);
				
				//se estiver inserindo algo numa data menor que a carreira atual
				if ($data_atual<$ultima_data)
					$atual= 0;
				else {
					$atual= 1;
					$result_pre2= mysql_query("update rh_carreiras set atual = NULL
												where id_funcionario = '". $_POST["id_funcionario"] ."'
												");
					if (!$result_pre2) $var++;
				}
				
				
				
				$result1= mysql_query("insert into rh_carreiras (id_empresa, id_funcionario, data,
																 id_acao_carreira, id_detalhe_carreira, id_cargo, id_departamento, id_turno,
																 id_intervalo, atual, turnante, insalubridade, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."',
												'". formata_data_hifen($_POST["data"]) .". ', '". $_POST["id_acao_carreira"] ."', '". $_POST["id_detalhe_carreira"] ."',
												'". $id_cargo_aqui ."', '". $id_departamento_aqui ."',
												'". $id_turno_aqui ."', '". $id_intervalo_aqui ."',
												'$atual', '". $turnante_aqui ."', '". $insalubridade_aqui ."',
												'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				//se está sendo demitido
				if ($_POST["id_acao_carreira"]=="2") {
					//deleta cartão
					$result_cartoes= mysql_query("delete from rh_cartoes where id_funcionario = '". $_POST["id_funcionario"] ."' limit 1 ");
					if (!$result_cartoes) $var++;
					
					//fazer o checklist para inativar
					$novo_status= -1;
				}
				else $novo_status= 1;
				
				$result2= mysql_query("update rh_funcionarios set status_funcionario = '$novo_status'
										where id_funcionario= '". $_POST["id_funcionario"] ."'
										") or die(mysql_error());
				if (!$result2) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/carreira";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados! Preencha todos os campos.";
		}
		
		if ($_GET["acao"]=="e") {
			
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento"]!="") && ($_POST["id_cargo"]!="")) {
				inicia_transacao();
				
				$result_pre1= mysql_query("select * from rh_carreiras
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   atual = '1'
											and   id_carreira <> '". $_POST["id_carreira"] ."'
											order by data desc limit 1
											");
				if (!$result_pre1) $var++;
				
				if (mysql_num_rows($result_pre1)==1) {
					$rs_pre1= mysql_fetch_object($result_pre1);
					
					$ultima_data= str_replace('-', '', $rs_pre1->data);
					$data_atual= formata_data($_POST["data"]);
					
					//se estiver editando algo numa data menor que a carreira atual
					if ($data_atual<$ultima_data)
						$atual= 0;
					else {
						$atual= 1;
						$result_pre2= mysql_query("update rh_carreiras set atual = NULL
													where id_funcionario = '". $_POST["id_funcionario"] ."'
													");
						if (!$result_pre2) $var++;
					}
					
					$str_atual= " atual= '". $atual ."', ";
				}
				
				
				$result1= mysql_query("update rh_carreiras set data= '". formata_data($_POST["data"]) ."',
																id_acao_carreira= '". $_POST["id_acao_carreira"] ."',
																id_detalhe_carreira= '". $_POST["id_detalhe_carreira"] ."',
																id_cargo= '". $_POST["id_cargo"] ."',
																id_departamento= '". $_POST["id_departamento"] ."',
																id_turno= '". $_POST["id_turno"] ."',
																id_intervalo= '". $_POST["id_intervalo"] ."',
																". $str_atual ."
																turnante= '". $_POST["turnante"] ."',
																insalubridade= '". $_POST["insalubridade"] ."'
																where id_carreira = '". $_POST["id_carreira"] ."'
																and   id_funcionario = '". $_POST["id_funcionario"] ."'
																and   id_empresa = '". $_SESSION["id_empresa"] ."'
																") or die(mysql_error());
				
				if (!$result1) $var++;
				
				//se está sendo demitido
				if ($_POST["id_acao_carreira"]=="2") {
					//deleta cartão
					$result_cartoes= mysql_query("delete from rh_cartoes where id_funcionario = '". $_POST["id_funcionario"] ."' limit 1 ");
					if (!$result_cartoes) $var++;
					
					$novo_status= 0;
				}
				else $novo_status= 1;
				
				$result2= mysql_query("update rh_funcionarios set status_funcionario = '$novo_status'
										where id_funcionario= '". $_POST["id_funcionario"] ."'
										") or die(mysql_error());
				if (!$result2) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/carreira";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formVT"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_linha"]!="")) {
				inicia_transacao();
				
				if ($_POST["trajeto"]==2) {
					$j=2;
					
					$trajeto_valor[1]= 1;
					$trajeto_valor[2]= 0;
				}
				else {
					$j=1;
					
					$trajeto_valor[1]= $_POST["trajeto"];
				}
				
				for ($i=1; $i<=$j; $i++) {
					$result_pre= mysql_query("select * from rh_vt
												where id_funcionario = '". $_POST["id_funcionario"] ."'
												and   id_linha = '". $_POST["id_linha"] ."'
												and   trajeto = '". $trajeto_valor[$i] ."'
												");
					$linhas_pre= mysql_num_rows($result_pre);
					
					if ($linhas_pre==0) {
						$result1= mysql_query("insert into rh_vt (id_empresa, id_funcionario, trajeto, id_linha, id_usuario)
												values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $trajeto_valor[$i] ."',
															'". $_POST["id_linha"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
						if (!$result1) $var++;
					}
					else $var++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/vt";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_linha"]!="") && ($_POST["id_vt"]!="")) {
				inicia_transacao();
				
				$result_pre= mysql_query("select * from rh_vt
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   id_linha = '". $_POST["id_linha"] ."'
											and   trajeto = '". $_POST["trajeto"] ."'
											and   id_vt <> '". $_POST["id_vt"] ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if ($linhas_pre==0) {
					$result1= mysql_query("update rh_vt set trajeto= '". $_POST["trajeto"] ."',
											id_funcionario= '". $_POST["id_funcionario"] ."',
											id_linha= '". $_POST["id_linha"] ."'
											where id_vt = '". $_POST["id_vt"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
				} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/vt";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formVTDesconto"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["qtde"]!="")) {
				inicia_transacao();
				
				$periodo2= explode("/", $_POST["periodo"]);
				
				$result_pre= mysql_query("select * from rh_vt_descontos
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   mes = '". $periodo2[0] ."'
											and   ano = '". $periodo2[1] ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if ($linhas_pre==0) {
					$result1= mysql_query("insert into rh_vt_descontos (id_empresa, id_funcionario, mes, ano, qtde, data_entrega, id_usuario)
											values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $periodo2[0] ."', '". $periodo2[1] ."',
													'". $_POST["qtde"] ."', '". formata_data($_POST["data_entrega"]) ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
					if (!$result1) $var++;
				}
				else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/vt";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_vt_desconto"]!="")) {
				inicia_transacao();
				
				$periodo2= explode("/", $_POST["periodo"]);
				
				$result_pre= mysql_query("select * from rh_vt_descontos
											where id_funcionario = '". $_POST["id_funcionario"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   mes = '". $periodo2[0] ."'
											and   ano = '". $periodo2[1] ."'
											and   id_vt_desconto <> '". $_POST["id_vt_desconto"] ."'
											");
				$linhas_pre= mysql_num_rows($result_pre);
				
				if ($linhas_pre==0) {
					$result1= mysql_query("update rh_vt_descontos set
											id_funcionario= '". $_POST["id_funcionario"] ."',
											mes= '". $periodo2[0] ."',
											ano= '". $periodo2[1] ."',
											data_entrega= '". formata_data($_POST["data_entrega"]) ."',
											qtde= '". $_POST["qtde"] ."'
											where id_vt_desconto = '". $_POST["id_vt_desconto"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
				} else $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/vt";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formHEAutorizacao"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["data_he"]!="")) {
				inicia_transacao();
				
			
				$result1= mysql_query("insert into rh_he_autorizacao (id_empresa, id_funcionario, data_he, hora_he, qtde_horas, data_compensacao, motivo, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". formata_data($_POST["data_he"]) ."', '". $_POST["hora_he"] ."',
												'". $_POST["qtde_horas"] ."', '". formata_data($_POST["data_compensacao"]) ."', '". $_POST["motivo"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/he_autorizacao";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_he_autorizacao"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update rh_he_autorizacao set
										id_funcionario= '". $_POST["id_funcionario"] ."',
										and   data_he = '". formata_data($_POST["data_he"]) ."',
										and   hora_he = '". $_POST["hora_he"] ."',
										and   qtde_horas = '". $_POST["qtde_horas"] ."',
										and   data_compensacao = '". formata_data($_POST["data_compensacao"]) ."',
										and   motivo = '". $_POST["motivo"] ."'
										where id_he_autorizacao = '". $_POST["id_he_autorizacao"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/he_autorizacao";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formSubstituicaoFuncao"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["data_substituicao"]!="")) {
				inicia_transacao();
				
				$id_cargo_atual= pega_id_cargo_atual($_POST["id_funcionario"]);
			
				$result1= mysql_query("insert into rh_substituicao_funcao (id_empresa, id_funcionario, data_substituicao, id_cargo_atual, funcao_substituicao, periodo_substituicao, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."',
										'". formata_data($_POST["data_substituicao"]) ."', '". $id_cargo_atual ."', '". $_POST["funcao_substituicao"] ."', '". $_POST["periodo_substituicao"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/substituicao_funcao";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_substituicao_funcao"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update rh_substituicao_funcao set
										id_funcionario= '". $_POST["id_funcionario"] ."',
										data_substituicao = '". formata_data($_POST["data_substituicao"]) ."',
										funcao_substituicao= '". $_POST["funcao_substituicao"] ."',
										periodo_substituicao= '". $_POST["periodo_substituicao"] ."'
										where id_substituicao_funcao = '". $_POST["id_substituicao_funcao"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/substituicao_funcao";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form
	
	if (isset($_GET["formInsalubridade"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
	
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_departamento"]!="")) {
				inicia_transacao();
			
				$result1= mysql_query("insert into rh_insalubridade (id_empresa, id_funcionario, id_departamento, data_insalubridade,
																	 hora_inicio, hora_fim, id_usuario)
										values ('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."', '". $_POST["id_departamento"] ."',
													'". formata_data($_POST["data_insalubridade"]) ."',
													'". $_POST["hora_inicio"] ."', '". $_POST["hora_fim"] ."',
													'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/insalubridade";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
		
		if ($_GET["acao"]=="e") {
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="") && ($_POST["id_insalubridade"]!="")) {
				inicia_transacao();
				
				$result1= mysql_query("update rh_insalubridade set id_funcionario= '". $_POST["id_funcionario"] ."',
										id_departamento= '". $_POST["id_departamento"] ."',
										data_insalubridade= '". formata_data($_POST["data_insalubridade"]) ."',
										hora_inicio= '". $_POST["hora_inicio"] ."',
										hora_fim= '". $_POST["hora_fim"] ."'
										where id_insalubridade = '". $_POST["id_insalubridade"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$acao="i";
				$pagina= "rh/insalubridade";
				require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&msg=". $msg);
			}
			else echo "Faltam dados!";
		}
	}//form


	if (isset($_GET["formFuncionario"])) {
		if ($_GET["acao"]=="i") {
			$var=0;
			if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento"]!="") && ($_POST["id_turno"]!="") && ($_POST["id_cargo"]!="") && ($_POST["nome"]!="") && ($_POST["passa_cpf"]!="")) {
				inicia_transacao();
			
				$cpf= $_POST["cpf"];
				$auth= gera_auth();
				
				$result1= mysql_query("insert into pessoas (nome_rz, apelido_fantasia, rg_ie, sexo,
															cpf_cnpj, data, contato, tipo,
															auth, id_empresa) values
										(
										'". strtoupper($_POST["nome"]) ."', '". strtoupper($_POST["apelido"]) ."',
										'". $_POST["rg"] ."', '". $_POST["sexo"] ."',
										
										'". $cpf ."', '". formata_data($_POST["data_nasc"]) ."',
										'". strtoupper($_POST["contato"]) ."', 'f',
										
										'". $auth ."', '". $_SESSION["id_empresa"] ."'
										) ") or die("1: ". mysql_error());
				if (!$result1) $var++;
				$id_pessoa= mysql_insert_id();
				
				$result1b= mysql_query("insert into pessoas_tipos (id_pessoa, tipo_pessoa, id_empresa) values
										('". $id_pessoa ."', 'u', '". $_SESSION["id_empresa"] ."') ") or die(mysql_error());
				if (!$result1b) $var++;
				
				$num_func= (pega_num_ultimo_funcionario($_SESSION["id_empresa"])+1);
				
				$result2 = mysql_query("insert into rh_funcionarios (id_pessoa, id_empresa, id_empresa_rel, matr_cont, status_funcionario, num_func,
														org_exp_rg, uf_rg, data_exp_rg, pis,
														data_pis, ctps, serie_ctps, id_uf_ctps,
														data_exp_ctps, cnh, validade_cnh, escolaridade, naturalidade_id_cid,
														estado_civil, nomepai, nomemae, id_banco, agencia, conta, operacao, tamanho_uniforme, numero_calcado)
														values
														(
														'$id_pessoa', '". $_SESSION["id_empresa"] ."',
														'". $_POST["id_empresa_rel"] ."', '". $_POST["matr_cont"] ."', '-1', '$num_func',
														
														'". $_POST["orgrg"] ."', '". $_POST["rg_id_uf"] ."',
														'". formata_data($_POST["data_exp_rg"]) ."', '". $_POST["pis"] ."',
														
														'". formata_data($_POST["data_cad_pis"]) ."', '". $_POST["ctps"] ."',
														'". $_POST["serie_ctps"] ."', '". $_POST["ctps_id_uf"]. "',
														
														'". formata_data($_POST["data_exp_ctps"]) ."', '". $_POST["cnh"] ."',
														'". formata_data($_POST["cnh_validade"]) ."',
														'". $_POST["escolaridade"] ."',
														'". $_POST["id_cidade_naturalidade"] ."',
														'". $_POST["estado_civil"]. "', '". $_POST["nomepai"]. "',
														'". $_POST["nomemae"]. "',
														'". $_POST["id_banco"]. "', '". $_POST["agencia"]. "', '". $_POST["conta"]. "', '". $_POST["operacao"]. "',
														'". $_POST["tamanho_uniforme"]. "', '". $_POST["numero_calcado"]. "'
														) ") or die("2: ". mysql_error());
				if (!$result2) $var++;
				$id_funcionario= mysql_insert_id();
				
				$result3= mysql_query("insert into rh_enderecos (id_pessoa, id_cidade, rua, numero, complemento, bairro, cep, tel_com, tel_res, tel_cel, email, site)
										values
										('$id_pessoa', '". $_POST["id_cidade"] ."', '". strtoupper($_POST["rua"]) ."', '". $_POST["numero"] ."',
										'". strtoupper($_POST["complemento"]) ."', '". strtoupper($_POST["bairro"]) ."', '". $_POST["cep"] ."', '". $_POST["tel_com"] ."',
										'". $_POST["tel_res"] ."', '". $_POST["tel_cel"] ."', '". $_POST["email"] ."', '". $_POST["site"] ."' )") or die("3: ". mysql_error());
										
				if (!$result3) $var++;
				
				$result4= mysql_query("insert into rh_carreiras (id_empresa, id_funcionario, data, id_acao_carreira, id_cargo, id_departamento, id_turno, id_intervalo, atual, turnante)
										values ('". $_SESSION["id_empresa"] ."', '$id_funcionario', '". formata_data_hifen($_POST["data_admissao"]) ."', '1',
													'". $_POST["id_cargo"] ."', '". $_POST["id_departamento"] ."',
													'". $_POST["id_turno"] ."', '". $_POST["id_intervalo"] ."', '1', '". $_POST["turnante"] ."' ) ") or die("4: ". mysql_error());
				if (!$result4) $var++;
				
				$result5= mysql_query("insert into tel_contatos (id_empresa, tipo_contato, nome, email,
															 id_pessoa, id_usuario)
										values (
												'". $_SESSION["id_empresa"] ."', '2',
												'". strtoupper($_POST["nome"]) ."', '". $_POST["email"] ."',
												'$id_pessoa', '". $_SESSION["id_usuario"] ."' ) ") or die("5: ". mysql_error());
				if (!$result5) $var++;
				$id_contato= mysql_insert_id();
				
				//echo $_POST["tel_res"] ."xxxx". $_POST["tel_cel"];
				
				if ($_POST["tel_res"]!="") {
					$result5= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
										values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_res"] ."', '1' ) ") or die("6: ". mysql_error());
					if (!$result5) $var++;
				}
				if ($_POST["tel_cel"]!="") {
					$result6= mysql_query("insert into tel_contatos_telefones (id_empresa, id_contato, telefone, tipo)
										values ('". $_SESSION["id_empresa"] ."', '$id_contato', '". $_POST["tel_cel"] ."', '3' ) ") or die("7: ". mysql_error());
					if (!$result6) $var++;
				}
				
				if ($_FILES["foto"]["name"]!="") {
					$caminho= CAMINHO . "pessoa_". $id_pessoa .".jpg";
					@move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
					//otimiza_foto($caminho, 200);
				}
				
				$i=0;
				while ($_POST["nome_filho"][$i]!="") {
					$result_filho[$i]= mysql_query("insert into rh_funcionarios_filhos
													(id_empresa, id_funcionario, nome_filho, sexo_filho, data_nasc_filho)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_funcionario ."',
													'". $_POST["nome_filho"][$i] ."', '". $_POST["sexo_filho"][$i] ."',
													'". formata_data($_POST["data_nasc_filho"][$i]) ."'
													)
													") or die(mysql_error());
					if (!$result_filho[$i]) $var++;
				
					$i++;
				}
				
				$result6= mysql_query("insert into rh_carreiras_departamentos
										(id_empresa, id_funcionario, id_departamento, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $id_funcionario ."',
										'". $_POST["id_departamento"]  ."', '". $_SESSION["id_usuario"] ."')
										") or die(mysql_error());
				if (!$result6) $var++;
				
				//divulgando no livro
				
				$foto= CAMINHO ."pessoa_". $id_pessoa .".jpg";
		
				if (file_exists($foto))
					$mostra_foto= "<img src=\"includes/phpthumb/phpThumb.php?src=pessoa_". $id_pessoa .".jpg&amp;w=200&amp;zc=1&amp;far=T\" width=\"200\" />";
				
				/*
				$mensagem_aqui= "<div class=\"maior\">
									<strong>Novo colaborador na empresa:</strong><br /><br />
									
									<div class=\"parte40\">
										$mostra_foto
									</div>
									<div class=\"parte60\">
										<strong>Nome:</strong> ". strtoupper($_POST["nome"]) ."<br /><br />
										<strong>Setor:</strong> ". pega_departamento($_POST["id_departamento"]) ."<br /><br />
										<strong>Turno:</strong> ". pega_turno($_POST["id_turno"]) ."<br /><br />
										<strong>Admissão:</strong> ". $_POST["data_admissao"] ."
									</div>
								</div>
								";
				
				$result_num_livro= mysql_query("select * from com_livro
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_motivo <> '34'
												and   id_motivo <> '37'
												and   id_motivo <> '41'
												and   id_motivo <> '42'
												and   DATE_FORMAT(data_livro, '%Y') = '". date("Y") ."'
												order by num_livro desc limit 1
												");
				$rs_num_livro= mysql_fetch_object($result_num_livro);
				$num_livro= $rs_num_livro->num_livro+1;
				
				$result_livro= mysql_query("insert into com_livro
												(id_empresa, num_livro, tipo_de, de, id_outro_departamento, mensagem, 
												 data_livro, hora_livro, tipo_resposta, resposta, resposta_id_livro,
												 id_motivo, resposta_requerida, id_departamento_principal, prioridade_dias,
												 reclamacao_original, reclamacao_original_id_livro, reclamacao_id_cliente
												 ) values
												('". $_SESSION["id_empresa"] ."', '". $num_livro ."', 'f', '0', '1',
												 '". $mensagem_aqui ."', '". date("Ymd") ."', '". date("His") ."',
												 '0', '0', '0',
												 '0', '0',
												 '0', '0',
												 '0', '', '0'
												 ) ") or die(mysql_error());
				$id_livro_inserido= mysql_insert_id();
				
				
				$result_depto= mysql_query("select * from rh_departamentos
										   	where id_empresa = '". $_SESSION["id_empresa"] ."'
											order by departamento asc
											");
				while ($rs_depto= mysql_fetch_object($result_depto))
					$result_permissao= mysql_query("insert into com_livro_permissoes
														 (id_empresa, id_livro, id_departamento)
														 values 
														 ('". $_SESSION["id_empresa"] ."', '". $id_livro_inserido ."',
														 '". $rs_depto->id_departamento ."' )
														 ") or die(mysql_error());
				
				*/
				finaliza_transacao($var);
				$msg= $var;
					
				//$pagina= "rh/funcionario_listar";
				//require_once("index2.php");
				header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $id_funcionario ."&msg=". $msg ."&acao=e");
			}
		}
			
		//editar empresa
		if ($_GET["acao"]=="e") {
			if (($_POST["id_funcionario"]!="") && ($_POST["nome"]!="") && ($_POST["passa_cpf"]!="")) {
				$var=0;
				
				$result_pre= mysql_query("select pessoas.id_pessoa from pessoas, rh_funcionarios
											where rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.id_funcionario <> '". $_POST["id_funcionario"] ."'
											and   pessoas.cpf_cnpj = '". $_POST["cpf"] ."'
											and   pessoas.status_pessoa = '1'
											");
				//echo mysql_num_rows($result_pre);
				
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update pessoas, rh_funcionarios set
											pessoas.nome_rz= '". strtoupper($_POST["nome"]) ."',
											pessoas.apelido_fantasia= '". strtoupper($_POST["apelido"]) ."',
											pessoas.sexo= '". $_POST["sexo"] ."',
											pessoas.rg_ie= '". $_POST["rg"] ."',
											pessoas.cpf_cnpj= '". $_POST["cpf"] ."',
											pessoas.data= '". formata_data($_POST["data_nasc"]) ."'
											where pessoas.id_pessoa = rh_funcionarios.id_pessoa
											and   rh_funcionarios.id_funcionario = '". $_POST["id_funcionario"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$result2= mysql_query("update rh_funcionarios set matr_cont='". $_POST["matr_cont"] ."',
													id_empresa_rel='". $_POST["id_empresa_rel"] ."',
													org_exp_rg='". $_POST["orgrg"] ."',
													uf_rg='". $_POST["rg_id_uf"] ."',
													data_exp_rg='". formata_data($_POST["data_exp_rg"]) ."',
													pis='". $_POST["pis"] ."',
													data_pis='". formata_data($_POST["data_cad_pis"]) ."',
													ctps='". $_POST["ctps"] ."',
													serie_ctps='". $_POST["serie_ctps"] ."',
													id_uf_ctps='". $_POST["ctps_id_uf"] ."',
													data_exp_ctps='". formata_data($_POST["data_exp_ctps"]) ."',
													cnh='". $_POST["cnh"] ."',
													validade_cnh='". formata_data($_POST["cnh_validade"]) ."',
													naturalidade_id_cid='". $_POST["id_cidade_naturalidade"] ."',
													escolaridade='". $_POST["escolaridade"] ."',
													estado_civil='". $_POST["estado_civil"] ."',
													nomepai='". $_POST["nomepai"] ."',
													nomemae='". $_POST["nomemae"] ."',
													num_func='". $_POST["num_func"] ."',
													id_banco='". $_POST["id_banco"] ."',
													agencia='". $_POST["agencia"] ."',
													conta='". $_POST["conta"] ."',
													operacao='". $_POST["operacao"] ."',
													tamanho_uniforme='". $_POST["tamanho_uniforme"] ."',
													numero_calcado='". $_POST["numero_calcado"] ."'
													
													where id_funcionario = '". $_POST["id_funcionario"] ."' ") or die(mysql_error());
					if (!$result2) $var++;
					
					$result3= mysql_query("update pessoas, rh_funcionarios, rh_enderecos set
											rh_enderecos.id_cidade= '". $_POST["id_cidade"] ."',
											rh_enderecos.rua= '". strtoupper($_POST["rua"]) ."',
											rh_enderecos.numero= '". $_POST["numero"] ."',
											rh_enderecos.complemento= '". strtoupper($_POST["complemento"]) ."',
											rh_enderecos.bairro= '". strtoupper($_POST["bairro"]) ."',
											rh_enderecos.cep= '". $_POST["cep"] ."',
											rh_enderecos.tel_res= '". $_POST["tel_res"] ."',
											rh_enderecos.tel_com= '". $_POST["tel_com"] ."',
											rh_enderecos.tel_cel= '". $_POST["tel_cel"] ."',
											rh_enderecos.tel_fax= '". $_POST["tel_fax"] ."',
											rh_enderecos.email= '". $_POST["email"] ."',
											rh_enderecos.site= '". $_POST["site"] ."'
											where rh_enderecos.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.id_pessoa = pessoas.id_pessoa
											and   rh_funcionarios.id_funcionario = '". $_POST["id_funcionario"] ."'
											") or die(mysql_error());
					if (!$result3) $var++;
					
					if ($_FILES["foto"]["name"]!="") {
						$id_pessoa= pega_id_pessoa_do_funcionario($_POST["id_funcionario"]);
						$caminho= CAMINHO . "pessoa_". $id_pessoa .".jpg";
						move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho);
						//otimiza_foto($caminho, 200);
					}
					
					$result_filho_pre= mysql_query("delete from rh_funcionarios_filhos
													where id_funcionario = '". $_POST["id_funcionario"] ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													") or die(mysql_error());
					
					$i=0;
					while ($_POST["nome_filho"][$i]!="") {
						$result_filho[$i]= mysql_query("insert into rh_funcionarios_filhos
														(id_empresa, id_funcionario, nome_filho, sexo_filho, data_nasc_filho)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."',
														'". $_POST["nome_filho"][$i] ."', '". $_POST["sexo_filho"][$i] ."',
														'". formata_data($_POST["data_nasc_filho"][$i]) ."'
														)
														") or die(mysql_error());
						if (!$result_filho[$i]) $var++;
					
						$i++;
					}
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				//$pagina= "rh/funcionario_listar";
				//require_once("index2.php");
				//header("location: ./?pagina=rh/funcionario_listar&msg=". $msg);
				header("location: ./?pagina=rh/funcionario_esquema&id_funcionario=". $_POST["id_funcionario"] ."&acao=e&msg=". $msg);
			}//teste vars
		}//e
	}//form
	
	if (isset($_GET["formMotivo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["motivo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_motivos (id_empresa, tipo_motivo, motivo, status_motivo, qtde_dias) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["tipo_motivo"] ."',
											'". strtoupper($_POST["motivo"]) ."', '1', '". $_POST["qtde_dias"] ."'
											 ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				$tipo_motivo= $_POST["tipo_motivo"];
				$pagina= "rh/motivo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select motivo from rh_motivos
											where motivo = '". strtoupper($_POST["motivo"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   tipo_motivo = '". $_POST["tipo_motivo"] ."'
											and   id_motivo <> '". $_POST["id_motivo"] ."'
											and   status_motivo = '1'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update rh_motivos set
											motivo= '". strtoupper($_POST["motivo"]) ."',
											tipo_motivo= '". $_POST["tipo_motivo"] ."',
											qtde_dias= '". strtoupper($_POST["qtde_dias"]) ."'
											where id_motivo = '". $_POST["id_motivo"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$tipo_motivo= $_POST["tipo_motivo"];
				
				$pagina= "rh/motivo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formDepartamentoPermissao"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("delete from rh_carreiras_departamentos
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_funcionario = '". $_POST["id_funcionario"] ."'
												") or die(mysql_error());
		if (!$result_pre) $var++;
		
		$i=0;
		while ($_POST["id_departamento"][$i]!="") {
			$result1[$i]= mysql_query("insert into rh_carreiras_departamentos
									(id_empresa, id_funcionario, id_departamento, valido, id_usuario) values
									('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."',
									'". $_POST["id_departamento"][$i] ."', '". $_POST["valido"] ."', '". $_SESSION["id_usuario"] ."')
									") or die(mysql_error());
			if (!$result1[$i]) $var++;
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "rh/carreira_departamento_permissao";
		require_once("index2.php");
	}
	
	
	if (isset($_GET["formDepartamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["departamento"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_departamentos (id_empresa, departamento, status_departamento,
																	 alerta_aniversariantes, alerta_aniversariantes_clientes, presente_livro, bate_ponto) values
										('". $_SESSION["id_empresa"] ."', '". strtoupper($_POST["departamento"]) ."', '1',
											'". $_POST["alerta_aniversariantes"] ."', '". $_POST["alerta_aniversariantes_clientes"] ."', '". $_POST["presente_livro"] ."', '". $_POST["bate_ponto"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/departamento_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select departamento from rh_departamentos
											where departamento = '". strtoupper($_POST["departamento"]) ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_departamento <> '". $_POST["id_departamento"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();
					
					$result1= mysql_query("update rh_departamentos set
											departamento= '". strtoupper($_POST["departamento"]) ."',
											bate_ponto= '". $_POST["bate_ponto"] ."',
											presente_livro= '". $_POST["presente_livro"] ."',
											alerta_aniversariantes= '". $_POST["alerta_aniversariantes"] ."',
											alerta_aniversariantes_clientes= '". $_POST["alerta_aniversariantes_clientes"] ."'
											where id_departamento = '". $_POST["id_departamento"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "rh/departamento_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formCargo"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento"]!="") && ($_POST["cargo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_cargos (id_departamento, cargo, val_salario, val_salario_experiencia, descricao, status_cargo) values
										('". $_POST["id_departamento"] ."', '". strtoupper($_POST["cargo"]) ."',
											'". formata_valor($_POST["val_salario"]) ."', '". formata_valor($_POST["val_salario_experiencia"]) ."', '". strtoupper($_POST["descricao"]) ."', '1' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/cargo_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select cargo from rh_cargos
											where cargo = '". strtoupper($_POST["cargo"]) ."'
											and   id_departamento = '". $_POST["id_departamento"] ."'
											and   id_cargo <> '". $_POST["id_cargo"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();

					$result1= mysql_query("update rh_cargos, rh_departamentos set
											rh_cargos.id_departamento= '". $_POST["id_departamento"] ."',
											rh_cargos.cargo= '". strtoupper($_POST["cargo"]) ."',
											rh_cargos.val_salario= '". formata_valor($_POST["val_salario"]) ."',
											rh_cargos.val_salario_experiencia= '". formata_valor($_POST["val_salario_experiencia"]) ."',
											rh_cargos.descricao= '". strtoupper($_POST["descricao"]) ."'
											where rh_cargos.id_cargo = '". $_POST["id_cargo"] ."'
											and   rh_cargos.id_departamento = rh_departamentos.id_departamento
											and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "rh/cargo_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formEscala"])) {
		if ($_SESSION["id_empresa"]!="") {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["id_funcionario"][$i]!="") {
				if ($_POST["data_escala"][$i]!="") {
					$result_pre[$i]= mysql_query("delete from rh_escala
													where id_funcionario = '". $_POST["id_funcionario"][$i] ."'
													and   data_escala = '". $_POST["data_escala"][$i] ."' ") or die(mysql_error());
					
					if ($_POST["trabalha"][$i]!="") {
						$result1[$i]= mysql_query("insert into rh_escala
												(id_funcionario, data_escala, trabalha, id_usuario) values
												('". $_POST["id_funcionario"][$i] ."', '". $_POST["data_escala"][$i]  ."',
												'". $_POST["trabalha"][$i] ."', '". $_SESSION["id_usuario"] ."')
												") or die(mysql_error());
						if (!$result1[$i]) $var++;
					}
				}						
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "rh/escala";
			require_once("index2.php");
		}//fim teste variáveis
	}
	
	if (isset($_GET["formAcompanhamento"])) {
		if ( ($_SESSION["id_funcionario_sessao"]!="") && ($_SESSION["id_empresa"]!="") ) {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["id_acompanhamento"][$i]!="") {
				if ($_POST["data_acompanhamento"][$i]!="") {
					$result_pre[$i]= mysql_query("delete from rh_acompanhamento_atividades
													where id_acompanhamento = '". $_POST["id_acompanhamento"][$i] ."'
													and   data_acompanhamento = '". $_POST["data_acompanhamento"][$i] ."'
													and   periodo = '". $_POST["periodo"][$i] ."'
													") or die(mysql_error());
					
					if ($_POST["valor"][$i]!="") {
						$result1[$i]= mysql_query("insert into rh_acompanhamento_atividades
												(id_empresa, id_funcionario, id_acompanhamento, data_acompanhamento, valor, periodo, id_usuario) values
												('". $_SESSION["id_empresa"] ."', '". $_SESSION["id_funcionario_sessao"] ."', '". $_POST["id_acompanhamento"][$i]  ."', '". $_POST["data_acompanhamento"][$i]  ."',
												'". $_POST["valor"][$i] ."', '". $_POST["periodo"][$i] ."', '". $_SESSION["id_usuario"] ."')
												") or die(mysql_error());
						if (!$result1[$i]) $var++;
					}
				}						
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "rh/acompanhamento";
			require_once("index2.php");
		}//fim teste variáveis
	}
	
	if (isset($_GET["formDesconto"])) {
		if ($_SESSION["id_empresa"]!="") {
				
			$var=0;
			inicia_transacao();
			
			$i=0;
			while ($_POST["id_funcionario"][$i]!="") {
				
				$result_pre[$i]= mysql_query("delete from rh_descontos
												where id_funcionario = '". $_POST["id_funcionario"][$i] ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												and   mes = '". $_POST["mes"] ."'
												and   ano = '". $_POST["ano"] ."'
												and   id_motivo = '". $_POST["id_motivo"][$i] ."'
												") or die(mysql_error());
				
				if (($_POST["valor"][$i]!="") && ($_POST["valor"][$i]!="0") && ($_POST["valor"][$i]!="0,00")) {
					
					$result1[$i]= mysql_query("insert into rh_descontos
												(id_empresa, id_funcionario, mes, ano, id_motivo, valor, id_usuario)
												values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"][$i] ."',
												'". $_POST["mes"] ."', '". $_POST["ano"] ."', '". $_POST["id_motivo"][$i] ."',
												'". formata_valor($_POST["valor"][$i]) ."', '". $_SESSION["id_usuario"] ."'
												)
												") or die(mysql_error());
					if (!$result1[$i]) $var++;
				}
			
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "rh/desconto";
			require_once("index2.php");
		}//fim teste variáveis
	}
	
	if (isset($_GET["formFeriado"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["feriado"]!="") && ($_POST["data_feriado"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_feriados (id_empresa, data_feriado, feriado) values
										('". $_SESSION["id_empresa"] ."', '". formata_data($_POST["data_feriado"]) ."', '". $_POST["feriado"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=rh/feriado_listar&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select feriado from rh_feriados
											where data_feriado = '". formata_data($_POST["data_feriado"]) ."'
											and   id_feriado <> '". $_POST["id_feriado"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();

					$result1= mysql_query("update rh_feriados set
											data_feriado = '". formata_data($_POST["data_feriado"]) ."',
											feriado= '". $_POST["feriado"] ."'
											where id_feriado = '". $_POST["id_feriado"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				header("location: ./?pagina=rh/feriado_listar&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formTreinamento"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["treinamento"]!="") && ($_POST["data_treinamento"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_treinamentos (id_empresa, tipo_treinamento, treinamento, data_treinamento, carga_horaria, monitor, instituicao, participantes, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["tipo_treinamento"] ."', '". $_POST["treinamento"] ."', '". formata_data($_POST["data_treinamento"]) ."',
										 '". $_POST["carga_horaria"] ."', '". $_POST["monitor"] ."', '". $_POST["instituicao"] ."', '". $_POST["participantes"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$id_treinamento= mysql_insert_id();
				
				$i=0;
				while ($_POST["nada"][$i]) {
					if ($_POST["id_funcionario"][$i]!="") {
						
						$result2[$i]= mysql_query("insert into rh_treinamentos_funcionarios
												  	(id_empresa, id_treinamento, id_funcionario, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_treinamento ."', '". $_POST["id_funcionario"][$i] ."', '". $_SESSION["id_usuario"] ."')
													") or die(mysql_error());
						if (!$result2[$i]) $var++;
					}
					$i++;
				}
				
				finaliza_transacao($var);
				$msg= $var;
				
				header("location: ./?pagina=rh/treinamento_listar&tipo_treinamento=". $_POST["tipo_treinamento"] ."&msg=". $msg);
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				
				inicia_transacao();
				$var=0;

				$result1= mysql_query("update rh_treinamentos set
										treinamento= '". $_POST["treinamento"] ."',
										data_treinamento = '". formata_data($_POST["data_treinamento"]) ."',
										carga_horaria= '". $_POST["carga_horaria"] ."',
										monitor= '". $_POST["monitor"] ."',
										instituicao= '". $_POST["instituicao"] ."',
										participantes= '". $_POST["participantes"] ."'
										where id_treinamento = '". $_POST["id_treinamento"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				$result_limpa= mysql_query("delete from rh_treinamentos_funcionarios
										   	where id_empresa = '". $_SESSION["id_empresa"] ."'
											and   id_treinamento = '". $_POST["id_treinamento"] ."'
											");
				
				if (!$result_limpa) $var++;
				
				$i=0;
				while ($_POST["nada"][$i]) {
					if ($_POST["id_funcionario"][$i]!="") {
						
						$result2[$i]= mysql_query("insert into rh_treinamentos_funcionarios
												  	(id_empresa, id_treinamento, id_funcionario, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $id_treinamento ."', '". $_POST["id_funcionario"][$i] ."', '". $_SESSION["id_usuario"] ."')
													");
						if (!$result2[$i]) $var++;
					}
					$i++;
				}
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				header("location: ./?pagina=rh/treinamento_listar&tipo_treinamento=". $_POST["tipo_treinamento"] ."&msg=". $msg);
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formVTLinha"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["linha"]!="") && ($_POST["valor"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_vt_linhas (id_empresa, linha, valor) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["linha"] ."', '". formata_valor($_POST["valor"]) ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/vt_linha_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();

				$result1= mysql_query("update rh_vt_linhas set
										linha= '". $_POST["linha"] ."',
										valor = '". formata_valor($_POST["valor"]) ."'
										where id_linha = '". $_POST["id_linha"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "rh/vt_linha_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formHistorico"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_historico (id_empresa, id_funcionario, data_historico, historico, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario"] ."',
										'". formata_data($_POST["data_historico"]) ."', '". $_POST["historico"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/historico_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
			
				inicia_transacao();

				$result1= mysql_query("update rh_historico set
										id_funcionario= '". $_POST["id_funcionario"] ."',
										data_historico = '". formata_data($_POST["data_historico"]) ."',
										historico= '". $_POST["historico"] ."'
										where id_historico = '". $_POST["id_historico"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "rh/historico_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formEscalaTroca"])) {
		
		//inserir
		if ($_GET["acao"]=="i") {	
			if ( ($_SESSION["id_empresa"]!="") && ($_POST["id_funcionario_solicitante"]!="") && ($_POST["id_funcionario_assume"]!="") ) {
			
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_escala_troca (id_empresa, id_funcionario_solicitante, id_funcionario_assume, data_escala_troca, justificativa, id_usuario) values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_funcionario_solicitante"] ."', '". $_POST["id_funcionario_assume"] ."',
										'". formata_data($_POST["data_escala_troca"]) ."', '". $_POST["justificativa"] ."', '". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				
				$result2= mysql_query("insert into rh_escala
												(id_funcionario, data_escala, trabalha, troca, id_usuario) values
												('". $_POST["id_funcionario_assume"] ."', '". formata_data($_POST["data_escala_troca"])  ."',
												 '1', '1', '". $_SESSION["id_usuario"] ."') ");
				if (!$result2) $var++;
				
				$result3= mysql_query("update rh_escala
									  			set trabalha = '0'
									  			where id_funcionario= '". $_POST["id_funcionario_solicitante"] ."'
												and   data_escala= '". formata_data($_POST["data_escala_troca"]) ."'
												limit 1
												");
				if (!$result3) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
					
				$pagina= "rh/escala_troca_listar";
				require_once("index2.php");
			}
		}
			
		//editar
		if ($_GET["acao"]=="e") {
			if ($_POST["id_escala_troca"]!="") {
				$var=0;
			
				inicia_transacao();

				$result1= mysql_query("update rh_escala_troca set
										justificativa= '". $_POST["justificativa"] ."'
										where id_escala_troca = '". $_POST["id_escala_troca"] ."'
										") or die(mysql_error());
										
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$pagina= "rh/escala_troca_listar";
				require_once("index2.php");
			}//teste variáveis
		}//e
	}
	
	if (isset($_GET["formTurno"])) {
		if (($_SESSION["id_empresa"]!="") && ($_POST["id_departamento"]!="") && ($_POST["turno"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result1= mysql_query("insert into rh_turnos (id_departamento, turno, status_turno, dias_trabalhados_semana, id_regime) values
										('". $_POST["id_departamento"] ."', '". strtoupper($_POST["turno"]) ."',
											'1', '". $_POST["dias_trabalhados_semana"] ."', '". $_POST["id_regime"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_turno= mysql_insert_id();
				
				//-------------------------- dias
				$i=0;
				while ($_POST["id_dia"][$i]!="") {
					if (($_POST["entrada"][$i]!="") && ($_POST["saida"][$i]!="")) {
						$result2= mysql_query("insert into rh_turnos_horarios
												(id_turno, id_dia, entrada, saida, hl) values
												('$id_turno', '". $_POST["id_dia"][$i]  ."', '". $_POST["entrada"][$i] ."', '". $_POST["saida"][$i] ."', '". $_POST["hl"][$i] ."')
												");
						if (!$result2) $var++;
					}
					$i++;
				}
				
				//----------------------- intervalos
				
				finaliza_transacao($var);
				$msg= $var;
				
				$pagina= "rh/turno_listar";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				$result_pre= mysql_query("select turno from rh_turnos
											where turno = '". strtoupper($_POST["turno"]) ."'
											and   id_departamento = '". $_POST["id_departamento"] ."'
											and   id_turno <> '". $_POST["id_turno"] ."'
											");
				if (mysql_num_rows($result_pre)==0) {
					inicia_transacao();

					$result1= mysql_query("update rh_turnos, rh_departamentos set
											rh_turnos.id_departamento= '". $_POST["id_departamento"] ."',
											rh_turnos.turno= '". strtoupper($_POST["turno"]) ."',
											rh_turnos.dias_trabalhados_semana= '". $_POST["dias_trabalhados_semana"] ."',
											rh_turnos.id_regime= '". strtoupper($_POST["id_regime"]) ."'
											where rh_turnos.id_turno = '". $_POST["id_turno"] ."'
											and   rh_turnos.id_departamento = rh_departamentos.id_departamento
											and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
											") or die(mysql_error());
					if (!$result1) $var++;
					
					$result2= mysql_query("delete from rh_turnos_horarios
											where id_turno = '". $_POST["id_turno"] ."' ") or die(mysql_error());
					if (!$result2) $var++;
					
					/*$result3= mysql_query("delete from rh_turnos_intervalos
											where id_turno = '". $_POST["id_turno"] ."' ") or die(mysql_error());
					if (!$result3) $var++;*/
					
					//-------------------------- dias
					
					$i=0;
					while ($_POST["id_dia"][$i]!="") {
						if (($_POST["entrada"][$i]!="") && ($_POST["saida"][$i]!="")) {
							$result4[$i]= mysql_query("insert into rh_turnos_horarios
													(id_turno, id_dia, entrada, saida, hl) values
													('". $_POST["id_turno"] ."', '". $_POST["id_dia"][$i]  ."', '". $_POST["entrada"][$i] ."', '". $_POST["saida"][$i] ."', '". $_POST["hl"][$i] ."')
													") or die(mysql_error());
							if (!$result4[$i]) $var++;
						}						
						$i++;
					}
					
					finaliza_transacao($var);
				} else $var++;
				
				$msg= $var;
				
				$pagina= "rh/turno_listar";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formIntervaloTurno"])) {
		if (($_POST["id_turno"]!="") && ($_POST["intervalo"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result= mysql_query("insert into rh_turnos_intervalos (id_turno, intervalo) values
										('". $_POST["id_turno"] ."', '". strtoupper($_POST["intervalo"]) ."' ) ") or die(mysql_error());
				if (!$result) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				$acao='i';
				$pagina= "rh/turno_intervalo";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();

				$result1= mysql_query("update rh_turnos_intervalos set
										intervalo= '". strtoupper($_POST["intervalo"]) ."'
										where id_intervalo = '". $_POST["id_intervalo"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				$msg= $var;
				
				$acao= 'i';
				$pagina= "rh/turno_intervalo";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formIntervaloHorario"])) {
		if (($_POST["id_intervalo"]!="") && ($_POST["id_dia"]!="") && ($_POST["intervalo_apos"]!="") && ($_POST["intervalo_duracao"]!="")) {
			inicia_transacao();
			$var=0;
			
			//$result_pre= mysql_query("select * from rh_turnos_intervalos_horarios
			//								where id_intervalo = '". $_POST["id_intervalo"] ."'
			//								and   id_dia = '". $_POST["id_dia"] ."' ") or die(mysql_error());
				
			//if (mysql_num_rows($result_pre)==0) {
				
				//se estiver inserindo na segunda, no intervalinho manjado... faz pros outros dias da semana
				if (( ($_POST["id_dia"]==0) || ($_POST["id_dia"]==1) ) && ($_POST["intervalo_apos"]=="03:00:00") && ($_POST["intervalo_duracao"]=="00:15:00") && ($_POST["automatico"]==1)) {
					$limite= inverte($_POST["id_dia"]);
					for ($i=$_POST["id_dia"]; $i<(6+$limite); $i++) {
						$result[$i]= mysql_query("insert into rh_turnos_intervalos_horarios (id_intervalo, id_dia, intervalo_apos, intervalo_duracao, automatico) values
												('". $_POST["id_intervalo"] ."', '". $i ."',
												'". $_POST["intervalo_apos"] ."', '". $_POST["intervalo_duracao"] ."',
												'". $_POST["automatico"] ."' ) ") or die(mysql_error());
						if (!$result[$i]) $var++;
					}
				}
				else {
					$result= mysql_query("insert into rh_turnos_intervalos_horarios (id_intervalo, id_dia, intervalo_apos, intervalo_duracao, automatico) values
											('". $_POST["id_intervalo"] ."', '". $_POST["id_dia"] ."',
											'". $_POST["intervalo_apos"] ."', '". $_POST["intervalo_duracao"] ."',
											'". $_POST["automatico"] ."' ) ") or die(mysql_error());
					if (!$result) $var++;
				}
			//} else $var++;
			
			finaliza_transacao($var);
			$msg= $var;
			
			$acao='i';
			$pagina= "rh/turno_intervalo_horarios";
			require_once("index2.php");
		}//fim teste variáveis
	}
}

/* ---------- ESTOQUE ------------------------------------------------------------------- */

if (pode("iq|", $_SESSION["permissao"])) {
	
	if (isset($_GET["formItem"])) {
		if (($_POST["item"]!="") && ($_POST["tipo_apres"]!="")) {
			//inserir
			if ($_GET["acao"]=="i") {	
				inicia_transacao();
				$var=0;
			
				$result_antes= mysql_query("select item from fi_itens
												where item = '". $_POST["item"] ."'
												and   tipo_apres = '". $_POST["tipo_apres"] ."'
												");
		
				if (mysql_num_rows($result_antes)==0) {
					$result= mysql_query("insert into fi_itens (item, tipo_apres, id_centro_custo_tipo, id_usuario)
									values ('". strtoupper(trim($_POST["item"])) ."', '". $_POST["tipo_apres"] ."',
											'". $_POST["id_centro_custo_tipo"] ."', '". $_SESSION["id_usuario"] ."') ");
				}
				
				if (!$result) $var++;
				
				finaliza_transacao($var);
				$msg= $var;
				
				$letra= strtolower(substr($_POST["item"], 0, 1));
				$pagina= "financeiro/item_esquema";
				require_once("index2.php");
			}
				
			//editar
			if ($_GET["acao"]=="e") {
				$var=0;
				inicia_transacao();

				$result1= mysql_query("update fi_itens set
										item= '". strtoupper(trim($_POST["item"])) ."',
										tipo_apres= '". $_POST["tipo_apres"] ."',
										id_centro_custo_tipo= '". $_POST["id_centro_custo_tipo"] ."'
										where id_item = '". $_POST["id_item"] ."'
										") or die(mysql_error());
				if (!$result1) $var++;
				
				finaliza_transacao($var);
				
				$msg= $var;
				
				$letra= strtolower(substr($_POST["item"], 0, 1));
				$pagina= "financeiro/item_esquema";
				require_once("index2.php");
			}//e
		}//fim teste variáveis
	}
	
	if (isset($_GET["formEstoqueEntrada"])) {
		if (($_POST["id_item"]!="") && ($_POST["qtde"]!="")) {
		
			$var= 0;
			inicia_transacao();
			
			$id_centro_custo_tipo= pega_id_centro_custo_tipo_do_item($_POST["id_item"]);
			
			$result1= mysql_query("insert into fi_estoque_mov
									(id_empresa, id_item, tipo_trans, qtde,
									 data_trans, hora_trans, observacoes, valor_unitario, modo, id_centro_custo_tipo, id_usuario)
									values
									('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."', 'e',
									'". formata_valor($_POST["qtde"]) ."', '". date("Ymd") ."', '". date("His") ."',
									'". strip_tags($_POST["observacoes"]) ."', '". formata_valor($_POST["valor_unitario"]) ."',
									'1', '". $id_centro_custo_tipo ."', '". $_SESSION["id_usuario"] ."'
									)");
			
			if (!$result1) $var++;
			$id_mov= mysql_insert_id();
			
			$result2= mysql_query("select id from fi_estoque
									where id_empresa= '". $_SESSION["id_empresa"] ."'
									and   id_item = '". $_POST["id_item"] ."'
									");
			
			if (mysql_num_rows($result2)==0)
				$result3= mysql_query("insert into fi_estoque 
										(id_empresa, id_item, qtde_atual)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."', '". formata_valor($_POST["qtde"]) ."' ) ");
			else
				$result3= mysql_query("update fi_estoque set
										qtde_atual = qtde_atual + '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										");
			
			if (!$result3) $var++;
			
			$result5_pre= mysql_query("select * from fi_estoque_iv
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										and   valor_unitario = ". formata_valor($_POST["valor_unitario"]) ."
										");
			
			$linhas5_pre= mysql_num_rows($result5_pre);
			
			if ($linhas5_pre==0)
				$result5= mysql_query("insert into fi_estoque_iv
										(id_empresa, id_item, qtde, valor_unitario, data_trans, hora_trans, id_nota, id_mov, id_centro_custo_tipo, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."',
										'". formata_valor($_POST["qtde"]) ."',
										'". formata_valor($_POST["valor_unitario"]) ."',
										'". date("Ymd") ."', '". date("His") ."', 
										'0', '$id_mov', '$id_centro_custo_tipo',
										'". $_SESSION["id_usuario"] ."'
										) ");
			else
				$result5= mysql_query("update fi_estoque_iv
										set qtde = qtde + '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										and   valor_unitario = ". formata_valor($_POST["valor_unitario"]) ."
										");
			
			if (!$result5) $var++;
			
			finaliza_transacao($var);
		}
		
		//if ($var==0) {
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá entrada em remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]), $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
		//}
	
		$msg= $var;
		
		$pagina= "financeiro/estoque_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formItemExcluirNovo"])) {
		if (($_POST["id_item"]!="") && ($_POST["id_item_novo"]!="")) {
		
			$var= 0;
			inicia_transacao();
			
			
			
			
			
			
			
			$result_pre= mysql_query("select * from fi_estoque_mov
											where id_item = '". $_POST["id_item"] ."'
											");
			//procura na movimentação se o item a ser excluído já foi usado, caso não deleta o produto...
			if (mysql_num_rows($result_pre)==0) {
				$rs= mysql_fetch_object(mysql_query("select * from fi_itens where id_item= '". $_POST["id_item"] ."' "));
				
				$result1= mysql_query("delete from fi_itens
										where id_item = '". $_POST["id_item"] ."'
										limit 1
										");
				if (!$result1) $var++;
				
				//echo "1";
			}
			//já foi usado
			else {
				//echo "2";
				//atualiza tudo na movimentação que era do item antigo, passa para o novo...
				$result1= mysql_query("update fi_estoque_mov set
										id_item = '". $_POST["id_item_novo"] ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										");
				if (!$result1) $var++;
				
				
				$result2_pre= mysql_query("select * from fi_estoque
											where id_empresa= '". $_SESSION["id_empresa"] ."'
											and   id_item = '". $_POST["id_item"] ."'
											");
				
				//procura no estoque atual a quantidade do item velho...
				//caso encontre alguma coisa, atualiza o estoque do item novo, somando a quantidade do velho.
				if (mysql_num_rows($result2_pre)>0) {
					//echo "3";
					$rs2_pre= mysql_fetch_object($result2_pre);
					
					$result3_pre= mysql_query("select * from fi_estoque
												where id_empresa= '". $_SESSION["id_empresa"] ."'
												and   id_item = '". $_POST["id_item_novo"] ."'
												");
					$linhas3_pre= mysql_num_rows($result3_pre);
					
					if ($linhas3_pre>0) {
						//echo "4";
						$result3= mysql_query("update fi_estoque set
												qtde_atual = qtde_atual + '". $rs2_pre->qtde_atual ."'
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_item= '". $_POST["id_item_novo"] ."'
												");
					}
					else {
						//echo "5";
						$result3= mysql_query("insert into fi_estoque
												(id_empresa, id_item, qtde_atual)
												values
												('". $_SESSION["id_empresa"] ."', '". $_POST["id_item_novo"] ."', '". $rs2_pre->qtde_atual ."')
												");
					}
					if (!$result3) $var++;
				}
				
				
				$result4= mysql_query("delete from fi_itens
										where id_item = '". $_POST["id_item"] ."'
										limit 1
										");
				if (!$result4) $var++;
		
			}
			
			finaliza_transacao($var);
		}
		
		//if ($var==0) {
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá entrada em remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]), $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
		//}
	
		$msg= $var;
		
		$letra= strtolower(substr($_POST["item"], 0, 1));
		$pagina= "financeiro/item_esquema";
		require_once("index2.php");
	}
	
	if (isset($_GET["formEstoqueSaida"])) {
		if (($_POST["id_item"]!="") && ($_POST["qtde"]!="")) {
			$var= 0;
			inicia_transacao();
			
			if ($_POST["id_deposito"]!="") $subtipo_trans= "m";
			else $subtipo_trans= "";
			
			$data_saida= formata_data($_POST["data_saida"]);
			$hora_saida= $_POST["hora_saida"];
			
			$result_pre= mysql_query("select qtde_atual from fi_estoque
										where id_empresa= '". $_SESSION["id_empresa"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										");
			
			$rs_pre= mysql_fetch_object($result_pre);
			
			//se a qtde atual for maior ou igual q a solicitada para saída
			if ($rs_pre->qtde_atual >= formata_valor($_POST["qtde"])) {
				
				$result1= mysql_query("insert into fi_estoque_mov
										(id_empresa, id_deposito, id_item, tipo_trans, subtipo_trans, id_motivo, qtde, data_trans, hora_trans, observacoes, id_usuario)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_deposito"] ."', '". $_POST["id_item"] ."', 's', '". $subtipo_trans ."', '". $_POST["id_motivo"] ."',
										'". formata_valor($_POST["qtde"]) ."', '". $data_saida ."', '". $hora_saida ."',
										'". strip_tags($_POST["observacoes"]) ."', '". $_SESSION["id_usuario"] ."')");
				
				if (!$result1) $var++;
				$id_mov= mysql_insert_id();
				
				$result2= mysql_query("update fi_estoque set
										qtde_atual = qtde_atual - '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										") or die(mysql_error());
				
				if (!$result2) $var++;
				
				//se não está só saindo, está adicionando em um depósito
				if ($_POST["id_deposito"]!="") {
					$result4= mysql_query("select id from fi_estoque_deposito
											where id_empresa= '". $_SESSION["id_empresa"] ."'
											and   id_deposito = '". $_POST["id_deposito"] ."'
											and   id_item = '". $_POST["id_item"] ."'
											");
					
					if (mysql_num_rows($result4)==0)
						$result5= mysql_query("insert into fi_estoque_deposito
												(id_deposito, id_empresa, id_item, qtde_atual)
												 values
												('". $_POST["id_deposito"] ."', '". $_SESSION["id_empresa"] ."',
												 '". $_POST["id_item"] ."', '". formata_valor($_POST["qtde"]) ."' ) ");
					else
						$result5= mysql_query("update fi_estoque_deposito set
												qtde_atual = qtde_atual + '". formata_valor($_POST["qtde"]) ."'
												where id_empresa = '". $_SESSION["id_empresa"] ."'
												and   id_deposito = '". $_POST["id_deposito"] ."'
												and   id_item= '". $_POST["id_item"] ."'
												");
					
					if (!$result5) $var++;
				}
				
				$result_iv= mysql_query("select * from fi_estoque_iv
											where id_item= '". $_POST["id_item"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											and   qtde > '0'
											order by data_trans asc, hora_trans asc
											");
				$saldo= 0;
				$i= 0;
				
				while ($rs_iv= mysql_fetch_object($result_iv)) {
					if ($i==0) {
						$var_qtde= $_POST["qtde"];
						$saldo= $_POST["qtde"];
					}
					else $var_qtde= $saldo;
					
					//se o que está dando saída é maior que a linha atual
					if ($var_qtde>=$rs_iv->qtde) {
						$saldo= $_POST["qtde"]-$rs_iv->qtde;
						
						$valor= $rs_iv->qtde*$rs_iv->valor_unitario;
						
						//centro de custo...
						if ($_POST["id_motivo"]=="0") {
							$result3= mysql_query("insert into fi_custos
													(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
													values
													('". $_SESSION["id_empresa"] ."', '". $_POST["id_centro_custo"] ."',
													'". $_POST["id_centro_custo_tipo"] ."', '$id_mov',
													'". $data_saida ."', '". $hora_saida ."', '". $valor ."', '1',
													'". $_SESSION["id_usuario"] ."')");
				
							if (!$result3) $var++;
						}
						
						$result4= mysql_query("update fi_estoque_iv
												set qtde= '0'
												where id_iv= '". $rs_iv->id_iv ."'
												limit 1
												");
						if (!$result4) $var++;
					}
					else {
						if ($saldo>0) {
							$qtde_aqui=$saldo;
							
							if ($saldo<$rs_iv->qtde) $saldo= 0;
							else $saldo-= $rs_iv->qtde;
							
							$valor= $qtde_aqui*$rs_iv->valor_unitario;
							
							//centro de custo...
							if ($_POST["id_motivo"]=="0") {
								$result3= mysql_query("insert into fi_custos
														(id_empresa, id_centro_custo, id_centro_custo_tipo, id_mov, data, hora, valor, modo, id_usuario)
														values
														('". $_SESSION["id_empresa"] ."', '". $_POST["id_centro_custo"] ."',
														'". $_POST["id_centro_custo_tipo"] ."', '$id_mov',
														'". $data_saida ."', '". $hora_saida ."', '". $valor ."', '1',
														'". $_SESSION["id_usuario"] ."')");
								if (!$result3) $var++;
							}
							
							$result4= mysql_query("update fi_estoque_iv
													set qtde = qtde - '". $qtde_aqui ."'
													where id_iv= '". $rs_iv->id_iv ."'
													limit 1
													");
							if (!$result4) $var++;
						}
					}
					
					$i++;
				}
				
			}//fim teste js+
			else
				$var++;
			
			finaliza_transacao($var);
		}
		
		//if ($var==0)
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá saída de remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]) ." subtipo: ". $_POST["subtipo_trans"] ." para ". pega_nome($_POST["id_pessoa"]) ."", $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			
		$msg= $var;
		
		if ($var==0) $pagina= "financeiro/estoque_saida_inserir";
		else $pagina= "financeiro/estoque_saida_inserir";
		
		require_once("index2.php");
	}
	
	if (isset($_GET["formEstoqueDepositoSaida"])) {
		if (($_POST["id_item"]!="") && ($_POST["qtde"]!="")) {
			$var= 0;
			inicia_transacao();
			
			$result_pre= mysql_query("select qtde_atual from fi_estoque_deposito
										where id_empresa= '". $_SESSION["id_empresa"] ."'
										and   id_deposito = '". $_POST["id_deposito"] ."'
										and   id_item = '". $_POST["id_item"] ."'
										");
			
			$rs_pre= mysql_fetch_object($result_pre);
	
			//se a qtde atual for maior ou igual q a solicitada para saída
			if ($rs_pre->qtde_atual >= formata_valor($_POST["qtde"])) {
				
				$result1= mysql_query("insert into fi_estoque_mov
										(id_deposito, id_item, tipo_trans, id_motivo, qtde, data_trans, hora_trans, observacoes, id_usuario)
										values
										('". $_POST["id_deposito"] ."', '". $_POST["id_item"] ."', 's', '". $_POST["id_motivo"] ."',
										'". formata_valor($_POST["qtde"]) ."', '". date("Ymd") ."', '". date("His") ."',
										'". strip_tags($_POST["observacoes"]) ."', '". $_SESSION["id_usuario"] ."')");
				
				if (!$result1) $var++;
				$id_mov= mysql_insert_id();
				
				$result2= mysql_query("update fi_estoque_deposito set
										qtde_atual = qtde_atual - '". formata_valor($_POST["qtde"]) ."'
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_deposito = '". $_POST["id_deposito"] ."'
										and   id_item= '". $_POST["id_item"] ."'
										") or die(mysql_error());
				
				if (!$result2) $var++;
				
			}//fim teste js+
			else
				$var++;
			
			finaliza_transacao($var);
		}
		
		//if ($var==0)
		//	@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "dá saída de remédio ". $_POST["qtde"]." unid(s) de ID ". $_POST["id_remedio"] ." | ". pega_remedio($_POST["id_remedio"]) ." subtipo: ". $_POST["subtipo_trans"] ." para ". pega_nome($_POST["id_pessoa"]) ."", $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			
		$msg= $var;
		
		if ($var==0) $pagina= "financeiro/estoque_deposito_listar";
		else $pagina= "financeiro/estoque_deposito_saida_inserir";
		
		require_once("index2.php");
	}
	
	if (isset($_GET["formEstoqueMinimo"])) {
		$result_pre= mysql_query("select * from fi_estoque_minimo
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_item = '". $_POST["id_item"] ."'
									");
		//insere
		if (mysql_num_rows($result_pre)==0) {
			$sql= "insert into fi_estoque_minimo
					(id_empresa, id_item, qtde_minima, provisionamento, id_usuario)
					values
					('". $_SESSION["id_empresa"] ."', '". $_POST["id_item"] ."', '". formata_valor($_POST["qtde_minima"]) ."',
					 '". formata_valor($_POST["provisionamento"]) ."',  '". $_SESSION["id_usuario"] ."')
					";
		}
		//edita
		else {
			$sql= "update fi_estoque_minimo set
					qtde_minima = '". formata_valor($_POST["qtde_minima"]) ."',
					provisionamento = '". formata_valor($_POST["provisionamento"]) ."'
					where id_empresa = '". $_SESSION["id_empresa"] ."'
					and   id_item = '". $_POST["id_item"] ."'
					";
		}
		
		$result= mysql_query($sql) or die(mysql_error());
		
		echo "<script language='javascript' type='text/javascript'>;";
		if ($result) {
			//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "insere remédio, ID ". mysql_insert_id() ." | ". $_POST["remedio"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			echo "alert('Dados armazenados com sucesso!');";
			//echo "atribuiValor('qtde_minima', '');";
			echo "fechaDiv('tela_aux');";
		}
		else {
			//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 0, "falha ao inserir remédio, ". $_POST["remedio"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			echo "alert('Não foi possível cadastrar, tente novamente!');";
		}
		echo "</script>";
	}
	
	
}//fim estoque


/*	
	if (isset($_GET["formEqInserir"])) {
		if ($_POST["id_empresa"]!="") {
			
			$result= mysql_query("insert into equipamentos (id_empresa, ident, modelo,
									n_serie, voltagem, classe, tipo, dimensoes) values
									
									('". $_POST["id_empresa"] ."', '". $_POST["ident"] ."',
									'". $_POST["modelo"] ."', '". $_POST["n_serie"] ."',
									'". $_POST["voltagem"] ."', '". $_POST["classe"] ."',
									'". $_POST["tipo"] ."', '". $_POST["dimensoes"] ."' )") or die(mysql_error());
		}

		if ($result) $msg= 0;
		else $msg=1;
		
		$pagina= "_empresas/eq_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formEqEditar"])) {
		if (($_POST["id_eq"]!="") && ($_POST["id_empresa"]!="") ) {
			
			$result= mysql_query("update equipamentos set ident= '". $_POST["ident"] ."',
									modelo= '". $_POST["modelo"] ."',
									n_serie= '". $_POST["n_serie"] ."',
									voltagem= '". $_POST["voltagem"] ."',
									classe= '". $_POST["classe"] ."',
									tipo= '". $_POST["tipo"] ."',
									dimensoes= '". $_POST["dimensoes"] ."'
									where id_eq = '". $_POST["id_eq"] ."' ") or die(mysql_error());

		}

		if ($result) $msg= 0;
		else $msg=1;
		
		$pagina= "_empresas/eq_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formOsEditar"])) {
		if ($_POST["id_os"]!="") {
			
			//inicia_transacao();
			$var=0;
			$result= mysql_query("update oss
										set status_os= '". $_POST["status_os"] ."',
										id_tecnico= '". $_POST["id_tecnico"] ."',
										obs_gerais_tecnico=  '". $_POST["obs_gerais_tecnico"] ."'
										where id_os= '". $_POST["id_os"] ."'
										") or die(mysql_error());
			if (!$result) $var++;
			
			switch($_POST["id_s"]) {
				case "1":
							$result_pre= mysql_query("select id_os from os_hemodialise where id_os = '". $_POST["id_os"] ."' ");
							if (!$result_pre) $var++;
							
							if (mysql_num_rows($result_pre)==0) {
								echo "123";
								$result2= mysql_query("insert into os_hemodialise
													(id_os, servico_executado, material_utilizado)
													values
													('". $_POST["id_os"] ."', '". $_POST["servico_executado"] ."', '". $_POST["material_utilizado"] ."')
													") or die(mysql_error());							
							}
							else {
								echo "123456";
								$result2= mysql_query("update os_hemodialise
													set servico_executado= '". $_POST["servico_executado"] ."',
													material_utilizado= '". $_POST["material_utilizado"] ."'
													where id_os= '". $_POST["id_os"] ."'
													") or die(mysql_error());
							}
							if (!$result2) $var++;
							break;
				case "2":	
							$result_pre= mysql_query("select id_os from os_agua where id_os = '". $_POST["id_os"] ."' ");
							if (!$result_pre) $var++;
							
							if (mysql_num_rows($result_pre)==0)
								$result2= mysql_query("insert into os_agua
													(id_os, servico_executado, ver_50micras, ver_20micras, ver_01micra, obs_50micras,
													obs_20micras, obs_01micra, ntroca, ver_formol40, ver_hipoclorito12, ver_salgrosso, ver_desibac, obs_formol40, 
													obs_hipoclorito12, obs_salgrosso, obs_desibac)
													
													values
													
													('". $_POST["id_os"] ."', '". $_POST["servico_executado"] ."',
													'". $_POST["ver_50micras"] ."', '". $_POST["ver_20micras"] ."', '". $_POST["ver_01micra"] ."',
													'". $_POST["obs_50micras"] ."', '". $_POST["obs_20micras"] ."', '". $_POST["obs_01micra"] ."',
													'". $_POST["ntroca"] ."', '". $_POST["ver_formol40"] ."', '". $_POST["ver_hipoclorito12"] ."',
													'". $_POST["ver_salgrosso"] ."', '". $_POST["ver_desibac"] ."', '". $_POST["obs_formol40"] ."',
													'". $_POST["obs_hipoclorito12"] ."', '". $_POST["obs_salgrosso"] ."', '". $_POST["obs_desibac"] ."' )

													") or die(mysql_error());	
							else
								$result2= mysql_query("update os_agua
													set servico_executado= '". $_POST["servico_executado"] ."',
													ver_50micras= '". $_POST["ver_50micras"] ."',
													ver_20micras= '". $_POST["ver_20micras"] ."',
													ver_01micra= '". $_POST["ver_01micra"] ."',
													obs_50micras= '". $_POST["obs_50micras"] ."',
													obs_20micras= '". $_POST["obs_20micras"] ."',
													obs_01micra= '". $_POST["obs_01micra"] ."',
													ntroca= '". $_POST["ntroca"] ."',
													ver_formol40= '". $_POST["ver_formol40"] ."',
													ver_hipoclorito12= '". $_POST["ver_hipoclorito12"] ."',
													ver_salgrosso= '". $_POST["ver_salgrosso"] ."',
													ver_desibac= '". $_POST["ver_desibac"] ."',
													obs_formol40= '". $_POST["obs_formol40"] ."',
													obs_hipoclorito12= '". $_POST["obs_hipoclorito12"] ."',
													obs_salgrosso= '". $_POST["obs_salgrosso"] ."',
													obs_desibac= '". $_POST["obs_desibac"] ."'
													where id_os= '". $_POST["id_os"] ."'
													") or die(mysql_error());
							if (!$result2) $var++;
							//deletar os parametros para inseri-los novamente
							$result_del= mysql_query("delete from os_agua_analise where id_os = '". $_POST["id_os"] ."' ");
							
							//-------------------------- análise da água
							$i=0;
							while ($_POST["id_carac"][$i]) {
								$result3= mysql_query("insert into os_agua_analise
														(id_os, id_carac, parametro) values
														('". $_POST["id_os"]  ."', '". $_POST["id_carac"][$i] ."', '". $_POST["parametro"][$i] ."')
														");
								if (!$result3) $var++;
								$i++;
							}
							
							
						break;
			}//fim switch
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$id_os= $_POST["id_os"];
			$pagina= "_os/os_ver";
			require_once("index2.php");
		}
	}
	
	if (isset($_GET["formCpBuscar"])) {
		$pagina= "_agua/cp_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formCtBuscar"])) {
		$pagina= "_agua/ct_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formMceBuscar"])) {
		$pagina= "_agua/mce_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formMlBuscar"])) {
		$pagina= "_agua/ml_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formAclBuscar"])) {
		$pagina= "_agua/acl_listar";
		require_once("index2.php");
	}
	if (isset($_GET["formVtBuscar"])) {
		$pagina= "_agua/vt_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formFluxoBuscar"])) {
		$pagina= "_fluxo/fluxo_listar";
		require_once("index2.php");
	}

}//fim soh empresa admin

// ------------------------------------------------------------------------ OS --------------------------

if (pode("o", $_SESSION["permissao"])) {
	if (isset($_GET["formOsInserir"])) {
		if (($_POST["id_servico"]!="") && ($_POST["prioridade"]!="") && ($_POST["solicitante"]!="") ) {
			
			$result= mysql_query("insert into oss (status_os, id_empresa, data_os, solicitante, tel_solicitante, prioridade, id_tecnico, tipo_atendimento,
													id_servico, equipamento, nserie, obs, obs_gerais) values
									('0', '". $_SESSION["id_empresa"] ."', '". date("Y-m-d H:i:s") ."', '". $_POST["solicitante"] ."', '". $_POST["tel_solicitante"] ."',
										'". $_POST["prioridade"] ."', '". $_POST["id_tecnico"] ."', '". $_POST["tipo_atendimento"] ."', '". $_POST["id_servico"] ."',
										'". $_POST["equipamento"] ."', '". $_POST["nserie"] ."', '". $_POST["obs"] ."', '". $_POST["obs_gerais"] ."' ) ") or die(mysql_error());
			
			if ($result) {
				$id_os= mysql_insert_id();
				$corpo= "<b>". data_extenso() ."</b>
							<br /><br />
							Olá <b>Prospital</b>, a empresa <b>". $_SESSION["nome_fantasia"] ."</b> acaba de inserir uma ordem de serviço no site da Prospital.
							<br /><br />
							Para visualizar/editar/imprimir a solicitação clique no link abaixo:
							<br /><br />
							<a href=\"http://www.prospital.com/sistema/?pagina=_os/os_ver&amp;id_os=". $id_os ."\" target=\"_blank\">http://www.prospital.com/sistema/?pagina=_os/os_ver&amp;id_os=". $id_os ."</a>
							<br /><br />
							------ <br />
							Atenciosamente,
							<br /><br />
							Prospital.com<br />
							<a href=\"http://www.prospital.com\">http://www.prospital.com</a>
							";
				
				@enviar_email("jaisonn@gmail.com, prospital@prospital.com", "Prospital.com | Nova OS de ". $_SESSION["nome_fantasia"], $corpo);
			}
		}
	
		if ($result) $msg= 0;
		else $msg=1;
		
		$pagina= "_os/os_listar";
		require_once("index2.php");
	}
}

// ------------------------------------------------------------------------ ÁGUA ------------------------

if (pode("a", $_SESSION["permissao"])) {
	
	if (isset($_GET["formAguaMce"])) {
			$var=0;
			inicia_transacao();
			
			$result_pre= mysql_query("select *
										from agua_mce
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_mce = '". $_POST["id_mce"] ."'
										") or die(mysql_error());
			if (!$result_pre) $var++;
			
			//se não tem nada
			if (mysql_num_rows($result_pre)==0) {
				//-------------------------- geral
				$result1= mysql_query("insert into agua_mce (tipo, id_empresa, mes, ano,
															data_coleta, n_laudo, resultado)
															values
															('". $_POST["tipo"] ."', '". $_SESSION["id_empresa"] ."',
															'". $_POST["mes"] ."',
															'". $_POST["ano"] ."', '". formata_data($_POST["data_coleta"]) ."',
															'". $_POST["n_laudo"] ."', '". $_POST["resultado"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_mce= mysql_insert_id();
			}//fim num_rows
			else {
				$id_mce= $_POST["id_mce"];
				
				$result1= mysql_query("update agua_mce set
													tipo= '". $_POST["tipo"] ."',
													mes= '". $_POST["mes"] ."',
													ano= '". $_POST["ano"] ."',
													data_coleta= '". formata_data($_POST["data_coleta"]) ."',
													n_laudo= '". $_POST["n_laudo"] ."',
													resultado= '". $_POST["resultado"] ."'
													where id_mce = '". $id_mce ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													");
				if (!$result1) $var++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/mce_listar";
			require_once("index2.php");
		//}
	}
	
	if (isset($_GET["formAguaMl"])) {
			$var=0;
			inicia_transacao();
			
			$result_pre= mysql_query("select *
										from agua_ml
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   id_ml = '". $_POST["id_ml"] ."'
										") or die(mysql_error());
			if (!$result_pre) $var++;
			
			//se não tem nada
			if (mysql_num_rows($result_pre)==0) {
				//-------------------------- geral
				$result1= mysql_query("insert into agua_ml (id_empresa, mes, ano,
															data_limpeza, data_ver)
															values
															('". $_SESSION["id_empresa"] ."',
															'". $_POST["mes"] ."',
															'". $_POST["ano"] ."', '". formata_data($_POST["data_limpeza"]) ."',
															'". formata_data($_POST["data_ver"]) ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_ml= mysql_insert_id();
			}//fim num_rows
			else {
				$id_ml= $_POST["id_ml"];
				
				$result1= mysql_query("update agua_ml set
													mes= '". $_POST["mes"] ."',
													ano= '". $_POST["ano"] ."',
													data_limpeza= '". formata_data($_POST["data_limpeza"]) ."',
													data_ver= '". formata_data($_POST["data_ver"]) ."'
													where id_ml = '". $id_ml ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													");
				if (!$result1) $var++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/ml_listar";
			require_once("index2.php");
		//}
	}
	
	if (isset($_GET["formAguaVt"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_vt
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_vt = '". $_POST["id_vt"] ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0) {
			//-------------------------- geral
			$result1= mysql_query("insert into agua_vt (id_empresa, n_visitas,
														data_inicial, data_final,
														n_oss, recomendacoes, solicitacao_pecas,
														ver_responsavel, ver_cargo, sis_responsavel, sis_cargo)
														values
														('". $_SESSION["id_empresa"] ."',
														'". $_POST["n_visitas"] ."',
														'". formata_data($_POST["data_inicial"]) ."', '". formata_data($_POST["data_final"]) ."',
														'". $_POST["n_oss"] ."', '". $_POST["recomendacoes"] ."', '". $_POST["solicitacao_pecas"] ."',
														'". $_POST["ver_responsavel"] ."', '". $_POST["ver_cargo"] ."',
														'". $_POST["sis_responsavel"] ."', '". $_POST["sis_cargo"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			$id_vt= mysql_insert_id();
		}//fim num_rows
		else {
			$id_vt= $_POST["id_vt"];
			
			$result1= mysql_query("update agua_vt set
												n_visitas= '". $_POST["n_visitas"] ."',
												data_inicial= '". formata_data($_POST["data_inicial"]) ."',
												data_final= '". formata_data($_POST["data_final"]) ."',
												n_oss= '". $_POST["n_oss"] ."',
												recomendacoes= '". $_POST["recomendacoes"] ."',
												solicitacao_pecas= '". $_POST["solicitacao_pecas"] ."',
												ver_responsavel= '". $_POST["ver_responsavel"] ."',
												ver_cargo= '". $_POST["ver_cargo"] ."',
												sis_responsavel= '". $_POST["sis_responsavel"] ."',
												sis_cargo= '". $_POST["sis_cargo"] ."'
												where id_vt = '". $id_vt ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
			if (!$result1) $var++;
			
			$result_del1= mysql_query("delete from agua_vt_equipamentos where id_vt = '$id_vt' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			$result_del2= mysql_query("delete from agua_vt_filtros where id_vt = '$id_vt' ") or die(mysql_error());
			if (!$result_del2) $var++;
		}
		
		//-------------------------- equipamentos/locais
		$i=0;
		while ($_POST["id_eq"][$i]) {
			$result2= mysql_query("insert into agua_vt_equipamentos
									(id_vt, id_eq, condicao) values
									('$id_vt', '". $_POST["id_eq"][$i] ."', '". $_POST["condicao"][$i] ."')
									") or die(mysql_error());
			if (!$result2) $var++;
			$i++;
		}
		
		//-------------------------- filtros
		$i=0;
		while ($_POST["id_filtro"][$i]) {
			$result3= mysql_query("insert into agua_vt_filtros
									(id_vt, id_filtro, filtro, data_troca, qtde_total) values
									('$id_vt', '". $_POST["id_filtro"][$i] ."', '". $_POST["filtro"][$i] ."',
									'". formata_data($_POST["data_troca"][$i]) ."', '". $_POST["qtde_total"][$i] ."')
									") or die(mysql_error());
			if (!$result3) $var++;
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "_agua/vt_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formAguaCp"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_cp
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   data_cp = '". date("Ymd") ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0) {
			//-------------------------- geral
			$result1= mysql_query("insert into agua_cp (data_cp, id_empresa, qtde_equipamentos,
														obs, conclusao_final, ver_responsavel, ver_cargo, sis_responsavel, sis_cargo)
														values
														(". date("Ymd") .", '". $_SESSION["id_empresa"] ."',
														'". $_POST["qtde_equipamentos"] ."',
														'". $_POST["obs"] ."', '". $_POST["conclusao_final"] ."',
														'". $_POST["ver_responsavel"] ."', '". $_POST["ver_cargo"] ."',
														'". $_POST["sis_responsavel"] ."', '". $_POST["sis_cargo"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			$id_cp= mysql_insert_id();
		}//fim num_rows
		else {
			$id_cp= $_POST["id_cp"];
			
			$result1= mysql_query("update agua_cp set
												qtde_equipamentos= '". $_POST["qtde_equipamentos"] ."',
												obs= '". $_POST["obs"] ."',
												conclusao_final= '". $_POST["conclusao_final"] ."',
												ver_responsavel= '". $_POST["ver_responsavel"] ."',
												ver_cargo= '". $_POST["ver_cargo"] ."',
												sis_responsavel= '". $_POST["sis_responsavel"] ."',
												sis_cargo= '". $_POST["sis_cargo"] ."'
												where id_cp = '". $id_cp ."'
												and   data_cp = '". date("Ymd") ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
			if (!$result1) $var++;
			
			$result_del1= mysql_query("delete from agua_cp_equipamentos where id_cp = '$id_cp' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			$result_del2= mysql_query("delete from agua_cp_analise where id_cp = '$id_cp' ") or die(mysql_error());
			if (!$result_del2) $var++;
			
			$result_del3= mysql_query("delete from agua_cp_filtros where id_cp = '$id_cp' ") or die(mysql_error());
			if (!$result_del3) $var++;
		}
		
		//-------------------------- equipamentos/locais
		$i=0;
		while ($_POST["id_eq"][$i]) {
			$result2= mysql_query("insert into agua_cp_equipamentos
									(id_cp, id_eq, condicao) values
									('$id_cp', '". $_POST["id_eq"][$i] ."', '". $_POST["condicao"][$i] ."')
									");
			if (!$result2) $var++;
			$i++;
		}
		
		//-------------------------- análise da água
		$i=0;
		while ($_POST["id_carac"][$i]) {
			$result3= mysql_query("insert into agua_cp_analise
									(id_cp, id_carac, parametro) values
									('$id_cp', '". $_POST["id_carac"][$i] ."', '". $_POST["parametro"][$i] ."')
									") or die(mysql_error());
			if (!$result3) $var++;
			$i++;
		}
		
		//-------------------------- filtros
		$i=0;
		while ($_POST["filtro"][$i]) {
			$result4= mysql_query("insert into agua_cp_filtros
									(id_cp, filtro, troca, data_troca) values
									('$id_cp', '". $_POST["filtro"][$i] ."', '". $_POST["troca"][$i] ."', '". formata_data($_POST["data_troca"][$i]) ."')
									") or die(mysql_error());
			if (!$result4) $var++;
			$i++;
		}
			
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "_agua/cp_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["formAguaCt1"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_ct
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0) {
			//-------------------------- geral
			$result1= mysql_query("insert into agua_ct (data_ct, id_empresa, qe_qtde, qe_obs, ba_qtde, ba_obs,
														fa_qtde, fa_obs, fa_conclusao_final)
														values
														(". date("Ymd") .", '". $_SESSION["id_empresa"] ."',
														'". $_POST["qe_qtde"] ."', '". $_POST["qe_obs"] ."',
														'". $_POST["ba_qtde"] ."', '". $_POST["ba_obs"] ."',
														'". $_POST["fa_qtde"] ."', '". $_POST["fa_obs"] ."',
														'". $_POST["fa_conclusao_final"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			$id_ct= mysql_insert_id();
			
			//$result1= mysql_query
		}//fim num_rows
		else {
			$id_ct= $_POST["id_ct"];
			
			$result1= mysql_query("update agua_ct set
												qe_qtde= '". $_POST["qe_qtde"] ."',
												qe_obs= '". $_POST["qe_obs"] ."',
												ba_qtde= '". $_POST["ba_qtde"] ."',
												ba_obs= '". $_POST["ba_obs"] ."',
												fa_qtde= '". $_POST["fa_qtde"] ."',
												fa_obs= '". $_POST["fa_obs"] ."',
												fa_conclusao_final= '". $_POST["fa_conclusao_final"] ."'
												where id_ct = '". $id_ct ."'
												and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
												and   id_empresa = '". $_SESSION["id_empresa"] ."'
												") or die(mysql_error());
			if (!$result1) $var++;
			
			$result_del1= mysql_query("delete from agua_ct1_ba_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			$result_del2= mysql_query("delete from agua_ct1_ba_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del2) $var++;
			
			$result_del3= mysql_query("delete from agua_ct1_equipamentos where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del3) $var++;
			
			$result_del4= mysql_query("delete from agua_ct1_fa_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del4) $var++;
			
			$result_del5= mysql_query("delete from agua_ct1_fa_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del5) $var++;
			
			$result_del6= mysql_query("delete from agua_ct1_fa_ag where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del6) $var++;
			
			$result_del7= mysql_query("delete from agua_ct1_qe_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del7) $var++;
	
		}
		
		//-------------------------- equipamentos/locais
		$i=0;
		while ($_POST["id_eq"][$i]) {
			$result2= mysql_query("insert into agua_ct1_equipamentos
									(id_ct, id_eq, condicao) values
									('$id_ct', '". $_POST["id_eq"][$i] ."', '". $_POST["condicao"][$i] ."')
									") or die(mysql_error());
			if (!$result2) $var++;
			$i++;
		}
		
		//-------------------------- quadro elétrico
		$i=0;
		while ($_POST["id_comp_qe"][$i]) {
			$result3= mysql_query("insert into agua_ct1_qe_av
									(id_ct, id_comp_qe, problema_qe) values
									('$id_ct', '". $_POST["id_comp_qe"][$i] ."', '". $_POST["problema_qe"][$i] ."')
									") or die(mysql_error());
			if (!$result3) $var++;
			$i++;
		}
		
		//-------------------------- bombas de alimentação
		$i=0;
		while ($_POST["id_comp_ba"][$i]) {
			$result3= mysql_query("insert into agua_ct1_ba_av
									(id_ct, id_comp_ba, problema_ba) values
									('$id_ct', '". $_POST["id_comp_ba"][$i] ."', '". $_POST["problema_ba"][$i] ."')
									") or die(mysql_error());
			if (!$result3) $var++;
			$i++;
		}
		
		$i=0;
		while ($_POST["id_eq_ba"][$i]) {
			$result4= mysql_query("insert into agua_ct1_ba_eq
									(id_ct, id_eq_ba, b1_ba, b2_ba) values
									('$id_ct', '". $_POST["id_eq_ba"][$i] ."', '". formata_valor($_POST["b1_ba"][$i]) ."', '". formata_valor($_POST["b2_ba"][$i]) ."')
									") or die(mysql_error());
			if (!$result4) $var++;
			$i++;
		}
		
		//-------------------------- filtros de areia
		$i=0;
		while ($_POST["id_comp_fa"][$i]) {
			$result5= mysql_query("insert into agua_ct1_fa_av
									(id_ct, id_comp_fa, problema_fa) values
									('$id_ct', '". $_POST["id_comp_fa"][$i] ."', '". $_POST["problema_fa"][$i] ."')
									") or die(mysql_error());
			if (!$result5) $var++;
			$i++;
		}
		
		$i=0;
		while ($_POST["id_eq_fa"][$i]) {
			$result6= mysql_query("insert into agua_ct1_fa_eq
									(id_ct, id_eq_fa, c1_eq_entrada_fa, c1_eq_saida_fa, c2_eq_entrada_fa, c2_eq_saida_fa) values
									('$id_ct', '". $_POST["id_eq_fa"][$i] ."',
									'". formata_valor($_POST["c1_eq_entrada_fa"][$i]) ."', '". formata_valor($_POST["c1_eq_saida_fa"][$i]) ."',
									'". formata_valor($_POST["c2_eq_entrada_fa"][$i]) ."', '". formata_valor($_POST["c2_eq_saida_fa"][$i]) ."' )
									") or die(mysql_error());
			if (!$result6) $var++;
			$i++;
		}
		
		$i=0;
		while ($_POST["id_ag_fa"][$i]) {
			$result7= mysql_query("insert into agua_ct1_fa_ag
									(id_ct, id_ag_fa, c1_ag_entrada_fa, c1_ag_saida_fa, c2_ag_entrada_fa, c2_ag_saida_fa) values
									('$id_ct', '". $_POST["id_ag_fa"][$i] ."',
									'". formata_valor($_POST["c1_ag_entrada_fa"][$i]) ."', '". formata_valor($_POST["c1_ag_saida_fa"][$i]) ."',
									'". formata_valor($_POST["c2_ag_entrada_fa"][$i]) ."', '". formata_valor($_POST["c2_ag_saida_fa"][$i]) ."' )
									") or die(mysql_error());
			if (!$result7) $var++;
			$i++;
		}
			
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "_agua/ct2";
		require_once("index2.php");
	//}
	}
	
	if (isset($_GET["formAguaCt2"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_ct
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
									and   id_ct = '". $_POST["id_ct"] ."'
									") or die(mysql_error()) or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0)
			die("Preencha corretamente este formulário!");
		else {
			//-------------------------- geral
			$result1= mysql_query("update agua_ct set ab_qtde= '". $_POST["ab_qtde"] ."',
													  ab_obs= '". $_POST["ab_obs"] ."',
													  ab_conclusao_final= '". $_POST["ab_conclusao_final"] ."',
													  fc_qtde= '". $_POST["fc_qtde"] ."',
													  fc_obs= '". $_POST["fc_obs"] ."',
													  fc_conclusao_final= '". $_POST["fc_conclusao_final"] ."'
													  where id_ct = '". $_POST["id_ct"] ."'
													  and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
													  and   id_empresa = '". $_SESSION["id_empresa"] ."'	
														") or die(mysql_error());
			if (!$result1) $var++;
			$id_ct= $_POST["id_ct"];
			
			$result_del1= mysql_query("delete from agua_ct2_ab_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			$result_del2= mysql_query("delete from agua_ct2_ab_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del2) $var++;
			
			$result_del4= mysql_query("delete from agua_ct2_ab_ag where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del4) $var++;
			
			$result_del5= mysql_query("delete from agua_ct2_fc_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del5) $var++;
			
			$result_del6= mysql_query("delete from agua_ct2_fc_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del6) $var++;
			
			$result_del7= mysql_query("delete from agua_ct2_fc_ag where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del7) $var++;
	
			//-------------------------- abrandadores
			$i=0;
			while ($_POST["id_comp_ab"][$i]) {
				$result3= mysql_query("insert into agua_ct2_ab_av
										(id_ct, id_comp_ab, problema_ab) values
										('$id_ct', '". $_POST["id_comp_ab"][$i] ."', '". $_POST["problema_ab"][$i] ."')
										") or die(mysql_error());
				if (!$result3) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_eq_ab"][$i]) {
				$result6= mysql_query("insert into agua_ct2_ab_eq
										(id_ct, id_eq_ab, c1_eq_entrada_ab, c1_eq_saida_ab, c2_eq_entrada_ab, c2_eq_saida_ab) values
										('$id_ct', '". $_POST["id_eq_ab"][$i] ."',
										'". formata_valor($_POST["c1_eq_entrada_ab"][$i]) ."', '". formata_valor($_POST["c1_eq_saida_ab"][$i]) ."',
										'". formata_valor($_POST["c2_eq_entrada_ab"][$i]) ."', '". formata_valor($_POST["c2_eq_saida_ab"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_ag_ab"][$i]) {
				$result6= mysql_query("insert into agua_ct2_ab_ag
										(id_ct, id_ag_ab, c1_ag_entrada_ab, c1_ag_saida_ab, c2_ag_entrada_ab, c2_ag_saida_ab) values
										('$id_ct', '". $_POST["id_ag_ab"][$i] ."',
										'". formata_valor($_POST["c1_ag_entrada_ab"][$i]) ."', '". formata_valor($_POST["c1_ag_saida_ab"][$i]) ."',
										'". formata_valor($_POST["c2_ag_entrada_ab"][$i]) ."', '". formata_valor($_POST["c2_ag_saida_ab"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			//-------------------------- filtros de carvao
			$i=0;
			while ($_POST["id_comp_fc"][$i]) {
				$result3= mysql_query("insert into agua_ct2_fc_av
										(id_ct, id_comp_fc, problema_fc) values
										('$id_ct', '". $_POST["id_comp_fc"][$i] ."', '". $_POST["problema_fc"][$i] ."')
										") or die(mysql_error());
				if (!$result3) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_eq_fc"][$i]) {
				$result6= mysql_query("insert into agua_ct2_fc_eq
										(id_ct, id_eq_fc, c1_eq_entrada_fc, c1_eq_saida_fc, c2_eq_entrada_fc, c2_eq_saida_fc) values
										('$id_ct', '". $_POST["id_eq_fc"][$i] ."',
										'". formata_valor($_POST["c1_eq_entrada_fc"][$i]) ."', '". formata_valor($_POST["c1_eq_saida_fc"][$i]) ."',
										'". formata_valor($_POST["c2_eq_entrada_fc"][$i]) ."', '". formata_valor($_POST["c2_eq_saida_fc"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_ag_fc"][$i]) {
				$result6= mysql_query("insert into agua_ct2_fc_ag
										(id_ct, id_ag_fc, c1_ag_entrada_fc, c1_ag_saida_fc, c2_ag_entrada_fc, c2_ag_saida_fc) values
										('$id_ct', '". $_POST["id_ag_fc"][$i] ."',
										'". formata_valor($_POST["c1_ag_entrada_fc"][$i]) ."', '". formata_valor($_POST["c1_ag_saida_fc"][$i]) ."',
										'". formata_valor($_POST["c2_ag_entrada_fc"][$i]) ."', '". formata_valor($_POST["c2_ag_saida_fc"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
				
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/ct3";
			require_once("index2.php");
		}
	}
	
	if (isset($_GET["formAguaCt3"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_ct
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
									and   id_ct = '". $_POST["id_ct"] ."'
									") or die(mysql_error()) or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0)
			die("Preencha corretamente este formulário!");
		else {
			//-------------------------- geral
			$result1= mysql_query("update agua_ct set or_qtde= '". $_POST["or_qtde"] ."',
													  or_obs= '". $_POST["or_obs"] ."',
													  or_conclusao_final= '". $_POST["or_conclusao_final"] ."',
													  br_qtde= '". $_POST["br_qtde"] ."',
													  br_obs= '". $_POST["br_obs"] ."',
													  br_conclusao_final= '". $_POST["br_conclusao_final"] ."',
													  ts_qtde= '". $_POST["ts_qtde"] ."',
													  ts_obs= '". $_POST["ts_obs"] ."'
													  where id_ct = '". $_POST["id_ct"] ."'
													  and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
													  and   id_empresa = '". $_SESSION["id_empresa"] ."'	
														") or die(mysql_error());
			if (!$result1) $var++;
			$id_ct= $_POST["id_ct"];
			
			$result_del1= mysql_query("delete from agua_ct3_or_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			$result_del2= mysql_query("delete from agua_ct3_or_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del2) $var++;
			
			$result_del4= mysql_query("delete from agua_ct3_or_ag where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del4) $var++;
			
			$result_del5= mysql_query("delete from agua_ct3_br_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del5) $var++;
			
			$result_del6= mysql_query("delete from agua_ct3_br_eq where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del6) $var++;
			
			$result_del7= mysql_query("delete from agua_ct3_ts_av where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del7) $var++;
	
			//-------------------------- osmose reversa
			$i=0;
			while ($_POST["id_comp_or"][$i]) {
				$result3= mysql_query("insert into agua_ct3_or_av
										(id_ct, id_comp_or, problema_or) values
										('$id_ct', '". $_POST["id_comp_or"][$i] ."', '". $_POST["problema_or"][$i] ."')
										") or die(mysql_error());
				if (!$result3) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_eq_or"][$i]) {
				$result6= mysql_query("insert into agua_ct3_or_eq
										(id_ct, id_eq_or, c1_eq_entrada_or, c1_eq_saida_or, c2_eq_entrada_or, c2_eq_saida_or) values
										('$id_ct', '". $_POST["id_eq_or"][$i] ."',
										'". formata_valor($_POST["c1_eq_entrada_or"][$i]) ."', '". formata_valor($_POST["c1_eq_saida_or"][$i]) ."',
										'". formata_valor($_POST["c2_eq_entrada_or"][$i]) ."', '". formata_valor($_POST["c2_eq_saida_or"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_ag_or"][$i]) {
				$result6= mysql_query("insert into agua_ct3_or_ag
										(id_ct, id_ag_or, c1_ag_entrada_or, c1_ag_saida_or, c2_ag_entrada_or, c2_ag_saida_or) values
										('$id_ct', '". $_POST["id_ag_or"][$i] ."',
										'". formata_valor($_POST["c1_ag_entrada_or"][$i]) ."', '". formata_valor($_POST["c1_ag_saida_or"][$i]) ."',
										'". formata_valor($_POST["c2_ag_entrada_or"][$i]) ."', '". formata_valor($_POST["c2_ag_saida_or"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			//-------------------------- bombas de recirculação
			$i=0;
			while ($_POST["id_comp_br"][$i]) {
				$result3= mysql_query("insert into agua_ct3_br_av
										(id_ct, id_comp_br, problema_br) values
										('$id_ct', '". $_POST["id_comp_br"][$i] ."', '". $_POST["problema_br"][$i] ."')
										") or die(mysql_error());
				if (!$result3) $var++;
				$i++;
			}
			
			$i=0;
			while ($_POST["id_eq_br"][$i]) {
				$result6= mysql_query("insert into agua_ct3_br_eq
										(id_ct, id_eq_br, b1_br, b2_br) values
										('$id_ct', '". $_POST["id_eq_br"][$i] ."',
										'". formata_valor($_POST["b1_br"][$i]) ."', '". formata_valor($_POST["b1_br"][$i]) ."' )
										") or die(mysql_error());
				if (!$result6) $var++;
				$i++;
			}
			
			//-------------------------- tanques...
			$i=0;
			while ($_POST["id_comp_ts"][$i]) {
				$result3= mysql_query("insert into agua_ct3_ts_av
										(id_ct, id_comp_ts, problema_ts) values
										('$id_ct', '". $_POST["id_comp_ts"][$i] ."', '". $_POST["problema_ts"][$i] ."')
										") or die(mysql_error());
				if (!$result3) $var++;
				$i++;
			}
				
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/ct4";
			require_once("index2.php");
		}
	}
	
	if (isset($_GET["formAguaCt4"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from agua_ct
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
									and   id_ct = '". $_POST["id_ct"] ."'
									") or die(mysql_error()) or die(mysql_error());
		if (!$result_pre) $var++;
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0)
			die("Preencha corretamente este formulário!");
		else {
			//-------------------------- geral
			$result1= mysql_query("update agua_ct set fil_qtde= '". $_POST["fil_qtde"] ."',
													  ver_responsavel= '". $_POST["ver_responsavel"] ."',
													  ver_cargo= '". $_POST["ver_cargo"] ."',
													  sis_responsavel= '". $_POST["sis_responsavel"] ."',
													  sis_cargo= '". $_POST["sis_cargo"] ."'
													  where id_ct = '". $_POST["id_ct"] ."'
													  and   DATE_FORMAT(data_ct, '%u/%Y') = '". date("W/Y") ."'
													  and   id_empresa = '". $_SESSION["id_empresa"] ."'	
													  ") or die(mysql_error());
			if (!$result1) $var++;
			$id_ct= $_POST["id_ct"];
			
			$result_del1= mysql_query("delete from agua_ct4_filtros where id_ct = '$id_ct' ") or die(mysql_error());
			if (!$result_del1) $var++;
			
			//-------------------------- filtros
			$i=0;
			while ($_POST["filtro"][$i]) {
				$result4= mysql_query("insert into agua_ct4_filtros
										(id_ct, filtro, troca, data_troca) values
										('$id_ct', '". $_POST["filtro"][$i] ."', '". $_POST["troca"][$i] ."', '". formata_data($_POST["data_troca"][$i]) ."')
										");
				if (!$result4) $var++;
				$i++;
			}
						
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/ct_listar";
			require_once("index2.php");
		}
	}
	
	if (isset($_GET["formAguaAcl"])) {
			$var=0;
			inicia_transacao();
			
			//se não tem nada
			if ($_POST["id_acl"]=="") {
				//-------------------------- geral
				$result1= mysql_query("insert into agua_acl (id_empresa, data_acl, responsavel_tecnico)
															values
															('". $_SESSION["id_empresa"] ."', '". formata_data($_POST["data_acl"]) ."',
															'". $_POST["responsavel_tecnico"] ."' ) ") or die(mysql_error());
				if (!$result1) $var++;
				$id_acl= mysql_insert_id();
			}//fim num_rows
			else {
				$id_acl= $_POST["id_acl"];
				
				$result1= mysql_query("update agua_acl set
													data_acl= '". formata_data($_POST["data_acl"]) ."',
													responsavel_tecnico= '". $_POST["responsavel_tecnico"] ."'
													where id_acl = '". $id_acl ."'
													and   id_empresa = '". $_SESSION["id_empresa"] ."'
													");
				if (!$result1) $var++;
				
				$result_del1= mysql_query("delete from agua_acl_cond where id_acl = '$id_acl' ");
				if (!$result_del1) $var++;
				
				$result_del2= mysql_query("delete from agua_acl_prod where id_acl = '$id_acl' ");
				if (!$result_del2) $var++;
			}
			
			//-------------------------- condições operacionais
			$i=0;
			while ($_POST["id_carac"][$i]) {
				if ($_POST["id_carac"][$i]==6) {
					$antes= formata_valor($_POST["antes_entrada"]) .'@'. formata_valor($_POST["antes_saida"]);
					$apos= formata_valor($_POST["apos_entrada"]) .'@'. formata_valor($_POST["apos_saida"]);
				}
				else {
					$antes= formata_valor($_POST["antes"][$i]);
					$apos= formata_valor($_POST["apos"][$i]);
				}
				
				$result2= mysql_query("insert into agua_acl_cond
										(id_acl, id_carac, antes, apos) values
										('$id_acl', '". $_POST["id_carac"][$i] ."', '". $antes ."', '". $apos ."')
										");
				if (!$result2) $var++;
				$i++;
			}
			
			//-------------------------- produtos utilizados
			$i=0;
			while ($_POST["id_prod"][$i]) {
				$result3= mysql_query("insert into agua_acl_prod
										(id_acl, id_prod, produto, qtde, vol_solucao, ri, tr, rf, cor, ph, temp) values
										('$id_acl', '". $_POST["id_prod"][$i] ."', '". $_POST["produto"][$i] ."',
										'". formata_valor($_POST["qtde"][$i]) ."', '". formata_valor($_POST["vol_solucao"][$i]) ."',
										'". $_POST["ri"][$i] ."', '". $_POST["tr"][$i] ."',
										'". $_POST["rf"][$i] ."', '". $_POST["cor"][$i] ."',
										'". formata_valor($_POST["ph"][$i]) ."', '". formata_valor($_POST["temp"][$i]) ."') ");
				if (!$result3) $var++;
				$i++;
			}
			
			finaliza_transacao($var);
			
			$msg= $var;
			
			$pagina= "_agua/acl_listar";
			require_once("index2.php");
		//}
	}
}//fim pode da água

// ------------------------------------------------------------------------ FLUXO --------------

if (pode("f", $_SESSION["permissao"])) {
	
	if (isset($_GET["formFluxo"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select *
									from fluxo
									where id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_eq = '". $_POST["id_eq"] ."'
									") or die(mysql_error());
		if (!$result_pre) $var++;
		
		$i=0;
		while ($_POST["avaliacao_final_item"][$i]) {
			$avaliacao_final .= $_POST["avaliacao_final_item"][$i];
			$i++;
		}
		
		$avaliacao_final= ".". $avaliacao_final .".";
		
		//se não tem nada
		if (mysql_num_rows($result_pre)==0) {
			//-------------------------- geral
			$result1= mysql_query("insert into fluxo (id_empresa, id_eq,
										pf_qtde, pf_ef, pf_marca,
										f_qtde, f_ef, f_marca,
										teste_vel_espec, teste_cont_espec,
										teste_fuga_vaz, teste_fuga_rep,
										avaliacao_final,
										especificacao, obs, ultima_atualizacao, iso_classe,
										inspecionado, data_inspecao, responsavel_tecnico)
										values
										('". $_SESSION["id_empresa"] ."', '". $_POST["id_eq"] ."',
										'". $_POST["pf_qtde"] ."', '". $_POST["pf_ef"] ."', '". $_POST["pf_marca"] ."',
										'". $_POST["f_qtde"] ."', '". $_POST["f_ef"] ."', '". $_POST["f_marca"] ."',
										'". $_POST["teste_vel_espec"] ."', '". $_POST["teste_cont_espec"] ."',
										'". $_POST["teste_fuga_vaz"] ."', '". $_POST["teste_fuga_rep"] ."',
										'". $avaliacao_final ."',
										'". $_POST["especificacao"] ."', '". $_POST["obs"] ."', '". date("Ymd") ."', '". $_POST["iso_classe"] ."',
										'". $_POST["inspecionado"] ."', '". formata_data($_POST["data_inspecao"]) ."', '". $_POST["responsavel_tecnico"] ."' ) ") or die(mysql_error());
			if (!$result1) $var++;
			$id_fluxo= mysql_insert_id();
		}//fim num_rows
		else {
			$id_fluxo= $_POST["id_fluxo"];
			
			$result1= mysql_query("update fluxo set
										pf_qtde= '". $_POST["pf_qtde"] ."',
										pf_ef= '". $_POST["pf_ef"] ."',
										pf_marca= '". $_POST["pf_marca"] ."',
										f_qtde= '". $_POST["f_qtde"] ."',
										f_ef= '". $_POST["f_ef"] ."',
										f_marca= '". $_POST["f_marca"] ."',
										teste_vel_espec= '". $_POST["teste_vel_espec"] ."',
										teste_cont_espec= '". $_POST["teste_cont_espec"] ."',
										teste_fuga_vaz= '". $_POST["teste_fuga_vaz"] ."',
										teste_fuga_rep= '". $_POST["teste_fuga_rep"] ."',
										avaliacao_final= '". $avaliacao_final ."',
										especificacao= '". $_POST["especificacao"] ."',
										obs= '". $_POST["obs"] ."',
										ultima_atualizacao= '". date("Ymd") ."',
										iso_classe= '". $_POST["iso_classe"] ."',
										inspecionado= '". $_POST["inspecionado"] ."',
										data_inspecao= '". formata_data($_POST["data_inspecao"]) ."',
										responsavel_tecnico= '". $_POST["responsavel_tecnico"] ."'
										where id_fluxo = '". $id_fluxo ."'
										and   id_eq = '". $_POST["id_eq"] ."'
										");
			if (!$result1) $var++;
		}
		
		//------------------------------------------------------- itens
		
		$result_del1= mysql_query("delete from fluxo_itens where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_item"][$i]) {
			$result3= mysql_query("insert into fluxo_itens
									(id_item, id_fluxo, condicao) values
									('". $_POST["id_item"][$i] ."', '". $id_fluxo ."', '". $_POST["condicao_".$i] ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		//------------------------------------------------------- parametros
		
		$result_del1= mysql_query("delete from fluxo_parametros where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_parametro"][$i]) {
			$result3= mysql_query("insert into fluxo_parametros
									(id_parametro, id_fluxo, valor) values
									('". $_POST["id_parametro"][$i] ."', '". $id_fluxo ."', '". $_POST["valor"][$i] ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		//------------------------------------------------------- teste de velocidade
		
		$result_del1= mysql_query("delete from fluxo_teste_vel where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_teste_vel"][$i]) {
			if ($i==0) {
				$f1= $_POST["f1_1"] ."@!@". $_POST["f1_2"];
				$f2= $_POST["f2_1"] ."@!@". $_POST["f2_2"];
				$f3= $_POST["f3_1"] ."@!@". $_POST["f3_2"];
				$f4= $_POST["f4_1"] ."@!@". $_POST["f4_2"];
				$f5= $_POST["f5_1"] ."@!@". $_POST["f5_2"];
			}
			else {
				$f1= $_POST["f1"];
				$f2= $_POST["f2"];
				$f3= $_POST["f3"];
				$f4= $_POST["f4"];
				$f5= $_POST["f5"];
			}
			
			$result3= mysql_query("insert into fluxo_teste_vel
									(id_teste_vel, id_fluxo, f1, f2, f3, f4, f5) values
									('". $_POST["id_teste_vel"][$i] ."', '". $id_fluxo ."', '". $f1 ."', '". $f2 ."', '". $f3 ."', '". $f4 ."', '". $f5 ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		//------------------------------------------------------- teste de contagem
		
		$result_del1= mysql_query("delete from fluxo_teste_cont where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_filtro_cont"][$i]) {
			$p1= $_POST["min1"][$i] ."@!@". $_POST["max1"][$i];
			$p2= $_POST["min2"][$i] ."@!@". $_POST["max2"][$i];
			$p3= $_POST["min3"][$i] ."@!@". $_POST["max3"][$i];
			$p4= $_POST["min4"][$i] ."@!@". $_POST["max4"][$i];
			$p5= $_POST["min5"][$i] ."@!@". $_POST["max5"][$i];
			
			$result3= mysql_query("insert into fluxo_teste_cont
									(id_filtro_cont, id_fluxo, p1, p2, p3, p4, p5) values
									('". $_POST["id_filtro_cont"][$i] ."', '". $id_fluxo ."', '". $p1 ."', '". $p2 ."', '". $p3 ."', '". $p4 ."', '". $p5 ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		//------------------------------------------------------- teste de fuga 1
		
		$result_del1= mysql_query("delete from fluxo_teste_fuga where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_filtro_fuga"][$i]) {
			//echo $i ." - ". $_POST["id_filtro_fuga"][$i] ." - ". $_POST["teste_fuga"][$i] ."<br><br>";
			$result3= mysql_query("insert into fluxo_teste_fuga
									(id_filtro_fuga, id_fluxo, valor) values
									('". $_POST["id_filtro_fuga"][$i] ."', '". $id_fluxo ."', '". $_POST["teste_fuga"][$i] ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		//------------------------------------------------------- teste de fuga 2
		
		$result_del1= mysql_query("delete from fluxo_teste_fuga_vaz where id_fluxo = '". $id_fluxo ."' ");
		if (!$result_del1) $var++;
				
		$i=0;
		while ($_POST["id_vaz"][$i]) {
			//echo "*************** ". $i ." - ". $_POST["id_vaz"][$i] ." - ". $_POST["valor_vaz"][$i] ."<br><br>";
			
			$causa_vaz= $_POST["causa_vaz"][$i]; $reparado_vaz= $_POST["reparado_vaz"][$i];
				
			$result3= mysql_query("insert into fluxo_teste_fuga_vaz
									(id_vaz, id_fluxo, valor_vaz, causa_vaz, reparado_vaz) values
									('". $_POST["id_vaz"][$i] ."', '". $id_fluxo ."', '". $_POST["valor_vaz"][$i] ."',
									'". $causa_vaz ."', '". $reparado_vaz ."') ");
			if (!$result3) $var++;
			$i++;
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "_fluxo/fluxo";
		require_once("index2.php");
	}
}//fim pode do fluxo

if (isset($_GET["formContato"])) {
	$enviar_email= @mail("jaisonn@gmail.com, prospital@prospital.com", "PROSPITAL SISTEMA | CONTATO ",
							"Alguém da empresa <b>". $_SESSION["nome_fantasia"] ."</b> enviou a seguinte mensagem em <b>". date("H:i:s d/m/Y") ."</b>: <br />
							<br />
							<b>IP:</b> ". $_SERVER["REMOTE_ADDR"] ." <br />
							<b>Nome:</b> ". $_POST["nome"] ." <br />
							<b>E-mail:</b> ". $_POST["email"] ." <br />
							<b>Telefone:</b> ". $_POST["telefone"] ." <br />
							<b>Cidade:</b> ". $_POST["cidade"] ." <br />
							<b>Tipo de contato:</b> ". $_POST["tipo_contato"] ." <br />
							<b>Mensagem:</b> ". nl2br($_POST["mensagem"]) ."
							<br /><br />
							---------------------
							<br /><br />
							Prospital Sistema<br />
							<a href=\"http://www.prospital.com\">http://www.prospital.com</a>
							",
							"From: ". $_POST["nome"] ." <". $_POST["email"] ."> \nContent-type: text/html\n"); 
	
	$msg= 1;
	if ($enviar_email)
		$msg=0;
	
	$pagina= "_ajuda/contato";
	require_once("index2.php");
}

*/

//echo '</body></html>';

?>