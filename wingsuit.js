jQuery(document).ready(function($){
/*	$('.wp-color-picker-field').wpColorPicker({change: function(event, ui){
		$('.logo-svg:not(.static)').find($(event.target).data('selector')).css('fill',ui.color);
	}});*/
	$(document).on('click', '.wingsuit-area .element:not(.disabled) li', function(){
		var color_elem = $(this),
			parent_elem = color_elem.closest('.element');
		$('.wingsuit div:not(.static)>svg').find(parent_elem.data('selector')).css(parent_elem.data('property')||'fill',color_elem.css('backgroundColor'));
		parent_elem.find('li').removeClass('active');
		parent_elem.data('color',color_elem.css('backgroundColor'));
		parent_elem.data('colorname',color_elem.data('color'));
		setTimeout(function(){color_elem.addClass('active')},100);
	});
	$('.addon input[type=checkbox]').on('change', function(){
		var element = $(this).closest('.addon').find('.element');
		if($(this).is(":checked")){
			element.removeClass('disabled');
			if($(this).hasClass('hendly'))$('.wingsuit div:not(.static)>svg').find($(this).data('selector')).show();
		}else{
			element.addClass('disabled');
			element.closest('.wingsuit-area').removeClass('fail');
			if($(this).hasClass('hendly'))$('.wingsuit div:not(.static)>svg').find($(this).data('selector')).hide();
		}
		var total_price = $('.suit-price').data('price');
		$('.addon input[type=checkbox]').each(function(i,el){
			if($(el).is(":checked"))total_price+=$(el).data('price');
		});
		$('.suit-price span').text(total_price);
	});
	var svg_top = $('.wingsuit-svg>svg').offset().top;
	var svg_height = $('.wingsuit-svg').height();
	var addons_height = $('#addons').height();
	var human_top = $('.wingsuit-human').offset().top;
	$(document).on( 'scroll', function(){
		var addons_top = $('#addons').offset().top+addons_height-svg_height-30,
			wingsuit = $('.wingsuit-svg:not(.no-colors)');
		if(svg_top < $(window).scrollTop()){
			if(!wingsuit.hasClass('fixed')){
				wingsuit.addClass('fixed');
				wingsuit.closest('.wingsuit-row').css('padding-top',svg_height);
			}
		}else{
			wingsuit.removeClass('fixed');
			wingsuit.closest('.wingsuit-row').removeAttr('style');
		}
		if(addons_top < $(window).scrollTop()){
			if(!wingsuit.hasClass("bottom-fixed"))wingsuit.addClass('bottom-fixed').css('top',addons_top);
		}else wingsuit.removeClass('bottom-fixed').removeAttr('style');
		
		if(human_top < $(window).scrollTop()){
			if(!$('.sizes').hasClass('fixed')){
				$('.sizes').addClass('fixed');
			}
		}else{
			$('.sizes').removeClass('fixed');
		}
	});
	$('.wingsuit-menu ul').on('click',function(){
		$(this).addClass('open');
	});
	$('.wingsuit-menu li.active a').on('click',function(){
		$(this).closest('ul').addClass('open');
		return false;
	});
	$(document).on('click',function(e){
		if (!$(e.target).closest(".wingsuit-list").length) {
			$('.wingsuit-menu ul').removeClass('open');
		}
	});
	$('.wingsuit-fields input').on('click',function(){
		if(typeof($(this).data('img')) !== "undefined" ) $('.wingsuit-human img').attr('src',$(this).data('img'));
		if(typeof($(this).data('descr')) !== "undefined" ) $('.wingsuit-human-descr').text($(this).data('descr'));
		else $('.wingsuit-human-descr').text('');
	});
	$.fn.random = function() {
		var randomIndex = Math.floor(Math.random() * this.length);  
		return jQuery(this[randomIndex]);
	};
	$('.randomcolors').on('click',function(){
		$('.wingsuit-area').each(function(i,el){
			var colors = $(el).find('.element:not(.disabled) li');
			if(colors.length)colors.random().click();
		});
		/*$('.wingsuit-fields input').each(function(i,el){
			$(el).val(Math.floor(Math.random() * 100));
		});*/
	});
	$('.makepdf').on('click',function(){
		getPdf(true);
	});
	function getPdf(download=false){
		var docDefinition = {},
			logoObj = {},
			suitSvg = document.createElement('canvas'),
			suitdata = new XMLSerializer().serializeToString($('.wingsuit-svg svg')[0]),
			wingsuit_main = getColorsArray($('.wingsuit_main')),
			wingsuit_front = getColorsArray($('.wingsuit_front')),
			wingsuit_back = getColorsArray($('.wingsuit_back')),
			main_columns = [],
			color_columns = [],
			additional = [],
			content = [],
			fields_columns = [[],[],[],[]],
			i = 0,
			title = { table:{ widths: [ '100%' ], body:[[{text:$('.wingsuit-svg').data('name'),margin: [0, 10, 0, 10],fontSize:18, alignment:"center", bold: true}]]}}
		if($('.logo-svg svg').length){
			var logo = document.createElement('canvas'),
				logodata = new XMLSerializer().serializeToString($('.logo-svg svg')[0]);
			logo.setAttribute('width', 460);
			logo.setAttribute('height', 100);
			canvg(logo, logodata);
			logoObj = {
						image: logo.toDataURL("image/png"),
						width: 240
					};
		}
		
		suitSvg.setAttribute('width', 1100);
		suitSvg.setAttribute('height', 500);
		canvg(suitSvg, suitdata);
		suitSvgObj = {
			image: suitSvg.toDataURL("image/png"),
			width: 300,
			alignment:"center",
			margin: [0, 10, 0, 0]
		};
		main_columns.push([title])
		if(wingsuit_main.length){
			main_columns.push([
					{ text: wingsuit.common, bold: true },
					{
					  fontSize: 10,
					  table: {
						widths: [ '*', 'auto' ],
						body: wingsuit_main
					  }
					}
				]);
		}
		content.push({columns: main_columns, columnGap: 10},suitSvgObj);
		if(wingsuit_front.length){
			color_columns.push([
				{ text: $('.wingsuit_front-title').text(), bold: true, margin: [0, 10, 0, 0] },
				{
				  fontSize: 10,
				  table: {
					widths: [ '*', 'auto' ],
					body: wingsuit_front
					  
				  }
				}
			]);
		}
		if(wingsuit_front.length){
			color_columns.push([
				{ text: $('.wingsuit_back-title').text(), bold: true, margin: [0, 10, 0, 0] },
				{
				  fontSize: 10,
				  table: {
					widths: [ '*', 'auto' ],
					body: wingsuit_back
					  
				  }
				}
			]);
		}
		if(color_columns.length){
			 content.push({columns: color_columns,columnGap: 10});
		}

		content.push({text: $('.addons-title').text(), bold: true, margin: [0, 10, 0, 0]});
			$('.addon input[type=checkbox]').each(function(i,el){
				if($(el).is(":checked")){
					var colors = getColorsArray($(el).closest('.addon'));
					additional.push({text:$(el).closest('label').text()+": "+wingsuit.yes, margin: [0, 5, 0, 0]});
					if(colors.length)additional.push({
					  table: {
						body: colors
					  },
					  layout: 'noBorders',
					  listType: 'none'
					});
				}
				else additional.push({text:$(el).closest('label').text()+": "+wingsuit.no, margin: [0, 5, 0, 0]});
			});
		  content.push({
			  columns: [
				[
					{ fontSize: 10, ul: additional }
				],
				[
					logoObj
				]
			  ],
			  columnGap: 10
		  });
		  
		  content.push({text: wingsuit.sizes, bold: true, margin: [0, 10, 0, 0]});

		  $.each(getFieldsArray($('.wingsuit-fields')),function(e,el){
			  fields_columns[i++].push({text:el[0]+': '+el[1], margin: [0, 5, 0, 0]});
			  if(i==4)i=0;
		  });
		  content.push({fontSize: 10, columns: fields_columns});
		  
		  content.push({text: wingsuit.contacts, bold: true, margin: [0, 10, 0, 0]});
		  content.push({
					  fontSize: 10,
					  table: {
						widths: [ '35%', '*' ],

						body: getFieldsArray($('.wingsuit-form'))
						  
					  }
					});
		  docDefinition.content = content;
		  docDefinition.footer = [
			  { text: wingsuit.site, fontSize: 10, margin: [10, 0, 0, 0] },
			  { text: wingsuit.phone, margin: [10, 0, 0, 0] },
			  { text: wingsuit.email, fontSize: 10, margin: [10, 0, 0, 0] }
		  ];
		
		pdfMake.tableLayouts = {
		  exampleLayout: {
			hLineWidth: function (i, node) {
			  if (i === 0 || i === node.table.body.length) {
				return 0;
			  }
			  return (i === node.table.headerRows) ? 2 : 1;
			},
			vLineWidth: function (i) {
			  return 0;
			},
			hLineColor: function (i) {
			  return i === 1 ? 'black' : '#aaa';
			},
			paddingLeft: function (i) {
			  return i === 0 ? 0 : 8;
			},
			paddingRight: function (i, node) {
			  return (i === node.table.widths.length - 1) ? 0 : 8;
			}
		  }
		};

		var pdf = pdfMake.createPdf(docDefinition);
		if(download) pdf.download();
		else return pdf
		
	}
	function getFieldsArray(area,fields=[]) {
		area.find('.wingsuit-field input,.wingsuit-row select,.wingsuit-textarea textarea').each(function(i,el){
			var element = $(el);
			if(element.val()== '' || !element.is(':valid')){
				fields.push([element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').find('label').contents().not('span.hint').text(),'']);
				element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').addClass('fail');
 			}else{
				fields.push([
					element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').find('label').contents().not('span.hint').text(),
					element.val()+(typeof(element.attr('placeholder'))!=="undefined"&&element.val()!=""?' '+element.attr('placeholder'):'')
				]);
				element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').removeClass('fail');
			}
		});
		return fields;
	}
	function getColorsArray(area, color = []) {
		area.find('.wingsuit-area .element:not(.disabled)').each(function(i,el){
			var element = $(el);
 			if(typeof(element.data('color')) === 'undefined'){
				color.push([element.closest('.wingsuit-area').find('label').text(),'']);
				element.closest('.wingsuit-area').addClass('fail');
 			}else{
				color.push([element.closest('.wingsuit-area').find('label').text(),{ text: element.data('colorname'), fillColor: rgb2hex(element.data('color')) }]);
				element.closest('.wingsuit-area').removeClass('fail');
			}
		});
		return color;
	}
	
	$('.send').on('click',function(){
		var oreder = '';
		/*oreder += "<h4>Общее</h4>\r\n";
		oreder += '<table>'+getColors($('.wingsuit_main'))+'</table>';
		oreder += "<h4>Передняя часть</h4>\r\n";
		oreder += '<table>'+getColors($('.wingsuit_front'))+'</table>';
		oreder += "<h4>Задняя часть</h4>\r\n";
		oreder += '<table>'+getColors($('.wingsuit_back'))+'</table>';
		oreder += "<h4>Дополнительно</h4>\r\n";
		oreder += "<table>";
		$('.addon input[type=checkbox]').each(function(i,el){
			if($(el).is(":checked")){
				oreder += '<tr><td>'+$(el).closest('label').text()+":</td><td>Да</td></tr>\r\n";
				oreder += '<tr><td><table>'+getColors($(el).closest('.addon'))+'</table></td><td></td></tr>';
			}
			else oreder += '<tr><td>'+$(el).closest('label').text()+":</td><td>Нет</td></tr>\r\n";
		});
		oreder += "</table>";
		oreder += "<h4>Размер</h4>\r\n";
		oreder += getFields($('.wingsuit-fields'));*/
		oreder += "<h4>"+wingsuit.contacts+"</h4>\r\n";
		oreder += getFields($('.wingsuit-form'));
		var pdf = getPdf();
		if($('.wingsuit .fail').length === 0){
			pdf.getBase64(function(buffer) {
				$.ajax({
					url: wingsuit.ajax,
					method: "POST",
					data:{action: 'wingsuit_send',order:oreder,pdf:buffer,nonce_code:wingsuit.nonce},
					beforeSend: function(){
						$('.send').prop( "disabled", true );
					},
					success:function(result){
						alert(wingsuit.success);
					},
					error:function(result){
						alert(wingsuit.error);
						console.log(result);
					},
					complete: function(){
						$('.send').prop( "disabled", false );
					}
				});
			});
		}else alert(wingsuit.warning);
	});
	function getColors(area) {
		var colors='';
		area.find('.wingsuit-area .element:not(.disabled)').each(function(i,el){
			var element = $(el);
 			if(typeof(element.data('color')) === 'undefined') element.closest('.wingsuit-area').addClass('fail');
 			else{
				colors += '<tr><td>'+element.closest('.wingsuit-area').find('label').text()+':</td><td bgcolor="'+rgb2hex(element.data('color'))+'" style="background-color:'+rgb2hex(element.data('color'))+'">'+rgb2hex(element.data('color'))+"</td></tr>\r\n";
				element.closest('.wingsuit-area').removeClass('fail');
			}
		});
		return colors;
	}
	function getFields(area) {
		var fields='';
		area.find('.wingsuit-field input,.wingsuit-row select,.wingsuit-textarea textarea').each(function(i,el){
			var element = $(el);
 			if(element.val()== '' || !element.is(':valid')) element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').addClass('fail');
 			else{
				fields += element.closest('.wingsuit-field,.wingsuit-textarea').find('label').contents().not('span.hint').text()+': '+element.val()+"<br>\r\n";
				element.closest('.wingsuit-field,.wingsuit-textarea,.wingsuit-row').removeClass('fail');
			}
		});
		return fields;
	}
	function rgb2hex(rgb) {
		if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;

		rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		function hex(x) {
			return ("0" + parseInt(x).toString(16)).slice(-2);
		}
		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
});