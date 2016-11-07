<?php
/* 

siscontrat 

Exemplo de uso:

require "../funcoes/funcoesSiscontrat.php";

$contrato = siscontrat($idPedido);
$pj = siscontratDocs($contrato['IdProponente'],$contrato['TipoPessoa']);
$representante01 = siscontratDocs($pj['Representante01'],3);
$representante02 = siscontratDocs($pj['Representante02'],3);
$executante = siscontratDocs($contrato['executante'],1);

$conectar = bancoMysqli(); //cria conexão
$sql = "SELECT * FROM ig_evento WHERE idEvento = '$idEvento' LIMIT 0,10";
while($pedido = mysqli_fetch_array(mysqli_query($conectar,$sql))){
	$nome_do_evento = $pedido['nomeEvento'];
		
} //executa query

 
*/


function siscontratLista($tipoPessoa,$instituicao,$num_registro,$pagina,$ordem,$estado){
	$con = bancoMysqli();
	if($estado == "todos"){
		$est = "";	
	}else{
		$est = " AND estado = '$estado' ";
	}
	
	if($tipoPessoa == "todos"){
		$tipo = "";	
	}else{
		$tipo = " AND tipoPessoa = '$tipoPessoa' ";
	}
	
	if($instituicao == ""){
		$inst = "";		
	}else{
		$inst = " AND instituicao = '$instituicao'";
	}
	
	$sql_lista_total = "SELECT * FROM igsis_pedido_contratacao WHERE publicado = '1' $tipo  AND estado <> '' $inst $est ORDER BY idPedidoContratacao $ordem ";
	$query_lista_total = mysqli_query($con,$sql_lista_total);
	$total_registros = mysqli_num_rows($query_lista_total);
	$pag = $pagina - 1;
	$registro_inicial = $num_registro * $pag;
	$total_paginas = $total_registros / $num_registro; // gera o número de páginas
	$sql_lista_pagina = "SELECT * FROM igsis_pedido_contratacao WHERE  publicado = '1' $tipo AND estado <> '' $inst $est ORDER BY idPedidoContratacao $ordem LIMIT $registro_inicial,$num_registro";
		$query_lista_pagina = mysqli_query($con,$sql_lista_pagina);
	//$x = $sql_lista_pagina;
	$i = 0;
	while($pedido = mysqli_fetch_array($query_lista_pagina)){
		$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
		$local = listaLocais($pedido['idEvento']);
		$local_juridico = listaLocaisJuridico($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$pessoa = recuperaPessoa($pedido['idPessoa'],$tipoPessoa);
		$fiscal = recuperaUsuario($evento['idResponsavel']);
		$suplente = recuperaUsuario($evento['suplente']);
		$protocolo = ""; //recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");
		if($pedido['parcelas'] > 0){
			$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
			$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
		}else{
			$valorTotal = $pedido['valor'];
			$formaPagamento = $pedido['formaPagamento'];
		}
		
		
				
		$x[$i] = array(
		    "idPedido" => $pedido['idPedidoContratacao'],
			"idEvento" => $pedido['idEvento'], 
			"idSetor" => $usuario['idInstituicao'],
			"Setor" => $instituicao['instituicao']  ,
			"TipoPessoa" => $pedido['tipoPessoa'],
			"CategoriaContratacao" => $evento['ig_modalidade_IdModalidade'] , //precisa ver se retorna o id
			"Objeto" => retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'] ,
			"Local" => substr($local,1) , //retira a virgula no começo da string
			"LocalJuridico" => substr($local,1) , //retira a virgula no começo da string
			"ValorGlobal" => $valorTotal,
			"ValorIndividual" => $pedido['valorIndividual'],
			"FormaPagamento" => $formaPagamento,
			"Periodo" => $periodo, 
			"Duracao" => $duracao, 
			"Verba" => $pedido['idVerba'] ,
			"Justificativa" => $evento['justificativa'] ,
			"ParecerTecnico" => $evento['parecerArtistico'],
			"DataCadastro" => $evento['dataEnvio'],
			"Fiscal" => $fiscal['nomeCompleto'] ,
			"Suplente" => $suplente['nomeCompleto'],
			"Observacao"=> $pedido['observacao'], //verificar
			"Horario" => "", //SPCultura
			"IdProponente" => $pedido['idPessoa'],
			"ProtocoloSIS" => '', //$protocolo['idProtocolo'],
			"NumeroProcesso" => $pedido['NumeroProcesso'],
			"NotaEmpenho" => $pedido['NumeroNotaEmpenho'],
			"EmissaoNE" => $pedido['DataEmissaoNotaEmpenho'],
			"EntregaNE" => $pedido['DataEntregaNotaEmpenho'],
			"Assinatura" => "",
			"Cargo" => "",
			"Instituicao" => $instituicao['instituicao'],
			"Sigla" => $instituicao['sigla'],
			"Contratos" => $pedido['idContratos'],
			"parcelas" => $pedido['parcelas'],
			"Status" => $pedido['estado']
		);
		
		$i++;
	}
	return $x;
}


function siscontrat($idPedido){ 
	$con = bancoMysqli();
	if($idPedido != ""){ //retorna 1 array do pedido ['nomedocampo'];
		
		$pedido = recuperaDados("igsis_pedido_contratacao",$idPedido,"idPedidoContratacao");
				
			$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
			$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
			$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
			$local = listaLocais($pedido['idEvento']);
			$local_juridico = listaLocaisJuridico($pedido['idEvento']);
			$duracao = retornaDuracao($pedido['idEvento']);
			$proponente = recuperaPessoa($pedido['idPessoa'],$pedido['tipoPessoa']);
			$fiscal = recuperaUsuario($evento['idResponsavel']);
			$suplente = recuperaUsuario($evento['suplente']);
			$assinatura = recuperaDados("sis_assinatura",$pedido['instituicao'],"idInstituicao");
			$penalidades = recuperaPenalidades($pedido['idPenalidade']);
			$verba = recuperaVerba($pedido['idPenalidade']);
			
			$viradaOcorrencia = viradaOcorrencia($pedido['idEvento']);
			
			if($viradaOcorrencia['bool'] == '1'){
				$virada = "Uma hora de evento no período da Virada Cultural.";	
			}else{
				$virada = " - ";
			}	
			
			
			
			if($pedido['parcelas'] > 0){
				if ($pedido['tipoPessoa'] == 4){
					$pagamento = txtParcelasFormacao ($idPedido,$pedido['parcelas']);
				} else {

				$pagamento = txtParcelas($idPedido,$pedido['parcelas']);	
				}
			}else{
				$pagamento = $pedido['formaPagamento'];	
			}
			
			if($pedido['tipoPessoa'] == 4){
				$formacao = recuperaDados("sis_formacao",$pedido['idPedidoContratacao'],"idPedidoContratacao");
				$cargo = recuperaDados("sis_formacao_cargo",$formacao['IdCargo'],"Id_Cargo");
				$programa = recuperaDados("sis_formacao_programa",$formacao['IdPrograma'],"Id_Programa");
				$objeto = "CONTRATAÇÃO COMO ".strtoupper($cargo['Cargo'])." DO ".strtoupper($programa['Programa'])." NOS TERMOS DO EDITAL ".$programa['edital']." - PROGRAMAS DA DIVISÃO DE FORMAÇÃO.";
				if($cargo['coordenador'] == 1){ 
					$loc = "SMC e equipamentos sobre sua supervisão";
				}else{
					$loc = retornaLocal($formacao['IdEquipamento01'])." / ".retornaLocal($formacao['IdEquipamento02'])." / ".retornaLocal($formacao['IdEquipamento03']);

				}
				$loc_jur = "SMC e equipamentos sobre sua supervisão";
				$periodo = retornaPeriodoVigencia($idPedido);
				$carga = retornaCargaHoraria($pedido['idPedidoContratacao'],$pedido['parcelas'])." horas";
				//$carga = "";
				$justificativa = $cargo['justificativa'];
			}else{
				$objeto = retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'];
				$loc = substr($local,1);
				$loc_jur = substr($local_juridico,1);
				$periodo = retornaPeriodo($pedido['idEvento']);
				$carga = "";
				$justificativa = $pedido['justificativa'];
			}
			
		$x = array(
			"idEvento" => $pedido['idEvento'], 
			"idSetor" => $usuario['idInstituicao'],
			"Setor" => $instituicao['instituicao']  ,
			"TipoPessoa" => $pedido['tipoPessoa'],
			"CategoriaContratacao" => $evento['ig_modalidade_IdModalidade'] , //precisa ver se retorna o id
			"Objeto" => $objeto ,
			"Local" => $loc , //retira a virgula no começo da string
			"LocalJuridico" => $loc_jur , //retira a virgula no começo da string
			"ValorGlobal" => $pedido['valor'],
			"ValorIndividual" => $pedido['valorIndividual'],
			"FormaPagamento" => $pagamento,
			"Periodo" => $periodo, 
			"Duracao" => $duracao." min", 
			"Verba" => $pedido['idVerba'] ,
			"Justificativa" => $justificativa,
			"ParecerTecnico" => $pedido['parecerArtistico'],
			"DataCadastro" => $evento['dataEnvio'],
			"Fiscal" => $fiscal['nomeCompleto'] ,
			"Suplente" => $suplente['nomeCompleto'],
			"Observacao"=> $pedido['observacao'], //verificar
			"NotaEmpenho" => "",
			"Horario" => "", //SPCultura
			"IdProponente" => $pedido['idPessoa'],
			"idRepresentante01" => $pedido['idRepresentante01'],
			"idRepresentante02" => $pedido['idRepresentante02'],
			"IdExecutante" => $pedido['IdExecutante'],
			"CargaHoraria" => $carga,
			"NumeroProcesso" => $pedido['NumeroProcesso'],
			"NotaEmpenho" => $pedido['NumeroNotaEmpenho'],
			"EmissaoNE" => $pedido['DataEmissaoNotaEmpenho'],
			"EntregaNE" => $pedido['DataEntregaNotaEmpenho'],
			"Assinatura" => $assinatura['Assinatura'],
			"Cargo" => $assinatura['Cargo'],
			"parcelas" => $pedido['parcelas'],
			"RfFiscal" => $fiscal['rf'],
			"RfSuplente" => $suplente['rf'],
			"AmparoLegal" => $pedido['AmparoLegal'],
			"Finalizacao" => $pedido['Finalizacao'],
			"ComplementoDotacao" => $pedido['ComplementoDotacao'],
			"Status" => $pedido['estado'],
			"Virada" => $virada,
			"Penalidade" => $penalidades['txt'],
			"extratoLiquidacao" => $pedido['extratoLiquidacao'],
			"retencoesINSS" => $pedido['retencoesINSS'],
			"retencoesISS" => $pedido['retencoesISS'],
			"retencoesIRRF" => $pedido['retencoesIRRF'],
			"notaFiscal" => $pedido['notaFiscal'],
			"descricaoNF" => $pedido['descricaoNF'],
			"aprovacaoFinanca" => $pedido['aprovacaoFinanca']
			);
		
		
		
	return $x;	
	}else{
		return "Erro";
	}
}

function siscontratFormacao($idPedido){ 
	$con = bancoMysqli();
	if($idPedido != ""){ //retorna 1 array do pedido ['nomedocampo'];
		
		$pedido = recuperaDados("igsis_pedido_contratacao",$idPedido,"idPedidoContratacao");
		$formacao = recuperaDados("sis_formacao_evento",$pedido['idPedidoContratacao'],"idPedidoContratacao"); //$tabela,$idEvento,$campo
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
		$local = listaLocais($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$proponente = recuperaPessoa($pedido['idPessoa'],$pedido['tipoPessoa']);
		$fiscal = recuperaUsuario($evento['idResponsavel']);
		$suplente = recuperaUsuario($evento['suplente']);
		$assinatura = recuperaDados("sis_assinatura",$pedido['instituicao'],"idInstituicao");
		$penalidades = recuperaPenalidades($pedido['idPenalidade']);
		if($pedido['parcelas'] > 1){
			$pagamento = txtParcelasFormacao($idPedido,$pedido['parcelas']);	
		}else{
			$pagamento = $pedido['formaPagamento'];	
		}
		$x = array(
			"idEvento" => $pedido['idEvento'], 
			"idSetor" => $usuario['idInstituicao'],
			"Setor" => $instituicao['instituicao']  ,
			"TipoPessoa" => $pedido['tipoPessoa'],
			"CategoriaContratacao" => $evento['ig_modalidade_IdModalidade'] , //precisa ver se retorna o id
			"Objeto" => retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'] ,
			"Local" => substr($local,1) , //retira a virgula no começo da string
			"ValorGlobal" => $pedido['valor'],
			"ValorIndividual" => $pedido['valorIndividual'],
			"FormaPagamento" => $pagamento,
			"Periodo" => $periodo, 
			"Duracao" => $duracao." min", 
			"Verba" => $pedido['idVerba'] ,
			"Justificativa" => $pedido['justificativa'],
			"ParecerTecnico" => $pedido['parecerArtistico'],
			"DataCadastro" => $evento['dataEnvio'],
			"Fiscal" => $fiscal['nomeCompleto'] ,
			"Suplente" => $suplente['nomeCompleto'],
			"Observacao"=> $pedido['observacao'], //verificar
			"NotaEmpenho" => "",
			"Horario" => "", //SPCultura
			"IdProponente" => $pedido['idPessoa'],
			"idRepresentante01" => $pedido['idRepresentante01'],
			"idRepresentante02" => $pedido['idRepresentante02'],
			"IdExecutante" => $pedido['IdExecutante'],
			"CargaHoraria" => "",
			"NumeroProcesso" => $pedido['NumeroProcesso'],
			"NotaEmpenho" => $pedido['NumeroNotaEmpenho'],
			"EmissaoNE" => $pedido['DataEmissaoNotaEmpenho'],
			"EntregaNE" => $pedido['DataEntregaNotaEmpenho'],
			"Assinatura" => $assinatura['Assinatura'],
			"Cargo" => $assinatura['Cargo'],
			"parcelas" => $pedido['parcelas'],
			"RfFiscal" => $fiscal['rf'],
			"RfSuplente" => $suplente['rf'],
			"AmparoLegal" => $pedido['AmparoLegal'],
			"Finalizacao" => $pedido['Finalizacao'],
			"ComplementoDotacao" => $pedido['ComplementoDotacao'],
			"Status" => $pedido['estado'],
			"Penalidade" => $penalidades['txt']	
			);
		
		
		
	return $x;	
	}else{
		return "Erro";
	}
}




function siscontratDocs($idPessoa,$tipo){
	if($idPessoa == NULL){
		return NULL;	
	}else{	
	$con = bancoMysqli();
	switch($tipo){
		case 1: // Pessoa Física
			$sql = "SELECT * FROM sis_pessoa_fisica WHERE Id_PessoaFisica = $idPessoa";
			$query = mysqli_query($con,$sql);
			$x = mysqli_fetch_array($query);
			$estadoCivil = recuperaEstadoCivil($x['IdEstadoCivil']);
			$endereco = retornaEndereco($x['CEP'],$x['Numero'],$x['Complemento']);
			$banco = recuperaDados('igsis_bancos',$x['codBanco'],'ID');
			
				$y = array(
				"Nome" => $x['Nome'],
				"NomeArtistico" => $x['NomeArtistico'] ,
				"IdEstadoCivil" => $x['IdEstadoCivil'] ,
				"EstadoCivil" => $estadoCivil['EstadoCivil'] ,
				"DataNascimento" => $x['DataNascimento'] ,
				"LocalNascimento" => $x['LocalNascimento'] ,
				"Nacionalidade" => $x['Nacionalidade'] ,
				"DRT" => $x['DRT'] ,
				"PIS" => $x['Pis'] ,
				"Observacao" => $x['Observacao'] ,
				"RG" => $x['RG'] ,
				"CPF" => $x['CPF'],
				"CNPJ" => "",
				"CCM" => $x['CCM'],
				"docCCM" => "nomedoarquivo",
				"OMB" => $x['OMB'] ,
				"Endereco" => $endereco ,
				"CEP" => $x['CEP'],
				"NumEndereco" => $x['Numero'],
				"Complemento" => $x['Complemento'],				
				"Telefones" => $x['Telefone1']." / ".$x['Telefone2']." / ".$x['Telefone3'],
				"INSS" => $x['InscricaoINSS'] ,
				"Email" => $x['Email'] ,
				"Telefone01" => $x['Telefone1'],
				"Banco" => $banco['banco'],
				"Conta" => $x['conta'],
				"Agencia" => $x['agencia'],
				"CodigoBanco" => $banco['codigo'],
				"cbo" => $x['cbo'] ,	
				"Funcao" => $x['Funcao'],			
				"Representante01" => "",
				"Representante02" => ""


			);
			return $y;

		break;
		case 2: // Pessoa Jurídica
			$sql = "SELECT * FROM sis_pessoa_juridica WHERE Id_PessoaJuridica = '$idPessoa';";
			$query = mysqli_query($con,$sql);
			$x = mysqli_fetch_array($query);
			$endereco = retornaEndereco($x['CEP'],$x['Numero'],$x['Complemento']);
						$banco = recuperaDados('igsis_bancos',$x['codBanco'],'ID');
			
				$y = array(
				"Nome" => $x['RazaoSocial'],
				"NomeArtistico" => "" ,
				"IdEstadoCivil" => "" ,
				"EstadoCivil" => "" ,
				"DataNascimento" => "" ,
				"LocalNascimento" => "" ,
				"Nacionalidade" => "" ,
				"DRT" =>"" ,
				"PIS" => "" ,
				"Observacao" => $x['Observacao'] ,
				"RG" => "" ,
				"CPF" => "",
				"CEP" => $x['CEP'],
				"CNPJ" => $x['CNPJ'],
				"CCM" => $x['CCM'],
				"OMB" => "",
				"Endereco" => $endereco ,
				"NumEndereco" => $x['Numero'],
				"Complemento" => $x['Complemento'],				
				"Telefones" => $x['Telefone1']." / ".$x['Telefone2']." / ".$x['Telefone3'],
				"Telefone01" => $x['Telefone1'],
				"Banco" => $banco['banco'],
				"Conta" => $x['conta'],
				"Agencia" => $x['agencia'],
				"CodigoBanco" => $banco['codigo'],
				"INSS" => "" ,
				"Email" => $x['Email'] ,
				"Funcao" => "",
				"Representante01" => $x['IdRepresentanteLegal1'],
				"Representante02" => $x['IdRepresentanteLegal2']


			);
			return $y;	
		break;

		case 3: // Representante legal
			$sql = "SELECT * FROM sis_representante_legal WHERE Id_RepresentanteLegal = $idPessoa";
			$query = mysqli_query($con,$sql);
			$x = mysqli_fetch_array($query);
			//$endereco = retornaEndereco($x['CEP'],$x['Numero'],$x['Complemento']);
			$estadoCivil = recuperaEstadoCivil($x['IdEstadoCivil']);
				$y = array(
				"Nome" => $x['RepresentanteLegal'],
				"NomeArtistico" => "" ,
				"IdEstadoCivil" =>  $x['IdEstadoCivil'] ,
				"EstadoCivil" => $estadoCivil['EstadoCivil'] ,
				"DataNascimento" => "" ,
				"LocalNascimento" => "" ,
				"Nacionalidade" => $x['Nacionalidade'] ,
				"DRT" =>"" ,
				"PIS" => "" ,
				"Observacao" => "" ,
				"RG" => $x['RG'] ,
				"CPF" => $x['CPF'],
				"CNPJ" => "",
				"CCM" => "",
				"OMB" => "",
				"Endereco" => "" ,
				"Telefones" => "",
				"INSS" => "" ,
				"Email" => "" ,
				"Funcao" => "",
				"Representante01" => $x['Id_RepresentanteLegal'],
				"Representante02" => ""

			);
			return $y;	
		break;		

	}
	}
}
function listaPedidoContratacao($idEvento){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	$num = mysqli_num_rows($query);
	if($num >0){
		$i = 0;
		while($pedido = mysqli_fetch_array($query)){
			$x[$i] = $pedido['idPedidoContratacao'];
			$i++;	
		}		
	return $x;
	}else{
	return NULL;
	}
}	


function listaArquivosPessoaSiscontrat($idPessoa,$tipo,$pedido,$form,$pag){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_arquivos_pessoa WHERE idPessoa = '$idPessoa' AND idTipoPessoa = '$tipo' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	echo "<table class='table table-condensed'>
					<thead>
						<tr class='list_menu'>
							<td width='30%'>Tipo</td>
							<td>Nome do arquivo</td>
							<td width='10%'></td>
						</tr>
					</thead>
					<tbody>";
	while($campo = mysqli_fetch_array($query)){
		$tipoDoc = recuperaDados("igsis_upload_docs",$campo['tipo'],"idTipoDoc");
			echo "<tr>";
			echo "<td class='list_description'>".$tipoDoc['documento']."</td>";
			echo "<td class='list_description'><a href='../uploadsdocs/".$campo['arquivo']."' target='_blank'>".$campo['arquivo']."</a></td>";
			echo "
			<td class='list_description'>
			<form method='POST' action='?perfil=".$pag."&p=frm_arquivos&id=".$idPessoa."&tipo=".$tipo."'>
			<input type='hidden' name='idPessoa' value='".$idPessoa."' />
			<input type='hidden' name='tipoPessoa' value='".$tipo."' />
			<input type='hidden' name='$form' value='1' />
			
			<input type='hidden' name='apagar' value='".$campo['idArquivosPessoa']."' />
			<input type ='submit' class='btn btn-theme  btn-block' value='apagar'></td></form>"	;
			echo "</tr>";		
	}
					
						

                        

						
		
	echo "					</tbody>
				</table>
				
				";	
				
}

function listaArquivosPessoaExecutante	($idPessoa,$tipo,$pedido,$form,$pag){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_arquivos_pessoa WHERE idPessoa = '$idPessoa' AND idTipoPessoa = '$tipo' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	echo "<table class='table table-condensed'>
					<thead>
						<tr class='list_menu'>
							<td width='30%'>Tipo</td>
							<td>Nome do arquivo</td>
							<td width='10%'></td>
						</tr>
					</thead>
					<tbody>";
	while($campo = mysqli_fetch_array($query)){
		$tipoDoc = recuperaDados("igsis_upload_docs",$campo['tipo'],"idTipoDoc");
			echo "<tr>";
			echo "<td class='list_description'>".$tipoDoc['documento']."</td>";
			echo "<td class='list_description'><a href='../uploadsdocs/".$campo['arquivo']."' target='_blank'>".$campo['arquivo']."</a></td>";
			echo "
			<td class='list_description'>
			<form method='POST' action='?perfil=contratados&p=arqexec'>
			<input type='hidden' name='$form' value='1' />
			<input type='hidden' name='apagar' value='".$campo['idArquivosPessoa']."' />
			<input type ='submit' class='btn btn-theme  btn-block' value='apagar'></td></form>"	;
			echo "</tr>";		
	}
					
						

                        

						
		
	echo "					</tbody>
				</table>
				
				";	
				
}

function listaArquivosPessoaSiscontratFormacao($idPessoa,$tipo,$pedido,$form,$pag){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_arquivos_pessoa WHERE idPessoa = '$idPessoa' AND idTipoPessoa = '$tipo' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	echo "<table class='table table-condensed'>
					<thead>
						<tr class='list_menu'>
							<td width='30%'>Tipo</td>
							<td>Nome do arquivo</td>
							<td width='10%'></td>
						</tr>
					</thead>
					<tbody>";
	while($campo = mysqli_fetch_array($query)){
		$tipoDoc = recuperaDados("sis_formacao_upload",$campo['tipo'],"idTipoDoc");
			echo "<tr>";
			echo "<td class='list_description'>".$tipoDoc['documento']."</td>";
			echo "<td class='list_description'><a href='../uploadsdocs/".$campo['arquivo']."' target='_blank'>".$campo['arquivo']."</a></td>";
			echo "
			<td class='list_description'>
			<form method='POST' action='?perfil=".$pag."&p=frm_arquivos&id=".$idPessoa."&tipo=".$tipo."'>
			<input type='hidden' name='idPessoa' value='".$idPessoa."' />
			<input type='hidden' name='tipoPessoa' value='".$tipo."' />
			<input type='hidden' name='$form' value='1' />
			
			<input type='hidden' name='apagar' value='".$campo['idArquivosPessoa']."' />
			<input type ='submit' class='btn btn-theme  btn-block' value='apagar'></td></form>"	;
			echo "</tr>";		
	}
					
						

                        

						
		
	echo "					</tbody>
				</table>
				
				";	
				
}
	
function buscaSiscontrat($busca,$tipo){



}

function analiseSiscontrat($idPedido){

$pedido = recuperaDados("igsis_pedido_contratacao",$idPedido,"idPedidoContratacao");

if(($pedido['NumeroNotaEmpenho'] != NULL)OR 
	($pedido['NumeroNotaEmpenho'] != "")){
		$status = "Nota de Empenho gerada";		
}else{
	if(($pedido['NumeroProcesso'] != NULL) OR
	($pedido['NumeroProcesso'] != "")){
		$status = "Numero de Processo gerado";
	}else{
		
	}	
}


}

function siscontratListaEvento($tipoPessoa,$instituicao,$num_registro,$pagina,$ordem,$estado,$idUsuario){
	$con = bancoMysqli();
	if($estado == "todos"){
		$est = "";	
	}else{
		$est = " AND igsis_pedido_contratacao.estado = '$estado' ";
	}
	
	if($tipoPessoa == "todos"){
		$tipo = "";	
	}else{
		$tipo = " AND tipoPessoa = '$tipoPessoa' ";
	}

	
	
	$sql_lista_total = "SELECT * FROM igsis_pedido_contratacao, ig_evento WHERE igsis_pedido_contratacao.idEvento = ig_evento.idEvento AND (ig_evento.idUsuario = '$idUsuario' OR ig_evento.idResponsavel = '$idUsuario' OR ig_evento.suplente = '$idUsuario') AND igsis_pedido_contratacao.publicado = '1' AND ig_evento.dataEnvio IS NOT NULL $tipo AND ig_evento.idInstituicao = '$instituicao' $est ";
	$query_lista_total = mysqli_query($con,$sql_lista_total);
	$total_registros = mysqli_num_rows($query_lista_total);
	$pag = $pagina - 1;
	$registro_inicial = $num_registro * $pag;
	$total_paginas = $total_registros / $num_registro; // gera o número de páginas
	$sql_lista_pagina = "SELECT * FROM igsis_pedido_contratacao, ig_evento WHERE igsis_pedido_contratacao.idEvento = ig_evento.idEvento AND (ig_evento.idUsuario = '$idUsuario' OR ig_evento.idResponsavel = '$idUsuario' OR ig_evento.suplente = '$idUsuario') AND igsis_pedido_contratacao.publicado = '1' AND ig_evento.dataEnvio IS NOT NULL $tipo AND ig_evento.idInstituicao = '$instituicao' $est ORDER BY igsis_pedido_contratacao.idPedidoContratacao $ordem LIMIT $registro_inicial,$num_registro";
		$query_lista_pagina = mysqli_query($con,$sql_lista_pagina);
	//$x = $sql_lista_pagina;
	$i = 0;
	while($pedido = mysqli_fetch_array($query_lista_pagina)){
		$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
		$local = listaLocais($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$pessoa = recuperaPessoa($pedido['idPessoa'],$tipoPessoa);
		$fiscal = recuperaUsuario($evento['idResponsavel']);
		$suplente = recuperaUsuario($evento['suplente']);
		$protocolo = ""; //recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");
		if($pedido['parcelas'] > 0){
			$pagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
		}else{
			$pagamento = $pedido['formaPagamento'];
		
		}
		
		$x[$i] = array(
		    "idPedido" => $pedido['idPedidoContratacao'],
			"idEvento" => $pedido['idEvento'], 
			"idSetor" => $usuario['idInstituicao'],
			"Setor" => $instituicao['instituicao']  ,
			"TipoPessoa" => $pedido['tipoPessoa'],
			"CategoriaContratacao" => $evento['ig_modalidade_IdModalidade'] , //precisa ver se retorna o id
			"Objeto" => retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'] ,
			"Local" => substr($local,1) , //retira a virgula no começo da string
			"ValorGlobal" => $pedido['valor'],
			"ValorIndividual" => $pedido['valorIndividual'],
			"FormaPagamento" => $pagamento,
			"Periodo" => $periodo, 
			"Duracao" => $duracao, 
			"Verba" => $pedido['idVerba'] ,
			"Justificativa" => $evento['justificativa'] ,
			"ParecerTecnico" => $evento['parecerArtistico'],
			"DataCadastro" => $evento['dataEnvio'],
			"Fiscal" => $fiscal['nomeCompleto'] ,
			"Suplente" => $suplente['nomeCompleto'],
			"Observacao"=> $pedido['observacao'], //verificar
			"Horario" => "", //SPCultura
			"IdProponente" => $pedido['idPessoa'],
			"ProtocoloSIS" => '', //$protocolo['idProtocolo'],
			"NumeroProcesso" => $pedido['NumeroProcesso'],
			"NotaEmpenho" => $pedido['NumeroNotaEmpenho'],
			"EmissaoNE" => $pedido['DataEmissaoNotaEmpenho'],
			"EntregaNE" => $pedido['DataEntregaNotaEmpenho'],
			"Assinatura" => "",
			"Cargo" => "",
			"Contratos" => $pedido['idContratos'],
			"Status" => $pedido['estado']
		);
		
		$i++;
	}
	return $x;
}

function txtParcelas($idPedido,$numero)
{
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido'";
	$query = mysqli_query($con,$sql);
	$i = 1;
	$num_parcelas = mysqli_num_rows($query);
	
		if($num_parcelas > 1){
			while($parcela = mysqli_fetch_array($query)){
				$x[$i]['valor'] = $parcela['valor'];
				$x[$i]['vencimento'] = $parcela['vencimento'];
				$i++;
			}
			
			$k = 1;
			$texto = "";
			for($k = 1; $k <= $numero; $k++){
				if($x[$k]['valor'] != 0){
					$texto .= $k."ª parcela de R$ ".dinheiroParaBr($x[$k]['valor']).". Entrega de documentos a partir de ".exibirDataBr($x[$k]['vencimento']).".\n";		
				}
			}
			$texto .= "O pagamento de cada parcela se dará no 20º (vigésimo) dia após a data de entrega de toda documentação correta relativa ao pagamento.";
			
			return $texto;
		}
		else
		{
			return "O pagamento se dará no 20º (vigésimo) dia após a data de entrega de toda documentação correta relativa ao pagamento.";
		}
}

function txtParcelasFormacao($idPedido,$numero)
{
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido'";
	$query = mysqli_query($con,$sql);
	$i = 1;
	$num_parcelas = mysqli_num_rows($query);
	
		if($num_parcelas > 1){
			while($parcela = mysqli_fetch_array($query)){
				$x[$i]['valor'] = $parcela['valor'];
				$x[$i]['vencimento'] = $parcela['vencimento'];
				$i++;
			}
			
			$k = 1;
			$texto = "";
			for($k = 1; $k <= $numero; $k++){
				if($x[$k]['valor'] != 0){
					$texto .= $k."ª parcela de R$ ".dinheiroParaBr($x[$k]['valor']).". Entrega de documentos a partir de ".exibirDataBr($x[$k]['vencimento']).".\n";		
				}
			}
			$texto .= "O pagamento de cada parcela se dará em 8 (oito) dias úteis após a data de confirmação da correta execução do(s) serviço(s).​";
			
			return $texto;
		}
		else
		{
			return "O pagamento se dará no 20º (vigésimo) dia após a data de entrega de toda documentação correta relativa ao pagamento.";
		}
}

function listaGrupo($idPedido){
	$con = bancoMysqli();
	$sql_grupos = "SELECT * FROM igsis_grupos WHERE idPedido = '$idPedido' and publicado = '1'";
	$query_grupos = mysqli_query($con,$sql_grupos);
	$num = mysqli_num_rows($query_grupos);
	if($num > 0){
		$txt = "";
		while($grupo = mysqli_fetch_array($query_grupos)){
			$txt .= $grupo['nomeCompleto']." CPF: ".$grupo['cpf']." RG: ".$grupo['rg']."\n";
		}
	}else{
		$txt = "Não há integrantes de grupo inseridos";
	}
	return $txt;

}
/*
function grupos($idPedido){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_grupos WHERE idPedido = '$idPedido' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	$y = array();
	$i = 0;
	while($x = mysqli_fetch_array($query)){
		$y[$i]['nomeCompleto'] = $x['nomeCompleto'];
		$y[$i]['rg'] = $x['rg'];
		$y[$i]['cpf'] = $x['cpf'];
		$y[$i]['assinatura'] = "";
		$i++;
	}
	$y['numero'] = $i;
	return $y;
}
*/


function grupos($idPedido){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_grupos WHERE idPedido = '$idPedido' AND publicado = '1'";
	$query = mysqli_query($con,$sql);
	$y = array();
	$i = 0;
	$texto = "";
	while($x = mysqli_fetch_array($query)){
		$y[$i]['nomeCompleto'] = $x['nomeCompleto'];
		$y[$i]['rg'] = $x['rg'];
		$y[$i]['cpf'] = $x['cpf'];
		$y[$i]['assinatura'] = "";
		$texto .= $x['nomeCompleto'].", RG nº ".$x['rg'].", CPF nº ".$x['cpf'].", ";
		$i++;
	}
	$y['texto'] = substr($texto,0,-1);
	$y['numero'] = $i;
	return $y;
}


function sobrenome($string){
	$partes = explode(' ', $string);
	$primeiroNome = array_shift($partes);
	$ultimoNome = array_pop($partes);  
  return $primeiroNome." ".$ultimoNome;
  
}

function geraOpcaoContrato($id){
	$con = bancoMysqli();
	$sql = "SELECT * FROM ig_usuario WHERE publicado = '1' AND (contratos = '1' OR contratos = '2')  ORDER BY nomeCompleto";
	$query = mysqli_query($con,$sql);
	while($user = mysqli_fetch_array($query)){
		if($user['idUsuario'] == $id){
			echo "<option value='".$user['idUsuario']."' selected>".$user['nomeCompleto']."</option>";	
		}else{
			echo "<option value='".$user['idUsuario']."'>".$user['nomeCompleto']."</option>";			
		}
	}
}



function geraOpcaoEstado($select,$area){ //gera os options de um select
	if($area == "0"){
		$sql = "SELECT * FROM sis_estado ORDER BY ordem";
	}else{
		$sql = "SELECT * FROM sis_estado WHERE area = '$area' ORDER BY ordem";
	}
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_row($query)){
		if($option[0] == $select){
			echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
		}else{
			echo "<option value='".$option[0]."'>".$option[1]."</option>";	
		}
	}
}


function atualizaStatus($idPedido){
	$con = bancoMysqli();
	$pedido = recuperaDados("igsis_pedido_contratacao",$idPedido,"idPedidoContratacao");
	
	//Inicio do algorítimo
	
	if($pedido['estado'] == 11){  //Se estão nesses estados, não haverá atualização
		
		$txt = "Não houve atualização de status (1)";

		
	}else{ // Esses estados restantes permitem alteração automática
	
		if(trim($pedido['NumeroNotaEmpenho']) != "" && $pedido['NumeroNotaEmpenho'] != NULL){ // Se há um Número de Empenho Válido

			$sql = "UPDATE igsis_pedido_contratacao SET estado = '10' WHERE idPedidoContratacao = '$idPedido'";
			
			$txt = "O pedido $idPedido mudou seu status para 10 (2)";
			
			
		}else{ // Se não há um Número de Empenho Válido
			
			if(trim($pedido['DataReserva']) != "" && $pedido['DataReserva'] != NULL && $pedido['DataReserva'] != '0000-00-00'){ //Se há um pedido de reserva

				$sql = "UPDATE igsis_pedido_contratacao SET estado = '6' WHERE idPedidoContratacao = '$idPedido'";

				$txt = "O pedido $idPedido mudou seu status para 6 (3)";
				
				
			}else{ // Se não há um Pedido de Reserva

				if(trim($pedido['DataProposta']) != "" && $pedido['DataProposta'] != NULL && $pedido['DataProposta'] != '0000-00-00'){ //Se já foi gerado uma Proposta

					$sql = "UPDATE igsis_pedido_contratacao SET estado = '5' WHERE idPedidoContratacao = '$idPedido'";

					$txt = "O pedido $idPedido mudou seu status para 5 (4)";


				}else{ //Caso não tenha sido gerado uma Proposta
					
					if($pedido['NumeroProcesso'] != NULL && trim($pedido['NumeroProcesso']) != ""){ // Caso possua um Número de Processo SEI
					
						$sql = "UPDATE igsis_pedido_contratacao SET estado = '4' WHERE idPedidoContratacao = '$idPedido'";

						$txt = "O pedido $idPedido mudou seu status para 4 (5)";

					
					}else{ // Caso não possua ainda um Número de Processo SEI
						
						if(trim($pedido['DataContrato']) != "" && $pedido['DataContrato'] != NULL && $pedido['DataContrato'] != '0000-00-00'){ //Caso o contrato tenha visto						
							$sql = "UPDATE igsis_pedido_contratacao SET estado = '3' WHERE idPedidoContratacao = '$idPedido'";
			
							$txt = "O pedido $idPedido mudou seu status para 3 (6)";

							
						}else{
							
							$txt =  "Não houve atualização de status (7)";
						}
					
						
					}
					
				}
				
			}
			
		}
	
		
	}
	
	if(isset($sql)){
		$query = mysqli_query($con,$sql);
		if($query){
			return $txt;	
		}else{
			return "Erro ao atualizar status";	
		}
		
	}
}

function atualizaEstado($idPedido){
	$con = bancoMysqli();
	$pedido = recuperaDados("igsis_pedido_contratacao",$idPedido,"idPedidoContratacao");
	
	//Inicio do algorítimo
	
	if($pedido['estado'] == 11){  //Se estão nesses estados, não haverá atualização
		
	}else{ // Esses estados restantes permitem alteração automática
	
		if(trim($pedido['NumeroNotaEmpenho']) != "" && $pedido['NumeroNotaEmpenho'] != NULL){ // Se há um Número de Empenho Válido
			$sql = "UPDATE igsis_pedido_contratacao SET estado = '10' WHERE idPedidoContratacao = '$idPedido'";
			
		}else{ // Se não há um Número de Empenho Válido
			
			if(trim($pedido['DataReserva']) != "" && $pedido['DataReserva'] != NULL && $pedido['DataReserva'] != '0000-00-00'){ //Se há um pedido de reserva
				$sql = "UPDATE igsis_pedido_contratacao SET estado = '6' WHERE idPedidoContratacao = '$idPedido'";
				
				
			}else{ // Se não há um Pedido de Reserva
				if(trim($pedido['DataProposta']) != "" && $pedido['DataProposta'] != NULL && $pedido['DataProposta'] != '0000-00-00'){ //Se já foi gerado uma Proposta
					$sql = "UPDATE igsis_pedido_contratacao SET estado = '5' WHERE idPedidoContratacao = '$idPedido'";
				}else{ //Caso não tenha sido gerado uma Proposta
					
					if($pedido['NumeroProcesso'] != NULL && trim($pedido['NumeroProcesso']) != ""){ // Caso possua um Número de Processo SEI
					
						$sql = "UPDATE igsis_pedido_contratacao SET estado = '4' WHERE idPedidoContratacao = '$idPedido'";
					
					}else{ // Caso não possua ainda um Número de Processo SEI
						
						if(trim($pedido['DataContrato']) != "" && $pedido['DataContrato'] != NULL && $pedido['DataContrato'] != '0000-00-00'){ //Caso o contrato tenha visto						
							$sql = "UPDATE igsis_pedido_contratacao SET estado = '3' WHERE idPedidoContratacao = '$idPedido'";
							
						}else{
							
							return "Não houve atualização de status";
						}
					
						
					}
					
				}
				
			}
			
		}
	
		
	}
	
	if(isset($sql)){
		$query = mysqli_query($con,$sql);
		if($query){
			return "Status atualizado";	
		}else{
			return "Erro ao atualizar status";	
		}
		
	}
}

function dataProposta($idPedido){
	$con = bancoMysqli();
	$dataAgora = date('Y-m-d H:s:i');
	$sql = "UPDATE igsis_pedido_contratacao SET DataProposta = '$dataAgora' WHERE idPedidoContratacao = '$idPedido'";
	$query = mysqli_query($con,$sql);
	if($query){
		atualizaEstado($idPedido);
	}
}

function dataReserva($idPedido){
	$con = bancoMysqli();
	$dataAgora = date('Y-m-d H:s:i');
	$sql = "UPDATE igsis_pedido_contratacao SET DataReserva = '$dataAgora' WHERE idPedidoContratacao = '$idPedido'";
	$query = mysqli_query($con,$sql);
	if($query){
		atualizaEstado($idPedido);
	}
}

function dataPagamento($idPedido){
	$con = bancoMysqli();
	$dataAgora = date('Y-m-d H:s:i');
	$sql = "UPDATE igsis_pedido_contratacao SET DataPagamento = '$dataAgora' WHERE idPedidoContratacao = '$idPedido'";
	$query = mysqli_query($con,$sql);
	if($query){
		$sql2 = "UPDATE igsis_pedido_contratacao SET estado = 14 WHERE idPedidoContratacao = '$idPedido'";
		$query = mysqli_query($con,$sql2);
	}else{
			return "Erro ao atualizar status";	
		}
}

function gravaPenalidade($idPedido,$idPenalidade){
	$con = bancoMysqli();
	$sql = "UPDATE igsis_pedido_contratacao SET idPenalidade = '$idPenalidade' WHERE idPedidoContratacao = '$idPedido'";
	$query = mysqli_query($con,$sql);
	if($query){
		return TRUE;	
	}else{
		return FALSE;
	}
	
}

function retornaPeriodoVigencia($idPedido){
	$con = bancoMysqli();
	$sql01 = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido' AND valor > '0' ORDER BY vigencia_inicio ASC  LIMIT 0,1 ";
	$sql02 = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido' AND valor > '0' ORDER BY vigencia_final DESC LIMIT 0,1 ";	
	$query01 = mysqli_query($con, $sql01);
	$query02 = mysqli_query($con, $sql02);
	$i = mysqli_fetch_array($query01);
	$j = mysqli_fetch_array($query02);
	return exibirDataBr($i['vigencia_inicio'])." a ".exibirDataBr($j['vigencia_final']);

}

function retornaEstado($idEstado){
	$estado = recuperaDados("sis_estado",$idEstado,"idEstado");
	return $estado['estado'];
}

function retornaCargaHoraria($idPedido,$parcelas){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido' ORDER BY numero ASC";
	$query = mysqli_query($con,$sql);
	$i= 1;
	while($hora = mysqli_fetch_array($query)){
		$carga[$i] = $hora['horas'];
		$i++;
	}
	
	$total = 0;
	
	for($i = 1; $i <= $parcelas; $i++){
		$total = $total + $carga[$i];
	}
	return $total;
}

function retornaParcelaPagamento($idPedido){
	$con = bancoMysqli();
	$sql = "SELECT * FROM igsis_parcelas WHERE idPedido = '$idPedido' AND valor <> '0' ORDER BY numero ASC";
	$query = mysqli_query($con,$sql);
	$i = 1;
	while($parcela = mysqli_fetch_array($query)){
	
	$x[$i]['valor'] = dinheiroParaBr($parcela['valor']);
	$x[$i]['horas'] = $parcela['horas'];
	$x[$i]['periodo'] = exibirDataBr($parcela['vigencia_inicio'])." a ".exibirDataBr($parcela['vigencia_final']);
	$x[$i]['pagamento'] = exibirDataBr($parcela['vencimento']);
	$x[$i]['vigencia_inicio'] = exibirDataBr($parcela['vigencia_inicio']);
	$x[$i]['vigencia_final'] = exibirDataBr($parcela['vigencia_final']);	
	$i++;
	}
	$x['total'] = $i - 1;
	
	return $x;

}


function recuperaVerba($idverba){ //dados da tabela sis_verba
	$con = bancoMysqli();
	$sql = "SELECT * FROM sis_verba WHERE Id_Verba = '$idverba'";
		$query = mysqli_query($con,$sql);
		$x = mysqli_fetch_array($query);
		$y['dotacao'] = $x['DotacaoOrcamentaria']; 
		$y['detalhamento'] = $x['DetalhamentoAcao'];
		$y['reservapj'] = $x['NumeroReservaPJ'];
		$y['linkpj'] = $x['LinkPJ'];
		$y['reservapf'] = $x['NumeroReservaPF'];
		$y['linkpf'] = $x['LinkPF'];
		$y['vocativo'] = $x['Vocativo'];
		$y['NovoNumeroReservaPJ'] = $x['NovoNumeroReservaPJ'];
		
		return $y;
}


function geraOpcaoOrdem($tabela,$order){ //gera os options de um select com ordenação
	$sql = "SELECT * FROM $tabela ORDER BY $order";	
	$con = bancoMysqli();
	$query = mysqli_query($con,$sql);
	while($option = mysqli_fetch_row($query)){
		if($option[0] == $select){
			echo "<option value='".$option[0]."' selected >".$option[1]."</option>";	
		}else{
			echo "<option value='".$option[0]."'>".$option[1]."</option>";	
		}
	}
}

function lista_prazo($tipoPessoa,$num_registro,$pagina,$ordem){
	$con = bancoMysqli();
		
	if($tipoPessoa == "todos"){
		$tipo = "";	
	}else{
		$tipo = " AND tipoPessoa = '$tipoPessoa' ";
	}
	
	
	$sql_lista_total = "
		SELECT ped.idEvento, ped.idPedidoContratacao, ped.tipoPessoa, ped.idPessoa, ped.instituicao 
		FROM igsis_pedido_contratacao AS ped
		INNER JOIN ig_evento AS eve ON ped.idEvento = eve.idEvento
		WHERE eve.dataEnvio AND eve.publicado=1 IS NULL ORDER BY eve.idEvento DESC";
	$query_lista_total = mysqli_query($con,$sql_lista_total);
	$total_registros = mysqli_num_rows($query_lista_total);
	$pag = $pagina - 1;
	$registro_inicial = $num_registro * $pag;
	$total_paginas = $total_registros / $num_registro; // gera o número de páginas
	$sql_lista_pagina = "
		SELECT ped.idEvento, ped.idPedidoContratacao, ped.tipoPessoa, ped.idPessoa, ped.instituicao 
		FROM igsis_pedido_contratacao AS ped
		INNER JOIN ig_evento AS eve ON ped.idEvento = eve.idEvento
		WHERE eve.dataEnvio IS NULL AND eve.publicado=1 ORDER BY eve.idEvento DESC LIMIT $registro_inicial,$num_registro";
	$query_lista_pagina = mysqli_query($con,$sql_lista_pagina);
	//$x = $sql_lista_pagina;
	$i = 0;
	while($pedido = mysqli_fetch_array($query_lista_pagina)){
		$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$local = listaLocais($pedido['idEvento']);
		$local_juridico = listaLocaisJuridico($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$pessoa = recuperaPessoa($pedido['idPessoa'],$tipoPessoa);
		$fiscal = recuperaUsuario($evento['idResponsavel']);
				
				
		$x[$i] = array(
		    "idPedido" => $pedido['idPedidoContratacao'],
			"idEvento" => $pedido['idEvento'], 
			"TipoPessoa" => $pedido['tipoPessoa'],
			"Objeto" => retornaTipo($evento['ig_tipo_evento_idTipoEvento'])." - ".$evento['nomeEvento'] ,
			"Local" => substr($local,1) , //retira a virgula no começo da string
			"LocalJuridico" => substr($local,1) , //retira a virgula no começo da string
			"Periodo" => $periodo, 
			"Duracao" => $duracao, 			
			"DataCadastro" => $evento['dataEnvio'],
			"Fiscal" => $fiscal['nomeCompleto'] ,
			"Horario" => "", //SPCultura
			"IdProponente" => $pedido['idPessoa']
		);
		
		$i++;
	}
	return $x;
}

?>