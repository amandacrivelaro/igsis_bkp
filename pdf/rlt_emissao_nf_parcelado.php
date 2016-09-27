<?php 
 	
//require '../include/';
require_once("../funcoes/funcoesConecta.php");
require_once("../funcoes/funcoesGerais.php");
require_once("../funcoes/funcoesSiscontrat.php");

//CONEXÃO COM BANCO DE DADOS 
$conexao = bancoMysqli();

//CONSULTA 
$id_ped=$_GET['id'];
$ano=date('Y');

dataPagamento($id_ped);

$pedido = siscontrat($id_ped);
$pj = siscontratDocs($pedido['IdProponente'],2);
$ex = siscontratDocs($pedido['IdExecutante'],1);
$rep01 = siscontratDocs($pj['Representante01'],3);
$rep02 = siscontratDocs($pj['Representante02'],3);
$parcelamento = retornaParcelaPagamento($id_ped);

$id_parcela = $_GET['parcela'];

$valorParcela = $parcelamento[$id_parcela]['valor'];
$ValorPorExtenso = valorPorExtenso(($parcelamento[$id_parcela]['valor']));


$id = $pedido['idEvento'];
$Objeto = $pedido["Objeto"];
$Periodo = $pedido["Periodo"];
$ValorGlobal = dinheiroParaBr($pedido["ValorGlobal"]);
$FormaPagamento = $pedido["FormaPagamento"];
$notaFiscal = $pedido["notaFiscal"];
$descricaoNF = $pedido["descricaoNF"];
$notaempenho = $pedido["NotaEmpenho"];
$data_entrega_empenho = exibirDataBr($pedido['EntregaNE']);

//PessoaJuridica

$pjRazaoSocial = $pj["Nome"];
$pjCNPJ = $pj['CNPJ'];


// Executante

$exNome = $ex["Nome"];
$exRG = $ex["RG"];
$exCPF = $ex["CPF"];


// Representante01

$rep01Nome = $rep01["Nome"];
$rep01EstadoCivil = $rep01["EstadoCivil"];
$rep01Nacionalidade = $rep01["Nacionalidade"];
$rep01RG = $rep01["RG"];
$rep01CPF = $rep01["CPF"];


// Representante02

$rep02Nome = $rep02["Nome"];
$rep02EstadoCivil = $rep02["EstadoCivil"];
$rep02Nacionalidade = $rep02["Nacionalidade"];
$rep02RG = $rep02["RG"];
$rep02CPF = $rep02["CPF"];


// GERANDO O WORD:
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$dataAtual - Processo SEI $NumeroProcesso - Parcela $id_parcela.doc");

?>
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>

<p><strong><?php echo $pjRazaoSocial?></strong></p>
<p>&nbsp;</p>
<p>Segue abaixo os dados para emissão da Nota Fiscal:  </p>
<p>&nbsp;</p>
<p><strong>Sacado:</strong> Secretaria Municipal de Cultura </p>
<p><strong>CNPJ:</strong> 49.269.244/0001-63 </p>
<p><strong>Endereço:</strong> Av. São João, 473 - 11º andar - CEP: 01035-000 </p>
<p><strong>Município:</strong> São Paulo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Estado:</strong> São Paulo &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>I. Est. Nº</strong>: Isento </p>
<p>&nbsp;</p>
<p><strong>Nota Fiscal:</strong> <?php echo $notaFiscal?></p>
<p align="justify"><strong>Valor:</strong> R$<?php echo $valorParcela?> (<?php echo $ValorPorExtenso?> )</p>
<p align="justify"><strong>Descrição:</strong> <?php echo $descricaoNF?></p>
<p align="justify">Pagamento referente ao <?php echo $Objeto?></p>
<p>&nbsp;</p>   
<?php $ocor = listaOcorrenciasContrato($id);

   for($i = 0; $i < $ocor['numero']; $i++)
   {
	
   $tipo = $ocor[$i]['tipo'];
   $dia = $ocor[$i]['data'];
   $hour = $ocor[$i]['hora'];
   $lugar = $ocor[$i]['espaco'];
   
   echo "<p align='justify'><strong>Tipo:</strong> ".$tipo."</p>";
   echo "<p align='justify'><strong>Data/Perído:</strong> ".$dia."</p>";
   echo "<p align='justify'><strong>Horário:</strong> ".$hour."</p>";
   echo "<p align='justify'><strong>Local:</strong> ".$lugar."</p>";
   echo "<p>&nbsp;</p>";

   }
?>
<p><strong>Nota de Empenho nº:</strong> <?php echo $notaempenho?></p>
<p><strong>Emitida em:</strong> <?php echo $data_entrega_empenho?></p> 

</body>
</html>