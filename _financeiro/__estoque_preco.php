<? if (pode("iq|", $_SESSION["permissao"])) { ?>

<div id="tela_mensagens2">
<? include("__tratamento_msgs.php"); ?>
</div>

<h2 class="titulos">Consulta de pre�o</h2>

<div class="parte30 cm">
	<fieldset>
		<legend>Pesquisa de produto</legend>
		
		<label for="pesquisa">Produto:</label>
		<input id="pesquisa" name="pesquisa" class="tamanho80" onkeyup="itemPesquisar('p');" />
		<? /*<button type="button" class="tamanho30" onclick="itemPesquisar('e');">ok</button>*/ ?>
		<br />
			
		<div id="item_atualiza">
		</div>
        <br />
        
	</fieldset>
</div>

<div class="parte70">
	
	<fieldset>
		<legend>Relat�rio</legend>
	
		<div id="preco_atualiza">
        	Fa�a a busca do produto no campo ao lado para ter acesso ao hist�rio de compras.
        </div>
	</fieldset>
</div>
<script language="javascript" type="text/javascript">daFoco('pesquisa');</script>
<? } ?>