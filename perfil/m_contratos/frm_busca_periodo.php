<?php include 'includes/menu.php';?>

<?php 
			/* query com filtro de estado
			$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado ='1' 
			AND (estado = '1' OR
			estado = '2' OR
			estado = '3' OR
			estado = '4' OR
			estado = '5' OR
			estado = '6' )
			
			
			ORDER BY idPedidoContratacao DESC";
*/

function retornaDataInicio($idEvento){ //retorna o período
	$con = bancoMysqli();
	$sql_anterior = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataInicio ASC LIMIT 0,1"; //a data inicial mais antecedente
	$query_anterior = mysqli_query($con,$sql_anterior);
	$data = mysqli_fetch_array($query_anterior);
	$data_inicio = $data['dataInicio'];
	
	$sql_posterior01 = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataFinal DESC LIMIT 0,1"; //quando existe data final
	$sql_posterior02 = "SELECT * FROM ig_ocorrencia WHERE idEvento = '$idEvento' AND publicado = '1' ORDER BY dataInicio DESC LIMIT 0,1"; //quando há muitas datas únicas
	
	$query_anterior01 = mysqli_query($con,$sql_posterior01);
	$data = mysqli_fetch_array($query_anterior01);
	$num = mysqli_num_rows($query_anterior01);
	
	if(($data['dataFinal'] != '0000-00-00') OR ($data['dataFinal'] != NULL)){  //se existe uma data final e que é diferente de NULO
		$dataFinal01 = $data['dataFinal'];	
	}

	$query_anterior02 = mysqli_query($con,$sql_posterior02); //recupera a data única mais tarde
	$data = mysqli_fetch_array($query_anterior02);
	$dataFinal02 = $data['dataInicio'];
	
		
	if(isset($dataFinal01)){ //se existe uma temporada, compara com a última data única
		if($dataFinal01 > $dataFinal02){
			$dataFinal = $dataFinal01;
		}else{
			$dataFinal = $dataFinal02;
		}
	}else{
		$dataFinal = $dataFinal02;		
	}
	
	if($data_inicio == $dataFinal){ 
		return $data_inicio;
	}else{
		return $data_inicio;
	}
	
}

?>


<?php  

if(isset($_POST['periodo'])){
$inicio = exibirDataMysql($_POST['inicio']);
$final = exibirDataMysql($_POST['final']);	
$con = bancoMysqli();
$sql_evento = "SELECT DISTINCT idEvento FROM igsis_agenda WHERE data BETWEEN '$inicio' AND '$final'  ORDER BY data ASC ";
$query_evento = mysqli_query($con,$sql_evento);
$num = mysqli_num_rows($query_evento);

//gera uma array
$i = 0;
	while($evento = mysqli_fetch_array($query_evento)){
		$idEvento = $evento['idEvento'];
		$dataInicio = strtotime(retornaDataInicio($idEvento));
		if($dataInicio >= strtotime($inicio) AND $dataInicio <= strtotime($final)){
		
		$event = recuperaDados("ig_evento",$idEvento,"idEvento");
		if($event['dataEnvio'] != NULL AND $event['publicado']){ // se o evento estiver publicado e tiver sido enviado 
			$sql_pedido = "SELECT * FROM igsis_pedido_contratacao WHERE idEvento = '$idEvento' AND publicado ='1' 
			ORDER BY idPedidoContratacao DESC";
			$query_pedido = mysqli_query($con,$sql_pedido);
			while($pedido = mysqli_fetch_array($query_pedido)){
				$usuario = recuperaDados("ig_usuario",$event['idUsuario'],"idUsuario");
				$instituicao = recuperaDados("ig_instituicao",$event['idInstituicao'],"idInstituicao");
				$local = listaLocais($pedido['idEvento']);
				$periodo = retornaPeriodo($pedido['idEvento']);
				$operador = recuperaUsuario($pedido['idContratos']);
				if($pedido['parcelas'] > 1){
					$valorTotal = somaParcela($pedido['idPedidoContratacao'],$pedido['parcelas']);
					$formaPagamento = txtParcelas($pedido['idPedidoContratacao'],$pedido['parcelas']);	
				}else{
					$valorTotal = $pedido['valor'];
					$formaPagamento = $pedido['formaPagamento'];
				}

			if ( $pedido ['estado'] == 1 OR $pedido ['estado'] == 2 OR 	$pedido ['estado'] == 3 OR $pedido ['estado'] == 4 OR $pedido ['estado'] == 5 OR
			$pedido ['estado'] == 6 OR $pedido ['estado'] == 13) {
				
			$x[$i]['id']= $pedido['idPedidoContratacao'];
			$x[$i]['objeto'] = retornaTipo($event['ig_tipo_evento_idTipoEvento'])." - ".$event['nomeEvento'];
			if($pedido['tipoPessoa'] == 1){
				$pessoa = recuperaDados("sis_pessoa_fisica",$pedido['idPessoa'],"Id_PessoaFisica");
				$x[$i]['proponente'] = $pessoa['Nome'];
				$x[$i]['tipo'] = "Física";
			}else{
				$pessoa = recuperaDados("sis_pessoa_juridica",$pedido['idPessoa'],"Id_PessoaJuridica");
				$x[$i]['proponente'] = $pessoa['RazaoSocial'];
				$x[$i]['tipo'] = "Jurídica";

			}
			$x[$i]['local'] = substr($local,1);
			$x[$i]['instituicao'] = $instituicao['sigla'];
			$x[$i]['periodo'] = $periodo;
			$x[$i]['status'] = $pedido['estado'];	
			$x[$i]['operador'] = $operador['nomeCompleto'];		
			$i++;
			}

			}
			}
		}
		
	}	
	

$x['num'] = $i;


if($num > 0){ ?>

<br />
<br />
	<section id="list_items">
		<div class="container">
			 <h3>Resultado da busca</3>
             <h5>Foram encontrados <?php echo $x['num']; ?> pedidos de contratação.</h5>
             <h5><a href="?perfil=contratos&p=frm_busca_periodo">Fazer outra busca</a></h5>
			<div class="table-responsive list_info">
				<table class="table table-condensed">
					<thead>
						<tr class="list_menu">
							<td>Codigo do Pedido</td>
							<td>Proponente</td>
							<td>Tipo</td>
							<td>Objeto</td>
							<td width="20%">Local</td>
                            <td>Instituição</td>
							<td>Periodo</td>
							<td>Status</td>
   							<td>Operador</td>
						</tr>
					</thead>
					<tbody>

<?php
$data=date('Y');
for($h = 0; $h < $x['num']; $h++)
 {
	 $status = recuperaDados("sis_estado",$x[$h]['status'],"idEstado");
	if($x[$h]['tipo'] == 'Física'){
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapf&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
	}else{
		echo "<tr><td class='lista'> <a href='?perfil=contratos&p=frm_edita_propostapj&id_ped=".$x[$h]['id']."'>".$x[$h]['id']."</a></td>";
		
	}
	echo '<td class="list_description">'.$x[$h]['proponente'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['tipo'].					'</td> ';
	echo '<td class="list_description">'.$x[$h]['objeto'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['local'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['instituicao'].				'</td> ';
	echo '<td class="list_description">'.$x[$h]['periodo'].						'</td> ';
	echo '<td class="list_description">'.$status['estado'].						'</td> ';
	echo '<td class="list_description">'.$x[$h]['operador'].						'</td> </tr>';

	}
?>                    
                    


					</tbody>
				</table>
			</div>
		</div>
	</section>

<?php /*
echo "<pre>";
var_dump($x);
echo "</pre>";
*/
?>

<?php
}else{
?>

	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por período</h2>
                     <p><?php if(isset($mensagem)){ echo $num; }?></p>
                    <p>É preciso ao menos um critério de busca ou você pesquisou por um pedido inexistente. Tente novamente.</p>

					</div>
				  </div>
			  </div>
			  


	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
            <form method="POST" action="?perfil=contratos&p=frm_busca_periodo" class="form-horizontal" role="form">
                <div class="form-group">
                	<div class="col-md-offset-2 col-md-6">
               			 <label>Data início *</label>
                		<input type="text" name="inicio" class="form-control" id="datepicker01" placeholder="">
               		 </div>
                	<div class=" col-md-6">
                		<label>Data encerramento *</label>
                		<input type="text" name="final" class="form-control" id="datepicker02"  placeholder="">
               		</div>
                </div>
                            

            	</div>
             </div>
				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="periodo" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>


            </div>
	</section>
    <?php } 
}else{
?>

	 <section id="services" class="home-section bg-white">
		<div class="container">
			  <div class="row">
				  <div class="col-md-offset-2 col-md-8">
					<div class="section-heading">
					 <h2>Busca por período</h2>
                     <p><?php if(isset($mensagem)){ echo $num; }?></p>

					</div>
				  </div>
			  </div>
			  


	        <div class="row">
            <div class="form-group">
            	<div class="col-md-offset-2 col-md-8">
            <h5><?php if(isset($mensagem)){ echo $mensagem; } ?>
            <form method="POST" action="?perfil=contratos&p=frm_busca_periodo" class="form-horizontal" role="form">
                <div class="form-group">
                	<div class="col-md-offset-2 col-md-6">
               			 <label>Data início *</label>
                		<input type="text" name="inicio" class="form-control" id="datepicker01" placeholder="">
               		 </div>
                	<div class=" col-md-6">
                		<label>Data encerramento *</label>
                		<input type="text" name="final" class="form-control" id="datepicker02"  placeholder="">
               		</div>
                </div>
                            

            	</div>
             </div>
				<br />             
	            <div class="form-group">
		            <div class="col-md-offset-2 col-md-8">
                	<input type="hidden" name="periodo" value="1" />
    		        <input type="submit" class="btn btn-theme btn-lg btn-block" value="Pesquisar">
                    </form>
        	    	</div>
        	    </div>


            </div>
	</section>
<?php } ?>