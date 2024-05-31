
<div id="centralizado">
    <h2>Ops! Você não tem acesso a esta área!</h2>
    
    <ul class="recuo1">
	<?
    switch ($erro_a) {
		case 1:
	?>
    <li>Somente no posto de atendimento CENTRAL é possível dar entrada ou fazer movimentações.</li>
    <?
    break;
	case 2:
	?>
    <li>Somente em um posto de atendimento é possível realizar consultas.</li>
    <?
	break;
	default:
	?>
    <li>Você está acessando uma área restrita.</li>
    <? } ?>
    </ul>
</div>
