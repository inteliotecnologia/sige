// Tem um bando de bugs nas funcoes referentes a datas no javascript,
// entao o melhor e simplificar: vamos comparar a hora local da maquina
// cliente com a hora da cidade desejada e calcular o horario da cidade
// a cada segundo de acordo com essa diferenca. Nao vamos usar tempos
// UTC porque ha varios problemas com timezones e horario de verao.


var hora_inicial_cidade = new Date(2008, 6 ,23 ,22 ,22 ,(49 + 2));

// Na data acima, somamos 2 segs ao horario enviado pelo servidor porque
// ha um certo atraso entre o momento em que o servidor "gera" a data e o momento
// em que o javascript e executado; "adiantando" o relogio 2 segundos, esse erro
// e minimizado (mas nao eliminado, pois nao da pra determinar o erro com precisao).
var contagemID = null;
var contagemAtivada = false;
var diferenca = 0;
var acerta = 0;

// Array relacionando o numero do mes ao nome
mes_port = new Object();
mes_port[0] ="01";      mes_port[1] ="02";      mes_port[2] ="03";     mes_port[3] ="04";
mes_port[4] ="05";      mes_port[5] ="06";      mes_port[6] ="07";     mes_port[7] ="08";
mes_port[8] ="09";      mes_port[9] ="10";      mes_port[10] ="11";    mes_port[11] ="12";

// Vamos usar sempre o ano com 4 digitos; como ha diferencas entre
// o explorer e o navigator, precisa desta funcaozinha.
function getFullYear(obj_data) {
	var ano = obj_data.getYear();
	if (ano < 1000) ano += 1900;
	return ano;
}


// Calculemos a diferenca entre o horario enviado pelo servidor e o horario da
// maquina cliente; com esse numero, podemos recalcular a cada segundo o horario
// correto da cidade independente do horario da maquina cliente.
function iniciaconta() {
	hora_inicial_local=new Date;
	diferenca =(hora_inicial_cidade.getTime() - hora_inicial_local.getTime()); 
	return diferenca;
}

function mostrarTempo(acerta){

	// Pega a hora local atual:
	var agora = new Date();
	// Acerta de acordo com a diferenca calculada antes:
	//agora.setTime(agora.getTime() + acerta);
	agora.setTime(agora.getTime());
	// Divide em ano, mes, dia etc.
	var ano = getFullYear(agora);
	var mes = mes_port[agora.getMonth()];
	var dia = agora.getDate();

	var data_cidade=((dia < 10) ? "0" : "") + dia +"/" +mes+"/" + ano + " <br /> ";

	var hora = agora.getHours();
	var minuto = agora.getMinutes();
	var segundo = agora.getSeconds();

	var hora_cidade = ((((hora >12) ? hora :hora) ) < 10 ? "0" : "") + ((hora >12) ? hora :hora);
        hora_cidade += ((minuto < 10) ? ":0" : ":") + minuto;
        hora_cidade += ((segundo < 10) ? ":0" : ":") + segundo;
        //hora_cidade += (hora >= 12) ? " PM" : " AM" ;

	// Lanca a data correta na pagina HTML.
	document.getElementById("relogio").innerHTML = data_cidade + hora_cidade;

	// Atualiza a cada segundo.
	contagemID = setTimeout("mostrarTempo (acerta)",1000);

    contagemAtivada = true;
}

function iniciaRelogio() {
	acerta=iniciaconta();
    mostrarTempo(acerta);
}
