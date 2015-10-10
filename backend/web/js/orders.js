$(document).ready(function(){
  $(".jsAddGoods").click(function(event){
  	var ajaxorderurl = js_context.ajax_add_order_url;
    var id = $(event.currentTarget).attr("goods_id");
    var _event = $(event.currentTarget);
  	$.ajax({
        url: ajaxorderurl,
        type: 'post',
        data: {goods_id: id},
        success: function (data) { 
          _event.attr("added", data.actionadd);
          var html = data.actionadd ? "已添加" : "添加";
          _event.html(html);
          var count = data.count;
          var count_html = data.count > 0 ? "Shopping Car("+count+")" : "Shopping Car";
          $(".jsCount").html(count_html);
          console.log(data);
       }
  	});
  });

  $(".jsDelGood").click(function(event){
    var ajaxorderurl = js_context.ajax_del_good_url;
    var id = $(event.currentTarget).attr("goods_id");
    var _event = $(event.currentTarget);
    $.ajax({
        url: ajaxorderurl,
        type: 'post',
        data: {goods_id: id},
        success: function (data) {          
          _event.parent().parent().html("");
          $(".jsGoodsCount").val(data.count);          
          console.log(data);
       }
    });
  });

  $(".jsValideOrder").click(function(event){
    var type = $(event.currentTarget).parent().parent().parent().find(".jsOrderType").val();

    var count = $(".jsGoodsCount").val();

    if((type == 1 || type == 2) && count > 0 ) {
      $("#form-order-valider").submit();
    } else {
      alert("请正确设置type或者添加商品");
    }
  });

  $(".jsSelectDate").click(function(event){
    var year = $(".jsYear").val();
    var month = $(".jsMonth").val();
    console.log(year);
    console.log(month);
    location.href = js_context.ledger_url + "&year=" + year + "&month=" + month;
  });

});

