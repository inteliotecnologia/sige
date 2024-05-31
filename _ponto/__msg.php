<?
	switch($erro) {
		case 1: $erro_string= "ERRO DE LEITURA OU CARTÃO NÃO CADASTRADO!"; break;
		case 2: $erro_string= "ENTRADA PROIBIDA: FUNCIONÁRIO INATIVO!"; break;
		case 3: $erro_string= "ERRO: ESTE É OUTRO CARTÃO DE SUPERVISOR!"; break;
		case 4: $erro_string= "ERRO: CARTÃO REGISTRADO A MENOS DE 1 MINUTO, AGUARDE!"; break;
		case 5: $erro_string= "FORA DO HORÁRIO DE ENTRADA"; break;
		case 6: $erro_string= "ERRO: OPERAÇÃO DE SAÍDA, NÃO É NECESSARIO SUPERVISOR!"; break;
		case 7: $erro_string= "ERRO: NÚMERO DE ENTRADAS NO DIA SUPERIOR AO PERMITIDO!"; break;
		case 8: $erro_string= "ERRO: SAÍDA COM JORNADA MUITO LONGA, PROCURE O RH!"; break;
		case 9: $erro_string= "FORA DO HORÁRIO DE RETORNO DO INTERVALO"; break;
		case 10: $erro_string= "DIA SEM EXPEDIENTE"; break;
		case 11: $erro_string= "REGULARIZE SUA SITUAÇÃO COM O RH!"; break;
		case 12: $erro_string= "VOCÊ FALTOU NO DIA ANTERIOR DE TRABALHO, PROCURE O RH!"; break;
		case 13: $erro_string= "VOCÊ ESTÁ DE FOLGA HOJE!"; break;
		
		case 9999: $erro_string= "CONDIÇÃO AINDA NÃO TRATADA!"; break;
		
	}
	
	//log_ponto($id_empresa, $num_cartao, $id_funcionario, $data_log, $hora_log, $msg, $tipo, $ip) {
	@log_ponto(1, $_GET["cartao"], $id_funcionario, date("Ymd"), date("His"), $turnante, $id_supervisor, $erro_string, -1, $_SERVER["REMOTE_ADDR"], 0, "");
	
?>
<div id="erro"><?= $erro_string; ?></div>

<script type="text/javascript" language="javascript">
	var temporizador= setTimeout("resetaTelaPonto()", 5000);
</script>