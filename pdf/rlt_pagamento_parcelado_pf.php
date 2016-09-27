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
$dataAtual = date("d/m/Y");

dataPagamento($id_ped);

$pedido = siscontrat($id_ped);
$pessoa = siscontratDocs($pedido['IdProponente'],1);
$parcelamento = retornaParcelaPagamento($id_ped);

$id_parcela = $_GET['parcela'];

$valorParcela = $parcelamento[$id_parcela]['valor'];
$ValorPorExtenso = valorPorExtenso(dinheiroDeBr($parcelamento[$id_parcela]['valor']));

$id = $pedido['idEvento'];
$Objeto = $pedido["Objeto"];
$Periodo = $pedido["Periodo"];
$Duracao = $pedido["Duracao"];
$CargaHoraria = $pedido["CargaHoraria"];
$Local = $pedido["Local"];
$ValorGlobal = dinheiroParaBr($pedido["ValorGlobal"]);
$FormaPagamento = $pedido["FormaPagamento"];
$Justificativa = $pedido["Justificativa"];
$Fiscal = $pedido["Fiscal"];
$Suplente = $pedido["Suplente"];

//PessoaFisica

$Nome = $pessoa["Nome"];
$NomeArtistico = $pessoa["NomeArtistico"];
$EstadoCivil = $pessoa["EstadoCivil"];
$Nacionalidade = $pessoa["Nacionalidade"];
$DataNascimento = exibirDataBr($pessoa["DataNascimento"]);
$RG = $pessoa["RG"];
$CPF = $pessoa["CPF"];
$CCM = $pessoa["CCM"];
$OMB = $pessoa["OMB"];
$DRT = $pessoa["DRT"];
$cbo = $pessoa["cbo"];
$Funcao = $pessoa["Funcao"];
$Endereco = $pessoa["Endereco"];
$Telefones = $pessoa["Telefones"];
$Email = $pessoa["Email"];
$INSS = $pessoa["INSS"];



// GERANDO O WORD:
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$dataAtual - Processo SEI $NumeroProcesso - Parcela $id_parcela.doc");

   
?>
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>

<p align="center"><strong>PEDIDO DE PAGAMENTO DE PESSOA FÍSICA</strong></p>
<p>&nbsp;</p>
<p><strong>
	Ilustríssimo(a) Senhor(a) Diretor(a)</br>
	Secretaria Municipal de Cultura
</strong></p>
<p>&nbsp;</p>
<p align="justify"><strong>Nome:</strong> <?php echo $Nome?></p>
<p><strong>CCM:</strong> <?php echo $CCM?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>CPF:</strong> <?php echo $CPF?></p>   
<p><strong>Endereço:</strong> <?php echo $Endereco?></p>
<p><strong>Telefone:</strong> <?php echo $Telefones?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>E-mail:</strong> <?php echo $Email?></p>  
<p>&nbsp;</p>   
<p align="justify"><strong>Objeto:</strong> <?php echo $Objeto?></p>   
<p align="justify"><strong>Data / Período:</strong> <?php echo $Periodo?></p> 
<p align="justify"><strong>Local:</strong> <?php echo $Local?></p>
<p align="justify"><strong>Valor:</strong> R$ <?php echo $valorParcela?> (<?php echo $ValorPorExtenso?> )</p>
<p>&nbsp;</p>
<p align="justify">Venho, mui respeitosamente, requerer  que Vossa Senhoria se digne  submeter a exame   à  decisão do órgão competente o pedido supra.</p>
<p align="justify">Declaro, sob as penas da Lei, não possuir débitos perante as Fazendas Públicas, em especial com a Prefeitura do Município de São Paulo.
Nestes termos, encaminho para deferimento.</p>
<p>&nbsp;</p>
<p align="justify">São Paulo, _______ de ________________________ de <?php echo $ano?></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>________________________</p>
<p><?php echo $Nome?></p>
<p>RG: <?php echo $RG?></p>   

</body>
</html>