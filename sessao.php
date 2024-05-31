<?
require("conexao.php");
require("funcoes.php");

echo "id_empresa: ". $_SESSION["id_empresa"];
echo "<br />id_usuario: ". $_SESSION["id_usuario"];
echo "<br />tipo_usuario: ". $_SESSION["tipo_usuario"];

echo "<br />nome: ". $_SESSION["nome"];
echo "<br />id_funcionario: ". $_SESSION["id_funcionario_sessao"];
echo "<br />id_departamento: ". $_SESSION["id_departamento_sessao"];

echo "<br />permissao: ". $_SESSION["permissao"];
echo "<br />id_acesso: ". $_SESSION["id_acesso"];


?>