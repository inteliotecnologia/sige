
/* FUNCOES PARA VALIDAR VALORES GENÉRICOSSSS */

function g(quem) {
	return document.getElementById(quem);
}

function buscaTempo() {
	ajaxLink("relogio", "buscaTempo");
}

function resetaTelaPonto() {
	ajaxLink("corpo_ponto", "carregaPagina&pagina=ponto/padrao");
}

function formata_saida(valor, tamanho_saida) {
	valor+="";
	var tamanho= valor.length;
	var saida="";
	
	for (var i=tamanho; i<tamanho_saida; i++)
		saida+='0';
	
	return(saida+valor);
}

function apontaPesagensCliente(id_cliente) {
	if (id_cliente!="") window.top.location.href="./?pagina=op/pesagem_limpa_cliente_listar&id_cliente="+id_cliente;
	else window.top.location.href="./?pagina=op/pesagem_limpa_listar";
}

function checaDeschecaPeso(id_cliente, local) {
	ajaxLink("div_nada", "checaDeschecaPeso&id_cliente="+id_cliente+"&local="+local);
}

function alteraClienteTipo(id_cliente_tipo) {
	
	if (id_cliente_tipo==1) {
		abreDiv("div_lavanderia");
	}
	else {
		fechaDiv("div_lavanderia");
	}
	
}

function alteraSujidadeRelatorioModo(modo, numero) {
	
	if (modo==2) {
		abreDiv("div_clientes_"+numero);
	}
	else {
		fechaDiv("div_clientes_"+numero);
	}
	
}


function checaMesmaDataNota() {
	var data_emissao= g("data_emissao").value;
	var data_vencimento= g("data_vencimento").value;
	
	if (data_vencimento==data_emissao) abreDiv("div_nota_pagar");
	else fechaDiv("div_nota_pagar");
}

function trataAcaoCarreira(id_acao_carreira) {
	//desligamento
	if (id_acao_carreira==2) {
		atribuiValor("validacoes", "id_funcionario@vazio|id_empresa@vazio|data@data|id_acao_carreira@vazio");
		setaClasse("carreira_dados", "escondido");
		setaClasse("carreira_dados_desligamento", "");
	}
	else {
		atribuiValor("validacoes", "id_funcionario@vazio|id_empresa@vazio|data@data|id_acao_carreira@vazio|id_departamento@vazio|id_cargo@vazio");
		setaClasse("carreira_dados", "");
		setaClasse("carreira_dados_desligamento", "escondido");
	}
	
}

function mostraTipoRoupaLavagem(local) {
	var cliente= g("cliente");
	//var id_cliente= g("id_cliente");
	if ((cliente.value=="") && (local==1)) {
		setaClasse("div_id_cliente", "escondido");
		setaClasse("div_id_peca", "");
		
		atribuiValor("id_cliente", "");
		preencheDiv("nome_cliente", "");
		daFoco("id_peca");
	}
	//else setaClasse("div_id_peca", "escondido");

	var id_peca= g("id_peca");
	if ((id_peca.value=="") && (local==2)) {
		setaClasse("div_id_cliente", "");
		setaClasse("div_id_peca", "escondido");
		daFoco("cliente");
	}
	//else if(local==2) setaClasse("div_id_cliente", "escondido");
}

function setaIdHorario(id_horario, tipo, horario, id_funcionario, data1, data2) {
	var edita= g("link_edita_horario");
	edita.setAttribute("onclick", "editaHorario('"+id_horario+"', '"+data1+"', '"+data2+"')");
	
	var exclui= g("link_exclui_horario");
	exclui.setAttribute("onclick", "excluiHorario('"+id_horario+"', '"+tipo+"', '"+horario+"', '"+id_funcionario+"', '"+data1+"', '"+data2+"')");
	
	var tipo_string;
	
	if (tipo=='1') tipo_string= "Entrada";
	else tipo_string= "Saída";
	
	preencheDiv("horario_oque_deseja_fazer", "<center>"+tipo_string +" - "+horario+"</center>");
	
	abreDiv('horario_opcao');
	
	daFoco("link_edita_horario");
}

function buscaRemessasDoDia() {
	var data= g("data_remessa").value;
	ajaxLink("div_remessa_lista", "buscaRemessasDoDia&data="+data);
}

function avaliaReclamacaoAcao(id_livro, id_reclamacao_andamento, campo, nota) {
	var mensagem_adicional;
	//if (parseInt(nota)<=3) mensagem_adicional= "\n\nA RM será reaberta devido a baixa qualificação!";
	//else 
	mensagem_adicional= "";
	
	var confirma= confirm("Tem certeza que deseja dar nota "+nota+" para a resolução da reclamação?"+mensagem_adicional);
	
	if (confirma) {
		window.top.location.href= "link.php?avaliaReclamacaoAcao&id_livro="+id_livro+"&id_reclamacao_andamento="+id_reclamacao_andamento+"&nota="+nota;
	}
	else campo[0].selected= true;
}

function avaliaServicoRM(id_rm, id_rm_andamento, campo, nota) {
	var mensagem_adicional;
	if (parseInt(nota)==0) mensagem_adicional= "\n\nA RM será reaberta devido a baixa qualificação!";
	else mensagem_adicional= "";
	
	var confirma= confirm("Tem certeza que deseja avaliar o serviço executado?"+mensagem_adicional);
	
	if (confirma) {
		window.top.location.href= "link.php?avaliaRM&id_rm="+id_rm+"&id_rm_andamento="+id_rm_andamento+"&nota="+nota;
	}
	else campo[0].selected= true;
}

function avaliaServicoOS(id_os, id_os_andamento, campo, nota) {
	var mensagem_adicional;
	if (parseInt(nota)==0) mensagem_adicional= "\n\nA OS será reaberta devido a baixa qualificação!";
	else mensagem_adicional= "";
	
	var confirma= confirm("Tem certeza que deseja avaliar o serviço executado?"+mensagem_adicional);
	
	if (confirma) {
		window.top.location.href= "link.php?avaliaOS&id_os="+id_os+"&id_os_andamento="+id_os_andamento+"&nota="+nota;
	}
	else campo[0].selected= true;
}

function editaHorario(id_horario, data1, data2) {
	ajaxLink("horario_edita", "carregaPagina&pagina=rh/espelho_form&acao=e&id_horario="+id_horario+"&data1="+data1+"&data2="+data2);
	fechaDiv("horario_opcao");
}

function excluiHorario(id_horario, tipo, horario, id_funcionario, data1, data2) {
	var tipo_string;
	
	atribuiValor("id_horario_exclusao", id_horario);
	
	if (tipo=='1') tipo_string= "Entrada";
	else tipo_string= "Saída";

	preencheDiv("horario_exclui_horario", ""+tipo_string +" - "+ horario);
	atribuiValor("id_funcionario_exclusao", id_funcionario);
	atribuiValor("data1_exclusao", data1);
	atribuiValor("data2_exclusao", data2);
	
	fechaDiv("horario_opcao");
	abreDiv("horario_exclui");
}

function pegaDadosTurnodoFuncionario() {
	var data_escala_troca= g("data_escala_troca");
	var id_funcionario= g("id_funcionario_solicitante");
	
	ajaxLink("turno_atualiza", "pegaDadosTurnodoFuncionario&data_escala_troca="+data_escala_troca.value+"&id_funcionario="+id_funcionario.value);
}

function selecionaPasta(pasta) {
	var id_departamento= g("id_departamento").value;
	if (id_departamento!="") ajaxLink("div_pastas", "carregaPastasdoDepto&id_departamento="+id_departamento+"&pasta="+pasta);
}

function carregaPastas(id_departamento) {
	ajaxLink("div_pastas", "carregaPastasdoDepto&id_departamento="+id_departamento);
}

function carregaPesagensSuja() {
	var data= g("data_pesagem");
	var cliente= g("cliente");
	
	if ((data.value!="") && (cliente.value!="")) {
		ajaxLink("div_remessa_lista", "carregaPesagensSuja&data="+data.value+"&cliente="+cliente.value);
	}
}

function pegaVeiculo(codigo) {
	//alert(tecla);
	if (codigo!="")
		ajaxLink("veiculo_atualiza", "pegaVeiculo&codigo="+codigo);
}

function pegaPercursosVeiculo(codigo, acao) {
	var data_remessa= g("data_remessa").value;
	var id_remessa;
	
	if (acao=="e") id_remessa= g("id_remessa").value;
	else id_remessa=0;
	
	if (codigo!="")
		ajaxLink("id_percurso_atualiza", "pegaPercursosVeiculo&codigo="+codigo+"&data_remessa="+data_remessa+"&id_remessa="+id_remessa);
}

function pegaClienteMultiploSimples(codigo, cont) {
	if (codigo!="") {
		ajaxLink("cliente_atualiza_"+cont, "pegaClienteMultiploSimples&cont="+cont+"&codigo="+codigo);
	}
	else {
		//habilitaCampo("data_remessa");
		//habilitaCampo("num_remessa");
	}
}

function pegaClienteMultiplo(local, cont, codigo) {
	try {
		var id_remessa= g("id_remessa").value;
	}
	catch ( eee ) {
		var id_remessa= 0;
	}
	
	if (codigo!="") {
		ajaxLink("cliente_atualiza_"+local+"_"+cont, "pegaClienteMultiplo&local="+local+"&cont="+cont+"&codigo="+codigo+"&id_remessa="+id_remessa);
		
		if ((local==1) && (cont==1)) {
			atribuiValor("cliente_2_"+cont, codigo);
			ajaxLink("cliente_atualiza_2_"+cont, "pegaClienteMultiplo&local="+local+"&cont="+cont+"&codigo="+codigo+"&id_remessa="+id_remessa);
		}
	}
	else {
		habilitaCampo("data_remessa");
		habilitaCampo("num_remessa");
	}
}

function copiaValorCesto(cont, peso) {
	if (cont==1) {
		atribuiValor("peso_2_"+cont, peso);
	}
}

function pegaCliente(codigo) {
	//alert(tecla);
	try {
		var id_remessa= g("id_remessa").value;
	}
	catch ( eee ) {
		var id_remessa= 0;
	}
	
	if (codigo!="")
		ajaxLink("cliente_atualiza", "pegaCliente&codigo="+codigo+"&id_remessa="+id_remessa);
	else {
		try {
			habilitaCampo("data_remessa");
			habilitaCampo("num_remessa");
		}
		catch (eee) { }
	}
}

function pegaRemessaVetor(cont) {
	var data_remessa= g("data_remessa_"+cont).value;
	var num_remessa= g("num_remessa_"+cont).value;
	
	//alert(tecla);
	if ((data_remessa!="") && (num_remessa!=""))
		ajaxLink("remessa_atualiza_"+cont, "pegaRemessaVetor&data_remessa="+data_remessa+"&num_remessa="+num_remessa+"&cont="+cont);
}


function pegaRemessa() {
	var data_remessa= g("data_remessa").value;
	var num_remessa= g("num_remessa").value;
	
	var acao= document.getElementsByTagName("form");
	var formulario;
	
	if (acao[0].id=="formEmpresaEmular") formulario= acao[1];
	else formulario= acao[0];
	
	acao= formulario.action.split("acao=");
	//alert(acao[1]);
	
	if ((data_remessa!="") && (num_remessa!=""))
		ajaxLink("remessa_atualiza", "pegaRemessa&data_remessa="+data_remessa+"&num_remessa="+num_remessa+"&acao="+acao[1]);
}

function pegaProcesso(codigo) {
	//alert(tecla);
	if (codigo!="")
		ajaxLink("processo_atualiza", "pegaProcesso&codigo="+codigo);
}

function pegaEquipamento(codigo, tipo_equipamento, acao) {
	//alert(tecla);
	if (codigo!="") ajaxLink("equipamento_atualiza", "pegaEquipamento&codigo="+codigo+"&tipo_equipamento="+tipo_equipamento+"&acao="+acao);
}

function finalizaLavagem(id_lavagem) {
	if (id_lavagem!="") ajaxLink("conteudo", "finalizaLavagem&id_lavagem="+id_lavagem);
}

function verificaQtdePacote(id_peca) {
	var qtde= 10;
	if ((id_peca==21) || (id_peca==22)) qtde= 5;
	
	g("qtde_pacote").value= qtde;
}

function pegaNumeroPacotes() {
	var num_pecas= g("num_pecas").value;
	if (num_pecas!="") num_pecas= parseInt(num_pecas);
	
	var qtde_pacote= g("qtde_pacote").value;
	if (qtde_pacote!="") qtde_pacote= parseInt(qtde_pacote);
	
	var num_pacotes= g("num_pacotes");
	
	var total= (num_pecas/qtde_pacote);
	
	if ((num_pecas==0) || (num_pecas=='') || (qtde_pacote==0) || (qtde_pacote=='')) num_pacotes.value=0;
	else num_pacotes.value= Math.floor(total)+1;
}

function calculaValorTotalAbastecimento() {
	var valor_litro= g("valor_litro").value;
	var litros= g("litros").value;
	var valor_total= g("valor_total");
	
	if ( (valor_litro!="") && (litros!="") ) {
		valor_litro= parseFloat(valor_litro.replace(",", "."));
		litros= parseFloat(litros.replace(",", "."));
		
		var conta;
		conta= valor_litro*litros;
		conta= conta+"";
		
		if (conta.indexOf(".")!=-1) {
			conta= conta.replace(".", ",");
			var partes= conta.split(",");
			valor_total.value= partes[0] +","+ partes[1].substr(0, 3);
		}
		else
			valor_total.value= conta;
	}
}

function somaPesos() {
	var peso1= g("peso1").value;
	var peso2= g("peso2").value;
	var peso_total= g("peso_total");
	
	if ( (peso1!="") && (peso2!="") ) {
		peso1= parseFloat(peso1.replace(",", "."));
		peso2= parseFloat(peso2.replace(",", "."));
		
		var soma= peso1+peso2;
		soma= soma+"";
		
		if (soma.indexOf(".")!=-1) {
			soma= soma.replace(".", ",");
			var partes= soma.split(",");
			peso_total.value= partes[0] +","+ partes[1].substr(0, 2);
		}
		else
			peso_total.value= soma;
	}
}

function ajeitaMascaraTelefone(campo) {
	var valor= campo.value;
	
	if ((valor.length==3) && (valor=="(08")) {
		campo.value= "08";
		campo.setAttribute("onkeypress", "return formataCampoVarios(form, this.id, '9999-999-9999', event);");
		campo.setAttribute("maxlength", "13");
	}
	else {
		if ((valor.length==3) && (valor=="(40")) {
			campo.value= "40";
			campo.setAttribute("onkeypress", "return formataCampoVarios(form, this.id, '9999-9999', event);");
			campo.setAttribute("maxlength", "9");
		}
		else {
			if (valor=="") {
				campo.setAttribute("onkeypress", "return formataCampoVarios(form, this.id, '(99) 9999-9999', event);");
				campo.setAttribute("maxlength", "14");
			}
		}
	}
}

function removeDiv(dpai, did) {
	var pai= g(dpai);
	var filho= g(did);
	pai.removeChild(filho);
}

function abreTelinhaAd(cont) {
	var id_cliente= g("id_cliente_"+cont);
	
	abreDiv("assinatura_digital");
	ajaxLink("assinatura_digital_conteudo", "carregaPagina&pagina=transporte/ad_valida&cont="+cont+"&id_cliente="+id_cliente.value);
}

function checaPendencias(tipo, parte, id_departamento) {
	var div= g("div_pendencias_"+tipo+"_"+parte);
	
	if (div.className=="nao_mostra") {
		abreFechaDiv("div_pendencias_"+tipo+"_"+parte);
		ajaxLink("div_pendencias_"+tipo+"_"+parte, "carregaPagina&pagina=com/livro_pendencias&parte_pendencia="+parte+"&tipo_pendencia="+tipo+"&id_departamento="+id_departamento);
	}
	else {
		abreFechaDiv("div_pendencias_"+tipo+"_"+parte);
	}
}

function checaPendencias2(tipo, parte, id_departamento) {
	var div= g("div_pendencias_"+tipo+"_"+parte);
	
	//if (div.className=="nao_mostra") {
		ajaxLink("div_pendencias_"+tipo+"_"+parte, "carregaPagina&pagina=com/livro_pendencias&parte_pendencia="+parte+"&tipo_pendencia="+tipo+"&id_departamento="+id_departamento);
	//}
}

function checaLivroHistoricoCliente(campo) {
	if (campo.checked) {
		ajaxLink("reclamacao_formulario", "carregaPagina&pagina=com/livro_form_reclamacao&historico=1");
	}
	else preencheDiv("reclamacao_formulario", "");
}

function abreFechaReclamacaoFormulario(id_motivo) {
	if ((id_motivo==34) || (id_motivo==37)) {
		
		//if (id_motivo==34) abreDiv("div_enviar_historico");
		//else fechaDiv("div_enviar_historico");
		
		ajaxLink("reclamacao_formulario", "carregaPagina&pagina=com/livro_form_reclamacao&id_motivo="+id_motivo);
	}
	else {
		preencheDiv("reclamacao_formulario", "");
		//fechaDiv("div_enviar_historico");
	}
}

function cancelaRespondeLivro(id_livro, id_departamento, tipo_resposta, resposta, minhas, id_funcionario, parte, id_motivo, resposta_requerida, data1, data2, data, depto_para, id_departamento_principal) {
	preencheDiv('livro_resposta_'+id_livro, '<a href="javascript:void(0);" onclick="respondeLivro(\''+id_livro+'\', \''+id_departamento+'\', \''+tipo_resposta+'\', \''+resposta+'\', \''+minhas+'\', \''+id_funcionario+'\', \''+parte+'\', \''+id_motivo+'\', \''+resposta_requerida+'\', \''+data1+'\', \''+data2+'\', \''+data+'\', \''+depto_para+', \''+id_departamento_principal+'\');">responder</a>');
}

function respondeLivro(id_livro, id_departamento, tipo_resposta, resposta, minhas, id_funcionario, parte, id_motivo, resposta_requerida, data1, data2, data, depto_para, id_departamento_principal) {
	var livro= g("livro_"+id_livro);
	var div_nova;
	
	div_nova= '<form action="form.php?formLivro&acao=i" method="post" onsubmit="return validaFormNormal(\'validacoes_resposta_id_livro_'+id_livro+'\', true, 1);"><label class="tamanho60" for="mensagem_'+id_livro+'">Resposta:</label>'
		/* +'<input type="hidden" name="para[]" class="escondido" value="'+id_departamento+'" />' */
		+'<input type="hidden" name="tipo_resposta" class="escondido" value="'+tipo_resposta+'" />'
		+'<input type="hidden" name="resposta" class="escondido" value="'+resposta+'" />'
		+'<input type="hidden" name="id_livro" class="escondido" value="'+id_livro+'" />'
		
		+'<input type="hidden" id="validacoes_resposta_id_livro_'+id_livro+'" class="escondido"  value="" />'
		
		+'<input type="hidden" name="minhas_busca" class="escondido" value="'+minhas+'" />'
		+'<input type="hidden" name="id_funcionario_busca" class="escondido" value="'+id_funcionario+'" />'
		+'<input type="hidden" name="parte_busca" class="escondido" value="'+parte+'" />'
		+'<input type="hidden" name="id_motivo_busca" class="escondido" value="'+id_motivo+'" />'
		+'<input type="hidden" name="resposta_requerida_busca" class="escondido" value="'+resposta_requerida+'" />'
		+'<input type="hidden" name="data1_busca" class="escondido" value="'+data1+'" />'
		+'<input type="hidden" name="data2_busca" class="escondido" value="'+data2+'" />'
		+'<input type="hidden" name="data_busca" class="escondido" value="'+data+'" />'
		+'<input type="hidden" name="depto_para_busca" class="escondido" value="'+depto_para+'" />'
		+'<input type="hidden" name="id_departamento_principal_busca" class="escondido" value="'+id_departamento_principal+'" />'
		
		 +'<textarea name=\"mensagem\" id="mensagem_'+id_livro+'"></textarea><br /><br />'
		 
		 +'<label class="tamanho300 alinhar_esquerda">Enviar esta resposta para:</label><br />'
		 +'<div id="para_'+id_livro+'"></div>'
		 
		 +'<button type="submit" id="enviar_id_livro_'+id_livro+'">responder</button>'
		 +'<button type="button" id="cancela_id_livro_'+id_livro+'" onclick="cancelaRespondeLivro(\''+id_livro+'\', \''+id_departamento+'\', \''+tipo_resposta+'\', \''+resposta+'\', \''+minhas+'\', \''+id_funcionario+'\', \''+parte+'\', \''+id_motivo+'\', \''+resposta_requerida+'\', \''+data1+'\', \''+data2+'\', \''+data+', \''+depto_para+', \''+id_departamento_principal+'\');">cancelar</button></form><br />';
	
	preencheDiv("livro_resposta_"+id_livro, div_nova);
	
	desabilitaCampo("enviar_id_livro_"+id_livro);
	desabilitaCampo("cancela_id_livro_"+id_livro);
	
	ajaxLink("para_"+id_livro, "carregaRespostaParaLivro&id_livro="+id_livro);
	
	daFoco("resposta_"+id_livro);
}

function processaEntrega(opcao) {
	//entrega extra
	if (opcao==1) {
		g("entrega").value=1;
		//fechaDiv("div_entrega");
		
		abreDiv("div_denominacao");
		g("denominacao")[1].selected=true;
	}
	//entrega normal
	else {
		//abreDiv("div_entrega");
		
		fechaDiv("div_denominacao");
		g("denominacao")[0].selected=true;
		
	}
}

function alteraTipoPercurso(tipo, id_percurso, acao) {
	
	//se for coleta ou entrega
	if ((tipo==1) || (tipo==2) || (tipo==4) || (tipo==5)) {
		ajaxLink("percurso", "alteraTipoPercurso&id_percurso="+id_percurso+"&acao="+acao);
		
		abreDiv("div_regiao");
		
		var tipo_novo;
		
		if ((tipo==1) || (tipo==4)) tipo_novo=1;
		else tipo_novo=2;
		
		ajaxLink("id_veiculo_atualiza", "alteraVeiculos&tipo="+tipo_novo+"&acao="+acao+"&id_percurso="+id_percurso);
		//preencheDiv("livro_resposta_"+id_livro, div_nova);
	}
	else {
		preencheDiv("percurso", "");
		fechaDiv("div_regiao");
		
		ajaxLink("id_veiculo_atualiza", "alteraVeiculos&permite=1");
	}
}

function alteraRegiao(id_regiao, id_percurso, acao) {
	
	ajaxLink("percurso", "alteraTipoPercurso&id_percurso="+id_percurso+"&acao="+acao+"&id_regiao="+id_regiao);
	
}

function pulaParaPagina(parametros, campo) {
	if ((sohNumeros(campo.value)) && (campo.value>0)) {
		var pagina_real= campo.value-1;
		
		window.top.location.href=parametros+pagina_real;
	}
	else alert("Digite uma página válida!");
}

function permiteTodosVeiculosPercurso(campo) {
	if (campo.checked) ajaxLink("id_veiculo_atualiza", "alteraVeiculos&permite=1");
	else {
		var tipo= g("tipo").value;
		ajaxLink("id_veiculo_atualiza", "alteraVeiculos&tipo="+tipo);
	}
}

function procuraPercursos(div) {
	var data= g("data"+div).value;
	var id_cliente= g("id_cliente"+div).value;
	
	if ((id_cliente!="") && (data!="")) ajaxLink("entregas_"+div, "procuraPercursos&id_cliente="+id_cliente+"&data="+data+"&local="+div);
	else preencheDiv("entregas_"+div, "<label>Entrega:</label>---<br /><br />");
}

function checaPercursoExtra(local, id_percurso) {
	if (id_percurso!="") {
		ajaxLink("div_nada", "checaPercursoExtra&id_percurso="+id_percurso+"&local="+local);
	}
}

function criaEspacoPercurso(id_percurso) {
	var espaco= g("espaco");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_nova= document.createElement("div");
	div_nova.id= "div_percurso_"+cont;
	
	//1- passagem em cliente | 2- retorno à empresa
	var tipo_percurso= g("tipo_percurso").value;
	
	//1- coleta | 2- entrega | 3-outros
	var tipo= g("tipo").value;
	
	var aux= ""; var aux2= ""; var titulo= ""; var passo=3;
	
	var remessa= "";
	
	var hoje= new Date();
	var data_atual= formata_saida(hoje.getDate(), 2)+'/'+formata_saida(hoje.getMonth()+1, 2)+'/'+hoje.getFullYear();
	var hora_atual= formata_saida(hoje.getHours(), 2)+':'+formata_saida(hoje.getMinutes(), 2);
	
	if (tipo_percurso==1) {
		aux= '<label class="tamanho50" for="id_cliente_'+cont+'">Cliente:</label>'
			 +'<div id="div_id_cliente_'+cont+'"></div><br />'
			 +'<div id="div_id_ad_'+cont+'"></div>'
             +'';
		aux2= ' | <a href="javascript:void(0);" onclick="abreTelinhaAd('+cont+');">autenticar</a>';
		titulo= 'Passagem em cliente';
		passo=2;
	}
	else {
		aux= '<input type=\"hidden\" class=\"escondido\" name=\"id_cliente[]\" value=\"3\" />';
		aux2= "";
		titulo= 'Retorno à empresa';
		passo=3;
		
		if (tipo==1)
			remessa= '<br /><br /><fieldset><legend>Associar à remessa (Área Suja)</legend>'
						+'<label for="data_remessa">Data/número:</label>'
						+'<input name="data_remessa" id="data_remessa" class="tamanho80 espaco_dir" onkeyup="formataData(this);"  onblur="pegaRemessa();" maxlength="10" value="'+data_atual+'" title="Data da remessa" />'
						+'<input id="num_remessa" name="num_remessa" class="tamanho15p" value="" title="Número da remessa" onblur="pegaRemessa();" />'
						+'<div id="remessa_atualiza">'
                    		+'<input id="id_remessa" name="id_remessa" value="" title="Remessa" class="escondido" />'
                		+'</div>'
						+'</fieldset>';
	}
	
	div_nova.innerHTML= '<fieldset>'
						+'<legend>'+titulo+'</legend>'
						
						+'<code class="escondido"></code>'
						
						+'<input name="id_ad[]" id="id_ad_'+cont+'" type="hidden" class="escondido" value="" />'
						+'<input name="passo[]" id="passo_'+cont+'" type="hidden" class="escondido" value="'+passo+'" />'
						
						+'<label class="tamanho50" for="data_percurso_'+cont+'">Data:</label>'
						+'<input name="data_percurso[]" id="data_percurso_'+cont+'" class="tamanho80" onkeyup="formataData(this);" maxlength="10" value="'+data_atual+'" title="Data" />'
						
						+'<label class="tamanho40" for="hora_percurso_'+cont+'">Hora:</label>'
						+'<input name="hora_percurso[]" id="hora_percurso_'+cont+'" class="tamanho50" onkeyup="formataHora(this);" maxlength="5" value="'+hora_atual+'" title="Hora" />'
						
						+'<label class="tamanho30" for="km_'+cont+'">Km:</label>'
						+'<input name="km[]" id="km_'+cont+'" class="tamanho70" value="" title="Km" />'
						+'<br />'+aux
		
						+'<label class="tamanho50">&nbsp;</label><a href="javascript:removeDiv(\'espaco\', \'div_percurso_'+cont+'\');" onclick="return confirm(\'Tem certeza que deseja remover este registro?\');">remover</a><br />'
						
						+aux2
						
						+remessa
						
					+'</fieldset>'
						
	
	espaco.appendChild(div_nova);
	
	if (tipo_percurso==1) ajaxLink("div_id_cliente_"+cont, "carregaClientesNoPercurso&cont="+cont+"&id_percurso="+id_percurso);
}

function criaEspacoPercursoRemessa() {
	var remessas= g("percurso_remessas");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_remessa= document.createElement("div");
	div_remessa.id= "div_remessa_"+cont;
	
	div_remessa.innerHTML= '<code class="escondido"></code>'
						+'<label for="data_remessa_'+cont+'">Data remessa/num:</label>'
						+'<input id="data_remessa_'+cont+'" name="data_remessa[]" class="espaco_dir tamanho15p" value="" onkeyup="formataData(this);" maxlength="10" title="Data da remessa" />'
						+'<input id="num_remessa_'+cont+'" name="num_remessa[]" class="tamanho10p" value="" title="Número da remessa" onblur="pegaRemessaVetor('+cont+');" />'
						
						+'<div id="remessa_atualiza_'+cont+'">'
                        	+'<input id="id_remessa_'+cont+'" name="id_remessa[]" value="" title="Remessa" class="escondido" />'
	                    +'</div>'
                    
	                    +'<br /><label>&nbsp;</label>'
	                    +'<a href="javascript:void(0);" onclick="removeDiv(\'percurso_remessas\', \'div_remessa_'+cont+'\');">remover</a><br /><br />';
	
	remessas.appendChild(div_remessa);
}

function verificaGrupoRoupa(id_peca) {
	ajaxLink("retorno", "verificaGrupoRoupa&id_peca="+id_peca);
}

function corrigeGrupoRoupa(id_grupo) {
	if (id_grupo==1) {
		var num_divs = document.getElementsByTagName("code");
		var cont= parseInt(num_divs.length);
		var i;
		
		for (i=2; i<=cont; i++)
			removeDiv('pesagens', 'div_pesagem_'+i);
		
		g("adiciona_carrinho").disabled=true;
	}
	else g("adiciona_carrinho").disabled=false;
}

function criaEspacoAnexo() {
	var anexos= g("anexos");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_anexo= document.createElement("div");
	div_anexo.id= "div_anexo_"+cont;
	
	div_anexo.innerHTML= '<code class="escondido"></code>'
						+'<label for="anexo_'+cont+'">Anexo '+cont+':</label>'
						+'<input type="file" class="espaco_dir tamanho25p" title="Anexo" name="anexo[]" id="anexo_'+cont+'" /><br />'
						+'<label>&nbsp;</label>'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'anexos\', \'div_anexo_'+cont+'\');">remover</a><br /><br />';
	
	anexos.appendChild(div_anexo);
}

function pegaDadosVeiculo(id_veiculo) {
	if (id_veiculo!="") ajaxLink("div_veiculo", "pegaDadosVeiculo&id_veiculo="+id_veiculo);
	else preencheDiv("div_veiculo", "");
}

function criaEspacoFilho() {
	var filhos= g("filhos");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_filho= document.createElement("div");
	div_filho.id= "div_filho_"+cont;
	
	div_filho.innerHTML= '<div class="parte33"><code class="escondido"></code>'
						+'<label for="nome_filho_'+cont+'">Nome:</label>'
						+'<input class="" title="Filho" name="nome_filho[]" id="nome_filho_'+cont+'" value="" /><br />'
						
						+'<label for="sexo_filho_'+cont+'">Sexo:</label>'
						+'<select class="" name="sexo_filho[]" id="sexo_filho_'+cont+'">'
							+'<option value="m">Masculino</option>'
							+'<option value="f" class="cor_sim">Feminino</option>'
						+'</select><br />'
						
						+'<label for="data_nasc_filho_'+cont+'">Data de nascimento:</label>'
						+'<input class="" title="Data de nascimento" onkeyup="formataData(this);" maxlength="10" name="data_nasc_filho[]" id="data_nasc_filho_'+cont+'" /><br />'
						
						+'<label>&nbsp;</label>'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'filhos\', \'div_filho_'+cont+'\');">remover</a><br /><br /></div>';
	
	filhos.appendChild(div_filho);
	
	daFoco("nome_filho_"+cont);
}

function calculaTotalPesoCestos() {
	var soma=0;
	var peso;
	var peso_total= g("peso_total");
	
	for (var local=1; local<3; local++) {
		
		//alert("local: "+local);
		
		var lavagens= g("lavagens_"+local);
		var num_divs = lavagens.getElementsByTagName("code");
		var total= parseInt(num_divs.length);
		
		//alert("total: "+total);
		
		for (var cont=1; cont<=total; cont++) {
			
			//alert("cont: "+cont);
			
			peso= g("peso_"+local+"_"+cont).value;
			
			if (peso!='') {
				peso= parseFloat(peso.replace(",", "."));
				soma+=peso;
			}
		}
	}
	
	soma= soma+"";
	
	if (soma.indexOf(".")!=-1) {
		soma= soma.replace(".", ",");
		var partes= soma.split(",");
		peso_total.value= partes[0] +","+ partes[1].substr(0, 2);
	}
	else
		peso_total.value= soma;
	
	//atribuiValor("peso_total", peso_total);
}

function criaEspacoCestoLavagem(cesto) {
	var lavagens= g("lavagens_"+cesto);
	var num_divs = lavagens.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	var tabi=(cont+6);
	
	var div_lavagem= document.createElement("div");
	div_lavagem.id= "div_lavagem_"+cesto+"_"+cont;
	
	div_lavagem.innerHTML= '<code class="escondido"></code>'+ 
							'<input type="hidden" id="id_cesto_'+cesto+'_'+cont+'" name="id_cesto[]" value="'+cesto+'" class="escondido" />'+
							
							'<label for="cliente_'+cesto+'_'+cont+'">* Cliente:</label>'+
							'<input id="cliente_'+cesto+'_'+cont+'" name="cliente_'+cesto+'_'+cont+'" class="tamanho25p espaco_dir" onblur="pegaClienteMultiplo('+cesto+', '+cont+', this.value);" />'+
							'<div id="cliente_atualiza_'+cesto+'_'+cont+'">'+
								'<div id="nome_cliente_'+cesto+'_'+cont+'"></div>'+
								'<input id="id_cliente_'+cesto+'_'+cont+'" name="id_cliente[]" title="Cliente" class="escondido" />'+
							'</div>'+
							'<br />'+
							
							'<label for="peso_'+cesto+'_'+cont+'">* Peso:</label>'+
							'<input id="peso_'+cesto+'_'+cont+'" name="peso[]" class="espaco_dir tamanho25p" onkeydown="formataValor(this,event);" onkeyup="calculaTotalPesoCestos();" title="Peso" />'+
							'<a href="javascript:void(0);" onclick="removeDiv(\'lavagens_'+cesto+'\', \'div_lavagem_'+cesto+'_'+cont+'\'); calculaTotalPesoCestos();">remover</a>'+
							'<br />';
	
	lavagens.appendChild(div_lavagem);
	
	daFoco('cliente_'+cesto+'_'+cont);
}

function criaEspacoSujaPesagem() {
	var pesagens= g("pesagens");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	var tabi=(cont+7);
	var tabi2= tabi+1;
	
	var div_pesagem= document.createElement("div");
	div_pesagem.id= "div_pesagem_"+cont;
	
	div_pesagem.innerHTML= '<code class="escondido"></code>'
						+'<label for="peso_'+cont+'">'+cont+' (peso/hampers):</label>'
						+'<input class="espaco_dir tamanho15p" title="Carrinho" name="peso[]" id="peso_'+cont+'" value="" onkeydown="formataValor(this,event);" />'
						+'<input class="espaco_dir tamanho15p" title="Hampers" name="hampers[]" id="hampers_'+cont+'" value="" />'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'pesagens\', \'div_pesagem_'+cont+'\');">remover</a><br />';
	
	pesagens.appendChild(div_pesagem);
	
	daFoco("peso_"+cont);
}

function criaEspacoLimpaPesagem() {
	var pesagens= g("pesagens");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	var tabi=(cont+7);
	var tabi2= tabi+1;
	
	var div_pesagem= document.createElement("div");
	div_pesagem.id= "div_pesagem_"+cont;
	
	div_pesagem.innerHTML= '<code class="escondido"></code>'
						+'<div id="div_tipo_roupa_'+cont+'"></div>'
						+'<input id="num_pacotes_'+cont+'" name="num_pacotes[]" class="espaco_dir tamanho70" value="" title="Pacotes" />'
						+'<input id="pacotes_sobra_'+cont+'" name="pacotes_sobra[]" class="espaco_dir tamanho70" value="" title="Pacotes sobra" />'
						+'<input id="qtde_pecas_sobra_'+cont+'" name="qtde_pecas_sobra[]" class="espaco_dir tamanho70" value="" title="Sobra" />'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'pesagens\', \'div_pesagem_'+cont+'\');">remover</a><br />';

	pesagens.appendChild(div_pesagem);
	
	ajaxLink("div_tipo_roupa_"+cont, "pegaTipoRoupa&cont="+cont);
}

function criaEspacoSujaLavagem() {
	var pecas= g("pecas");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	var tabi=(cont+7);
	var tabi2= tabi+1;
	
	var div_peca= document.createElement("div");
	div_peca.id= "div_peca_"+cont;
	
	div_peca.innerHTML= '<code class="escondido"></code>'
						+'<div id="div_tipo_roupa_'+cont+'"></div>'
						+'<input id="qtde_pecas_'+cont+'" name="qtde_pecas[]" class="espaco_dir tamanho70" value="" title="Qtde peças" />'
						+'<input id="cliente_'+cont+'" name="cliente[]" class="espaco_dir tamanho70" value="" title="Cliente" onblur="pegaClienteMultiploSimples(this.value, '+cont+');" />'
						
						+'<div id="cliente_atualiza_'+cont+'" class="flutuar_esquerda tamanho100">'
						+'<input type="hidden" class="escondido" name="id_cliente_peca[]" id="id_cliente_peca_'+cont+'" value="" />&nbsp;</div>'
						
						+'<a href="javascript:void(0);" onclick="removeDiv(\'pecas\', \'div_peca_'+cont+'\');">remover</a><br />';

	pecas.appendChild(div_peca);
	
	ajaxLink("div_tipo_roupa_"+cont, "pegaTipoRoupa&cont="+cont+"&todas=1");
}

function criaEspacoCosturaConserto() {
	var pesagens= g("pesagens");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	var tabi=(cont+7);
	var tabi2= tabi+1;
	
	var div_pesagem= document.createElement("div");
	div_pesagem.id= "div_pesagem_"+cont;
	
	div_pesagem.innerHTML= '<code class="escondido"></code>'
						+'<div id="div_tipo_roupa_'+cont+'"></div>'
						+'<input id="qtde_recebido_'+cont+'" name="qtde_recebido[]" class="espaco_dir tamanho80" value="" title="Recebido" />'
						+'<input id="qtde_consertado_'+cont+'" name="qtde_consertado[]" class="espaco_dir tamanho80" value="" title="Consertado" />'
						+'<input id="qtde_substituido_'+cont+'" name="qtde_substituido[]" class="espaco_dir tamanho80" value="" title="Substituído" />'
						+'<input id="qtde_baixa_'+cont+'" name="qtde_baixa[]" class="espaco_dir tamanho80" value="" title="Baixa" />'
						
						+'<div id="div_motivo_costura_'+cont+'"></div>'
						
						+'<a href="javascript:void(0);" onclick="removeDiv(\'pesagens\', \'div_pesagem_'+cont+'\');">remover</a><br />';

	pesagens.appendChild(div_pesagem);
	
	ajaxLink("div_tipo_roupa_"+cont, "pegaTipoRoupa&cont=1");
	ajaxLink("div_motivo_costura_"+cont, "pegaMotivosCostura&cont=1");
	
}

function criaEspacoTelefone() {
	var telefones= g("telefones");
	var num_divs = document.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_telefone= document.createElement("div");
	div_telefone.id= "div_telefone_"+cont;
	
	div_telefone.innerHTML= '<code class="escondido"></code>'
						+'<label for="telefone_'+cont+'">Telefone '+cont+':</label>'
						+'<input class="tamanho25p" title="Telefone" name="telefone[]" id="telefone_'+cont+'" value="" onkeyup="ajeitaMascaraTelefone(this);" onkeypress=\'return formataCampoVarios(form, this.id, "(99) 9999-9999", event);\' maxlength="14" />'
						
						+'<select class="tamanho25p" name="tipo[]" id="tipo_'+cont+'">'
							+'<option value="1">Residencial</option>'
							+'<option value="2" class="cor_sim">Comercial</option>'
							+'<option value="3">Celular</option>'
							+'<option value="4" class="cor_sim">Fax</option>'
							+'<option value="5">Outros</option>'
						+'</select><br />'
						
						+'<label for="obs_'+cont+'">OBS:</label>'
						+'<input class="tamanho25p espaco_dir" title="Observação" name="obs[]" id="obs_'+cont+'" />'
						
						+'<a href="javascript:void(0);" onclick="removeDiv(\'telefones\', \'div_telefone_'+cont+'\');">remover</a><br /><br />';
	
	telefones.appendChild(div_telefone);
	
	daFoco("telefone_"+cont);
}

function criaEspacoNotaParcela() {
	var parcelas= g("parcelas");
	var num_divs = parcelas.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_parcela= document.createElement("div");
	div_parcela.id= "div_parcela_"+cont;
	
	div_parcela.innerHTML= 
						
						'<fieldset><legend>Parcela '+cont+'</legend>'
						+'<code class="escondido"></code>'
						
						+'<label for="data_vencimento_'+cont+'">Data de vencimento:</label>'
						+'<input class="tamanho25p" title="Data de vencimento" name="data_vencimento[]" id="data_vencimento_'+cont+'" value="" onkeyup="formataData(this);" maxlength="10" />'
						+'<br />'
						
						+'<label for="valor_'+cont+'">Valor:</label>'
						+'<input class="tamanho25p" title="Valor da parcela" name="valor[]" id="valor_'+cont+'" value="" onkeydown="formataValor(this,event);" />'
						+'<br />'
						
						+'<label>&nbsp;</label>'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'parcelas\', \'div_parcela_'+cont+'\');">remover</a><br /><br />'
						
						+'</fieldset>';
	
	parcelas.appendChild(div_parcela);
	
	daFoco("data_vencimento_"+cont);
}

function criaEspacoNotaItem(id_nota) {
	var itens= g("itens");
	var num_divs = itens.getElementsByTagName("code");
	var cont= parseInt(num_divs.length+1);
	
	var div_item= document.createElement("div");
	div_item.id= "div_item_"+cont;
	
	div_item.innerHTML= 
						
						'<fieldset><legend>Item '+cont+'</legend>'
						+'<code class="escondido"></code>'
						
						+'<input type="hidden" class="escondido" name="nada[]" value="1" />'
						
						+'<label for="destinacao_'+cont+'">Destinação:</label>'
						+'<select name="destinacao[]" id="destinacao_'+cont+'" onchange="alteraDestinacao('+cont+');">'
						+'<option value="1">Estoque</option>'
						+'<option value="2" class="cor_sim">Centro de custo</option>'
						+'</select>'
						+'<br />'
						
						+'<div id="destinacao_atualiza_'+cont+'">'
						
							+'<label for="item_'+cont+'">Pesquisa:</label>'
							+'<input class="tamanho25p" title="Item" name="item[]" id="item_'+cont+'" value="" class="espaco_dir" onkeyup="itemBusca('+cont+');" />'
							//+'<button type="button" onclick="itemBusca('+cont+');">ok</button>'
							+'<br />'
							
							+'<label for="id_item_'+cont+'">Item:</label>'
							+'<div id="item_atualiza_'+cont+'"><select name="id_item[]" id="id_item_'+cont+'"><option value="">---</option></select></div>'
							+'<br />'
							
							+'<div id="cc_atualiza_'+cont+'" class="escondido"></div>'
							
						+'</div>'
						
						+'<label for="valor_unitario_'+cont+'">Valor unitário:</label>'
						+'<input onkeydown="formataValor(this,event);" class="tamanho25p" title="Valor unitário" name="valor_unitario[]" id="valor_unitario_'+cont+'" value="" />'
						+'<br />'
						
						+'<label for="qtde_'+cont+'">Quantidade:</label>'
						+'<input class="tamanho25p" title="Quantidade" name="qtde[]" id="qtde_'+cont+'" value="" onkeydown="formataValor(this,event);" onblur="calculaValorTotalItemNota('+cont+');" />'
						+'<br />'
						
						+'<label for="valor_total_'+cont+'">Valor total:</label>'
						+'<input class="tamanho25p" title="Valor total" name="valor_total[]" id="valor_total_'+cont+'" value="" onkeydown="formataValor(this,event);" />'
						+'<br />'
						
						+'<label>&nbsp;</label>'
						+'<a href="javascript:void(0);" onclick="removeDiv(\'itens\', \'div_item_'+cont+'\');">remover</a><br /><br />'
						
						+'</fieldset>';
	
	itens.appendChild(div_item);
	ajaxLink("cc_atualiza_"+cont, "alteraNotaCentroCusto&cont="+cont+"&id_nota="+id_nota);
	
	daFoco("item_"+cont);
}

function alteraDestinacao(cont, id_nota) {
	var campo= g("destinacao_"+cont).value;
	var id_nota= g("id_nota").value;
	var div_atualiza= "destinacao_atualiza_"+cont;
	
	//centro de custo
	if (campo==2) {
		setaClasse("cc_atualiza_"+cont, "");
		ajaxLink(div_atualiza, "alteraNotaCentroCusto&cont="+cont+"&id_nota="+id_nota);
	}
	//estoque
	else {
		
		var div_estoque= '<div id="cc_atualiza_'+cont+'"></div>'
						+'<label for="item_'+cont+'">Pesquisa:</label>'
						+'<input class="tamanho25p" title="Item" name="item[]" id="item_'+cont+'" value="" class="espaco_dir" onkeyup="itemBusca('+cont+');" />'
						//+'<button type="button" onclick="itemBusca('+cont+');">ok</button>'
						+'<br />'
						
						+'<label for="id_item_'+cont+'">Item:</label>'
						+'<div id="item_atualiza_'+cont+'"><select name="id_item[]" id="id_item_'+cont+'"><option value="">---</option></select></div>'
						+'<br />';
		preencheDiv(div_atualiza, div_estoque);
		ajaxLink("cc_atualiza_"+cont, "alteraNotaCentroCusto&cont="+cont+"&id_nota="+id_nota);
		
		setaClasse("cc_atualiza_"+cont, "escondido");
	}
}

/*
function alteraSaidaCentroCusto() {
	var campo= g("id_motivo").value;
	var div_atualiza= "destinacao_atualiza";
	
	if (campo=="0") ajaxLink(div_atualiza, "alteraSaidaCentroCusto2");
	else preencheDiv(div_atualiza, "");
}
*/

function alteraSaidaEstoque() {
	var id_motivo= g("id_motivo").value;
	var id_ccts= g("id_ccts").value;
	var div_atualiza= "destinacao_atualiza";
	
	if (id_motivo=="0") {
		ajaxLink(div_atualiza, "alteraSaidaCentroCusto2&id_ccts="+id_ccts);
	}
	else {
		preencheDiv(div_atualiza, "");
		preencheDiv("destinacao_atualiza2", "");
	}
}

function pegaCCTipos(id_centro_custo) {
	var id_ccts= g("id_ccts").value;
	var div_atualiza= "destinacao_atualiza2";
	
	if (id_centro_custo!="") ajaxLink(div_atualiza, "alteraSaidaCentroCustoTipo2&id_ccts="+id_ccts+"&id_centro_custo="+id_centro_custo);
	else preencheDiv(div_atualiza, "");
}

function itemBusca(cont) {
	var campo= g("item_"+cont).value;
	
	if (campo.length>=3) {
		var div_atualiza= "item_atualiza_"+cont;
		
		if ((campo!="") && (campo.length>=3)) ajaxLink(div_atualiza, "itemPesquisar&origem=e&modo=select&pesquisa="+campo+"&cont="+cont);
		else {
			alert("Entre com pelo menos 3 caracteres para fazer a busca!");
			daFoco("item_"+cont);
		}
	}
}

function itemBuscaUnico() {
	var campo= g("item_busca").value;
	
	//alert(campo.length);
	
	if (campo.length>=3) {
		var div_atualiza= "item_atualiza_unico";
		
		if ((campo!="") && (campo.length>=3)) {
			ajaxLink(div_atualiza, "itemPesquisarUnico&origem=e&modo=select&pesquisa="+campo);
		}
		else {
			alert("Entre com pelo menos 3 caracteres para fazer a busca!");
			daFoco("item");
		}
	}
}

function processaDecimalUnico() {
	var id_item= g("id_item");
	var indice= id_item.selectedIndex;
	var texto= id_item[indice].text;
	var apres= texto.substr(-3);
	
	if (apres=="lt.") habilitaFormatacaoDecimal(1, "qtde");
	else habilitaFormatacaoDecimal(0, "qtde");
}

function processaDecimal(cont) {
	var id_item= g("id_item_"+cont);
	var indice= id_item.selectedIndex;
	var texto= id_item[indice].text;
	var apres= texto.substr(-3);
	
	if (apres=="lt.") habilitaFormatacaoDecimal(1, "qtde_"+cont);
	else habilitaFormatacaoDecimal(0, "qtde_"+cont);
}

function habilitaFormatacaoDecimal(opcao, campo) {
	var campo= g(campo);
	
	if (opcao==1) campo.setAttribute("onkeydown", "formataValor(this,event)");
	else campo.setAttribute("onkeydown", "");
}

function calculaValorTotalItemNota(cont) {
	if (cont!="") {
		var valor_unitario= g("valor_unitario_"+cont).value;
		var qtde= g("qtde_"+cont).value;
		var valor_total= g("valor_total_"+cont);
	}
	else {
		var valor_unitario= g("valor_unitario").value;
		var qtde= g("qtde").value;
		var valor_total= g("valor_total");
	}
	var total;
	var formata_aux;
	
	if ( (valor_unitario!="") && (qtde!="") ) {
		formata_aux= valor_unitario.replace(".", "");
		//alert(formata_aux);
		formata_aux= formata_aux.replace(",", ".");
		//alert(formata_aux);
		valor_unitario= parseFloat(formata_aux);
		//valor_unitario= parseFloat(valor_unitario.replace(".", ""));
		//alert(valor_unitario);
		
		qtde= qtde.replace(".", "");
		qtde= qtde.replace(",", ".");
		
		qtde= parseFloat(qtde);
		
		total= valor_unitario*qtde;
		total= total+"";
		
		if (total.indexOf(".")!=-1) {
			total= total.replace(".", ",");
			var partes= total.split(",");
			valor_total.value= partes[0] +","+ partes[1].substr(0, 2);
		}
		else
			valor_total.value= total;
	}
}

function submetePonto(cartao, id_supervisor) {
	var novo= cartao.value;
	
	//alert(novo.length);
	//if(novo.length>=8) {
		//var novo= cartao_string.substring(1,9);
		ajaxLink("corpo_ponto", "submetePonto&cartao="+novo+"&id_supervisor="+id_supervisor);
	//}
}

function checarTudo(local) {
	if (local=="tudo")
		var campos= document.getElementsByTagName("input");
	else {
		var local= g(local);
		var campos= local.getElementsByTagName("input");
	}
	
	for (i=0; i<campos.length; i++) {
		if (campos[i].type=="checkbox") {
			if (campos[i].checked) campos[i].checked= false;
			else campos[i].checked= true;
		}
	}
}

function alteraTipoPessoa(tipo_pessoa, acao) {
	ajaxLink("tipo_pessoa_atualiza", "alteraTipoPessoa&tipo_pessoa="+tipo_pessoa+"&acao="+acao);
}

function desabilitaTudo() {
	var campos_select= document.getElementsByTagName("select");
	for (i=0; i<campos_select.length; i++) {
		if (campos_select[i].className!="escondido") {
			campos_select[i].className= campos_select[i].className+" desativado campo_rel";
			campos_select[i].disabled= true;
		}
	}
	var campos_input= document.getElementsByTagName("input");
	for (i=0; i<campos_input.length; i++) {
		if (campos_input[i].className!="escondido") {
			campos_input[i].className= campos_input[i].className+" desativado campo_rel";
			campos_input[i].disabled= true;
		}
	}
	var campos_textarea= document.getElementsByTagName("textarea");
	for (i=0; i<campos_textarea.length; i++) {
		if (campos_textarea[i].className!="escondido") {
			campos_textarea[i].className= campos_textarea[i].className+" desativado campo_rel";
			campos_textarea[i].disabled= true;
		}
	}
}

function setaClasse(campo, classe) {
	try {
		g(campo).className= classe;
	}
	catch (eee) { }
}

function atribuiAbaAtual(id_elemento, local) {
	var menu= g(local);
	var itens= menu.getElementsByTagName("li");
	
	for (i=0; i<itens.length; i++) {
		if (itens[i].id==id_elemento) {
			itens[i].className= "atual";
			
			var link_dentro= itens[i].getElementsByTagName("a");
			link_dentro[0].blur();
		}
		else itens[i].className= "";
	}
}

function ajeitaTecla(evtKeyPress) {
	if (document.all) { // Internet Explorer
		nTecla = evtKeyPress.keyCode;
		} else if(document.layers) { // Nestcape
			nTecla = evtKeyPress.which;
		} else {
			nTecla = evtKeyPress.which;
			if (nTecla == 8) {
				return true;
			}
		}
	
	if (((nTecla > 47) && (nTecla < 58)) || (nTecla==0) || (nTecla==8))
		return(true);
	else
		return(false);
}

function sohNumeros(numero) {
	var nonNumbers = /\D/;
	if (nonNumbers.test(numero))
		return(false);
	else
		return(true);
}

function limpaValor(valor, validos) {
	var result = "";
	var aux;
	for (var i=0; i < valor.length; i++) {
		aux = validos.indexOf(valor.substring(i, i+1));
		if (aux>=0)
			result += aux;
	}
	return result;
}

//onkeydown="formataValor(this,event);"
function formataValor(campo, teclapres) {
	var tammax = 200;
	var decimal = 2;
	var tecla = teclapres.keyCode;
	vr = limpaValor(campo.value,"0123456789");
	tam = vr.length;
	dec=decimal
	
	if (tam < tammax && tecla != 8){ tam = vr.length + 1 ; }
	
	if (tecla == 8 )
	{ tam = tam - 1 ; }
	
	if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
	{
	
	if ( tam <= dec )
	{ campo.value = vr ; }
	
	if ( (tam > dec) && (tam <= 5) ){
	campo.value = vr.substr( 0, tam - 2 ) + "," + vr.substr( tam - dec, tam ) ; }
	if ( (tam >= 6) && (tam <= 8) ){
	campo.value = vr.substr( 0, tam - 5 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ;
	}
	if ( (tam >= 9) && (tam <= 11) ){
	campo.value = vr.substr( 0, tam - 8 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ; }
	if ( (tam >= 12) && (tam <= 14) ){
	campo.value = vr.substr( 0, tam - 11 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - dec, tam ) ; }
	if ( (tam >= 15) && (tam <= 17) ){
	campo.value = vr.substr( 0, tam - 14 ) + "." + vr.substr( tam - 14, 3 ) + "." + vr.substr( tam - 11, 3 ) + "." + vr.substr( tam - 8, 3 ) + "." + vr.substr( tam - 5, 3 ) + "," + vr.substr( tam - 2, tam ) ;}
	}
} 

//onkeyup="formataData(this);"
function formataData(val) {
	var pass = val.value;
	var expr = /[0123456789]/;
		
	for(i=0; i<pass.length; i++){
		var lchar = val.value.charAt(i);
		var nchar = val.value.charAt(i+1);
	
		if(i==0) {
		   if ((lchar.search(expr) != 0) || (lchar>3)){
			  val.value = "";
		   }
		   
		} else if(i==1){
			   
			   if(lchar.search(expr) != 0){
				  var tst1 = val.value.substring(0,(i));
				  val.value = tst1;				
				  continue;			
			   }
			   
			   if ((nchar != '/') && (nchar != '')){
					var tst1 = val.value.substring(0, (i)+1);
				
					if(nchar.search(expr) != 0) 
						var tst2 = val.value.substring(i+2, pass.length);
					else
						var tst2 = val.value.substring(i+1, pass.length);
	
					val.value = tst1 + '/' + tst2;
			   }

		 }else if(i==4){
			
				if(lchar.search(expr) != 0){
					var tst1 = val.value.substring(0, (i));
					val.value = tst1;
					continue;			
				}
		
				if	((nchar != '/') && (nchar != '')){
					var tst1 = val.value.substring(0, (i)+1);

					if(nchar.search(expr) != 0) 
						var tst2 = val.value.substring(i+2, pass.length);
					else
						var tst2 = val.value.substring(i+1, pass.length);
	
					val.value = tst1 + '/' + tst2;
				}
		  }
		
		  if(i>=6) {
			  if(lchar.search(expr) != 0) {
					var tst1 = val.value.substring(0, (i));
					val.value = tst1;			
			  }
		  }
	 }
	
	 if(pass.length>10)
		val.value = val.value.substring(0, 10);
		return true;
}

function formataHora(val) {
	var pass = val.value;
	var expr = /[0123456789]/;
		
	for(i=0; i<pass.length; i++){
		var lchar = val.value.charAt(i);
		var nchar = val.value.charAt(i+1);
	
		if(i==0) {
		   if ((lchar.search(expr) != 0) || (lchar>3)){
			  val.value = "";
		   }
		   
		} else if(i==1){
			   
			   if(lchar.search(expr) != 0){
				  var tst1 = val.value.substring(0,(i));
				  val.value = tst1;				
				  continue;			
			   }
			   
			   if ((nchar != ':') && (nchar != '')){
					var tst1 = val.value.substring(0, (i)+1);
				
					if(nchar.search(expr) != 0) 
						var tst2 = val.value.substring(i+2, pass.length);
					else
						var tst2 = val.value.substring(i+1, pass.length);
	
					val.value = tst1 + ':' + tst2;
			   }

		 }else if(i==4){
			
				if(lchar.search(expr) != 0){
					var tst1 = val.value.substring(0, (i));
					val.value = tst1;
					continue;			
				}
		
				if	((nchar != ':') && (nchar != '')){
					var tst1 = val.value.substring(0, (i)+1);

					if(nchar.search(expr) != 0) 
						var tst2 = val.value.substring(i+2, pass.length);
					else
						var tst2 = val.value.substring(i+1, pass.length);
	
					val.value = tst1 + ':' + tst2;
				}
		  }
		
		  if(i>=6) {
			  if(lchar.search(expr) != 0) {
					var tst1 = val.value.substring(0, (i));
					val.value = tst1;			
			  }
		  }
	 }
	
	 if(pass.length>10)
		val.value = val.value.substring(0, 10);
		return true;
}


/* ------------------------------------------------------------------------------------------------ */

function retornaDataFinal(data_inicial, qtde_dias) {
	var data_inicial= g("data_inicial_abono").value;
	var qtde_dias= g("qtde_dias").value;
	
	if ( (data_inicial!="") && (qtde_dias!="") )
		ajaxLink('resultado_data', 'retornaDataFinal&data_inicial='+data_inicial+'&qtde_dias='+qtde_dias);
}

function alteraBancoHorasFuncionario(id_funcionario, data1, data2) {
	ajaxLink('conteudo_interno', 'carregaPagina&pagina=rh/banco&id_funcionario='+id_funcionario+'&data1='+data1+'&data2='+data2);
}

function alteraEspelhoFuncionario(id_funcionario, data1, data2) {
	ajaxLink('conteudo_interno', 'carregaPagina&pagina=rh/espelho&id_funcionario='+id_funcionario+'&data1='+data1+'&data2='+data2);
}

function batidaAutomatica(id_dia, data, id_funcionario, data1, data2) {
	ajaxLink('conteudo_interno', 'batidaAutomatica&id_dia='+id_dia+'&data='+data+'&id_funcionario='+id_funcionario+'&data1='+data1+'&data2='+data2);
}

function batidaIntervaloFind(id_dia, data, id_funcionario, data1, data2) {
	ajaxLink('conteudo_interno', 'batidaIntervaloFind&id_dia='+id_dia+'&data='+data+'&id_funcionario='+id_funcionario+'&data1='+data1+'&data2='+data2);
}

function batidaIntervaloNormal(id_dia, data, id_funcionario, data1, data2) {
	ajaxLink('conteudo_interno', 'batidaIntervaloNormal&id_dia='+id_dia+'&data='+data+'&id_funcionario='+id_funcionario+'&data1='+data1+'&data2='+data2);
}

function alteraCidade(div, id_uf, nome_campo) {
	desabilitaCampo("enviar");
	var id_uf = g(id_uf);
	ajaxLink(div, "alteraCidade&id_uf="+id_uf.value+"&nome_campo="+nome_campo);
}

function alteraDepartamentos(condicao) {
	desabilitaCampo("enviar");
	var id_empresa = g("id_empresa");
	ajaxLink("id_departamento_atualiza", "alteraDepartamentos&id_empresa="+id_empresa.value+"&condicao="+condicao);
}

function alteraDepartamentos2(id_empresa, div, condicao) {
	desabilitaCampo("enviar");
	ajaxLink("id_departamento_atualiza_"+div, "alteraDepartamentos&id_empresa="+id_empresa+"&condicao="+condicao);
}

function alteraPastas() {
	desabilitaCampo("enviar");
	var id_departamento = g("id_departamento");
	ajaxLink("id_pasta_atualiza", "alteraPastas&id_departamento="+id_departamento.value);
}

function verificaCartao(numero_cartao) {
	var id_funcionario= g("id_funcionario").value;
	ajaxLink("cartao_atualiza", "verificaCartao&numero_cartao="+numero_cartao+"&id_funcionario="+id_funcionario);
}

function verificaCRM(crm) {
	ajaxLink("nome_medico_area", "verificaCrm&crm="+crm);
}

function verificaTelefone(telefone) {
	ajaxLink("para_area", "verificaTelefone&telefone="+telefone);
}


function retornaCid(condicao) {
	var pesquisa_cid = g("pesquisa_cid");
	if (pesquisa_cid.value!="") {
		desabilitaCampo("enviar");
		ajaxLink("cid_atualiza", "retornaCid&pesquisa_cid="+pesquisa_cid.value);
	}
}

function desconsideraProducaoDia(campo, i, t, data, id_departamento) {
	var desconsidera;
	
	if (campo.checked) desconsidera=1;
	else desconsidera=0;
	
	ajaxLink("div_desconsiderar_"+i+"_"+t, "desconsideraProducaoDia&data="+data+"&t="+t+"&&id_departamento="+id_departamento+"&desconsidera="+desconsidera);
}

function alteraClientesAtivosInativos(status_pessoa, div) {
	//desabilitaCampo("enviar");
	ajaxLink(div, "alteraClientesAtivosInativos&status_pessoa="+status_pessoa);
}

function alteraFuncionariosAtivosInativos(status_funcionario) {
	desabilitaCampo("enviar");
	ajaxLink("id_funcionario_atualiza", "alteraFuncionariosAtivosInativos&status_funcionario="+status_funcionario);
}

function alteraFuncionarios() {
	desabilitaCampo("enviar");
	var id_empresa = g("id_empresa");
	ajaxLink("id_funcionario_atualiza", "alteraFuncionarios&id_empresa="+id_empresa.value);
}

function alteraPessoas() {
	desabilitaCampo("enviar");
	var id_empresa = g("id_empresa");
	ajaxLink("id_pessoa_atualiza", "alteraPessoas&id_empresa="+id_empresa.value);
}


function alteraCargos() {
	desabilitaCampo("enviar");
	var id_departamento = g("id_departamento");
	ajaxLink("id_cargo_atualiza", "alteraCargos&id_departamento="+id_departamento.value);
}

function alteraTurnosSoh() {
	//desabilitaCampo("enviar");
	var id_departamento = g("id_departamento");
	ajaxLink("id_turno_atualiza", "alteraTurnos&id_departamento="+id_departamento.value+"&soh=1");
}

function alteraTurnosSohMultiplo(id_departamento, lugar) {
	ajaxLink("id_turno_atualiza_"+lugar, "alteraTurnos&id_departamento="+id_departamento+"&soh=1");
}

function alteraTurnos() {
	desabilitaCampo("enviar");
	var id_departamento = g("id_departamento");
	ajaxLink("id_turno_atualiza", "alteraTurnos&id_departamento="+id_departamento.value);
}

function alteraIntervalos() {
	desabilitaCampo("enviar");
	var id_turno = g("id_turno");
	ajaxLink("id_intervalo_atualiza", "alteraIntervalos&id_turno="+id_turno.value);
}

function atualizaHorarioTurno(horario, tipo, id_turno_horario) {
	desabilitaCampo("enviar");
	ajaxLink("ajax_rotina", "atualizaHorarioTurno&horario="+horario+"&tipo="+tipo+"&id_turno_horario="+id_turno_horario);
}

function pegaHorario(id_turno, tipo) {
	desabilitaCampo("enviar");
	var id_dia= g("id_dia").value;
	
	ajaxLink("horario_"+tipo+"_atualiza", "pegaHorario&id_turno="+id_turno+"&id_dia="+id_dia+"&tipo="+tipo);
}



function pegaQtdeDiasPeloMotivo() {
	var id_motivo = g("id_motivo").value;
	var valor= id_motivo.split('@', 2);
	
	preencheDiv("qtde_dias_atualiza", valor[1]);
	atribuiValor("qtde_dias", valor[1]);
}

function cadastraNovoTipoPessoa(id_pessoa, tipo_pessoa) {
	var confirma= confirm("Tem certeza que deseja associar\nesta pessoa nesta nova categoria?");
	
	if (confirma)
		ajaxLink("conteudo", "cadastraNovoTipoPessoa&id_pessoa="+id_pessoa+"&tipo_pessoa="+tipo_pessoa);
}

function verificaCnpj(acao) {
	var cnpj= g("cnpj");
	var cnpj_teste= validaCnpj(cnpj.value);
	var tipo_pessoa= g("tipo_pessoa");
	
	if (cnpj_teste.length==0) {
		//inserção
		if (acao=='i')
			ajaxLink("cnpj_testa", "verificaCnpj&cnpj="+cnpj.value+"&tipo_pessoa="+tipo_pessoa.value);
		//edicao
		else {
			var id_pessoa= g("id_pessoa");
			ajaxLink("cnpj_testa", "verificaCnpj&cnpj="+cnpj.value+"&id_pessoa="+id_pessoa.value+"&tipo_pessoa="+tipo_pessoa.value);
		}
	}
	else {
		var span_cnpj_testa= g("span_cnpj_testa");
		span_cnpj_testa.className= "vermelho";
		span_cnpj_testa.innerHTML= cnpj_teste;
	}
}

function verificaCpf(acao, local) {
	var cpf= g("cpf");
	var cpf_teste= validaCpf(cpf.value);
	var tipo_pessoa= g("tipo_pessoa");
	
	if (cpf_teste.length==0) {
		//inserção
		if (acao=='i')
			ajaxLink("cpf_testa", "verificaCpf&cpf="+cpf.value+"&tipo_pessoa="+tipo_pessoa.value);
		//edicao
		else {
			var id_pessoa= g("id_pessoa");
			ajaxLink("cpf_testa", "verificaCpf&cpf="+cpf.value+"&id_pessoa="+id_pessoa.value+"&tipo_pessoa="+tipo_pessoa.value);
		}
	}
	else {
		var span_cpf_testa= g("span_cpf_testa");
		span_cpf_testa.className= "vermelho";
		span_cpf_testa.innerHTML= cpf_teste;
	}
}

function bloqueaCampos(div) {
	var area= g(div);
	
	var campos= area.getElementsByTagName("input");
	for (i=0; i<campos.length; i++) campos[i].disabled= true;
	
	var campos= area.getElementsByTagName("select");
	for (i=0; i<campos.length; i++) campos[i].disabled= true;
	
	var campos= area.getElementsByTagName("textarea");
	for (i=0; i<campos.length; i++) campos[i].disabled= true;
}

function alteraTipoRM(opcao) {
	if (opcao=="e") { setaClasse("div_id_equipamento", ""); setaClasse("div_item", "escondido"); }
	else { setaClasse("div_id_equipamento", "escondido"); setaClasse("div_item", ""); }
}

function alteraLocalOS(opcao) {
	if (opcao=="2") { setaClasse("div_clientes", ""); }
	else { setaClasse("div_clientes", "nao_mostra"); }
}

function alteraCamadasEquipamento() {
	var id_servico= g("id_servico");
	var equipamentos= g("equipamentos");
	
	if (id_servico.value!="")
		equipamentos.className= "avista";
	else
		equipamentos.className= "escondido";
		
}

function ativaDesativa(id_campo) {
	var campo= g(id_campo);
	
	if (campo.className=="desativado") {
		//campo.disabled=false;
		campo.className= campo.className+" ativado";
	}
	else {
		//campo.disabled=true;
		campo.value= "";
		campo.className=  campo.className+" desativado";
	}
}

function ativaDesativaData(valor, id_campo) {
	var campo= g(id_campo);
	
	if (parseInt(valor)==0) {
		campo.disabled=true;
		campo.value= "";
		campo.className= "desativado";
	}
	else {
		campo.disabled=false;
		campo.className= "ativado";
	}
}

function alteraTipoTecnico(tipo_tecnico) {
	if (tipo_tecnico==1) outro=2;
	else outro=1;
	
	abreDiv("tipo_tecnico_"+tipo_tecnico);
	fechaDiv("tipo_tecnico_"+outro);
}

function alteraTipoContatoFuncionario(tipo_contato) {
	if (tipo_contato=="2") abreDiv("funcionario_identificacao");
	else fechaDiv("funcionario_identificacao");
}

function abreDiv(div) {
	var div_mesmo= g(div);
	div_mesmo.style.display="block";
}

function abreFechaDiv(div) {
	var div_mesmo= g(div);
	
	if ((div_mesmo.className=="nao_mostra") || (div_mesmo.className=="escondido")) {
		div_mesmo.style.display=="block";
		div_mesmo.className= "mostra";
	}
	else {
		div_mesmo.style.display=="none";
		div_mesmo.className= "nao_mostra";
	}
}

function fechaDiv(div) {
	var div_mesmo= g(div);
	div_mesmo.style.display="none";
}

function preencheDiv(div, conteudo) {
	var div_mesmo= g(div);
	div_mesmo.innerHTML=conteudo;
}

function checaCampo(campo) {
	var campo_dest= g(campo);
	campo_dest.checked= true;
}

function atribuiValor(campo, valor) {
	var campo_dest= g(campo);
	campo_dest.value= valor;
}

function daFoco(campo) {
	try {
		g(campo).focus();
	} catch (eee) { }
}
function daBlur(campo) {
	g(campo).blur();
}

/* ------------------------------------------------------------------------------------------------ */

function validaCpf(cpf) {
	 var strcpf = cpf;
	 var str_aux = "";
	 var erros= "";
	 
	 for (i = 0; i <= strcpf.length - 1; i++)
	   if ((strcpf.charAt(i)).match(/\d/))
		 str_aux += strcpf.charAt(i);
	   else if (!(strcpf.charAt(i)).match(/[\.\-]/)) {
		 erros += "Apenas números no campo CPF!\n";
		 break;
		 //return false;
	   }

	 if (str_aux.length < 11) {
	   erros += "O campo CPF deve conter 11 dígitos!\n";
	   //return false;
	 }
	 else {
		 soma1 = soma2 = 0;
		 for (i = 0; i <= 8; i++) {
		   soma1 += str_aux.charAt(i) * (10-i);
		   soma2 += str_aux.charAt(i) * (11-i);
		 }
		 d1 = ((soma1 * 10) % 11) % 10;
		 d2 = (((soma2 + (d1 * 2)) * 10) % 11) % 10;
		 if ((d1 != str_aux.charAt(9)) || (d2 != str_aux.charAt(10))) {
		   erros += "O CPF digitado é inválido!\n";
		   //return false;
		 }
		  if ((cpf=="00000000000") || (cpf=="11111111111") || (cpf=="22222222222") || (cpf=="33333333333") || 
		  (cpf=="44444444444") || (cpf=="55555555555") || (cpf=="66666666666") || (cpf=="77777777777") || 
		  (cpf=="88888888888") || (cpf=="99999999999") ) {
		   erros += "O CPF digitado é inválido!!\n";
		   //return false;
		 }
	 }
	 return (erros);
}

function validaCnpj(CNPJ) {
	 erro = new String;
	 if (CNPJ.length < 18)
	 	erro = "CNPJ inválido!";
	 if ((CNPJ.charAt(2) != ".") || (CNPJ.charAt(6) != ".") || (CNPJ.charAt(10) != "/") || (CNPJ.charAt(15) != "-")){
	 if (erro.length == 0)
	 	erro = "CNPJ inválido!";
	 }
	 //substituir os caracteres que não são números
   if(document.layers && parseInt(navigator.appVersion) == 4){
		   x = CNPJ.substring(0,2);
		   x += CNPJ. substring (3,6);
		   x += CNPJ. substring (7,10);
		   x += CNPJ. substring (11,15);
		   x += CNPJ. substring (16,18);
		   CNPJ = x;
   } else {
		   CNPJ = CNPJ. replace (".","");
		   CNPJ = CNPJ. replace (".","");
		   CNPJ = CNPJ. replace ("-","");
		   CNPJ = CNPJ. replace ("/","");
   }
   var nonNumbers = /\D/;
   if (nonNumbers.test(CNPJ))
   	  if (erro.length == 0)	
		erro += "O campo CNPJ suporta apenas números!";
	
   var a = [];
   var b = new Number;
   var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
   for (i=0; i<12; i++){
		   a[i] = CNPJ.charAt(i);
		   b += a[i] * c[i+1];
   }
   if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
   b = 0;
   for (y=0; y<13; y++) {
		   b += (a[y] * c[y]);
   }
   if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
   if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
	   if (erro.length == 0)
		   erro = "CNPJ inválido!";
   }
   return(erro);
}

function validaData(data, tipo) {
	var retorno=true;
	if (data=="") {
		retorno=false;
	}
	else {
		var dia= data.substring(0, 2);
		var mes= data.substring(3, 5);
		var ano= data.substring(6, 10);
		
		var barra1= data.substring(2, 3);
		var barra2= data.substring(5, 6);
		
		if ((barra1=="/") && (barra2=="/")) {
			var nonNumbers = /\D/;
						
			if ( (dia<=0) || (dia>31)  || (nonNumbers.test(dia)) )
				retorno=false;
			/*else {
				if ( ((mes=="02") || (mes=="04") || (mes=="06") || (mes=="09") || (mes=="11")) && (dia=="31") )
					retorno=false;
			}*/
			
			if ( (mes<=0) || (mes>12)  || (nonNumbers.test(mes)) )
				retorno=false;
			
			if (tipo==2) {
				var dataAtual= new Date();
				var anoAtual= dataAtual.getFullYear();
				
				if ( (ano<=0) || (ano>anoAtual) || (nonNumbers.test(ano)) )
					retorno=false;
			}
			
			//ano bissexto
			if ((ano%4!=0) && (mes==2) && (dia>28))
				retorno=false;
		}
		else
			retorno=false;
	}
	return(retorno);
}

/***
* Descrição.: formata um campo do formulário de
* acordo com a máscara informada...
* Parâmetros: - objForm (o Objeto Form)
* - strField (string contendo o nome
* do textbox)
* - sMask (mascara que define o
* formato que o dado será apresentado,
* usando o algarismo "9" para
* definir números e o símbolo "!" para
* qualquer caracter...
* - evtKeyPress (evento)
* Uso.......: <input type="textbox"
* name="xxx".....
* onkeypress="return txtBoxFormat(document.rcfDownload, 'str_cep', '99999-999', event);">
* Observação: As máscaras podem ser representadas como os exemplos abaixo:
* CEP -> 99.999-999
* CPF -> 999.999.999-99
* CNPJ -> 99.999.999/9999-99
* Data -> 99/99/9999
* Tel Resid -> (99) 999-9999
* Tel Cel -> (99) 9999-9999
* Processo -> 99.999999999/999-99
* C/C -> 999999-!
* E por aí vai...
***/

function formataCampo(objForm, strField, sMask, evtKeyPress) {
  var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;

  if(document.all) { // Internet Explorer
    nTecla = evtKeyPress.keyCode;
	} else if(document.layers) { // Nestcape
		nTecla = evtKeyPress.which;
	} else {
		nTecla = evtKeyPress.which;
		//alert(nTecla);
		if ((nTecla==8) || (nTecla==0))
			return true;
	}

  sValue = objForm[strField].value;

  // Limpa todos os caracteres de formatação que
  // já estiverem no campo.
  sValue = sValue.toString().replace( "-", "" );
  sValue = sValue.toString().replace( "-", "" );
  sValue = sValue.toString().replace( ".", "" );
  sValue = sValue.toString().replace( ".", "" );
  sValue = sValue.toString().replace( "/", "" );
  sValue = sValue.toString().replace( "/", "" );
  sValue = sValue.toString().replace( "(", "" );
  sValue = sValue.toString().replace( "(", "" );
  sValue = sValue.toString().replace( ")", "" );
  sValue = sValue.toString().replace( ")", "" );
  sValue = sValue.toString().replace( " ", "" );
  sValue = sValue.toString().replace( " ", "" );
  fldLen = sValue.length;
  mskLen = sMask.length;

  i = 0;
  nCount = 0;
  sCod = "";
  mskLen = fldLen;

  while (i <= mskLen) {
	bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == "/"))
	bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " "))

	if (bolMask) {
	  sCod += sMask.charAt(i);
	  mskLen++; }
	else {
	  sCod += sValue.charAt(nCount);
	  nCount++;
	}

	i++;
  }

  objForm[strField].value = sCod;

  if (nTecla != 8) { // backspace
	if (sMask.charAt(i-1) == "9") { // apenas números...
	  return ((nTecla > 47) && (nTecla < 58)); } // números de 0 a 9
	else { // qualquer caracter...
	  return true;
	} }
  else {
	return true;
  }
}

function formataCampoVarios(objForm, idCampo, sMask, evtKeyPress) {
  var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;

  if(document.all) { // Internet Explorer
    nTecla = evtKeyPress.keyCode;
	} else if(document.layers) { // Nestcape
		nTecla = evtKeyPress.which;
	} else {
		nTecla = evtKeyPress.which;
		//alert(nTecla);
		if ((nTecla==8) || (nTecla==0))
			return true;
	}
  
  var campo = g(idCampo);
  var sValue = campo.value;

  // Limpa todos os caracteres de formatação que
  // já estiverem no campo.
  sValue = sValue.toString().replace( "-", "" );
  sValue = sValue.toString().replace( "-", "" );
  sValue = sValue.toString().replace( ".", "" );
  sValue = sValue.toString().replace( ".", "" );
  sValue = sValue.toString().replace( "/", "" );
  sValue = sValue.toString().replace( "/", "" );
  sValue = sValue.toString().replace( "(", "" );
  sValue = sValue.toString().replace( "(", "" );
  sValue = sValue.toString().replace( ")", "" );
  sValue = sValue.toString().replace( ")", "" );
  sValue = sValue.toString().replace( " ", "" );
  sValue = sValue.toString().replace( " ", "" );
  fldLen = sValue.length;
  mskLen = sMask.length;

  i = 0;
  nCount = 0;
  sCod = "";
  mskLen = fldLen;

  while (i <= mskLen) {
	bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == "/"))
	bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " "))

	if (bolMask) {
	  sCod += sMask.charAt(i);
	  mskLen++; }
	else {
	  sCod += sValue.charAt(nCount);
	  nCount++;
	}

	i++;
  }

  campo.value = sCod;

  if (nTecla != 8) { // backspace
	if (sMask.charAt(i-1) == "9") { // apenas números...
	  return ((nTecla > 47) && (nTecla < 58)); } // números de 0 a 9
	else { // qualquer caracter...
	  return true;
	} }
  else {
	return true;
  }
}


function validaCnpj(CNPJ) {
	 erro = new String;
	 if (CNPJ.length < 18)
	 	erro = "CNPJ inválido!";
	 if ((CNPJ.charAt(2) != ".") || (CNPJ.charAt(6) != ".") || (CNPJ.charAt(10) != "/") || (CNPJ.charAt(15) != "-")){
	 if (erro.length == 0)
	 	erro = "CNPJ inválido!";
	 }
	 //substituir os caracteres que não são números
   if(document.layers && parseInt(navigator.appVersion) == 4){
	   x = CNPJ.substring(0,2);
	   x += CNPJ. substring (3,6);
	   x += CNPJ. substring (7,10);
	   x += CNPJ. substring (11,15);
	   x += CNPJ. substring (16,18);
	   CNPJ = x;
   }
   else {
	   CNPJ = CNPJ. replace (".","");
	   CNPJ = CNPJ. replace (".","");
	   CNPJ = CNPJ. replace ("-","");
	   CNPJ = CNPJ. replace ("/","");
   }
   var nonNumbers = /\D/;
   if (nonNumbers.test(CNPJ))
   	  if (erro.length == 0)	
		erro += "O campo CNPJ suporta apenas números!";
	
   var a = [];
   var b = new Number;
   var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
   for (i=0; i<12; i++){
		   a[i] = CNPJ.charAt(i);
		   b += a[i] * c[i+1];
   }
   if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
   b = 0;
   for (y=0; y<13; y++) {
		   b += (a[y] * c[y]);
   }
   if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
   if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
	   if (erro.length == 0)
		   erro = "CNPJ inválido!";
   }
   return(erro);
}

function validaEmail(email) {
	var retorno= true;
	
	if (email=="")
		retorno= false;
	if (email.indexOf("@") < 2)
		retorno= false;
	if (email.indexOf(".") < 1)
		retorno= false;
	
	return(retorno);
}

function desabilitaCampo(id_elemento) {
	g(id_elemento).disabled=true;
}

function habilitaCampo(id_elemento) {
	g(id_elemento).disabled=false;
}

function pegaTitle(campo) {
	var titulo= g(campo).title;
	
	if (titulo=="") titulo= g(campo).id;
	return(titulo);
}

function pegaValor(campo) {
	return(g(campo).value);
}

/*
---------------------------------------------------------------------
---------------------------------------------------------------------
------------- FUNCOES PARA VALIDAR FORMULARIOS ---------------------
---------------------------------------------------------------------
---------------------------------------------------------------------
*/

function estaVazio(nome_elemento) {
	var valor= g(nome_elemento).value;
	if (valor=="") return true;
	else return false;
}

function ehIgual(campo1, campo2) {
	if (campo1==campo2) return true;
	else return false;
}

function validaFormNormal(campo, pedir_confirmacao, desabilitar_campo) {
	var permissao=true;
	var passa=true;
	var desabilita;
	
	//alert(campo);
	
	if (desabilitar_campo=="1") desabilita= true;
	else desabilita= false;
	
	try {
		var validacoes= g(campo).value;
		permissao= validaForm(validacoes);
	}
	catch (eee) {
		
	}
	
	if (permissao) {
		if (pedir_confirmacao) passa= confirm("Confirma?");
		
		if (passa) {
			if (desabilita) {
				
				var desabilita_qual_campo;
				var esconde_campo;
				
				//respostas do livro
				if (campo.indexOf("id_livro")!=-1) {
					desabilita_qual_campo= campo.replace("validacoes_resposta", "enviar");
					
					//alert(campo);
					
					esconde_campo= campo.replace("validacoes_resposta", "cancela");
					fechaDiv(esconde_campo);
				}
				else {
					desabilita_qual_campo= "enviar";
				}
					
				//alert(desabilita_qual_campo);
				
				desabilitaCampo(desabilita_qual_campo);
				preencheDiv(desabilita_qual_campo, "aguarde...");	
			}
			return(true);
		}
		else
			return(false);
	}
	else
		return(false);
	
}

function validaForm(validacoes) {
	
	var parte= validacoes.split("|");
	var i, aqui, retorno=true, foco, campo, tipo_validacao, mensagem="", campo_foco="", outro_campo;
	
	for (i=0; i<parte.length; i++) {
		aqui= parte[i].split("@", 4);
		campo= aqui[0];
		tipo_validacao= aqui[1];
		campo_foco= aqui[2];
		outro_campo= aqui[3];
		
		switch (tipo_validacao) {
			case "igual": retorno= ehIgual(pegaValor(campo), pegaValor(outro_campo)); break;
			case "vazio": retorno= !estaVazio(campo); break;
			case "data": retorno= validaData(pegaValor(campo), 1); break;
			case "data_passada": retorno= validaData(pegaValor(campo), 2); break;
			case "email": retorno= validaEmail(pegaValor(campo)); break;
			case "numeros": retorno= sohNumeros(pegaValor(campo)); break;
		}

		if (!retorno) {
			if (campo_foco!=undefined) foco= campo_foco;
			else foco= campo;
			
			i=9999;
			mensagem= "Preencha corretamente o campo \""+pegaTitle(campo)+"\"!";
		}
	}
	
	if (foco!="") daFoco(foco);
	if (mensagem!="") alert("ATENÇÃO:\n\n"+mensagem);
	
	return(retorno);
}

/* ---------- ESTOQUE ------------------------------------------------------------------- */

function alteraTipoEstoqueCC(cont, id_item) {
	var id_nota= g("id_nota").value;
	ajaxLink("cc_atualiza_"+cont, "alteraNotaCentroCusto&cont="+cont+"&id_nota="+id_nota+"&id_item="+id_item);
}

function itemDepositoPesquisar(id_deposito, origem) {
	var pesquisa= g("pesquisa");
	
	if (pesquisa.value.length<3) {
		//if ( (pesquisa.value=="") || (pesquisa.value.length<3) ) {
		var item_atualiza= g("item_atualiza");
		item_atualiza.innerHTML= "<div class=\"espacamento vermelho\">Entre com pelo menos 3 caracteres para realizar a busca!</div>";
		pesquisa.focus();
	}
	else
		ajaxLink("item_atualiza", "itemDepositoPesquisar&pesquisa="+pesquisa.value+"&id_deposito="+id_deposito+"&origem="+origem);
}

function itemPesquisar(origem) {
	var pesquisa= g("pesquisa");
	
	if (pesquisa.value.length<3) {
		//if ( (pesquisa.value=="") || (pesquisa.value.length<3) ) {
		var item_atualiza= g("item_atualiza");
		item_atualiza.innerHTML= "<div class=\"espacamento vermelho\">Entre com pelo menos 3 caracteres para realizar a busca!</div>";
		pesquisa.focus();
	}
	else
		ajaxLink("item_atualiza", "itemPesquisar&pesquisa="+pesquisa.value+"&origem="+origem);
}

function itemCadastroOk() {
	var item2= g("item");
	var tipo_apres2= g("tipo_apres");
	var id_centro_custo_tipo2= g("id_centro_custo_tipo2");
		
	if ((item2.value!="") && (item2.value.length>3)) {
		var teste= confirm('Tem certeza que deseja cadastrar esse item?\n\nTenha absoluta certeza dele não existir no sistema,\npara evitar cadastros duplicados!');
		
		if (teste) ajaxLink("item_cadastro3", "itemInserir&item="+item2.value+"&tipo_apres="+tipo_apres2.value+"&id_centro_custo_tipo="+id_centro_custo_tipo2.value);
	}
	else {
		alert("Preencha o campo item corretamente!");
		daFoco('item');
	}
}