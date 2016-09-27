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
$pj = siscontratDocs($pedido['IdProponente'],2);
$ex = siscontratDocs($pedido['IdExecutante'],1);
$rep01 = siscontratDocs($pj['Representante01'],3);
$rep02 = siscontratDocs($pj['Representante02'],3);
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

//PessoaJuridica

$pjRazaoSocial = $pj["Nome"];
$pjNomeArtistico = $pj["NomeArtistico"];
$pjEstadoCivil = $pj["EstadoCivil"];
$pjNacionalidade = $pj["Nacionalidade"];
$pjRG = $pj["RG"];
$pjCPF = $pj["CPF"];
$pjCCM = $pj["CCM"];
$pjOMB = $pj["OMB"];
$pjDRT = $pj["DRT"];
$pjFuncao = $pj["Funcao"];
$pjEndereco = $pj["Endereco"];
$pjTelefones = $pj["Telefones"];
$pjEmail = $pj["Email"];
$pjINSS = $pj["INSS"];
$pjCNPJ = $pj['CNPJ'];

$codPed = "";

// Executante

$exNome = $ex["Nome"];
$exNomeArtistico = $ex["NomeArtistico"];
$exEstadoCivil = $ex["EstadoCivil"];
$exNacionalidade = $ex["Nacionalidade"];
$exRG = $ex["RG"];
$exCPF = $ex["CPF"];
$exCCM = $ex["CCM"];
$exOMB = $ex["OMB"];
$exDRT = $ex["DRT"];
$exFuncao = $ex["Funcao"];
$exEndereco = $ex["Endereco"];
$exTelefones = $ex["Telefones"];
$exEmail = $ex["Email"];
$exINSS = $ex["INSS"];

// Representante01

$rep01Nome = $rep01["Nome"];
$rep01NomeArtistico = $rep01["NomeArtistico"];
$rep01EstadoCivil = $rep01["EstadoCivil"];
$rep01Nacionalidade = $rep01["Nacionalidade"];
$rep01RG = $rep01["RG"];
$rep01CPF = $rep01["CPF"];
$rep01CCM = $rep01["CCM"];
$rep01OMB = $rep01["OMB"];
$rep01DRT = $rep01["DRT"];
$rep01Funcao = $rep01["Funcao"];
$rep01Endereco = $rep01["Endereco"];
$rep01Telefones = $rep01["Telefones"];
$rep01Email = $rep01["Email"];
$rep01INSS = $rep01["INSS"];


// Representante02

$rep02Nome = $rep02["Nome"];
$rep02NomeArtistico = $rep02["NomeArtistico"];
$rep02EstadoCivil = $rep02["EstadoCivil"];
$rep02Nacionalidade = $rep02["Nacionalidade"];
$rep02RG = $rep02["RG"];
$rep02CPF = $rep02["CPF"];
$rep02CCM = $rep02["CCM"];
$rep02OMB = $rep02["OMB"];
$rep02DRT = $rep02["DRT"];
$rep02Funcao = $rep02["Funcao"];
$rep02Endereco = $rep02["Endereco"];
$rep02Telefones = $rep02["Telefones"];
$rep02Email = $rep02["Email"];
$rep02INSS = $rep02["INSS"];


// GERANDO O WORD:
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$dataAtual - Processo SEI $NumeroProcesso - Parcela $id_parcela.doc");

?>

<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>

<p align="center"><strong>RECIBO DE PAGAMENTO</strong></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p align="justify">Recebi da Prefeitura de São Paulo - Secretaria Municipal de Cultura a importância de R$ <?php echo $valorParcela?> (<?php echo $ValorPorExtenso?>  ) referente à serviços prestados <?php echo $Periodo?>.</p>
<p>&nbsp;</p>
<p align="justify"><strong>Nome da empresa:</strong> <?php echo $pjRazaoSocial?></p>
<p><strong>CCM:</strong> <?php echo $pjCCM?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>CNPJ:</strong> <?php echo $pjCNPJ?></p>   
<p><strong>Endereço:</strong> <?php echo $pjEndereco?></p>
<p><strong>Telefone:</strong> <?php echo $pjTelefones?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>E-mail:</strong> <?php echo $pjEmail?></p>  
<p>&nbsp;</p>
<p align="justify"><strong>Serviço Prestado:</strong> <?php echo $Objeto?></p>
<p align="justify"><strong>Data / Período:</strong> <?php echo $Periodo?></p>
<p align="justify"><strong>Duração:</strong> <?php echo $Duracao?>utos</p>
<p align="justify"><strong>Local:</strong> <?php echo $Local?></p>
<p>&nbsp;</p>
<p align="justify">São Paulo, _______ de ________________________ de <?php echo $ano?>.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="100%" border="0">
<p>________________________</p>
<p><?php echo $rep01Nome?></p>
<p>RG: <?php echo $rep01RG?></p>    
<p align="justify"><strong>OBSERVAÇÃO:</strong> A validade deste recibo fica condicionada ao efetivo por ordem de pagamento ou depósito na conta corrente no Banco do Brasil, indicada pelo contratado, ou na falta deste, ao recebimento no Departamento do Tesouro da Secretaria das Finanças e Desenvolvimento Econômico, situado à Rua Pedro Américo, 32.</p>

</body>
</html>
