0<?php
require "../funcoes/funcoesSiscontrat.php";
?>
	
	  	  
	 <!-- inicio_list -->
	 <br /><br /><br />
	<section id="list_items">
		<div class="container">
			 <div class="sub-title">RELATORIO DEBORA</div>
			<div class="table-responsive list_info">
				<table class="table table-condensed"><script type=text/javascript language=JavaScript src=../js/find2.js> </script>
					<thead>
						<tr class="list_menu">
							<td>Codigo do Pedido</td>
							<td>Proponente</td>
							<td>Tipo</td>
							<td>Objeto</td>
							<td>Local</td>
							<td>Periodo</td>
							<td>Valor</td>
							<td>Parcelas</td>
							<td>Verba</td>
                            <td>Processo</td>
							<td>Dotação</td>
							<td>Finanças</td>
						</tr>
					</thead>
				
<?php

$data=date('Y');
$con = bancoMysqli();
$sql_pf = "SELECT * FROM igsis_pedido_contratacao WHERE estado IS NOT NULL AND (tipoPessoa = 4 OR tipoPessoa = 1 OR tipoPessoa = 2) AND publicado = 1 ORDER BY idPedidoContratacao DESC LIMIT 0,50000";
$query_pf = mysqli_query($con,$sql_pf);


while($pedido = mysqli_fetch_array($query_pf))
 {
	 
	$ped = siscontrat($pedido['idPedidoContratacao']);
	$verba = recuperaDados("sis_verba",$ped['Verba'],"Id_Verba");
	echo '<tr><td class="list_description">'.$pedido['idPedidoContratacao'].'</td> ';
	if($pedido['tipoPessoa'] == 2){
		$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
		echo '<td class="list_description">'.$pessoa['RazaoSocial'].'</td>';
		echo '<td class="list_description">Juridica</td>';
	}
	
	if($pedido['tipoPessoa'] == 1){
		$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
		echo '<td class="list_description">'.$pessoa['Nome'].'</td>';
		echo '<td class="list_description">Fisica</td>';
	}	
	if($pedido['tipoPessoa'] == 4){
		$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
		echo '<td class="list_description">'.$pessoa['Nome'].'</td>';
		echo '<td class="list_description">Formacao</td>';
	}	


	echo '<td class="list_description">'.$ped['Objeto'].'</td>';
	echo '<td class="list_description">'.$ped['Local'].'</td> ';

	

	echo '<td class="list_description">'.$ped['Periodo'].'</td> ';
	echo '<td class="list_description">'.dinheiroParaBr($ped['ValorGlobal']).'</td> ';
	echo '<td class="list_description">'.$ped['parcelas'].'</td> ';
	echo '<td class="list_description">'.$verba['Verba'].'</td> ';
	echo '<td class="list_description">'.$ped['NumeroProcesso'].'</td>';
	echo '<td class="list_description">'.$ped['ComplementoDotacao'].'</td>';
	echo '<td class="list_description">'.$ped['aprovacaoFinanca'].'</td> </tr>';
	}

?>
	
					

				</table>
			</div>
		</div>
	</section>

<!--fim_list-->