<? if ($_SESSION["id_empresa"]!="") { ?>
<div id="tela_mensagens2">
	<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Entre com contato conosco</h2>

<div class="parte_esquerda">

	<form action="<?= AJAX_FORM ?>formContato" method="post" name="formContato" id="formContato" onsubmit="return ajaxForm('conteudo', 'formContato');">
		<p>Caso tenha alguma d�vida, preencha os campos abaixo para entrar em contato.</p>
		<br />
		
		<label>Nome:</label>
		<input name="nome" id="nome" value="<?= $_SESSION["nome_pessoa_sessao"]; ?>" onmouseover="Tip('Informe seu nome.');" />
		<br />

		<label>E-mail:</label>
		<input name="email" id="email" onmouseover="Tip('Informe seu e-mail, para que possamos entrar em contato.');"  />
		<br />

		<label>Telefone:</label>
		<input name="telefone" id="telefone" onmouseover="Tip('Informe seu telefone, caso necess�rio.');" />
		<br />

		<label>Cidade:</label>
		<input name="cidade" id="cidade" onmouseover="Tip('Informe sua cidade, caso necess�rio.');" />
		<br />

		<label>Tipo:</label>
		<select name="tipo_contato" id="tipo_contato" onmouseover="Tip('Selecione o tipo do contato.');" />
			<option value="">--- selecione ---</option>
			<option class="cor_sim" value="D�vida">D�vida</option>
			<option value="Sugest�o">Sugest�o</option>
			<option class="cor_sim" value="Reclama��o">Reclama��o</option>
			<option value="Reportar problema">Reportar problema</option>
		</select>
		<br />
		
		<label>Mensagem:</label>
		<textarea name="mensagem" id="mensagem" onmouseover="Tip('Digite a mensagem.');"></textarea>
		<br />
		
		<label>&nbsp;</label>
		<button type="submit" id="enviar">Enviar</button>
		<br />
		
	</form>
</div>

<div class="parte_direita">
	<h3 class="titulos">Prospital</h3>	
    
    <strong>PROSPITAL Produtos M�dico Hospitalares Ltda.</strong><br /><br />
    
    Av. Presidente Kennedy, n� 1333 sala 702 <br />
    Campinas - S�o Jos�/SC <br /><br />
    
    <strong>CEP:</strong> 88102-401 <br /><br />
    <strong>Telefones:</strong> (48) 3357.5240 e (48) 3357.5214 <br />
</div>
<? } ?>