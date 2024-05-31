<?
require_once("funcoes.php");

if (!$conexao)
	require_once("conexao.php");

header("Content-type: text/html; charset=iso-8859-1", true);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ( (!isset($_GET["livroExcluir"])) && (!isset($_GET["rmAndamentoExcluir"])) && (!isset($_GET["mensagemExcluir"]))
		&& (!isset($_GET["rmAndamentoNotaExcluir"])) && (!isset($_GET["avaliaRM"])) && (!isset($_GET["avaliaOS"]))
		&& (!isset($_GET["reclamacaoAndamentoExcluir"])) && (!isset($_GET["reclamacaoAcaoNotaExcluir"]))
		&& (!isset($_GET["avaliaReclamacaoAcao"]) && (!isset($_GET["apagaSeparacaoRemessa"])) )
		)
	echo "<!DOCTYPE>
			<html>
			<head>
			<title>Sige</title>
			</head>
			<body>";

// ############################################### TODOS ###############################################

if (isset($_GET["buscaTempo"])) {
	echo date("d/m/Y") ."<br />". date("H:i:s");
}

if (isset($_GET["carregaPagina"])) {
	require_once("index2.php");
}
if (isset($_GET["carregaPaginaInterna"])) {
	require_once("index2.php");
}

if (isset($_GET["alteraCidade"])) {
	$result= mysql_query("select * from cidades where id_uf = '". $_GET["id_uf"] ."' order by cidade asc ");
	
	$str= "<select name=\"". $_GET["nome_campo"] ."\" id=\"". $_GET["nome_campo"] ."\" title=\"Cidade\">
			<option value=\"\">---</option>";
	
	$i=1;
	while ($rs= mysql_fetch_object($result)) {
		if ($i==1) $classe= " class=\"cor_sim\"";
		else $classe= " ";
		$i++;
		$str .= "<option ". $classe ." value=\"". $rs->id_cidade ."\">". $rs->cidade ."</option>";
		if ($i==2) $i=0;
	}
	
	$str .= "</select>";
	echo $str;
	echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
}

if (isset($_GET["retornaDataFinal"])) {
	$data= explode('/', $_GET["data_inicial"]);
	echo date("d/m/Y", mktime(0, 0, 0, $data[1], $data[0]+($_GET["qtde_dias"]-1), $data[2]));
}

if (isset($_GET["carregaRespostaParaLivro"])) {
	require_once("_com/__livro_form_para.php");
}

// ############################################ LIVRO E RECLAMAÇÕES ###############################################

if (pode("i12o", $_SESSION["permissao"])) {	
	
	if (isset($_GET["clientePecaDobraExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		/*$result_pre= mysql_query("select * from pessoas
									where id_contrato= '". $_GET["id_contrato"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas= mysql_num_rows($result_pre);
		
		if ($linhas==0) {*/
			$result= mysql_query("delete from fi_clientes_pecas_dobra
									where id_cliente = '". $_GET["id_cliente"] ."'
									and   id_cliente_peca_dobra = '". $_GET["id_cliente_peca_dobra"] ."'
									limit 1
									") or die(mysql_error());
			if (!$result) $var++;
			
			@unlink(CAMINHO . "cliente_peca_dobra_". $_GET["id_cliente_peca_dobra"] .".jpg");
		//} else $var++;
		
		finaliza_transacao($var);
		//$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "financeiro/ad_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["clienteHistoricoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from com_livro
								where id_livro = '". $_GET["id_livro"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   restrito = '1'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["clienteSetorExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		/*$result_pre= mysql_query("select * from pessoas
									where id_contrato= '". $_GET["id_contrato"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas= mysql_num_rows($result_pre);
		
		if ($linhas==0) {*/
			$result= mysql_query("delete from fi_clientes_setores
									where id_cliente = '". $_GET["id_cliente"] ."'
									and   id_cliente_setor = '". $_GET["id_cliente_setor"] ."'
									limit 1
									");
			if (!$result) $var++;
		//} else $var++;
		
		finaliza_transacao($var);
		//$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "financeiro/ad_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["clienteItemCedidoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		/*$result_pre= mysql_query("select * from pessoas
									where id_contrato= '". $_GET["id_contrato"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas= mysql_num_rows($result_pre);
		
		if ($linhas==0) {*/
			$result= mysql_query("delete from fi_clientes_itens_cedidos
									where id_cliente = '". $_GET["id_cliente"] ."'
									and   id_item_cedido = '". $_GET["id_item_cedido"] ."'
									limit 1
									");
			if (!$result) $var++;
		//} else $var++;
		
		finaliza_transacao($var);
		//$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "financeiro/ad_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["reclamacaoAcaoNotaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update qual_reclamacoes_andamento
								set nota= NULL
								where id_livro = '". $_GET["id_livro"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_reclamacao_andamento = '". $_GET["id_reclamacao_andamento"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["reclamacaoAndamentoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from qual_reclamacoes_andamento
								where id_livro = '". $_GET["id_livro"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_reclamacao_andamento = '". $_GET["id_reclamacao_andamento"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["avaliaReclamacaoAcao"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update qual_reclamacoes_andamento set
								nota = '". $_GET["nota"] ."'
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_reclamacao_andamento = '". $_GET["id_reclamacao_andamento"] ."'
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["pesquisaItemStatus"])) {
		
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update qual_pesquisa_itens
							  	set status_item = '". $_GET["status"] ."'
								where id_pesquisa_item = '". $_GET["id_pesquisa_item"] ."'
								");
		if (!$result) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		//echo excluido_ou_nao($var);
		
		$pagina= "qualidade/pesquisa_item_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pesquisaItemExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select id_pesquisa_item from qual_pesquisa_notas
								 	where id_pesquisa_item = '". $_GET["id_pesquisa_item"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas_pre= mysql_num_rows($result_pre);
		
		if ($linhas_pre==0) {
			$result1= mysql_query("delete from qual_pesquisa_itens
									where id_pesquisa_item = '". $_GET["id_pesquisa_item"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									limit 1
									");
			if (!$result1) $var++;
		}
		else $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["pesquisaCategoriaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select qual_pesquisa_notas.id_pesquisa_item from qual_pesquisa_notas, qual_pesquisa_itens
								 	where qual_pesquisa_itens.id_pesquisa_categoria = '". $_GET["id_pesquisa_categoria"] ."'
									and   qual_pesquisa_notas.id_pesquisa_item = qual_pesquisa_itens.id_pesquisa_item
									and   qual_pesquisa_notas.id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas_pre= mysql_num_rows($result_pre);
		
		if ($linhas_pre==0) {
			$result1= mysql_query("delete from qual_pesquisa_itens
									where id_pesquisa_categoria = '". $_GET["id_pesquisa_categoria"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
			if (!$result1) $var++;
			
			$result2= mysql_query("delete from qual_pesquisa_categorias
									where id_pesquisa_categoria = '". $_GET["id_pesquisa_categoria"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
			if (!$result2) $var++;
			
		}
		else $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["clientePesquisaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		/*$result_pre= mysql_query("select qual_pesquisa_notas.id_pesquisa_item from qual_pesquisa_notas, qual_pesquisa_itens
								 	where qual_pesquisa_itens.id_pesquisa_categoria = '". $_GET["id_pesquisa_categoria"] ."'
									and   qual_pesquisa_notas.id_pesquisa_item = qual_pesquisa_itens.id_pesquisa_item
									and   qual_pesquisa_notas.id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas_pre= mysql_num_rows($result_pre);
		
		if ($linhas_pre==0) {*/
			$result1= mysql_query("update qual_pesquisa
									set  status_pesquisa = '2'
									where id_pesquisa = '". $_GET["id_pesquisa"] ."'
									and   id_cliente = '". $_GET["id_cliente"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									") or die(mysql_error());
			if (!$result1) $var++;
			
			/*$result2= mysql_query("delete from qual_pesquisa_notas
									where id_pesquisa = '". $_GET["id_pesquisa"] ."'
									and   id_cliente = '". $_GET["id_cliente"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									") or die(mysql_error());
			if (!$result2) $var++;*/
			
			/*
		}
		else $var++;*/
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//header("location: ./?pagina=qualidade/reclamacao&acao=e&id_livro=". $_GET["id_livro"] ."&msg=". $msg ."#situacao");
	}
	
}
// ############################################### ADMIN GERAL ###############################################

if ($_SESSION["tipo_usuario"]=="a") {
	if (isset($_GET["usuarioStatus"])) {
		$result= mysql_query("update usuarios set status_usuario= '". $_GET["status"] ."'
								where id_usuario= '". $_GET["id_usuario"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "acesso/usuario_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["usuarioExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update usuarios
							  	set situacao = '0'
								where id_usuario = '". $_GET["id_usuario"] ."'
								");
		if (!$result) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "acesso/usuario_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["adExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update tr_clientes_ad
							  	set situacao = '0'
								where id_ad = '". $_GET["id_ad"] ."'
								");
		if (!$result) $var++;
		
		finaliza_transacao($var);
		
		//$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "financeiro/ad_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["contratoStatus"])) {
		
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("update fi_contratos
							  	set status_contrato = '". $_GET["status"] ."'
								where id_contrato = '". $_GET["id_contrato"] ."'
								");
		if (!$result) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		//echo excluido_ou_nao($var);
		
		$pagina= "financeiro/contrato_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["contratoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from pessoas
									where id_contrato= '". $_GET["id_contrato"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		$linhas= mysql_num_rows($result_pre);
		
		if ($linhas==0) {
			$result= mysql_query("delete from fi_contratos
									where id_contrato = '". $_GET["id_contrato"] ."'
									limit 1
									");
			if (!$result) $var++;
		} else $var++;
		
		finaliza_transacao($var);
		//$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "financeiro/ad_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["livroExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from com_livro
								where id_livro = '". $_GET["id_livro"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from com_livro_permissoes
								where id_livro = '". $_GET["id_livro"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		if ($_GET["retorno"]=="r") header("location: ./?pagina=qualidade/reclamacao_listar&msg=". $msg);
		else header("location: ./?pagina=com/livro&data=". $_GET["data"] ."&msg=". $msg);
	}
	
}//fim empresa admin

if (pode("api", $_SESSION["permissao"])) {	
	if (isset($_GET["verificaCnpj"])) {
		$cnpj= $_GET["cnpj"];
		$sql= "select pessoas.id_pessoa from pessoas
						where pessoas.cpf_cnpj = '". $cnpj ."'
						and   pessoas.tipo = 'j'
						and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
						";
		
		if ($_GET["id_pessoa"]!="")
			$sql .= " and pessoas.id_pessoa <> '". $_GET["id_pessoa"] ."' " ;
		
		$result= mysql_query($sql) or die(mysql_error());
		
		$campo[0]= "<input type=\"hidden\" name=\"passa_cnpj\" id=\"passa_cnpj\" value=\"\" class=\"escondido\" />";
		$campo[1]= "<input type=\"hidden\" name=\"passa_cnpj\" id=\"passa_cnpj\" value=\"1\" class=\"escondido\" />";
	
		if (mysql_num_rows($result)==0) {
			echo $campo[1] ."<span id=\"span_cnpj_testa\" class=\"verde\">CNPJ disponível!</span>";
			echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
		}
		else {
			$rs= mysql_fetch_object($result);
			
			$result2= mysql_query("select * from pessoas, pessoas_tipos
								 	where pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas.id_pessoa = '". $rs->id_pessoa ."'
									");
			$linhas2= mysql_num_rows($result2);
			
			$como= " como ";
			$pode_duplicar= true;
			
			$i=1;
			while ($rs2= mysql_fetch_object($result2)) {
				if ($rs2->tipo_pessoa=='c') $pode_duplicar= false;
				
				$como .= "<strong>". pega_tipo_pessoa($rs2->tipo_pessoa) ."</strong>";
				
				if ($i!=$linhas2) $como .= ", ";
				
				$i++;
			}
			
			echo $campo[1] ."<span id=\"span_cnpj_testa\" class=\"vermelho\">CNPJ já cadastrado ". $como ."!</span>";
			
			if ($pode_duplicar)
				echo "<br /><label>&nbsp;</label><a class=\"menor\" href=\"javascript:void(0);\" onclick=\"cadastraNovoTipoPessoa('". $rs->id_pessoa ."', '". $_GET["tipo_pessoa"] ."');\">&raquo; cadastrar como <strong>". pega_tipo_pessoa($_GET["tipo_pessoa"]) ."</strong></a>";
			
			echo "<br /><label>&nbsp;</label> <span class=\"menor\">ou prossiga para cadastrar com o mesmo CNPJ</span>";
		}
	}
	
	if (isset($_GET["alteraTipoPessoa"])) {
		if ($_GET["tipo_pessoa"]=='f') require_once("_financeiro/__pessoaf.php");
		else require_once("_financeiro/__pessoaj.php");
	}

	if (isset($_GET["pessoaStatus"])) {
		$result= mysql_query("update pessoas set status_pessoa = '". $_GET["status_pessoa"] ."'
								where id_pessoa= '". $_GET["id_pessoa"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "financeiro/pessoa_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pessoaExcluir"])) {
		$var=0;
		inicia_transacao();
		
		if ($_GET["tipo_pessoa"]!='a') {
			$result_pre= mysql_query("select * from pessoas, pessoas_tipos
										where pessoas.id_pessoa= '". $_GET["id_pessoa"] ."'
										and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
										");
			$linhas= mysql_num_rows($result_pre);
			
			if ($linhas==1) {
				$rs_pre= mysql_fetch_object($result_pre);
				
				$result_pre2= mysql_query("select * from op_suja_pesagem where id_cliente = '". $_GET["id_pessoa"] ."'");
				
				if (mysql_num_rows($result_pre2)==0) {
					/*$result1= mysql_query("delete from pessoas where id_pessoa = '". $_GET["id_pessoa"] ."' limit 1 ");
					if (!$result1) $var++;
					
					$result2= mysql_query("delete from rh_enderecos where id_pessoa = '". $_GET["id_pessoa"] ."' limit 1 ");
					if (!$result2) $var++;
					*/
					
					$result2= mysql_query("delete from pessoas_tipos where id_pessoa = '". $_GET["id_pessoa"] ."' limit 1 ");
					if (!$result2) $var++;
					
					/*$result3= mysql_query("delete from empresas where id_pessoa = '". $rs_pre->id_pessoa ."'
											and  id_empresa = '". $_GET["id_empresa"] ."'
											limit 1 ");
					if (!$result3) $var++;
					*/
				}
			}
			elseif ($linhas>1) {
				$result2= mysql_query("delete from pessoas_tipos
										where id_pessoa = '". $_GET["id_pessoa"] ."'
										and   tipo_pessoa = '". $_GET["tipo_pessoa"] ."'
										limit 1 ");
				if (!$result2) $var++;
				
			} else $var++;
		
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "financeiro/pessoa_listar";
		require_once("index2.php");
	}
}

// ############################################### TRANSPORTE ADM ###############################################

if (pode("pey", $_SESSION["permissao"])) {

	if (isset($_GET["alteraVeiculos"])) {
		if ($_GET[acao]=='e') {
			$result_percurso= mysql_query("select * from tr_percursos
										  	where id_percurso = '". $_GET["id_percurso"] ."'
											and   id_empresa = '". $_SESSION["id_empresa"] ."'
											");
			$rs_percurso= mysql_fetch_object($result_percurso);
		}
		
		if ($_GET["permite"]!=1) $str= " and   tipo_padrao = '". $_GET["tipo"] ."' ";
		else $str= "";
		
		if ($rs_percurso->veiculo_permissao==1) $str= "";
		
		$result= mysql_query("select * from op_veiculos
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   status_veiculo = '1'
								". $str ."
								order by veiculo asc,
								placa asc");
		
		$str= "<select name=\"id_veiculo\" id=\"id_veiculo\" title=\"Veículo\">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\" ";
			else $classe= " ";
			$i++;
			
			if ($rs->id_veiculo==$rs_percurso->id_veiculo) $selecionado= " selected=\"selected\" ";
			else $selecionado= "";
			
			$str .= "<option ". $classe . $selecionado ." value=\"". $rs->id_veiculo ."\">". $rs->veiculo ." ". $rs->placa ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}

	if (isset($_GET["transpCronomgramaExcluir"])) {
		
		$result= mysql_query("delete from tr_cronograma
								where id_cronograma= '". $_GET["id_cronograma"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		
		if ($result) $var=0; else $var=1;
		
		$msg= $var;
		$pagina= "transporte/cronograma_listar";
		require_once("index2.php");
	}

}

// ############################################### TRANSPORTE ###############################################

if (pode("peysl", $_SESSION["permissao"])) {
	
	if (isset($_GET["alteraTipoPercurso"])) {
		require_once("_transporte/__percurso_form_adicional.php");
	}
	
	if (isset($_GET["pegaDadosVeiculo"])) {
		$result= mysql_query("select * from op_veiculos
							 	where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_veiculo = '". $_GET["id_veiculo"] ."'
								");
		$rs= mysql_fetch_object($result);
	?>
    <label>Modelo:</label><?= $rs->veiculo; ?><br />
    <label>Placa:</label><?= $rs->placa; ?><br />
    <label>Chassi:</label><?= $rs->chassi; ?><br />
    <label>Cód. cor:</label><?= $rs->cod_cor; ?><br />
    <label>Motor:</label><?= $rs->motor; ?><br />
    <label>Peso bruto:</label><?= fnum($rs->peso_bruto); ?> kg<br />
    <label>Entre eixos:</label><?= $rs->entre_eixos; ?><br />
    <?
	}
	
	if (isset($_GET["alteraClientesAtivosInativos"])) {
	?>
    <select name="id_cliente" id="id_cliente" title="Cliente">
        <option value="">-</option>
        <?
        $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
									where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.status_pessoa = '". $_GET["status_pessoa"] ."'
									order by 
									pessoas.apelido_fantasia asc
                                    ") or die(mysql_error());
        $k=0;
        while ($rs_ced = mysql_fetch_object($result_ced)) {
        ?>
        <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cliente; ?>"><?= $rs_ced->apelido_fantasia; ?></option>
        <? $k++; } ?>
    </select>
    <?
	}
	
	if (isset($_GET["carregaClientesNoPercurso"])) {
	?>
    <select name="id_cliente[]" id="id_cliente_<?= $_GET["cont"];?>" title="Cliente">
        <?
        $result_ced= mysql_query("select *, pessoas.id_pessoa as id_cedente from pessoas, pessoas_tipos, tr_percursos_clientes
                                    where pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
                                    and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                    and   pessoas_tipos.tipo_pessoa = 'c'
									and   pessoas.id_cliente_tipo = '1'
									and   pessoas.id_pessoa = tr_percursos_clientes.id_cliente
									and   tr_percursos_clientes.id_percurso = '". $_GET["id_percurso"] ."'
                                    order by  pessoas.apelido_fantasia asc
                                    ") or die(mysql_error());
        $k=0;
        while ($rs_ced = mysql_fetch_object($result_ced)) {
        ?>
        <option  <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_ced->id_cedente; ?>" <? if ($rs_ced->id_cedente==$rs->local_origem) echo "selected=\"selected\""; ?>><?= $rs_ced->apelido_fantasia; ?></option>
        <? $k++; } ?>
    </select>
    <?
	}
	
	if (isset($_GET["transpPercursoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from tr_percursos, tr_percursos_passos
									where tr_percursos.id_percurso = '". $_GET["id_percurso"] ."'
									and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
									and   tr_percursos_passos.passo = '1'
									") or die(mysql_error());
		$rs_pre= mysql_fetch_object($result_pre);
		
		switch($rs_pre->tipo) {
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
		
		//primeiro precisa passar e arrumar para todos os clientes que estavam envolvidos, isso evita que clientes retirados fiquem ser alteração alguma
		$result_num_pre_cliente= mysql_query("select tr_percursos_clientes.* from tr_percursos, tr_percursos_clientes
												where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos.id_percurso = '". $_GET["id_percurso"] ."'
												
												$str_tipo
												
												and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". $rs_pre->data_percurso ."'
												order by tr_percursos.data_hora_percurso asc
												") or die("2: ". mysql_error());
		
		$result3= mysql_query("delete from tr_percursos_clientes
								where id_percurso= '". $_GET["id_percurso"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result3) $var++;
		
		
		//continuando o conserto…
		while ($rs_num_cliente= mysql_fetch_object($result_num_pre_cliente)) {
			
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
		
		$result1= mysql_query("delete from tr_percursos
								where id_percurso= '". $_GET["id_percurso"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from tr_percursos_passos
								where id_percurso= '". $_GET["id_percurso"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "transporte/percurso_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["vistoriaItemExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update tr_vistorias_itens
								set status_item = '2'
								where id_item = '". $_GET["id_item"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["vistoriaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from tr_vistorias
								where id_vistoria= '". $_GET["id_vistoria"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from tr_vistorias_itens_checklist
								where id_vistoria= '". $_GET["id_vistoria"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		$pagina= "transporte/vistoria_listar";
		require_once("index2.php");
	}

}

// ############################################### MANUTENÇÃO ###############################################

if (pode("jk", $_SESSION["permissao"])) {
	
	if (isset($_GET["servicoTipoExcluir"])) {
		$result_pre= mysql_query("select * from man_rms
									where id_servico_tipo= '". $_GET["id_servico_tipo"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from man_servicos_tipos
									where id_servico_tipo= '". $_GET["id_servico_tipo"] ."'
									limit 1
									");
		
		if ($result) $msg= 0;
		else $msg=1;
			
		echo excluido_ou_nao($msg);
			
		//$pagina= "op/equipamento_tipo_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["itemPesquisarUnico"])) {
				
		$result= mysql_query("select * from fi_itens, fi_estoque, fi_cc_ct
							where (fi_itens.item like '%". $_GET["pesquisa"] ."%'
									or fi_itens.apelidos like '%". $_GET["pesquisa"] ."%')
							and   fi_itens.id_item = fi_estoque.id_item
							and   fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
							and   fi_estoque.qtde_atual > '0'
							and   fi_cc_ct.id_centro_custo_tipo = fi_itens.id_centro_custo_tipo
							/* and   fi_cc_ct.id_centro_custo = '8' */
							order by fi_itens.item asc
							") or die("1: ". mysql_error());	
		
		//volta as solicitacoes em select
		if ($_GET["modo"]=="select") {
			echo "<select name=\"id_item\" id=\"id_item\" onchange=\"processaDecimalUnico();\">";
			
			if (mysql_num_rows($result)==0)
				echo "<option value=\"\">Nenhum registro encontrado!</li>";
			else {
				echo "<option value=\"\">---</li>";
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$qtde_atual= pega_qtde_atual_item($_SESSION["id_empresa"], $rs->id_item);
					$qtde_atual= fnumf($qtde_atual);
					
					if (($i%2)==1) $classe= "cor_sim";
					else $classe= "cor_nao";
					
					$var= "<option class=\"". $classe ."\" value=\"". $rs->id_item ."\">". $rs->item ." - ". $qtde_atual ." ". pega_tipo_apres($rs->tipo_apres) ."</option>";
																				
					echo $var;
					$i++;
				}
			}
			echo "</select>";
		}
		
		//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario"], $_SESSION["id_empresa"], 1, "pesquisa produto na telinha, termo: ". $_GET["pesquisa"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
	}
	
	if (isset($_GET["tecnicoManutencaoStatus"])) {
		$result= mysql_query("update man_tecnicos set status_tecnico= '". $_GET["status"] ."'
								where id_tecnico = '". $_GET["id_tecnico"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "manutencao/tecnico_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["iniciaRMServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("insert into man_rms_servicos
								(id_empresa, id_funcionario, id_rm, data_inicio, hora_inicio, id_usuario)
								values
								('". $_SESSION["id_empresa"] ."', '". $_SESSION["id_funcionario_sessao"] ."', '". $_GET["id_rm"] ."',
								'". formata_data($_GET["data_inicio"]) ."', '". $_GET["hora_inicio"] ."', '". $_SESSION["id_usuario"] ."' )
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_rm= $_GET["id_rm"];
		require_once("_manutencao/__rm_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["finalizaRMServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_rms_servicos set
							    data_fim = '". formata_data($_GET["data_fim"]) ."',
								hora_fim = '". $_GET["hora_fim"] ."'
								where id_rm= '". $_GET["id_rm"] ."'
								and   id_rm_servico= '". $_GET["id_rm_servico"] ."'
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_rm= $_GET["id_rm"];
		require_once("_manutencao/__rm_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["iniciaOSServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("insert into man_oss_servicos
								(id_empresa, id_funcionario, id_os, data_inicio, hora_inicio, id_usuario)
								values
								('". $_SESSION["id_empresa"] ."', '". $_SESSION["id_funcionario_sessao"] ."', '". $_GET["id_os"] ."',
								'". formata_data($_GET["data_inicio"]) ."', '". $_GET["hora_inicio"] ."', '". $_SESSION["id_usuario"] ."' )
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_rm= $_GET["id_rm"];
		require_once("_manutencao/__os_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["finalizaOSServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_oss_servicos set
							    data_fim = '". formata_data($_GET["data_fim"]) ."',
								hora_fim = '". $_GET["hora_fim"] ."'
								where id_os= '". $_GET["id_os"] ."'
								and   id_os_servico= '". $_GET["id_os_servico"] ."'
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_os= $_GET["id_os"];
		require_once("_manutencao/__os_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["avaliaRM"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_rms_andamento set
								nota = '". $_GET["nota"] ."'
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_rm_andamento = '". $_GET["id_rm_andamento"] ."'
								");
		if (!$result1) $var++;
		
		//se for qualificado como baixo, reabrir a RM
		if ($_GET["nota"]==0) {
			
			//$descricao_nota= pega_descricao_nota($_GET["nota"]);
			$descricao_nota= pega_descricao_contento($_GET["nota"]);
			
			$descricao_nota= explode("@", $descricao_nota);
			
			$result2= mysql_query("insert into man_rms_andamento (id_empresa, id_rm, id_situacao, data_rm_andamento, hora_rm_andamento, obs, id_usuario)
												   values
									('". $_SESSION["id_empresa"] ."', '". $_GET["id_rm"] ."', '6',
									'". date("Ymd") ."', '". date("H:i:s") ."',
									'RM reaberta por baixa avaliação do serviço executado (<span class=\"". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>)',
									'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result2) $var++;
		}
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=manutencao/rm&acao=e&id_rm=". $_GET["id_rm"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["avaliaOS"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_oss_andamento set
								nota = '". $_GET["nota"] ."'
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_os_andamento = '". $_GET["id_os_andamento"] ."'
								");
		if (!$result1) $var++;
		
		//se for qualificado como baixo, reabrir a RM
		if ($_GET["nota"]==0) {
			
			//$descricao_nota= pega_descricao_nota($_GET["nota"]);
			$descricao_nota= pega_descricao_contento($_GET["nota"]);
			
			$descricao_nota= explode("@", $descricao_nota);
			
			$result2= mysql_query("insert into man_oss_andamento (id_empresa, id_os, id_situacao, data_os_andamento, hora_os_andamento, obs, id_usuario)
												   values
									('". $_SESSION["id_empresa"] ."', '". $_GET["id_os"] ."', '6',
									'". date("Ymd") ."', '". date("H:i:s") ."',
									'OS reaberta por baixa avaliação do serviço executado (<span class=\"". $descricao_nota[1] ."\">". $descricao_nota[0] ."</span>)',
									'". $_SESSION["id_usuario"] ."' ) ") or die(mysql_error());
			if (!$result2) $var++;
		}
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=manutencao/os&acao=e&id_os=". $_GET["id_os"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["excluiRMEstoque"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from fi_estoque_mov
								 	where id_rm = '". $_GET["id_rm"] ."'
									and   id_mov = '". $_GET["id_mov"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									limit 1
								");
		if (!$result_pre) $var++;
		$rs_pre= mysql_fetch_object($result_pre);
		
		$result1= mysql_query("delete from fi_estoque_mov
								where id_rm = '". $_GET["id_rm"] ."'
								and   id_mov = '". $_GET["id_mov"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("update fi_estoque set
								qtde_atual = qtde_atual + '". $rs_pre->qtde ."'
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_item= '". $rs_pre->id_item."'
								") or die(mysql_error());
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from fi_custos
								where id_mov = '". $_GET["id_mov"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result3) $var++;
		
		//pegar os itens/valores exatos deste produto e retornar como era antes
		
		$result_iv= mysql_query("select * from fi_estoque_iv_iteracoes
									where id_mov = '". $_GET["id_mov"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		while ($rs_iv= mysql_fetch_object($result_iv)) {
			$result4= mysql_query("update fi_estoque_iv
									set qtde = qtde + '". $rs_iv->qtde_removida ."'
									where id_iv= '". $rs_iv->id_iv ."'
									");
			if (!$result4) $var++;
		}
		
		$result_exclui_iv= mysql_query("delete from fi_estoque_iv_iteracoes
										where id_mov = '". $_GET["id_mov"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
		if (!$result_exclui_iv) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_rm= $_GET["id_rm"];
		require_once("_manutencao/__rm_estoque_listar.php");
	}
	
	if (isset($_GET["excluiOSEstoque"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from fi_estoque_mov
								 	where id_os = '". $_GET["id_os"] ."'
									and   id_mov = '". $_GET["id_mov"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									limit 1
								");
		if (!$result_pre) $var++;
		$rs_pre= mysql_fetch_object($result_pre);
		
		$result1= mysql_query("delete from fi_estoque_mov
								where id_os = '". $_GET["id_os"] ."'
								and   id_mov = '". $_GET["id_mov"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("update fi_estoque set
								qtde_atual = qtde_atual + '". $rs_pre->qtde ."'
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_item= '". $rs_pre->id_item ."'
								") or die(mysql_error());
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from fi_custos
								where id_mov = '". $_GET["id_mov"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result3) $var++;
		
		//pegar os itens/valores exatos deste produto e retornar como era antes
		
		$result_iv= mysql_query("select * from fi_estoque_iv_iteracoes
									where id_mov = '". $_GET["id_mov"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		while ($rs_iv= mysql_fetch_object($result_iv)) {
			$result4= mysql_query("update fi_estoque_iv
									set qtde = qtde + '". $rs_iv->qtde_removida ."'
									
									/* set qtde = qtde + '". $rs_iv->qtde ."' */
									
									where id_iv= '". $rs_iv->id_iv ."'
									");
			if (!$result4) $var++;
		}
		
		$result_exclui_iv= mysql_query("delete from fi_estoque_iv_iteracoes
										where id_mov = '". $_GET["id_mov"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
		if (!$result_exclui_iv) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_os= $_GET["id_os"];
		require_once("_manutencao/__os_estoque_listar.php");
	}
	
	if (isset($_GET["excluiRMServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from man_rms_servicos
								where id_rm = '". $_GET["id_rm"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_rm_servico = '". $_GET["id_rm_servico"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_rm= $_GET["id_rm"];
		require_once("_manutencao/__rm_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["excluiOSServico"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from man_oss_servicos
								where id_os = '". $_GET["id_os"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_os_servico = '". $_GET["id_os_servico"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$id_os= $_GET["id_os"];
		require_once("_manutencao/__os_tempo_trabalho_listar.php");
	}
	
	if (isset($_GET["rmAndamentoNotaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_rms_andamento
								set nota= NULL
								where id_rm = '". $_GET["id_rm"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_rm_andamento = '". $_GET["id_rm_andamento"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=manutencao/rm&acao=e&id_rm=". $_GET["id_rm"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["rmAndamentoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from man_rms_andamento
								where id_rm = '". $_GET["id_rm"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_rm_andamento = '". $_GET["id_rm_andamento"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		header("location: ./?pagina=manutencao/rm&acao=e&id_rm=". $_GET["id_rm"] ."&msg=". $msg ."#situacao");
	}
	
	if (isset($_GET["osExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_oss
								set status_os = '0'
								where id_os= '". $_GET["id_os"] ."'
								/* and   id_usuario= '". $_SESSION["id_usuario"] ."' */
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result1) $var++;
		
		/*
		$result2= mysql_query("delete from man_oss_servicos
								where id_os= '". $_GET["id_os"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from man_oss_lidas
								where id_os= '". $_GET["id_os"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result3) $var++;
		*/
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		echo excluido_ou_nao($msg);
	}
	
	if (isset($_GET["rmExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("update man_rms
								set status_rm= '0'
								where id_rm= '". $_GET["id_rm"] ."'
								
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result1) $var++;
		
		/*
		$result2= mysql_query("delete from man_rms_andamento
								where id_rm= '". $_GET["id_rm"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from man_rms_lidas
								where id_rm= '". $_GET["id_rm"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result3) $var++;
		
		$result4= mysql_query("delete from man_rms_servicos
								where id_rm= '". $_GET["id_rm"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."' ") or die(mysql_error());
		if (!$result4) $var++;
		*/
		
		finaliza_transacao($var);
		
		$msg= $var;
		$pagina= "manutencao/rm_listar";
		require_once("index2.php");
	}
	
}

// ############################################### MENSAGENS ###############################################

if (pode("n", $_SESSION["permissao"])) {

	if (isset($_GET["mensagemExcluir"])) {
		$id_pessoa= pega_id_pessoa_do_usuario($_SESSION["id_usuario"]);
		
		if ($_GET["tipo"]=="e") $result= mysql_query("update com_mensagens set situacao_de= '0'
													 	where id_mensagem= '". $_GET["id_mensagem"] ."'
														and   de= '". $id_pessoa ."'") or die(mysql_error());
		if ($_GET["tipo"]=="r") $result= mysql_query("update com_mensagens set situacao_para= '0'
													 	where id_mensagem= '". $_GET["id_mensagem"] ."'
														and   para= '". $id_pessoa ."'") or die(mysql_error());
		if ($result) $var=0; else $var=1;
		
		$msg= $var;
		
		header("location: ./?pagina=com/mensagem_listar&tipo=". $_GET["tipo"] ."&msg=". $msg);
	}

}

// ######################################### EMISSÃO DE DOCUMENTOS #########################################

if (pode("c3", $_SESSION["permissao"])) {

	if (isset($_GET["documentoEmissaoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from dc_documentos_emissoes
								where id_documento_emissao = '". $_GET["id_documento_emissao"] ."'
								and   tipo = '". $_GET["tipo"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "dc/documento_emissao_listar";
		require_once("index2.php");
	}
}

// ######################################### ARQUIVO DE DOCUMENTOS #########################################

if (pode("d5", $_SESSION["permissao"])) {

	if (isset($_GET["documentoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from dc_documentos
								where id_documento = '". $_GET["id_documento"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "dc/documento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["carregaPastasdoDepto"])) {
		if ($_GET[id_departamento]!="") {
		?>
        <select name="id_pasta" id="id_pasta" title="Pasta">
            <option value="">-</option>
			<?
            $result_cc2= mysql_query("select *
                                        from  dc_documentos_pastas
                                        where id_departamento = '". $_GET["id_departamento"] ."'
                                        order by pasta asc
                                        ") or die(mysql_error());
            $i=0;
            while ($rs_cc2= mysql_fetch_object($result_cc2)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; else echo "class=\"cor_nao\""; ?> value="<?= $rs_cc2->id_pasta; ?>" <? if ($_GET["pasta"]==$rs_cc2->pasta) echo "selected=\"selected\""; ?>><?= $rs_cc2->pasta ." - ". $rs_cc2->nome_pasta ." (". ativo_inativo($rs_cc2->status_pasta) .")"; ?></option>
            <? $i++; } ?>
        </select>
        <?
        }
        else {
        ?>
        <select name="id_pasta" id="id_pasta" title="Pasta">
            <option value="">-</option>
            <?
            $result_emp= mysql_query("select * from pessoas, pessoas_tipos, empresas
                                        where pessoas.id_pessoa = empresas.id_pessoa
                                        and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
                                        and   pessoas_tipos.tipo_pessoa = 'a'
                                        order by 
                                        pessoas.nome_rz asc");
            while ($rs_emp= mysql_fetch_object($result_emp)) {
            ?>
            <optgroup class="opt1" label="<?= $rs_emp->apelido_fantasia; ?>">
                <?
                $result_cc= mysql_query("select *
                                            from  rh_departamentos
                                            where id_empresa = '". $rs_emp->id_empresa ."'
                                            order by departamento asc
                                            ") or die(mysql_error());
                while ($rs_cc= mysql_fetch_object($result_cc)) {
                ?>
                <optgroup class="opt2" label="<?= $rs_cc->departamento; ?>">
                    <?
                    $result_cc2= mysql_query("select *
                                                from  dc_documentos_pastas
                                                where /* id_empresa = '". $_SESSION["id_empresa"] ."'
                                                and   */ id_departamento = '". $rs_cc->id_departamento ."'
                                                order by pasta asc
                                                ") or die(mysql_error());
                    $i=0;
                    while ($rs_cc2= mysql_fetch_object($result_cc2)) {
                    ?>
                    <option <? if ($i%2==0) echo "class=\"cor_sim\""; else echo "class=\"cor_nao\""; ?> value="<?= $rs_cc2->id_pasta; ?>" <? if (($rs_cc2->id_pasta==$rs->id_pasta) || ($rs_cc2->id_pasta==$_GET["id_pasta"])) echo "selected=\"selected\""; ?>><?= $rs_cc2->pasta ." - ". $rs_cc2->nome_pasta ." (". ativo_inativo($rs_cc2->status_pasta) .")"; ?></option>
                    <? $i++; } ?>
                </optgroup>
                <? } ?>
            </optgroup>
            <? } ?>
        </select>
        <?
        }
	}
	
	if (isset($_GET["documentoPastaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from dc_documentos
										where id_pasta = '". $_GET["id_pasta"] ."'
										");

		if (mysql_num_rows($result_pre)==0) {
			$result1= mysql_query("delete from dc_documentos_pastas
									where id_pasta = '". $_GET["id_pasta"] ."'
									limit 1
									");
			if (!$result1) $var++;
		} else $var++;
		
		finaliza_transacao($var);
		
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["documentoPastaStatus"])) {
		$result= mysql_query("update dc_documentos_pastas set status_pasta= '". $_GET["status"] ."'
								where id_pasta = '". $_GET["id_pasta"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "dc/documento_pasta_listar";
		require_once("index2.php");
	}
}

// ############################################### AUTORIZAÇÕES ###############################################

if (pode("u", $_SESSION["permissao"])) {

	if (isset($_GET["abastecimentoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from fi_abastecimentos
								where id_abastecimento= '". $_GET["id_abastecimento"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "financeiro/abastecimento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["refeicaoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from fi_refeicoes
								where id_refeicao= '". $_GET["id_refeicao"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "financeiro/refeicao_listar";
		require_once("index2.php");
	}
}

// ############################################### TELEFONE ###############################################

if (pode("t", $_SESSION["permissao"])) {

	if (isset($_GET["contatoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from tel_contatos
								where id_contato= '". $_GET["id_contato"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from tel_contatos_telefones
								where id_contato= '". $_GET["id_contato"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result2) $var++;
		
		$letra= substr($_GET["nome"], 0, 1);
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "contatos/contato_esquema";
		require_once("index2.php");
	}
	
	if (isset($_GET["ligacaoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from tel_contatos_ligacoes
								where id_ligacao= '". $_GET["id_ligacao"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result1) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "contatos/ligacao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["verificaTelefone"])) {
		$var=0;
		
		$result_pre= mysql_query("select * from tel_contatos_telefones
										where telefone = '". $_GET["telefone"] ."'
										");

		if (mysql_num_rows($result_pre)>0) {
			$rs_pre= mysql_fetch_object($result_pre);
			
			$result= mysql_query("select * from tel_contatos
										where id_contato = '". $rs_pre->id_contato ."'
										");
			$rs= mysql_fetch_object($result);
		}
		
		echo "<input title=\"Para\" name=\"para\" id=\"para\" value=\"". $rs->nome ."\" />";
	}
}

// ############################################### QUALIDADE ###############################################

if (pode("12(", $_SESSION["permissao"])) {
	
	if (isset($_GET["costuraConsertoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from op_limpa_costura_consertos
								where id_costura_conserto= '". $_GET["id_costura_conserto"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from op_limpa_costura_consertos_pecas
								where id_costura_conserto= '". $_GET["id_costura_conserto"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "qualidade/costura_conserto_listar";
		require_once("index2.php");
	}
	
}

// ############################################### OPERACIONAL ###############################################

if (pode("psl(", $_SESSION["permissao"])) {
	
	if (isset($_GET["desconsideraProducaoDia"])) {
		
		if ($_GET["desconsidera"]=="1") {
			if ($_GET["t"]=="0")
				$mensagem= "O plantão do dia ". desformata_data($_GET["data"]) ." foi desconsiderado da produção!";
			else
				$mensagem= "O dia ". desformata_data($_GET["data"]) ."/". pega_turno_padrao($_GET["t"]) ." foi desconsiderado da produção!";
			
			$sql= "insert into rh_ponto_producao_desconsiderados
					(id_empresa, id_departamento, id_turno_index, vale_dia, desconsiderado, id_usuario)
					values
					('". $_SESSION["id_empresa"] ."', '". $_GET["id_departamento"] ."', '". $_GET["t"] ."', '". $_GET["data"] ."', '1', '". $_SESSION["id_usuario"] ."')
					";
		}
		else {
			$mensagem= "O dia ". desformata_data($_GET["data"]) ." foi devolvido à produção!";
			
			$sql= "delete from rh_ponto_producao_desconsiderados
					where id_empresa = '". $_SESSION["id_empresa"] ."'
					and   id_departamento = '". $_GET["id_departamento"] ."'
					and   id_turno_index = '". $_GET["t"] ."'
					and   vale_dia = '". $_GET["data"] ."'
					";
		}
					
		$result= mysql_query($sql) or die(mysql_error());
		
		echo "<script language=\"javascript\">alert('". $mensagem ."');</script>";
	}
	
	if (isset($_GET["checaDeschecaPeso"])) {
		$result= mysql_query("select * from pessoas, fi_contratos
							 	where pessoas.id_pessoa = '". $_GET["id_cliente"] ."'
								and   fi_contratos.id_contrato = pessoas.id_contrato
								");
		$rs= mysql_fetch_object($result);
		
		if ($rs->peso_nota==1) $acao_checagem="true";
		else $acao_checagem="false";
		
		echo "<script language=\"javascript\">g('mostrar_peso". $_GET["local"] ."').checked=". $acao_checagem ."; g('mostrar_peso". $_GET["local"] ."').disabled=". $acao_checagem .";</script>";
	}
	
	if (isset($_GET["verificaGrupoRoupa"])) {
		$id_grupo= pega_id_grupo_da_peca($_GET["id_peca"]);
		
		echo "<script language=\"javascript\">corrigeGrupoRoupa('". $id_grupo ."');</script>";
	}
	
	if (isset($_GET["pegaTipoRoupa"])) {
		
		if ($_GET["todas"]!="1") {
			if ($_GET["cont"]>1) $str_aqui= "and   id_grupo = '2' ";
			$k= $_GET["cont"]-1;
		}
		?>
		<select class="tamanho160 espaco_dir" id="id_tipo_roupa_<?=$_GET["cont"];?>" name="id_tipo_roupa[]" title="Tipo de roupa">
			<option value="">-</option>
			<?
            $result_pecas= mysql_query("select * from op_limpa_pecas
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
                                        and   status_peca = '1'
										$str_aqui
                                        order by peca asc
                                        ");
            $j=1;
            while ($rs_peca= mysql_fetch_object($result_pecas)) {
            ?>
            <option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_peca->id_peca; ?>" <? if ($rs_peca->id_peca==$rs->id_tipo_roupa) echo "selected=\"selected\""; ?>><?= $rs_peca->peca; ?></option>
            <? $j++; } ?>
		</select>
        
        <script language="javascript">
			daFoco("id_tipo_roupa_<?= $_GET["cont"]; ?>");
		</script>
		<?
	}
	
	if (isset($_GET["pegaMotivosCostura"])) {
		?>
		<select class="tamanho120 espaco_dir" id="id_motivo_costura_<?=$_GET["cont"];?>" name="id_motivo_costura[]" title="Motivo">
			<option value="">-</option> 
			<?
            $vetor= pega_motivo_costura('l');
            $c=1;
            while ($vetor[$c]) {
            ?>
            <option <? if ($c%2==0) echo "class=\"cor_sim\""; ?> value="<?=$c;?>"><?= $vetor[$c]; ?></option>
            <? $c++; } ?>
		</select>
        
        <script language="javascript">
			daFoco("id_motivo_costura_<?= $_GET["cont"]; ?>");
		</script>
		<?
	}
	
	if (isset($_GET["pedidoExcluir"])) {
		/*$result_pre= mysql_query("select * from op_suja_lavagem
									where id_equipamento= '". $_GET["id_equipamento"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)*/
			$result= mysql_query("delete from op_pedidos
									where id_pedido= '". $_GET["id_pedido"] ."'
									limit 1
									");
		if ($result) $msg= 0;
		else $msg=1;
		
		echo excluido_ou_nao($var);
		
		//$pagina= "op/pedido_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["procuraPercursos"])) {
		
		if ($_GET[local]=="2") $soma_dia=0;
		else $soma_dia=1;
		
		$data_procura= soma_data($_GET["data"], $soma_dia, 0, 0);
		
		$result_percurso= mysql_query("select *, DATE_FORMAT(tr_percursos.data_hora_percurso, '%d/%m/%Y') as data_percurso2,
										DATE_FORMAT(tr_percursos.data_hora_percurso, '%H:%i:%s') as hora_percurso2
										from tr_percursos, tr_percursos_clientes
										where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
										and   (tr_percursos.tipo = '2' or tr_percursos.tipo = '5')
										and   tr_percursos_clientes.id_cliente = '". $_GET["id_cliente"] ."'
										and   DATE_FORMAT(tr_percursos.data_hora_percurso, '%Y-%m-%d') = '". formata_data_hifen($data_procura) ."'
										order by tr_percursos.data_hora_percurso asc
										") or die(mysql_error());
		
		echo "<label for=\"num_percurso\">Entrega:</label>";
		
		if (mysql_num_rows($result_percurso)==0) echo "Nenhum percurso de entrega neste dia. <br /><br />";
		else {
	?>
    <select name="id_percurso" id="id_percurso" title="Entrega" onchange="checaPercursoExtra('<?=$_GET[local];?>', this.value);">
        <option value="">-</option>
        <?
        $k=0;
        while ($rs_percurso= mysql_fetch_object($result_percurso)) {
        ?>
        <option <? if ($k%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_percurso->id_percurso; ?>"><?= pega_coleta_entrega($rs_percurso->tipo) ." nº". $rs_percurso->num_percurso_tipo ." (". $rs_percurso->data_percurso2 ." ". substr($rs_percurso->hora_percurso2, 0, 5) .")"; ?></option>
        <? $k++; } ?>
    </select>
    <?
		}
	}
	
	if (isset($_GET["checaPercursoExtra"])) {
		$result_percurso= mysql_query("select *
										from tr_percursos
										where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_percursos.id_percurso = '". $_GET["id_percurso"] ."'
										limit 1
										") or die(mysql_error());
		
		$rs_percurso= mysql_fetch_object($result_percurso);
		
		//se for extra, abre a janela para escolher a denominação
		if ($rs_percurso->tipo=="5") {
			?>
			<script language="javascript">abreDiv('denominacao_<?=$_GET[local];?>');</script>
			<?
		}
		else {
			?>
			<script language="javascript">fechaDiv('denominacao_<?=$_GET[local];?>');</script>
			<?
		}
		
	}
	
	if (isset($_GET["iniciaSeparacaoRemessa"])) {
		
		$var=0;
		
		$result_insere= mysql_query("insert into op_suja_remessas_separacoes
									(id_empresa, id_remessa, data_separacao, hora_separacao, tipo_separacao, id_usuario)
									values
									('". $_SESSION["id_empresa"] ."', '". $_GET["id_remessa"] ."',
									'". date("Y-m-d") ."', '". date("H:i:s") ."', '1', '". $_SESSION["id_usuario"] ."')
									") or die(mysql_error());
		if (!$result_insere) $var++;
		
		$pagina="op/separacao_listar";
		require_once("index2.php");
		
	}
	
	if (isset($_GET["finalizaSeparacaoRemessa"])) {
		
		$var=0;
		
		$result_insere= mysql_query("insert into op_suja_remessas_separacoes
									(id_empresa, id_remessa, data_separacao, hora_separacao, tipo_separacao, id_separacao_fecha, id_usuario)
									values
									('". $_SESSION["id_empresa"] ."', '". $_GET["id_remessa"] ."',
									'". date("Y-m-d") ."', '". date("H:i:s") ."', '0', '". $_GET["id_separacao"] ."', '". $_SESSION["id_usuario"] ."')
									") or die(mysql_error());
		if (!$result_insere) $var++;
		
		$pagina="op/separacao_listar";
		require_once("index2.php");
		
	}
	
	if (isset($_GET["apagaSeparacaoRemessa"])) {
		
		$result_insere= mysql_query("delete from op_suja_remessas_separacoes
										where id_separacao = '". $_GET["id_separacao"] ."'
										or    id_separacao_fecha = '". $_GET["id_separacao"] ."'
									") or die(mysql_error());
		
		header("location: ./?pagina=op/separacao_listar");
		
	}
	
	if (isset($_GET["buscaRemessasDoDia"])) {
		
		/*$result_remessas= mysql_query("select * from op_suja_remessas
										where data_remessa= '". formata_data($_GET["data"]) ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										order by num_remessa asc
										");
		
		if (mysql_num_rows($result_remessas)==0)
			echo "Nenhuma remessa neste dia.";
		else {
			?>
            <table cellspacing="0">
            	<tr>
                	<th width="25%">Núm.</th>
                    <th width="75%" align="left">Clientes</th>
                </tr>
                <?
                while ($rs_remessas= mysql_fetch_object($result_remessas)) {
					$result_clientes= mysql_query("select distinct(op_suja_pesagem.id_cliente) from op_suja_pesagem, op_suja_remessas
												   where op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
												   and   op_suja_remessas.id_remessa = '". $rs_remessas->id_remessa ."'
												");
					$clientes= "";
					$linhas_clientes= mysql_num_rows($result_clientes);
					
					if ($linhas_clientes>0) {
						while ($rs_clientes= mysql_fetch_object($result_clientes)) {
							$clientes .= "- ". pega_sigla_pessoa($rs_clientes->id_cliente) ."; <br />";
						}
					}
				?>
				<tr>
                	<td valign="top" align="center"><?= $rs_remessas->num_remessa;?></td>
                    <td valign="top" class="menor"><?=$clientes;?></td>
	            </tr>
                <? } ?>
            </table>
            <?
		}
		*/
		
		$result_remessas= mysql_query("select * from op_suja_remessas
										where data_remessa= '". formata_data($_GET["data"]) ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										order by num_remessa asc
										");
		
		if (mysql_num_rows($result_remessas)==0)
			echo "Nenhuma remessa neste dia.";
		else {
			?>
            <table cellspacing="0">
            	<tr class="menor">
                	<th width="15%">Nº</th>
                    <th width="20%">Motorista</th>
                    <th width="65%" align="left">Clientes</th>
                </tr>
                <?
                while ($rs_remessas= mysql_fetch_object($result_remessas)) {
					$result_clientes= mysql_query("select tr_percursos_clientes.id_cliente, tr_percursos.id_motorista
												   from tr_percursos_clientes, tr_percursos, op_suja_remessas
												   where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												   and   tr_percursos.id_percurso = op_suja_remessas.id_percurso
												   and   op_suja_remessas.id_remessa = '". $rs_remessas->id_remessa ."'
												");
					$clientes= "";
					$motorista= "";
					
					$linhas_clientes= mysql_num_rows($result_clientes);
					
					if ($linhas_clientes>0) {
						$i=0;
						while ($rs_clientes= mysql_fetch_object($result_clientes)) {
							$clientes .= "(<strong>". pega_codigo_do_cliente($rs_clientes->id_cliente) ."</strong>) ". pega_sigla_pessoa($rs_clientes->id_cliente) ."; <br />";
							
							if ($i==0) $motorista= primeira_palavra(pega_funcionario($rs_clientes->id_motorista));
							
							$i++;
						}
						
						
					}
				?>
				<tr>
                	<td valign="top" align="center"><?= $rs_remessas->num_remessa;?></td>
                    <td valign="top" align="center" class="menor"><?= $motorista;?></td>
                    <td valign="top" class="menor"><?=$clientes;?></td>
	            </tr>
                <? } ?>
            </table>
            <?
		}
		
	}
	
	if (isset($_GET["carregaPesagensSuja"])) {
		
		$id_cliente= pega_id_cliente_pelo_codigo($_GET["cliente"]);
		
		echo "<strong>". pega_pessoa($id_cliente) ."</strong><br /><br />";
		
		for ($i=-1; $i<1; $i++) {
			$j= $i+1;
			
			$data_aqui= soma_data($_GET["data"], $i, 0, 0);
			?>
            
            <table cellspacing="0">
                <tr>
                    <th colspan="2" align="left"><?= $data_aqui; ?></th>
                </tr>
            
            <?	
			$result_remessas= mysql_query("select * from tr_percursos, tr_percursos_clientes, tr_percursos_passos, op_suja_remessas
											where tr_percursos.id_percurso = op_suja_remessas.id_percurso
											and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
											and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
											and   tr_percursos_passos.id_percurso = tr_percursos.id_percurso
											and   tr_percursos_passos.passo = '1'
											and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
											and   op_suja_remessas.data_remessa = '". formata_data($data_aqui) ."'
											order by tr_percursos.data_hora_percurso asc
											");
			
			$r= 1;
            while($rs_remessas= mysql_fetch_object($result_remessas)) {
                $result_peso= mysql_query("select sum(op_suja_pesagem.peso) as peso_total from op_suja_pesagem
                                            where op_suja_pesagem.id_remessa = '". $rs_remessas->id_remessa ."'
											and   id_cliente = '". $id_cliente ."'
                                            ");
				$rs_peso= mysql_fetch_object($result_peso);
            ?>
                <tr>
                    <td valign="top"><strong><?= $r; ?></strong></td>
                    <td valign="top"><?= fnum($rs_peso->peso_total);?> kg</td>
                </tr>
            <?
            	$r++;
			}
			
			$result_peso_remessa= mysql_query("select sum(op_suja_pesagem.peso) as peso_total from op_suja_pesagem, op_suja_remessas
												where op_suja_pesagem.id_remessa = op_suja_remessas.id_remessa
												and   op_suja_remessas.data_remessa = '". formata_data($data_aqui) ."'
												and   op_suja_pesagem.id_cliente = '". $id_cliente ."'
												");
			$rs_peso_remessa= mysql_fetch_object($result_peso_remessa);
			?>
            	<tr>
                    <td valign="top"><strong>=</strong></td>
                    <td valign="top"><?= fnum($rs_peso_remessa->peso_total);?> kg</td>
                </tr>
            </table>
            <br />
            <?
		}//fim for
		
		//pegar a ultima entrega efetuada
		$result_entrega_ultima= mysql_query("select * from tr_percursos, tr_percursos_passos, tr_percursos_clientes
												where tr_percursos.tipo = '2'
												and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
												and   tr_percursos_passos.passo = '1'
												and   tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												and   tr_percursos_clientes.id_cliente = '". $id_cliente ."'
												order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
												limit 1
												");
		$rs_entrega_ultima= mysql_fetch_object($result_entrega_ultima);
		
		$result_peso_proxima= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem
											where op_limpa_pesagem.data_hora_pesagem > '". $rs_entrega_ultima->data_percurso ." ". $rs_entrega_ultima->hora_percurso ."'
											and   op_limpa_pesagem.data_hora_pesagem < '". date("Y-m-d H:i:s") ."'
											and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
											and   op_limpa_pesagem.extra = '0'
											");
		$rs_peso_proxima= mysql_fetch_object($result_peso_proxima);
		
		$result_peso_proxima_extra= mysql_query("select sum(op_limpa_pesagem.peso) as peso_total from op_limpa_pesagem
											where op_limpa_pesagem.data_hora_pesagem > '". $rs_entrega_ultima->data_percurso ." ". $rs_entrega_ultima->hora_percurso ."'
											and   op_limpa_pesagem.data_hora_pesagem < '". date("Y-m-d H:i:s") ."'
											and   op_limpa_pesagem.id_cliente = '". $id_cliente ."'
											and   op_limpa_pesagem.extra = '1'
											");
		$rs_peso_proxima_extra= mysql_fetch_object($result_peso_proxima_extra);
		?>
        <table cellspacing="0">
            <tr>
                <th colspan="2" align="left">A entregar</th>
            </tr>
            <tr>
                <td valign="top"><strong>=</strong></td>
                <td valign="top"><?= fnum($rs_peso_proxima->peso_total-$rs_peso_proxima_extra->peso_total); ?> kg</td>
            </tr>
        </table>
        <?
	}
	
	if (isset($_GET["equipamentoExcluir"])) {
		$result_pre1= mysql_query("select * from op_suja_lavagem
									where id_equipamento= '". $_GET["id_equipamento"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		$result_pre2= mysql_query("select * from man_rms
									where id_equipamento= '". $_GET["id_equipamento"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if ((mysql_num_rows($result_pre1)==0) && (mysql_num_rows($result_pre2)==0))
			$result= mysql_query("delete from op_equipamentos
									where id_equipamento= '". $_GET["id_equipamento"] ."'
									limit 1
									");
		if ($result) $msg= 0;
		else $msg=1;
		
		echo excluido_ou_nao($msg);
		
		//$pagina= "op/equipamento_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["equipamentoTipoExcluir"])) {
		$result_pre= mysql_query("select * from op_equipamentos
									where tipo_equipamento= '". $_GET["id_equipamento_tipo"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from op_equipamentos_tipos
									where id_equipamento_tipo= '". $_GET["id_equipamento_tipo"] ."'
									limit 1
									");
		
		if ($result) $msg= 0;
		else $msg=1;
			
		echo excluido_ou_nao($msg);
			
		//$pagina= "op/equipamento_tipo_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["pecaExcluir"])) {
		$result_pre= mysql_query("select * from op_limpa_pesagem_pecas
									where id_tipo_roupa= '". $_GET["id_peca"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									") or die(mysql_error());
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from op_limpa_pecas
									where id_peca= '". $_GET["id_peca"] ."'
									limit 1
									");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/peca_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pecaStatus"])) {
		$result= mysql_query("update op_limpa_pecas set status_peca= '". $_GET["status"] ."'
								where id_peca= '". $_GET["id_peca"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
		
		$pagina= "op/peca_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["veiculoStatus"])) {
		$result= mysql_query("update op_veiculos set status_veiculo= '". $_GET["status"] ."'
								where id_veiculo = '". $_GET["id_veiculo"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
		
		$pagina= "op/veiculo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["veiculoExcluir"])) {
		$result_pre= mysql_query("select * from op_suja_remessas
									where id_veiculo= '". $_GET["id_veiculo"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from op_veiculos
									where id_veiculo= '". $_GET["id_veiculo"] ."'
									limit 1
									");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/veiculo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["processoExcluir"])) {
		/*$result_pre= mysql_query("select * from op_suja_lavagem
									where id_processo= '". $_GET["id_processo"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)*/
		
			$result= mysql_query("update op_equipamentos_processos
									set status_processo = '0'
									where id_processo= '". $_GET["id_processo"] ."'
									") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
		
		$var= $msg;
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["reclamacaoCausaExcluir"])) {
		/* ? */
	}
	
	if (isset($_GET["acompanhamentoItemExcluir"])) {
		/*$result_pre= mysql_query("select * from op_suja_lavagem
									where id_processo= '". $_GET["id_processo"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)*/
		
			$result= mysql_query("update op_acompanhamento_itens
									set status_item = '0'
									where id_acompanhamento_item= '". $_GET["id_acompanhamento_item"] ."'
									") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
		
		$var= $msg;
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["remessaExcluir"])) {
		$result_pre= mysql_query("select * from op_suja_pesagem
									where id_remessa= '". $_GET["id_remessa"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from op_suja_remessas
									where id_remessa= '". $_GET["id_remessa"] ."'
									limit 1
									");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/remessa_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pegaVeiculo"])) {
		$result= mysql_query("select * from op_veiculos
								where codigo= '". $_GET["codigo"] ."'
								and   status_veiculo = '1'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		
		if (mysql_num_rows($result)>0) {
			$rs= mysql_fetch_object($result);
			echo $rs->veiculo;
			echo "<input id=\"id_veiculo\" name=\"id_veiculo\" value=\"". $rs->id_veiculo ."\" title=\"Veículo\" class=\"escondido\" />";
		}
		else echo "Não localizado! <input id=\"id_veiculo\" name=\"id_veiculo\" value=\"\" title=\"Veículo\" class=\"escondido\" />";
		
	}
	
	if (isset($_GET["pegaPercursosVeiculo"])) {
		
		if ($_GET["data_remessa"]=="") $hoje= date("Y-m-d");
		else $hoje= formata_data_hifen($_GET["data_remessa"]);
		
        $ontem= soma_data($hoje, -1, 0, 0);
		
		$id_percurso_remessa= pega_id_percurso_da_remessa($_GET["id_remessa"]);
		
		$result_per= mysql_query("select *
                                        from  tr_percursos, tr_percursos_passos, op_veiculos
                                        where tr_percursos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   tr_percursos.id_veiculo = op_veiculos.id_veiculo
										and   op_veiculos.codigo = '". $_GET["codigo"] ."'
										and   (tr_percursos.tipo = '1' or tr_percursos.tipo = '4')
                                        /* and   (tr_percursos.id_situacao_atual = '1' or tr_percursos.id_situacao_atual = '2') */
                                        and   tr_percursos.id_percurso = tr_percursos_passos.id_percurso
                                        and   tr_percursos_passos.passo = '1'
                                         and   (tr_percursos_passos.data_percurso = '". $hoje ."' /* or tr_percursos_passos.data_percurso = '". $ontem ."' */ ) 
                                        order by tr_percursos_passos.data_percurso desc, tr_percursos_passos.hora_percurso desc
                                        ") or die(mysql_error());
										
		$linhas_per= mysql_num_rows($result_per);
		
		?>
        <select id="id_percurso" name="id_percurso">
        	<?
            if ($linhas_per==0) echo "<option value=\"\">Nenhum percurso para este veículo.</option>"; 
			else {
			?>
            <option value="">-</option>
            <?
				$j=0;
				
				while ($rs_per= mysql_fetch_object($result_per)) {
				?>
				<option <? if ($j%2==1) echo "class=\"cor_sim\""; ?> value="<?= $rs_per->id_percurso; ?>" <? if ($rs_per->id_percurso==$id_percurso_remessa) echo "selected=\"selected\""; ?>><?= primeira_palavra(pega_funcionario($rs_per->id_motorista)) ." | ". desformata_data($rs_per->data_percurso) ." ". substr($rs_per->hora_percurso, 0, 5); ?></option>
				<? $j++; } ?>
            <? } ?>
        </select>
        <?
	}
	
	if (isset($_GET["pegaCliente"])) {
		$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
								where pessoas.codigo= '". $_GET["codigo"] ."'
								and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								and   pessoas.status_pessoa = '1'
								");
		
		if (mysql_num_rows($result)>0) {
			$rs= mysql_fetch_object($result);
			
			if (($_GET["id_remessa"]!="") && ($_GET["id_remessa"]!="0")) {
					$result_clientes= mysql_query("select tr_percursos_clientes.id_cliente, tr_percursos.id_motorista
												   from tr_percursos_clientes, tr_percursos, op_suja_remessas
												   where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
												   and   tr_percursos.id_percurso = op_suja_remessas.id_percurso
												   and   op_suja_remessas.id_remessa = '". $_GET["id_remessa"] ."'
												   and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
												");
				$linhas_clientes= mysql_num_rows($result_clientes);
				
				if ($linhas_clientes==0) echo "Cliente não encontrado na remessa informada! <input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"\" title=\"Cliente\" class=\"escondido\" />";
				else {
					echo "<div id=\"nome_cliente\">". $rs->apelido_fantasia ."</div>";
					echo "<input id=\"id_cliente\" name=\"id_cliente\" value=\"". $rs->id_cliente ."\" title=\"Cliente\" class=\"escondido\" />";
					
					echo "<script language=\"javascript\">desabilitaCampo('data_remessa');</script>";
					echo "<script language=\"javascript\">desabilitaCampo('num_remessa');</script>";
				}
			}
			else {
				echo "<div id=\"nome_cliente\">". $rs->apelido_fantasia ."</div>";
				echo "<input id=\"id_cliente\" name=\"id_cliente\" value=\"". $rs->id_cliente ."\" title=\"Cliente\" class=\"escondido\" />";
				
				echo "<script language=\"javascript\">desabilitaCampo('data_remessa');</script>";
				echo "<script language=\"javascript\">desabilitaCampo('num_remessa');</script>";
			}
		}
		else echo "Não localizado! <input id=\"id_cliente\" name=\"id_cliente\" value=\"\" title=\"Cliente\" class=\"escondido\" />";
	}
	
	if (isset($_GET["pegaClienteMultiploSimples"])) {
		$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
								where pessoas.codigo= '". $_GET["codigo"] ."'
								and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
								and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
								and   pessoas_tipos.tipo_pessoa = 'c'
								");
		
		if (mysql_num_rows($result)>0) {
			$rs= mysql_fetch_object($result);
			
			echo "<input id=\"id_cliente_peca_". $_GET["cont"] ."\" name=\"id_cliente_peca[]\" value=\"". $rs->id_cliente ."\" title=\"Cliente\" class=\"escondido\" />";
			echo $rs->sigla;
		}
		else echo "<input type='hidden' class='escondido' name='id_cliente_peca[]' id='id_cliente_peca_". $_GET["cont"] ."' /> N/A";
	}
	
	if (isset($_GET["pegaClienteMultiplo"])) {
		if ($_GET["codigo"]=="0") {
			echo "<div id=\"nome_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\">RELAVE</div>";
			echo "<input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"0\" title=\"Cliente\" class=\"escondido\" />";
			
			echo "<script language=\"javascript\">desabilitaCampo('data_remessa');</script>";
			echo "<script language=\"javascript\">desabilitaCampo('num_remessa');</script>";
		}
		else {
			
			$result= mysql_query("select *, pessoas.id_pessoa as id_cliente from pessoas, pessoas_tipos
									where pessoas.codigo= '". $_GET["codigo"] ."'
									and   pessoas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas_tipos.tipo_pessoa = 'c'
									");
			
			if (mysql_num_rows($result)>0) {
				$rs= mysql_fetch_object($result);
				
				/*if (($_GET["id_remessa"]!="") && ($_GET["id_remessa"]!="0")) {
					$result_clientes= mysql_query("select tr_percursos_clientes.id_cliente, tr_percursos.id_motorista
													   from tr_percursos_clientes, tr_percursos, op_suja_remessas
													   where tr_percursos.id_percurso = tr_percursos_clientes.id_percurso
													   and   tr_percursos.id_percurso = op_suja_remessas.id_percurso
													   and   op_suja_remessas.id_remessa = '". $_GET["id_remessa"] ."'
													   and   tr_percursos_clientes.id_cliente = '". $rs->id_cliente ."'
													");
					$linhas_clientes= mysql_num_rows($result_clientes);
					
					if ($linhas_clientes==0) echo "Cliente não encontrado na remessa informada! <input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"\" title=\"Cliente\" class=\"escondido\" />";
					else {
						echo "<div id=\"nome_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\">". $rs->apelido_fantasia ."</div>";
						echo "<input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"". $rs->id_cliente ."\" title=\"Cliente\" class=\"escondido\" />";
						
						echo "<script language=\"javascript\">desabilitaCampo('data_remessa');</script>";
						echo "<script language=\"javascript\">desabilitaCampo('num_remessa');</script>";
					}
				}
				else {*/
					echo "<div id=\"nome_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\">". $rs->apelido_fantasia ."</div>";
					echo "<input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"". $rs->id_cliente ."\" title=\"Cliente\" class=\"escondido\" />";
					
					echo "<script language=\"javascript\">desabilitaCampo('data_remessa');</script>";
					echo "<script language=\"javascript\">desabilitaCampo('num_remessa');</script>";
				//}
			}
			else echo "Não localizado! <input id=\"id_cliente_". $_GET["local"] ."_". $_GET["cont"] ."\" name=\"id_cliente[]\" value=\"\" title=\"Cliente\" class=\"escondido\" />";
		}
	}
	
	if (isset($_GET["pegaProcesso"])) {
		$result= mysql_query("select * from op_equipamentos_processos
								where codigo= '". $_GET["codigo"] ."'
								and   status_processo = '1'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		
		if (mysql_num_rows($result)>0) {
			$rs= mysql_fetch_object($result);
			echo $rs->processo;
			echo "<input id=\"id_processo\" name=\"id_processo\" value=\"". $rs->id_processo ."\" title=\"Processo\" class=\"escondido\" />";
		}
		else echo "Não localizado! <input id=\"id_processo\" name=\"id_processo\" value=\"\" title=\"Processo\" class=\"escondido\" />";
	}
	
	if (isset($_GET["pegaEquipamento"])) {
		$result= mysql_query("select * from op_equipamentos
								where codigo= '". $_GET["codigo"] ."'
								and   tipo_equipamento= '". $_GET["tipo_equipamento"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		
		if (mysql_num_rows($result)>0) {
			$rs= mysql_fetch_object($result);
			
			echo $rs->equipamento;
			
			if (($rs->ocupado==1) && ($_GET["acao"]=='i'))
				echo " (<span class=\"vermelho\">OCUPADA</span>) <input id=\"id_equipamento\" name=\"id_equipamento\" value=\"\" title=\"Máquina\" class=\"escondido\" />";
			else
				echo "<input id=\"id_equipamento\" name=\"id_equipamento\" value=\"". $rs->id_equipamento ."\" title=\"Máquina\" class=\"escondido\" />";
		}
		else echo "Não localizado! <input id=\"id_equipamento\" name=\"id_equipamento\" value=\"\" title=\"Máquina\" class=\"escondido\" />";
	}
	
	if (isset($_GET["finalizaLavagem"])) {
		
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("select * from op_suja_lavagem
								where id_lavagem= '". $_GET["id_lavagem"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								and   data_fim_lavagem is NULL
								and   hora_fim_lavagem is NULL
								");
		if (!$result) $var++;
		
		if (mysql_num_rows($result)==1) {
			$rs= mysql_fetch_object($result);
			
			$result2= mysql_query("update op_suja_lavagem set
								  	data_fim_lavagem = '". date("Y-m-d") ."',
									hora_fim_lavagem = '". date("H:i:s") ."'
									where id_lavagem = '". $rs->id_lavagem ."'
									");
			if (!$result2) $var++;
			
			$result3= mysql_query("update op_equipamentos set
								  	ocupado = '0'
									where id_equipamento = '". $rs->id_equipamento ."'
									");
			if (!$result3) $var++;
			
		}
		else $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$acao='i';
		$pagina= "op/lavagem_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pegaRemessa"])) {
		
		echo "<script language=\"javascript\">desabilitaCampo('enviar');</script>";
		
		if (($_GET["acao"]=='i') && (!pode("p", $_SESSION["permissao"]))) {
			$hoje= date("Y-m-d");
			$ontem= date("Y-m-d", mktime(14, 0, 0, date("m"), date("d")-1, date("Y")));
			
			$str_periodo= " and   (data_remessa = '". $hoje ."' or data_remessa = '". $ontem ."') ";
		}
		
		$result_remessa= mysql_query("select * from op_suja_remessas
										where data_remessa= '". formata_data($_GET["data_remessa"]) ."'
										and   num_remessa = '". $_GET["num_remessa"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_periodo
										");
		
		if (mysql_num_rows($result_remessa)>0) {
			$rs_remessa= mysql_fetch_object($result_remessa);
			echo "<span class=\"menorx\">Remessa nº <strong>". $rs_remessa->num_remessa ."</strong> (". $rs_remessa->hora_chegada .")</span>";
			echo "<input id=\"id_remessa\" name=\"id_remessa\" value=\"". $rs_remessa->id_remessa ."\" title=\"Remessa\" class=\"escondido\" />";
		}
		else echo "Não localizado ou data de remessa inválida! <input id=\"id_remessa\" name=\"id_remessa\" value=\"\" title=\"Remessa\" class=\"escondido\" />";
		
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["pegaRemessaVetor"])) {
		$result_remessa= mysql_query("select * from op_suja_remessas
										where data_remessa= '". formata_data($_GET["data_remessa"]) ."'
										and   num_remessa = '". $_GET["num_remessa"] ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
		
		if (mysql_num_rows($result_remessa)>0) {
			$rs_remessa= mysql_fetch_object($result_remessa);
			echo "<span class=\"menorx\">Remessa nº <strong>". $rs_remessa->num_remessa ."</strong> (". $rs_remessa->hora_chegada .")</span>";
			echo "<input id=\"id_remessa_". $_GET["cont"] ."\" name=\"id_remessa[]\" value=\"". $rs_remessa->id_remessa ."\" title=\"Remessa\" class=\"escondido\" />";
		}
		else echo "Não localizado! <input id=\"id_remessa_". $_GET["cont"] ."\" name=\"id_remessa[]\" value=\"\" title=\"Remessa\" class=\"escondido\" />";
	}
	
	if (isset($_GET["pesagemSujaExcluir"])) {
		$result= mysql_query("delete from op_suja_pesagem
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_pesagem = '". $_GET["id_pesagem"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		echo excluido_ou_nao($var);
		
		//$pagina= "op/pesagem_suja_listar";
		//require_once("index2.php");
	}
	
	if (isset($_GET["lavagemExcluir"])) {
		$result= mysql_query("delete from op_suja_lavagem
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_lavagem = '". $_GET["id_lavagem"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/lavagem_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["devolucaoExcluir"])) {
		$result= mysql_query("delete from op_suja_devolucao
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_devolucao = '". $_GET["id_devolucao"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/devolucao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["pesagemLimpaExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from op_limpa_pesagem
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_pesagem = '". $_GET["id_pesagem"] ."'
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from op_limpa_pesagem_pecas
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_pesagem = '". $_GET["id_pesagem"] ."'
								");
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		echo excluido_ou_nao($var);
	}
	
	if (isset($_GET["costuraExcluir"])) {
		$result= mysql_query("delete from op_limpa_costura
							 	where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_costura = '". $_GET["id_costura"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/costura_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["gomaExcluir"])) {
		$result= mysql_query("delete from op_suja_gomas
							 	where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_goma = '". $_GET["id_goma"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/goma_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["trocaQuimicoExcluir"])) {
		$result= mysql_query("delete from op_suja_quimicos_trocas
								where id_empresa= '". $_SESSION["id_empresa"] ."'
								and   id_troca_quimico = '". $_GET["id_troca_quimico"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "op/quimico_listar";
		require_once("index2.php");
	}
}


// ############################################### FINANCEIRO ###############################################

if (pode("i", $_SESSION["permissao"])) {
	
	if (isset($_GET["notaPagamentoExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from fi_notas, fi_notas_parcelas, fi_notas_parcelas_pagamentos
									where fi_notas.id_empresa = '". $_SESSION["id_empresa"] ."'
									and   fi_notas.id_nota = fi_notas_parcelas.id_nota
									and   fi_notas_parcelas.id_parcela = fi_notas_parcelas_pagamentos.id_parcela
									and   fi_notas_parcelas_pagamentos.id_parcela_pagamento = '". $_GET["id_parcela_pagamento"] ."'
									") or die(mysql_error());
		
		$rs_pre= mysql_fetch_object($result_pre);
		
		//echo $rs_pre->id_nota;
		
		//se esta parcela já estava paga/recebida
		if ($rs_pre->status_parcela==1) {
			$result_parcela= mysql_query("update fi_notas_parcelas
										 	set   status_parcela = '0'
											where id_parcela = '". $rs_pre->id_parcela ."'
											limit 1
											");
			if (!$result_parcela) $var++;
		}
		
		//se esta nota já estava paga/recebida
		if ($rs_pre->status_parcela==1) {
			$result_nota= mysql_query("update fi_notas
										 	set   status_nota = '0'
											where id_nota = '". $rs_pre->id_nota ."'
											limit 1
											");
			if (!$result_nota) $var++;
		}
			
		$result_pagamento= mysql_query("delete from fi_notas_parcelas_pagamentos
									   	where id_parcela_pagamento = '". $rs_pre->id_parcela_pagamento ."'
										limit 1
										");
		if (!$result_pagamento) $var++;
			
		/*$result1= mysql_query("delete from fi_notas
								where id_nota= '". $_GET["id_nota"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								") or die(mysql_error());
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from fi_notas_parcelas
								where id_nota= '". $_GET["id_nota"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if (!$result2) $var++;
		*/
		
		finaliza_transacao($var);
		$msg= $var;
		
		$pagina_inclui= "_financeiro/__nota_pagamento.php";
		$pagina= "financeiro/nota_esquema";
		require_once("index2.php");
	}
	
	if (isset($_GET["notaExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result1= mysql_query("delete from fi_notas
								where id_nota= '". $_GET["id_nota"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								") or die(mysql_error());
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from fi_notas_parcelas
								where id_nota= '". $_GET["id_nota"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from fi_notas_parcelas_pagamentos
								where id_nota= '". $_GET["id_nota"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if (!$result3) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
		
		$pagina= "financeiro/nota_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["depositoExcluir"])) {
		$result= mysql_query("update fi_depositos set status_deposito= '2'
								where id_deposito= '". $_GET["id_deposito"] ."'
								and   id_empresa = '".$_SESSION["id_empresa"]  ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "financeiro/deposito_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["clienteTipoExcluir"])) {
		$result= mysql_query("update fi_clientes_tipos set status_cliente_tipo= '2'
								where id_cliente_tipo= '". $_GET["id_cliente_tipo"] ."'
								and   id_empresa = '".$_SESSION["id_empresa"]  ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "financeiro/cliente_tipo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["centroCustoStatus"])) {
		$result= mysql_query("update fi_centro_custos set status_centro_custo= '". $_GET["status"] ."'
								where id_centro_custo= '". $_GET["id_centro_custo"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "financeiro/centro_custo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["centroCustoExcluir"])) {
		$result_pre= mysql_query("select * from fi_cc_ct
									where id_centro_custo= '". $_GET["id_centro_custo"] ."'
									") or die(mysql_error());
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from fi_centro_custos
									where id_centro_custo= '". $_GET["id_centro_custo"] ."'
									limit 1
									") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "financeiro/centro_custo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["tipoCentroCustoExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from fi_centro_custos_tipos
								where id_centro_custo_tipo= '". $_GET["id_centro_custo_tipo"] ."'
								limit 1
								") or die(mysql_error());
		if (!$result) $var++;
		
		$result2= mysql_query("delete from fi_cc_ct
								where id_centro_custo_tipo= '". $_GET["id_centro_custo_tipo"] ."'
								") or die(mysql_error());
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "financeiro/centro_custo_tipo_listar";
		require_once("index2.php");
	}

}

// ############################################### RH ###############################################

if (pode("rhviwcd", $_SESSION["permissao"])) {
	
	if (isset($_GET["afastamentoManual"])) {
		$var=0;
		
		$result= mysql_query("update  rh_funcionarios
								set   afastado = '". $_GET["afastado"] ."'
								where id_empresa = '". $_SESSION["id_empresa"] ."'
								and   id_funcionario = '". $_GET["id_funcionario"] ."'
								");
		$rs= mysql_fetch_object($result);
		
		$pagina= "rh/carreira";
		require_once("index2.php");
	}
	
	
	if (isset($_GET["verificaCartao"])) {
		$var=0;
		
		$result= mysql_query("select * from rh_cartoes
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   numero_cartao = '". $_GET["numero_cartao"] ."'
										");
		$linhas_cartao= mysql_num_rows($result);
		
		if ($linhas_cartao==0)
			echo "<span class=\"verde\">Cartão não está em uso!</span>";
		else {
			$rs= mysql_fetch_object($result);
			echo "<span class=\"vermelho\">Cartão em uso por <strong>". pega_funcionario($rs->id_funcionario) ."</strong>!</span>";
		}
	}
	
	if (isset($_GET["verificaCrm"])) {
		$var=0;
		
		$result= mysql_query("select * from rh_afastamentos
										where id_empresa = '". $_SESSION["id_empresa"] ."'
										and   crm = '". $_GET["crm"] ."'
										");
		$rs= mysql_fetch_object($result);
		
		echo "<input title=\"Nome do médico\" name=\"nome_medico\" id=\"nome_medico\" value=\"". $rs->nome_medico ."\" />";
	}
	
	if (isset($_GET["batidaIntervaloFind"])) {
		$var=0;
		inicia_transacao();
		
		/*$result_horario= mysql_query("select * from rh_funcionarios, rh_carreiras, rh_turnos, rh_turnos_horarios
										where rh_funcionarios.id_funcionario = '". $_GET["id_funcionario"] ."'
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_turno = rh_turnos.id_turno
										and   rh_turnos.id_turno = rh_turnos_horarios.id_turno
										and   rh_turnos_horarios.id_dia = '". $_GET["id_dia"] ."' ");
		
		$rs_horario= mysql_fetch_object($result_horario);
		*/
		
		//0-> tipo
		//1-> data
		//2-> hora
		//3-> hl
		//4-> vale_dia
		
		$result_ponto= mysql_query("select * from rh_ponto
								   	where id_funcionario = '". $_GET["id_funcionario"] ."'
									and   data_batida = '". formata_data($_GET["data"]) ."'
									and   vale_dia = '". formata_data($_GET["data"]) ."'
									and   tipo = '1'
									order by hora asc limit 1
									");
		
		//echo $rs_ponto->data_batida ." ". $rs_ponto->hora;
		
		$linhas_ponto= mysql_num_rows($result_ponto);
		$rs_ponto= mysql_fetch_object($result_ponto);
		
		for ($j=0; $j<2; $j++) {
			
			$minutos_rand1= rand(-9, 9);
			$minutos_rand2= rand(-9, 9);
			
			$segundos_rand1= rand(-30, 30);
			$segundos_rand2= rand(-30, 30);
			
			switch($j) {
				case 0:
					$data_hora_aqui= soma_data_hora($rs_ponto->data_batida ." ". $rs_ponto->hora, 0, 0, 0, 6, $minutos_rand1, $segundos_rand1);
					break;
				case 1:
					$data_hora_aqui= soma_data_hora($rs_ponto->data_batida ." ". $rs_ponto->hora, 0, 0, 0, 7, $minutos_rand2, $segundos_rand2);
					break;
			}
			
			$pedacos= explode(" ", $data_hora_aqui);
			
			/*echo "operação: ". $j;
			echo "<br>data: ". $pedacos[0];
			echo "<br>hora: ". $pedacos[1];
			echo "<br>vale_dia: ". formata_data($_GET["data"]) ."<br><br><br>";
			*/
			
			$result_ponto= mysql_query("insert into rh_ponto (id_funcionario, data_batida, hora, data_hora_batida, tipo, hl, vale_dia, id_usuario)
										values
										('". $_GET["id_funcionario"] ."', '". $pedacos[0] ."', '". $pedacos[1] ."', '". $pedacos[0] ." ". $pedacos[1] ."',
										'". $j ."', '0', '". formata_data($_GET["data"]) ."', '". $_SESSION["id_usuario"] ."')
										");
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "rh/espelho";
		require_once("index2.php");
		
		
	}
	
	if (isset($_GET["batidaIntervaloNormal"])) {
		$var=0;
		inicia_transacao();
		
		/*$result_horario= mysql_query("select * from rh_funcionarios, rh_carreiras, rh_turnos, rh_turnos_horarios
										where rh_funcionarios.id_funcionario = '". $_GET["id_funcionario"] ."'
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_turno = rh_turnos.id_turno
										and   rh_turnos.id_turno = rh_turnos_horarios.id_turno
										and   rh_turnos_horarios.id_dia = '". $_GET["id_dia"] ."' ");
		
		$rs_horario= mysql_fetch_object($result_horario);
		*/
		
		//0-> tipo
		//1-> data
		//2-> hora
		//3-> hl
		//4-> vale_dia
		
		$result_ponto= mysql_query("select * from rh_ponto
								   	where id_funcionario = '". $_GET["id_funcionario"] ."'
									and   vale_dia = '". formata_data($_GET["data"]) ."'
									and   tipo = '1'
									order by hora asc limit 1
									");
		
		
		
		$linhas_ponto= mysql_num_rows($result_ponto);
		$rs_ponto= mysql_fetch_object($result_ponto);
		/*
		echo $rs_ponto->data_batida ." ". $rs_ponto->hora;
		
		echo " | ". soma_data_hora($rs_ponto->data_batida ." ". $rs_ponto->hora, 0, 0, 0, 6, $minutos_rand1, $segundos_rand1);
		
		die("<br />
<br />
morreu!");
		*/
		
		for ($j=0; $j<2; $j++) {
			
			$minutos_rand1= rand(-9, 9);
			$minutos_rand2= rand(-9, 9);
			
			$segundos_rand1= rand(-30, 30);
			$segundos_rand2= rand(-30, 30);
			
			
			
			switch($j) {
				case 0:
					$data_hora_aqui= soma_data_hora($rs_ponto->data_batida ." ". $rs_ponto->hora, 0, 0, 0, 6, $minutos_rand1, $segundos_rand1);
					break;
				case 1:
					$data_hora_aqui= soma_data_hora($rs_ponto->data_batida ." ". $rs_ponto->hora, 0, 0, 0, 7, $minutos_rand2, $segundos_rand2);
					break;
			}
			
			$pedacos= explode(" ", $data_hora_aqui);
			
			//echo "operação: ". $operacao[$j][0];
			//echo "<br>data: ". $operacao[$j][1];
			//echo "<br>hora: ". $operacao[$j][2];
			//echo "<br>hl: ". $operacao[$j][3];
			//echo "<br>vale_dia: ". $operacao[$j][4] ."<br><br><br>";
			
			$result_ponto= mysql_query("insert into rh_ponto (id_funcionario, data_batida, hora, data_hora_batida, tipo, hl, vale_dia, id_usuario)
										values
										('". $_GET["id_funcionario"] ."', '". $pedacos[0] ."', '". $pedacos[1] ."', '". $pedacos[0] ." ". $pedacos[1] ."',
										'". $j ."', '0', '". formata_data($_GET["data"]) ."', '". $_SESSION["id_usuario"] ."')
										");
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "rh/espelho";
		require_once("index2.php");
	}
	
	if (isset($_GET["batidaAutomatica"])) {
		$var=0;
		inicia_transacao();
		
		$result_horario= mysql_query("select * from rh_funcionarios, rh_carreiras, rh_turnos, rh_turnos_horarios
										where rh_funcionarios.id_funcionario = '". $_GET["id_funcionario"] ."'
										and   rh_funcionarios.id_funcionario = rh_carreiras.id_funcionario
										and   rh_carreiras.atual = '1'
										and   rh_carreiras.id_turno = rh_turnos.id_turno
										and   rh_turnos.id_turno = rh_turnos_horarios.id_turno
										and   rh_turnos_horarios.id_dia = '". $_GET["id_dia"] ."' ");
		
		$rs_horario= mysql_fetch_object($result_horario);
		
		//0-> tipo
		//1-> data
		//2-> hora
		//3-> hl
		//4-> vale_dia
		
		if ($rs_horario->hl==1) $soma=1; else $soma=0;
		$dataex= explode('/', $_GET["data"]);
		//$data= date("Y-m-d", mktime(0, 0, 0, ));
		
		$i=0;
		$entrada_auto= explode(":", $rs_horario->entrada);
		$entrada_completa= explode(" ", date("Y-m-d H:i:s", mktime($entrada_auto[0], $entrada_auto[1], $entrada_auto[2], $dataex[1], $dataex[0]+$soma, $dataex[2])));
		
		$operacao[$i][0]= 1;
		$operacao[$i][1]= $entrada_completa[0];
		$operacao[$i][2]= $entrada_completa[1];
		$operacao[$i][3]= $rs_horario->hl;
		$operacao[$i][4]= formata_data_hifen($_GET["data"]);
		$i++;
		
		//se tem intervalo
		if (tem_intervalo_no_dia($rs_horario->id_intervalo, $_GET["id_dia"])) {
			$intervalo_inicio= explode(' ', calcula_horario_intervalo('i', $rs_horario->id_intervalo, $_GET["id_dia"], formata_data_hifen($_GET["data"])));
			$intervalo_fim= explode(' ', calcula_horario_intervalo('f', $rs_horario->id_intervalo, $_GET["id_dia"], formata_data_hifen($_GET["data"])));
			
			//saida para intervalo
			$operacao[$i][0]= 0;
			$operacao[$i][1]= $intervalo_inicio[0];
			$operacao[$i][2]= $intervalo_inicio[1];
			$operacao[$i][3]= $rs_horario->hl;
			$operacao[$i][4]= formata_data_hifen($_GET["data"]);
			$i++;
			
			//saida para intervalo
			$operacao[$i][0]= 1;
			$operacao[$i][1]= $intervalo_fim[0];
			$operacao[$i][2]= $intervalo_fim[1];
			$operacao[$i][3]= $rs_horario->hl;
			$operacao[$i][4]= formata_data_hifen($_GET["data"]);
			$i++;
		}
		
		$saida_auto= explode(":", $rs_horario->saida);
		
		//vira o dia
		if (intval($entrada_auto[0])>intval($saida_auto[0])) $soma++;
		
		$saida_completa= explode(" ", date("Y-m-d H:i:s", mktime($saida_auto[0], $saida_auto[1], $saida_auto[2], $dataex[1], $dataex[0]+$soma, $dataex[2])));
		
		//saida
		$operacao[$i][0]= 0;
		$operacao[$i][1]= $saida_completa[0];
		$operacao[$i][2]= $saida_completa[1];
		$operacao[$i][3]= $rs_horario->hl;
		$operacao[$i][4]= formata_data_hifen($_GET["data"]);
		$i++;
		
		for ($j=0; $j<$i; $j++) {
			/*echo "operação: ". $operacao[$j][0];
			echo "<br>data: ". $operacao[$j][1];
			echo "<br>hora: ". $operacao[$j][2];
			echo "<br>hl: ". $operacao[$j][3];
			echo "<br>vale_dia: ". $operacao[$j][4] ."<br><br><br>";*/
			
			$result_ponto= mysql_query("insert into rh_ponto (id_funcionario, data_batida, hora, data_hora_batida, tipo, hl, vale_dia, id_usuario)
										values
										('". $_GET["id_funcionario"] ."', '". $operacao[$j][1] ."', '". $operacao[$j][2] ."', '". $operacao[$j][1] ." ". $operacao[$j][2] ."',
										'". $operacao[$j][0] ."', '". $operacao[$j][3] ."', '". $operacao[$j][4] ."', '". $_SESSION["id_usuario"] ."')
										");
		}
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "rh/espelho";
		require_once("index2.php");
		
		
	}
	
	if (isset($_GET["arquivoExcluir"])) {
		$apagar= @unlink(CAMINHO . $_GET["arquivo"]);
		
		if ($apagar) echo "Arquivo excluído com sucesso!";
		else echo "Não foi possível excluir o arquivo!";
	}
	
	if (isset($_GET["cadastraNovoTipoPessoa"])) {
		$result= mysql_query("insert into pessoas_tipos
								(id_pessoa, tipo_pessoa, id_empresa)
								values
								('". $_GET["id_pessoa"] ."', '". $_GET["tipo_pessoa"] ."', '". $_SESSION["id_empresa"] ."')
								");
		
		$pagina= "financeiro/pessoa_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["verificaCpf"])) {
		$cpf= $_GET["cpf"];
		$sql= "select pessoas.id_pessoa from pessoas, rh_funcionarios
					where pessoas.cpf_cnpj = '". $cpf ."'
					and   pessoas.tipo = 'f'
					and   pessoas.id_pessoa = rh_funcionarios.id_funcionario
					and   rh_funcionarios.status_funcionario <> '2'
					";
		
		if ($_GET["id_pessoa"]!="")
			$sql .= " and pessoas.id_pessoa <> '". $_GET["id_pessoa"] ."' " ;
		
		$result= mysql_query($sql) or die(mysql_error());
		
		$campo[0]= "<input type=\"hidden\" name=\"passa_cpf\" id=\"passa_cpf\" value=\"\" class=\"escondido\" />";
		$campo[1]= "<input type=\"hidden\" name=\"passa_cpf\" id=\"passa_cpf\" value=\"1\" class=\"escondido\" />";
	
		if (mysql_num_rows($result)==0) {
			echo $campo[1] ."<span id=\"span_cpf_testa\" class=\"verde\">CPF disponível!</span>";
			echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
		}
		else {
			$rs= mysql_fetch_object($result);
			
			$result2= mysql_query("select * from pessoas, pessoas_tipos
								 	where pessoas.id_pessoa = pessoas_tipos.id_pessoa
									and   pessoas.id_pessoa = '". $rs->id_pessoa ."'
									");
			$linhas2= mysql_num_rows($result2);
			$como= " como ";
			
			$i=1;
			while ($rs2= mysql_fetch_object($result2)) {
				
				$como .= "<strong>". pega_tipo_pessoa($rs2->tipo_pessoa) ."</strong>";
				
				if ($i!=$linhas2) $como .= ", ";
				
				$i++;
			}
			
			echo $campo[0] ."<span id=\"span_cpf_testa\" class=\"vermelho\">CPF já cadastrado ". $como ."!</span>";
			
			if ($_GET["tipo_pessoa"]!='u') {
				echo "<br /><label>&nbsp;</label><a class=\"menor\" href=\"javascript:void(0);\" onclick=\"cadastraNovoTipoPessoa('". $rs->id_pessoa ."', '". $_GET["tipo_pessoa"] ."');\">&raquo; cadastrar como <strong>". pega_tipo_pessoa($_GET["tipo_pessoa"]) ."</strong></a>";	
			}
		}
	}
	
	if (isset($_GET["retornaCid"])) {
		$result= mysql_query("select CID10, DESCR from rh_cid10 where CID10 = '". $_GET["pesquisa_cid"] ."'");
		
		if (mysql_num_rows($result)==0) {
			echo "Doença não encontrada. Verifique veracidade!";
			echo "<input type=\"hidden\" name=\"cid10\" id=\"cid10\" value=\"\" class=\"escondido\" />";
		}
		else {
			$rs= mysql_fetch_object($result);
			echo strtoupper($rs->DESCR);
			echo "<input type=\"hidden\" name=\"cid10\" id=\"cid10\" value=\"". $rs->CID10 ."\" class=\"escondido\" />";
		}
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}

	
	if (isset($_GET["funcionarioStatus"])) {
		$result= mysql_query("update rh_funcionarios set status_funcionario= '". $_GET["status"] ."'
								where id_funcionario= '". $_GET["id_funcionario"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/funcionario_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["departamentoStatus"])) {
		$result= mysql_query("update rh_departamentos set status_departamento= '". $_GET["status"] ."'
								where id_departamento= '". $_GET["id_departamento"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/departamento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["departamentoExcluir"])) {
		$result= mysql_query("delete from rh_departamentos
								where id_departamento= '". $_GET["id_departamento"] ."'
								limit 1
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/departamento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["motivoExcluir"])) {
		$result= mysql_query("delete from rh_motivos
								where id_motivo= '". $_GET["id_motivo"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/motivo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["cargoStatus"])) {
		$result= mysql_query("update rh_cargos set status_cargo= '". $_GET["status"] ."'
								where id_cargo= '". $_GET["id_cargo"] ."'
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/cargo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["cargoExcluir"])) {
		$result_pre= mysql_query("select * from rh_carreiras
									where id_cargo= '". $_GET["id_cargo"] ."'
									");
		
		if (mysql_num_rows($result_pre)==0)
			$result= mysql_query("delete from rh_cargos
									where id_cargo= '". $_GET["id_cargo"] ."'
									");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/cargo_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["cartaoExcluir"])) {
		$result= mysql_query("delete from rh_cartoes
								where id_cartao= '". $_GET["id_cartao"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."'
								") or die(mysql_error());
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/cartao_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["feriadoExcluir"])) {
		$result= mysql_query("delete from rh_feriados
								where id_feriado= '". $_GET["id_feriado"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if ($result) $msg= 0;
		else $msg=1;
		
		echo excluido_ou_nao($msg);
	}
	
	if (isset($_GET["VTLinhaExcluir"])) {
		$result= mysql_query("delete from rh_vt_linhas
								where id_linha= '". $_GET["id_linha"] ."'
								and   id_empresa= '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/vt_linha_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["turnoStatus"])) {
		$result= mysql_query("update rh_turnos set status_turno= '". $_GET["status"] ."'
								where id_turno= '". $_GET["id_turno"] ."'
								limit 1
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/turno_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["historicoExcluir"])) {
		$result= mysql_query("delete from rh_historico
								where id_historico= '". $_GET["id_historico"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if ($result) $msg= 0;
		else $msg=1;
			
		$pagina= "rh/historico_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["escalaTrocaExcluir"])) {
		
		inicia_transacao();
		$var=0;
		
		$result_pre= mysql_query("select * from rh_escala_troca
								 	where id_empresa= '". $_SESSION["id_empresa"] ."'
									and   id_escala_troca= '". $_GET["id_escala_troca"] ."'
									");
		$rs_pre= mysql_fetch_object($result_pre);
		
		$result1= mysql_query("update rh_escala
							  	set trabalha = '1'
								where id_funcionario= '". $rs_pre->id_funcionario_solicitante ."'
								and   data_escala= '". $rs_pre->data_escala_troca ."'
								and   trabalha= '0'
								limit 1
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("update rh_escala
							  	set trabalha = '0'
								where id_funcionario= '". $rs_pre->id_funcionario_assume ."'
								and   data_escala= '". $rs_pre->data_escala_troca ."'
								and   trabalha= '1'
								limit 1
								");
		if (!$result2) $var++;
		
		$result3= mysql_query("delete from rh_escala_troca
								where id_escala_troca= '". $_GET["id_escala_troca"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result3) $var++;
		
		finaliza_transacao($var);
		$msg= $var;
			
		$pagina= "rh/escala_troca_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["carreiraExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from rh_carreiras
									where id_carreira= '". $_GET["id_carreira"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (!$result_pre) $var++;
		
		if (mysql_num_rows($result_pre)==1) {
			//pega os dados da carreira que quer excluir
			$rs_pre= mysql_fetch_object($result_pre);
			
			//se estiver excluindo a carreira atual, a anterior vira atual
			if ($rs_pre->atual==1) {
				$result1= mysql_query("select * from rh_carreiras
										where data < '". $rs_pre->data ."'
										and   id_funcionario = '". $rs_pre->id_funcionario ."'
										order by data desc limit 1
										");
				if (!$result1) $var++;
				$rs1= mysql_fetch_object($result1);
				
				$result2= mysql_query("update rh_carreiras set atual = '1'
										where id_carreira= '". $rs1->id_carreira ."' ");
				if (!$result2) $var++;
			}
			
			//se estiver excluindo uma demissao... precisa ativar denovo
			if ($rs_pre->id_acao_carreira==2) {
				$result3= mysql_query("update rh_funcionarios set status_funcionario = '1'
										where id_funcionario= '". $rs_pre->id_funcionario ."'
										and   id_empresa = '". $_SESSION["id_empresa"] ."'
										");
				if (!$result3) $var++;
			}
			
			$result4= mysql_query("delete from rh_carreiras where id_carreira= '". $rs_pre->id_carreira ."' ");
			if (!$result4) $var++;
						
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$id_funcionario= $rs_pre->id_funcionario;
		$pagina= "rh/carreira";
		require_once("index2.php");
	}
	
	if (isset($_GET["VTDescontoExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from rh_vt_descontos
							 	where id_vt_desconto= '". $_GET["id_vt_desconto"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "rh/vt";
		require_once("index2.php");
	}
	
	if (isset($_GET["HEAutorizacaoExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from rh_he_autorizacao
							 	where id_he_autorizacao= '". $_GET["id_he_autorizacao"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "rh/he_autorizacao";
		require_once("index2.php");
	}
	
	if (isset($_GET["substituicaoFuncaoExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from rh_substituicao_funcao
							 	where id_substituicao_funcao= '". $_GET["id_substituicao_funcao"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "rh/substituicao_funcao";
		require_once("index2.php");
	}
	
	if (isset($_GET["VTExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from rh_vt
							 	where id_vt= '". $_GET["id_vt"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		$msg= $var;
		
		$pagina= "rh/vt";
		require_once("index2.php");
	}
	
	if (isset($_GET["insalubridadeExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result= mysql_query("delete from rh_insalubridade
							 	where id_insalubridade= '". $_GET["id_insalubridade"] ."'
								and   id_empresa = '". $_SESSION["id_empresa"] ."'
								limit 1
								");
		if (!$result) $var++;
	
		finaliza_transacao($var);
		
		$msg= $var;
		
		$id_funcionario= $rs_pre->id_funcionario;
		$pagina= "rh/insalubridade";
		require_once("index2.php");
	}
	
	if (isset($_GET["bancoHorasExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from rh_ponto_banco
									where id_banco = '". $_GET["id_banco"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		
		if (!$result_pre) $var++;
		
		if (mysql_num_rows($result_pre)==1) {
			//pega os dados da carreira que quer excluir
			$rs_pre= mysql_fetch_object($result_pre);
			
			$result1= mysql_query("update rh_ponto_banco_atual set
									he = he - '". $rs_pre->he ."'
									where id_funcionario = '". $rs_pre->id_funcionario ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									and   tipo_he = '". $rs_pre->tipo_he ."' ") or die(mysql_error());
			if (!$result1) $var++;
				
			$result2= mysql_query("delete from rh_ponto_banco
									where id_banco = '". $_GET["id_banco"] ."'
									and   id_empresa = '". $_SESSION["id_empresa"] ."' ");
			if (!$result2) $var++;
						
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$id_funcionario= $rs_pre->id_funcionario;
		
		$pagina= "rh/banco";
		require_once("index2.php");
	}
	
	if (isset($_GET["turnoExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from rh_carreiras
									where id_turno= '". $_GET["id_turno"] ."' ");
		
		if (!$result_pre) $var++;
		
		if (mysql_num_rows($result_pre)==0) {
			$result1= mysql_query("delete from rh_turnos where id_turno= '". $_GET["id_turno"] ."'	");
			if (!$result1) $var++;
			
			$result2= mysql_query("delete from rh_turnos_horarios where id_turno= '". $_GET["id_turno"] ."' ");
			if (!$result2) $var++;
			
			$result3_pre= mysql_query("select * from rh_turnos_intervalos where id_turno= '". $_GET["id_turno"] ."' ");
			if (!$result3_pre) $var++;
			
			$i=0;
			while ($rs3_pre= mysql_fetch_object($result3_pre)) {
				$result3[$i]= mysql_query("delete from rh_turnos_intervalos_horarios where id_intervalo= '". $rs3_pre->id_intervalo ."' ");
				if (!$result3[$i]) $var++;
				$i++;
			}
			
			$result4= mysql_query("delete from rh_turnos_intervalos where id_turno= '". $_GET["id_turno"] ."' ");
			if (!$result4) $var++;
			
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$pagina= "rh/turno_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["intervaloExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from rh_carreiras
									where id_intervalo= '". $_GET["id_intervalo"] ."' ");
		
		if (!$result_pre) $var++;
		
		if (mysql_num_rows($result_pre)==0) {
						
			$result1= mysql_query("delete from rh_turnos_intervalos where id_intervalo= '". $_GET["id_intervalo"] ."' ");
			if (!$result1) $var++;
			
			$result2= mysql_query("delete from rh_turnos_intervalos_horarios where id_intervalo= '". $_GET["id_intervalo"] ."' ");
			if (!$result2) $var++;

			
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
			
		$acao= 'i';
		$pagina= "rh/turno_intervalo";
		require_once("index2.php");
	}
	
	if (isset($_GET["intervaloHorarioExcluir"])) {
		$var=0;
		inicia_transacao();
		
		$result2= mysql_query("delete from rh_turnos_intervalos_horarios
								where id_intervalo_horario = '". $_GET["id_intervalo_horario"] ."'
								and   id_intervalo = '". $_GET["id_intervalo"] ."' limit 1
								");
	
		if (!$result2) $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		//echo $msg;
		$acao= 'i';
		$pagina= "rh/turno_intervalo_horarios";
		require_once("index2.php");
	}
	
	if (isset($_GET["pegaHorario"])) {
		$rs= mysql_fetch_object(mysql_query("select ". $_GET["tipo"] ." as horario from rh_turnos_horarios
												where id_turno= '". $_GET["id_turno"] ."'
												and   id_dia = '". $_GET["id_dia"] ."'
												"));
		echo $rs->horario;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["horarioExcluir"])) {
		$id_funcionario= pega_id_funcionario_do_id_horario($_GET["id_horario"]);
		
		$rs= mysql_fetch_object(mysql_query("select ". $_GET["tipo"] ." as horario from rh_turnos_horarios
												where id_turno= '". $_GET["id_turno"] ."'
												and   id_dia = '". $_GET["id_dia"] ."'
												"));
		echo $rs->horario;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["afastamentoExcluir"])) {
		$var=0;
		inicia_transacao();
					
		$result1= mysql_query("delete from rh_afastamentos
								where id_afastamento= '". $_GET["id_afastamento"] ."'
								");
		if (!$result1) $var++;
		
		$result2= mysql_query("delete from rh_afastamentos_dias
								where id_afastamento= '". $_GET["id_afastamento"] ."'
								/* tipo_afastamento = '". $_GET["tipo_afastamento"] ."' and   */
								");
		if (!$result2) $var++;

		finaliza_transacao($var);
		
		$msg=$var;			
		$pagina= "rh/afastamento_listar";
		require_once("index2.php");
	}
	
	if (isset($_GET["alteraDepartamentos"])) {
		$result= mysql_query("select * from rh_departamentos
							 		where id_empresa = '". $_GET["id_empresa"] ."'
									order by departamento asc ");
		
		if ($_GET["condicao"]==1)
			$str_mais= "onchange=\"alteraTurnos(); alteraCargos();\"";
		if ($_GET["condicao"]==3)
			$str_mais= "onchange=\"alteraPastas();\"";
		
		$str= "<select name=\"id_departamento\" id=\"id_departamento\" title=\"Departamento\" ". $str_mais .">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_departamento ."\">". $rs->departamento ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraPastas"])) {
		$result= mysql_query("select * from dc_documentos_pastas
								where dc_documentos_pastas.id_departamento= '". $_GET["id_departamento"] ."' 
								". $str ."
								order by dc_documentos_pastas.pasta asc");
		
		$str= "<select name=\"id_pasta\" id=\"id_pasta\" title=\"Pasta\">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_pasta ."\">". $rs->pasta ." - ". $rs->nome_pasta ." (". ativo_inativo($rs->status_pasta) .")" ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	
	if (isset($_GET["alteraPessoas"])) {
		$result= mysql_query("select pessoas.id_pessoa, pessoas.nome_rz
									from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									and   rh_enderecos.id_pessoa = pessoas.id_pessoa
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_acao_carreira = '1'
									and   id_empresa = '". $_GET["id_empresa"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$str= "<select name=\"id_pessoa\" id=\"id_pessoa\" title=\"Pessoa\">
				<option value=\"\">- NENHUM (CRIAR EM NOME DA EMPRESA) -</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_pessoa ."\">". $rs->nome_rz ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraFuncionariosAtivosInativos"])) {
		$result= mysql_query("select * from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									and   rh_enderecos.id_pessoa = pessoas.id_pessoa
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_acao_carreira = '1'
									and   (rh_funcionarios.status_funcionario = '". $_GET["status_funcionario"] ."')
									and   rh_funcionarios.id_empresa = '". $_SESSION["id_empresa"] ."'
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$str= "<select name=\"id_funcionario\" id=\"id_funcionario\" title=\"Funcionário\">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_funcionario ."\">". $rs->nome_rz ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraFuncionarios"])) {
		$result= mysql_query("select *
									from  pessoas, rh_funcionarios, rh_enderecos, rh_carreiras
									where pessoas.id_pessoa = rh_funcionarios.id_pessoa
									and   pessoas.tipo = 'f'
									and   rh_enderecos.id_pessoa = pessoas.id_pessoa
									and   rh_carreiras.id_funcionario = rh_funcionarios.id_funcionario
									and   rh_carreiras.id_acao_carreira = '1'
									and   (rh_funcionarios.status_funcionario = '1' or rh_funcionarios.status_funcionario = '-1')
									and   rh_funcionarios.id_empresa = '". $_GET["id_empresa"] ."'
									and   rh_funcionarios.id_funcionario not in
									(select id_funcionario from usuarios)
									order by pessoas.nome_rz asc
									") or die(mysql_error());
		
		$str= "<select name=\"id_funcionario\" id=\"id_funcionario\" title=\"Funcionário\">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_funcionario ."\">". $rs->nome_rz ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraCargos"])) {
		$result= mysql_query("select * from rh_cargos where id_departamento = '". $_GET["id_departamento"] ."'
									order by cargo asc ");
		
		$str= "<select name=\"id_cargo\" id=\"id_cargo\" title=\"Cargo\" \">
				<option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_cargo ."\">". $rs->cargo ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraTurnos"])) {
		$result= mysql_query("select * from rh_turnos
							 	where id_departamento = '". $_GET["id_departamento"] ."'
								and   status_turno = '1'
								order by turno asc ");
		
		$str= "<select name=\"id_turno\" id=\"id_turno\" title=\"Turno\"";
		if ($_GET["soh"]!=1) $str .=" onchange=\"alteraIntervalos(); ";
		$str .=" \"> <option value=\"\">---</option>";
		
		$i=1;
		while ($rs= mysql_fetch_object($result)) {
			if ($i==1) $classe= " class=\"cor_sim\"";
			else $classe= " ";
			$i++;
			$str .= "<option ". $classe ." value=\"". $rs->id_turno ."\">". $rs->turno ."</option>";
			if ($i==2) $i=0;
		}
		
		$str .= "</select>";
		echo $str;
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["alteraIntervalos"])) {
		$result= mysql_query("select * from rh_turnos_intervalos
									where id_turno = '". $_GET["id_turno"] ."'
									order by intervalo asc ");
		
		if (mysql_num_rows($result)==0) {
			echo "<input type=\"hidden\" class=\"escondido\" name=\"id_intervalo\" id=\"id_intervalo\" value=\"0\" >";
			echo "Turno sem intervalo.";
		}
		else {
			$str= "<select name=\"id_intervalo\" id=\"id_intervalo\" title=\"Intervalo\">
					<option value=\"\">---</option>";
			
			$i=1;
			while ($rs= mysql_fetch_object($result)) {
				if ($i==1) $classe= " class=\"cor_sim\"";
				else $classe= " ";
				$i++;
				$str .= "<option ". $classe ." value=\"". $rs->id_intervalo ."\">". $rs->intervalo ."</option>";
				if ($i==2) $i=0;
			}
			
			$str .= "</select>";
			echo $str;
		}
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["atualizaHorarioTurno"])) {
		$result_pre= mysql_query("select rh_departamentos.id_empresa from rh_departamentos, rh_turnos, rh_turnos_horarios
									where rh_turnos_horarios.id_turno_horario = '". $_GET["id_turno_horario"] ."'
									and   rh_turnos.id_departamento = rh_departamentos.id_departamento
									and   rh_turnos.id_turno = rh_turnos_horarios.id_turno
									and   rh_departamentos.id_empresa = '". $_SESSION["id_empresa"] ."'
									");
		if (mysql_num_rows($result_pre)==1) {
			$result= mysql_query("update rh_turnos_horarios
									set ". $_GET["tipo"] ." = '". $_GET["horario"] ."'
									where id_turno_horario = '". $_GET["id_turno_horario"] ."'
									") or die(mysql_error());
			
			if (!$result)
				echo "<script language=\"javascript\">alert('Não foi possível atualizar o horário, tente novamente!');</script>";
		}
		echo "<script language=\"javascript\">habilitaCampo('enviar');</script>";
	}
	
	if (isset($_GET["pegaDadosTurnodoFuncionario"])) {
		$result= mysql_query("select * from rh_carreiras
									where atual = '1'
									and   id_empresa = '". $_SESSION["id_empresa"] ."'
									and   id_funcionario = '". $_GET["id_funcionario"] ."'
									") or die(mysql_error());
		$rs= mysql_fetch_object($result);
		
		$data= explode("/", $_GET["data_escala_troca"]);
		$id_dia= date("w", mktime(0, 0, 0, $data[1], $data[0], $data[2]));
		
		//echo traduz_dia($id_dia);
		
		$horarios= pega_horarios_turno($rs->id_turno, $id_dia);
		echo "<strong>Horário de trabalho:</strong> ". $horarios[0] ." até ". $horarios[1];
		
		$result2= mysql_query("select * from rh_escala
									where id_funcionario = '". $_GET["id_funcionario"] ."'
									and   data_escala= '". formata_data($_GET["data_escala_troca"]) ."'
									and   trabalha= '1'
									") or die(mysql_error());
		$rs2= mysql_fetch_object($result2);
		
		if ($rs2->trabalha==1) $classe= "verde";
		else $classe= "vermelho";
		
		echo "<br /><label>&nbsp;</label><strong>Trabalha neste dia:</strong> <span class=\"". $classe ."\">". sim_nao($rs2->trabalha) ."</span>"; 
		
	}
}

/* ---------- ESTOQUE ------------------------------------------------------------------- */

if (pode("iq|", $_SESSION["permissao"])) {

	if (isset($_GET["precoConsulta"])) {
		$pagina= "financeiro/estoque_preco_consulta";
		require_once("index2.php");
	}
	
	if (isset($_GET["itemPesquisar"])) {
		
		switch ($_GET["origem"]) {
			//se for saida... soh mostra oq tem no estoque
			case "s":
					$sql= "select * from fi_itens, fi_estoque
										where (fi_itens.item like '%". $_GET["pesquisa"] ."%'
												or fi_itens.apelidos like '%". $_GET["pesquisa"] ."%')
										and   fi_itens.id_item = fi_estoque.id_item
										and   fi_estoque.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   fi_estoque.qtde_atual > '0'
										order by fi_itens.item asc
										";
					break;
			//entrada
			case "e":
			//pesquisa de preço
			case "p":
					$sql= "select * from fi_itens
								where (fi_itens.item like '%". $_GET["pesquisa"] ."%'
										or fi_itens.apelidos like '%". $_GET["pesquisa"] ."%')
								order by fi_itens.item asc
								";
					break;
		}
		//echo $sql;
		$result= mysql_query($sql) or die("1: ". mysql_error());	
		
		//volta as solicitacoes em select
		if ($_GET["modo"]=="select") {
			echo "<select name=\"id_item[]\" id=\"id_item_". $_GET["cont"] ."\" onchange=\"processaDecimal('". $_GET["cont"] ."'); alteraTipoEstoqueCC('". $_GET["cont"] ."', this.value);\">";
			
			if (mysql_num_rows($result)==0)
				echo "<option value=\"\">Nenhum registro encontrado!</li>";
			else {
				echo "<option value=\"\">---</li>";
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$qtde_atual= pega_qtde_atual_item($_SESSION["id_empresa"], $rs->id_item);
					$qtde_atual= fnumf($qtde_atual);
					
					if (($i%2)==1) $classe= "cor_sim";
					else $classe= "cor_nao";
					
					$var= "<option class=\"". $classe ."\" value=\"". $rs->id_item ."\">". $rs->item ." - ". $qtde_atual ." ". pega_tipo_apres($rs->tipo_apres) ."</option>";
																				
					echo $var;
					$i++;
				}
			}
			echo "</select>";
		}
		//volta em link da lista
		else {
			
			if (mysql_num_rows($result)==0)
				echo "<li class=\"espacamento vermelho\">Nenhum registro encontrado!</li>";
			else {
				echo "<ul class=\"recuo2\">";
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$qtde_atual= pega_qtde_atual_item($_SESSION["id_empresa"], $rs->id_item, $rs->tipo_apres);
						
					//if ($rs->apelidos!="") $apelidos= "onmouseover=\"Tip('Também conhecido como: ". $rs->apelidos ."');\"";
					//else $apelidos= "";
					
					if ($rs->tipo_apres=="t") $str_acao= "habilitaFormatacaoDecimal(1, 'qtde');";
					else $str_acao= "habilitaFormatacaoDecimal(0, 'qtde');";
					
					switch ($_GET["origem"]) {
						case "s":
								$result_cct= mysql_query("select distinct(id_centro_custo_tipo) from fi_estoque_mov
															where tipo_trans = 'e'
															and   id_item = '". $rs->id_item ."'
															and   id_centro_custo_tipo <> ''
															and   id_centro_custo_tipo <> '0'
															");
								$id_ccts= "@";
								$ccts= "";
								
								while ($rs_cct= mysql_fetch_object($result_cct)) {
									$id_ccts .= $rs_cct->id_centro_custo_tipo ."@";
									$ccts .= pega_centro_custo_tipo($rs_cct->id_centro_custo_tipo) .";<br />";
								}
								
								$str_acao .= "
												atribuiValor('id_item', '". $rs->id_item ."');
												atribuiValor('tit_item', '". $rs->item ."');
												habilitaCampo('qtde');
												habilitaCampo('enviar');
												habilitaCampo('id_deposito');
												atribuiValor('id_ccts', '". $id_ccts ."');
												habilitaCampo('id_motivo');
												atribuiValor('tit_qtde', '". fnumf($qtde_atual) ."');
												atribuiValor('tit_apres', '". pega_tipo_apres($rs->tipo_apres) ."');
											";
						break;
						case "e":
							$str_acao .= "
											atribuiValor('id_item', '". $rs->id_item ."');
											atribuiValor('tit_item', '". $rs->item ."');
											habilitaCampo('qtde');
											habilitaCampo('enviar');
											habilitaCampo('valor_unitario');
											atribuiValor('tit_qtde', '". fnumf($qtde_atual) ."');
											atribuiValor('tit_apres', '". pega_tipo_apres($rs->tipo_apres) ."');
										";
						break;
						case "p":
							$str_acao= "
											ajaxLink('preco_atualiza', 'precoConsulta&id_item=". $rs->id_item ."');
									";
						break;
					}
					
					if ($ccts!="") $ccts_tip= "onmouseover=\"Tip('". $ccts ."');\"";
					else $ccts_tip= "";
					
					$var= "<li><a $ccts_tip href=\"javascript:void(0);\" onclick=\"
																			". $str_acao ."
																			\">
																			". addslashes($rs->item) ."</a></li>";
																				
					echo $var;
					$i++;
				}
				echo "</ul>";
			}
		}//fim modo ul
		
		//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario"], $_SESSION["id_empresa"], 1, "pesquisa produto na telinha, termo: ". $_GET["pesquisa"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
	}
	
	if (isset($_GET["itemDepositoPesquisar"])) {
		
		switch ($_GET["origem"]) {
			//se for saida... soh mostra oq tem no estoque
			case "s":
					$sql= "select * from fi_itens, fi_estoque_deposito
										where (fi_itens.item like '%". $_GET["pesquisa"] ."%'
												or fi_itens.apelidos like '%". $_GET["pesquisa"] ."%')
										and   fi_itens.id_item = fi_estoque_deposito.id_item
										and   fi_estoque_deposito.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   fi_estoque_deposito.id_deposito = '". $_GET["id_deposito"] ."'
										and   fi_estoque_deposito.qtde_atual > '0'
										order by fi_itens.item asc
										";
					break;
		}
		//echo $sql;
		$result= mysql_query($sql) or die("1: ". mysql_error());	
		
		//volta as solicitacoes em select
		if ($_GET["modo"]=="select") {
			echo "<select name=\"id_item[]\" id=\"id_item_". $_GET["cont"] ."\" onchange=\"processaDecimal('". $_GET["cont"] ."'); alteraTipoEstoqueCC('". $_GET["cont"] ."', this.value);\">";
			
			if (mysql_num_rows($result)==0)
				echo "<option value=\"\">Nenhum registro encontrado!</li>";
			else {
				echo "<option value=\"\">---</li>";
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$qtde_atual= pega_qtde_atual_item($_SESSION["id_empresa"], $rs->id_item);
					$qtde_atual= fnumf($qtde_atual);
					
					if (($i%2)==1) $classe= "cor_sim";
					else $classe= "cor_nao";
					
					$var= "<option class=\"". $classe ."\" value=\"". $rs->id_item ."\">". $rs->item ." - ". $qtde_atual ." ". pega_tipo_apres($rs->tipo_apres) ."</option>";
																				
					echo $var;
					$i++;
				}
			}
			echo "</select>";
		}
		//volta em link da lista
		else {
			if (mysql_num_rows($result)==0)
				echo "<li class=\"vermelho\">Nenhum registro encontrado!</li>";
			else {
				echo "<ul class=\"recuo2\">";
				$i=0;
				while ($rs= mysql_fetch_object($result)) {
					$qtde_atual= pega_qtde_atual_item_deposito($_GET["id_deposito"], $rs->id_item, $rs->tipo_apres);
						
					if ($rs->tipo_apres=="t") $str_acao= "habilitaFormatacaoDecimal(1, 'qtde');";
					else $str_acao= "habilitaFormatacaoDecimal(0, 'qtde');";
					
					switch ($_GET["origem"]) {
						case "s":
								$str_acao .= "
												atribuiValor('id_item', '". $rs->id_item ."');
												atribuiValor('tit_item', '". $rs->item ."');
												habilitaCampo('qtde');
												habilitaCampo('enviar');
												habilitaCampo('observacoes');
												habilitaCampo('id_motivo');
												atribuiValor('tit_qtde', '". fnumf($qtde_atual) ."');
												atribuiValor('tit_apres', '". pega_tipo_apres($rs->tipo_apres) ."');
											";
						break;
					}
					
					if ($ccts!="") $ccts_tip= "onmouseover=\"Tip('". $ccts ."');\"";
					else $ccts_tip= "";
					
					$var= "<li><a $ccts_tip href=\"javascript:void(0);\" onclick=\"
																			". $str_acao ."
																			\">
																			". $rs->item ."</a></li>";
																				
					echo $var;
					$i++;
				}
				echo "</ul>";
			}
		}//fim modo ul
		
		//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario"], $_SESSION["id_empresa"], 1, "pesquisa produto na telinha, termo: ". $_GET["pesquisa"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
	}
	
	if (isset($_GET["itemExcluir"])) {
		
		$var=0;
		inicia_transacao();
		
		$result_pre= mysql_query("select * from fi_estoque_mov
										where id_item = '". $_GET["id_item"] ."'
										");

		if (mysql_num_rows($result_pre)==0) {
			$rs= mysql_fetch_object(mysql_query("select * from fi_itens where id_item= '". $_GET["id_item"] ."' "));
			
			$result1= mysql_query("delete from fi_itens
									where id_item = '". $_GET["id_item"] ."'
									limit 1
									");
			if (!$result1) $var++;
		} else $var++;
		
		finaliza_transacao($var);
		
		$msg= $var;
		
		$letra= strtolower(substr($rs->item, 0, 1));
		$pagina= "financeiro/item_esquema";
		require_once("index2.php");
	}
	
	if (isset($_GET["itemInserir"])) {
		if ($_GET["item"]!="") {
			$result_antes= mysql_query("select item from fi_itens
											where item = '". $_GET["item"] ."'
											and   tipo_apres = '". $_GET["tipo_apres"] ."'
											");
	
			if (mysql_num_rows($result_antes)==0) {
				$result= mysql_query("insert into fi_itens (item, tipo_apres, id_centro_custo_tipo, id_usuario)
								values ('". strtoupper($_GET["item"]) ."', '". $_GET["tipo_apres"] ."',
										'". $_GET["id_centro_custo_tipo"] ."', '". $_SESSION["id_usuario"] ."') ");
			}
		}
	
		echo "<script language='javascript' type='text/javascript'>;";
		if ($result) {
			//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 1, "insere remédio, ID ". mysql_insert_id() ." | ". $_POST["remedio"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			echo "atribuiValor('item', '');";
			echo "fechaDiv('item_cadastro');";
			echo "alert('Produto cadastrado com sucesso!');";
		}
		else {
			//@logs($_SESSION["id_acesso"], $_SESSION["id_usuario_sessao"], $_SESSION["id_cidade_sessao"], $_SESSION["id_posto_sessao"], 0, "falha ao inserir remédio, ". $_POST["remedio"], $_SERVER["REMOTE_ADDR"].":".$_SERVER["REMOTE_PORT"], gethostbyaddr($_SERVER["REMOTE_ADDR"]).":".$_SERVER["REMOTE_PORT"]);
			echo "alert('Item já cadastrado!');";
		}
		echo "</script>";
	}
	
	if (isset($_GET["alteraNotaCentroCusto"])) {
		
		$result_nota_item= mysql_query("select * from fi_notas_itens
									   		where id_nota = '". $_GET["id_nota"] ."'
											and   id_nota_item = '". $_GET["id_nota_item"] ."'
											");
		$rs_nota_item= mysql_fetch_object($result_nota_item);
		
		$id_pessoa_nota= pega_id_cedente_nota($_GET["id_nota"]);
		
		if ($_GET["id_centro_custo_tipo"]!="")
			$id_centro_custo_tipo= $_GET["id_centro_custo_tipo"];
		else
			$id_centro_custo_tipo= pega_id_centro_custo_tipo_pelo_id_item($_GET["id_item"]);
		
		if ($id_pessoa_nota!="") {
			$colc= "[]";
			$str_add= "and   id_centro_custo IN
						(
						select fi_cc_ct.id_centro_custo
						from   fi_centro_custos_tipos, fi_cc_ct, fi_pessoas_cc_tipos
						where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
						and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
						and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_pessoas_cc_tipos.id_centro_custo_tipo
						and   fi_pessoas_cc_tipos.id_pessoa = '$id_pessoa_nota'
						 )";
		}
		else $colc= "";
		
		?>
        
        <label>Centro de custo:</label>
        <select name="id_centro_custo<?=$colc;?>" id="id_centro_custo" title="Centro de custo">
			<? /*<option selected="selected" value="">-</option>*/ ?>
			<?
            $result_cc= mysql_query("select * from  fi_centro_custos
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_add
                                        order by centro_custo asc
                                        ") or die(mysql_error());
            while ($rs_cc= mysql_fetch_object($result_cc)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc->id_centro_custo; ?>" <? if ($rs_cc->id_centro_custo==$rs_nota_item->id_centro_custo) echo "selected=\"selected\""; ?>><?= $rs_cc->centro_custo; ?></option>
            <? $i++; } ?>
        </select>
        <br />

        <label>Tipo:</label>
        <select name="id_centro_custo_tipo<?=$colc;?>" id="id_centro_custo_tipo" title="Centro de custo">
			<?
            /*$result_cc= mysql_query("select *
                                        from  fi_centro_custos
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_add
                                        order by centro_custo asc
                                        ") or die(mysql_error());
            while ($rs_cc= mysql_fetch_object($result_cc)) {
            ?>
            <optgroup label="<?= $rs_cc->centro_custo; ?>">
                <?
				*/
                //if ($id_pessoa_nota!="") {
					/*$sql_cc2= "select distinct(fi_centro_custos_tipos.id_centro_custo_tipo)
                                            from  fi_centro_custos_tipos, fi_pessoas_cc_tipos, fi_cc_ct
                                            where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
											and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_pessoas_cc_tipos.id_centro_custo_tipo
											and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
											
											$xstr_add
											
											and   fi_cc_ct.id_centro_custo = '". $rs_cc->id_centro_custo ."'
											
											and   fi_pessoas_cc_tipos.id_pessoa = '$id_pessoa_nota'
                                            order by fi_centro_custos_tipos.centro_custo_tipo asc
                                            ";*/
				//}
				//else
					$sql_cc2= "select distinct(fi_centro_custos_tipos.id_centro_custo_tipo)
                                            from  fi_centro_custos_tipos, fi_cc_ct
                                            where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
											
                                            and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
											/* and   fi_cc_ct.id_centro_custo = '". $rs_cc->id_centro_custo ."' */
                                            order by fi_centro_custos_tipos.centro_custo_tipo asc
                                            ";
				
				$result_cc2= mysql_query($sql_cc2) or die(mysql_error());
				
                $i=0;
                while ($rs_cc2= mysql_fetch_object($result_cc2)) {
                    /*if ($acao=='e') {
                        $result_cc3= mysql_query("select * from fi_pessoas_cc_tipos
                                                    where id_pessoa = '". $rs->id_pessoa ."'
                                                    and   id_centro_custo_tipo = '". $rs_cc2->id_centro_custo_tipo ."'
                                                    ");
                        $linhas_cc3= mysql_num_rows($result_cc3);
                    }*/
                ?>
                <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc2->id_centro_custo_tipo; ?>"<? if ($rs_cc2->id_centro_custo_tipo==$id_centro_custo_tipo) echo "selected=\"selected\""; ?>><?= pega_centro_custo_tipo($rs_cc2->id_centro_custo_tipo); ?></option>
                <? $i++; } ?>
            <? /*</optgroup>
            <? } */ ?>
        </select>
        <br />
        
        <label for="descricao_<?=$k;?>">Descrição:</label>
        <input title="Descrição" name="descricao[]" id="descricao_<?=$k;?>" value="<?= $rs_nota_item->descricao;?>" />
        <br />
        <?
	}
	
	if (isset($_GET["alteraSaidaCentroCustoTipo2"])) {
		if (($_GET["id_ccts"]!="") && ($_GET["id_ccts"]!="@")) {
			$id_ccts= substr($_GET["id_ccts"], 1);
			$id_ccts= substr($_GET["id_ccts"], 1, -1);
			
			$id_cct_vetor= explode("@", $id_ccts);
			
			$str_add_parte= "";
			$i=0;
			while ($id_cct_vetor[$i]) {
				$j= $i+1;
				
				$str_add_parte .= "fi_cc_ct.id_centro_custo_tipo = '". $id_cct_vetor[$i] ."' ";
				
				if ($j!=sizeof($id_cct_vetor)) $str_add_parte .=" or ";
				
				$i++;
			}
		}
		else $str_add_parte= "1=1";
		?>
        <label>Tipo:</label>
        <select name="id_centro_custo_tipo" id="id_centro_custo_tipo" title="Tipo">
			<?
            $result_cc= mysql_query("select *
                                        from  fi_centro_custos_tipos, fi_cc_ct
                                        where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
										and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
										and   fi_cc_ct.id_centro_custo = '". $_GET["id_centro_custo"] ."'
										/* and   ( $str_add_parte ) */
                                        order by fi_centro_custos_tipos.centro_custo_tipo asc
                                        ") or die(mysql_error());
            $i=0;
			while ($rs_cc= mysql_fetch_object($result_cc)) {
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc->id_centro_custo_tipo; ?>"><?= $rs_cc->centro_custo_tipo; ?></option>
            <? $i++; } ?>
        </select>
        <?
	}
	
	if (isset($_GET["alteraSaidaCentroCusto2"])) {
		if (($_GET["id_ccts"]!="") && ($_GET["id_ccts"]!="@")) {
			$id_ccts= substr($_GET["id_ccts"], 1);
			$id_ccts= substr($_GET["id_ccts"], 1, -1);
			
			$id_cct_vetor= explode("@", $id_ccts);
			
			$str_add_parte= "";
			$i=0;
			while ($id_cct_vetor[$i]) {
				$j= $i+1;
				
				$str_add_parte .= "fi_cc_ct.id_centro_custo_tipo = '". $id_cct_vetor[$i] ."' ";
				
				if ($j!=sizeof($id_cct_vetor)) $str_add_parte .=" or ";
				
				$i++;
			}
			
			$str_add= "and   id_centro_custo IN
						(
						select fi_cc_ct.id_centro_custo
						from   fi_centro_custos_tipos, fi_cc_ct
						where fi_centro_custos_tipos.id_empresa = '". $_SESSION["id_empresa"] ."'
						and   fi_centro_custos_tipos.id_centro_custo_tipo = fi_cc_ct.id_centro_custo_tipo
						/* and   ( $str_add_parte ) */
						 )";
		}
		//echo "xx". $_GET["id_ccts"];
		
		$result_cc= mysql_query("select *
                                        from  fi_centro_custos
                                        where id_empresa = '". $_SESSION["id_empresa"] ."'
										$str_add
                                        order by centro_custo asc
                                        ") or die(mysql_error());
		
		?>
        <label>Centro de custo:</label>
        <select name="id_centro_custo" id="id_centro_custo" title="Centro de custo" onchange="pegaCCTipos(this.value);">
			<? if (mysql_num_rows($result_cc)>1) { ?>
            <option value="">---</option>
            <? } ?>
			<?
			$i=0;
			while ($rs_cc= mysql_fetch_object($result_cc)) {
				$id_centro_custo_aqui= $rs_cc->id_centro_custo;
            ?>
            <option <? if ($i%2==0) echo "class=\"cor_sim\""; ?> value="<?= $rs_cc->id_centro_custo; ?>"><?= $rs_cc->centro_custo; ?></option>
            <? $i++; } ?>
        </select>
        
        <? if (mysql_num_rows($result_cc)==1) { ?>
        <script language="javascript">
			pegaCCTipos('<?=$id_centro_custo_aqui;?>');
		</script>
        <? } ?>
    <?
	}
	
}//fim pode estoque

/* ---------------------------------------------------------------------------------------------------- */

echo "</body></html>";

/* <div id="temp">
	<strong>id_usuario:</strong> <?= $_SESSION["id_usuario"]; ?> <br />
	<strong>tipo_usuario:</strong> <?= $_SESSION["tipo_usuario"]; ?> <br />
	<strong>id_empresa:</strong> <?= $_SESSION["id_empresa"]; ?> <br />
	<strong>nome:</strong> <?= $_SESSION["nome"]; ?> <br />
	<strong>permissao:</strong> <?= $_SESSION["permissao"]; ?> <br />
	<strong>trocando:</strong> <?= $_SESSION["trocando"]; ?>
</div>
*/
            
?>