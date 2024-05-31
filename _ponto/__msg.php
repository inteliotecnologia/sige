<?
	switch($erro) {
		case 1: $erro_string= "ERRO DE LEITURA OU CART�O N�O CADASTRADO!"; break;
		case 2: $erro_string= "ENTRADA PROIBIDA: FUNCION�RIO INATIVO!"; break;
		case 3: $erro_string= "ERRO: ESTE � OUTRO CART�O DE SUPERVISOR!"; break;
		case 4: $erro_string= "ERRO: CART�O REGISTRADO A MENOS DE 1 MINUTO, AGUARDE!"; break;
		case 5: $erro_string= "FORA DO HOR�RIO DE ENTRADA"; break;
		case 6: $erro_string= "ERRO: OPERA��O DE SA�DA, N�O � NECESSARIO SUPERVISOR!"; break;
		case 7: $erro_string= "ERRO: N�MERO DE ENTRADAS NO DIA SUPERIOR AO PERMITIDO!"; break;
		case 8: $erro_string= "ERRO: SA�DA COM JORNADA MUITO LONGA, PROCURE O RH!"; break;
		case 9: $erro_string= "FORA DO HOR�RIO DE RETORNO DO INTERVALO"; break;
		case 10: $erro_string= "DIA SEM EXPEDIENTE"; break;
		case 11: $erro_string= "REGULARIZE SUA SITUA��O COM O RH!"; break;
		case 12: $erro_string= "VOC� FALTOU NO DIA ANTERIOR DE TRABALHO, PROCURE O RH!"; break;
		case 13: $erro_string= "VOC� EST� DE FOLGA HOJE!"; break;
		
		case 9999: $erro_string= "CONDI��O AINDA N�O TRATADA!"; break;
		
	}
	
	//log_ponto($id_empresa, $num_cartao, $id_funcionario, $data_log, $hora_log, $msg, $tipo, $ip) {
	@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, $erro_string, -1, $_SERVER["REMOTE_ADDR"], 0, "");
	
?>
<div id="erro"><?= $erro_string; ?></div>

<script type="text/javascript" language="javascript">
	var temporizador= setTimeout("resetaTelaPonto()", 5000);
</script>