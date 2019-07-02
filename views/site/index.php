<?php

/* @var $this yii\web\View */

$this->title = 'Тестовое задание';


$script = '
function loadXML(queries){
	$.ajax({
		type: "GET",
		url: "/api/csv",
		data: queries,
		dataType: "xml",
		success: function(xml) {
			var tr = "";
			$(xml).find("item").each(function () {
				tr += "<tr data-row="+$(this).find("id").text()+"><td>"+$(this).find("key").text()+"</td><td>"+$(this).find("value").text()+"</td></tr>";
			});
			$("#items tbody").html(tr)
		}
	}).done(function(){
		$("table#items tbody").find("tr").on("click", function(){
			if(selected == $(this).attr("data-row")) {
				$("table#items tr").removeClass("selected")
				selected = null
			} else {
				$("table#items tr").removeClass("selected")
				selected = $(this).attr("data-row")
				$(this).addClass("selected")
			}
			
		})
	});
}
var queries = {};
var selected = null;

function sortBy(name) {
	var cls = ""
	if(typeof queries["sort"] !== "undefined") {
		if(queries["sort"] == "-"+name) {
			queries["sort"] = name
			cls = "asc"
		}
		else {
			queries["sort"] = "-"+name
			cls = "desc"
		}
	}
	else {
		queries["sort"] = name
		cls = "asc"
	}

	$(".sort a").removeClass("desc").removeClass("asc")
	$(".sort."+name+" a").addClass(cls)
	
	loadXML(queries)
}

function filterBy(field, value) {
	if(value=="") delete queries[field];
	else queries[field] = value
	
	loadXML(queries)
}

function calcValue(action) {
	if(selected!==null) {
		$.ajax({
			type: "POST",
			url: "/api/change/"+selected,
			data: "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"+
			"<data>"+
				"<action>"+action+"</action>"+
				"<value>"+$("#valueChange").val()+"</value>"+
			"</data>",
			contentType: "text/xml",
			dataType: "xml",
			success: function(xml) {
				var value = $(xml).find("value");
				$("table#items tr[data-row=\'"+selected+"\']").find("td").eq(1).html(value.text())
			}
		}).done(function(){
			
		});
	} else {
		alert("Не выделен ключ для редактирования")
	}
}

loadXML(queries)


';
  
$this->registerJs($script, yii\web\View::POS_END);

?>
<div class="site-index">

    

    <div class="body-content">
		<div class="row">
			<div class="col-md-2">
				Загрузить файл
				<?php
					echo \trntv\filekit\widget\Upload::widget([
						'name' => 'filename',
						'hiddenInputId' => 'filename', // must for not use model
						'url' => ['upload'],
						'uploadPath' => 'csv', // optional, for storing files in storage subfolder
						'sortable' => true,
						'maxNumberOfFiles' => 1,
						'acceptFileTypes' => new yii\web\JsExpression('/(\.|\/)(csv)$/i'),
						'showPreviewFilename' => false,
						'clientOptions' => [ 
							'done' => new yii\web\JsExpression('function(e, data) {
								
								//$(".upload-kit").hide()
								loadXML()
								
							}'),
						 ]
					]);
				?>
				<input class="form-control" placeholder="Значение" value="" id="valueChange" autocomplete="off">
				<div class="dropdown">
				  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					Изменить запись
					<span class="caret"></span>
				  </button>
				  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="javascript:calcValue('add')">+ Прибавить</a></li>
					<li><a href="javascript:calcValue('sub')">- Вычесть</a></li>
					<li><a href="javascript:calcValue('mul')">* Умножить</a></li>
					<li><a href="javascript:calcValue('div')">/ Разделить</a></li>
				  </ul>
				</div>
			</div>
			<div class="col-md-10">
				<table id="items" class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th class="sort sort-ordinal key"><a href="javascript:sortBy('key')">Ключ</a></th>
							<th class="sort sort-numerical value"><a href="javascript:sortBy('value')">Значение</a></th>
						</tr>
						<tr>
							<th><input type="text" onblur="filterBy('key', this.value)"></th>
							<th><input type="text" onblur="filterBy('value', this.value)"></th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				
				<div class="dropdown">
				  
				  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="#">Action</a></li>
					<li><a href="#">Another action</a></li>
					<li><a href="#">Something else here</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Separated link</a></li>
				  </ul>
				</div>
			</div>
		</div>
        

    </div>
</div>
