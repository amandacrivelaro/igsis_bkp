<?php 


function Listar($tipoPessoa){	
$con = bancoMysqli();

	if($tipoPessoa == "todos"){
		$tipo = "";	
	}
	
	$sql_lista_total = "SELECT * FROM ig_evento AS eve INNER JOIN igsis_pedido_contratacao AS ped ON eve.idEvento=ped.idEvento WHERE eve.publicado = '1' AND ped.publicado = '1' $tipo AND idContratos IS NULL ORDER BY eve.idEvento DESC";
	$query_lista_total = mysqli_query($con,$sql_lista_total);
	//$total_registros = mysqli_num_rows($query_lista_total);
	//$pag = $pagina - 1;
	//$registro_inicial = $num_registro * $pag;
	//$total_paginas = $total_registros / $num_registro; // gera o número de páginas
	//$sql_lista_pagina = "SELECT * FROM ig_evento AS eve INNER JOIN igsis_pedido_contratacao AS ped ON eve.idEvento=ped.idEvento WHERE eve.publicado = '1' AND ped.publicado = '1' AND idContratos IS NULL ORDER BY eve.idEvento DESC";
	//	$query_lista_pagina = mysqli_query($con,$sql_lista_pagina);
	//$x = $sql_lista_pagina;
	$i = 0;
	while($pedido = mysqli_fetch_array($query_lista_total)){
		$evento = recuperaDados("ig_evento",$pedido['idEvento'],"idEvento"); //$tabela,$idEvento,$campo
		$usuario = recuperaDados("ig_usuario",$evento['idUsuario'],"idUsuario");
		$instituicao = recuperaDados("ig_instituicao",$usuario['idInstituicao'],"idInstituicao");
		$local = listaLocais($pedido['idEvento']);
		$local_juridico = listaLocaisJuridico($pedido['idEvento']);
		$periodo = retornaPeriodo($pedido['idEvento']);
		$duracao = retornaDuracao($pedido['idEvento']);
		$pessoa = recuperaPessoa($pedido['idPessoa'],$tipoPessoa);//recuperaDados("sis_protocolo",$pedido['idEvento'],"idEvento");
		$verba = recuperaDados("sis_verba",$pedido['idVerba'],"Id_Verba");
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
			"Periodo" => $periodo, 
			"Duracao" => $duracao, 
			"Verba" => $pedido['idVerba'] ,
			"IdProponente" => $pedido['idPessoa'],
			"Instituicao" => $instituicao['instituicao'],
			"Sigla" => $instituicao['sigla'],
			"Status" => $pedido['estado']
		);
		
		$i++;
	}
	return $x;
}

include 'includes/menu.php';


$linha_tabela_lista = Listar("todos"); //esse gera uma array com os pedidos

//$tipoPessoa,$instituicao,$num_registro,$pagina,$ordem,$estado

$link="index.php?perfil=contratos&p=frm_edita_propostapf&id_ped=";

?>
	
<?php include 'includes/menu.php';?>	
	  	  
	 <!-- inicio_list -->
	<section id="list_items">
		<div class="container">
			 <div class="sub-title">PEDIDO DE CONTRATAÇÃO DE PESSOA FÍSICA</div>
			<div class="table-responsive list_info">
				<table class="table table-condensed"><script type=text/javascript language=JavaScript src=../js/find2.js> </script>
					<thead>
						<tr class="list_menu">
							<td>Codigo do Pedido</td>
							<td>Proponente</td>
							<td>Objeto</td>
							<td>Local</td>
							<td>Periodo</td>
                            <td>Processo</td>
                            <td>Status</td>
						</tr>
					</thead>
					<tbody>
<?php
$data=date('Y');
for($i = 0; $i < count($linha_tabela_lista); $i++)
 {
	 
	$linha_tabela_pedido_contratacaopf = recuperaDados("sis_pessoa_fisica",$linha_tabela_lista[$i]['IdProponente'],"Id_PessoaFisica");
	$chamado = recuperaAlteracoesEvento($linha_tabela_lista[$i]['idEvento']);	 
	echo "<tr><td class='lista'> <a href='".$link.$linha_tabela_lista[$i]['idPedido']."'>".$linha_tabela_lista[$i]['idPedido']."</a></td>";
	echo '<td class="list_description">'.$linha_tabela_pedido_contratacaopf['Nome'].'</td> ';
	echo '<td class="list_description">'.$linha_tabela_lista[$i]['Objeto'].' [';
					if($chamado['numero'] == '0'){
						echo "0";
					}else{
						echo "<a href='?perfil=chamado&p=evento&id=".$linha_tabela_lista[$i]['idEvento']."' target='_blank'>".$chamado['numero']."</a>";	
					}
					
	echo '] </td> ';
	echo '<td class="list_description">'.$linha_tabela_lista[$i]['Local'].'</td> ';
	echo '<td class="list_description">'.$linha_tabela_lista[$i]['Periodo'].'</td> ';
	echo '<td class="list_description">'.$linha_tabela_lista[$i]['NumeroProcesso'].'</td>';
	echo '<td class="list_description">'.$linha_tabela_lista[$i]['Status'].'</td> </tr>';
	}

?>
	
					
					</tbody>
				</table>
			</div>
		</div>
	</section>

<!--fim_list-->