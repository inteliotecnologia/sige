<?
@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, "SUPERVISOR AUTORIZANDO", -2, $_SERVER["REMOTE_ADDR"], 0, "");
?>

<div id="supervisor">SUPERVISOR <em><?= strtoupper($nome_supervisor[0]); ?></em> AUTORIZANDO... </div>
<div id="titulo_padrao2">Passe o cartão do funcionário para registrar a autorização.</div>

<div id="formulario_ponto">
    <form action="#" method="post" onsubmit="return false;">
        <input name="cartao" id="cartao" type="text" onkeyup="if (event.keyCode==13) submetePonto(this, <?= $id_funcionario; ?>);" onblur="daFoco('cartao');" />
    </form>
</div>

<script type="text/javascript" language="javascript">
	daFoco("cartao");
	var temporizador= setTimeout("resetaTelaPonto()", 6000);
</script>