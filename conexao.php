<?
session_start();

$conf_host="enceladus.cle1tvcm29jx.us-east-1.rds.amazonaws.com";
$conf_usuario="intelio_user";

$conf_senha="2i92iS02iV3d23dm";
$conf_db="sige_db";


$conexao= @mysql_connect($conf_host, $conf_usuario, $conf_senha) or die("O servidor está um pouco instável, favor tente novamente! ". mysql_error());
@mysql_select_db($conf_db) or die("O servidor está um pouco instável, favor tente novamente!! ". mysql_error());

define("AJAX_LINK", "link.php?");
define("AJAX_FORM", "form.php?");
define("CAMINHO", "uploads/");
define("VERSAO", "Sistema SiGE 0.81");

setlocale(LC_CTYPE, "pt_BR");

//if ($_GET["pagina"]=="")
//	header("location: index2.php?pagina=login");

//se a pagina atual nao for a de login

//echo $_GET["pagina"];

if (($_GET["pagina"]!="login") && ($_GET["pagina"]!="login_turno") && ($_GET["pagina"]!="ad_ativa") && ($_GET["pagina"]!="ponto/padrao") && ($_GET["pagina"]!="ponto/abre")) {
	$retorno= true;
	if ($_SESSION["id_usuario"]=="")
		$retorno= false;
	
	if (!$retorno)
		header("location: index2.php?pagina=login&redireciona");
}
?>