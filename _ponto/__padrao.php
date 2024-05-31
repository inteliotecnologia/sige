
<div id="titulo_padrao">Passe o cartão no leitor para registrar o seu horário.</div>

<div id="imagens">
	<img src="images/1_ponto_<?= rand(1, 3); ?>.gif" />
	<? /*<img src="images/cartaz.jpg" />*/ ?>
</div>

<div id="formulario_ponto">
    <form action="#" method="post" onsubmit="return false;">
        <input name="cartao" id="cartao" onkeyup="if (event.keyCode==13) submetePonto(this, 0);" onblur="daFoco('cartao');" />
    </form>
</div>

<script type="text/javascript" language="javascript">
	daFoco("cartao");
</script>