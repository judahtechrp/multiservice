<!-- Content ================================================== -->
<link href="css/color_scheme.css" rel="stylesheet"> 
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<?php
if(!isset($_POST['opcao_delivery']) || $cart->isEmpty()):
	header("Location: {$site}{$Url[0]}");
endif;

$bairrosstatus = 'false';
$pedidos = '';
$allItems = $cart->getItems();
foreach ($allItems as $items) {
	foreach ($items as $item) {
		if(!empty($item['attributes']['totalAdicionais'])):
			$todosOsAdicionais = '';
			$todosOsAdicionaisSoma = 0;
			for($i=0; $i < $item['attributes']['totalAdicionais']; $i++):
				$todosOsAdicionais = $todosOsAdicionais.$item['attributes']['adicional_nome'.$i].', ';
				$todosOsAdicionaisSoma = ($todosOsAdicionaisSoma + $item['attributes']['adicional_valor'.$i]);
			endfor;
		endif;							

		$pedidos = $pedidos.'<b>'.$texto['msg_qtd'].'</b> '
		.$item['quantity'].'x '
		.$item['attributes']['nome']
		.(!empty($item['attributes']['adicionais_gratis']) ? '<br /><b>Adicionais grátis:</b><br />'.$item['attributes']['adicionais_gratis'] : '')

		.(!empty($item['attributes']['totalAdicionais']) ? '<br /><b>Adicionais pagos:</b><br />'.$todosOsAdicionais : '')


		.'<br />'

		.(!empty($item['attributes']['opcao_unica']) ? '<b>Opção: </b>'.$item['attributes']['opcao_unica'].'<br />' : '')
		.'<b>'.$texto['msg_valor'].'</b> R$ '.Check::Real(($item['attributes']['preco'] * $item['quantity']) + (!empty($item['attributes']['totalAdicionais']) ? ($todosOsAdicionaisSoma * $item['quantity']) : 0) )
		.'<br /><b>OBS:</b> '.$item['attributes']['observacao']

		.'<br /><br />';
	}
}

function tirarAcentos($string){
	$formato = array();
	$formato['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
	$formato['b'] = 'AAAAAAAcEEEEIIIIDNOOOOOOUUUUuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = strtr(utf8_decode($string), utf8_decode($formato['a']), $formato['b']);

	return utf8_encode($string);
}
?>

<div class="container">
	<div id="detalhes_pedido"></div>
	
	<div class="row">
		<div class="col-md-3">
			<div class="box_style_2 info">
				<h4 class="nomargin_top"><?=$texto['msg_delivery_time'];?> <i class="icon_clock_alt pull-right"></i></h4>
				<p>
					<?=(!empty($msg_tempo_delivery) ? $msg_tempo_delivery : "");?>
					<?=(!empty($minimo_delivery) && $minimo_delivery != '0.00' ? "<br /><b style='color:red;'>Valor mínimo:</b> R$ ".Check::Real($minimo_delivery) : '');?>
				</p>
				<hr />
				<h4 class="nomargin_top">Retirada no Balcão <i class="icon_clock_alt pull-right"></i></h4>
				<p>
					<?=(!empty($msg_tempo_buscar) ? $msg_tempo_buscar : "");?>
				</p>
			</div><!-- End box_style_1 -->

			<div class="box_style_2 hidden-xs" id="help">
				<i class="icon_lifesaver"></i>
				<h4><?=$texto['home_ajuda'];?></h4>
				<a href="https://api.whatsapp.com/send?1=pt_BR&phone=<?=(!empty($telefone_empresa) ? '55'.$telefone_empresa : '');?>" target="_blank" class="phone" style="color: #000000"><?=(!empty($telefone_empresa) ? formatPhone($telefone_empresa) : '(00) 00000-0000');?></a>
			</div>

		</div><!-- End col-md-3 -->
		<form id="getDadosPedidoCompleto" method="post">
			<div class="col-md-6">
				<div class="mg-b-10" align="center">
					<?php if($_POST['opcao_delivery'] == 'true'): ?>
						<img src="<?=$site;?>img/delivery.jpg" style="max-width: 100%;height: auto;" />
						<?php elseif($_POST['opcao_delivery'] == 'false'): ?>
							<img src="<?=$site;?>img/bcartao.jpg" style="max-width: 100%;height: auto;" />
							<?php elseif($_POST['opcao_delivery'] == 'false2'): ?>
								<img src="<?=$site;?>img/mesa.jpg" style="max-width: 100%;height: auto;" />
							<?php endif; ?>

						</div>	
						<div class="box_style_2" id="order_process">				
							<div style="text-transform: uppercase;font-weight: 700;font-size: 13px;color: #343a40;letter-spacing: 1px;"><i class="fa fa-caret-right"></i> <?=$texto['msg_msg_dadosabaixo'];?></div>	
							<br />				
							<div class="form-group">
								<label for="telefone"><span style="color: red;">* </span><?=$texto['msg_seu_tell'];?></label>
								<input required type="tel" id="telefone" name="telefone" class="form-control" placeholder="(99) 99999-9999" data-mask="(00) 00000-0000" maxlength="15">
							</div>	
							<div class="form-group">
								<label for="nome"><span style="color: red;">* </span><?=$texto['msg_seu_nome'];?></label>
								<input required type="text" class="form-control" id="nome" name="nome" placeholder="<?=$texto['msg_seu_nome'];?>">
							</div>

							<?php if($_POST['opcao_delivery'] == 'false2'):?>						
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label"><span style="color: red;">*</span> <?=$texto['msg_msg_Nmesa'];?></label>
											<input type="number" name="mesa" id="mesa" class="form-control numero" maxlength="2" required>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label class="form-control-label"><span style="color: red;">*</span> <?=$texto['msg_msg_qtdpessoas'];?></label>
											<input type="number" name="pessoas" id="pessoas" class="form-control numero" maxlength="2" required>
										</div>
									</div>
								</div> 
							<?php endif; ?>


							<script type="text/javascript">
								$(document).ready(function() {
									$('.js-example-basic-single').select2();
								});
							</script>
							<?php if($_POST['opcao_delivery'] == 'true'):?>	

								<?php

								$lerbanco->ExeRead('bairros_delivery', 'WHERE user_id = :birros', "birros={$getu}");
								if($lerbanco->getResult()):
									$bairrosstatus = 'true';
									?>
									<!-- INICIO DO LOOP DOS BAIRROS -->	
									<input type="hidden" required name="bairro2" id="bairro2" value="">	
									<div class="form-group">
										<label for="bairro"><span style="color: red;">* </span><?=$texto['msg_seu_bairro'];?></label>
										<select name="bairro" id="framework" class="form-control js-example-basic-single getBairro" required data-live-search="true">
											<option value=""><?=$texto['msg_sel_bairro'];?></option>								
											<?php	
											$lerbanco->ExeRead('bairros_delivery', 'WHERE user_id = :birrosss', "birrosss={$getu}");
											if($lerbanco->getResult()):
												foreach ($lerbanco->getResult() as $getBancoBairros):
													extract($getBancoBairros);
													?>
													<option value="<?=$id;?>"><?=$bairro.' (R$ '.Check::real($taxa).')';?></option>
													<?php
												endforeach;
											endif;

											?>
										</select>
									</div>
									<!-- FINAL DO LOOP DOS BAIRROS -->	
									<?php
								else:
									?>
									<div class="form-group">
										<label for="bairro"><span style="color: red;">* </span><?=$texto['msg_seu_b'];?></label>
										<input type="text" required size="150" id="bairro" name="bairro"  class="form-control" placeholder="">
									</div>
									<?php
								endif;
								?>	

								<div class="row">
									<div class="col-md-12 col-sm-12">
										<div class="form-group">
											<label for="cep"><span style="color: red;">* </span>Cep</label>
											<input type="text" id="cep" maxlength="8" class="form-control" placeholder="Digite seu cep">
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="cidade"><span style="color: red;">* </span><?=$texto['msg_sua_cidade'];?></label>
											<input type="text" value="<?=($bairrosstatus == 'false' ? $cidade_empresa : '');?>" required id="cidade" name="cidade" size="40" class="form-control" placeholder="">
										</div>
									</div>
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="uf"><span style="color: red;">* </span><?=$texto['msg_seu_estado'];?></label>
											<input type="text" value="<?=($bairrosstatus == 'false' ? $end_uf_empresa : '');?>" required id="uf" name="uf" size="2" class="form-control" placeholder="">
										</div>
									</div>


								</div>

								<div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="rua"><span style="color: red;">* </span><?=$texto['msg_sua_rua'];?></label>
											<input type="text" required id="rua" name="rua" size="60" class="form-control" placeholder="">
										</div>
									</div>
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="unidade"><span style="color: red;">* </span><?=$texto['msg_seu_n'];?></label>
											<input type="tel" required id="unidade" data-mask="#########0" name="unidade" size="60" class="form-control" placeholder="">
										</div>
									</div>

								</div>

								<div class="form-group">
									<label for="complemento"><?=$texto['msg_complemento'];?></label>
									<input type="text" size="150" id="complemento" name="complemento"  class="form-control" placeholder="<?=$texto['msg_placehold_complemento'];?>">
								</div>
								<div class="form-group">
									<label for="observacao"><?=$texto['msg_obs_endereco'];?></label>
									<input type="text" id="observacao" name="observacao" id="observacao" class="form-control" placeholder="<?=$texto['msg_placehold_obs'];?>">
								</div>				
								<hr>

								<?php 
							else: 
							endif;?>
							<div class="row">
								<?php if($_POST['opcao_delivery'] != 'false2'): ?>
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="forma_pagamento"><span style="color: red;">* </span><?=$texto['msg_f_pagamento'];?></label>
											<select class="form-control" required name="forma_pagamento" id="forma_pagamento">	
											    
												<?php	
												$lerbanco->ExeRead('ws_formas_pagamento', 'WHERE user_id = :idus', "idus={$getu}");
												if($lerbanco->getResult()):
													foreach ($lerbanco->getResult() as $getBancoBairros):
														extract($getBancoBairros);
														if ($type_pay == 2) {
														    if (strpos(strtoupper($f_pagamento),"DINHEIRO") === false) {
														        ?>
														        <option value="<?=$f_pagamento;?>"><?=$f_pagamento;?></option>
														        <?
														    }
														}
														else {
														    ?>
														    <option value="<?=$f_pagamento;?>"><?=$f_pagamento;?></option>
														    <?
														}
													endforeach;
												endif;

												?>
											</select>
										</div>
									</div>
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<label for="valor_troco"><span style="color: red;">* </span><?=$texto['msg_troco'];?></label>
											<input required type="tel" maxlength="11" data-mask="#.##0,00" data-mask-reverse="true" name="valor_troco" id="valor_troco" class="form-control" placeholder="0,00" value="0,00" />
										</div>
									</div>
									<?php else: ?>

										<div class="col-md-12">
											<div class="form-group">
												<label class="form-control-label"><?=$texto['msg_msg_obsmesa'];?></label>
												<textarea rows="3" class="form-control" name="name_observacao_mesa" id="name_observacao_mesa" maxlength="60">Nenhuma</textarea>
											</div>
										</div>

									<?php endif; ?>
									<div class="col-md-12">
										<div class="form-group">								
											<div class="icheck-material-grey">
												<input <?=($btn_whats == 0 ? 'checked' : '');?> type="checkbox" name="confirm_whatsapp" value="true" id="green" />
												<label for="green"><strong><?=$texto['msg_msg_enviarzap'];?></strong></label>
											</div>
										</div>
									</div>
									<input type="hidden" required name="valor_taxa" id="valor_taxa" value="<?=($bairrosstatus == 'false' && $_POST['opcao_delivery'] == 'true' ? $config_delivery : '0.00');?>">


								</div>
								<hr />
								
								<span style="color: red;"><b>OBSERVAÇÕES:</b>
									<br /><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Nunca passe seus dados do cartão pelo WHATSAPP!
									<br /><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Ao clicar em "<?=$texto['msg_pedir_agora'];?>" Seu pedido será enviado para o WHATSAPP com um <strong>código</strong> de identificação.
								</span>
							</div><!-- End box_style_1 -->

						</div><!-- End col-md-6 -->

						<div class="col-md-3" id="sidebar">

							<div class="theiaStickySidebar">
								<div id="cart_box" >
									<h3><?=$texto['msg_resumo_pedido'];?> <i class="icon_cart_alt pull-right"></i></h3>			
									<?php
									if($cart->isEmpty()):
										if(file_exists("img/imgfome.png") && !is_dir("img/imgfome.png")):
											echo "<div id=\"div-img-fome\"><figure><img id=\"img-fome\" src=\"img/imgfome.png\" title=\"img-fome\" alt=\"img-fome\" /></figure></div><hr />";
									endif;
								else:
									?>
									<table class="table table_summary">
										<tbody>
											<?php
											echo $pedidos;	
											?>
										</tbody>
									</table>
									<?php	
									echo "<hr />";
								endif;
								?>



								<?php
								if($_POST['opcao_delivery'] == 'true'):
									echo "<input type=\"hidden\" name=\"opcao_delivery\" id=\"opcao_delivery\" value=\"true\" />";
								else:
									echo "<input type=\"hidden\" name=\"opcao_delivery\" id=\"opcao_delivery\" value=\"false\" />";
								endif;
								?>

								<table class="table table_summary">
									<tbody>
										<tr>
											<td>
												<?=$texto['msg_subtotal'];?> <span class="pull-right">R$ <?=(!empty($cart->getAttributeTotal('preco')) ? Check::Real($cart->getAttributeTotal('preco')) : '0,00');?></span>
											</td>
										</tr>
										<tr>
											<td>
												<?=$texto['msg_adicionais'];?> <span class="pull-right">
													R$ <?php				

								// Get all items in the cart

													$allItems = $cart->getItems();

													$totaldeadicionais = 0;

													foreach ($allItems as $items):

														foreach ($items as $item):

															$todosOsAdicionaisSoma2 = 0;
															if(!empty($item['attributes']['totalAdicionais'])):

																for($i=0; $i < $item['attributes']['totalAdicionais']; $i++):
																	$todosOsAdicionaisSoma2 = ($todosOsAdicionaisSoma2 + $item['attributes']['adicional_valor'.$i]);
																endfor;
																$todosOsAdicionaisSoma2 = ($todosOsAdicionaisSoma2 * $item['quantity']);
															endif;

															$totaldeadicionais = $totaldeadicionais + $todosOsAdicionaisSoma2;


														endforeach;
													endforeach;

													echo Check::Real($totaldeadicionais);

													$total_do_pedido =  $cart->getAttributeTotal('preco') + $totaldeadicionais;

													$total_g = ($bairrosstatus == 'false' && $_POST['opcao_delivery'] == 'true' ? $total_do_pedido + $config_delivery : $total_do_pedido);

													$porcentagem = 0;
													$fixed_value = 0.00;
													if(!empty($_SESSION['desconto_cupom']) && $_SESSION['desconto_cupom']['user_id'] == $getu):
													    if ($_SESSION['desconto_cupom']['type_discount'] == 1) {
														    $porcentagem = Check::porcentagem($_SESSION['desconto_cupom']['desconto'], $total_g);
														}
														elseif ($_SESSION['desconto_cupom']['type_discount'] == 2) {
														    $fixed_value = $_SESSION['desconto_cupom']['desconto'];
													    }
													endif;
													//$total_g = $_SESSION['desconto_cupom']['type_discount'] == 1 ? $total_g - $porcentagem : $total_g - $fixed_value;	
													$total_g = isset($_SESSION['desconto_cupom']) && $_SESSION['desconto_cupom']['type_discount'] == 1 ? $total_g - $porcentagem : $total_g - $fixed_value;	
													?>											
												</span>
											</td>
										</tr>
										<?php if($_POST['opcao_delivery'] == 'true'):?>
											<tr>
												<td>
													Delivery <span id="taxaDelivery" style="color: red;" class="pull-right"><?=($bairrosstatus == 'false' ? $config_delivery : '0.00');?></span>
												</td>
											</tr>
										<?php endif;?>
										<tr>
											<?php
											if(!empty($_SESSION['desconto_cupom']) && $_SESSION['desconto_cupom']['user_id'] == $getu):
												?>
												<tr>
													<td>
														<a style="color: green;">
															Desconto
															<span class="pull-right">
																<!--<?=$_SESSION['desconto_cupom']['desconto'];?> %-->
																<?=($_SESSION['desconto_cupom']['type_discount'] == 1 ? $_SESSION['desconto_cupom']['desconto']." %" : "R$ ".str_replace('.',',',$_SESSION['desconto_cupom']['desconto']));?>
															</span>
														</a>
													</td>
												</tr>
												<?php
											endif;

											?>
											<td class="total">

												<?=$texto['msg_total_valor'];?> <span class="pull-right"><b  id="v-total-p"><?=$total_g;?></span>
													<script type="text/javascript">
														var totalSemFormatacao = <?=$total_g;?>;
														var pegaTaxa = $('#taxaDelivery').text();
													</script>
												</td>
											</tr>
										</tbody>
									</table>
									<hr>
									<input type="hidden" name="enviar_pedido" value="enviar_agora" />
									<input type="hidden" name="user_id" value="<?=$getu;?>" />
									<input type="hidden" id="sub_total" name="sub_total" value="<?=$total_do_pedido;?>" />
									
									<?
									if ($type_pay == 0) {
									    ?>
    									<button id="btn_pay_ps" type="button" class="pagseguro-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (PAGSEGURO)</button>
									    <div id="div_script_btn">
    									    <!--<script id="script_btn"></script>-->
    									    <button id="btn_pay_mp" type="button" class="mercadopago-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (MERCADOPAGO)</button>
    									</div>
									    <button id="btn_pedir_agora" class="btn_full enviarpedido"><?=$texto['msg_pedir_agora'];?></button>
									    <?
									}
									else if ($type_pay == 1) {
									    ?>
									    <button id="btn_pedir_agora" class="btn_full enviarpedido"><?=$texto['msg_pedir_agora'];?></button>
									    <?
									}
									else if ($type_pay == 2) {
									    ?>
									    <div id="div_script_btn">
    									    <!--<script id="script_btn" data-summary-shipping="68" src="https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js" data-preference-id="726027488-a8e1009d-a93a-42e7-b60e-f136032598e9"></script>-->
    									    <button id="btn_pay_mp" type="button" class="mercadopago-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (MERCADOPAGO)</button>
    									    <!---->
    									</div>
									    <?
									}
									else if ($type_pay == 3) {
									    ?>
									    <button id="btn_pay_ps" type="button" class="pagseguro-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (PAGSEGURO)</button>
									    <?
									}
									else if ($type_pay == 4) {
									    ?>
									    <button id="btn_pay_ps" type="button" class="pagseguro-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (PAGSEGURO)</button>
									    <div id="div_script_btn">
    									    <!--<script id="script_btn" data-summary-shipping="68" src="https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js" data-preference-id="726027488-a8e1009d-a93a-42e7-b60e-f136032598e9"></script>-->
    									    <button id="btn_pay_mp" type="button" class="mercadopago-button" onclick="x0p('', 'Opss... Preencha todos os campos!', 'error', false);">PAGAR (MERCADOPAGO)</button>
    									    <!---->
    									</div>
									    <?
									}
    								?>
									<!--<button id="btn_pedir_agora" class="btn_full enviarpedido"><?=$texto['msg_pedir_agora'];?></button>-->
									<script type="text/javascript">
										$(document).ready(function(){
											$('.enviarpedido').click(function(){
											    $('.enviarpedido').html('AGUARDE...');
												$('.enviarpedido').prop('disabled', true);

												$.ajax({
													url: '<?=$site;?>includes/processaenviarpedido.php',
													method: "post",
													data: $('#getDadosPedidoCompleto').serialize(),

													success: function(data){				
														$('#resultadoEnviarPedido').html(data);
														$('.enviarpedido').html('<?=$texto['msg_pedir_agora'];?>');
														$('.enviarpedido').prop('disabled', false);
													}
												});

											}); 
										});
									</script>
									<a class="btn_full_outline" href="<?=$site. $Url[0];?>"><i class="icon-right"></i> <?=$texto['msg_add_mai'];?></a>
								</form>
							</div><!-- End cart_box -->
						</div><!-- End theiaStickySidebar -->

					</div><!-- End col-md-3 -->

				</div><!-- End row -->
			</div><!-- End container -->
			<div id="resultadoGetcliente"></div>
			<div id="resultadoEnviarPedido"></div>
			<!-- End Content =============================================== -->
			<script type="text/javascript" >		
				var iduserr = <?=$getu;?>;
				var s = 0.00;

				$(document).ready(function() {	
					$("#v-total-p").text(totalSemFormatacao.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
					$("#taxaDelivery").text(parseFloat(pegaTaxa).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));		


					$('.getBairro').change(function(){

						var idDoLocal = $(this).val();

						$.ajax({
							url: '<?=$site;?>includes/processaGetLocal.php',
							method: "post",
							dataType: 'json',
							data: {'idLocal' : idDoLocal, 'iduserrr' : <?=$getu;?>},

							success: function(data){  


                  //Atualiza os campos com os valores da consulta.
                  $("#cidade").val(data.cidade);
                  $("#uf").val(data.uf);
                  $("#valor_taxa").val(data.taxa); 
                  $("#bairro2").val(data.bairro);                  
                  $('#taxaDelivery').text(data.taxa);     

                  s = parseFloat(data.taxa);
                  soma = parseFloat(totalSemFormatacao) + parseFloat(s);

                  $("#v-total-p").text(soma.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                  $("#taxaDelivery").text(s.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                  
                  mpay("change");
                  ppay("change");
              }
          });

					});

		//Recupera o valor para validar o campo troco.
		$('#forma_pagamento').change(function (){
			
			var tell = $(this).val();
			
			if (tell == "Dinheiro" || tell == "DINHEIRO" || tell == "dinheiro") {
				$('#valor_troco').prop('disabled', false);
			}
			else {
				$('#valor_troco').val('0,00');
				$('#valor_troco').prop('disabled', true);
			}
		});

		//Quando o campo telefone perde o foco.
		$("#telefone").blur(function() {

                //Nova variável "numerowhats" somente com dígitos.
                var numerowhats = $(this).val().replace(/\D/g, '');

                $.ajax({
                	url: '<?=$site;?>includes/processagetdadoscliente.php',
                	method: "post",
                	data: {'numerocliente' : numerowhats, 'iduser' : iduserr},

                	success: function(data){        
                		$('#resultadoGetcliente').html(data);
                	}
                });
            });

	});
	
	
	$(document).ready(function() {
	    $("#cep").change(function (){
	        var cep = $("#cep").val();
	        if (cep.length == 8) {
	            $.ajax({
	                url: '<?=$site;?>includes/api_cep.php',
                	method: "post",
                	dataType: "json",
                	data: {'cep' : cep},

                	success: function(data){        
                		var res = data;
                		//alert(res['logradouro']);
                		$("#cidade").attr("value",res['localidade'])
                		$("#uf").attr("value",res['uf'])
                		$("#rua").attr("value",res['logradouro'])
                		//$("#unidade").attr("value",res['logradouro'])
                	},

	            });
	        }
	    });
	});
	
	function mpay(action) {
	    if (!checkFields()) {
	        //alert("Preencha os campos!");
	        return;
	    }
	    $("#btn_pay_mp").attr("disabled");
	    $("#btn_pay_mp").removeAttr("onclick");
	    $("#btn_pay_mp").html("AGUARDE...");
	    
	    var opcao_delivery = '<?=$_POST['opcao_delivery'];?>';
	    
	    data = new FormData();
	    if (opcao_delivery == "true") {
            data.append('action','add');
            data.append('user',<?=$getu;?>);
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            data.append('cidade',$("#cidade").val());
            data.append('uf',$("#uf").val());
            data.append('rua',$("#rua").val());
            data.append('unidade',$("#unidade").val());
            data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    else if (opcao_delivery == "false") {
	        //alert("falseeee");
	        data.append('action','add');
            data.append('user','<?=$getu;?>');
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            //data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            //data.append('cidade',$("#cidade").val());
            //data.append('uf',$("#uf").val());
            //data.append('rua',$("#rua").val());
            //data.append('unidade',$("#unidade").val());
            //data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    else if (opcao_delivery == "false2") {
	        //alert("falseeee");
	        data.append('action','add');
            data.append('user','<?=$getu;?>');
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            data.append('mesa',$("#mesa").val());
            data.append('pessoas',$("#pessoas").val());
            data.append('name_observacao_mesa',$("#name_observacao_mesa").val());
            //data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            //data.append('cidade',$("#cidade").val());
            //data.append('uf',$("#uf").val());
            //data.append('rua',$("#rua").val());
            //data.append('unidade',$("#unidade").val());
            //data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    
	    $.ajax({
	        url: '<?=$site;?>includes/mpay.php',
	        method: "post",
	        //dataType: "json",
	        processData: false,
	        contentType: false,
	        /*data: { "action" : "add", "user" : <?=$getu;?>, "taxa_v" : $("#valor_taxa").val(), "sub_total" : $("#sub_total").val(), 
	                "telefone" : $("#telefone").val(), "nome" : $("#nome").val(), "bairro" : $("#framework option:selected").html(),
	                "cidade" : $("#cidade").val(), "uf" : $("#uf").val(), "rua" : $("#rua").val(),
	                "unidade" : $("#unidade").val(), "complemento" : $("#complemento").val(), "confirm_whatsapp" : $("#green").prop("checked"), "opcao_delivery" : $("#opcao_delivery").val() },*/
	        data: data,
	        success: function (response) {
	            var res = response;
	            if (res.toString() == "fields") {
	                return;
	            }
	            else if (res.toString() == "min_delivery") {
	                $("#btn_pay_mp").attr("onclick","x0p('', 'Opss... O valor mínimo do delivery e de R$ <?=(Check::Real($minimo_delivery));?>', 'error', false);")
	                $("#btn_pay_mp").removeAttr("disabled");
	                $("#btn_pay_mp").html("PAGAR (MERCADOPAGO)");
	                return;
	            }
	            else if (res.toString().toUpperCase().indexOf("UNDEFINED") != -1) {
	                $("#btn_pay_mp").attr("onclick","x0p('', 'Opss... O token de acesso é inválido!', 'error', false);")
	                $("#btn_pay_mp").removeAttr("disabled");
	                $("#btn_pay_mp").html("PAGAR (MERCADOPAGO)");
	                return;
	            }
	            
	            if (action == "change") {
	                //$("#div_script_btn").html(" ");
	                //$("#div_script_btn").html('<button id="btn_pay_mp" type="button" class="mercadopago-button" onclick="window.open(\''+res.toString()+'\',\'_blank\')">Pagar Agora</button>');
	                
	                $("#btn_pay_mp").attr("onclick","window.open('"+res.toString()+"','_blank');")
	                $("#btn_pay_mp").removeAttr("disabled");
	                $("#btn_pay_mp").html("PAGAR (MERCADOPAGO)");
	            }
	        },

	    });
	}
	
	function ppay(action) {
	    if (!checkFields()) {
	        //alert("Preencha os campos!");
	        return;
	    }
	    $("#btn_pay_ps").attr("disabled");
	    $("#btn_pay_ps").removeAttr("onclick");
	    $("#btn_pay_ps").html("AGUARDE...");
	    
	    var opcao_delivery = '<?=$_POST['opcao_delivery'];?>';
	    
	    data = new FormData();
	    if (opcao_delivery == "true") {
            data.append('action','add');
            data.append('user',<?=$getu;?>);
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            data.append('cidade',$("#cidade").val());
            data.append('uf',$("#uf").val());
            data.append('rua',$("#rua").val());
            data.append('unidade',$("#unidade").val());
            data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    else if (opcao_delivery == "false") {
	        //alert("falseeee");
	        data.append('action','add');
            data.append('user','<?=$getu;?>');
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            //data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            //data.append('cidade',$("#cidade").val());
            //data.append('uf',$("#uf").val());
            //data.append('rua',$("#rua").val());
            //data.append('unidade',$("#unidade").val());
            //data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    else if (opcao_delivery == "false2") {
	        //alert("falseeee");
	        data.append('action','add');
            data.append('user','<?=$getu;?>');
            data.append('taxa_v',$("#valor_taxa").val());
            data.append('sub_total',$("#sub_total").val());
            data.append('telefone',$("#telefone").val());
            data.append('nome',$("#nome").val());
            data.append('mesa',$("#mesa").val());
            data.append('pessoas',$("#pessoas").val());
            data.append('name_observacao_mesa',$("#name_observacao_mesa").val());
            //data.append('bairro',($("#framework").length > 0 ? $("#framework option:selected").html() : $("#bairro").val()));
            //data.append('cidade',$("#cidade").val());
            //data.append('uf',$("#uf").val());
            //data.append('rua',$("#rua").val());
            //data.append('unidade',$("#unidade").val());
            //data.append('complemento',$("#complemento").val());
            data.append('confirm_whatsapp',$("#green").prop("checked"));
            data.append('opcao_delivery',$("#opcao_delivery").val());
	    }
	    
	    $.ajax({
	        url: '<?=$site;?>includes/ppay.php',
	        method: "post",
	        //dataType: "json",
	        processData: false,
	        contentType: false,
	        /*data: { "action" : "add", "user" : <?=$getu;?>, "taxa_v" : $("#valor_taxa").val(), "sub_total" : $("#sub_total").val(), 
	                "telefone" : $("#telefone").val(), "nome" : $("#nome").val(), "bairro" : $("#framework option:selected").html(),
	                "cidade" : $("#cidade").val(), "uf" : $("#uf").val(), "rua" : $("#rua").val(),
	                "unidade" : $("#unidade").val(), "complemento" : $("#complemento").val(), "confirm_whatsapp" : $("#green").prop("checked"), "opcao_delivery" : $("#opcao_delivery").val() },*/
	        data: data,
	        success: function (response) {
	            var res = response;
	            //alert("aaaa");
	            //alert(res.toString());
	            if (res.toString() == "fields") {
	                return;
	            }
	            else if (res.toString() == "min_delivery") {
	                $("#btn_pay_ps").attr("onclick","x0p('', 'Opss... O valor mínimo do delivery e de R$ <?=(Check::Real($minimo_delivery));?>', 'error', false);")
	                $("#btn_pay_ps").removeAttr("disabled");
	                $("#btn_pay_ps").html("PAGAR (PAGSEGURO)");
	                return;
	            }
	            else if (res.toString().toUpperCase().indexOf("UNDEFINED") != -1) {
	                $("#btn_pay_ps").attr("onclick","x0p('', 'Opss... O código de pagamento não foi gerado corretamente, atualize a página!', 'error', false);")
	                $("#btn_pay_ps").removeAttr("disabled");
	                $("#btn_pay_ps").html("PAGAR (PAGSEGURO)");
	                return;
	            }
	            
	            if (action == "change") {
	                //$("#div_script_btn").html(" ");
	                //$("#div_script_btn").html('<button id="btn_pay_ps" type="button" class="mercadopago-button" onclick="window.open(\''+res.toString()+'\',\'_blank\')">Pagar Agora</button>');
	                
	                $("#btn_pay_ps").attr("onclick","window.open('"+res.toString()+"','_blank');")
	                $("#btn_pay_ps").removeAttr("disabled");
	                $("#btn_pay_ps").html("PAGAR (PAGSEGURO)");
	            }
	        },

	    });
	}
	
	<?
	if ($type_pay == 0 or $type_pay == 2 or $type_pay == 3 or $type_pay == 4) {
	    ?>
    	$("#forma_pagamento").change(function() {
    	    var sel = $("#forma_pagamento").val().toUpperCase();
    	    if (sel.indexOf("CARTÃO") != -1 || sel.indexOf("CRÉDITO") != -1) {
    	        mpay("change");
    	        ppay("change");
    	    }
    	});
    	
    	$(window).load(function() {
            //mpay("load");
        });
    	<?
	}
	?>
	
	function checkFields() {
	    var opcao_delivery = '<?=$_POST['opcao_delivery'];?>';
	    if (opcao_delivery == "true") {
	        if ($("#telefone").val() == "" ||
    	        $("#nome").val() == "" ||
    	        ($("#framework").length > 0 && $("#framework option:selected").val() == "") ||
    	        ($("#bairro").length > 0 && $("#bairro").val() == "") ||
    	        $("#cidade").val() == "" ||
    	        $("#uf").val() == "" ||
    	        $("#rua").val() == "" ||
    	        $("#unidade").val() == "" ||
    	        $("#forma_pagamento option:selected").val() == "") {
    	        return false;
    	    }
    	    return true;
	    }
	    else if (opcao_delivery == "false") {
	        if ($("#telefone").val() == "" ||
    	        $("#nome").val() == "") {
    	        return false;
    	    }
    	    return true;
	    }
	    else if (opcao_delivery == "false2") {
	        if ($("#telefone").val() == "" ||
    	        $("#nome").val() == "" ||
    	        $("#mesa").val() == "" ||
    	        $("#pessoas").val() == "" ||
    	        $("#name_observacao_mesa").val() == "") {
    	        return false;
    	    }
    	    return true;
	    }
	}
	
	var timer = window.setInterval(checkFieldsTimer, 1500);
    function checkFieldsTimer() {
        if (checkFields()) {
            mpay("change");
            ppay("change");
            clearInterval(timer);
        }
    }

</script>


<style>
    button.mercadopago-button {
        border: none;
    	font-family: inherit;
    	color:#fff;
    	width:100%;
    	background: #41B8F3;
    	cursor: pointer;
    	padding: 12px 20px;
    	outline: none;
    	font-size: 15px;
    	-webkit-transition: all 0.3s;
    	-moz-transition: all 0.3s;
    	transition: all 0.3s;
    	-webkit-border-radius: 3px;
    	-moz-border-radius: 3px;
    	border-radius: 3px;
    	text-transform:uppercase;
    	font-weight:bold;
    	display:block;
    	text-align:center;
    	margin-bottom:5px;
    }
    
    button.mercadopago-button:hover{
    	background:#000000;
    }
    
    button.pagseguro-button {
        border: none;
    	font-family: inherit;
    	color:#fff;
    	width:100%;
    	background: #4AD17C;
    	cursor: pointer;
    	padding: 13px 20px;
    	outline: none;
    	font-size: 15px;
    	-webkit-transition: all 0.3s;
    	-moz-transition: all 0.3s;
    	transition: all 0.3s;
    	-webkit-border-radius: 3px;
    	-moz-border-radius: 3px;
    	border-radius: 3px;
    	text-transform:uppercase;
    	font-weight:bold;
    	display:block;
    	text-align:center;
    	margin-bottom:5px;
    }
    
    button.pagseguro-button:hover{
    	background:#000000;
    }
</style>
